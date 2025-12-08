<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Valuation;
use App\Models\Property;
use App\Models\PropertyMaterialInformation;
use App\Models\Offer;
use App\Models\User;
use App\Models\AmlCheck;
use App\Models\Viewing;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
     * Get agent's assigned property IDs.
     * Agents are assigned to properties via PropertyInstruction requested_by field.
     *
     * @param int $agentId
     * @return array
     */
    private function getAgentPropertyIds($agentId): array
    {
        return \App\Models\PropertyInstruction::where('requested_by', $agentId)
            ->pluck('property_id')
            ->toArray();
    }

    /**
     * Show the admin dashboard (super user - full system access).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Only admins can access this dashboard
        if ($user->role !== 'admin') {
            if ($user->role === 'agent') {
                return redirect()->route('admin.agent.dashboard');
            }
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access the admin dashboard.');
        }
        
        // ============================================
        // ADMIN DASHBOARD DATA AGGREGATION
        // SECURITY: Admin has full system access - no filtering
        // ============================================
        
        // Fetch all valuations (admin sees all)
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

        // Dashboard statistics (admin sees all)
        $pendingValuations = Valuation::where('status', 'pending')->count();
        $scheduledValuations = Valuation::where('status', 'scheduled')->count();
        $activeListings = Property::where('status', 'live')->count();
        $offersReceived = Offer::where('status', 'pending')->count();
        $salesInProgress = Property::where('status', 'sold')->count();
        $pvasActive = User::where('role', 'pva')->count();

        $stats = [
            'total_valuations' => Valuation::count(),
            'pending_valuations' => $pendingValuations,
            'scheduled_valuations' => $scheduledValuations,
            'active_listings' => $activeListings,
            'offers_received' => $offersReceived,
            'sales_in_progress' => $salesInProgress,
            'pvas_active' => $pvasActive,
        ];

        // Get recent data (admin sees all)
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
            ->withCount(['assignedViewings' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // COMPREHENSIVE DATA AGGREGATION
        // ============================================
        
        // 1. NEW LISTINGS - Recently created properties (last 7 days)
        $newListings = Property::where('created_at', '>=', now()->subDays(7))
            ->with(['seller'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // 2. AML PENDING - AML checks awaiting verification
        $amlPending = AmlCheck::where('verification_status', 'pending')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // 3. OFFERS PENDING SELLER RESPONSE - Offers with pending/countered status
        $offersPendingResponse = Offer::whereIn('status', ['pending', 'countered'])
            ->with(['buyer', 'property.seller'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // 4. HOMECHECK PENDING - HomeCheck reports awaiting completion
        $homecheckPending = \App\Models\HomecheckReport::whereIn('status', ['pending', 'in_progress'])
            ->with(['property.seller'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // 5. RECENT ACTIVITY - Comprehensive activity log
        $recentActivity = collect();
        
        // Add recent valuations
        $recentValuations = Valuation::with('seller')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'valuation',
                    'id' => $item->id,
                    'title' => 'New Valuation Request',
                    'description' => $item->property_address ?? 'N/A',
                    'user' => $item->seller->name ?? 'N/A',
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'route' => route('admin.valuations.show', $item->id),
                ];
            });
        $recentActivity = $recentActivity->merge($recentValuations);
        
        // Add recent offers
        $recentOffers = Offer::with(['buyer', 'property'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'offer',
                    'id' => $item->id,
                    'title' => 'New Offer Received',
                    'description' => '£' . number_format($item->offer_amount, 0) . ' on ' . ($item->property->address ?? 'N/A'),
                    'user' => $item->buyer->name ?? 'N/A',
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'route' => route('admin.properties.show', $item->property_id),
                ];
            });
        $recentActivity = $recentActivity->merge($recentOffers);
        
        // Add recent properties
        $recentProperties = Property::with('seller')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'property',
                    'id' => $item->id,
                    'title' => 'New Property Listing',
                    'description' => $item->address ?? 'N/A',
                    'user' => $item->seller->name ?? 'N/A',
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'route' => route('admin.properties.show', $item->id),
                ];
            });
        $recentActivity = $recentActivity->merge($recentProperties);
        
        // Add recent viewings
        $recentViewings = \App\Models\Viewing::with(['buyer', 'property'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'viewing',
                    'id' => $item->id,
                    'title' => 'New Viewing Request',
                    'description' => $item->property->address ?? 'N/A',
                    'user' => $item->buyer->name ?? 'N/A',
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'route' => route('admin.properties.show', $item->property_id),
                ];
            });
        $recentActivity = $recentActivity->merge($recentViewings);
        
        // Sort by date and limit to 15 most recent
        $recentActivity = $recentActivity->sortByDesc('date')->take(15);
        
        // Generate alerts based on pending items
        $alerts = [];
        if ($pendingValuations > 0) {
            $alerts[] = "You have {$pendingValuations} pending valuation request(s) that need attention.";
        }
        if ($offersReceived > 0) {
            $alerts[] = "You have {$offersReceived} pending offer(s) awaiting seller response.";
        }
        if ($amlPending->count() > 0) {
            $alerts[] = "You have {$amlPending->count()} AML check(s) pending verification.";
        }
        if ($homecheckPending->count() > 0) {
            $alerts[] = "You have {$homecheckPending->count()} HomeCheck(s) pending completion.";
        }
        if ($newListings->count() > 0) {
            $alerts[] = "You have {$newListings->count()} new listing(s) in the last 7 days.";
        }
        if (empty($alerts)) {
            $alerts[] = 'System running normally';
            $alerts[] = 'No pending maintenance tasks';
            $alerts[] = 'All services operational';
        }

        return view('admin.dashboard', compact(
            'stats', 
            'valuations', 
            'todaysAppointments', 
            'sellers', 
            'buyers', 
            'offers', 
            'sales', 
            'pvas', 
            'alerts',
            'newListings',
            'amlPending',
            'offersPendingResponse',
            'homecheckPending',
            'recentActivity'
        ));
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
     * Show the agent dashboard (restricted - only sees assigned properties, progress, sales, and tasks).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function agentDashboard()
    {
        $user = auth()->user();
        
        // Only agents can access this dashboard
        if ($user->role !== 'agent') {
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access the agent dashboard.');
        }

        // Get agent's assigned property IDs
        $agentPropertyIds = $this->getAgentPropertyIds($user->id);
        
        // Get agent's assigned properties
        $properties = Property::whereIn('id', $agentPropertyIds)
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get valuations for agent's assigned properties
        $valuations = Valuation::whereHas('seller', function($q) use ($agentPropertyIds) {
                $q->whereHas('properties', function($query) use ($agentPropertyIds) {
                    $query->whereIn('properties.id', $agentPropertyIds);
                });
            })
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get offers for agent's assigned properties
        $offers = Offer::whereIn('property_id', $agentPropertyIds)
            ->with(['property', 'buyer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get today's scheduled valuations (appointments) for agent
        $todaysAppointments = Valuation::whereHas('seller', function($q) use ($agentPropertyIds) {
                $q->whereHas('properties', function($query) use ($agentPropertyIds) {
                    $query->whereIn('properties.id', $agentPropertyIds);
                });
            })
            ->where('status', 'scheduled')
            ->whereDate('valuation_date', today())
            ->with('seller')
            ->orderBy('valuation_time', 'asc')
            ->get();

        // Get viewings for agent's assigned properties
        $viewings = \App\Models\Viewing::whereIn('property_id', $agentPropertyIds)
            ->with(['property', 'buyer'])
            ->orderBy('viewing_date', 'asc')
            ->limit(10)
            ->get();

        // Get sales (sold properties) for agent's assigned properties
        $sales = Property::whereIn('id', $agentPropertyIds)
            ->where('status', 'sold')
            ->with('seller')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $stats = [
            'assigned_properties' => Property::whereIn('id', $agentPropertyIds)->count(),
            'active_listings' => Property::whereIn('id', $agentPropertyIds)->where('status', 'live')->count(),
            'pending_valuations' => Valuation::whereHas('seller', function($q) use ($agentPropertyIds) {
                    $q->whereHas('properties', function($query) use ($agentPropertyIds) {
                        $query->whereIn('properties.id', $agentPropertyIds);
                    });
                })
                ->where('status', 'pending')
                ->count(),
            'pending_offers' => Offer::whereIn('property_id', $agentPropertyIds)
                ->where('status', 'pending')
                ->count(),
            'upcoming_viewings' => \App\Models\Viewing::whereIn('property_id', $agentPropertyIds)
                ->where('viewing_date', '>=', now())
                ->count(),
            'sales_in_progress' => Property::whereIn('id', $agentPropertyIds)
                ->whereIn('status', ['sold', 'under_offer'])
                ->count(),
        ];

        return view('admin.agent-dashboard', compact('stats', 'properties', 'valuations', 'offers', 'viewings', 'sales', 'todaysAppointments'));
    }

    /**
     * List all users with their roles (Admin Only).
     * Agents cannot access this page - they can only view their clients through property relationships.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users()
    {
        $user = auth()->user();
        
        // Only admins can access user management
        if ($user->role !== 'admin') {
            if ($user->role === 'agent') {
                return redirect()->route('admin.agent.dashboard')
                    ->with('error', 'You do not have permission to access user management. You can only view your assigned clients through their properties.');
            }
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $query = User::query();

        // Search by name or email
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // Filter by role
        if (request()->has('role') && request('role')) {
            $query->where('role', request('role'));
        }

        // Filter by registration date
        if (request()->has('date_from') && request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }

        if (request()->has('date_to') && request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        return view('admin.users.index', compact('users'));
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

        $valuationsQuery = Valuation::with('seller');
        
        // For agents, only show valuations for their assigned properties
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (empty($agentPropertyIds)) {
                $valuations = collect([]);
            } else {
                $valuationsQuery->whereHas('seller', function($q) use ($agentPropertyIds) {
                    $q->whereHas('properties', function($query) use ($agentPropertyIds) {
                        $query->whereIn('properties.id', $agentPropertyIds);
                    });
                });
            }
        }
        
        $valuations = $valuationsQuery->orderBy('created_at', 'desc')->paginate(20);

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
        
        // For agents, verify they have access to this valuation's property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            $property = Property::where('seller_id', $valuation->seller_id)
                ->where('address', $valuation->property_address)
                ->first();
            
            if (!$property || !in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.valuations.index')
                    ->with('error', 'You do not have permission to view this valuation.');
            }
        }

        return view('admin.valuations.show', compact('valuation'));
    }

    /**
     * Update valuation schedule (date, time, and status) by admin/agent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Valuation ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateValuationSchedule(Request $request, $id)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to update valuation schedules.');
        }

        $valuation = Valuation::with('seller')->findOrFail($id);

        $validated = $request->validate([
            'valuation_date' => ['required', 'date'],
            'valuation_time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:pending,scheduled,completed'],
        ]);

        $valuation->valuation_date = $validated['valuation_date'];
        $valuation->valuation_time = $validated['valuation_time'] ?? null;
        $valuation->status = $validated['status'];
        $valuation->save();

        return redirect()
            ->route('admin.valuations.show', $valuation->id)
            ->with('success', 'Valuation schedule updated successfully.');
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

        // Check if property already exists for this valuation
        $existingProperty = \App\Models\Property::where('seller_id', $valuation->seller_id)
            ->where('address', $valuation->property_address)
            ->first();

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
            'reception_rooms' => $existingProperty->reception_rooms ?? null,
            'outbuildings' => $existingProperty->outbuildings ?? null,
            'garden_details' => $existingProperty->garden_details ?? null,
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
            'access_notes' => $existingProperty->access_notes ?? null,
            'pricing_notes' => $existingProperty->pricing_notes ?? null,
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
            'reception_rooms' => ['nullable', 'integer', 'min:0'],
            'outbuildings' => ['nullable', 'string', 'max:500'],
            'garden_details' => ['nullable', 'string', 'max:2000'],
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
            'pricing_notes' => ['nullable', 'string', 'in:Offers in the Region of,Offers in Excess of,Guide Price,Asking Price'],
            
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
                    'reception_rooms' => $validated['reception_rooms'] ?? null,
                    'outbuildings' => $validated['outbuildings'] ?? null,
                    'garden_details' => $validated['garden_details'] ?? null,
                    'parking' => $validated['parking'] ?? null,
                    'tenure' => $validated['tenure'],
                    'lease_years_remaining' => $validated['lease_years_remaining'] ?? null,
                    'ground_rent' => $validated['ground_rent'] ?? null,
                    'service_charge' => $validated['service_charge'] ?? null,
                    'managing_agent' => $validated['managing_agent'] ?? null,
                    'asking_price' => $validated['asking_price'] ?? null,
                    'pricing_notes' => $validated['pricing_notes'] ?? null,
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
                'notes' => $validated['agent_notes'] ?? $valuation->notes,
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

            // Immediately send Terms & Conditions (instruction request) to seller after valuation
            try {
                // Create or update instruction record
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

                // Email seller with link to sign Terms & Conditions
                \Mail::to($property->seller->email)->send(
                    new \App\Mail\InstructionRequestNotification($property->seller, $property, $instruction)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send automatic instruction request after valuation: ' . $e->getMessage());
            }

            // Redirect directly to property page
            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'Valuation Form completed successfully! Property details have been captured and saved to the seller\'s profile (status: Property Details Captured). Terms & Conditions have been emailed to the seller as the next step.');

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function properties(Request $request)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $propertiesQuery = Property::with(['seller', 'instruction']);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $propertiesQuery->where('status', $request->status);
        }
        
        // For agents, only show their assigned properties
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (empty($agentPropertyIds)) {
                $properties = collect([]);
            } else {
                $propertiesQuery->whereIn('id', $agentPropertyIds);
            }
        }
        
        $properties = $propertiesQuery->orderBy('created_at', 'desc')->paginate(20);

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

        $property = Property::with(['seller', 'instruction', 'materialInformation', 'homecheckReports', 'homecheckData', 'photos', 'documents', 'offers.buyer', 'offers.latestDecision'])->findOrFail($id);
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.index')
                    ->with('error', 'You do not have permission to view this property.');
            }
        }

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
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.index')
                    ->with('error', 'You do not have permission to schedule a HomeCheck for this property.');
            }
        }

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
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.index')
                    ->with('error', 'You do not have permission to schedule a HomeCheck for this property.');
            }
        }

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
            // Convert date string to datetime for scheduled_date
            $scheduledDate = \Carbon\Carbon::parse($validated['scheduled_date'])->startOfDay();
            
            // Create HomeCheck report
            // Use empty string for report_path if column is still NOT NULL (temporary workaround until migration runs)
            // After migration, this can be changed to null
            $homecheckReport = \App\Models\HomecheckReport::create([
                'property_id' => $property->id,
                'status' => 'scheduled',
                'scheduled_by' => $user->id,
                'scheduled_date' => $scheduledDate,
                'notes' => $validated['notes'] ?? null,
                'report_path' => '', // Temporary: empty string until migration makes column nullable
            ]);

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'HomeCheck scheduled successfully! Scheduled date: ' . \Carbon\Carbon::parse($validated['scheduled_date'])->format('l, F j, Y'));

        } catch (\Exception $e) {
            \Log::error('HomeCheck scheduling error: ' . $e->getMessage());
            \Log::error('HomeCheck scheduling error trace: ' . $e->getTraceAsString());
            \Log::error('HomeCheck scheduling error file: ' . $e->getFile() . ':' . $e->getLine());

            // Provide more helpful error message
            $errorMessage = 'An error occurred while scheduling the HomeCheck. ';
            if (str_contains($e->getMessage(), 'report_path')) {
                $errorMessage .= 'Please run the migration to make report_path nullable: php artisan migrate';
            } else {
                $errorMessage .= 'Error: ' . $e->getMessage();
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
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
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.index')
                    ->with('error', 'You do not have permission to complete a HomeCheck for this property.');
            }
        }

        // Get the active HomeCheck report
        $homecheckReport = \App\Models\HomecheckReport::where('property_id', $property->id)
            ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
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
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.index')
                    ->with('error', 'You do not have permission to complete a HomeCheck for this property.');
            }
        }

        // Get the active HomeCheck report
        $homecheckReport = \App\Models\HomecheckReport::where('property_id', $property->id)
            ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
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
            'rooms.*.moisture_reading' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'rooms.required' => 'Please add at least one room.',
            'rooms.min' => 'Please add at least one room.',
            'rooms.*.name.required' => 'Room name is required for all rooms.',
            'rooms.*.images.required' => 'Please upload at least one image for each room.',
            'rooms.*.images.min' => 'Please upload at least one image for each room.',
            'rooms.*.images.*.image' => 'All files must be images.',
            'rooms.*.images.*.max' => 'Image size must not exceed 10MB.',
            'rooms.*.moisture_reading.numeric' => 'Moisture reading must be a number.',
            'rooms.*.moisture_reading.min' => 'Moisture reading must be between 0 and 100.',
            'rooms.*.moisture_reading.max' => 'Moisture reading must be between 0 and 100.',
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
                $moistureReading = isset($roomData['moisture_reading']) && $roomData['moisture_reading'] !== '' 
                    ? (float) $roomData['moisture_reading'] 
                    : null;
                
                // Determine storage disk (S3 if configured, otherwise public)
                $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
                
                // Process each image with optimization
                $imageOptimizer = new \App\Services\ImageOptimizationService();
                foreach ($roomData['images'] as $imageIndex => $image) {
                    // Store image in property-specific folder
                    $imagePath = $image->store('homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos'), $disk);
                    
                    // Optimize the image (max width 1920px, quality 85%)
                    try {
                        $imageOptimizer->optimizeExisting($imagePath, $disk, 1920, 85);
                    } catch (\Exception $e) {
                        \Log::warning('Image optimization failed for homecheck image: ' . $e->getMessage());
                        // Continue even if optimization fails
                    }
                    
                    // Get image metadata
                    $imageSize = $image->getSize();
                    $imageMimeType = $image->getMimeType();
                    
                    // Create homecheck data record with moisture reading and metadata
                    \App\Models\HomecheckData::create([
                        'property_id' => $property->id,
                        'homecheck_report_id' => $homecheckReport->id,
                        'room_name' => $roomName,
                        'image_path' => $imagePath,
                        'is_360' => $is360, // Store if it's a 360 image
                        'moisture_reading' => $moistureReading, // Save moisture reading per room
                        'created_at' => now(),
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
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.index')
                    ->with('error', 'You do not have permission to upload listing materials for this property.');
            }
        }

        // Only allow listing upload if property status is 'signed' or later
        if (!in_array($property->status, ['signed', 'pre_marketing', 'draft'])) {
            return back()->with('error', 'Listing can only be prepared for properties with signed instruction or later.');
        }

        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'], // 10MB max
            'primary_photo_index' => ['nullable', 'integer', 'min:0'],
            'floorplan' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg', 'max:10240'], // 10MB max
            'epc' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg', 'max:10240'], // 10MB max
            'additional_documents' => ['nullable', 'array'],
            'additional_documents.*' => ['file', 'mimes:pdf,doc,docx,jpeg,png,jpg', 'max:10240'], // 10MB max
        ], [
            'photos.required' => 'Please upload at least one property photo.',
            'photos.min' => 'Please upload at least one property photo.',
            'photos.*.image' => 'All photo files must be images.',
            'photos.*.max' => 'Photo size must not exceed 10MB.',
            'floorplan.mimes' => 'Floorplan must be a PDF, DOC, DOCX, or image file.',
            'epc.mimes' => 'EPC must be a PDF, DOC, DOCX, or image file.',
            'additional_documents.*.mimes' => 'Additional documents must be PDF, DOC, DOCX, or image files.',
            'additional_documents.*.max' => 'Each additional document must not exceed 10MB.',
        ]);

        try {
            \DB::beginTransaction();

            // Determine storage disk (S3 if configured, otherwise public)
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

            // Upload and save photos with optimization
            $imageOptimizer = new \App\Services\ImageOptimizationService();
            $primaryPhotoIndex = $validated['primary_photo_index'] ?? 0;
            foreach ($validated['photos'] as $index => $photo) {
                // Store original path first
                $photoPath = $photo->store('properties/' . $property->id . '/photos', $disk);
                
                // Optimize the image (max width 1920px, quality 85%)
                try {
                    $imageOptimizer->optimizeExisting($photoPath, $disk, 1920, 85);
                } catch (\Exception $e) {
                    \Log::warning('Image optimization failed for property photo: ' . $e->getMessage());
                    // Continue even if optimization fails
                }
                
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
                $floorplanPath = $request->file('floorplan')->store('properties/' . $property->id . '/documents', $disk);
                
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
                $epcPath = $request->file('epc')->store('properties/' . $property->id . '/documents', $disk);
                
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

            // Upload additional documents if provided
            if ($request->hasFile('additional_documents')) {
                foreach ($request->file('additional_documents') as $document) {
                    $documentPath = $document->store('properties/' . $property->id . '/documents', $disk);
                    
                    \App\Models\PropertyDocument::create([
                        'property_id' => $property->id,
                        'document_type' => 'other',
                        'file_path' => $documentPath,
                        'uploaded_at' => now(),
                    ]);
                }
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
            \Log::error('Listing upload error trace: ' . $e->getTraceAsString());
            \Log::error('Listing upload error file: ' . $e->getFile() . ':' . $e->getLine());

            // Provide more helpful error message
            $errorMessage = 'An error occurred while uploading the listing. ';
            if (str_contains($e->getMessage(), 'storage') || str_contains($e->getMessage(), 'disk')) {
                $errorMessage .= 'Storage error: Please ensure the storage directory exists and is writable.';
            } elseif (str_contains($e->getMessage(), 'SQL') || str_contains($e->getMessage(), 'column')) {
                $errorMessage .= 'Database error: ' . $e->getMessage();
            } else {
                $errorMessage .= 'Error: ' . $e->getMessage();
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
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
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.index')
                    ->with('error', 'You do not have permission to publish this listing.');
            }
        }

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
            try {
                $portalResults = $this->publishToPortals($property);
            } catch (\Exception $portalError) {
                // Log portal error but don't fail the entire publish operation
                \Log::warning('Portal publishing error (continuing anyway): ' . $portalError->getMessage());
                $portalResults = [];
            }

            // Update property status to 'live'
            $property->update([
                'status' => 'live',
            ]);

            \DB::commit();

            $successMessage = 'Listing published successfully! Status updated to "Live on Market".';
            if (!empty($portalResults)) {
                $publishedPortals = array_filter($portalResults);
                if (!empty($publishedPortals)) {
                    $successMessage .= ' Published to: ' . implode(', ', array_keys($publishedPortals));
                }
            }

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Listing publish error: ' . $e->getMessage());
            \Log::error('Listing publish error trace: ' . $e->getTraceAsString());

            return back()
                ->with('error', 'An error occurred while publishing the listing: ' . $e->getMessage());
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
        $portals = ['Rightmove', 'Zoopla', 'OnTheMarket'];
        $results = [];

        foreach ($portals as $portal) {
            \Log::info("Publishing property {$property->id} to {$portal}");
            
            if ($portal === 'Rightmove') {
                // Generate RTDF file for Rightmove
                try {
                    $rtdfService = new \App\Services\RTDFGeneratorService();
                    $rtdfFilePath = $rtdfService->generateForProperty($property);
                    
                    // Upload to FTP (stub - ready for production)
                    $ftpConnector = new \App\Services\RTDFFTPConnector();
                    $fileName = 'property_' . $property->id . '.txt';
                    
                    // Get full local file path
                    $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
                    $localFilePath = $disk === 's3' 
                        ? Storage::disk($disk)->url($rtdfFilePath) 
                        : Storage::disk($disk)->path($rtdfFilePath);
                    
                    $uploaded = $ftpConnector->uploadFile($localFilePath, $fileName);
                    
                    $results[$portal] = $uploaded;
                    \Log::info("RTDF file generated and uploaded for property {$property->id}", [
                        'file_path' => $rtdfFilePath,
                        'uploaded' => $uploaded,
                    ]);
                } catch (\Exception $e) {
                    \Log::error("RTDF generation failed for property {$property->id}: " . $e->getMessage());
                    $results[$portal] = false;
                }
            } else {
                // For other portals, simulate API call
                // In production, make actual API calls here:
                // $response = Http::post("{$portalApiUrl}/properties", [...]);
                // $results[$portal] = $response->successful();
                
                // For now, simulate success
                $results[$portal] = true;
            }
        }

        return $results;
    }
    
    /**
     * Generate RTDF file for a property.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateRTDF($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }
        
        try {
            $property = Property::with(['seller', 'photos', 'materialInformation', 'documents'])
                ->findOrFail($id);
            
            // Generate RTDF file
            $rtdfService = new \App\Services\RTDFGeneratorService();
            $rtdfFilePath = $rtdfService->generateForProperty($property);
            
            if (!$rtdfFilePath) {
                throw new \Exception('RTDF file generation returned empty path');
            }
            
            // Get full file path
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            
            // Check if file exists
            if (!Storage::disk($disk)->exists($rtdfFilePath)) {
                throw new \Exception('RTDF file was not created at: ' . $rtdfFilePath);
            }
            
            // If S3, get temporary URL for download
            if ($disk === 's3') {
                $fileUrl = Storage::disk($disk)->temporaryUrl($rtdfFilePath, now()->addMinutes(5));
                return redirect($fileUrl);
            }
            
            // For local storage, get the full path
            $fullPath = Storage::disk($disk)->path($rtdfFilePath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('RTDF file does not exist at: ' . $fullPath);
            }
            
            // Return file download
            return response()->download($fullPath, 'property_' . $property->id . '.txt', [
                'Content-Type' => 'text/plain',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('RTDF generation error: ' . $e->getMessage());
            \Log::error('RTDF generation error trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to generate RTDF file: ' . $e->getMessage());
        }
    }

    /**
     * List all AML checks for admin review.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function amlChecks()
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $amlChecksQuery = AmlCheck::with(['user', 'checker']);
        
        // For agents, only show AML checks for their assigned properties' sellers
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (empty($agentPropertyIds)) {
                $amlChecks = collect([]);
            } else {
                $agentSellerIds = Property::whereIn('id', $agentPropertyIds)
                    ->pluck('seller_id')
                    ->toArray();
                $amlChecksQuery->whereIn('user_id', $agentSellerIds);
            }
        }
        
        $amlChecks = $amlChecksQuery->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.aml-checks.index', compact('amlChecks'));
    }

    /**
     * Show individual AML check details and documents.
     *
     * @param  int  $id  AML Check ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAmlCheck($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $amlCheck = AmlCheck::with(['user', 'checker', 'documents'])->findOrFail($id);
        
        // For agents, verify they have access to this AML check's user
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            $agentSellerIds = Property::whereIn('id', $agentPropertyIds)
                ->pluck('seller_id')
                ->toArray();
            
            if (!in_array($amlCheck->user_id, $agentSellerIds)) {
                return redirect()->route('admin.aml-checks.index')
                    ->with('error', 'You do not have permission to view this AML check.');
            }
        }

        return view('admin.aml-checks.show', compact('amlCheck'));
    }

    /**
     * Verify or reject an AML check.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  AML Check ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyAmlCheck(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $validated = $request->validate([
            'verification_status' => ['required', 'string', 'in:verified,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'verification_status.required' => 'Please select a verification status.',
            'verification_status.in' => 'Invalid verification status.',
        ]);

        $amlCheck = AmlCheck::findOrFail($id);
        
        // For agents, verify they have access to this AML check's user
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            $agentSellerIds = Property::whereIn('id', $agentPropertyIds)
                ->pluck('seller_id')
                ->toArray();
            
            if (!in_array($amlCheck->user_id, $agentSellerIds)) {
                return redirect()->route('admin.aml-checks.index')
                    ->with('error', 'You do not have permission to verify this AML check.');
            }
        }

        try {
            $amlCheck->update([
                'verification_status' => $validated['verification_status'],
                'checked_by' => $user->id,
                'checked_at' => now(),
            ]);

            $statusText = $validated['verification_status'] === 'verified' ? 'verified' : 'rejected';
            
            return redirect()->route('admin.aml-checks.show', $amlCheck->id)
                ->with('success', "AML check has been {$statusText} successfully.");

        } catch (\Exception $e) {
            \Log::error('AML verification error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while verifying the AML check. Please try again.');
        }
    }

    /**
     * Serve AML document securely with authentication and authorization.
     *
     * @param  int  $documentId  AML Document ID
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function serveAmlDocument($documentId)
    {
        $user = auth()->user();
        
        // Only admin and agent can access AML documents
        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'You do not have permission to access this document.');
        }

        $document = \App\Models\AmlDocument::with('amlCheck.user')->findOrFail($documentId);
        $amlCheck = $document->amlCheck;

        // For agents, verify they have access to this AML check's user
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            $agentSellerIds = \App\Models\Property::whereIn('id', $agentPropertyIds)
                ->pluck('seller_id')
                ->toArray();
            
            if (!in_array($amlCheck->user_id, $agentSellerIds)) {
                abort(403, 'You do not have permission to access this document.');
            }
        }

        // Determine storage disk
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

        try {
            // Check if file exists
            if (!\Storage::disk($disk)->exists($document->file_path)) {
                abort(404, 'Document not found.');
            }

            // Get file contents
            $fileContents = \Storage::disk($disk)->get($document->file_path);
            $mimeType = $document->mime_type ?: \Storage::disk($disk)->mimeType($document->file_path) ?: 'application/octet-stream';

            // Return file with appropriate headers
            return response($fileContents, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . ($document->file_name ?? 'document') . '"')
                ->header('Cache-Control', 'private, max-age=3600');
        } catch (\Exception $e) {
            \Log::error('Error serving AML document: ' . $e->getMessage(), [
                'document_id' => $documentId,
                'file_path' => $document->file_path
            ]);
            abort(404, 'Error loading document.');
        }
    }

    /**
     * Release offer amount to seller.
     * Allows admin/agent to release the offer amount to the seller after review.
     *
     * @param  int  $id  Offer ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function releaseOfferToSeller($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $offer = \App\Models\Offer::with(['property', 'buyer'])->findOrFail($id);
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($offer->property_id, $agentPropertyIds)) {
                return redirect()->route('admin.properties.show', $offer->property_id)
                    ->with('error', 'You do not have permission to release this offer.');
            }
        }

        // Check if already released
        if ($offer->released_to_seller) {
            return redirect()->route('admin.properties.show', $offer->property_id)
                ->with('info', 'This offer has already been released to the seller.');
        }

        try {
            \DB::beginTransaction();

            // Update offer to released
            $offer->update([
                'released_to_seller' => true,
                'released_at' => now(),
                'released_by' => $user->id,
            ]);

            // Notify seller that offer amount has been released
            try {
                \Mail::to($offer->property->seller->email)->send(
                    new \App\Mail\OfferAmountReleased($offer, $offer->property)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send offer release notification to seller: ' . $e->getMessage());
            }

            \DB::commit();

            return redirect()->route('admin.properties.show', $offer->property_id)
                ->with('success', 'Offer amount has been released to the seller. They have been notified via email.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error releasing offer to seller: ' . $e->getMessage());
            
            return redirect()->route('admin.properties.show', $offer->property_id)
                ->with('error', 'Failed to release offer. Please try again.');
        }
    }

    /**
     * Show form to create a new PVA (Agent or Admin).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createPva()
    {
        $user = auth()->user();
        
        // Only agents and admins can access this
        if (!in_array($user->role, ['agent', 'admin'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        // Use different views based on role
        if ($user->role === 'admin') {
            return view('admin.pvas.create');
        }

        return view('admin.agent.pvas.create');
    }

    /**
     * Store a new PVA (Agent or Admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePva(Request $request)
    {
        $user = auth()->user();
        
        // Only agents and admins can access this
        if (!in_array($user->role, ['agent', 'admin'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
        ], [
            'name.required' => 'Please provide the PVA name.',
            'email.required' => 'Please provide the PVA email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Please provide the PVA phone number.',
        ]);

        try {
            // Generate a secure random password
            $password = Str::random(12);

            // Create new PVA user
            $pva = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($password),
                'role' => 'pva',
                'email_verified_at' => now(), // Auto-verify email
            ]);

            // Send login credentials email (optional - you may want to create a mail class for this)
            try {
                \Mail::to($pva->email)->send(new \App\Mail\PvaCreated($pva, $password));
            } catch (\Exception $e) {
                \Log::error('Failed to send PVA creation email: ' . $e->getMessage());
                // Don't fail the creation if email fails
            }

            // Redirect based on user role
            if ($user->role === 'admin') {
                return redirect()->route('admin.pvas.index')
                    ->with('success', 'PVA created successfully. Login credentials have been sent to ' . $pva->email);
            }
            
            return redirect()->route('admin.agent.dashboard')
                ->with('success', 'PVA created successfully. Login credentials have been sent to ' . $pva->email);

        } catch (\Exception $e) {
            \Log::error('Error creating PVA: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create PVA. Please try again.');
        }
    }

    /**
     * Manage PVAs (Admin only).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function managePvas()
    {
        $user = auth()->user();
        
        // Only admins can access this
        if ($user->role !== 'admin') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $pvas = User::where('role', 'pva')
            ->withCount(['assignedViewings' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.pvas.index', compact('pvas'));
    }

    /**
     * List all viewings for admin to assign (Admin only).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewings()
    {
        $user = auth()->user();
        
        // Only admins can access this
        if ($user->role !== 'admin') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $viewings = Viewing::with(['buyer', 'property.seller', 'pva'])
            ->orderBy('viewing_date', 'asc')
            ->paginate(20);

        return view('admin.viewings.index', compact('viewings'));
    }

    /**
     * Show form to assign a viewing to a PVA (Admin only).
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAssignViewing($id)
    {
        $user = auth()->user();
        
        // Only admins can access this
        if ($user->role !== 'admin') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $viewing = Viewing::with(['buyer', 'property.seller', 'pva'])->findOrFail($id);
        $pvas = User::where('role', 'pva')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.viewings.assign', compact('viewing', 'pvas'));
    }

    /**
     * Assign a viewing to a PVA (Admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignViewing(Request $request, $id)
    {
        $user = auth()->user();
        
        // Only admins can access this
        if ($user->role !== 'admin') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $validated = $request->validate([
            'pva_id' => ['required', 'exists:users,id'],
        ], [
            'pva_id.required' => 'Please select a PVA.',
            'pva_id.exists' => 'The selected PVA is invalid.',
        ]);

        try {
            $viewing = Viewing::with(['buyer', 'property.seller', 'pva'])->findOrFail($id);
            
            // Validate that the selected user is a PVA
            $pva = User::findOrFail($validated['pva_id']);
            if ($pva->role !== 'pva') {
                return back()
                    ->with('error', 'The selected user is not a PVA.');
            }

            // Update viewing assignment
            $wasPending = $viewing->status === 'pending';
            $viewing->update([
                'pva_id' => $validated['pva_id'],
                'status' => $viewing->status === 'pending' ? 'scheduled' : $viewing->status,
            ]);

            // Reload viewing with relationships
            $viewing->refresh();
            $viewing->load(['buyer', 'property.seller', 'pva']);

            // Send confirmation emails if viewing was pending
            if ($wasPending || $viewing->status === 'scheduled') {
                try {
                    // Notify buyer
                    \Mail::to($viewing->buyer->email)->send(
                        new \App\Mail\ViewingConfirmed($viewing, $viewing->property, $viewing->buyer, 'buyer')
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send viewing confirmation to buyer: ' . $e->getMessage());
                }

                try {
                    // Notify seller
                    if ($viewing->property->seller) {
                        \Mail::to($viewing->property->seller->email)->send(
                            new \App\Mail\ViewingConfirmed($viewing, $viewing->property, $viewing->property->seller, 'seller')
                        );
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send viewing confirmation to seller: ' . $e->getMessage());
                }

                try {
                    // Notify PVA
                    \Mail::to($pva->email)->send(
                        new \App\Mail\ViewingAssigned($viewing, $viewing->property, $pva)
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send viewing assignment notification to PVA: ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.viewings.index')
                ->with('success', 'Viewing assigned to PVA successfully.');

        } catch (\Exception $e) {
            \Log::error('Error assigning viewing to PVA: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Failed to assign viewing. Please try again.');
        }
    }
}
