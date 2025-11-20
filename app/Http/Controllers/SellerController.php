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
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Status map
        $statusMap = [
            'draft' => 'Draft',
            'property_details_completed' => 'Property Details Completed',
            'pre_marketing' => 'Pre-Marketing',
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
        
        // If we have properties, fetch related data for all of them
        $upcomingViewings = collect();
        $offers = collect();
        
        if ($properties->count() > 0) {
            $propertyIds = $properties->pluck('id');
            
            // Fetch upcoming viewings for all seller's properties
            $upcomingViewings = \App\Models\Viewing::whereIn('property_id', $propertyIds)
                ->where('viewing_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->with(['buyer', 'property'])
                ->orderBy('viewing_date', 'asc')
                ->get();
            
            // Fetch pending offers for all seller's properties
            $offers = \App\Models\Offer::whereIn('property_id', $propertyIds)
                ->whereIn('status', ['pending', 'countered'])
                ->with(['buyer', 'property'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Load material information and homecheck data for the primary property if it exists
            if ($property) {
                $property->load(['materialInformation', 'homecheckData']);
            }
        }

        return view('seller.dashboard', compact(
            'properties',
            'property',
            'upcomingViewings',
            'offers'
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
            'property_details_completed' => 'Property Details Completed',
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
    public function createProperty()
    {
        return view('seller.properties.create');
    }

    /**
     * Store a newly created property.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:1000'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'property_type' => ['nullable', 'string', 'in:detached,semi,terraced,flat,maisonette,bungalow,other'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'parking' => ['nullable', 'string', 'in:none,on_street,driveway,garage,allocated,permit'],
            'tenure' => ['nullable', 'string', 'in:freehold,leasehold,share_freehold,unknown'],
            'lease_years_remaining' => ['nullable', 'integer', 'min:0'],
            'ground_rent' => ['nullable', 'numeric', 'min:0'],
            'service_charge' => ['nullable', 'numeric', 'min:0'],
            'managing_agent' => ['nullable', 'string', 'max:255'],
            'asking_price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'address.required' => 'Property address is required.',
        ]);

        $user = auth()->user();
        
        $property = \App\Models\Property::create([
            'seller_id' => $user->id,
            'address' => $validated['address'],
            'postcode' => $validated['postcode'] ?? null,
            'property_type' => $validated['property_type'] ?? null,
            'bedrooms' => $validated['bedrooms'] ?? null,
            'bathrooms' => $validated['bathrooms'] ?? null,
            'parking' => $validated['parking'] ?? null,
            'tenure' => $validated['tenure'] ?? null,
            'lease_years_remaining' => $validated['lease_years_remaining'] ?? null,
            'ground_rent' => $validated['ground_rent'] ?? null,
            'service_charge' => $validated['service_charge'] ?? null,
            'managing_agent' => $validated['managing_agent'] ?? null,
            'asking_price' => $validated['asking_price'] ?? null,
            'status' => 'draft',
        ]);

        return redirect()->route('seller.dashboard')
            ->with('success', 'Property created successfully! You can now proceed with onboarding.');
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
            ->with(['seller', 'offers.buyer', 'viewings.buyer', 'materialInformation', 'instruction'])
            ->firstOrFail();
        
        $statusMap = [
            'draft' => 'Draft',
            'property_details_completed' => 'Property Details Completed',
            'pre_marketing' => 'Pre-Marketing',
            'signed' => 'Signed',
            'live' => 'Live',
            'sstc' => 'Sold Subject to Contract',
            'withdrawn' => 'Withdrawn',
            'sold' => 'Sold',
        ];
        
        $property->status_text = $statusMap[$property->status] ?? ucfirst($property->status);
        
        return view('seller.properties.show', compact('property'));
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
        $propertyId = $id;
        $onboarding = (object) [
            'property_address' => null,
            'property_type' => null,
            'bedrooms' => null,
            'bathrooms' => null,
            'parking' => null,
            'tenure' => null,
            'lease_years' => null,
            'ground_rent' => null,
            'service_charge' => null,
            'managing_agent' => null,
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
            'property_address' => ['required', 'string', 'max:500'],
            'property_type' => ['required', 'string', 'in:detached,semi-detached,terraced,flat-maisonette,bungalow,other'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'numeric', 'min:0'],
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
            'epc_rating' => ['nullable', 'string', 'in:A,B,C,D,E,F,G'],
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
        ]);

        // In a real application, you would save this to database
        // Example:
        // $onboarding = SellerOnboarding::updateOrCreate(
        //     ['property_id' => $id, 'user_id' => auth()->id()],
        //     $validated
        // );
        //
        // Handle certificate uploads
        // if ($request->hasFile('certificates')) {
        //     foreach ($request->file('certificates') as $certificate) {
        //         $certPath = $certificate->store('onboarding/certificates/' . $onboarding->id, 'public');
        //         Certificate::create([
        //             'onboarding_id' => $onboarding->id,
        //             'file_path' => $certPath,
        //             'file_name' => $certificate->getClientOriginalName(),
        //         ]);
        //     }
        // }

        return redirect()->route('seller.onboarding', $id)->with('success', 'Onboarding information saved successfully. Please continue to the next step.');
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

            // Create or update instruction
            $instruction = \App\Models\PropertyInstruction::updateOrCreate(
                ['property_id' => $property->id],
                [
                    'seller_id' => $user->id,
                    'fee_percentage' => $request->input('fee_percentage', 1.5),
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

            // Update property status to "signed"
            $property->update([
                'status' => 'signed',
            ]);

            \DB::commit();

            // Send Welcome Pack email
            try {
                \Mail::to($user->email)->send(
                    new \App\Mail\WelcomePack($user, $property, $instruction)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send Welcome Pack email: ' . $e->getMessage());
            }

            return redirect()->route('seller.properties.show', $property->id)
                ->with('success', 'Congratulations! Your instruction has been signed successfully. A Welcome Pack has been sent to your email address.');

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
        // In a real application, you would fetch the offer from database
        // For now, we'll use sample data
        $offer = (object) [
            'id' => $id,
            'buyer_name' => 'John Doe',
            'amount' => 450000,
            'offer_date' => date('d M Y'),
            'buyer_position_text' => 'First-time buyer',
            'funding_type' => 'Mortgage',
            'deposit_amount' => 45000,
            'conditions' => 'Subject to survey and mortgage approval',
            'aml_badge_class' => 'good',
            'aml_status_text' => 'Verified',
            'pof_badge_class' => 'good',
            'pof_status_text' => 'Verified',
            'photo_id_status' => 'Verified',
            'address_proof_status' => 'Verified',
            'pof_documents_status' => 'Verified',
            'aip_status' => 'Provided',
            'solicitor_name' => 'Jane Smith',
            'solicitor_firm' => 'Smith & Partners Solicitors',
            'solicitor_email' => 'jane.smith@smithpartners.co.uk',
            'solicitor_phone' => '020 1234 5678',
        ];

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
        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:accepted,declined,counter'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // In a real application, you would update the offer in database
        // Example:
        // $offer = Offer::findOrFail($id);
        // $offer->update([
        //     'decision' => $validated['decision'],
        //     'seller_notes' => $validated['notes'] ?? null,
        //     'decision_date' => now(),
        //     'status' => $validated['decision'] === 'accepted' ? 'accepted' : ($validated['decision'] === 'declined' ? 'declined' : 'counter_offered'),
        // ]);
        //
        // Send notification to buyer
        // Notification::send($offer->buyer, new OfferDecisionNotification($offer));

        $decisionMessages = [
            'accepted' => 'You have accepted the offer. The buyer has been notified.',
            'declined' => 'You have declined the offer. The buyer has been notified.',
            'counter' => 'You have requested a counter-offer discussion. The buyer has been notified.',
        ];

        return redirect()->route('seller.offer.decision', $id)->with('success', $decisionMessages[$validated['decision']] ?? 'Your decision has been recorded.');
    }

    /**
     * Show the room upload page for HomeCheck.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showRoomUpload($id)
    {
        // In a real application, you would fetch the property from database
        // For now, we'll just pass the property ID
        $propertyId = $id;

        return view('seller.room-upload', compact('propertyId'));
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
        $validated = $request->validate([
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.name' => ['required', 'string', 'max:255'],
            'rooms.*.images' => ['required', 'array', 'min:1'],
            'rooms.*.images.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'], // 10MB max
        ], [
            'rooms.required' => 'Please add at least one room.',
            'rooms.min' => 'Please add at least one room.',
            'rooms.*.name.required' => 'Room name is required for all rooms.',
            'rooms.*.images.required' => 'Please upload at least one image for each room.',
            'rooms.*.images.min' => 'Please upload at least one image for each room.',
            'rooms.*.images.*.image' => 'All files must be images.',
            'rooms.*.images.*.max' => 'Image size must not exceed 10MB.',
        ]);

        // In a real application, you would save this to database
        // Example:
        // $homeCheck = HomeCheck::create([
        //     'property_id' => $id,
        //     'user_id' => auth()->id(),
        //     'status' => 'submitted',
        //     'submitted_at' => now(),
        // ]);
        //
        // foreach ($validated['rooms'] as $index => $roomData) {
        //     $room = Room::create([
        //         'home_check_id' => $homeCheck->id,
        //         'name' => $roomData['name'],
        //         'order' => $index,
        //     ]);
        //
        //     foreach ($roomData['images'] as $image) {
        //         $imagePath = $image->store('homechecks/' . $homeCheck->id . '/rooms/' . $room->id, 'public');
        //         RoomImage::create([
        //             'room_id' => $room->id,
        //             'image_path' => $imagePath,
        //             'order' => $room->images()->count(),
        //         ]);
        //     }
        // }

        return redirect()->route('seller.homecheck.upload', $id)->with('success', 'HomeCheck submitted successfully! Your room images have been uploaded.');
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
            'agent' => 'admin.dashboard',
            'buyer' => 'buyer.dashboard',
            'seller' => 'seller.dashboard',
            'both' => 'buyer.dashboard',
            'pva' => 'pva.dashboard',
        ];

        return $dashboards[$role] ?? 'home';
    }
}
