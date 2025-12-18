<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\OfferDecision;
use App\Models\Property;
use App\Models\SalesProgression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OfferDecisionNotification;
use App\Services\PropertyStatusTransitionService;
use App\Services\KeapService;

class OfferDecisionService
{
    protected $statusTransitionService;
    protected $keapService;

    public function __construct()
    {
        $this->statusTransitionService = new PropertyStatusTransitionService();
        $this->keapService = new KeapService();
    }

    /**
     * Process offer decision (accept, decline, or counter).
     * Handles all business logic consistently for both API and Web.
     *
     * @param Offer $offer
     * @param string $decision 'accepted', 'declined', or 'counter'
     * @param int $sellerId User ID making the decision
     * @param array $data Additional data (counter_amount, comments, etc.)
     * @return array Result with success status and data
     */
    public function processDecision(Offer $offer, string $decision, int $sellerId, array $data = []): array
    {
        // Validate decision value
        $validDecisions = ['accepted', 'declined', 'counter'];
        if (!in_array($decision, $validDecisions)) {
            return [
                'success' => false,
                'message' => "Invalid decision. Must be one of: " . implode(', ', $validDecisions),
            ];
        }

        // Validate offer is in pending state
        if ($offer->status !== 'pending' && $offer->status !== 'countered') {
            return [
                'success' => false,
                'message' => "Offer is not in a state that can be decided. Current status: {$offer->status}",
            ];
        }

        try {
            DB::beginTransaction();

            // Map decision to offer status
            $statusMap = [
                'accepted' => 'accepted',
                'declined' => 'declined',
                'counter' => 'countered',
            ];

            $newStatus = $statusMap[$decision];

            // Update offer status
            $offer->update([
                'status' => $newStatus,
            ]);

            // Create offer decision record
            $offerDecision = OfferDecision::create([
                'offer_id' => $offer->id,
                'seller_id' => $sellerId,
                'decision' => $decision,
                'comments' => $data['comments'] ?? $data['notes'] ?? null,
                'counter_amount' => ($decision === 'counter' && isset($data['counter_amount'])) 
                    ? (float) $data['counter_amount'] 
                    : null,
                'decided_at' => now(),
            ]);

            // If accepted, handle property status and sales progression
            if ($decision === 'accepted') {
                $acceptanceResult = $this->handleOfferAcceptance($offer, $offerDecision, $sellerId);
                
                if (!$acceptanceResult['success']) {
                    DB::rollBack();
                    return $acceptanceResult;
                }
            }

            DB::commit();

            // Send notifications (outside transaction)
            $this->sendNotifications($offer, $offerDecision, $decision);

            // Trigger Keap automation
            $this->triggerKeapEvent($offer, $offerDecision, $decision);

            return [
                'success' => true,
                'message' => "Offer decision processed successfully: {$decision}",
                'offer' => $offer->fresh(['buyer', 'property.seller', 'decisions']),
                'decision' => $offerDecision,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Offer decision processing failed', [
                'offer_id' => $offer->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process offer decision: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle offer acceptance logic.
     *
     * @param Offer $offer
     * @param OfferDecision $offerDecision
     * @param int $sellerId
     * @return array
     */
    protected function handleOfferAcceptance(Offer $offer, OfferDecision $offerDecision, int $sellerId): array
    {
        // Update property status to SSTC using service
        $statusResult = $this->statusTransitionService->changeStatus(
            $offer->property,
            Property::STATUS_SSTC,
            $sellerId,
            'Offer accepted by seller'
        );

        if (!$statusResult['success']) {
            return [
                'success' => false,
                'message' => 'Failed to update property status: ' . $statusResult['message'],
            ];
        }

        // Create sales progression record
        $salesProgression = SalesProgression::create([
            'property_id' => $offer->property_id,
            'buyer_id' => $offer->buyer_id,
            'offer_id' => $offer->id,
            'solicitor_seller' => $offer->property->solicitor_email ?? null,
        ]);

        // Check if buyer and seller have completed required information for MoS
        $sellerInfoComplete = $offer->property->solicitor_details_completed ?? false;
        $buyerInfoComplete = $this->checkBuyerInfoComplete($offer->buyer);

        // Generate and send Memorandum of Sale if both parties have completed their info
        if ($sellerInfoComplete && $buyerInfoComplete) {
            $this->generateMemorandumOfSale($offer, $offer->property, $salesProgression);
        } else {
            // Mark that MoS is pending completion of required info
            $salesProgression->update([
                'memorandum_pending_info' => true,
            ]);

            // Notify both parties that they need to complete their information
            $this->notifyPendingMemorandumInfo($offer, $offer->property, $sellerInfoComplete, $buyerInfoComplete);
        }

        return [
            'success' => true,
            'sales_progression' => $salesProgression,
        ];
    }

    /**
     * Check if buyer has completed required information.
     *
     * @param \App\Models\User $buyer
     * @return bool
     */
    protected function checkBuyerInfoComplete($buyer): bool
    {
        // Check if buyer has uploaded AML documents
        $amlCheck = \App\Models\AmlCheck::where('user_id', $buyer->id)
            ->where('verification_status', 'verified')
            ->exists();

        // Add other buyer info checks as needed
        // For now, AML verification is the main requirement

        return $amlCheck;
    }

    /**
     * Generate and send Memorandum of Sale.
     *
     * @param Offer $offer
     * @param Property $property
     * @param SalesProgression $salesProgression
     * @return void
     */
    protected function generateMemorandumOfSale(Offer $offer, Property $property, SalesProgression $salesProgression): void
    {
        try {
            $memorandumService = new \App\Services\MemorandumOfSaleService();
            $memorandumPath = $memorandumService->generateAndSave($offer, $property, $salesProgression);

            // Update sales progression with memorandum path
            $salesProgression->update([
                'memorandum_of_sale_issued' => true,
                'memorandum_path' => $memorandumPath,
            ]);

            // Send Memorandum of Sale to both solicitors
            if ($property->solicitor_email) {
                Mail::to($property->solicitor_email)->send(
                    new \App\Mail\MemorandumOfSale($offer, $property, $memorandumPath, 'seller')
                );
            }

            // Send to buyer solicitor (if available in offer or buyer profile)
            // For now, we'll send to buyer email as placeholder
            Mail::to($offer->buyer->email)->send(
                new \App\Mail\MemorandumOfSale($offer, $property, $memorandumPath, 'buyer')
            );
        } catch (\Exception $e) {
            Log::error('Memorandum of Sale generation error: ' . $e->getMessage());
            // Don't fail the transaction if memorandum generation fails
        }
    }

    /**
     * Notify parties about pending Memorandum of Sale information.
     *
     * @param Offer $offer
     * @param Property $property
     * @param bool $sellerInfoComplete
     * @param bool $buyerInfoComplete
     * @return void
     */
    protected function notifyPendingMemorandumInfo(Offer $offer, Property $property, bool $sellerInfoComplete, bool $buyerInfoComplete): void
    {
        try {
            if (!$sellerInfoComplete) {
                Mail::to($property->seller->email)->send(
                    new \App\Mail\MemorandumPendingInfo($offer, $property, 'seller')
                );
            }
            if (!$buyerInfoComplete) {
                Mail::to($offer->buyer->email)->send(
                    new \App\Mail\MemorandumPendingInfo($offer, $property, 'buyer')
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send MoS pending info notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send notifications about offer decision.
     *
     * @param Offer $offer
     * @param OfferDecision $offerDecision
     * @param string $decision
     * @return void
     */
    protected function sendNotifications(Offer $offer, OfferDecision $offerDecision, string $decision): void
    {
        try {
            // Reload offer with relationships
            $offer->load(['buyer', 'property.seller']);

            // Send notification to buyer
            Mail::to($offer->buyer->email)->send(
                new OfferDecisionNotification($offer, $offer->property, $offer->buyer, $decision)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send offer decision notification: ' . $e->getMessage());
        }
    }

    /**
     * Trigger Keap automation for offer decision.
     *
     * @param Offer $offer
     * @param OfferDecision $offerDecision
     * @param string $decision
     * @return void
     */
    protected function triggerKeapEvent(Offer $offer, OfferDecision $offerDecision, string $decision): void
    {
        try {
            if ($decision === 'accepted') {
                $this->keapService->triggerOfferAccepted($offer, $offerDecision);
            }
            // Add other Keap triggers as needed for declined/countered
        } catch (\Exception $e) {
            Log::error('Keap trigger error for offer decision: ' . $e->getMessage());
        }
    }
}

