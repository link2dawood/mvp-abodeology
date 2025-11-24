<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valuation;
use App\Models\Property;
use App\Models\PropertyMaterialInformation;
use App\Models\Offer;
use App\Models\User;

class AdminController extends Controller
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
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Role check as fallback (middleware should handle this, but extra protection)
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access the admin dashboard.');
        }
        
        // Fetch real data from database
        $valuations = Valuation::with('seller')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get today's scheduled valuations (appointments)
        $todaysAppointments = Valuation::with('seller')
            ->where('status', 'scheduled')
            ->whereDate('valuation_date', today())
            ->orderBy('valuation_time', 'asc')
            ->get();

        $pendingValuations = Valuation::where('status', 'pending')->count();
        $scheduledValuations = Valuation::where('status', 'scheduled')->count();
        $activeListings = Property::where('status', 'live')->count();
        $offersReceived = Offer::where('status', 'pending')->count();
        $salesInProgress = Property::where('status', 'sold')->count();
        $pvasActive = User::where('role', 'pva')->count();

        // Dashboard statistics
        $stats = [
            'total_valuations' => Valuation::count(),
            'pending_valuations' => $pendingValuations,
            'scheduled_valuations' => $scheduledValuations,
            'active_listings' => $activeListings,
            'offers_received' => $offersReceived,
            'sales_in_progress' => $salesInProgress,
            'pvas_active' => $pvasActive,
        ];

        // Get recent data
        $sellers = User::whereIn('role', ['seller', 'both'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $buyers = User::whereIn('role', ['buyer', 'both'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $offers = Offer::with(['buyer', 'property'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $sales = Property::where('status', 'sold')
            ->with('seller')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $pvas = User::where('role', 'pva')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Generate alerts based on pending items
        $alerts = [];
        if ($pendingValuations > 0) {
            $alerts[] = "You have {$pendingValuations} pending valuation request(s) that need attention.";
        }
        if ($offersReceived > 0) {
            $alerts[] = "You have {$offersReceived} pending offer(s) awaiting response.";
        }
        if (empty($alerts)) {
            $alerts[] = 'System running normally';
            $alerts[] = 'No pending maintenance tasks';
            $alerts[] = 'All services operational';
        }

        return view('admin.dashboard', compact('stats', 'valuations', 'todaysAppointments', 'sellers', 'buyers', 'offers', 'sales', 'pvas', 'alerts'));
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

    /**
     * List all valuations for agents/admins.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function valuations()
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $valuations = Valuation::with('seller')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.valuations.index', compact('valuations'));
    }

    /**
     * Show a specific valuation.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showValuation($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $valuation = Valuation::with('seller')->findOrFail($id);

        return view('admin.valuations.show', compact('valuation'));
    }

    /**
     * Show the Valuation Form (Onboarding Form) for completing seller onboarding during valuation.
     * This is called "Valuation Form" in the UI for agents, but "Onboarding Form" internally.
     *
     * @param  int  $id  Valuation ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showValuationForm($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $valuation = Valuation::with('seller')->findOrFail($id);

        // Pre-fill form with valuation and seller data (for on-site completion)
        $onboarding = (object) [
            // Seller information (pre-filled from valuation)
            'seller_name' => $valuation->seller->name ?? '',
            'seller_email' => $valuation->seller->email ?? '',
            'seller_phone' => $valuation->seller->phone ?? '',
            
            // Property information (pre-filled from valuation)
            'property_address' => $valuation->property_address,
            'postcode' => $valuation->postcode,
            'property_type' => $valuation->property_type,
            'bedrooms' => $valuation->bedrooms,
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

        // Check which route was used to determine which view to show
        $viewName = request()->route()->getName();
        $view = (str_contains($viewName, 'onboarding')) 
            ? 'admin.valuations.onboarding' 
            : 'admin.valuations.valuation-form';

        return view($view, compact('valuation', 'onboarding'));
    }

    /**
     * Store the Valuation Form (Onboarding Form) data completed by agent during valuation.
     * This saves the form directly to the seller's profile with status "property_details_captured".
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Valuation ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeValuationForm(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $valuation = Valuation::with('seller')->findOrFail($id);

        $validated = $request->validate([
            'property_address' => ['required', 'string', 'max:500'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'property_type' => ['required', 'string', 'in:detached,semi,terraced,flat,maisonette,bungalow,other'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'numeric', 'min:0'],
            'parking' => ['nullable', 'string', 'in:none,on_street,driveway,garage,allocated,permit'],
            'tenure' => ['required', 'string', 'in:freehold,leasehold,share_freehold,unknown'],
            'lease_years_remaining' => ['nullable', 'integer', 'min:0'],
            'ground_rent' => ['nullable', 'numeric', 'min:0'],
            'service_charge' => ['nullable', 'numeric', 'min:0'],
            'managing_agent' => ['nullable', 'string', 'max:255'],
            'asking_price' => ['nullable', 'numeric', 'min:0'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            
            // Material Information
            'heating_type' => ['nullable', 'string', 'in:gas,electric,oil,underfloor,other'],
            'boiler_age_years' => ['nullable', 'integer', 'min:0'],
            'boiler_last_serviced' => ['nullable', 'date'],
            'epc_rating' => ['nullable', 'string', 'in:A,B,C,D,E,F,G'],
            'gas_supply' => ['nullable'],
            'electricity_supply' => ['nullable'],
            'mains_water' => ['nullable'],
            'drainage' => ['nullable', 'string', 'in:mains,septic_tank,private_system'],
            'known_issues' => ['nullable', 'string', 'max:2000'],
            'planning_alterations' => ['nullable', 'string', 'max:2000'],
            
            // Access & Notes
            'access_notes' => ['nullable', 'string', 'max:1000'],
            'viewing_contact' => ['nullable', 'string', 'max:255'],
            'preferred_viewing_times' => ['nullable', 'string', 'max:500'],
            'agent_notes' => ['nullable', 'string', 'max:5000'],
            
            // ID Visual Check (HMRC/EA Act Requirement)
            'id_visual_check' => ['required', 'accepted'],
            'id_visual_check_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'property_address.required' => 'Property address is required.',
            'property_type.required' => 'Property type is required.',
            'bedrooms.required' => 'Number of bedrooms is required.',
            'bathrooms.required' => 'Number of bathrooms is required.',
            'tenure.required' => 'Tenure is required.',
            'id_visual_check.required' => 'You must confirm that you have visually checked the seller\'s ID document.',
            'id_visual_check.accepted' => 'You must confirm that you have visually checked the seller\'s ID document.',
        ]);

        try {
            \DB::beginTransaction();

            // Create or update property from valuation
            $property = Property::updateOrCreate(
                [
                    'seller_id' => $valuation->seller_id,
                    'address' => $validated['property_address'],
                ],
                [
                    'postcode' => $validated['postcode'] ?? $valuation->postcode,
                    'property_type' => $validated['property_type'],
                    'bedrooms' => $validated['bedrooms'],
                    'bathrooms' => $validated['bathrooms'],
                    'parking' => $validated['parking'] ?? null,
                    'tenure' => $validated['tenure'],
                    'lease_years_remaining' => $validated['lease_years_remaining'] ?? null,
                    'ground_rent' => $validated['ground_rent'] ?? null,
                    'service_charge' => $validated['service_charge'] ?? null,
                    'managing_agent' => $validated['managing_agent'] ?? null,
                    'asking_price' => $validated['asking_price'] ?? null,
                    'status' => 'property_details_captured', // Set status after Valuation Form completion
                ]
            );

            // Create or update material information
            PropertyMaterialInformation::updateOrCreate(
                ['property_id' => $property->id],
                [
                    'heating_type' => $validated['heating_type'] ?? null,
                    'boiler_age_years' => $validated['boiler_age_years'] ?? null,
                    'boiler_last_serviced' => $validated['boiler_last_serviced'] ?? null,
                    'epc_rating' => $validated['epc_rating'] ?? null,
                    'gas_supply' => isset($validated['gas_supply']) && $validated['gas_supply'],
                    'electricity_supply' => isset($validated['electricity_supply']) && $validated['electricity_supply'],
                    'mains_water' => isset($validated['mains_water']) && $validated['mains_water'],
                    'drainage' => $validated['drainage'] ?? null,
                    'known_issues' => $validated['known_issues'] ?? null,
                    'planning_alterations' => $validated['planning_alterations'] ?? null,
                ]
            );

            // Update valuation status and save agent notes + ID visual check
            $valuation->update([
                'estimated_value' => $validated['estimated_value'] ?? null,
                'status' => 'completed',
                'notes' => $validated['agent_notes'] ?? $validated['pricing_notes'] ?? $valuation->notes,
                'id_visual_check' => true,
                'id_visual_check_notes' => $validated['id_visual_check_notes'] ?? null,
            ]);

            \DB::commit();

            // Find the created/updated property (refresh to get updated instance)
            $property = Property::where('seller_id', $valuation->seller_id)
                ->where('address', $validated['property_address'])
                ->first();

            if (!$property) {
                return redirect()->route('admin.valuations.show', $valuation->id)
                    ->with('error', 'Property was created but could not be found. Please check the properties list.');
            }

            // Redirect directly to property page where agent can request instruction
            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'Valuation Form completed successfully! Property details have been captured and saved to the seller\'s profile. Status: Property Details Captured. You can now request instruction from the seller.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Valuation onboarding error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving the onboarding data. Please try again.');
        }
    }

    /**
     * List all properties (for agents/admins).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function properties()
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $properties = Property::with(['seller', 'instruction'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show property details (for agents).
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showProperty($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $property = Property::with(['seller', 'instruction', 'materialInformation', 'homecheckReports', 'photos', 'documents'])->findOrFail($id);

        return view('admin.properties.show', compact('property'));
    }

    /**
     * Request instruction from seller (agent action).
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestInstruction($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::with('seller')->findOrFail($id);

        // Check if instruction already exists
        $instruction = \App\Models\PropertyInstruction::where('property_id', $property->id)->first();

        if ($instruction && $instruction->status === 'signed') {
            return back()->with('error', 'This property already has a signed instruction.');
        }

        // Create or update instruction request
        $instruction = \App\Models\PropertyInstruction::updateOrCreate(
            ['property_id' => $property->id],
            [
                'seller_id' => $property->seller_id,
                'status' => 'pending',
                'requested_by' => $user->id,
                'requested_at' => now(),
                'fee_percentage' => 1.5, // Default fee
            ]
        );

        // Send notification email to seller with link to sign instruction
        try {
            \Mail::to($property->seller->email)->send(
                new \App\Mail\InstructionRequestNotification($property->seller, $property, $instruction)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send instruction request notification email: ' . $e->getMessage());
        }

        return redirect()->route('admin.properties.show', $property->id)
            ->with('success', 'Instruction request sent to seller. The seller will receive a notification to sign the Terms & Conditions.');
    }

    /**
     * Send post-valuation email to seller (for "Sign Up Later" option).
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPostValuationEmail($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::with('seller')->findOrFail($id);

        // Check if instruction already exists and is signed
        $instruction = \App\Models\PropertyInstruction::where('property_id', $property->id)->first();

        if ($instruction && $instruction->status === 'signed') {
            return back()->with('error', 'This property already has a signed instruction.');
        }

        // Create or update instruction request (pending status - not yet requested)
        if (!$instruction) {
            $instruction = \App\Models\PropertyInstruction::create([
                'property_id' => $property->id,
                'seller_id' => $property->seller_id,
                'status' => 'pending',
                'requested_by' => $user->id,
                'requested_at' => null, // Will be set when seller clicks from email
                'fee_percentage' => 1.5, // Default fee
            ]);
        }

        // Send post-valuation email to seller with "Instruct Abodeology" button
        try {
            \Mail::to($property->seller->email)->send(
                new \App\Mail\PostValuationEmail($property->seller, $property)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send post-valuation email: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to send post-valuation email. Please try again.');
        }

        return redirect()->route('admin.properties.show', $property->id)
            ->with('success', 'Post-valuation email sent to seller. The email contains an "Instruct Abodeology" button for them to sign the Terms & Conditions when ready.');
    }

    /**
     * Show HomeCheck scheduling form.
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showScheduleHomeCheck($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::with(['seller', 'homecheckReports'])->findOrFail($id);

        // Check if there's already a scheduled or in-progress HomeCheck
        $existingHomeCheck = \App\Models\HomecheckReport::where('property_id', $property->id)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->first();

        return view('admin.properties.schedule-homecheck', compact('property', 'existingHomeCheck'));
    }

    /**
     * Schedule a HomeCheck.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeScheduleHomeCheck(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'scheduled_date.required' => 'Please select a scheduled date.',
            'scheduled_date.date' => 'Please provide a valid date.',
            'scheduled_date.after_or_equal' => 'Scheduled date must be today or in the future.',
        ]);

        try {
            // Check if there's already a scheduled or in-progress HomeCheck
            $existingHomeCheck = \App\Models\HomecheckReport::where('property_id', $property->id)
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->first();

            if ($existingHomeCheck) {
                return back()
                    ->withInput()
                    ->with('error', 'A HomeCheck is already scheduled or in progress for this property.');
            }

            // Create HomeCheck report
            $homecheckReport = \App\Models\HomecheckReport::create([
                'property_id' => $property->id,
                'status' => 'scheduled',
                'scheduled_by' => $user->id,
                'scheduled_date' => $validated['scheduled_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'HomeCheck scheduled successfully! Scheduled date: ' . \Carbon\Carbon::parse($validated['scheduled_date'])->format('l, F j, Y'));

        } catch (\Exception $e) {
            \Log::error('HomeCheck scheduling error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while scheduling the HomeCheck. Please try again.');
        }
    }

    /**
     * Show HomeCheck completion form (upload 360 images + photos).
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showCompleteHomeCheck($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::with(['seller', 'homecheckReports'])->findOrFail($id);

        // Get the active HomeCheck report
        $homecheckReport = \App\Models\HomecheckReport::where('property_id', $property->id)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->first();

        if (!$homecheckReport) {
            return redirect()->route('admin.properties.show', $property->id)
                ->with('error', 'No scheduled HomeCheck found. Please schedule a HomeCheck first.');
        }

        // Get existing homecheck data
        $homecheckData = \App\Models\HomecheckData::where('property_id', $property->id)
            ->orderBy('room_name')
            ->orderBy('created_at')
            ->get();

        return view('admin.properties.complete-homecheck', compact('property', 'homecheckReport', 'homecheckData'));
    }

    /**
     * Complete HomeCheck by uploading 360 images + photos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCompleteHomeCheck(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::findOrFail($id);

        // Get the active HomeCheck report
        $homecheckReport = \App\Models\HomecheckReport::where('property_id', $property->id)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->first();

        if (!$homecheckReport) {
            return redirect()->route('admin.properties.show', $property->id)
                ->with('error', 'No scheduled HomeCheck found. Please schedule a HomeCheck first.');
        }

        $validated = $request->validate([
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.name' => ['required', 'string', 'max:255'],
            'rooms.*.images' => ['required', 'array', 'min:1'],
            'rooms.*.images.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'], // 10MB max
            'rooms.*.is_360' => ['nullable', 'boolean'], // Flag for 360 images
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'rooms.required' => 'Please add at least one room.',
            'rooms.min' => 'Please add at least one room.',
            'rooms.*.name.required' => 'Room name is required for all rooms.',
            'rooms.*.images.required' => 'Please upload at least one image for each room.',
            'rooms.*.images.min' => 'Please upload at least one image for each room.',
            'rooms.*.images.*.image' => 'All files must be images.',
            'rooms.*.images.*.max' => 'Image size must not exceed 10MB.',
        ]);

        try {
            \DB::beginTransaction();

            // Update HomeCheck report status to in_progress while uploading
            $homecheckReport->update([
                'status' => 'in_progress',
            ]);

            // Process each room
            foreach ($validated['rooms'] as $roomIndex => $roomData) {
                $roomName = $roomData['name'];
                $is360 = isset($roomData['is_360']) && $roomData['is_360'];
                
                // Process each image
                foreach ($roomData['images'] as $imageIndex => $image) {
                    // Store image in property-specific folder
                    $imagePath = $image->store('homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos'), 'public');
                    
                    // Create homecheck data record
                    \App\Models\HomecheckData::create([
                        'property_id' => $property->id,
                        'homecheck_report_id' => $homecheckReport->id,
                        'room_name' => $roomName,
                        'image_path' => $imagePath,
                        'is_360' => $is360, // Store if it's a 360 image
                        // AI analysis can be added later
                    ]);
                }
            }

            // Mark HomeCheck as completed
            $homecheckReport->update([
                'status' => 'completed',
                'completed_by' => $user->id,
                'completed_at' => now(),
                'notes' => $validated['notes'] ?? $homecheckReport->notes,
            ]);

            \DB::commit();

            // Process HomeCheck and generate AI report asynchronously (or synchronously for now)
            try {
                $reportService = new \App\Services\HomeCheckReportService();
                $reportGenerated = $reportService->processAndGenerateReport($homecheckReport);
                
                if ($reportGenerated) {
                    \Log::info('AI report generated successfully for HomeCheck ID: ' . $homecheckReport->id);
                } else {
                    \Log::warning('AI report generation failed for HomeCheck ID: ' . $homecheckReport->id);
                }
            } catch (\Exception $e) {
                \Log::error('Error generating AI report: ' . $e->getMessage());
                // Don't fail the request if report generation fails
            }

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'HomeCheck completed successfully! All images have been uploaded. AI report is being generated and will be available shortly.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('HomeCheck completion error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while completing the HomeCheck. Please try again.');
        }
    }

    /**
     * Show listing upload form (photos, floorplan, EPC).
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showListingUpload($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::with(['seller', 'photos', 'documents'])->findOrFail($id);

        // Only allow listing upload if property status is 'signed' or later
        if (!in_array($property->status, ['signed', 'pre_marketing', 'draft'])) {
            return redirect()->route('admin.properties.show', $property->id)
                ->with('error', 'Listing can only be prepared for properties with signed instruction or later.');
        }

        return view('admin.properties.listing-upload', compact('property'));
    }

    /**
     * Store listing upload (photos, floorplan, EPC) and create listing draft.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeListingUpload(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::findOrFail($id);

        // Only allow listing upload if property status is 'signed' or later
        if (!in_array($property->status, ['signed', 'pre_marketing', 'draft'])) {
            return back()->with('error', 'Listing can only be prepared for properties with signed instruction or later.');
        }

        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'], // 10MB max
            'primary_photo_index' => ['nullable', 'integer', 'min:0'],
            'floorplan' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:10240'], // 10MB max
            'epc' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:10240'], // 10MB max
        ], [
            'photos.required' => 'Please upload at least one property photo.',
            'photos.min' => 'Please upload at least one property photo.',
            'photos.*.image' => 'All photo files must be images.',
            'photos.*.max' => 'Photo size must not exceed 10MB.',
            'floorplan.mimes' => 'Floorplan must be a PDF or image file.',
            'epc.mimes' => 'EPC must be a PDF or image file.',
        ]);

        try {
            \DB::beginTransaction();

            // Upload and save photos
            $primaryPhotoIndex = $validated['primary_photo_index'] ?? 0;
            foreach ($validated['photos'] as $index => $photo) {
                $photoPath = $photo->store('properties/' . $property->id . '/photos', 'public');
                
                \App\Models\PropertyPhoto::create([
                    'property_id' => $property->id,
                    'file_path' => $photoPath,
                    'sort_order' => $index,
                    'is_primary' => ($index == $primaryPhotoIndex),
                    'uploaded_at' => now(),
                ]);
            }

            // Upload floorplan if provided
            if ($request->hasFile('floorplan')) {
                $floorplanPath = $request->file('floorplan')->store('properties/' . $property->id . '/documents', 'public');
                
                \App\Models\PropertyDocument::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'document_type' => 'floorplan',
                    ],
                    [
                        'file_path' => $floorplanPath,
                        'uploaded_at' => now(),
                    ]
                );
            }

            // Upload EPC if provided
            if ($request->hasFile('epc')) {
                $epcPath = $request->file('epc')->store('properties/' . $property->id . '/documents', 'public');
                
                \App\Models\PropertyDocument::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'document_type' => 'epc',
                    ],
                    [
                        'file_path' => $epcPath,
                        'uploaded_at' => now(),
                    ]
                );
            }

            // Update property status to 'draft' (listing draft ready)
            if ($property->status === 'signed') {
                $property->update(['status' => 'draft']);
            }

            \DB::commit();

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'Listing draft created successfully! Photos and documents have been uploaded. You can now publish the listing.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Listing upload error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while uploading the listing. Please try again.');
        }
    }

    /**
     * Publish listing to portals (Rightmove, etc.) and update status to 'live'.
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishListing($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = Property::with(['seller', 'photos', 'documents'])->findOrFail($id);

        // Validate that listing is ready for publishing
        if ($property->photos->count() < 1) {
            return back()->with('error', 'Please upload at least one photo before publishing the listing.');
        }

        if (!$property->asking_price) {
            return back()->with('error', 'Please set an asking price before publishing the listing.');
        }

        try {
            \DB::beginTransaction();

            // Publish to portals (simulated - in production, this would call Rightmove/other portal APIs)
            $portalResults = $this->publishToPortals($property);

            // Update property status to 'live'
            $property->update([
                'status' => 'live',
            ]);

            \DB::commit();

            $successMessage = 'Listing published successfully! Status updated to "Live on Market".';
            if (!empty($portalResults)) {
                $successMessage .= ' Published to: ' . implode(', ', array_keys($portalResults));
            }

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Listing publish error: ' . $e->getMessage());

            return back()
                ->with('error', 'An error occurred while publishing the listing. Please try again.');
        }
    }

    /**
     * Publish property to external portals (Rightmove, Zoopla, etc.).
     * This is a placeholder - in production, integrate with actual portal APIs.
     *
     * @param  Property  $property
     * @return array  Portal results
     */
    protected function publishToPortals(Property $property): array
    {
        // Simulate portal publishing
        // In production, this would:
        // 1. Connect to Rightmove API
        // 2. Connect to Zoopla API
        // 3. Connect to OnTheMarket API
        // 4. Send property data and images
        // 5. Handle responses and errors

        $portals = ['Rightmove', 'Zoopla', 'OnTheMarket'];
        $results = [];

        foreach ($portals as $portal) {
            // Simulate API call
            \Log::info("Publishing property {$property->id} to {$portal}");
            
            // In production, make actual API calls here:
            // $response = Http::post("{$portalApiUrl}/properties", [...]);
            // $results[$portal] = $response->successful();
            
            // For now, simulate success
            $results[$portal] = true;
        }

        return $results;
    }
}
