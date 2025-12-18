<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Viewing;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ViewingController extends Controller
{
    /**
     * Get all viewings.
     * - Buyers: Can view their own viewings
     * - Sellers: Can view viewings on their properties
     * - PVAs: Can view viewings assigned to them
     * - Admin/Agent: Can view all viewings
     */
    public function index(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (in_array($user->role, ['admin', 'agent'])) {
            // Admin and agent can see all viewings
            $viewings = Viewing::with(['buyer', 'property.seller', 'pva'])->paginate(15);
        } elseif ($user->role === 'buyer') {
            // Buyers can see their own viewings
            $viewings = Viewing::where('buyer_id', $user->id)
                ->with(['buyer', 'property.seller', 'pva'])
                ->paginate(15);
        } elseif ($user->role === 'pva') {
            // PVAs can see viewings assigned to them
            $viewings = Viewing::where('pva_id', $user->id)
                ->with(['buyer', 'property.seller', 'pva'])
                ->paginate(15);
        } elseif (in_array($user->role, ['seller', 'both'])) {
            // Sellers can see viewings on their properties
            $viewings = Viewing::whereHas('property', function ($query) use ($user) {
                $query->where('seller_id', $user->id);
            })->with(['buyer', 'property.seller', 'pva'])->paginate(15);
        } else {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        return response()->json([
            'data' => $viewings->items(),
            'pagination' => [
                'current_page' => $viewings->currentPage(),
                'total' => $viewings->total(),
                'per_page' => $viewings->perPage(),
            ],
        ]);
    }

    /**
     * Get a specific viewing.
     * Uses canBeViewedBy() method for complex ownership logic
     */
    public function show($id): JsonResponse
    {
        $viewing = Viewing::with(['buyer', 'property.seller', 'pva'])->find($id);

        if (!$viewing) {
            return response()->json([
                'error' => 'Viewing not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Use the model's canBeViewedBy method
        if (!$viewing->canBeViewedBy($user)) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to view this viewing.',
            ], 403);
        }

        return response()->json([
            'data' => $viewing,
        ]);
    }

    /**
     * Book a viewing.
     * Requires: buyer role
     * Ownership: Automatically assigned to authenticated user (buyer_id)
     */
    public function store(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Only buyers can book viewings
        if (!in_array($user->role, ['buyer', 'both', 'admin', 'agent'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => 'Only buyers can book viewings.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'viewing_date' => 'required|date|after:now',
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

        // Validate property status allows buyer interactions
        $statusService = new \App\Services\PropertyStatusTransitionService();
        if ($statusService->blocksBuyerInteractions($property)) {
            return response()->json([
                'error' => 'Invalid property status',
                'message' => 'This property is no longer available for viewings. Status: ' . ucfirst($property->status),
            ], 400);
        }
        
        if ($property->status !== 'live') {
            return response()->json([
                'error' => 'Invalid property status',
                'message' => 'Viewings can only be booked for live properties.',
            ], 400);
        }

        // Create viewing - ownership is automatically assigned
        $viewing = Viewing::create([
            'property_id' => $request->property_id,
            'buyer_id' => $user->id, // Ownership enforced
            'viewing_date' => $request->viewing_date,
            'status' => 'scheduled',
        ]);

        return response()->json([
            'message' => 'Viewing booked successfully',
            'data' => $viewing->load(['buyer', 'property.seller', 'pva']),
        ], 201);
    }

    /**
     * Submit PVA feedback for a viewing.
     * Requires: pva role + ownership (pva_id matches) OR admin/agent
     */
    public function submitFeedback(Request $request, $id): JsonResponse
    {
        $viewing = Viewing::with('property')->find($id);

        if (!$viewing) {
            return response()->json([
                'error' => 'Viewing not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Check ownership: PVA must be assigned to this viewing
        $canSubmitFeedback = false;
        
        if (in_array($user->role, ['admin', 'agent'])) {
            $canSubmitFeedback = true;
        } elseif ($user->role === 'pva' && $viewing->pva_id === $user->id) {
            $canSubmitFeedback = true;
        }

        if (!$canSubmitFeedback) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to submit feedback for this viewing.',
            ], 403);
        }

        // Check role
        if (!in_array($user->role, ['pva', 'admin', 'agent'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => 'Only PVAs can submit viewing feedback.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'pva_feedback' => 'required|string|max:5000',
            'pva_notes' => 'nullable|array',
            'status' => 'sometimes|in:completed,cancelled,no_show',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'pva_feedback' => $request->pva_feedback,
            'status' => $request->status ?? 'completed',
        ];

        if ($request->has('pva_notes')) {
            $updateData['pva_notes'] = $request->pva_notes;
        }

        if (($request->status ?? 'completed') === 'completed') {
            $updateData['completed_at'] = now();
        }

        $viewing->update($updateData);

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'data' => $viewing->load(['buyer', 'property.seller', 'pva']),
        ]);
    }

    /**
     * Update a viewing (cancel, reschedule, assign PVA).
     * Requires: appropriate role + ownership
     */
    public function update(Request $request, $id): JsonResponse
    {
        $viewing = Viewing::with('property')->find($id);

        if (!$viewing) {
            return response()->json([
                'error' => 'Viewing not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Check permissions based on what's being updated
        $canUpdate = false;

        // Admin/Agent can update anything
        if (in_array($user->role, ['admin', 'agent'])) {
            $canUpdate = true;
        }
        // Buyer can cancel their own viewings
        elseif ($user->role === 'buyer' && $viewing->buyer_id === $user->id) {
            // Buyers can only cancel/reschedule, not assign PVAs
            if (!isset($request->pva_id)) {
                $canUpdate = true;
            }
        }
        // Seller can update viewings on their properties (assign PVA)
        elseif (in_array($user->role, ['seller', 'both']) && $viewing->property->seller_id === $user->id) {
            $canUpdate = true;
        }

        if (!$canUpdate) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to update this viewing.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'viewing_date' => 'sometimes|date|after:now',
            'pva_id' => 'nullable|exists:users,id',
            'status' => 'sometimes|in:scheduled,completed,cancelled,no_show',
        ]);

        // Validate PVA assignment
        if ($request->has('pva_id')) {
            $pva = \App\Models\User::find($request->pva_id);
            if (!$pva || $pva->role !== 'pva') {
                return response()->json([
                    'error' => 'Invalid PVA',
                    'message' => 'The specified user is not a PVA.',
                ], 422);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        $viewing->update($request->only([
            'viewing_date', 'pva_id', 'status',
        ]));

        return response()->json([
            'message' => 'Viewing updated successfully',
            'data' => $viewing->load(['buyer', 'property.seller', 'pva']),
        ]);
    }
}
