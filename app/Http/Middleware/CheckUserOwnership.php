<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckUserOwnership
{
    /**
     * Handle an incoming request.
     *
     * Enforces ownership: JWT user_id must equal the route parameter user ID
     * OR user role must be admin/agent.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $param  Route parameter name containing the user ID (default: 'buyer_id')
     */
    public function handle(Request $request, Closure $next, string $param = 'buyer_id'): Response
    {
        try {
            // Get authenticated user from request (set by jwt.auth middleware) or authenticate
            $user = $request->user() ?? JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'error' => 'Authentication failed',
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // Get the user ID from route parameter
            $routeUserId = $request->route($param);

            if (!$routeUserId) {
                return response()->json([
                    'error' => 'Invalid request',
                    'message' => "Route parameter '{$param}' not found.",
                ], 400);
            }

            // Convert to integer for comparison
            $routeUserId = (int) $routeUserId;
            $authenticatedUserId = (int) $user->id;

            // Check ownership: JWT user_id must equal route user_id OR role must be admin/agent
            $hasAccess = false;

            // Admin and agent can access any user's resources
            if (in_array($user->role, ['admin', 'agent'])) {
                $hasAccess = true;
            }
            // User can only access their own resources
            elseif ($authenticatedUserId === $routeUserId) {
                $hasAccess = true;
            }

            if (!$hasAccess) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'You do not have permission to access this resource.',
                    'detail' => 'Request JWT user_id must equal ' . $param . ' OR role must be admin/agent.',
                    'your_user_id' => $authenticatedUserId,
                    'requested_user_id' => $routeUserId,
                ], 403);
            }

            return $next($request);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Authentication error',
                'message' => 'Could not authenticate user.',
            ], 401);
        }
    }
}
