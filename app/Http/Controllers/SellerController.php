<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellerController extends Controller
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
     * Show the seller dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Role check as fallback (middleware should handle this, but extra protection)
        if (!in_array($user->role, ['seller', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access the seller dashboard.');
        }
        
        // Fetch seller's properties
        $properties = \App\Models\Property::where('seller_id', $user->id)
            ->with(['seller', 'instruction'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get upcoming valuations for display (always fetch for unified dashboard)
        $upcomingValuations = \App\Models\Valuation::where('seller_id', $user->id)
            ->where(function($q) {
                $q->where('status', 'scheduled')
                  ->orWhere('status', 'pending');
            })
            ->where(function($q) {
                $q->whereNull('valuation_date')
                  ->orWhere('valuation_date', '>=', now());
            })
            ->orderBy('valuation_date', 'asc')
            ->orderBy('valuation_time', 'asc')
            ->get();

        // Get AML check for the seller
        $amlCheck = \App\Models\AmlCheck::where('user_id', $user->id)->first();
        
        // Fetch valuations for the seller
        $valuations = \App\Models\Valuation::where('seller_id', $user->id)
            ->orderBy('valuation_date', 'desc')
            ->orderBy('valuation_time', 'desc')
            ->get();
        
        // Create a map of valuations by property address for easy lookup
        $valuationsByAddress = [];
        foreach ($valuations as $valuation) {
            $key = strtolower(trim($valuation->property_address ?? ''));
            if ($key) {
                $valuationsByAddress[$key] = $valuation;
            }
        }
        
        // Attach valuations to properties
        foreach ($properties as $prop) {
            $propKey = strtolower(trim($prop->address ?? ''));
            $prop->valuation = $valuationsByAddress[$propKey] ?? null;
        }
        
        // Status map
        $statusMap = [
            'draft' => 'Draft',
            'property_details_captured' => 'Property Details Captured',
            'property_details_completed' => 'Property Details Completed', // Legacy support
            'pre_marketing' => 'Pre-Marketing',
            'awaiting_aml' => 'Awaiting AML',
            'signed' => 'Signed',
            'live' => 'Live',
            'sstc' => 'Sold Subject to Contract',
            'withdrawn' => 'Withdrawn',
            'sold' => 'Sold',
        ];
        
        // Add status text to each property
        foreach ($properties as $prop) {
            $prop->status_text = $statusMap[$prop->status] ?? ucfirst($prop->status);
        }
        
        // Get the primary property (most recent or first one)
        $property = $properties->first();
        
        // ============================================
        // COMPREHENSIVE DATA AGGREGATION
        // ============================================
        
        $upcomingViewings = collect();
        $allViewings = collect();
        $offers = collect();
        $allOffers = collect();
        $materialInfo = collect();
        $homecheckReports = collect();
        $salesProgression = collect();
        
        if ($properties->count() > 0) {
            $propertyIds = $properties->pluck('id');
            
            // 1. VIEWINGS - Upcoming and all
            $upcomingViewings = \App\Models\Viewing::whereIn('property_id', $propertyIds)
                ->where('viewing_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->with(['buyer', 'property'])
                ->orderBy('viewing_date', 'asc')
                ->get();
            
            $allViewings = \App\Models\Viewing::whereIn('property_id', $propertyIds)
                ->with(['buyer', 'property'])
                ->orderBy('viewing_date', 'desc')
                ->get();
            
            // 2. OFFERS - Pending and all offers
            $offers = \App\Models\Offer::whereIn('property_id', $propertyIds)
                ->whereIn('status', ['pending', 'countered'])
                ->with(['buyer', 'property', 'latestDecision'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $allOffers = \App\Models\Offer::whereIn('property_id', $propertyIds)
                ->with(['buyer', 'property', 'latestDecision'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // 3. MATERIAL INFORMATION - For all properties
            $materialInfo = \App\Models\PropertyMaterialInformation::whereIn('property_id', $propertyIds)
                ->with(['property'])
                ->get();
            
            // 4. HOMECHECK REPORTS - For all properties
            $homecheckReports = \App\Models\HomecheckReport::whereIn('property_id', $propertyIds)
                ->with(['property', 'homecheckData'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // 5. SALES PROGRESSION - For properties in SSTC or sold status
            $salesProgression = \App\Models\SalesProgression::whereIn('property_id', $propertyIds)
                ->with(['property', 'buyer', 'offer'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Load relationships for the primary property if it exists
            if ($property) {
                $property->load([
                    'materialInformation', 
                    'homecheckData', 
                    'homecheckReports',
                    'salesProgression.buyer',
                    'salesProgression.offer',
                    'viewings.feedback',
                    'viewings.buyer'
                ]);
            }
        }
        
        // 7. VIEWING FEEDBACK SUMMARY - Aggregate feedback for seller's properties
        $viewingFeedbackSummary = collect();
        if ($properties->count() > 0) {
            $propertyIds = $properties->pluck('id');
            $viewingsWithFeedback = \App\Models\Viewing::whereIn('property_id', $propertyIds)
                ->whereHas('feedback')
                ->with(['feedback', 'buyer', 'property', 'pva'])
                ->orderBy('viewing_date', 'desc')
                ->get();
            
            $viewingFeedbackSummary = $viewingsWithFeedback->map(function($viewing) {
                return [
                    'viewing_id' => $viewing->id,
                    'property_address' => $viewing->property->address ?? 'N/A',
                    'buyer_name' => $viewing->buyer->name ?? 'N/A',
                    'viewing_date' => $viewing->viewing_date,
                    'buyer_interested' => $viewing->feedback->buyer_interested ?? null,
                    'buyer_feedback' => $viewing->feedback->buyer_feedback ?? null,
                    'property_condition' => $viewing->feedback->property_condition ?? null,
                    'pva_name' => $viewing->pva->name ?? 'N/A',
                ];
            });
        }

        // Load photos for properties
        $properties->load('photos');
        
        // Always return unified dashboard with all sections
        return view('seller.dashboard', compact(
            'properties',
            'property',
            'upcomingViewings',
            'allViewings',
            'offers',
            'allOffers',
            'materialInfo',
            'homecheckReports',
            'salesProgression',
            'viewingFeedbackSummary',
            'amlCheck',
            'valuations',
            'upcomingValuations'
        ));
    }

    /**
     * List all seller's properties.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        
        $properties = \App\Models\Property::where('seller_id', $user->id)
            ->with(['seller', 'offers', 'viewings'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $statusMap = [
            'draft' => 'Draft',
            'property_details_captured' => 'Property Details Captured',
            'property_details_completed' => 'Property Details Completed', // Legacy support
            'pre_marketing' => 'Pre-Marketing',
            'live' => 'Live',
            'sstc' => 'Sold Subject to Contract',
            'withdrawn' => 'Withdrawn',
            'sold' => 'Sold',
        ];
        
        foreach ($properties as $property) {
            $property->status_text = $statusMap[$property->status] ?? ucfirst($property->status);
        }
        
        return view('seller.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new property.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /**
     * Create property - DISABLED: Sellers cannot create properties.
     * Properties are created by agents after valuation.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createProperty()
    {
        return redirect()->route('seller.dashboard')
            ->with('error', 'Properties are created by agents after your valuation appointment. You cannot create properties directly.');
    }

    /**
     * Store property - DISABLED: Sellers cannot create properties.
     * Properties are created by agents after valuation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeProperty(Request $request)
    {
        return redirect()->route('seller.dashboard')
            ->with('error', 'Properties are created by agents after your valuation appointment. You cannot create properties directly.');
    }

    /**
     * Display the specified property.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showProperty($id)
    {
        $user = auth()->user();
        
        $property = \App\Models\Property::where('seller_id', $user->id)
            ->where('id', $id)
            ->with(['seller', 'offers.buyer', 'viewings.buyer', 'materialInformation', 'instruction', 'homecheckReports'])
            ->firstOrFail();

        // Get AML check for the seller
        $amlCheck = \App\Models\AmlCheck::where('user_id', $user->id)->first();
        
        $statusMap = [
            'draft' => 'Draft',
            'property_details_captured' => 'Property Details Captured',
            'property_details_completed' => 'Property Details Completed', // Legacy support
            'pre_marketing' => 'Pre-Marketing',
            'signed' => 'Signed',
            'live' => 'Live',
            'sstc' => 'Sold Subject to Contract',
            'withdrawn' => 'Withdrawn',
            'sold' => 'Sold',
        ];
        
        $property->status_text = $statusMap[$property->status] ?? ucfirst($property->status);
        
        return view('seller.properties.show', compact('property', 'amlCheck'));
    }

    /**
     * Show the seller onboarding page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showOnboarding($id)
    {
        // In a real application, you would fetch onboarding data from database
        // For now, we'll use sample data
        $user = auth()->user();
        $property = \App\Models\Property::where('seller_id', $user->id)->findOrFail($id);
        
        $propertyId = $id;
        $onboarding = (object) [
            'property_address' => $property->address,
            'property_type' => $property->property_type,
            'bedrooms' => $property->bedrooms,
            'bathrooms' => $property->bathrooms,
            'parking' => $property->parking,
            'tenure' => $property->tenure,
            'lease_years' => $property->lease_years_remaining,
            'ground_rent' => $property->ground_rent,
            'service_charge' => $property->service_charge,
            'managing_agent' => $property->managing_agent,
            'seller2_name' => $property->seller2_name,
            'seller2_email' => $property->seller2_email,
            'seller2_phone' => $property->seller2_phone,
            'reception_rooms' => $property->reception_rooms,
            'outbuildings' => $property->outbuildings,
            'garden_details' => $property->garden_details,
            'legal_owner' => null,
            'mortgaged' => null,
            'mortgage_lender' => null,
            'notices_charges' => null,
            'gas_supply' => null,
            'electricity_supply' => null,
            'mains_water' => null,
            'drainage' => null,
            'boiler_age' => null,
            'last_boiler_service' => null,
            'epc_rating' => null,
            'known_issues' => null,
            'alterations' => null,
            'viewing_contact' => null,
            'preferred_viewing_times' => null,
            'access_notes' => null,
            'for_sale_board' => null,
            'photography_homecheck' => null,
            'publish_marketing' => null,
        ];
        
        // Load material information if exists
        if ($property->materialInformation) {
            $materialInfo = $property->materialInformation;
            $onboarding->gas_supply = $materialInfo->gas_supply ? 'yes' : 'no';
            $onboarding->electricity_supply = $materialInfo->electricity_supply ? 'yes' : 'no';
            $onboarding->mains_water = $materialInfo->mains_water ? 'yes' : 'no';
            $onboarding->drainage = $materialInfo->drainage;
            $onboarding->boiler_age = $materialInfo->boiler_age_years;
            $onboarding->last_boiler_service = $materialInfo->boiler_last_serviced;
            $onboarding->epc_rating = $materialInfo->epc_rating;
            $onboarding->known_issues = $materialInfo->known_issues;
            $onboarding->alterations = $materialInfo->planning_alterations;
        }

        return view('seller.onboarding', compact('propertyId', 'onboarding'));
    }

    /**
     * Store the seller onboarding data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeOnboarding(Request $request, $id)
    {
        $validated = $request->validate([
            'seller1_name' => ['nullable', 'string', 'max:255'],
            'seller1_email' => ['nullable', 'email', 'max:255'],
            'seller1_phone' => ['nullable', 'string', 'max:20'],
            'seller2_name' => ['nullable', 'string', 'max:255'],
            'seller2_email' => ['nullable', 'email', 'max:255'],
            'seller2_phone' => ['nullable', 'string', 'max:20'],
            'property_address' => ['required', 'string', 'max:500'],
            'property_type' => ['required', 'string', 'in:detached,semi-detached,terraced,flat-maisonette,bungalow,other'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'numeric', 'min:0'],
            'reception_rooms' => ['nullable', 'integer', 'min:0'],
            'outbuildings' => ['nullable', 'string', 'max:500'],
            'garden_details' => ['nullable', 'string', 'max:2000'],
            'parking' => ['nullable', 'string', 'in:none,on-street,driveway,garage,allocated,permit'],
            'tenure' => ['required', 'string', 'in:freehold,leasehold,share-of-freehold,unknown'],
            'lease_years' => ['nullable', 'integer', 'min:0'],
            'ground_rent' => ['nullable', 'string', 'max:100'],
            'service_charge' => ['nullable', 'string', 'max:100'],
            'managing_agent' => ['nullable', 'string', 'max:255'],
            'legal_owner' => ['nullable', 'string', 'in:yes,no'],
            'mortgaged' => ['nullable', 'string', 'in:yes,no'],
            'mortgage_lender' => ['nullable', 'string', 'max:255'],
            'notices_charges' => ['nullable', 'string', 'max:1000'],
            'gas_supply' => ['nullable', 'string', 'in:yes,no'],
            'electricity_supply' => ['nullable', 'string', 'in:yes,no'],
            'mains_water' => ['nullable', 'string', 'in:yes,no'],
            'drainage' => ['nullable', 'string', 'in:mains,septic-tank,private'],
            'boiler_age' => ['nullable', 'integer', 'min:0'],
            'last_boiler_service' => ['nullable', 'date'],
            'epc_rating' => ['nullable', 'string', 'in:A,B,C,D,E,F,G,awaiting'],
            'known_issues' => ['nullable', 'string', 'max:2000'],
            'alterations' => ['nullable', 'string', 'max:2000'],
            'certificates' => ['nullable', 'array'],
            'certificates.*' => ['file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'viewing_contact' => ['nullable', 'string', 'in:seller1,seller2,tenant,other'],
            'preferred_viewing_times' => ['nullable', 'string', 'max:500'],
            'access_notes' => ['nullable', 'string', 'max:1000'],
            'for_sale_board' => ['nullable', 'string', 'in:yes,no'],
            'photography_homecheck' => ['nullable', 'string', 'in:yes,no'],
            'publish_marketing' => ['nullable', 'string', 'in:yes,no'],
        ], [
            'property_address.required' => 'Property address is required.',
            'property_type.required' => 'Property type is required.',
            'bedrooms.required' => 'Number of bedrooms is required.',
            'bathrooms.required' => 'Number of bathrooms is required.',
            'tenure.required' => 'Tenure is required.',
            'seller1_email.email' => 'Seller 1 email must be a valid email address.',
            'seller2_email.email' => 'Second seller email must be a valid email address.',
        ]);

        $user = auth()->user();
        $property = \App\Models\Property::where('seller_id', $user->id)->findOrFail($id);

        try {
            \DB::beginTransaction();

            // Update seller 1 information in user account if changed
            $userUpdates = [];
            if (isset($validated['seller1_name']) && $validated['seller1_name'] !== $user->name) {
                $userUpdates['name'] = $validated['seller1_name'];
            }
            if (isset($validated['seller1_email']) && $validated['seller1_email'] !== $user->email) {
                $userUpdates['email'] = $validated['seller1_email'];
            }
            if (isset($validated['seller1_phone']) && $validated['seller1_phone'] !== $user->phone) {
                $userUpdates['phone'] = $validated['seller1_phone'];
            }
            if (!empty($userUpdates)) {
                $user->update($userUpdates);
            }

            // Update property with onboarding data including second seller information
            $property->update([
                'address' => $validated['property_address'],
                'property_type' => $validated['property_type'],
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'reception_rooms' => $validated['reception_rooms'] ?? null,
                'outbuildings' => $validated['outbuildings'] ?? null,
                'garden_details' => $validated['garden_details'] ?? null,
                'parking' => $validated['parking'] ?? null,
                'tenure' => $validated['tenure'],
                'lease_years_remaining' => $validated['lease_years'] ?? null,
                'ground_rent' => $validated['ground_rent'] ?? null,
                'service_charge' => $validated['service_charge'] ?? null,
                'managing_agent' => $validated['managing_agent'] ?? null,
                'seller2_name' => $validated['seller2_name'] ?? null,
                'seller2_email' => $validated['seller2_email'] ?? null,
                'seller2_phone' => $validated['seller2_phone'] ?? null,
                'status' => 'property_details_captured',
            ]);

            // Save material information
            \App\Models\PropertyMaterialInformation::updateOrCreate(
                ['property_id' => $property->id],
                [
                    'heating_type' => null,
                    'boiler_age_years' => $validated['boiler_age'] ?? null,
                    'boiler_last_serviced' => $validated['last_boiler_service'] ?? null,
                    'epc_rating' => $validated['epc_rating'] ?? null,
                    'gas_supply' => isset($validated['gas_supply']) && $validated['gas_supply'] === 'yes',
                    'electricity_supply' => isset($validated['electricity_supply']) && $validated['electricity_supply'] === 'yes',
                    'mains_water' => isset($validated['mains_water']) && $validated['mains_water'] === 'yes',
                    'drainage' => $validated['drainage'] ?? null,
                    'known_issues' => $validated['known_issues'] ?? null,
                    'planning_alterations' => $validated['alterations'] ?? null,
                ]
            );

            // Handle certificate uploads if provided
            if ($request->hasFile('certificates')) {
                foreach ($request->file('certificates') as $certificate) {
                    // Determine storage disk (S3 if configured, otherwise public)
                    $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
                    $certPath = $certificate->store('properties/' . $property->id . '/certificates', $disk);
                    \App\Models\PropertyDocument::create([
                        'property_id' => $property->id,
                        'document_type' => 'certificate',
                        'file_path' => $certPath,
                        'uploaded_at' => now(),
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('seller.properties.show', $property->id)
                ->with('success', 'Onboarding information saved successfully! You can now proceed with instruction signing.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Onboarding save error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving your information. Please try again.');
        }
    }

    /**
     * Show the instruct Abodeology page.
     *
     * @param  int|null  $propertyId
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function instruct($propertyId = null)
    {
        $user = auth()->user();
        
        $property = null;
        $instruction = null;

        if ($propertyId) {
            $property = \App\Models\Property::where('seller_id', $user->id)
                ->where('id', $propertyId)
                ->firstOrFail();
            
            $instruction = \App\Models\PropertyInstruction::where('property_id', $property->id)->first();

            // If instruction doesn't exist but property exists, create a pending instruction
            // This handles the case when seller clicks from post-valuation email
            if (!$instruction && $property) {
                $instruction = \App\Models\PropertyInstruction::create([
                    'property_id' => $property->id,
                    'seller_id' => $user->id,
                    'status' => 'pending',
                    'requested_at' => now(), // Mark as requested when seller clicks the link
                    'fee_percentage' => 1.5,
                ]);
            }
        }

        // If no property-specific instruction exists, create default data
        if (!$instruction) {
            $instruction = (object) [
                'property_address' => $property ? $property->address : null,
                'seller_names' => $user->name,
                'fee_percentage' => '1.5',
            ];
        } else {
            // Pre-fill with existing instruction data
            $instruction->property_address = $property->address ?? null;
            $instruction->seller_names = $user->name;
        }

        return view('seller.instruct', compact('instruction', 'property'));
    }

    /**
     * Store the instruction agreement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $propertyId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeInstruct(Request $request, $propertyId = null)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
            'declaration_accurate' => ['required', 'accepted'],
            'declaration_legal_entitlement' => ['required', 'accepted'],
            'declaration_immediate_marketing' => ['nullable', 'accepted'],
            'declaration_terms' => ['required', 'accepted'],
            'declaration_homecheck' => ['nullable', 'accepted'],
            'seller1_name' => ['required', 'string', 'max:255'],
            'seller1_signature' => ['required', 'string', 'max:255'],
            'seller1_date' => ['required', 'date'],
            'seller2_name' => ['nullable', 'string', 'max:255'],
            'seller2_signature' => ['nullable', 'string', 'max:255'],
            'seller2_date' => ['nullable', 'date', 'required_with:seller2_name'],
        ], [
            'declaration_accurate.required' => 'You must confirm that all information provided is accurate.',
            'declaration_legal_entitlement.required' => 'You must confirm you are legally entitled to instruct the sale.',
            'declaration_terms.required' => 'You must accept the Estate Agency Terms & Conditions of Business.',
            'seller1_signature.required' => 'Digital signature is required for Seller 1.',
            'seller1_date.required' => 'Date is required for Seller 1.',
            'seller2_date.required_with' => 'Date is required when Seller 2 name is provided.',
        ]);

        // Get property ID from route or request
        $propertyId = $propertyId ?? $validated['property_id'] ?? null;

        if (!$propertyId) {
            return back()
                ->withInput()
                ->with('error', 'Property ID is required.');
        }

        // Get property and verify ownership
        $property = \App\Models\Property::where('seller_id', $user->id)
            ->where('id', $propertyId)
            ->firstOrFail();

        try {
            \DB::beginTransaction();

            // Calculate fee based on viewing hosting preference
            $selfHostViewings = $request->input('self_host_viewings') == '1';
            $standardFee = 1.5;
            $reducedFee = 1.25; // 0.25% reduction for self-hosted viewings
            $finalFee = $selfHostViewings ? $reducedFee : $standardFee;

            // Create or update instruction
            $instruction = \App\Models\PropertyInstruction::updateOrCreate(
                ['property_id' => $property->id],
                [
                    'seller_id' => $user->id,
                    'fee_percentage' => $finalFee,
                    'self_host_viewings' => $selfHostViewings,
                    'declaration_accurate' => true,
                    'declaration_legal_entitlement' => true,
                    'declaration_immediate_marketing' => $validated['declaration_immediate_marketing'] ?? false,
                    'declaration_terms' => true,
                    'declaration_homecheck' => $validated['declaration_homecheck'] ?? false,
                    'seller1_name' => $validated['seller1_name'],
                    'seller1_signature' => $validated['seller1_signature'],
                    'seller1_date' => $validated['seller1_date'],
                    'seller2_name' => $validated['seller2_name'] ?? null,
                    'seller2_signature' => $validated['seller2_signature'] ?? null,
                    'seller2_date' => $validated['seller2_date'] ?? null,
                    'status' => 'signed',
                    'signed_at' => now(),
                ]
            );

            // Update property status to "awaiting_aml" (seller must upload AML documents)
            $property->update([
                'status' => 'awaiting_aml',
            ]);

            // Create AML check record if it doesn't exist (for document upload)
            \App\Models\AmlCheck::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'verification_status' => 'pending',
                ]
            );

            \DB::commit();

            // Trigger Keap automation for seller onboarding completion (instruction signed)
            try {
                $keapService = new \App\Services\KeapService();
                $keapService->triggerSellerOnboarded($user, [
                    'property_id' => $property->id,
                    'property_address' => $property->address ?? '',
                    'instruction_signed_at' => $instruction->signed_at->toIso8601String(),
                    'custom_fields' => [
                        'property_id' => $property->id,
                        'instruction_status' => $instruction->status,
                        'instruction_signed_date' => $instruction->signed_at->format('Y-m-d'),
                    ],
                ]);
            } catch (\Exception $e) {
                \Log::error('Keap trigger error for seller onboarding: ' . $e->getMessage());
            }

            // Send Welcome Pack email
            try {
                \Mail::to($user->email)->send(
                    new \App\Mail\WelcomePack($user, $property, $instruction)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send Welcome Pack email: ' . $e->getMessage());
            }

            return redirect()->route('seller.properties.show', $property->id)
                ->with('success', 'Congratulations! Your instruction has been signed successfully. A Welcome Pack has been sent to your email address. Please provide your AML documents (ID + Proof of Address) and solicitor details to proceed.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Instruction signing error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while processing your instruction. Please try again.');
        }
    }

    /**
     * Show the offer decision page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showOfferDecision($id)
    {
        $user = auth()->user();
        
        $offer = \App\Models\Offer::with(['buyer', 'property'])->findOrFail($id);

        // Check if user owns the property
        if ($offer->property->seller_id !== $user->id && !in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You do not have permission to view this offer.');
        }

        // Only show pending offers for decision
        if ($offer->status !== 'pending') {
            return redirect()->route('seller.properties.show', $offer->property_id)
                ->with('info', 'This offer has already been responded to.');
        }

        return view('seller.offer-decision', compact('offer'));
    }

    /**
     * Handle the offer decision (accept, decline, or counter).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleOfferDecision(Request $request, $id)
    {
        $user = auth()->user();
        
        $offer = \App\Models\Offer::with(['buyer', 'property'])->findOrFail($id);

        // Check if user owns the property
        if ($offer->property->seller_id !== $user->id && !in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You do not have permission to respond to this offer.');
        }

        // Only allow responding to pending offers
        if ($offer->status !== 'pending') {
            return back()->with('error', 'This offer has already been responded to.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:accepted,declined,counter'],
            'counter_amount' => ['nullable', 'required_if:decision,counter', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'decision.required' => 'Please select a decision.',
            'decision.in' => 'Invalid decision selected.',
            'counter_amount.required_if' => 'Please enter a counter-offer amount.',
            'counter_amount.numeric' => 'Counter-offer amount must be a valid number.',
            'counter_amount.min' => 'Counter-offer amount must be greater than zero.',
        ]);

        try {
            // Use OfferDecisionService for consistent logic
            $offerDecisionService = new \App\Services\OfferDecisionService();
            
            $result = $offerDecisionService->processDecision(
                $offer,
                $validated['decision'],
                $user->id,
                [
                    'notes' => $validated['notes'] ?? null,
                    'counter_amount' => ($validated['decision'] === 'counter' && isset($validated['counter_amount'])) 
                        ? $validated['counter_amount'] 
                        : null,
                ]
            );

            if (!$result['success']) {
                return back()
                    ->withInput()
                    ->with('error', $result['message']);
            }

            $offerDecision = $result['decision'];

            $decisionMessages = [
                'accepted' => 'You have accepted the offer! A Memorandum of Sale has been generated and sent to both solicitors.',
                'declined' => 'You have declined the offer. The buyer has been notified.',
                'counter' => 'You have sent a counter-offer of £' . number_format($validated['counter_amount'] ?? 0, 2) . '. The buyer has been notified.',
            ];

            return redirect()->route('seller.offer.decision.success', $offer->id)
                ->with('success', $decisionMessages[$validated['decision']] ?? 'Your decision has been recorded.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Offer decision error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while processing your decision. Please try again.');
        }
    }

    /**
     * Show offer decision success page.
     *
     * @param  int  $id  Offer ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showOfferDecisionSuccess($id)
    {
        $user = auth()->user();
        
        $offer = \App\Models\Offer::with(['buyer', 'property', 'latestDecision'])->findOrFail($id);

        // Check if user owns the property
        if ($offer->property->seller_id !== $user->id && !in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You do not have permission to view this page.');
        }

        return view('seller.offer-decision-success', compact('offer'));
    }

    /**
     * Handle "Discuss with Agent" request for an offer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function discussWithAgent(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'offer_id' => ['required', 'exists:offers,id'],
            'contact_method' => ['nullable', 'string', 'in:call,video'],
            'urgency' => ['nullable', 'string', 'in:normal,urgent,asap'],
            'discussion_points' => ['nullable', 'string', 'max:1000'],
        ]);

        $offer = \App\Models\Offer::with(['buyer', 'property'])->findOrFail($validated['offer_id']);

        // Check if user owns the property
        if ($offer->property->seller_id !== $user->id && !in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You do not have permission to perform this action.');
        }

        try {
            // Notify all agents/admins about the discussion request
            $agents = \App\Models\User::whereIn('role', ['admin', 'agent'])->get();
            
            foreach ($agents as $agent) {
                try {
                    \Mail::to($agent->email)->send(
                        new \App\Mail\OfferDiscussionRequest($offer, $offer->property, $user, $validated)
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send discussion request to agent ' . $agent->email . ': ' . $e->getMessage());
                }
            }

            \Log::info('Offer discussion request created', [
                'offer_id' => $offer->id,
                'property_id' => $offer->property_id,
                'seller_id' => $user->id,
                'urgency' => $validated['urgency'] ?? 'normal',
            ]);

            return redirect()->route('seller.dashboard')
                ->with('success', 'Your request to discuss this offer with an agent has been sent. An agent will contact you soon.');

        } catch (\Exception $e) {
            \Log::error('Offer discussion request error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while sending your request. Please try again.');
        }
    }

    /**
     * Show HomeCheck report for vendor.
     *
     * @param  int  $propertyId
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showHomecheckReport($propertyId)
    {
        $user = auth()->user();
        
        // Verify seller owns this property
        $property = \App\Models\Property::where('seller_id', $user->id)
            ->with(['instruction'])
            ->findOrFail($propertyId);
        
        // Check if instruction is signed - HomeCheck report available immediately upon instruction signing
        $instruction = $property->instruction;
        if (!$instruction || $instruction->status !== 'signed') {
            return redirect()->route('seller.dashboard')
                ->with('error', 'HomeCheck report is available after you sign the Terms & Conditions.');
        }
        
        // Get HomeCheck report (prefer completed, but allow in-progress if instruction is signed)
        $homecheckReport = \App\Models\HomecheckReport::where('property_id', $propertyId)
            ->with(['property', 'homecheckData'])
            ->orderByRaw("CASE WHEN status = 'completed' THEN 0 ELSE 1 END")
            ->orderBy('completed_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$homecheckReport) {
            return redirect()->route('seller.properties.show', $propertyId)
                ->with('info', 'Your HomeCheck report is being prepared. It will be available here once your property visit is completed.');
        }
        
        // Get all HomeCheck data grouped by room
        $homecheckData = \App\Models\HomecheckData::where('homecheck_report_id', $homecheckReport->id)
            ->orWhere('property_id', $propertyId)
            ->orderBy('room_name')
            ->orderBy('created_at')
            ->get();
        
        // Load property relationships
        $property->load(['seller']);
        
        return view('seller.homecheck-report', compact('property', 'homecheckReport', 'homecheckData'));
    }

    /**
     * Proxy endpoint to serve HomeCheck images with proper CORS headers for 360° viewer.
     *
     * @param  int  $id  HomecheckData ID
     * @return \Illuminate\Http\Response
     */
    public function getHomecheckImage($id)
    {
        $user = auth()->user();
        
        // Get the homecheck data
        $homecheckData = \App\Models\HomecheckData::with('property')->findOrFail($id);
        
        // Verify seller owns the property
        if ($homecheckData->property->seller_id !== $user->id) {
            abort(403, 'Unauthorized access to this image.');
        }
        
        // Determine storage disk
        $s3Configured = !empty(config('filesystems.disks.s3.key')) && 
                       !empty(config('filesystems.disks.s3.secret')) && 
                       !empty(config('filesystems.disks.s3.bucket'));
        
        // Determine storage disk
        $disk = $s3Configured ? 's3' : 'public';
        $storage = \Illuminate\Support\Facades\Storage::disk($disk);
        
        if (!$storage->exists($homecheckData->image_path)) {
            abort(404, 'Image file not found.');
        }
        
        // Get file content and metadata
        $file = $storage->get($homecheckData->image_path);
        $mimeType = $storage->mimeType($homecheckData->image_path);
        $lastModified = $storage->lastModified($homecheckData->image_path);
        $fileSize = strlen($file);
        
        // Generate ETag based on file path and last modified time
        $etag = md5($homecheckData->image_path . $lastModified . $fileSize);
        
        // Check if client has a cached version (304 Not Modified)
        $request = request();
        $ifNoneMatch = $request->header('If-None-Match');
        $ifModifiedSince = $request->header('If-Modified-Since');
        
        if ($ifNoneMatch && $ifNoneMatch === '"' . $etag . '"') {
            return response('', 304)
                ->header('ETag', '"' . $etag . '"')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Access-Control-Allow-Origin', '*');
        }
        
        $lastModifiedDate = \Carbon\Carbon::createFromTimestamp($lastModified);
        if ($ifModifiedSince && $lastModifiedDate->lte(\Carbon\Carbon::parse($ifModifiedSince))) {
            return response('', 304)
                ->header('Last-Modified', $lastModifiedDate->toRfc7231String())
                ->header('ETag', '"' . $etag . '"')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Access-Control-Allow-Origin', '*');
        }
        
        // Cache duration: 1 year for 360° images (immutable), 1 month for regular images
        $maxAge = $homecheckData->is_360 ? 31536000 : 2592000; // 1 year for 360°, 1 month for regular
        $cacheControl = $homecheckData->is_360 
            ? 'public, max-age=31536000, immutable' 
            : 'public, max-age=2592000';
        
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', $fileSize)
            ->header('ETag', '"' . $etag . '"')
            ->header('Last-Modified', $lastModifiedDate->toRfc7231String())
            ->header('Cache-Control', $cacheControl)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type')
            ->header('Expires', now()->addSeconds($maxAge)->toRfc7231String());
        
        abort(404, 'Image not found.');
    }

    /**
     * Show the room upload page for HomeCheck.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showRoomUpload($id)
    {
        $user = auth()->user();
        
        // Verify the property belongs to the seller
        $property = \App\Models\Property::where('seller_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $propertyId = $id;

        // Get existing homecheck data for this property
        $existingRooms = \App\Models\HomecheckData::where('property_id', $property->id)
            ->orderBy('room_name')
            ->orderBy('created_at')
            ->get()
            ->groupBy('room_name');

        return view('seller.room-upload', compact('propertyId', 'existingRooms'));
    }

    /**
     * Store the HomeCheck room uploads.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRoomUpload(Request $request, $id)
    {
        $user = auth()->user();
        
        // Verify the property belongs to the seller
        $property = \App\Models\Property::where('seller_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.name' => ['required', 'string', 'max:255'],
            'rooms.*.images' => ['required', 'array', 'min:1'],
            'rooms.*.images.*' => ['required', 'image', 'mimes:jpeg,png,jpg'],
            'rooms.*.moisture_reading' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'rooms.required' => 'Please add at least one room.',
            'rooms.min' => 'Please add at least one room.',
            'rooms.*.name.required' => 'Room name is required for all rooms.',
            'rooms.*.images.required' => 'Please upload at least one image for each room.',
            'rooms.*.images.min' => 'Please upload at least one image for each room.',
            'rooms.*.images.*.image' => 'All files must be images.',
            'rooms.*.moisture_reading.numeric' => 'Moisture reading must be a number.',
            'rooms.*.moisture_reading.min' => 'Moisture reading must be between 0 and 100.',
            'rooms.*.moisture_reading.max' => 'Moisture reading must be between 0 and 100.',
        ]);

        try {
            \DB::beginTransaction();

            // Get or create a HomeCheck report for this property
            $homecheckReport = \App\Models\HomecheckReport::where('property_id', $property->id)
                ->whereIn('status', ['scheduled', 'in_progress', 'pending'])
                ->first();

            if (!$homecheckReport) {
                // Create a new HomeCheck report for seller uploads with 'pending' status
                $homecheckReport = \App\Models\HomecheckReport::create([
                    'property_id' => $property->id,
                    'status' => 'pending',
                    'scheduled_by' => $user->id,
                    'scheduled_date' => now(),
                ]);
            } else {
                // Update status to in_progress if it was scheduled or pending
                if ($homecheckReport->status === 'scheduled' || $homecheckReport->status === 'pending') {
                    $homecheckReport->update([
                        'status' => 'in_progress',
                    ]);
                }
            }

            // Determine storage disk (S3 if configured, otherwise public)
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

            // Process each room
            foreach ($validated['rooms'] as $roomIndex => $roomData) {
                $roomName = trim($roomData['name']);
                $is360 = false; // Sellers can't mark as 360 for now, can be added later
                $moistureReading = isset($roomData['moisture_reading']) && $roomData['moisture_reading'] !== '' 
                    ? (float) $roomData['moisture_reading'] 
                    : null;
                
                // Sanitize room name for file path (remove special characters)
                $sanitizedRoomName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $roomName);
                
                // Process each image with optimization
                $imageOptimizer = new \App\Services\ImageOptimizationService();
                foreach ($roomData['images'] as $imageIndex => $image) {
                    // Store image in property-specific folder
                    $imagePath = $image->store('homechecks/' . $property->id . '/rooms/' . $sanitizedRoomName . '/photos', $disk);
                    
                    // Optimize the image (max width 1920px, quality 85%)
                    try {
                        $imageOptimizer->optimizeExisting($imagePath, $disk, 1920, 85);
                    } catch (\Exception $e) {
                        \Log::warning('Image optimization failed for seller homecheck image: ' . $e->getMessage());
                        // Continue even if optimization fails
                    }
                    
                    // Get image metadata
                    $imageSize = $image->getSize();
                    $imageMimeType = $image->getMimeType();
                    
                    // Create homecheck data record with moisture reading and metadata
                    \App\Models\HomecheckData::create([
                        'property_id' => $property->id,
                        'homecheck_report_id' => $homecheckReport->id,
                        'room_name' => $roomName, // Store original room name in database
                        'image_path' => $imagePath,
                        'is_360' => $is360,
                        'moisture_reading' => $moistureReading, // Save moisture reading per room
                        'created_at' => now(),
                    ]);
                }
            }

            // Mark HomeCheck as completed
            $homecheckReport->update([
                'status' => 'completed',
                'completed_by' => $user->id,
                'completed_at' => now(),
            ]);

            \DB::commit();

        return redirect()->route('seller.homecheck.upload', $id)->with('success', 'HomeCheck submitted successfully! Your room images have been uploaded.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('HomeCheck upload error: ' . $e->getMessage());
            \Log::error('HomeCheck upload error trace: ' . $e->getTraceAsString());
            \Log::error('HomeCheck upload error file: ' . $e->getFile() . ':' . $e->getLine());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while uploading your images: ' . $e->getMessage() . '. Please try again.');
        }
    }

    /**
     * Show AML document upload form.
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAmlUpload($id)
    {
        $user = auth()->user();
        $property = \App\Models\Property::where('seller_id', $user->id)->findOrFail($id);

        // Only allow AML upload for properties awaiting AML
        if (!in_array($property->status, ['awaiting_aml', 'signed'])) {
            return redirect()->route('seller.properties.show', $property->id)
                ->with('error', 'You can only upload AML documents after signing the instruction.');
        }

        $amlCheck = \App\Models\AmlCheck::where('user_id', $user->id)->first();

        return view('seller.aml-upload', compact('property', 'amlCheck'));
    }

    /**
     * Store AML documents.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAmlUpload(Request $request, $id)
    {
        $user = auth()->user();
        $property = \App\Models\Property::where('seller_id', $user->id)->findOrFail($id);

        // Only allow AML upload for properties awaiting AML
        if (!in_array($property->status, ['awaiting_aml', 'signed'])) {
            return redirect()->route('seller.properties.show', $property->id)
                ->with('error', 'You can only upload AML documents after signing the instruction.');
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
            'id_documents.*.file' => 'Each ID document must be a valid file.',
            'id_documents.*.mimes' => 'ID documents must be JPEG, PNG, JPG, or PDF files.',
            'id_documents.*.max' => 'Each ID document must not be larger than 5MB.',
            'proof_of_address_documents.required' => 'Please upload at least one Proof of Address document (Utility bill, Bank statement, or Council tax bill).',
            'proof_of_address_documents.min' => 'Please upload at least one Proof of Address document.',
            'proof_of_address_documents.*.file' => 'Each Proof of Address must be a valid file.',
            'proof_of_address_documents.*.mimes' => 'Proof of Address documents must be JPEG, PNG, JPG, or PDF files.',
            'proof_of_address_documents.*.max' => 'Each Proof of Address must not be larger than 5MB.',
            'additional_documents.*.file' => 'Each additional document must be a valid file.',
            'additional_documents.*.mimes' => 'Additional documents must be JPEG, PNG, JPG, or PDF files.',
            'additional_documents.*.max' => 'Each additional document must not be larger than 5MB.',
        ]);

        try {
            \DB::beginTransaction();

            // Determine storage disk (S3 if configured, otherwise public)
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

            // Update or create AML check
            $amlCheck = \App\Models\AmlCheck::firstOrCreate(
                ['user_id' => $user->id],
                ['verification_status' => 'pending']
            );

            // Store ID documents
            if ($request->hasFile('id_documents')) {
                foreach ($request->file('id_documents') as $file) {
                    $filePath = $file->store('aml-documents/' . $user->id . '/id-documents', $disk);
                    \App\Models\AmlDocument::create([
                        'aml_check_id' => $amlCheck->id,
                        'document_type' => 'id_document',
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Store proof of address documents
            if ($request->hasFile('proof_of_address_documents')) {
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
            }

            // Store additional documents
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

            // Update legacy fields for backward compatibility (store first document paths)
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

            // Update property status from "awaiting_aml" to "signed" after AML documents are uploaded
            if ($property->status === 'awaiting_aml') {
                $property->update([
                    'status' => 'signed',
                ]);
            }

            return redirect()->route('seller.properties.show', $property->id)
                ->with('success', 'AML documents uploaded successfully! Your documents are being reviewed and you will be notified once verification is complete.');

        } catch (\Exception $e) {
            \Log::error('AML document upload error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while uploading your documents. Please try again.');
        }
    }

    /**
     * Show solicitor details form.
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showSolicitorDetails($id)
    {
        $user = auth()->user();
        $property = \App\Models\Property::where('seller_id', $user->id)->findOrFail($id);

        // Only allow solicitor details for signed properties
        if ($property->status !== 'signed') {
            return redirect()->route('seller.properties.show', $property->id)
                ->with('error', 'You can only provide solicitor details after signing the instruction.');
        }

        return view('seller.solicitor-details', compact('property'));
    }

    /**
     * Store solicitor details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSolicitorDetails(Request $request, $id)
    {
        $user = auth()->user();
        $property = \App\Models\Property::where('seller_id', $user->id)->findOrFail($id);

        // Only allow solicitor details for signed properties
        if ($property->status !== 'signed') {
            return redirect()->route('seller.properties.show', $property->id)
                ->with('error', 'You can only provide solicitor details after signing the instruction.');
        }

        $validated = $request->validate([
            'solicitor_name' => ['required', 'string', 'max:255'],
            'solicitor_firm' => ['required', 'string', 'max:255'],
            'solicitor_email' => ['required', 'email', 'max:255'],
            'solicitor_phone' => ['required', 'string', 'max:20'],
        ], [
            'solicitor_name.required' => 'Please provide the solicitor\'s name.',
            'solicitor_firm.required' => 'Please provide the solicitor\'s firm name.',
            'solicitor_email.required' => 'Please provide the solicitor\'s email address.',
            'solicitor_email.email' => 'Please provide a valid email address.',
            'solicitor_phone.required' => 'Please provide the solicitor\'s phone number.',
        ]);

        try {
            $property->update([
                'solicitor_name' => $validated['solicitor_name'],
                'solicitor_firm' => $validated['solicitor_firm'],
                'solicitor_email' => $validated['solicitor_email'],
                'solicitor_phone' => $validated['solicitor_phone'],
                'solicitor_details_completed' => true,
            ]);

            return redirect()->route('seller.properties.show', $property->id)
                ->with('success', 'Solicitor details saved successfully!');

        } catch (\Exception $e) {
            \Log::error('Solicitor details save error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving your solicitor details. Please try again.');
        }
    }

    /**
     * Check if buyer has completed required information for Memorandum of Sale.
     *
     * @param  \App\Models\User  $buyer
     * @return bool
     */
    private function checkBuyerInfoComplete($buyer)
    {
        // Check if buyer has provided solicitor details
        // Note: In the current implementation, buyer solicitor details are stored in buyer profile
        // For now, we'll check if the buyer has a solicitor email in the sales progression or offer
        // This is a simplified check - in a full implementation, you might check a buyer_profiles table
        // For the workflow, we'll assume buyer info is complete if they have basic contact info
        // This can be enhanced when buyer profiles are properly stored
        return true; // Simplified for now - can be enhanced with proper buyer profile checks
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
     * Show notifications page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function notifications()
    {
        $user = auth()->user();
        
        // Role check
        if (!in_array($user->role, ['seller', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        // Get seller properties with offers
        $properties = \App\Models\Property::where('seller_id', $user->id)
            ->with(['offers.latestDecision'])
            ->get();

        // Build notifications array
        $notifications = [];
        
        // Add notifications for new offers
        foreach ($properties as $property) {
            foreach ($property->offers as $offer) {
                if ($offer->created_at->isAfter(now()->subDays(30))) {
                    $notifications[] = [
                        'type' => 'info',
                        'icon' => 'ℹ',
                        'message' => "New offer of £" . number_format($offer->offer_amount, 2) . " received on " . $property->address,
                        'date' => $offer->created_at,
                        'link' => route('seller.properties.show', $property->id),
                    ];
                }
            }
        }

        // Sort notifications by date (newest first)
        usort($notifications, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('seller.notifications', compact('notifications'));
    }
}
