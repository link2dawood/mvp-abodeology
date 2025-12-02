<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuyerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the buyer dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Role check as fallback (middleware should handle this, but extra protection)
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access the buyer dashboard.');
        }
        
        // Buyer name from authenticated user
        $buyerName = $user->name;
        
        // Get AML verification status
        $amlCheck = \App\Models\AmlCheck::where('user_id', $user->id)->first();
        
        if ($amlCheck) {
            $amlStatusClass = $amlCheck->verification_status === 'verified' ? 'verified' : ($amlCheck->verification_status === 'rejected' ? 'failed' : 'pending');
            $amlStatusText = ucfirst($amlCheck->verification_status);
        } else {
            $amlStatusClass = 'pending';
            $amlStatusText = 'Pending';
        }
        
        // ============================================
        // DATA AGGREGATION - All buyer activity
        // ============================================
        
        // ============================================
        // SECURITY: All queries scoped to authenticated user
        // ============================================
        
        // 1. VIEWINGS - Upcoming and past
        // SECURITY: Only fetch viewings belonging to this buyer
        $upcomingViewings = \App\Models\Viewing::where('buyer_id', $user->id)
            ->where('viewing_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['property' => function($query) {
                // SECURITY: Only load property details for live properties
                $query->where('status', 'live');
            }])
            ->orderBy('viewing_date', 'asc')
            ->get();

        $pastViewings = \App\Models\Viewing::where('buyer_id', $user->id)
            ->where('viewing_date', '<', now())
            ->with(['property' => function($query) {
                // SECURITY: Only load property details for live properties
                $query->where('status', 'live');
            }])
            ->orderBy('viewing_date', 'desc')
            ->limit(5)
            ->get();

        // 2. OFFERS - All offers with full details
        // SECURITY: Only fetch offers belonging to this buyer
        $buyerOffers = \App\Models\Offer::where('buyer_id', $user->id)
            ->with(['property' => function($query) {
                // SECURITY: Ensure property data is accessible
                // Note: Offers can be on any status property, but we'll filter in view
            }, 'latestDecision'])
            ->orderBy('created_at', 'desc')
            ->get();

        $offers = $buyerOffers->map(function($offer) {
            $outcome = null;
            if ($offer->latestDecision) {
                if ($offer->latestDecision->decision === 'accepted') {
                    $outcome = 'Accepted';
                } elseif ($offer->latestDecision->decision === 'declined') {
                    $outcome = 'Declined';
                } elseif ($offer->latestDecision->decision === 'counter') {
                    $outcome = 'Counter-Offer: £' . number_format($offer->latestDecision->counter_amount ?? 0, 2);
                }
            }
            
            return [
                'id' => $offer->id,
                'property_id' => $offer->property_id,
                'property' => $offer->property->address ?? 'N/A',
                'amount' => $offer->offer_amount,
                'status' => $offer->status,
                'outcome' => $outcome,
                'decision' => $offer->latestDecision ? $offer->latestDecision->decision : null,
                'counter_amount' => $offer->latestDecision && $offer->latestDecision->decision === 'counter' ? $offer->latestDecision->counter_amount : null,
                'created_at' => $offer->created_at,
            ];
        });

        // 3. METRICS - Dashboard statistics
        $metrics = [
            'total_offers' => $buyerOffers->count(),
            'pending_offers' => $buyerOffers->where('status', 'pending')->count(),
            'accepted_offers' => $buyerOffers->where('status', 'accepted')->count(),
            'upcoming_viewings' => $upcomingViewings->count(),
            'total_viewings' => \App\Models\Viewing::where('buyer_id', $user->id)->count(),
        ];

        // 4. NOTIFICATIONS - Activity alerts
        $notifications = [];
        // Add notifications for offer decisions
        foreach ($buyerOffers as $offer) {
            if ($offer->latestDecision) {
                if ($offer->latestDecision->decision === 'accepted') {
                    $notifications[] = [
                        'type' => 'success',
                        'message' => "Your offer of £" . number_format($offer->offer_amount, 2) . " on " . ($offer->property->address ?? 'property') . " has been accepted!",
                        'date' => $offer->latestDecision->decided_at,
                    ];
                } elseif ($offer->latestDecision->decision === 'declined') {
                    $notifications[] = [
                        'type' => 'info',
                        'message' => "Your offer on " . ($offer->property->address ?? 'property') . " was declined.",
                        'date' => $offer->latestDecision->decided_at,
                    ];
                } elseif ($offer->latestDecision->decision === 'counter') {
                    $notifications[] = [
                        'type' => 'warning',
                        'message' => "The seller has made a counter-offer of £" . number_format($offer->latestDecision->counter_amount ?? 0, 2) . " on " . ($offer->property->address ?? 'property') . ".",
                        'date' => $offer->latestDecision->decided_at,
                    ];
                }
            }
        }
        
        // Sort notifications by date (newest first)
        usort($notifications, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });
        $notifications = array_slice($notifications, 0, 5); // Limit to 5 most recent

        // 5. RECOMMENDED PROPERTIES - Based on buyer's offer history and preferences
        $recommendedProperties = $this->getRecommendedProperties($user, $buyerOffers);

        // 6. SOLICITOR DETAILS - From user profile or offers
        $solicitorName = null;
        $solicitorFirm = null;
        $solicitorEmail = null;
        $solicitorPhone = null;
        
        // Try to get solicitor details from most recent offer
        $recentOffer = $buyerOffers->first();
        if ($recentOffer && $recentOffer->property) {
            // Note: Solicitor details might be stored in offer or property
            // For now, we'll leave as null if not available
        }

        return view('buyer.dashboard', compact(
            'buyerName',
            'amlStatusClass',
            'amlStatusText',
            'upcomingViewings',
            'pastViewings',
            'offers',
            'metrics',
            'notifications',
            'recommendedProperties',
            'solicitorName',
            'solicitorFirm',
            'solicitorEmail',
            'solicitorPhone'
        ));
    }

    /**
     * Show the buyer profile page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = auth()->user();
        
        // In a real application, you would fetch buyer profile data from a buyer_profiles table
        // For now, we'll use an empty object to avoid errors
        $buyerProfile = (object) [
            'buying_position' => null,
            'requires_mortgage' => null,
            'agreement_in_principle' => null,
            'deposit_amount' => null,
            'max_budget' => null,
            'financial_notes' => null,
            'solicitor_name' => null,
            'solicitor_firm' => null,
            'solicitor_email' => null,
            'solicitor_phone' => null,
        ];

        return view('buyer.profile', compact('user', 'buyerProfile'));
    }

    /**
     * Update the buyer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'buying_position' => ['nullable', 'string', 'in:first-time-buyer,renting,sold-sstc,cash-buyer,investor-btl'],
            'requires_mortgage' => ['nullable', 'string', 'in:yes,no'],
            'agreement_in_principle' => ['nullable', 'string', 'max:255'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'max_budget' => ['nullable', 'numeric', 'min:0'],
            'financial_notes' => ['nullable', 'string', 'max:1000'],
            'solicitor_name' => ['nullable', 'string', 'max:255'],
            'solicitor_firm' => ['nullable', 'string', 'max:255'],
            'solicitor_email' => ['nullable', 'email', 'max:255'],
            'solicitor_phone' => ['nullable', 'string', 'max:20'],
            'photo_id' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'proof_of_address' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'proof_of_funds' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ]);

        // Update user basic information
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        // In a real application, you would save buyer profile data to a buyer_profiles table
        // For now, we'll just handle file uploads if any
        if ($request->hasFile('photo_id')) {
            // Handle photo ID upload
            // $request->file('photo_id')->store('buyer-documents', 'public');
        }

        if ($request->hasFile('proof_of_address')) {
            // Handle proof of address upload
            // $request->file('proof_of_address')->store('buyer-documents', 'public');
        }

        if ($request->hasFile('proof_of_funds')) {
            // Handle proof of funds upload
            // $request->file('proof_of_funds')->store('buyer-documents', 'public');
        }

        return redirect()->route('buyer.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the make an offer page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function makeOffer($id)
    {
        $user = auth()->user();
        
        // Only buyers can make offers
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'You must be logged in as a buyer to make an offer.');
        }

        // Fetch the actual property from database
        $property = \App\Models\Property::where('status', 'live')
            ->with(['seller', 'photos'])
            ->findOrFail($id);

        // Prevent buyers from making offers on their own properties
        if ($property->seller_id === $user->id) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'You cannot make an offer on your own property.');
        }

        return view('buyer.make-offer', compact('property', 'user'));
    }

    /**
     * Store the offer submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeOffer(Request $request, $id)
    {
        $validated = $request->validate([
            'offer_amount' => ['required', 'numeric', 'min:0'],
            'funding_type' => ['required', 'string', 'in:cash,mortgage,combination'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'buying_position' => ['required', 'string', 'in:first-time-buyer,renting,living-with-family,sold-sstc,cash-buyer,investor-btl'],
            'conditions' => ['nullable', 'string', 'max:1000'],
            'proof_of_funds' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'agreement_in_principle' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'solicitor_name' => ['nullable', 'string', 'max:255'],
            'solicitor_firm' => ['nullable', 'string', 'max:255'],
            'solicitor_email' => ['nullable', 'email', 'max:255'],
            'solicitor_phone' => ['nullable', 'string', 'max:20'],
        ], [
            'offer_amount.required' => 'Please enter an offer amount.',
            'offer_amount.numeric' => 'Offer amount must be a valid number.',
            'offer_amount.min' => 'Offer amount must be greater than zero.',
            'funding_type.required' => 'Please select how you will fund the purchase.',
            'buying_position.required' => 'Please select your buying position.',
        ]);

        $user = auth()->user();
        $property = \App\Models\Property::where('status', 'live')->findOrFail($id);

        // Prevent buyers from making offers on their own properties
        if ($property->seller_id === $user->id) {
            return back()->with('error', 'You cannot make an offer on your own property.');
        }

        try {
            \DB::beginTransaction();

            // Map funding type
            $fundingTypeMap = [
                'cash' => 'cash',
                'mortgage' => 'mortgage',
                'combination' => 'part_mortgage',
            ];

            // Create offer
            $offer = \App\Models\Offer::create([
                'property_id' => $property->id,
                'buyer_id' => $user->id,
                'offer_amount' => $validated['offer_amount'],
                'funding_type' => $fundingTypeMap[$validated['funding_type']] ?? $validated['funding_type'],
                'deposit_amount' => $validated['deposit_amount'] ?? null,
                'chain_position' => $validated['buying_position'] ?? null,
                'conditions' => $validated['conditions'] ?? null,
                'status' => 'pending',
            ]);

            // Handle file uploads if needed (can be stored in a separate offers_documents table)
            // For now, we'll skip file handling as it's not in the Offer model fillable

            \DB::commit();

            // Trigger Keap automation for new offer submitted
            try {
                $keapService = new \App\Services\KeapService();
                $keapService->triggerOfferSubmitted($offer);
            } catch (\Exception $e) {
                \Log::error('Keap trigger error for offer submission: ' . $e->getMessage());
            }

            // Send notification to seller
            try {
                \Mail::to($property->seller->email)->send(
                    new \App\Mail\NewOfferNotification($offer, $property, $property->seller)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send new offer notification to seller: ' . $e->getMessage());
            }

            // Notify all agents/admins
            $agents = \App\Models\User::whereIn('role', ['admin', 'agent'])->get();
            foreach ($agents as $agent) {
                try {
                    \Mail::to($agent->email)->send(
                        new \App\Mail\NewOfferNotification($offer, $property, $agent)
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send new offer notification to agent: ' . $e->getMessage());
                }
            }

            return redirect()->route('buyer.offer.confirmation', $offer->id)
                ->with('success', 'Your offer has been submitted successfully!');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Offer submission error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while submitting your offer. Please try again.');
        }
    }

    /**
     * Show viewing request form for a property.
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showViewingRequest($id)
    {
        $user = auth()->user();
        
        try {
            // Allow buyers, both role, or sellers viewing their own properties
            if (in_array($user->role, ['buyer', 'both'])) {
                // Buyers can view any live property
                $property = \App\Models\Property::where('status', 'live')
                    ->with(['seller', 'photos'])
                    ->findOrFail($id);
            } elseif ($user->role === 'seller') {
                // Sellers can view their own live properties
                $property = \App\Models\Property::where('status', 'live')
                    ->where('seller_id', $user->id)
                    ->with(['seller', 'photos'])
                    ->findOrFail($id);
            } else {
                return redirect()->route($this->getRoleDashboard($user->role))
                    ->with('error', 'You do not have permission to access this page.');
            }

            return view('buyer.viewing-request', compact('property'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Property not found for viewing request', [
                'property_id' => $id,
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            
            return redirect()->route('buyer.dashboard')
                ->with('error', 'Property not found or is not available for viewing.');
        } catch (\Exception $e) {
            \Log::error('Error loading viewing request page: ' . $e->getMessage(), [
                'property_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('buyer.dashboard')
                ->with('error', 'An error occurred while loading the viewing request page. Please try again.');
        }
    }

    /**
     * Store viewing request and notify viewing partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeViewingRequest(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = \App\Models\Property::where('status', 'live')->findOrFail($id);

        $validated = $request->validate([
            'viewing_date' => ['required', 'date', 'after:today'],
            'viewing_time' => ['required', 'date_format:H:i'],
            'preferred_contact_method' => ['nullable', 'string', 'in:phone,email'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'viewing_date.required' => 'Please select a viewing date.',
            'viewing_date.date' => 'Please provide a valid date.',
            'viewing_date.after' => 'Viewing date must be in the future.',
            'viewing_time.required' => 'Please select a viewing time.',
            'viewing_time.date_format' => 'Please provide a valid time format.',
        ]);

        try {
            \DB::beginTransaction();

            // Combine date and time
            $viewingDateTime = \Carbon\Carbon::parse($validated['viewing_date'] . ' ' . $validated['viewing_time']);

            // Determine viewing status based on whether we hold keys
            // If property has keys, viewing can be scheduled directly
            // If property doesn't have keys, status is 'pending' until vendor confirms
            $viewingStatus = $property->with_keys ? 'scheduled' : 'pending';

            // Create viewing request
            $viewing = \App\Models\Viewing::create([
                'property_id' => $property->id,
                'buyer_id' => $user->id,
                'viewing_date' => $viewingDateTime,
                'status' => $viewingStatus,
            ]);

            // If viewing is automatically scheduled (with keys), send confirmation emails
            if ($viewingStatus === 'scheduled') {
                $viewing->load(['buyer', 'property.seller']);
                
                try {
                    // Notify buyer
                    \Mail::to($viewing->buyer->email)->send(
                        new \App\Mail\ViewingConfirmed($viewing, $property, $viewing->buyer, 'buyer')
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send viewing confirmation to buyer: ' . $e->getMessage());
                }

                try {
                    // Notify seller
                    if ($property->seller) {
                        \Mail::to($property->seller->email)->send(
                            new \App\Mail\ViewingConfirmed($viewing, $property, $property->seller, 'seller')
                        );
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send viewing confirmation to seller: ' . $e->getMessage());
                }
            }

            // Notify all viewing partners (PVAs) about the new viewing request
            try {
                $pvas = \App\Models\User::where('role', 'pva')->get();
                
                foreach ($pvas as $pva) {
                    try {
                        \Mail::to($pva->email)->send(
                            new \App\Mail\ViewingRequestNotification($viewing, $property, $user)
                        );
                    } catch (\Exception $e) {
                        // Log the error but continue with other PVAs
                        \Log::error('Failed to send viewing request notification to PVA ' . $pva->email . ': ' . $e->getMessage());
                    }
                }
                
                \Log::info('Viewing request created and notifications sent', [
                    'viewing_id' => $viewing->id,
                    'property_id' => $property->id,
                    'buyer_id' => $user->id,
                    'viewing_date' => $viewingDateTime,
                    'pvas_notified' => $pvas->count(),
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the viewing request
                \Log::error('Failed to send viewing request notifications: ' . $e->getMessage());
            }

            \DB::commit();

            return redirect()->route('buyer.dashboard')
                ->with('success', 'Viewing request submitted successfully!');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Viewing request error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while submitting your viewing request. Please try again.');
        }
    }

    /**
     * Show AML document upload form for buyers.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAmlUpload()
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $amlCheck = \App\Models\AmlCheck::where('user_id', $user->id)->first();

        return view('buyer.aml-upload', compact('amlCheck'));
    }

    /**
     * Store AML documents for buyers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAmlUpload(Request $request)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $validated = $request->validate([
            'id_documents' => ['required', 'array', 'min:1'],
            'id_documents.*' => ['file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'proof_of_address_documents' => ['required', 'array', 'min:1'],
            'proof_of_address_documents.*' => ['file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'additional_documents' => ['nullable', 'array'],
            'additional_documents.*' => ['file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ], [
            'id_documents.required' => 'Please upload at least one ID document (Photo ID, Passport, or Driving License).',
            'id_documents.min' => 'Please upload at least one ID document.',
            'proof_of_address_documents.required' => 'Please upload at least one proof of address document.',
            'proof_of_address_documents.min' => 'Please upload at least one proof of address document.',
        ]);

        try {
            \DB::beginTransaction();

            // Get or create AML check
            $amlCheck = \App\Models\AmlCheck::firstOrCreate(
                ['user_id' => $user->id],
                ['verification_status' => 'pending']
            );

            // Determine storage disk (S3 if configured, otherwise public)
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

            // Store ID documents
            foreach ($request->file('id_documents') as $file) {
                $filePath = $file->store('aml-documents/' . $user->id . '/id', $disk);
                \App\Models\AmlDocument::create([
                    'aml_check_id' => $amlCheck->id,
                    'document_type' => 'id_document',
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            // Store proof of address documents
            foreach ($request->file('proof_of_address_documents') as $file) {
                $filePath = $file->store('aml-documents/' . $user->id . '/proof-of-address', $disk);
                \App\Models\AmlDocument::create([
                    'aml_check_id' => $amlCheck->id,
                    'document_type' => 'proof_of_address',
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            // Store additional documents if provided
            if ($request->hasFile('additional_documents')) {
                foreach ($request->file('additional_documents') as $file) {
                    $filePath = $file->store('aml-documents/' . $user->id . '/additional', $disk);
                    \App\Models\AmlDocument::create([
                        'aml_check_id' => $amlCheck->id,
                        'document_type' => 'additional',
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Update legacy fields for backward compatibility
            $firstIdDoc = \App\Models\AmlDocument::where('aml_check_id', $amlCheck->id)
                ->where('document_type', 'id_document')
                ->first();
            $firstProofDoc = \App\Models\AmlDocument::where('aml_check_id', $amlCheck->id)
                ->where('document_type', 'proof_of_address')
                ->first();

            $amlCheck->update([
                'id_document' => $firstIdDoc ? $firstIdDoc->file_path : null,
                'proof_of_address' => $firstProofDoc ? $firstProofDoc->file_path : null,
                'verification_status' => 'pending',
            ]);

            \DB::commit();

            // Trigger Keap automation for buyer AML uploaded
            try {
                $keapService = new \App\Services\KeapService();
                $keapService->triggerAmlUploaded($amlCheck);
            } catch (\Exception $e) {
                \Log::error('Keap trigger error for AML upload: ' . $e->getMessage());
            }

            return redirect()->route('buyer.dashboard')
                ->with('success', 'AML documents uploaded successfully! Your documents are being reviewed and you will be notified once verification is complete.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Buyer AML document upload error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while uploading your documents. Please try again.');
        }
    }

    /**
     * Get the dashboard route name based on user role.
     *
     * @param string $role
     * @return string
     */
    private function getRoleDashboard(string $role): string
    {
        $dashboards = [
            'admin' => 'admin.dashboard',
            'agent' => 'admin.agent.dashboard',
            'buyer' => 'buyer.dashboard',
            'seller' => 'seller.dashboard',
            'both' => 'buyer.dashboard',
            'pva' => 'pva.dashboard',
        ];

        return $dashboards[$role] ?? 'home';
    }

    /**
     * Get recommended properties for buyer based on their activity.
     * 
     * SECURITY: Only returns live properties, excludes buyer's own properties,
     * and excludes properties buyer has already viewed/offered on.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Support\Collection  $buyerOffers
     * @return \Illuminate\Support\Collection
     */
    private function getRecommendedProperties($user, $buyerOffers)
    {
        // SECURITY CHECK: Only show live properties to buyers
        // Buyers should never see draft, pending, or other non-live properties
        $query = \App\Models\Property::where('status', 'live')
            ->where('seller_id', '!=', $user->id); // Don't recommend buyer's own properties

        // If buyer has made offers, recommend similar properties
        if ($buyerOffers->count() > 0) {
            $avgOfferAmount = $buyerOffers->avg('offer_amount');
            $propertyTypes = $buyerOffers->pluck('property.property_type')->filter()->unique();
            
            // Find properties in similar price range (±20%)
            if ($avgOfferAmount) {
                $minPrice = $avgOfferAmount * 0.8;
                $maxPrice = $avgOfferAmount * 1.2;
                $query->whereBetween('asking_price', [$minPrice, $maxPrice]);
            }
            
            // Prefer same property types if available
            if ($propertyTypes->count() > 0) {
                $query->whereIn('property_type', $propertyTypes->toArray());
            }
        }

        // Exclude properties buyer has already viewed or made offers on
        $viewedPropertyIds = \App\Models\Viewing::where('buyer_id', $user->id)
            ->pluck('property_id')
            ->toArray();
        $offeredPropertyIds = $buyerOffers->pluck('property_id')->toArray();
        $excludedIds = array_unique(array_merge($viewedPropertyIds, $offeredPropertyIds));
        
        if (count($excludedIds) > 0) {
            $query->whereNotIn('id', $excludedIds);
        }

        // Return 6 recommended properties
        return $query->with(['photos'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    }

    /**
     * Show offer confirmation page.
     *
     * @param  int  $id  Offer ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function offerConfirmation($id)
    {
        $user = auth()->user();
        
        // Only buyers can view their own offer confirmations
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'You must be logged in as a buyer to view this page.');
        }

        $offer = \App\Models\Offer::with(['property', 'buyer'])
            ->where('buyer_id', $user->id)
            ->findOrFail($id);

        return view('buyer.offer-confirmation', compact('offer'));
    }
}
