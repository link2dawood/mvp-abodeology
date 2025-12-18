<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Offer;
use App\Models\Viewing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PropertyStatusChangedNotification;

class PropertyStatusTransitionService
{
    /**
     * Valid property status transitions.
     * Maps from current status to allowed next statuses.
     */
    protected const VALID_TRANSITIONS = [
        'draft' => ['property_details_captured', 'pre_marketing', 'signed'],
        'property_details_captured' => ['pre_marketing', 'signed', 'draft'],
        'pre_marketing' => ['signed', 'live', 'draft'],
        'signed' => ['live', 'pre_marketing', 'draft', 'awaiting_aml'],
        'awaiting_aml' => ['signed', 'live'], // Can go back to signed or forward to live
        'live' => ['sstc', 'withdrawn', 'sold'],
        'sstc' => ['sold', 'live', 'withdrawn'],
        'withdrawn' => ['live', 'draft'],
        'sold' => [], // Final state - cannot transition from sold
    ];

    /**
     * Statuses that require cancellation of offers and viewings.
     */
    protected const STATUSES_REQUIRING_CANCELLATION = ['sold', 'withdrawn'];

    /**
     * Statuses that prevent buyer interactions.
     */
    protected const STATUSES_BLOCKING_BUYERS = ['sold', 'withdrawn', 'sstc'];

    /**
     * Change property status with validation and cascading updates.
     *
     * @param Property $property
     * @param string $newStatus
     * @param int|null $changedBy User ID who is making the change
     * @param string|null $reason Optional reason for the status change
     * @return array Result with success status and messages
     */
    public function changeStatus(Property $property, string $newStatus, ?int $changedBy = null, ?string $reason = null): array
    {
        $oldStatus = $property->status;
        
        // Validate transition
        $validation = $this->validateTransition($property, $newStatus);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'] ?? [],
            ];
        }

        try {
            DB::beginTransaction();

            // Update property status
            $property->update(['status' => $newStatus]);

            // Handle cascading updates
            $cascadeResult = $this->handleCascadingUpdates($property, $newStatus, $oldStatus);

            // Log the status change
            $this->logStatusChange($property, $oldStatus, $newStatus, $changedBy, $reason);

            DB::commit();

            return [
                'success' => true,
                'message' => "Property status changed from {$oldStatus} to {$newStatus}",
                'cancelled_offers' => $cascadeResult['cancelled_offers'] ?? 0,
                'cancelled_viewings' => $cascadeResult['cancelled_viewings'] ?? 0,
                'notifications_sent' => $cascadeResult['notifications_sent'] ?? 0,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property status transition failed', [
                'property_id' => $property->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to change property status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validate if status transition is allowed.
     *
     * @param Property $property
     * @param string $newStatus
     * @return array
     */
    protected function validateTransition(Property $property, string $newStatus): array
    {
        $currentStatus = $property->status;

        // Check if status is valid
        if (!isset(self::VALID_TRANSITIONS[$currentStatus])) {
            return [
                'valid' => false,
                'message' => "Current status '{$currentStatus}' is not recognized.",
            ];
        }

        // Check if transition is allowed
        $allowedStatuses = self::VALID_TRANSITIONS[$currentStatus];
        if (!in_array($newStatus, $allowedStatuses)) {
            return [
                'valid' => false,
                'message' => "Cannot transition from '{$currentStatus}' to '{$newStatus}'. Allowed transitions: " . implode(', ', $allowedStatuses),
            ];
        }

        // Special validation for sold status
        if ($newStatus === 'sold') {
            // Check if property is already in SSTC
            if ($currentStatus !== 'sstc') {
                return [
                    'valid' => false,
                    'message' => "Property must be in 'sstc' status before being marked as 'sold'.",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Handle cascading updates when property status changes.
     *
     * @param Property $property
     * @param string $newStatus
     * @param string $oldStatus
     * @return array
     */
    protected function handleCascadingUpdates(Property $property, string $newStatus, string $oldStatus): array
    {
        $result = [
            'cancelled_offers' => 0,
            'cancelled_viewings' => 0,
            'notifications_sent' => 0,
        ];

        // If status requires cancellation, cancel offers and viewings
        if (in_array($newStatus, self::STATUSES_REQUIRING_CANCELLATION)) {
            $result['cancelled_offers'] = $this->cancelPendingOffers($property, $newStatus);
            $result['cancelled_viewings'] = $this->cancelFutureViewings($property, $newStatus);
            $result['notifications_sent'] = $this->notifyAffectedUsers($property, $newStatus, $oldStatus);
        }

        return $result;
    }

    /**
     * Cancel pending offers for the property.
     *
     * @param Property $property
     * @param string $reason Status that triggered cancellation
     * @return int Number of offers cancelled
     */
    protected function cancelPendingOffers(Property $property, string $reason): int
    {
        $pendingOffers = Offer::where('property_id', $property->id)
            ->whereIn('status', ['pending', 'countered'])
            ->get();

        $cancelledCount = 0;

        foreach ($pendingOffers as $offer) {
            // Use 'withdrawn' status for offers when property is sold/withdrawn
            // This is more semantically correct than 'cancelled'
            $offer->update([
                'status' => 'withdrawn',
            ]);

            // Notify buyer
            try {
                Mail::to($offer->buyer->email)->send(
                    new PropertyStatusChangedNotification(
                        $offer->buyer,
                        $property,
                        $reason === 'sold' ? 'sold' : 'withdrawn',
                        "Your offer on {$property->address} has been cancelled because the property has been " . ($reason === 'sold' ? 'sold' : 'withdrawn') . "."
                    )
                );
            } catch (\Exception $e) {
                Log::error('Failed to send offer cancellation notification', [
                    'offer_id' => $offer->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $cancelledCount++;
        }

        Log::info("Cancelled {$cancelledCount} pending offers for property {$property->id} due to status change to {$reason}");

        return $cancelledCount;
    }

    /**
     * Cancel future viewings for the property.
     *
     * @param Property $property
     * @param string $reason Status that triggered cancellation
     * @return int Number of viewings cancelled
     */
    protected function cancelFutureViewings(Property $property, string $reason): int
    {
        $futureViewings = Viewing::where('property_id', $property->id)
            ->where('viewing_date', '>', now())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->get();

        $cancelledCount = 0;

        foreach ($futureViewings as $viewing) {
            $viewing->update([
                'status' => 'cancelled',
            ]);

            // Notify buyer
            try {
                Mail::to($viewing->buyer->email)->send(
                    new PropertyStatusChangedNotification(
                        $viewing->buyer,
                        $property,
                        $reason === 'sold' ? 'sold' : 'withdrawn',
                        "Your viewing for {$property->address} has been cancelled because the property has been " . ($reason === 'sold' ? 'sold' : 'withdrawn') . "."
                    )
                );
            } catch (\Exception $e) {
                Log::error('Failed to send viewing cancellation notification', [
                    'viewing_id' => $viewing->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Notify PVA if assigned
            if ($viewing->pva_id) {
                try {
                    Mail::to($viewing->pva->email)->send(
                        new PropertyStatusChangedNotification(
                            $viewing->pva,
                            $property,
                            $reason === 'sold' ? 'sold' : 'withdrawn',
                            "Viewing for {$property->address} has been cancelled because the property has been " . ($reason === 'sold' ? 'sold' : 'withdrawn') . "."
                        )
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send viewing cancellation notification to PVA', [
                        'viewing_id' => $viewing->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $cancelledCount++;
        }

        Log::info("Cancelled {$cancelledCount} future viewings for property {$property->id} due to status change to {$reason}");

        return $cancelledCount;
    }

    /**
     * Notify affected users about property status change.
     *
     * @param Property $property
     * @param string $newStatus
     * @param string $oldStatus
     * @return int Number of notifications sent
     */
    protected function notifyAffectedUsers(Property $property, string $newStatus, string $oldStatus): int
    {
        $notificationsSent = 0;

        // Notify seller
        try {
            Mail::to($property->seller->email)->send(
                new PropertyStatusChangedNotification(
                    $property->seller,
                    $property,
                    $newStatus,
                    "Your property at {$property->address} status has been changed from {$oldStatus} to {$newStatus}."
                )
            );
            $notificationsSent++;
        } catch (\Exception $e) {
            Log::error('Failed to send status change notification to seller', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $notificationsSent;
    }

    /**
     * Log status change for audit trail.
     *
     * @param Property $property
     * @param string $oldStatus
     * @param string $newStatus
     * @param int|null $changedBy
     * @param string|null $reason
     * @return void
     */
    protected function logStatusChange(Property $property, string $oldStatus, string $newStatus, ?int $changedBy = null, ?string $reason = null): void
    {
        Log::info('Property status changed', [
            'property_id' => $property->id,
            'property_address' => $property->address,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy ?? auth()->id(),
            'reason' => $reason,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Check if property status blocks buyer interactions.
     *
     * @param Property $property
     * @return bool
     */
    public function blocksBuyerInteractions(Property $property): bool
    {
        return in_array($property->status, self::STATUSES_BLOCKING_BUYERS);
    }

    /**
     * Get allowed status transitions for a property.
     *
     * @param Property $property
     * @return array
     */
    public function getAllowedTransitions(Property $property): array
    {
        $currentStatus = $property->status;
        return self::VALID_TRANSITIONS[$currentStatus] ?? [];
    }
}

