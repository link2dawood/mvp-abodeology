<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PVAController extends Controller
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
     * Show the PVA dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Role check as fallback (middleware should handle this, but extra protection)
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access the PVA dashboard.');
        }
        
        // PVA name from authenticated user
        $pvaName = $user->name;
        
        // ============================================
        // FETCH REAL DATA FROM DATABASE
        // ============================================
        
        // 1. UPCOMING VIEWINGS - Assigned to this PVA (excluding today's which are shown separately)
        $upcomingViewings = \App\Models\Viewing::where('pva_id', $user->id)
            ->where('viewing_date', '>', today()->endOfDay())
            ->where('status', '!=', 'cancelled')
            ->with(['buyer', 'property'])
            ->orderBy('viewing_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function($viewing) {
                // Determine access type
                $accessType = 'Keys needed';
                if ($viewing->property && $viewing->property->with_keys) {
                    $accessType = 'Keys available';
                } elseif ($viewing->access_instructions) {
                    if (stripos($viewing->access_instructions, 'vendor') !== false || stripos($viewing->access_instructions, 'seller') !== false) {
                        $accessType = 'Vendor will open';
                    } elseif (stripos($viewing->access_instructions, 'agent') !== false) {
                        $accessType = 'Agent will open';
                    }
                }
                
                return [
                    'id' => $viewing->id,
                    'date' => $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y') : 'N/A',
                    'time' => $viewing->viewing_date ? $viewing->viewing_date->format('g:i A') : 'N/A',
                    'property' => $viewing->property->address ?? 'N/A',
                    'buyer' => $viewing->buyer->name ?? 'N/A',
                    'buyer_phone' => $viewing->buyer->phone ?? 'N/A',
                    'access_type' => $accessType,
                    'special_instructions' => $viewing->special_instructions ?? null,
                    'status' => ucfirst($viewing->status ?? 'Scheduled'),
                ];
            });
        
        // 2. TODAY'S TASKS - Viewings scheduled for today
        $todaysTasks = \App\Models\Viewing::where('pva_id', $user->id)
            ->whereDate('viewing_date', today())
            ->where('status', '!=', 'cancelled')
            ->with(['buyer', 'property'])
            ->orderBy('viewing_date', 'asc')
            ->get()
            ->map(function($viewing) {
                // Determine access type
                $accessType = 'Keys needed';
                if ($viewing->property && $viewing->property->with_keys) {
                    $accessType = 'Keys available';
                } elseif ($viewing->access_instructions) {
                    if (stripos($viewing->access_instructions, 'vendor') !== false || stripos($viewing->access_instructions, 'seller') !== false) {
                        $accessType = 'Vendor will open';
                    } elseif (stripos($viewing->access_instructions, 'agent') !== false) {
                        $accessType = 'Agent will open';
                    }
                }
                
                return [
                    'id' => $viewing->id,
                    'time' => $viewing->viewing_date ? $viewing->viewing_date->format('g:i A') : 'N/A',
                    'property' => $viewing->property->address ?? 'N/A',
                    'buyer' => $viewing->buyer->name ?? 'N/A',
                    'buyer_phone' => $viewing->buyer->phone ?? 'N/A',
                    'access_type' => $accessType,
                    'special_instructions' => $viewing->special_instructions ?? null,
                    'access_instructions' => $viewing->access_instructions ?? null,
                    'status' => $viewing->status,
                    'arrival_time' => $viewing->arrival_time,
                ];
            });
        
        // 3. COMPLETED VIEWINGS - With feedback status
        $completedViewings = \App\Models\Viewing::where('pva_id', $user->id)
            ->where('status', 'completed')
            ->with(['buyer', 'property', 'feedback'])
            ->orderBy('viewing_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($viewing) {
                return [
                    'id' => $viewing->id,
                    'date' => $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y') : 'N/A',
                    'property' => $viewing->property->address ?? 'N/A',
                    'buyer' => $viewing->buyer->name ?? 'N/A',
                    'report' => $viewing->feedback ? true : false,
                    'feedback_id' => $viewing->feedback ? $viewing->feedback->id : null,
                ];
            });
        
        // 4. UNASSIGNED VIEWINGS - Available for PVA to claim
        $unassignedViewings = \App\Models\Viewing::where('pva_id', null)
            ->where('viewing_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['buyer', 'property'])
            ->orderBy('viewing_date', 'asc')
            ->limit(5)
            ->get();
        
        // 5. ASSIGNED VALUATIONS - Valuations assigned to this PVA
        $assignedValuations = \App\Models\Valuation::where('agent_id', $user->id)
            ->with(['seller'])
            ->orderBy('valuation_date', 'asc')
            ->get()
            ->map(function($valuation) {
                return [
                    'id' => $valuation->id,
                    'property_address' => $valuation->property_address,
                    'postcode' => $valuation->postcode,
                    'valuation_date' => $valuation->valuation_date ? $valuation->valuation_date->format('M j, Y') : 'N/A',
                    'valuation_time' => $valuation->valuation_time ? \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') : 'N/A',
                    'status' => ucfirst($valuation->status ?? 'Pending'),
                    'seller_name' => $valuation->seller->name ?? 'N/A',
                    'seller_email' => $valuation->seller->email ?? 'N/A',
                    'seller_phone' => $valuation->seller->phone ?? 'N/A',
                ];
            });
        
        // 6. STATISTICS
        $jobsCompletedCount = \App\Models\Viewing::where('pva_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        $pvaAreas = 'London, Surrey'; // Could be stored in user profile in future

        return view('pva.dashboard', compact(
            'pvaName',
            'upcomingViewings',
            'todaysTasks',
            'completedViewings',
            'unassignedViewings',
            'assignedValuations',
            'pvaAreas',
            'jobsCompletedCount'
        ));
    }

    /**
     * List all viewings assigned to this PVA.
     * This serves as the viewing scheduling endpoint.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewings()
    {
        $user = auth()->user();
        
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        // Assigned viewings
        $assignedViewings = \App\Models\Viewing::where('pva_id', $user->id)
            ->with(['buyer', 'property', 'feedback'])
            ->orderBy('viewing_date', 'asc')
            ->get();
        
        // Unassigned viewings (available to claim)
        $unassignedViewings = \App\Models\Viewing::where('pva_id', null)
            ->where('viewing_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['buyer', 'property'])
            ->orderBy('viewing_date', 'asc')
            ->get();

        return view('pva.viewings.index', compact('assignedViewings', 'unassignedViewings'));
    }

    /**
     * Show a specific viewing.
     *
     * @param  int  $id  Viewing ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showViewing($id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $viewing = \App\Models\Viewing::with(['buyer', 'property.seller', 'property.photos', 'feedback'])->findOrFail($id);

        // Check if PVA can view this viewing (allow unassigned viewings to be viewed for claiming)
        if ($viewing->pva_id !== null && $viewing->pva_id !== $user->id) {
            return redirect()->route('pva.viewings.index')
                ->with('error', 'You do not have permission to view this viewing.');
        }

        // Determine access type
        $accessType = 'Keys needed';
        if ($viewing->property && $viewing->property->with_keys) {
            $accessType = 'Keys available';
        } elseif ($viewing->access_instructions) {
            if (stripos($viewing->access_instructions, 'vendor') !== false || stripos($viewing->access_instructions, 'seller') !== false) {
                $accessType = 'Vendor will open';
            } elseif (stripos($viewing->access_instructions, 'agent') !== false) {
                $accessType = 'Agent will open';
            }
        }

        return view('pva.viewings.show', compact('viewing', 'accessType'));
    }

    /**
     * Start viewing - Mark arrival time.
     *
     * @param  int  $id  Viewing ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startViewing($id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $viewing = \App\Models\Viewing::with(['buyer', 'property'])->findOrFail($id);

        // Check if PVA is assigned to this viewing
        if ($viewing->pva_id !== $user->id) {
            return back()->with('error', 'You are not assigned to this viewing.');
        }

        // Check if viewing hasn't started yet
        if ($viewing->arrival_time !== null) {
            return back()->with('info', 'Viewing has already been started.');
        }

        try {
            $viewing->update([
                'arrival_time' => now(),
            ]);

            // Log activity
            \Log::info('PVA started viewing', [
                'viewing_id' => $viewing->id,
                'pva_id' => $user->id,
                'arrival_time' => now(),
            ]);

            return redirect()->route('pva.viewings.show', $viewing->id)
                ->with('success', 'Viewing started! Arrival time has been logged.');

        } catch (\Exception $e) {
            \Log::error('Error starting viewing: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while starting the viewing. Please try again.');
        }
    }

    /**
     * Confirm a viewing (PVA confirms they will attend).
     *
     * @param  int  $id  Viewing ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmViewing($id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $viewing = \App\Models\Viewing::with(['buyer', 'property'])->findOrFail($id);

        // Check if viewing is already confirmed
        $wasPending = $viewing->status === 'pending';
        
        // Assign PVA to viewing if not already assigned
        if ($viewing->pva_id === null) {
            $viewing->update([
                'pva_id' => $user->id,
                'status' => 'scheduled',
            ]);
        } elseif ($viewing->pva_id !== $user->id) {
            return back()->with('error', 'This viewing is already assigned to another PVA.');
        } else {
            // PVA already assigned, just confirm the viewing
            if ($viewing->status !== 'scheduled') {
                $viewing->update([
                    'status' => 'scheduled',
                ]);
            }
        }

        // Reload viewing with relationships
        $viewing->refresh();
        $viewing->load(['buyer', 'property.seller', 'pva']);

        // Send confirmation emails to buyer and seller when viewing is confirmed
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
        }

        return redirect()->route('pva.viewings.show', $viewing->id)
            ->with('success', 'Viewing confirmed! The buyer has been notified.');
    }

    /**
     * Show viewing feedback form.
     *
     * @param  int  $id  Viewing ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showFeedback($id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        $viewing = \App\Models\Viewing::with(['buyer', 'property', 'feedback'])->findOrFail($id);

        // Check if PVA is assigned to this viewing
        if ($viewing->pva_id !== $user->id) {
            return redirect()->route('pva.viewings.index')
                ->with('error', 'You do not have permission to submit feedback for this viewing.');
        }

        return view('pva.viewings.feedback', compact('viewing'));
    }

    /**
     * Store viewing feedback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Viewing ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFeedback(Request $request, $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $viewing = \App\Models\Viewing::with(['buyer', 'property'])->findOrFail($id);

        // Check if PVA is assigned to this viewing
        if ($viewing->pva_id !== $user->id) {
            return back()->with('error', 'You do not have permission to submit feedback for this viewing.');
        }

        $validated = $request->validate([
            'buyer_interest_level' => ['required', 'string', 'in:not_interested,maybe,interested,very_interested'],
            'buyer_feedback' => ['nullable', 'string', 'max:2000'],
            'buyer_questions' => ['nullable', 'string', 'max:2000'],
            'property_condition' => ['nullable', 'string', 'in:excellent,good,fair,poor'],
            'buyer_notes' => ['nullable', 'string', 'max:2000'],
            'pva_notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'buyer_interest_level.required' => 'Please select the buyer\'s interest level.',
            'buyer_interest_level.in' => 'Invalid interest level selected.',
        ]);

        try {
            \DB::beginTransaction();

            // Create or update viewing feedback
            $feedback = \App\Models\ViewingFeedback::updateOrCreate(
                ['viewing_id' => $viewing->id],
                [
                    'buyer_interest_level' => $validated['buyer_interest_level'],
                    'buyer_feedback' => $validated['buyer_feedback'] ?? null,
                    'buyer_questions' => $validated['buyer_questions'] ?? null,
                    'property_condition' => $validated['property_condition'] ?? null,
                    'buyer_notes' => $validated['buyer_notes'] ?? null,
                    'pva_notes' => $validated['pva_notes'] ?? null,
                ]
            );

            // Update viewing status to completed
            $viewing->update([
                'status' => 'completed',
            ]);

            \DB::commit();

            // Trigger Keap automation for PVA feedback submitted
            try {
                $keapService = new \App\Services\KeapService();
                $keapService->triggerPvaFeedbackSubmitted($feedback);
            } catch (\Exception $e) {
                \Log::error('Keap trigger error for PVA feedback: ' . $e->getMessage());
            }

            return redirect()->route('pva.viewings.show', $viewing->id)
                ->with('success', 'Viewing feedback submitted successfully!');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Viewing feedback error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while submitting the feedback. Please try again.');
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
     * Show notifications page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function notifications()
    {
        $user = auth()->user();
        
        // Role check
        if ($user->role !== 'pva') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        // Get assigned viewings
        $viewings = \App\Models\Viewing::where('pva_id', $user->id)
            ->with(['property', 'buyer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Build notifications array
        $notifications = [];
        
        // Add notifications for new viewing assignments
        foreach ($viewings as $viewing) {
            if ($viewing->created_at->isAfter(now()->subDays(30))) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'ℹ',
                    'message' => "New viewing assigned: " . ($viewing->property->address ?? 'property') . " on " . ($viewing->viewing_date ? $viewing->viewing_date->format('M j, Y') : 'TBD'),
                    'date' => $viewing->created_at,
                    'link' => route('pva.viewings.show', $viewing->id),
                ];
            }
        }

        // Sort notifications by date (newest first)
        usort($notifications, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('pva.notifications', compact('notifications'));
    }
}
