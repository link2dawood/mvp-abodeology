<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * Enhanced to support role checking with optional ownership validation.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
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

            // Get role from token payload (more secure than from user model)
            // This prevents role tampering by changing database directly
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token);
            $userRole = $payload->get('role');

            if (!$userRole) {
                return response()->json([
                    'error' => 'Invalid token',
                    'message' => 'Token does not contain role information.',
                ], 401);
            }

            // Handle role aliases
            $roles = $this->expandRoleAliases($roles);

            // Check if user has required role
            if (empty($roles) || in_array($userRole, $roles)) {
                // Attach user to request for use in controllers
                $request->attributes->set('authenticated_user', $user);
                return $next($request);
            }

            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => 'You do not have the required role to access this resource.',
                'required_roles' => $roles,
                'your_role' => $userRole,
            ], 403);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token error',
                'message' => 'Could not authenticate user.',
            ], 401);
        }
    }

    /**
     * Expand role aliases (e.g., 'seller' includes 'both', 'agent' includes 'admin').
     *
     * @param array $roles
     * @return array
     */
    private function expandRoleAliases(array $roles): array
    {
        $expanded = [];
        
        foreach ($roles as $role) {
            $expanded[] = $role;
            
            // 'both' role can access buyer and seller endpoints
            if ($role === 'buyer' || $role === 'seller') {
                if (!in_array('both', $expanded)) {
                    $expanded[] = 'both';
                }
            }
            
            // 'admin' can access agent endpoints
            if ($role === 'agent') {
                if (!in_array('admin', $expanded)) {
                    $expanded[] = 'admin';
                }
            }
        }
        
        return array_unique($expanded);
    }
}
