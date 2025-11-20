<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

class CheckOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $model  Model class name (e.g., 'Property', 'Offer', 'Viewing')
     * @param  string  $param  Route parameter name containing the ID (default: 'id')
     * @param  bool    $requireOwnership  Whether to strictly require ownership (false = admin/agent can bypass)
     */
    public function handle(Request $request, Closure $next, string $model, string $param = 'id', bool $requireOwnership = true): Response
    {
        try {
            // Get user from request (set by jwt.auth middleware) or authenticate
            $user = $request->user() ?? JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'error' => 'User not found',
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // Admin and agent can bypass ownership checks (unless requireOwnership is true)
            if (!$requireOwnership && in_array($user->role, ['admin', 'agent'])) {
                return $next($request);
            }

            // Get the resource ID from route
            $resourceId = $request->route($param);

            if (!$resourceId) {
                return response()->json([
                    'error' => 'Resource ID not found',
                ], 400);
            }

            // Resolve model class
            $modelClass = "App\\Models\\{$model}";
            
            if (!class_exists($modelClass)) {
                return response()->json([
                    'error' => 'Invalid model specified',
                ], 500);
            }

            // Find the resource
            $resource = $modelClass::find($resourceId);

            if (!$resource) {
                return response()->json([
                    'error' => 'Resource not found',
                ], 404);
            }

            // Check if model uses Ownable trait
            if (!in_array('App\Traits\Ownable', class_uses_recursive($resource))) {
                return response()->json([
                    'error' => 'Resource does not support ownership checks',
                ], 500);
            }

            // Special handling for models with custom canBeViewedBy method
            if (method_exists($resource, 'canBeViewedBy')) {
                if (!$resource->canBeViewedBy($user)) {
                    return response()->json([
                        'error' => 'Access denied',
                        'message' => 'You do not have permission to access this resource.',
                    ], 403);
                }
            } else {
                // Use standard ownership check
                if (!$resource->isOwnedBy($user)) {
                    return response()->json([
                        'error' => 'Access denied',
                        'message' => 'You do not have permission to access this resource.',
                    ], 403);
                }
            }

            // Attach resource to request for use in controller
            $request->attributes->set('resource', $resource);

            return $next($request);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token error',
                'message' => 'Could not authenticate user.',
            ], 401);
        }
    }
}
