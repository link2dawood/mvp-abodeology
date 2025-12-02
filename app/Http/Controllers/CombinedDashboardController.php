<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CombinedDashboardController extends Controller
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
     * Show the combined dashboard for users with both buyer and seller roles.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Only allow users with 'both' role
        if ($user->role !== 'both') {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this dashboard.');
        }

        // ============================================
        // SELLER DATA
        // ============================================
        $sellerProperties = \App\Models\Property::where('seller_id', $user->id)
            ->with(['seller', 'instruction', 'photos', 'offers.buyer', 'viewings.buyer', 'homecheckReports'])
            ->orderBy('created_at', 'desc')
            ->get();

        $primarySellerProperty = $sellerProperties->whereIn('status', ['live', 'sstc'])->first() ?? $sellerProperties->first();
        
        $sellerOffers = collect();
        $sellerViewings = collect();
        if ($sellerProperties->count() > 0) {
            $propertyIds = $sellerProperties->pluck('id');
            
            $sellerOffers = \App\Models\Offer::whereIn('property_id', $propertyIds)
                ->whereIn('status', ['pending', 'countered'])
                ->with(['buyer', 'property', 'latestDecision'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $sellerViewings = \App\Models\Viewing::whereIn('property_id', $propertyIds)
                ->where('viewing_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->with(['buyer', 'property'])
                ->orderBy('viewing_date', 'asc')
                ->get();
        }

        // ============================================
        // BUYER DATA
        // ============================================
        $buyerOffers = \App\Models\Offer::where('buyer_id', $user->id)
            ->with(['property.photos', 'latestDecision'])
            ->orderBy('created_at', 'desc')
            ->get();

        $upcomingBuyerViewings = \App\Models\Viewing::where('buyer_id', $user->id)
            ->where('viewing_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['property' => function($query) {
                $query->where('status', 'live');
            }])
            ->orderBy('viewing_date', 'asc')
            ->get();

        $activeBuyerOffer = $buyerOffers->whereIn('status', ['pending', 'countered'])->first();

        // ============================================
        // RECOMMENDED PROPERTIES
        // ============================================
        $recommendedProperties = \App\Models\Property::where('status', 'live')
            ->where('seller_id', '!=', $user->id) // Don't recommend own properties
            ->with(['photos'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Exclude properties buyer has already viewed or made offers on
        $viewedPropertyIds = \App\Models\Viewing::where('buyer_id', $user->id)
            ->pluck('property_id')
            ->toArray();
        $offeredPropertyIds = $buyerOffers->pluck('property_id')->toArray();
        $excludedIds = array_unique(array_merge($viewedPropertyIds, $offeredPropertyIds));
        
        if (count($excludedIds) > 0) {
            $recommendedProperties = $recommendedProperties->whereNotIn('id', $excludedIds);
        }

        return view('combined.dashboard', compact(
            'user',
            'sellerProperties',
            'primarySellerProperty',
            'sellerOffers',
            'sellerViewings',
            'buyerOffers',
            'upcomingBuyerViewings',
            'activeBuyerOffer',
            'recommendedProperties'
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
            'both' => 'combined.dashboard',
            'pva' => 'pva.dashboard',
        ];

        return $dashboards[$role] ?? 'home';
    }
}

