<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class PropertyController extends Controller
{
    /**
     * Get all properties.
     * - Buyers: Can view all live properties
     * - Sellers: Can view their own properties
     * - Admin/Agent: Can view all properties
     */
    public function index(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (in_array($user->role, ['admin', 'agent'])) {
            // Admin and agent can see all properties
            $properties = Property::with('seller')->paginate(15);
        } elseif ($user->role === 'buyer') {
            // Buyers can see all live properties
            $properties = Property::where('status', 'live')
                ->with('seller')
                ->paginate(15);
        } elseif (in_array($user->role, ['seller', 'both'])) {
            // Sellers can see their own properties
            $properties = Property::where('seller_id', $user->id)
                ->with('seller')
                ->paginate(15);
        } else {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        return response()->json([
            'data' => $properties->items(),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'total' => $properties->total(),
                'per_page' => $properties->perPage(),
            ],
        ]);
    }

    /**
     * Search properties.
     * Rate limit: 60 requests per minute
     * - Buyers: Can search live properties
     * - Sellers: Can search their own properties
     * - Admin/Agent: Can search all properties
     */
    public function search(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'query' => 'nullable|string|max:255',
            'property_type' => 'nullable|string|max:100',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'status' => 'nullable|in:draft,pending,live,sold,withdrawn',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        $query = Property::query();

        // Role-based filtering
        if (in_array($user->role, ['admin', 'agent'])) {
            // Admin and agent can search all properties
            // No additional filtering needed
        } elseif ($user->role === 'buyer') {
            // Buyers can only search live properties
            $query->where('status', 'live');
        } elseif (in_array($user->role, ['seller', 'both'])) {
            // Sellers can only search their own properties
            $query->where('seller_id', $user->id);
        } else {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        // Apply search filters
        if ($request->has('query') && $request->query) {
            $searchQuery = $request->query;
            $query->where(function ($q) use ($searchQuery) {
                $q->where('address', 'like', "%{$searchQuery}%")
                  ->orWhere('description', 'like', "%{$searchQuery}%")
                  ->orWhere('property_type', 'like', "%{$searchQuery}%");
            });
        }

        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->has('min_price')) {
            $query->where('asking_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('asking_price', '<=', $request->max_price);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('address')) {
            $query->where('address', 'like', "%{$request->address}%");
        }

        $properties = $query->with('seller')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => $properties->items(),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'total' => $properties->total(),
                'per_page' => $properties->perPage(),
                'last_page' => $properties->lastPage(),
            ],
        ]);
    }

    /**
     * Get a specific property.
     * - Buyers: Can view live properties
     * - Sellers: Can view their own properties (any status)
     * - Admin/Agent: Can view all properties
     */
    public function show($id): JsonResponse
    {
        $property = Property::with(['seller', 'offers', 'viewings'])->find($id);

        if (!$property) {
            return response()->json([
                'error' => 'Property not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Check access permissions
        $canView = false;
        
        if (in_array($user->role, ['admin', 'agent'])) {
            $canView = true;
        } elseif ($user->role === 'buyer' && $property->status === 'live') {
            $canView = true;
        } elseif (in_array($user->role, ['seller', 'both']) && $property->seller_id === $user->id) {
            $canView = true;
        }

        if (!$canView) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to view this property.',
            ], 403);
        }

        return response()->json([
            'data' => $property,
        ]);
    }

    /**
     * Create a new property.
     * Requires: seller role
     * Ownership: Automatically assigned to authenticated user
     */
    public function store(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Only sellers can create properties
        if (!in_array($user->role, ['seller', 'both', 'admin', 'agent'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => 'Only sellers can create properties.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:500',
            'property_type' => 'nullable|string|max:100',
            'asking_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'material_information' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        // Create property - ownership is automatically assigned
        $property = Property::create([
            'seller_id' => $user->id, // Ownership enforced
            'address' => $request->address,
            'property_type' => $request->property_type,
            'asking_price' => $request->asking_price,
            'description' => $request->description,
            'material_information' => $request->material_information,
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'Property created successfully',
            'data' => $property->load('seller'),
        ], 201);
    }

    /**
     * Update a property.
     * Requires: seller role + ownership OR admin/agent
     */
    public function update(Request $request, $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'error' => 'Property not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Check ownership (admin/agent can bypass)
        if (!in_array($user->role, ['admin', 'agent']) && $property->seller_id !== $user->id) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to update this property.',
            ], 403);
        }

        // Check role
        if (!in_array($user->role, ['seller', 'both', 'admin', 'agent'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => 'Only sellers can update properties.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'address' => 'sometimes|required|string|max:500',
            'property_type' => 'nullable|string|max:100',
            'asking_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'material_information' => 'nullable|array',
            'status' => ['sometimes', 'in:' . implode(',', \App\Models\Property::getValidStatuses())],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        // Handle status update with validation using PropertyStatusTransitionService
        $updateData = $request->only([
            'address', 'property_type', 'asking_price', 'description',
            'material_information',
        ]);

        if ($request->has('status') && $request->status !== $property->status) {
            $statusService = new \App\Services\PropertyStatusTransitionService();
            $statusResult = $statusService->changeStatus(
                $property,
                $request->status,
                $user->id,
                'Status updated via API'
            );

            if (!$statusResult['success']) {
                return response()->json([
                    'error' => 'Status update failed',
                    'message' => $statusResult['message'],
                ], 400);
            }
        } else {
            // Update other fields only
            $property->update($updateData);
        }

        return response()->json([
            'message' => 'Property updated successfully',
            'data' => $property->load('seller'),
        ]);
    }

    /**
     * Delete a property.
     * Requires: seller role + ownership OR admin
     */
    public function destroy($id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'error' => 'Property not found',
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Only admin can delete, or seller who owns it
        if ($user->role !== 'admin' && $property->seller_id !== $user->id) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have permission to delete this property.',
            ], 403);
        }

        // Check role for sellers
        if (!in_array($user->role, ['seller', 'both', 'admin'])) {
            return response()->json([
                'error' => 'Insufficient permissions',
            ], 403);
        }

        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully',
        ], 200);
    }
}
