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
        
        // In a real application, you would fetch this from your database
        // For now, we'll use sample data
        $upcomingViewings = [];
        $todaysTasks = [];
        $completedViewings = [];
        $pvaAreas = 'London, Surrey';
        $jobsCompletedCount = 0;

        return view('pva.dashboard', compact(
            'pvaName',
            'upcomingViewings',
            'todaysTasks',
            'completedViewings',
            'pvaAreas',
            'jobsCompletedCount'
        ));
    }

    /**
     * List all viewings assigned to this PVA.
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

        $viewings = \App\Models\Viewing::where('pva_id', $user->id)
            ->orWhere('pva_id', null) // Also show unassigned viewings
            ->with(['buyer', 'property.seller'])
            ->orderBy('viewing_date', 'asc')
            ->paginate(15);

        return view('pva.viewings.index', compact('viewings'));
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

        $viewing = \App\Models\Viewing::with(['buyer', 'property.seller', 'feedback'])->findOrFail($id);

        // Check if PVA can view this viewing
        if ($viewing->pva_id !== $user->id && $viewing->pva_id !== null) {
            return redirect()->route('pva.viewings.index')
                ->with('error', 'You do not have permission to view this viewing.');
        }

        return view('pva.viewings.show', compact('viewing'));
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

        // Assign PVA to viewing if not already assigned
        if ($viewing->pva_id === null) {
            $viewing->update([
                'pva_id' => $user->id,
                'status' => 'scheduled',
            ]);
        } elseif ($viewing->pva_id !== $user->id) {
            return back()->with('error', 'This viewing is already assigned to another PVA.');
        }

        // TODO: Send confirmation email to buyer
        // \Mail::to($viewing->buyer->email)->send(new \App\Mail\ViewingConfirmed($viewing));

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
            'buyer_interested' => ['required', 'boolean'],
            'buyer_feedback' => ['nullable', 'string', 'max:2000'],
            'property_condition' => ['nullable', 'string', 'in:excellent,good,fair,poor'],
            'buyer_notes' => ['nullable', 'string', 'max:2000'],
            'pva_notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'buyer_interested.required' => 'Please indicate if the buyer is interested.',
            'buyer_interested.boolean' => 'Invalid value for buyer interest.',
        ]);

        try {
            \DB::beginTransaction();

            // Create or update viewing feedback
            \App\Models\ViewingFeedback::updateOrCreate(
                ['viewing_id' => $viewing->id],
                [
                    'buyer_interested' => $validated['buyer_interested'],
                    'buyer_feedback' => $validated['buyer_feedback'] ?? null,
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
            'agent' => 'admin.dashboard',
            'buyer' => 'buyer.dashboard',
            'seller' => 'seller.dashboard',
            'both' => 'buyer.dashboard',
            'pva' => 'pva.dashboard',
        ];

        return $dashboards[$role] ?? 'home';
    }
}
