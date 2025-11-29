<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\KeapEventLog;

class KeapService
{
    protected $apiUrl;
    protected $apiKey;
    protected $enabled;
    
    public function __construct()
    {
        $this->apiUrl = config('keap.api_url', 'https://api.infusionsoft.com/crm/rest/v1');
        $this->apiKey = config('keap.api_key', '');
        $this->enabled = config('keap.enabled', false);
    }
    
    /**
     * Trigger event for new seller onboarded.
     *
     * @param \App\Models\User $seller
     * @param array $additionalData
     * @return bool
     */
    public function triggerSellerOnboarded($seller, array $additionalData = []): bool
    {
        return $this->triggerEvent('seller_onboarded', [
            'contact_id' => $seller->id,
            'email' => $seller->email,
            'name' => $seller->name,
            'phone' => $seller->phone ?? '',
            'role' => $seller->role,
            'onboarded_at' => now()->toIso8601String(),
            'custom_fields' => [
                'seller_id' => $seller->id,
                'user_role' => $seller->role,
                'registration_date' => $seller->created_at->format('Y-m-d'),
            ],
            ...$additionalData,
        ]);
    }
    
    /**
     * Trigger event for new buyer registered.
     *
     * @param \App\Models\User $buyer
     * @param array $additionalData
     * @return bool
     */
    public function triggerBuyerRegistered($buyer, array $additionalData = []): bool
    {
        return $this->triggerEvent('buyer_registered', [
            'contact_id' => $buyer->id,
            'email' => $buyer->email,
            'name' => $buyer->name,
            'phone' => $buyer->phone ?? '',
            'role' => $buyer->role,
            'registered_at' => now()->toIso8601String(),
            'custom_fields' => [
                'buyer_id' => $buyer->id,
                'user_role' => $buyer->role,
                'registration_date' => $buyer->created_at->format('Y-m-d'),
            ],
            ...$additionalData,
        ]);
    }
    
    /**
     * Trigger event for new offer submitted.
     *
     * @param \App\Models\Offer $offer
     * @param array $additionalData
     * @return bool
     */
    public function triggerOfferSubmitted($offer, array $additionalData = []): bool
    {
        $offer->load(['buyer', 'property.seller']);
        
        return $this->triggerEvent('offer_submitted', [
            'contact_id' => $offer->buyer->id,
            'email' => $offer->buyer->email,
            'name' => $offer->buyer->name,
            'phone' => $offer->buyer->phone ?? '',
            'offer_id' => $offer->id,
            'offer_amount' => $offer->offer_amount,
            'property_id' => $offer->property_id,
            'property_address' => $offer->property->address ?? '',
            'property_price' => $offer->property->asking_price ?? 0,
            'submitted_at' => $offer->created_at->toIso8601String(),
            'custom_fields' => [
                'offer_id' => $offer->id,
                'offer_amount' => number_format($offer->offer_amount, 2),
                'deposit_amount' => number_format($offer->deposit_amount ?? 0, 2),
                'funding_type' => $offer->funding_type ?? '',
                'chain_position' => $offer->chain_position ?? '',
                'property_address' => $offer->property->address ?? '',
                'seller_id' => $offer->property->seller_id ?? null,
            ],
            ...$additionalData,
        ]);
    }
    
    /**
     * Trigger event for seller accepts offer.
     *
     * @param \App\Models\Offer $offer
     * @param \App\Models\OfferDecision $decision
     * @param array $additionalData
     * @return bool
     */
    public function triggerOfferAccepted($offer, $decision, array $additionalData = []): bool
    {
        $offer->load(['buyer', 'property.seller']);
        
        return $this->triggerEvent('offer_accepted', [
            'contact_id' => $offer->buyer->id,
            'email' => $offer->buyer->email,
            'name' => $offer->buyer->name,
            'phone' => $offer->buyer->phone ?? '',
            'offer_id' => $offer->id,
            'offer_amount' => $offer->offer_amount,
            'property_id' => $offer->property_id,
            'property_address' => $offer->property->address ?? '',
            'seller_id' => $offer->property->seller_id ?? null,
            'seller_name' => $offer->property->seller->name ?? '',
            'seller_email' => $offer->property->seller->email ?? '',
            'accepted_at' => $decision->decided_at->toIso8601String(),
            'custom_fields' => [
                'offer_id' => $offer->id,
                'offer_amount' => number_format($offer->offer_amount, 2),
                'property_address' => $offer->property->address ?? '',
                'property_price' => number_format($offer->property->asking_price ?? 0, 2),
                'decision_comments' => $decision->comments ?? '',
                'seller_id' => $offer->property->seller_id ?? null,
            ],
            ...$additionalData,
        ]);
    }
    
    /**
     * Trigger event for buyer AML uploaded.
     *
     * @param \App\Models\AmlCheck $amlCheck
     * @param array $additionalData
     * @return bool
     */
    public function triggerAmlUploaded($amlCheck, array $additionalData = []): bool
    {
        $amlCheck->load('user');
        
        return $this->triggerEvent('aml_uploaded', [
            'contact_id' => $amlCheck->user->id,
            'email' => $amlCheck->user->email,
            'name' => $amlCheck->user->name,
            'phone' => $amlCheck->user->phone ?? '',
            'aml_check_id' => $amlCheck->id,
            'verification_status' => $amlCheck->verification_status,
            'uploaded_at' => $amlCheck->created_at->toIso8601String(),
            'custom_fields' => [
                'aml_check_id' => $amlCheck->id,
                'verification_status' => $amlCheck->verification_status,
                'document_count' => $amlCheck->documents()->count(),
                'upload_date' => $amlCheck->created_at->format('Y-m-d'),
            ],
            ...$additionalData,
        ]);
    }
    
    /**
     * Trigger event for PVA submits feedback.
     *
     * @param \App\Models\ViewingFeedback $feedback
     * @param array $additionalData
     * @return bool
     */
    public function triggerPvaFeedbackSubmitted($feedback, array $additionalData = []): bool
    {
        $feedback->load(['viewing.buyer', 'viewing.property.seller', 'viewing.pva']);
        
        return $this->triggerEvent('pva_feedback_submitted', [
            'contact_id' => $feedback->viewing->buyer->id ?? null,
            'email' => $feedback->viewing->buyer->email ?? '',
            'name' => $feedback->viewing->buyer->name ?? '',
            'phone' => $feedback->viewing->buyer->phone ?? '',
            'viewing_id' => $feedback->viewing_id,
            'property_id' => $feedback->viewing->property_id ?? null,
            'property_address' => $feedback->viewing->property->address ?? '',
            'pva_id' => $feedback->viewing->pva_id ?? null,
            'pva_name' => $feedback->viewing->pva->name ?? '',
            'buyer_interested' => $feedback->buyer_interested ? 'Yes' : 'No',
            'property_condition' => $feedback->property_condition ?? '',
            'submitted_at' => $feedback->created_at->toIso8601String(),
            'custom_fields' => [
                'viewing_id' => $feedback->viewing_id,
                'property_id' => $feedback->viewing->property_id ?? null,
                'property_address' => $feedback->viewing->property->address ?? '',
                'buyer_interested' => $feedback->buyer_interested ? 'Yes' : 'No',
                'property_condition' => $feedback->property_condition ?? '',
                'buyer_feedback' => substr($feedback->buyer_feedback ?? '', 0, 500),
                'pva_notes' => substr($feedback->pva_notes ?? '', 0, 500),
                'seller_id' => $feedback->viewing->property->seller_id ?? null,
            ],
            ...$additionalData,
        ]);
    }
    
    /**
     * Generic event trigger method.
     *
     * @param string $eventType
     * @param array $data
     * @return bool
     */
    protected function triggerEvent(string $eventType, array $data): bool
    {
        // Log event attempt
        $log = KeapEventLog::create([
            'event_type' => $eventType,
            'status' => 'pending',
            'payload' => $data,
            'response' => null,
            'error_message' => null,
        ]);
        
        // If Keap is disabled, mark as skipped
        if (!$this->enabled) {
            $log->update([
                'status' => 'skipped',
                'response' => ['message' => 'Keap integration is disabled'],
            ]);
            Log::info("Keap event skipped (disabled): {$eventType}", $data);
            return false;
        }
        
        // If API key is not set, mark as failed
        if (empty($this->apiKey)) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Keap API key not configured',
            ]);
            Log::warning("Keap event failed (no API key): {$eventType}", $data);
            return false;
        }
        
        try {
            // Map event type to Keap webhook/API endpoint
            $endpoint = $this->getEndpointForEvent($eventType);
            
            // Prepare request payload
            $payload = $this->preparePayload($eventType, $data);
            
            // Make API call
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($endpoint, $payload);
            
            // Log response
            $log->update([
                'status' => $response->successful() ? 'success' : 'failed',
                'response' => $response->json() ?? $response->body(),
                'error_message' => $response->successful() ? null : ($response->json()['message'] ?? $response->body()),
            ]);
            
            if ($response->successful()) {
                Log::info("Keap event triggered successfully: {$eventType}", [
                    'event_id' => $log->id,
                    'response' => $response->json(),
                ]);
                return true;
            } else {
                Log::error("Keap event failed: {$eventType}", [
                    'event_id' => $log->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'response' => ['exception' => get_class($e)],
            ]);
            
            Log::error("Keap event exception: {$eventType}", [
                'event_id' => $log->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return false;
        }
    }
    
    /**
     * Get Keap API endpoint for event type.
     *
     * @param string $eventType
     * @return string
     */
    protected function getEndpointForEvent(string $eventType): string
    {
        // Map event types to Keap endpoints
        // In production, these would be actual Keap webhook URLs or API endpoints
        $endpoints = [
            'seller_onboarded' => $this->apiUrl . '/contacts',
            'buyer_registered' => $this->apiUrl . '/contacts',
            'offer_submitted' => $this->apiUrl . '/contacts',
            'offer_accepted' => $this->apiUrl . '/contacts',
            'aml_uploaded' => $this->apiUrl . '/contacts',
            'pva_feedback_submitted' => $this->apiUrl . '/contacts',
        ];
        
        return $endpoints[$eventType] ?? $this->apiUrl . '/webhooks/trigger';
    }
    
    /**
     * Get contact ID from event data.
     *
     * @param array $data
     * @return string|null
     */
    protected function getContactId(array $data): ?string
    {
        return $data['contact_id'] ?? null;
    }
    
    /**
     * Prepare payload for Keap API.
     *
     * @param string $eventType
     * @param array $data
     * @return array
     */
    protected function preparePayload(string $eventType, array $data): array
    {
        // Base payload structure for Keap
        $payload = [
            'event_type' => $eventType,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ];
        
        // Add event-specific mappings
        switch ($eventType) {
            case 'seller_onboarded':
            case 'buyer_registered':
                // Create/update contact
                $payload = [
                    'email_addresses' => [
                        [
                            'email' => $data['email'],
                            'field' => 'EMAIL1',
                        ],
                    ],
                    'given_name' => explode(' ', $data['name'])[0] ?? '',
                    'family_name' => implode(' ', array_slice(explode(' ', $data['name']), 1)) ?? '',
                    'phone_numbers' => $data['phone'] ? [
                        [
                            'number' => $data['phone'],
                            'field' => 'PHONE1',
                        ],
                    ] : [],
                    'custom_fields' => $data['custom_fields'] ?? [],
                ];
                break;
                
            case 'offer_submitted':
            case 'offer_accepted':
            case 'aml_uploaded':
            case 'pva_feedback_submitted':
                // Apply tag/update contact
                $payload = [
                    'contact_id' => $data['contact_id'] ?? null,
                    'tag_id' => $this->getTagIdForEvent($eventType),
                    'custom_fields' => $data['custom_fields'] ?? [],
                ];
                break;
        }
        
        return $payload;
    }
    
    /**
     * Get Keap tag ID for event type.
     *
     * @param string $eventType
     * @return int|null
     */
    protected function getTagIdForEvent(string $eventType): ?int
    {
        // Map event types to Keap tag IDs
        // These would be configured in Keap and stored in config
        $tagIds = [
            'offer_submitted' => config('keap.tags.offer_submitted', null),
            'offer_accepted' => config('keap.tags.offer_accepted', null),
            'aml_uploaded' => config('keap.tags.aml_uploaded', null),
            'pva_feedback_submitted' => config('keap.tags.pva_feedback_submitted', null),
        ];
        
        return $tagIds[$eventType] ?? null;
    }
}

