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

        return view('admin.dashboard', compact('stats', 'valuations', 'sellers', 'buyers', 'offers', 'sales', 'pvas', 'alerts'));
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
     * Show the onboarding form for completing seller onboarding after valuation.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showValuationOnboarding($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $valuation = Valuation::with('seller')->findOrFail($id);

        // Pre-fill form with valuation data
        $onboarding = (object) [
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

        return view('admin.valuations.onboarding', compact('valuation', 'onboarding'));
    }

    /**
     * Store the onboarding data completed by agent after valuation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeValuationOnboarding(Request $request, $id)
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
        ], [
            'property_address.required' => 'Property address is required.',
            'property_type.required' => 'Property type is required.',
            'bedrooms.required' => 'Number of bedrooms is required.',
            'bathrooms.required' => 'Number of bathrooms is required.',
            'tenure.required' => 'Tenure is required.',
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
                    'status' => 'property_details_completed', // Set status after onboarding completion
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

            // Update valuation
            $valuation->update([
                'estimated_value' => $validated['estimated_value'] ?? null,
                'status' => 'completed',
                'notes' => $validated['agent_notes'] ?? null,
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

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'Seller onboarding completed successfully! Property has been created. You can now request instruction from the seller.');
            $property = Property::where('seller_id', $valuation->seller_id)
                ->where('address', $validated['property_address'])
                ->first();

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'Seller onboarding completed successfully! Property has been created. You can now request instruction from the seller.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Valuation onboarding error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving the onboarding data. Please try again.');
        }
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

        $property = Property::with(['seller', 'instruction', 'materialInformation'])->findOrFail($id);

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
}
