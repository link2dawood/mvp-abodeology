<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OfferController extends Controller
{
    /**
     * Get offers for a specific buyer.
     * Requires: JWT user_id must equal buyer_id OR role must be admin/agent
     * This endpoint enforces strict ownership: buyers can only see their own offers.
     */
    public function getBuyerOffers(Request $request, $buyer_id): JsonResponse
    {
        $user = $request->user() ?? JWTAuth::parseToken()->authenticate();

        // Verify buyer exists
        $buyer = \App\Models\User::find($buyer_id);

        if (!$buyer) {
            return response()->json([
                'error' => 'Buyer not found',
            ], 404);
        }

        // Ownership is enforced by CheckUserOwnership middleware
        // If we reach here, either:
        // 1. Authenticated user_id == buyer_id, OR
        // 2. User role is admin/agent

        // Get offers for this buyer
        $offers = Offer::where('buyer_id', $buyer_id)
            ->with(['buyer', 'property.seller', 'decisions.seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => $offers->items(),
            'buyer' => [
                'id' => $buyer->id,
                'name' => $buyer->name,
                'email' => $buyer->email,
            ],
            'pagination' => [
                'current_page' => $offers->currentPage(),
                'total' => $offers->total(),
                'per_page' => $offers->perPage(),
                'last_page' => $offers->lastPage(),
            ],
        ]);
    }

    /**
     * Get all offers.
     * - Buyers: Can view their own offers
     * - Sellers: Can view offers on their properties
     * - Admin/Agent: Can view all offers
     */
    public function index(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (in_array($user->role, ['admin', 'agent'])) {
            // Admin and agent can see all offers
            $offers = Offer::with(['buyer', 'property.seller'])->paginate(15);
        } elseif ($user->role === 'buyer') {
            // Buyers can see their own offers
            $offers = Offer::where('buyer_id', $user->id)
                ->with(['buyer', 'property.seller'])
                ->paginate(15);
        } elseif (in_array($user->role, ['seller', 'both'])) {
            // Sellers can see offers on their properties
            $offers = Offer::whereHas('property', function ($query) use ($user) {
                $query->where('seller_id', $user->id);
            })->with(['buyer', 'property.seller'])->paginate(15);
        } else {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        return response()->json([
            'data' => $offers->items(),
            'pagination' => [
                'current_page' => $offers->currentPage(),
                'total' => $offers->total(),
                'per_page' => $offers->perPage(),
            ],
        ]);
    }

    /**
     * Get a specific offer.
     * Uses canBeViewedBy() method for complex ownership logic
     */
    public function show($id): JsonResponse
    {
        $offer = Offer::with(['buyer', 'property.seller', 'decisions.seller'])->find($id);

        if (!$offer) {
            return response()->json([
                'error' => 'Offer not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Use the model's canBeViewedBy method
        if (!$offer->canBeViewedBy($user)) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to view this offer.',
            ], 403);
        }

        return response()->json([
            'data' => $offer,
        ]);
    }

    /**
     * Create a new offer.
     * Requires: buyer role
     * Ownership: Automatically assigned to authenticated user (buyer_id)
     */
    public function store(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Only buyers can create offers
        if (!in_array($user->role, ['buyer', 'both', 'admin', 'agent'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => 'Only buyers can submit offers.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'offer_amount' => 'required|numeric|min:0',
            'amount' => 'nullable|numeric|min:0', // Backwards compatibility
            'deposit_amount' => 'nullable|numeric|min:0',
            'funding_type' => 'nullable|in:cash,mortgage,part_mortgage',
            'aip_status' => 'nullable|in:provided,not_provided',
            'chain_position' => 'nullable|string|max:255',
            'conditions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        // Verify property exists and is live
        $property = Property::find($request->property_id);
        
        if (!$property) {
            return response()->json([
                'error' => 'Property not found',
            ], 404);
        }

        if ($property->status !== 'live') {
            return response()->json([
                'error' => 'Invalid property status',
                'message' => 'Offers can only be submitted on live properties.',
            ], 400);
        }

        // Prevent buyers from making offers on their own properties
        if ($property->seller_id === $user->id && $user->role !== 'admin') {
            return response()->json([
                'error' => 'Invalid operation',
                'message' => 'You cannot make an offer on your own property.',
            ], 400);
        }

        // Create offer - ownership is automatically assigned
        $offer = Offer::create([
            'property_id' => $request->property_id,
            'buyer_id' => $user->id, // Ownership enforced
            'offer_amount' => $request->offer_amount ?? $request->amount, // Support both for backwards compatibility
            'deposit_amount' => $request->deposit_amount,
            'funding_type' => $request->funding_type,
            'aip_status' => $request->aip_status ?? 'not_provided',
            'chain_position' => $request->chain_position,
            'conditions' => $request->conditions,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Offer submitted successfully',
            'data' => $offer->load(['buyer', 'property.seller']),
        ], 201);
    }

    /**
     * Update an offer (respond to it).
     * Requires: seller role + ownership of property OR admin/agent
     */
    public function update(Request $request, $id): JsonResponse
    {
        $offer = Offer::with('property')->find($id);

        if (!$offer) {
            return response()->json([
                'error' => 'Offer not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Check ownership: seller must own the property
        $canRespond = false;
        
        if (in_array($user->role, ['admin', 'agent'])) {
            $canRespond = true;
        } elseif (in_array($user->role, ['seller', 'both']) && $offer->property->seller_id === $user->id) {
            $canRespond = true;
        }

        if (!$canRespond) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to respond to this offer.',
            ], 403);
        }

        // Only sellers/admins can respond to offers
        if (!in_array($user->role, ['seller', 'both', 'admin', 'agent'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:accept,decline,counter',
            'comments' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        // Map decision to status
        $statusMap = [
            'accept' => 'accepted',
            'decline' => 'rejected',
            'counter' => 'countered',
        ];

        $newStatus = $statusMap[$request->decision];

        // Update offer status
        $offer->update([
            'status' => $newStatus,
        ]);

        // Create offer decision record
        \App\Models\OfferDecision::create([
            'offer_id' => $offer->id,
            'seller_id' => $user->role === 'seller' || $user->role === 'both' ? $user->id : $offer->property->seller_id,
            'decision' => $request->decision,
            'comments' => $request->comments,
            'decided_at' => now(),
        ]);

        return response()->json([
            'message' => 'Offer updated successfully',
            'data' => $offer->load(['buyer', 'property.seller', 'decisions.seller']),
        ]);
    }

    /**
     * Withdraw an offer.
     * Requires: buyer role + ownership OR admin
     */
    public function withdraw($id): JsonResponse
    {
        $offer = Offer::find($id);

        if (!$offer) {
            return response()->json([
                'error' => 'Offer not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Check ownership
        if ($user->role !== 'admin' && $offer->buyer_id !== $user->id) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You can only withdraw your own offers.',
            ], 403);
        }

        // Check role
        if (!in_array($user->role, ['buyer', 'both', 'admin'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json([
                'error' => 'Invalid operation',
                'message' => 'Only pending offers can be withdrawn.',
            ], 400);
        }

        $offer->update([
            'status' => 'withdrawn',
            'responded_at' => now(),
        ]);

        return response()->json([
            'message' => 'Offer withdrawn successfully',
            'data' => $offer->load(['buyer', 'property.seller']),
        ]);
    }
}
