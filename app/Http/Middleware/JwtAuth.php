<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * This middleware handles JWT authentication with the following responsibilities:
     * 1. Check JWT signature
     * 2. Check expiry
     * 3. Check user role (optional - if roles provided)
     * 4. Add user object to request
     * 5. Return 403 Forbidden if role mismatch
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Optional roles to check (if provided, validates role match)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        try {
            // 1. Check JWT signature and 2. Check expiry
            // This is handled automatically by parseToken() and authenticate()
            // - Invalid signature throws TokenInvalidException
            // - Expired token throws TokenExpiredException
            // - Blacklisted token throws TokenBlacklistedException
            
            $user = JWTAuth::parseToken()->authenticate();

            // Verify user exists in database
            if (!$user) {
                return response()->json([
                    'error' => 'Authentication failed',
                    'message' => 'User not found',
                ], 401);
            }

            // Check if account is locked
            if ($user->isLocked()) {
                $minutesRemaining = now()->diffInMinutes($user->locked_until, false);
                return response()->json([
                    'error' => 'Account locked',
                    'message' => 'Your account has been locked. Please try again in ' . max(1, $minutesRemaining) . ' minute(s).',
                ], 423);
            }

            // Check if email is verified (if required)
            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'error' => 'Email not verified',
                    'message' => 'Please verify your email address to continue.',
                ], 403);
            }

            // 3. Check user role (if roles are provided)
            if (!empty($roles)) {
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
                $expandedRoles = $this->expandRoleAliases($roles);

                // 5. If role mismatch → return 403 Forbidden
                if (!in_array($userRole, $expandedRoles)) {
                    return response()->json([
                        'error' => 'Forbidden',
                        'message' => 'You do not have permission to access this resource.',
                        'required_roles' => $roles,
                        'your_role' => $userRole,
                    ], 403);
                }
            }

            // 4. Add user object to request
            // Make user available via $request->user() or Auth::user()
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            // Also set as attribute for easy access in controllers
            $request->attributes->set('authenticated_user', $user);
            $request->attributes->set('authenticated_user_id', $user->id);

            // Make user available via auth() helper
            auth()->setUser($user);

            return $next($request);

        } catch (TokenExpiredException $e) {
            // Token has expired
            return response()->json([
                'error' => 'Token expired',
                'message' => 'Your authentication token has expired. Please refresh your token or log in again.',
                'code' => 'token_expired',
            ], 401);

        } catch (TokenInvalidException $e) {
            // Token signature is invalid
            return response()->json([
                'error' => 'Token invalid',
                'message' => 'Your authentication token is invalid or has been tampered with.',
                'code' => 'token_invalid',
            ], 401);

        } catch (TokenBlacklistedException $e) {
            // Token has been blacklisted (logged out)
            return response()->json([
                'error' => 'Token blacklisted',
                'message' => 'Your authentication token has been revoked. Please log in again.',
                'code' => 'token_blacklisted',
            ], 401);

        } catch (JWTException $e) {
            // Generic JWT exception
            return response()->json([
                'error' => 'Authentication error',
                'message' => 'Could not authenticate user: ' . $e->getMessage(),
                'code' => 'jwt_exception',
            ], 401);

        } catch (\Exception $e) {
            // Catch any other unexpected exceptions
            return response()->json([
                'error' => 'Authentication failed',
                'message' => 'An unexpected error occurred during authentication.',
                'code' => 'authentication_failed',
            ], 500);
        }
    }

    use \App\Traits\ExpandsRoleAliases;
}
