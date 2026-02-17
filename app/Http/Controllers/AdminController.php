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
use Illuminate\Support\Facades\Mail;
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
        
        // 5. EXPIRING LISTINGS - Properties with sole selling agreement expiring soon (within 30 days)
        // Assuming 12-week (84 days) sole selling agreement period from when property went live
        $expiringListings = Property::whereIn('status', ['live', 'sstc', 'pre_marketing', 'signed'])
            ->where('status', '!=', 'sold')
            ->with(['seller', 'instruction'])
            ->get()
            ->filter(function($property) {
                // Calculate expiration date: 84 days (12 weeks) from when property went live
                // If property has instruction with signed_at, use that + 84 days
                // Otherwise, use property created_at + 84 days as fallback
                $agreementStart = $property->instruction && $property->instruction->signed_at 
                    ? \Carbon\Carbon::parse($property->instruction->signed_at)
                    : \Carbon\Carbon::parse($property->created_at);
                
                $agreementEnd = $agreementStart->copy()->addDays(84);
                $daysUntilExpiry = now()->diffInDays($agreementEnd, false);
                
                // Expiring within next 30 days (including already expired)
                return $daysUntilExpiry <= 30 && $daysUntilExpiry >= -7; // Show up to 7 days past expiry
            })
            ->sortBy(function($property) {
                $agreementStart = $property->instruction && $property->instruction->signed_at 
                    ? \Carbon\Carbon::parse($property->instruction->signed_at)
                    : \Carbon\Carbon::parse($property->created_at);
                return $agreementStart->copy()->addDays(84);
            })
            ->take(10)
            ->values();
        
        // 6. RECENT ACTIVITY - Comprehensive activity log
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
            'expiringListings', 
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
            ->with(['property', 'buyer', 'pva'])
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
        
        $valuation = Valuation::with(['seller', 'agent'])->findOrFail($id);
        
        // Allow admin, agent, or assigned PVA to view
        if (!in_array($user->role, ['admin', 'agent'])) {
            // If user is PVA, check if they are assigned to this valuation
            if ($user->role === 'pva' && $valuation->agent_id === $user->id) {
                // PVA is assigned, allow access
            } else {
                return redirect()->route($this->getRoleDashboard($user->role))
                    ->with('error', 'You do not have permission to access this page.');
            }
        }
        
        // Find the property associated with this valuation
        $property = Property::where('seller_id', $valuation->seller_id)
            ->where('address', $valuation->property_address)
            ->first();
        
        // For agents, verify they have access to this valuation's property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            
            if (!$property || !in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.valuations.index')
                    ->with('error', 'You do not have permission to view this valuation.');
            }
        }

        // Load HomeCheck data if property exists
        $completedHomeCheck = null;
        $activeHomeCheck = null;
        
        if ($property) {
            // Get completed HomeCheck (if any)
            $completedHomeCheck = \App\Models\HomecheckReport::where('property_id', $property->id)
                ->where('status', 'completed')
                ->with(['completer'])
                ->orderBy('completed_at', 'desc')
                ->first();
            
            // Get active HomeCheck (pending, scheduled, or in_progress)
            $activeHomeCheck = \App\Models\HomecheckReport::where('property_id', $property->id)
                ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
                ->with(['scheduler'])
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Get all Agent users for the assignment dropdown (only show for admin/agent)
        // Use TRIM/LOWER to handle case/whitespace differences across DBs (e.g. Postgres).
        $agents = null;
        if (in_array($user->role, ['admin', 'agent'])) {
            $agents = User::query()
                ->whereRaw('LOWER(TRIM(role)) = ?', ['agent'])
                ->orderBy('name')
                ->get();
        }

        return view('admin.valuations.show', compact('valuation', 'agents', 'property', 'completedHomeCheck', 'activeHomeCheck'));
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

        $valuation = Valuation::with(['seller', 'agent'])->findOrFail($id);

        // Capture previous schedule details so we can decide whether to notify the vendor
        $previousDate = $valuation->valuation_date ? $valuation->valuation_date->format('Y-m-d') : null;
        $previousTime = $valuation->valuation_time ? \Carbon\Carbon::parse($valuation->valuation_time)->format('H:i') : null;
        $previousAgentId = $valuation->agent_id;

        $validated = $request->validate([
            'valuation_date' => ['nullable', 'date'],
            'valuation_time' => ['nullable', 'date_format:H:i'],
            'agent_id' => ['nullable', 'exists:users,id'],
        ]);

        // Normalize valuation_time to 30-minute increments (server-side safety)
        if (!empty($validated['valuation_time'])) {
            try {
                [$hh, $mm] = array_map('intval', explode(':', $validated['valuation_time']));
                $total = ($hh * 60) + $mm;
                $snapped = (int) round($total / 30) * 30;
                $snappedH = (int) floor(($snapped % 1440) / 60);
                $snappedM = (int) ($snapped % 60);
                $validated['valuation_time'] = sprintf('%02d:%02d', $snappedH, $snappedM);
            } catch (\Throwable $e) {
                // If anything goes wrong, keep the original validated value
            }
        }

        // Ensure agent_id (if provided) belongs to an Agent user
        if (!empty($validated['agent_id'])) {
            $assignedAgent = User::find($validated['agent_id']);
            $assignedRole = strtolower(trim((string) ($assignedAgent->role ?? '')));
            if (!$assignedAgent || $assignedRole !== 'agent') {
                return redirect()
                    ->route('admin.valuations.show', $valuation->id)
                    ->with('error', 'Invalid agent selected. Only Agent users can be assigned.');
            }
        }

        $valuation->valuation_date = $validated['valuation_date'] ?? null;
        $valuation->valuation_time = $validated['valuation_time'] ?? null;
        $valuation->agent_id = $validated['agent_id'] ?? null;

        // Update status automatically: scheduled when a date is set, pending when not. 'completed' is set when the valuation form is submitted.
        if ($valuation->status === 'completed') {
            // Never overwrite completed
        } elseif (!empty($valuation->valuation_date)) {
            $valuation->status = 'scheduled';
        } else {
            $valuation->status = 'pending';
        }

        $valuation->save();

        // Notify vendor when a valuation is scheduled or rescheduled (date/time/agent changed)
        try {
            // Refresh relations after possible agent_id change
            $valuation->loadMissing(['seller', 'agent']);

            $seller = $valuation->seller;
            $hasDate = !empty($valuation->valuation_date);

            $currentDate = $valuation->valuation_date ? $valuation->valuation_date->format('Y-m-d') : null;
            $currentTime = $valuation->valuation_time ? \Carbon\Carbon::parse($valuation->valuation_time)->format('H:i') : null;
            $currentAgentId = $valuation->agent_id;

            $scheduleChanged = $hasDate && (
                $previousDate !== $currentDate ||
                $previousTime !== $currentTime ||
                (string) $previousAgentId !== (string) $currentAgentId
            );

            if ($seller && !empty($seller->email) && $scheduleChanged) {
                Mail::to($seller->email)->send(
                    new \App\Mail\ValuationScheduledNotification($valuation)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send valuation scheduled email: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.valuations.show', $valuation->id)
            ->with('success', 'Valuation schedule updated successfully.');
    }

    /**
     * Resend login credentials email to the seller for a valuation.
     * Generates a new temporary password and emails it so the seller can log in (e.g. if original email was not received).
     *
     * @param  int  $id  Valuation ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendValuationLoginCredentials($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $valuation = Valuation::with('seller')->findOrFail($id);
        $seller = $valuation->seller;
        if (!$seller || !$seller->email) {
            return redirect()->route('admin.valuations.show', $valuation->id)
                ->with('error', 'No seller or email found for this valuation.');
        }

        $password = Str::random(12);
        $seller->update(['password' => Hash::make($password)]);

        try {
            Mail::to($seller->email)->send(
                new \App\Mail\ValuationLoginCredentials($seller, $password, $valuation)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to resend valuation login credentials: ' . $e->getMessage());
            return redirect()->route('admin.valuations.show', $valuation->id)
                ->with('error', 'Login credentials could not be sent. Please check mail configuration or try again later.');
        }

        return redirect()->route('admin.valuations.show', $valuation->id)
            ->with('success', 'Login credentials have been sent to ' . $seller->email . '. The seller can now log in with the new password from that email.');
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
            'fee_percentage' => ['required', 'numeric', 'min:0', 'max:10'],
            
            // Material Information
            'heating_type' => ['nullable', 'string', 'in:gas,electric,oil,underfloor,other'],
            'boiler_age_years' => ['nullable', 'integer', 'min:0'],
            'boiler_last_serviced' => ['nullable', 'date'],
            'epc_rating' => ['nullable', 'string', 'in:A,B,C,D,E,F,G,awaiting'],
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

            // Update valuation status and save agent notes + ID visual check + fee percentage
            $valuation->update([
                'estimated_value' => $validated['estimated_value'] ?? null,
                'fee_percentage' => $validated['fee_percentage'] ?? 1.5,
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
                // Create or update instruction record with fee percentage from valuation
                $instruction = \App\Models\PropertyInstruction::updateOrCreate(
                    ['property_id' => $property->id],
                    [
                        'seller_id' => $property->seller_id,
                        'status' => 'pending',
                        'requested_by' => $user->id,
                        'requested_at' => now(),
                        'fee_percentage' => $valuation->fee_percentage ?? 1.5, // Use fee discussed during valuation
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
            'rooms.*.images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp'],
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
                $disk = $this->getStorageDisk();
                
                // Process each image - save immediately to final location, then compress in background
                foreach ($roomData['images'] as $imageIndex => $image) {
                    try {
                        // Generate final target path
                        $imageExtension = $image->getClientOriginalExtension();
                        $imageFileName = uniqid() . '.' . $imageExtension;
                        $imagePath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos') . '/' . $imageFileName;
                        
                        // Save image immediately to final location (uncompressed) so it's accessible right away
                        $targetStorage = Storage::disk($disk);
                        $targetStorage->put($imagePath, file_get_contents($image->getRealPath()));
                        
                        // Create homecheck data record with target path
                        $homecheckData = \App\Models\HomecheckData::create([
                            'property_id' => $property->id,
                            'homecheck_report_id' => $homecheckReport->id,
                            'room_name' => $roomName,
                            'image_path' => $imagePath,
                            'is_360' => $is360,
                            'moisture_reading' => $moistureReading,
                            'created_at' => now(),
                        ]);
                        
                        // Process image compression (synchronously if queue is 'sync', otherwise queued)
                        $this->processImageCompression($imagePath, $disk, $homecheckData->id, 1920, 85);
                        
                        \Log::info('Image saved and queued for compression in storeCompleteHomeCheck', [
                            'homecheck_id' => $homecheckReport->id,
                            'homecheck_data_id' => $homecheckData->id,
                            'room_name' => $roomName,
                            'image_path' => $imagePath,
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error processing image in storeCompleteHomeCheck', [
                            'homecheck_id' => $homecheckReport->id,
                            'room_name' => $roomName,
                            'error' => $e->getMessage(),
                        ]);
                    }
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

            return redirect()->route('admin.properties.show', $property->id)
                ->with('success', 'HomeCheck completed successfully! All images have been uploaded. You can now process AI analysis from the HomeCheck page.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('HomeCheck completion error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while completing the HomeCheck. Please try again.');
        }
    }

    /**
     * List all HomeCheck reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function homechecks(Request $request)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        // Build query
        $query = \App\Models\HomecheckReport::with([
            'property.seller',
            'homecheckData'
        ]);

        // For agents, filter by their assigned properties
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (empty($agentPropertyIds)) {
                // Agent has no assigned properties, return empty result
                $homechecks = \App\Models\HomecheckReport::whereRaw('1 = 0')->paginate(20);
                return view('admin.homechecks.index', compact('homechecks'));
            }
            $query->whereIn('property_id', $agentPropertyIds);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $homechecks = $query->paginate(20)->withQueryString();

        return view('admin.homechecks.index', compact('homechecks'));
    }

    /**
     * Show HomeCheck report details.
     *
     * @param  int  $id  HomeCheck Report ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showHomeCheck($id)
    {
        try {
            $user = auth()->user();
            
            if (!in_array($user->role, ['admin', 'agent'])) {
                return redirect()->route($this->getRoleDashboard($user->role))
                    ->with('error', 'You do not have permission to access this page.');
            }

            $homecheckReport = \App\Models\HomecheckReport::with([
                'property.seller',
                'scheduler',
                'completer',
                'homecheckData' => function($query) {
                    $query->orderBy('room_name')->orderBy('created_at');
                }
            ])->findOrFail($id);

            $property = $homecheckReport->property;
            
            if (!$property) {
                return redirect()->route('admin.homechecks.index')
                    ->with('error', 'Property not found for this HomeCheck.');
            }
            
            // For agents, verify they have access to this property
            if ($user->role === 'agent') {
                $agentPropertyIds = $this->getAgentPropertyIds($user->id);
                if (!in_array($property->id, $agentPropertyIds)) {
                    return redirect()->route('admin.homechecks.index')
                        ->with('error', 'You do not have permission to view this HomeCheck.');
                }
            }

            // Get HomeCheck data grouped by room
            $homecheckData = $homecheckReport->homecheckData ?? collect();
            
            // Filter out any images without IDs or paths
            $homecheckData = $homecheckData->filter(function($item) {
                return $item->id && $item->image_path;
            });
            
            $roomsData = $homecheckData->groupBy('room_name');

            // Get AI analysis if available
            $aiAnalysis = null;
            if ($homecheckReport->report_path) {
                try {
                    $reportService = new \App\Services\HomeCheckReportService();
                    // Try to get AI analysis from report or generate summary
                    $aiAnalysis = $this->getHomeCheckAnalysis($homecheckReport, $homecheckData);
                } catch (\Exception $e) {
                    \Log::warning('Failed to load AI analysis: ' . $e->getMessage());
                }
            }

            return view('admin.homechecks.show', compact('homecheckReport', 'property', 'roomsData', 'homecheckData', 'aiAnalysis'));
            
        } catch (\Exception $e) {
            \Log::error('Error loading HomeCheck show page: ' . $e->getMessage(), [
                'homecheck_id' => $id,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('admin.homechecks.index')
                ->with('error', 'An error occurred while loading the HomeCheck. Please try again.');
        }
    }

    /**
     * Get HomeCheck AI analysis summary.
     *
     * @param \App\Models\HomecheckReport $homecheckReport
     * @param \Illuminate\Support\Collection $homecheckData
     * @return array|null
     */
    private function getHomeCheckAnalysis($homecheckReport, $homecheckData)
    {
        // Extract AI analysis from homecheck data
        $analysis = [
            'overall_rating' => null,
            'rooms' => [],
        ];

        if (!$homecheckData || $homecheckData->isEmpty()) {
            return $analysis;
        }

        foreach ($homecheckData->groupBy('room_name') as $roomName => $roomImages) {
            $firstImage = $roomImages->first();
            if ($firstImage) {
                $analysis['rooms'][$roomName] = [
                    'rating' => $firstImage->ai_rating ?? null,
                    'comments' => $firstImage->ai_comments ?? null,
                    'moisture' => $firstImage->moisture_reading ?? null,
                    'image_count' => $roomImages->count(),
                ];
            }
        }

        return $analysis;
    }

    /**
     * Show edit form for HomeCheck report.
     *
     * @param  int  $id  HomeCheck Report ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editHomeCheck($id)
    {
        try {
            $user = auth()->user();
            
            if (!in_array($user->role, ['admin', 'agent'])) {
                return redirect()->route($this->getRoleDashboard($user->role))
                    ->with('error', 'You do not have permission to access this page.');
            }

            $homecheckReport = \App\Models\HomecheckReport::with([
                'property.seller',
                'scheduler',
                'completer',
                'homecheckData' => function($query) {
                    $query->orderBy('room_name')->orderBy('created_at');
                }
            ])->findOrFail($id);

            $property = $homecheckReport->property;
            
            if (!$property) {
                return redirect()->route('admin.homechecks.index')
                    ->with('error', 'Property not found for this HomeCheck.');
            }
            
            // For agents, verify they have access to this property
            if ($user->role === 'agent') {
                $agentPropertyIds = $this->getAgentPropertyIds($user->id);
                if (!in_array($property->id, $agentPropertyIds)) {
                    return redirect()->route('admin.homechecks.index')
                        ->with('error', 'You do not have permission to edit this HomeCheck.');
                }
            }

            // Get HomeCheck data grouped by room
            $homecheckData = $homecheckReport->homecheckData ?? collect();
            
            // Filter out any images without IDs or paths
            $homecheckData = $homecheckData->filter(function($item) {
                return $item->id && $item->image_path;
            });
            
            $roomsData = $homecheckData->groupBy('room_name');

            // Ensure roomsData is always a collection
            if (!$roomsData instanceof \Illuminate\Support\Collection) {
                $roomsData = collect($roomsData);
            }

            return view('admin.homechecks.edit', compact('homecheckReport', 'property', 'roomsData', 'homecheckData'));
            
        } catch (\Exception $e) {
            \Log::error('Error loading HomeCheck edit page: ' . $e->getMessage(), [
                'homecheck_id' => $id,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('admin.homechecks.index')
                ->with('error', 'An error occurred while loading the HomeCheck. Please try again.');
        }
    }

    /**
     * Update HomeCheck report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  HomeCheck Report ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateHomeCheck(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $homecheckReport = \App\Models\HomecheckReport::with(['property', 'homecheckData'])->findOrFail($id);
        $property = $homecheckReport->property;
        
        if (!$property) {
            return redirect()->route('admin.homechecks.index')
                ->with('error', 'Property not found for this HomeCheck.');
        }
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.homechecks.index')
                    ->with('error', 'You do not have permission to update this HomeCheck.');
            }
        }

        // Validate basic fields (files are validated separately)
        $validated = $request->validate([
            'scheduled_date' => 'nullable|date',
            'status' => 'required|in:pending,scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            'existing_rooms' => 'nullable|array',
            'existing_rooms.*.name' => 'nullable|string|max:255',
            'existing_rooms.*.delete_room' => 'nullable|in:0,1',
            'existing_rooms.*.delete_images' => 'nullable|array',
            'existing_rooms.*.delete_images.*' => 'nullable|integer|exists:homecheck_data,id',
            'existing_rooms.*.is_360' => 'nullable|in:0,1',
            'existing_rooms.*.moisture_reading' => 'nullable|numeric|min:0|max:100',
            'rooms' => 'nullable|array',
            'rooms.*.name' => 'required_with:rooms|string|max:255',
            'rooms.*.is_360' => 'nullable|in:0,1',
            'rooms.*.moisture_reading' => 'nullable|numeric|min:0|max:100',
        ]);

        // Validate file uploads separately (no size restriction - will be compressed in background)
        $fileValidationRules = [];
        if ($request->has('existing_rooms')) {
            foreach ($request->input('existing_rooms', []) as $roomId => $roomData) {
                $fileValidationRules["existing_rooms.{$roomId}.new_images.*"] = 'nullable|image|mimes:jpeg,jpg,png,webp';
            }
        }
        if ($request->has('rooms')) {
            foreach ($request->input('rooms', []) as $roomIndex => $roomData) {
                $fileValidationRules["rooms.{$roomIndex}.images.*"] = 'nullable|image|mimes:jpeg,jpg,png,webp';
            }
        }
        
        if (!empty($fileValidationRules)) {
            $request->validate($fileValidationRules);
        }

        try {
            \DB::beginTransaction();

            // Update basic HomeCheck details
            $homecheckReport->update([
                'scheduled_date' => $validated['scheduled_date'] ?? $homecheckReport->scheduled_date,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? $homecheckReport->notes,
            ]);

            // Determine storage disk
            $disk = $this->getStorageDisk();
            $imageOptimizer = new \App\Services\ImageOptimizationService();

            // Process existing rooms
            if ($request->has('existing_rooms') && is_array($request->input('existing_rooms'))) {
                \Log::info('Processing existing rooms', [
                    'room_count' => count($request->input('existing_rooms')),
                    'all_files' => array_keys($request->allFiles()),
                ]);
                
                foreach ($request->input('existing_rooms') as $roomId => $roomData) {
                    \Log::info('Processing room', [
                        'room_id' => $roomId,
                        'has_files' => $request->hasFile("existing_rooms.{$roomId}.new_images"),
                    ]);
                    // Check if entire room should be deleted
                    if (isset($roomData['delete_room']) && $roomData['delete_room'] == '1') {
                        // Find all images for this room
                        $firstImage = \App\Models\HomecheckData::find($roomId);
                        if ($firstImage) {
                            $roomName = $firstImage->room_name;
                            $roomImages = \App\Models\HomecheckData::where('homecheck_report_id', $homecheckReport->id)
                                ->where('room_name', $roomName)
                                ->get();
                            
                            // Delete all images for this room
                            foreach ($roomImages as $image) {
                                // Delete file from storage
                                try {
                                    if ($disk === 's3') {
                                        \Storage::disk('s3')->delete($image->image_path);
                                    } else {
                                        \Storage::disk('public')->delete($image->image_path);
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete image file: ' . $e->getMessage());
                                }
                                
                                // Delete database record
                                $image->delete();
                            }
                        }
                        continue;
                    }

                    // Update room name if changed
                    $firstImage = \App\Models\HomecheckData::find($roomId);
                    if ($firstImage) {
                        $oldRoomName = $firstImage->room_name;
                        $newRoomName = isset($roomData['name']) && !empty($roomData['name']) ? $roomData['name'] : $oldRoomName;
                        
                        if ($oldRoomName !== $newRoomName) {
                            // Update all images in this room with new name
                            \App\Models\HomecheckData::where('homecheck_report_id', $homecheckReport->id)
                                ->where('room_name', $oldRoomName)
                                ->update(['room_name' => $newRoomName]);
                        }
                        
                        // Use the (potentially updated) room name for subsequent operations
                        $roomName = $newRoomName;
                    } else {
                        // If first image not found, skip this room
                        \Log::warning('First image not found for room ID: ' . $roomId);
                        continue;
                    }

                    // Delete specified images
                    if (isset($roomData['delete_images']) && is_array($roomData['delete_images'])) {
                        foreach ($roomData['delete_images'] as $imageId) {
                            if (empty($imageId)) continue;
                            
                            $image = \App\Models\HomecheckData::find($imageId);
                            if ($image && $image->homecheck_report_id == $homecheckReport->id) {
                                // Delete file from storage
                                try {
                                    if ($disk === 's3') {
                                        \Storage::disk('s3')->delete($image->image_path);
                                    } else {
                                        \Storage::disk('public')->delete($image->image_path);
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete image file: ' . $e->getMessage());
                                }
                                
                                // Delete database record
                                $image->delete();
                            }
                        }
                    }

                    // Add new images to existing room
                    if ($request->hasFile("existing_rooms.{$roomId}.new_images")) {
                        $is360 = isset($roomData['is_360']) && $roomData['is_360'] == '1';
                        $moistureReading = isset($roomData['moisture_reading']) && $roomData['moisture_reading'] !== '' 
                            ? (float) $roomData['moisture_reading'] 
                            : null;

                        $newImages = $request->file("existing_rooms.{$roomId}.new_images");
                        
                        // Handle single file or array of files
                        if (!is_array($newImages)) {
                            $newImages = [$newImages];
                        }
                        
                        \Log::info('Processing new images for room', [
                            'room_id' => $roomId,
                            'room_name' => $roomName,
                            'image_count' => count($newImages),
                        ]);

                        foreach ($newImages as $image) {
                            if (!$image || !$image->isValid()) {
                                \Log::warning('Invalid image file in HomeCheck update', [
                                    'room_id' => $roomId,
                                    'room_name' => $roomName,
                                ]);
                                continue;
                            }
                            
                            try {
                                // Generate final target path
                                $imageExtension = $image->getClientOriginalExtension();
                                $imageFileName = uniqid() . '.' . $imageExtension;
                                $targetPath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos') . '/' . $imageFileName;
                                
                                // Save image immediately to final location (uncompressed) so it's accessible right away
                                $targetStorage = Storage::disk($disk);
                                $targetStorage->put($targetPath, file_get_contents($image->getRealPath()));
                                
                                // Create homecheck data record
                                $homecheckData = \App\Models\HomecheckData::create([
                                    'property_id' => $property->id,
                                    'homecheck_report_id' => $homecheckReport->id,
                                    'room_name' => $roomName,
                                    'image_path' => $targetPath,
                                    'is_360' => $is360,
                                    'moisture_reading' => $moistureReading,
                                ]);
                                
                                // Process image compression (synchronously if queue is 'sync', otherwise queued)
                                $this->processImageCompression($targetPath, $disk, $homecheckData->id, 1920, 85);
                                
                                \Log::info('Image saved and queued for compression', [
                                    'homecheck_id' => $homecheckReport->id,
                                    'homecheck_data_id' => $homecheckData->id,
                                    'room_name' => $roomName,
                                    'target_path' => $targetPath,
                                ]);
                            } catch (\Exception $e) {
                                \Log::error('Error uploading image to HomeCheck room', [
                                    'homecheck_id' => $homecheckReport->id,
                                    'room_name' => $roomName,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);
                            }
                        }

                        // Update moisture reading for all images in the room if provided
                        if ($moistureReading !== null) {
                            \App\Models\HomecheckData::where('homecheck_report_id', $homecheckReport->id)
                                ->where('room_name', $roomName)
                                ->update(['moisture_reading' => $moistureReading]);
                        }
                    }
                }
            }

            // Process new rooms
            if ($request->has('rooms') && is_array($request->input('rooms'))) {
                foreach ($request->input('rooms') as $roomIndex => $roomData) {
                    if (empty($roomData['name'])) continue;
                    
                    $roomName = $roomData['name'];
                    $is360 = isset($roomData['is_360']) && $roomData['is_360'] == '1';
                    $moistureReading = isset($roomData['moisture_reading']) && $roomData['moisture_reading'] !== '' 
                        ? (float) $roomData['moisture_reading'] 
                        : null;

                    if ($request->hasFile("rooms.{$roomIndex}.images")) {
                        $newImages = $request->file("rooms.{$roomIndex}.images");
                        
                        // Handle single file or array of files
                        if (!is_array($newImages)) {
                            $newImages = [$newImages];
                        }

                        foreach ($newImages as $image) {
                            if (!$image || !$image->isValid()) {
                                \Log::warning('Invalid image file in new HomeCheck room', [
                                    'room_index' => $roomIndex,
                                    'room_name' => $roomName,
                                ]);
                                continue;
                            }
                            
                            try {
                                // Generate final target path
                                $imageExtension = $image->getClientOriginalExtension();
                                $imageFileName = uniqid() . '.' . $imageExtension;
                                $imagePath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos') . '/' . $imageFileName;
                                
                                // Save image immediately to final location (uncompressed) so it's accessible right away
                                $targetStorage = \Storage::disk($disk);
                                $targetStorage->put($imagePath, file_get_contents($image->getRealPath()));
                                
                                // Create homecheck data record
                                $homecheckData = \App\Models\HomecheckData::create([
                                    'property_id' => $property->id,
                                    'homecheck_report_id' => $homecheckReport->id,
                                    'room_name' => $roomName,
                                    'image_path' => $imagePath,
                                    'is_360' => $is360,
                                    'moisture_reading' => $moistureReading,
                                ]);
                                
                                // Process image compression (synchronously if queue is 'sync', otherwise queued)
                                $this->processImageCompression($imagePath, $disk, $homecheckData->id, 1920, 85);
                                
                                \Log::info('Image saved and queued for compression in new room', [
                                    'homecheck_id' => $homecheckReport->id,
                                    'homecheck_data_id' => $homecheckData->id,
                                    'room_name' => $roomName,
                                    'image_path' => $imagePath,
                                ]);
                            } catch (\Exception $e) {
                                \Log::error('Error uploading image to new HomeCheck room', [
                                    'homecheck_id' => $homecheckReport->id,
                                    'room_name' => $roomName,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    }
                }
            }

            \DB::commit();

            // Check if AI processing was requested
            $processAI = $request->has('process_ai') && $request->input('process_ai') == '1';
            
            if ($processAI) {
                // Check if there are new images that need AI processing
                $hasNewImages = false;
                if ($request->has('existing_rooms')) {
                    foreach ($request->input('existing_rooms', []) as $roomId => $roomData) {
                        if ($request->hasFile("existing_rooms.{$roomId}.new_images")) {
                            $hasNewImages = true;
                            break;
                        }
                    }
                }
                if (!$hasNewImages && $request->has('rooms')) {
                    foreach ($request->input('rooms', []) as $roomIndex => $roomData) {
                        if ($request->hasFile("rooms.{$roomIndex}.images")) {
                            $hasNewImages = true;
                            break;
                        }
                    }
                }

                if ($hasNewImages) {
                    // Redirect to AI processing
                    return redirect()->route('admin.homechecks.process-ai', $id)
                        ->with('info', 'HomeCheck updated successfully! Processing AI analysis for new images...');
                } else {
                    // No new images, but user requested AI processing - process all images
                    return redirect()->route('admin.homechecks.process-ai', $id)
                        ->with('info', 'HomeCheck updated successfully! Processing AI analysis...');
                }
            }

            return redirect()->route('admin.homechecks.show', $id)
                ->with('success', 'HomeCheck updated successfully!');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating HomeCheck: ' . $e->getMessage(), [
                'homecheck_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('admin.homechecks.edit', $id)
                ->with('error', 'An error occurred while updating the HomeCheck: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update a single room in a HomeCheck report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  HomeCheck Report ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHomeCheckRoom(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        $homecheckReport = \App\Models\HomecheckReport::with(['property'])->findOrFail($id);
        $property = $homecheckReport->property;
        
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found for this HomeCheck.'
            ], 404);
        }
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this HomeCheck.'
                ], 403);
            }
        }

        try {
            \DB::beginTransaction();

            $disk = $this->getStorageDisk();
            $imageOptimizer = new \App\Services\ImageOptimizationService();

            // Handle modal format (room_id, room_type, room_name, images[], etc.)
            if ($request->has('room_id') && $request->has('room_type')) {
                $roomId = $request->input('room_id');
                $roomType = $request->input('room_type');
                $roomName = $request->input('room_name');
                
                if (empty($roomName)) {
                    \DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Room name is required.'
                    ], 400);
                }
                
                if ($roomType === 'existing' && $roomId) {
                    // Update existing room
                    $firstImage = \App\Models\HomecheckData::find($roomId);
                    if (!$firstImage || $firstImage->homecheck_report_id != $id) {
                        \DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Room not found.'
                        ], 404);
                    }
                    
                    $oldRoomName = $firstImage->room_name;
                    if ($oldRoomName !== $roomName) {
                        \App\Models\HomecheckData::where('homecheck_report_id', $id)
                            ->where('room_name', $oldRoomName)
                            ->update(['room_name' => $roomName]);
                    }
                    
                    // Delete specified images
                    if ($request->has('delete_images') && is_array($request->input('delete_images'))) {
                        foreach ($request->input('delete_images') as $imageId) {
                            if (empty($imageId)) continue;
                            
                            $image = \App\Models\HomecheckData::find($imageId);
                            if ($image && $image->homecheck_report_id == $id) {
                                try {
                                    if ($disk === 's3') {
                                        \Storage::disk('s3')->delete($image->image_path);
                                    } else {
                                        \Storage::disk('public')->delete($image->image_path);
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete image file: ' . $e->getMessage());
                                }
                                
                                $image->delete();
                            }
                        }
                    }
                    
                    // Add new images
                    $images = $request->file('images');
                    if ($images) {
                        $imagesIs360 = $request->input('images_is_360', []);
                        
                        if (!is_array($images)) {
                            $images = [$images];
                        }
                        
                        foreach ($images as $index => $image) {
                            if (!$image || !$image->isValid()) {
                                \Log::warning('Invalid image file in room update', [
                                    'index' => $index,
                                    'room_id' => $roomId,
                                ]);
                                continue;
                            }
                            
                            $is360 = isset($imagesIs360[$index]) && $imagesIs360[$index] == '1';
                            
                            try {
                                $imageExtension = $image->getClientOriginalExtension();
                                $imageFileName = uniqid() . '.' . $imageExtension;
                                $targetPath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos') . '/' . $imageFileName;
                                
                                // Save image immediately to final location (uncompressed) so it's accessible right away
                                $targetStorage = \Storage::disk($disk);
                                $targetStorage->put($targetPath, file_get_contents($image->getRealPath()));
                                
                                $homecheckData = \App\Models\HomecheckData::create([
                                    'property_id' => $property->id,
                                    'homecheck_report_id' => $homecheckReport->id,
                                    'room_name' => $roomName,
                                    'image_path' => $targetPath,
                                    'is_360' => $is360,
                                    'created_at' => now(),
                                ]);
                                
                                \Log::info('Image saved and queued for compression in room update', [
                                    'homecheck_data_id' => $homecheckData->id,
                                    'disk' => $disk,
                                    'target_path' => $targetPath,
                                ]);
                                
                                // Process image compression (synchronously if queue is 'sync', otherwise queued)
                                $this->processImageCompression($targetPath, $disk, $homecheckData->id, 1920, 85);
                            } catch (\Exception $e) {
                                \Log::error('Error uploading image in room update: ' . $e->getMessage(), [
                                    'trace' => $e->getTraceAsString(),
                                ]);
                            }
                        }
                    }
                } else if ($roomType === 'new') {
                    // Create new room
                    $images = $request->file('images');
                    if (!$images || (is_array($images) && count($images) == 0)) {
                        \DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Please upload at least one image for the room.'
                        ], 400);
                    }
                    
                    $imagesIs360 = $request->input('images_is_360', []);
                    
                    if (!is_array($images)) {
                        $images = [$images];
                    }
                    
                    foreach ($images as $index => $image) {
                        if (!$image || !$image->isValid()) {
                            \Log::warning('Invalid image file in new room', [
                                'index' => $index,
                            ]);
                            continue;
                        }
                        
                        $is360 = isset($imagesIs360[$index]) && $imagesIs360[$index] == '1';
                        
                        try {
                            $imageExtension = $image->getClientOriginalExtension();
                            $imageFileName = uniqid() . '.' . $imageExtension;
                            $targetPath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos') . '/' . $imageFileName;
                            
                            // Save image immediately to final location (uncompressed) so it's accessible right away
                            $targetStorage = \Storage::disk($disk);
                            $targetStorage->put($targetPath, file_get_contents($image->getRealPath()));
                            
                            $homecheckData = \App\Models\HomecheckData::create([
                                'property_id' => $property->id,
                                'homecheck_report_id' => $homecheckReport->id,
                                'room_name' => $roomName,
                                'image_path' => $targetPath,
                                'is_360' => $is360,
                                'created_at' => now(),
                            ]);
                            
                            \Log::info('Image saved and queued for compression in new room', [
                                'homecheck_data_id' => $homecheckData->id,
                                'disk' => $disk,
                                'target_path' => $targetPath,
                            ]);
                            
                                // Process image compression (synchronously if queue is 'sync', otherwise queued)
                                $this->processImageCompression($targetPath, $disk, $homecheckData->id, 1920, 85);
                        } catch (\Exception $e) {
                            \Log::error('Error uploading image in new room: ' . $e->getMessage(), [
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }
                    }
                }
                
                \DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Room updated successfully!'
                ]);
            }

            // Process existing rooms (original format)
            if ($request->has('existing_rooms') && is_array($request->input('existing_rooms'))) {
                foreach ($request->input('existing_rooms') as $roomId => $roomData) {
                    // Skip if delete_room is set
                    if (isset($roomData['delete_room']) && $roomData['delete_room'] == '1') {
                        continue;
                    }

                    // Update room name if changed
                    $firstImage = \App\Models\HomecheckData::find($roomId);
                    if ($firstImage) {
                        $oldRoomName = $firstImage->room_name;
                        $newRoomName = isset($roomData['name']) && !empty($roomData['name']) ? $roomData['name'] : $oldRoomName;
                        
                        if ($oldRoomName !== $newRoomName) {
                            \App\Models\HomecheckData::where('homecheck_report_id', $homecheckReport->id)
                                ->where('room_name', $oldRoomName)
                                ->update(['room_name' => $newRoomName]);
                        }
                        
                        $roomName = $newRoomName;
                    } else {
                        \DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Room not found.'
                        ], 404);
                    }

                    // Delete specified images
                    if (isset($roomData['delete_images']) && is_array($roomData['delete_images'])) {
                        foreach ($roomData['delete_images'] as $imageId) {
                            if (empty($imageId)) continue;
                            
                            $image = \App\Models\HomecheckData::find($imageId);
                            if ($image && $image->homecheck_report_id == $homecheckReport->id) {
                                try {
                                    if ($disk === 's3') {
                                        \Storage::disk('s3')->delete($image->image_path);
                                    } else {
                                        \Storage::disk('public')->delete($image->image_path);
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete image file: ' . $e->getMessage());
                                }
                                
                                $image->delete();
                            }
                        }
                    }

                    // Add new images
                    if ($request->hasFile("existing_rooms.{$roomId}.new_images")) {
                        $is360 = isset($roomData['is_360']) && $roomData['is_360'] == '1';
                        $moistureReading = isset($roomData['moisture_reading']) && $roomData['moisture_reading'] !== '' 
                            ? (float) $roomData['moisture_reading'] 
                            : null;

                        $newImages = $request->file("existing_rooms.{$roomId}.new_images");
                        
                        if (!is_array($newImages)) {
                            $newImages = [$newImages];
                        }

                        foreach ($newImages as $image) {
                            if (!$image || !$image->isValid()) continue;
                            
                            try {
                                $imageExtension = $image->getClientOriginalExtension();
                                $imageFileName = uniqid() . '.' . $imageExtension;
                                $targetPath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos') . '/' . $imageFileName;
                                
                                // Save image immediately to final location (uncompressed) so it's accessible right away
                                $targetStorage = \Storage::disk($disk);
                                $targetStorage->put($targetPath, file_get_contents($image->getRealPath()));
                                
                                $homecheckData = \App\Models\HomecheckData::create([
                                    'property_id' => $property->id,
                                    'homecheck_report_id' => $homecheckReport->id,
                                    'room_name' => $roomName,
                                    'image_path' => $targetPath,
                                    'is_360' => $is360,
                                    'moisture_reading' => $moistureReading,
                                ]);
                                
                                // Process image compression (synchronously if queue is 'sync', otherwise queued)
                                $this->processImageCompression($targetPath, $disk, $homecheckData->id, 1920, 85);
                            } catch (\Exception $e) {
                                \Log::error('Error uploading image: ' . $e->getMessage());
                            }
                        }
                    }

                    // Update moisture reading for all images in the room if provided
                    if (isset($roomData['moisture_reading']) && $roomData['moisture_reading'] !== '') {
                        $moistureReading = (float) $roomData['moisture_reading'];
                        \App\Models\HomecheckData::where('homecheck_report_id', $homecheckReport->id)
                            ->where('room_name', $roomName)
                            ->update(['moisture_reading' => $moistureReading]);
                    }
                }
            }

            // Process new rooms
            if ($request->has('rooms') && is_array($request->input('rooms'))) {
                foreach ($request->input('rooms') as $roomIndex => $roomData) {
                    if (empty($roomData['name'])) continue;
                    
                    $roomName = $roomData['name'];
                    $is360 = isset($roomData['is_360']) && $roomData['is_360'] == '1';
                    $moistureReading = isset($roomData['moisture_reading']) && $roomData['moisture_reading'] !== '' 
                        ? (float) $roomData['moisture_reading'] 
                        : null;

                    if ($request->hasFile("rooms.{$roomIndex}.images")) {
                        $newImages = $request->file("rooms.{$roomIndex}.images");
                        
                        if (!is_array($newImages)) {
                            $newImages = [$newImages];
                        }

                        foreach ($newImages as $image) {
                            if (!$image || !$image->isValid()) continue;
                            
                            try {
                                $imageExtension = $image->getClientOriginalExtension();
                                $imageFileName = uniqid() . '.' . $imageExtension;
                                $targetPath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/' . ($is360 ? '360' : 'photos') . '/' . $imageFileName;
                                
                                // Save image immediately to final location (uncompressed) so it's accessible right away
                                $targetStorage = \Storage::disk($disk);
                                $targetStorage->put($targetPath, file_get_contents($image->getRealPath()));
                                
                                $homecheckData = \App\Models\HomecheckData::create([
                                    'property_id' => $property->id,
                                    'homecheck_report_id' => $homecheckReport->id,
                                    'room_name' => $roomName,
                                    'image_path' => $targetPath,
                                    'is_360' => $is360,
                                    'moisture_reading' => $moistureReading,
                                ]);
                                
                                // Process image compression (synchronously if queue is 'sync', otherwise queued)
                                $this->processImageCompression($targetPath, $disk, $homecheckData->id, 1920, 85);
                            } catch (\Exception $e) {
                                \Log::error('Error uploading image: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully!'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating HomeCheck room: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the room: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process AI analysis for a HomeCheck report.
     *
     * @param  int  $id  HomeCheck Report ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processHomeCheckAI($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $homecheckReport = \App\Models\HomecheckReport::with(['property', 'homecheckData'])->findOrFail($id);
        $property = $homecheckReport->property;
        
        if (!$property) {
            return redirect()->route('admin.homechecks.index')
                ->with('error', 'Property not found for this HomeCheck.');
        }
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return redirect()->route('admin.homechecks.index')
                    ->with('error', 'You do not have permission to process AI analysis for this HomeCheck.');
            }
        }

        // Check if HomeCheck has images
        if ($homecheckReport->homecheckData->isEmpty()) {
            return redirect()->route('admin.homechecks.show', $id)
                ->with('error', 'No images found. Please upload images before processing AI analysis.');
        }

        try {
            // Process AI analysis
            $reportService = new \App\Services\HomeCheckReportService();
            
            // Get all HomeCheck data for this report
            $homecheckData = \App\Models\HomecheckData::where('homecheck_report_id', $homecheckReport->id)
                ->orWhere('property_id', $property->id)
                ->orderBy('room_name')
                ->orderBy('created_at')
                ->get();

            // Generate AI analysis
            $aiAnalysis = $reportService->generateAIAnalysis($homecheckData, $property);

            // Update AI analysis in HomecheckData records (per image)
            foreach ($homecheckData as $data) {
                $roomName = $data->room_name;
                if (isset($aiAnalysis['rooms'][$roomName])) {
                    $roomAnalysis = $aiAnalysis['rooms'][$roomName];
                    
                    // Update each image in the room with AI analysis
                    $data->update([
                        'ai_rating' => $roomAnalysis['rating'] ?? null,
                        'ai_comments' => $roomAnalysis['comments'] ?? null,
                        // Keep existing moisture reading if set
                    ]);
                }
            }

            // Generate and save the full report
            $reportGenerated = $reportService->processAndGenerateReport($homecheckReport);
            
            if ($reportGenerated) {
                \Log::info('AI analysis processed successfully for HomeCheck ID: ' . $homecheckReport->id);
                return redirect()->route('admin.homechecks.show', $id)
                    ->with('success', 'AI analysis completed successfully! Analysis has been added to each image.');
            } else {
                \Log::warning('AI report generation failed for HomeCheck ID: ' . $homecheckReport->id);
                return redirect()->route('admin.homechecks.show', $id)
                    ->with('warning', 'AI analysis was processed but report generation failed. Analysis has been saved to images.');
            }

        } catch (\Exception $e) {
            \Log::error('Error processing AI analysis: ' . $e->getMessage());
            return redirect()->route('admin.homechecks.show', $id)
                ->with('error', 'An error occurred while processing AI analysis: ' . $e->getMessage());
        }
    }

    /**
     * Get HomeCheck image with proper CORS headers for 360° viewer (Admin/Agent).
     *
     * @param  int  $id  HomecheckData ID
     * @return \Illuminate\Http\Response
     */
    public function getHomecheckImage($id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Unauthorized access to this image.');
        }
        
        // Get the homecheck data
        $homecheckData = \App\Models\HomecheckData::with('property')->findOrFail($id);
        $property = $homecheckData->property;
        
        if (!$property) {
            abort(404, 'Property not found for this image.');
        }
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                abort(403, 'Unauthorized access to this image.');
            }
        }
        
        // Determine storage disk - check both S3 and public to find where image actually exists
        $s3Configured = !empty(config('filesystems.disks.s3.key')) && 
                       !empty(config('filesystems.disks.s3.secret')) && 
                       !empty(config('filesystems.disks.s3.bucket'));
        
        $disk = null;
        $storage = null;
        
        // First, try S3 if configured
        if ($s3Configured) {
            $s3Storage = \Illuminate\Support\Facades\Storage::disk('s3');
            if ($s3Storage->exists($homecheckData->image_path)) {
                $disk = 's3';
                $storage = $s3Storage;
            }
        }
        
        // If not found in S3, try public storage
        if (!$storage) {
            $publicStorage = \Illuminate\Support\Facades\Storage::disk('public');
            if ($publicStorage->exists($homecheckData->image_path)) {
                $disk = 'public';
                $storage = $publicStorage;
            }
        }
        
        // If still not found, abort with detailed error
        if (!$storage || !$storage->exists($homecheckData->image_path)) {
            $s3Exists = false;
            $publicExists = false;
            
            if ($s3Configured) {
                try {
                    $s3Exists = \Storage::disk('s3')->exists($homecheckData->image_path);
                } catch (\Exception $e) {
                    \Log::warning('Error checking S3 existence', ['error' => $e->getMessage()]);
                }
            }
            
            try {
                $publicExists = \Storage::disk('public')->exists($homecheckData->image_path);
            } catch (\Exception $e) {
                \Log::warning('Error checking public existence', ['error' => $e->getMessage()]);
            }
            
            \Log::error('HomeCheck image not found in any storage', [
                'image_id' => $id,
                'image_path' => $homecheckData->image_path,
                's3_configured' => $s3Configured,
                's3_exists' => $s3Exists,
                'public_exists' => $publicExists,
                'property_id' => $property->id ?? null,
            ]);
            
            abort(404, 'Image file not found in storage. Path: ' . $homecheckData->image_path);
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
    }

    /**
     * Get images for a specific room (AJAX).
     *
     * @param  int  $roomId  HomecheckData ID (first image of the room)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHomecheckRoomImages($roomId)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        try {
            $firstImage = \App\Models\HomecheckData::with('property')->findOrFail($roomId);
            $property = $firstImage->property;
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found.'
                ], 404);
            }
            
            // For agents, verify they have access to this property
            if ($user->role === 'agent') {
                $agentPropertyIds = $this->getAgentPropertyIds($user->id);
                if (!in_array($property->id, $agentPropertyIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access.'
                    ], 403);
                }
            }
            
            // Get all images for this room
            $roomImages = \App\Models\HomecheckData::where('homecheck_report_id', $firstImage->homecheck_report_id)
                ->where('room_name', $firstImage->room_name)
                ->orderBy('created_at')
                ->get();
            
            $images = [];
            foreach ($roomImages as $image) {
                try {
                    $imageUrl = route('admin.homecheck.image', ['id' => $image->id]);
                } catch (\Exception $e) {
                    $imageUrl = url('/admin/homecheck-image/' . $image->id);
                }
                
                $images[] = [
                    'id' => $image->id,
                    'url' => $imageUrl,
                    'is_360' => $image->is_360 ?? false,
                ];
            }
            
            return response()->json([
                'success' => true,
                'images' => $images
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading room images: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading images.'
            ], 500);
        }
    }

    /**
     * Get AI summary for a specific room (AJAX).
     *
     * @param  int  $roomId  HomecheckData ID (first image of the room)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHomecheckRoomAI($roomId)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        try {
            $firstImage = \App\Models\HomecheckData::with('property')->findOrFail($roomId);
            $property = $firstImage->property;
            
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found.'
                ], 404);
            }
            
            // For agents, verify they have access to this property
            if ($user->role === 'agent') {
                $agentPropertyIds = $this->getAgentPropertyIds($user->id);
                if (!in_array($property->id, $agentPropertyIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access.'
                    ], 403);
                }
            }
            
            return response()->json([
                'success' => true,
                'rating' => $firstImage->ai_rating,
                'comments' => $firstImage->ai_comments,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading room AI: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading AI summary.'
            ], 500);
        }
    }

    /**
     * Delete a room from a HomeCheck report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  HomeCheck Report ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteHomeCheckRoom(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'agent'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        $homecheckReport = \App\Models\HomecheckReport::with(['property'])->findOrFail($id);
        $property = $homecheckReport->property;
        
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found for this HomeCheck.'
            ], 404);
        }
        
        // For agents, verify they have access to this property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($property->id, $agentPropertyIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this room.'
                ], 403);
            }
        }

        $roomId = $request->input('room_id');
        
        if (!$roomId) {
            return response()->json([
                'success' => false,
                'message' => 'Room ID is required.'
            ], 400);
        }

        try {
            \DB::beginTransaction();

            // Get the first image to find room name
            $firstImage = \App\Models\HomecheckData::find($roomId);
            
            if (!$firstImage || $firstImage->homecheck_report_id != $id) {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found or does not belong to this HomeCheck.'
                ], 404);
            }

            $roomName = $firstImage->room_name;
            
            // Get all images for this room
            $roomImages = \App\Models\HomecheckData::where('homecheck_report_id', $id)
                ->where('room_name', $roomName)
                ->get();

            // Determine storage disk
            $disk = $this->getStorageDisk();
            $storage = \Illuminate\Support\Facades\Storage::disk($disk);

            // Delete image files
            foreach ($roomImages as $image) {
                if ($image->image_path && $storage->exists($image->image_path)) {
                    try {
                        $storage->delete($image->image_path);
                    } catch (\Exception $e) {
                        \Log::warning('Error deleting image file: ' . $e->getMessage(), [
                            'image_path' => $image->image_path
                        ]);
                    }
                }
            }

            // Delete database records
            \App\Models\HomecheckData::where('homecheck_report_id', $id)
                ->where('room_name', $roomName)
                ->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully.'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting HomeCheck room: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the room: ' . $e->getMessage()
            ], 500);
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
            
            // Return file download with caching headers
            $fileSize = filesize($fullPath);
            $lastModified = filemtime($fullPath);
            $etag = md5($rtdfFilePath . $lastModified . $fileSize);
            
            // Check if client has a cached version (304 Not Modified)
            $request = request();
            $ifNoneMatch = $request->header('If-None-Match');
            
            if ($ifNoneMatch && $ifNoneMatch === '"' . $etag . '"') {
                return response('', 304)
                    ->header('ETag', '"' . $etag . '"')
                    ->header('Cache-Control', 'private, max-age=3600'); // 1 hour for dynamically generated files
            }
            
            return response()->download($fullPath, 'property_' . $property->id . '.txt', [
                'Content-Type' => 'text/plain',
                'ETag' => '"' . $etag . '"',
                'Cache-Control' => 'private, max-age=3600', // 1 hour cache for dynamically generated files
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

            // Get file contents and metadata
            $fileContents = \Storage::disk($disk)->get($document->file_path);
            $mimeType = $document->mime_type ?: \Storage::disk($disk)->mimeType($document->file_path) ?: 'application/octet-stream';
            $lastModified = \Storage::disk($disk)->lastModified($document->file_path);
            $fileSize = strlen($fileContents);
            
            // Generate ETag for caching
            $etag = md5($document->file_path . $lastModified . $fileSize);
            
            // Check if client has a cached version (304 Not Modified)
            $request = request();
            $ifNoneMatch = $request->header('If-None-Match');
            $ifModifiedSince = $request->header('If-Modified-Since');
            
            if ($ifNoneMatch && $ifNoneMatch === '"' . $etag . '"') {
                return response('', 304)
                    ->header('ETag', '"' . $etag . '"')
                    ->header('Cache-Control', 'private, max-age=86400'); // 1 day for private documents
            }
            
            $lastModifiedDate = \Carbon\Carbon::createFromTimestamp($lastModified);
            if ($ifModifiedSince && $lastModifiedDate->lte(\Carbon\Carbon::parse($ifModifiedSince))) {
                return response('', 304)
                    ->header('Last-Modified', $lastModifiedDate->toRfc7231String())
                    ->header('ETag', '"' . $etag . '"')
                    ->header('Cache-Control', 'private, max-age=86400');
            }

            // Return file with appropriate headers (private cache for sensitive documents)
            return response($fileContents, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', $fileSize)
                ->header('Content-Disposition', 'inline; filename="' . ($document->file_name ?? 'document') . '"')
                ->header('ETag', '"' . $etag . '"')
                ->header('Last-Modified', $lastModifiedDate->toRfc7231String())
                ->header('Cache-Control', 'private, max-age=86400') // 1 day cache for private documents
                ->header('Expires', now()->addDay()->toRfc7231String());
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
     * List all viewings for admin/agent to assign.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewings()
    {
        $user = auth()->user();
        
        // Only admins and agents can access this
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        // For agents, only show viewings for their assigned properties
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            
            if (empty($agentPropertyIds)) {
                $viewings = collect([])->paginate(20);
            } else {
                $viewings = Viewing::whereIn('property_id', $agentPropertyIds)
                    ->with(['buyer', 'property.seller', 'pva'])
                    ->orderBy('viewing_date', 'asc')
                    ->paginate(20);
            }
        } else {
            // Admin sees all viewings
            $viewings = Viewing::with(['buyer', 'property.seller', 'pva'])
                ->orderBy('viewing_date', 'asc')
                ->paginate(20);
        }

        return view('admin.viewings.index', compact('viewings'));
    }

    /**
     * Show form to assign a viewing to a PVA (Admin or Agent).
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAssignViewing($id)
    {
        $user = auth()->user();
        
        // Only admins and agents can access this
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $viewing = Viewing::with(['buyer', 'property.seller', 'pva'])->findOrFail($id);
        
        // For agents, verify they have access to this viewing's property
        if ($user->role === 'agent') {
            $agentPropertyIds = $this->getAgentPropertyIds($user->id);
            if (!in_array($viewing->property_id, $agentPropertyIds)) {
                return redirect()->route('admin.viewings.index')
                    ->with('error', 'You do not have permission to assign this viewing.');
            }
        }
        
        $pvas = User::where('role', 'pva')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.viewings.assign', compact('viewing', 'pvas'));
    }

    /**
     * Assign a viewing to a PVA (Admin or Agent).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignViewing(Request $request, $id)
    {
        $user = auth()->user();
        
        // Only admins and agents can access this
        if (!in_array($user->role, ['admin', 'agent'])) {
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
            
            // For agents, verify they have access to this viewing's property
            if ($user->role === 'agent') {
                $agentPropertyIds = $this->getAgentPropertyIds($user->id);
                if (!in_array($viewing->property_id, $agentPropertyIds)) {
                    return redirect()->route('admin.viewings.index')
                        ->with('error', 'You do not have permission to assign this viewing.');
                }
            }
            
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

    /**
     * Show notifications page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function notifications()
    {
        $user = auth()->user();
        
        // Role check
        if (!in_array($user->role, ['admin', 'agent'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        // Build notifications array
        $notifications = [];
        
        // Get recent offers
        $recentOffers = \App\Models\Offer::with(['property', 'buyer'])
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($recentOffers as $offer) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'ℹ',
                'message' => "New offer of £" . number_format($offer->offer_amount, 2) . " on " . ($offer->property->address ?? 'property') . " from " . ($offer->buyer->name ?? 'buyer'),
                'date' => $offer->created_at,
                'link' => route('admin.properties.show', $offer->property_id),
            ];
        }

        // Get recent viewing requests
        $recentViewings = \App\Models\Viewing::with(['property', 'buyer'])
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentViewings as $viewing) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'ℹ',
                'message' => "New viewing request for " . ($viewing->property->address ?? 'property') . " from " . ($viewing->buyer->name ?? 'buyer'),
                'date' => $viewing->created_at,
                'link' => route('admin.viewings.index'),
            ];
        }

        // Sort notifications by date (newest first)
        usort($notifications, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Check if S3 is configured.
     *
     * @return bool
     */
    private function isS3Configured(): bool
    {
        return !empty(config('filesystems.disks.s3.key')) && 
               !empty(config('filesystems.disks.s3.secret')) && 
               !empty(config('filesystems.disks.s3.bucket'));
    }

    /**
     * Get the storage disk to use (S3 if configured, otherwise public).
     *
     * @return string
     */
    private function getStorageDisk(): string
    {
        return $this->isS3Configured() ? 's3' : 'public';
    }

    /**
     * Dispatch image compression job or process synchronously based on queue connection.
     * 
     * If QUEUE_CONNECTION is 'sync', the image will be processed immediately.
     * Otherwise, it will be queued and requires a queue worker (php artisan queue:work).
     * 
     * @param string $imagePath Path to the image in storage
     * @param string $disk Storage disk (s3 or public)
     * @param int $homecheckDataId HomecheckData ID
     * @param int $maxWidth Maximum width for compression
     * @param int $quality JPEG quality 0-100
     * @return void
     */
    private function processImageCompression($imagePath, $disk, $homecheckDataId, $maxWidth = 1920, $quality = 85): void
    {
        // If queue connection is 'sync', process immediately
        // Otherwise, queue the job (requires php artisan queue:work to be running)
        if (config('queue.default') === 'sync') {
            // Process synchronously - image will be compressed immediately
            try {
                $job = new \App\Jobs\CompressAndStoreImage($imagePath, $disk, $homecheckDataId, $maxWidth, $quality);
                $job->handle();
                \Log::info('Image compressed synchronously', [
                    'homecheck_data_id' => $homecheckDataId,
                    'image_path' => $imagePath,
                ]);
            } catch (\Exception $e) {
                \Log::error('Synchronous image compression failed', [
                    'homecheck_data_id' => $homecheckDataId,
                    'image_path' => $imagePath,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Queue the job for background processing
            // NOTE: Requires 'php artisan queue:work' to be running to process queued jobs
            \App\Jobs\CompressAndStoreImage::dispatch($imagePath, $disk, $homecheckDataId, $maxWidth, $quality);
            \Log::info('Image compression queued', [
                'homecheck_data_id' => $homecheckDataId,
                'image_path' => $imagePath,
                'queue_connection' => config('queue.default'),
            ]);
        }
    }
}
