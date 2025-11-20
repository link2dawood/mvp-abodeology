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
