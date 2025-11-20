<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Rate limiting: 5 attempts per 10 minutes per IP
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
            ], 429);
        }
        RateLimiter::hit($key, 600); // 600 seconds (10 minutes) decay

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $minutesRemaining = now()->diffInMinutes($user->locked_until, false);
            return response()->json([
                'error' => 'Account locked. Please try again in ' . $minutesRemaining . ' minute(s).',
            ], 423);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            $user->incrementFailedLoginAttempts();
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Check if email is verified (if required)
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'Email not verified. Please verify your email address.',
            ], 403);
        }

        // Reset failed login attempts on successful login
        $user->resetFailedLoginAttempts();

        try {
            // Generate access token
            $accessToken = JWTAuth::fromUser($user);

            // Generate refresh token with fingerprint
            $fingerprint = $this->generateFingerprint($request);
            $refreshToken = $this->createRefreshToken($user, $fingerprint, $request);

            // Set HTTP-only cookie for refresh token
            $cookie = cookie(
                'refresh_token',
                $refreshToken->token,
                config('jwt.refresh_ttl') * 60, // Convert minutes to seconds
                '/',
                null,
                true, // Secure (HTTPS only)
                true, // HttpOnly
                false,
                'Strict' // SameSite
            );

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60, // Convert minutes to seconds
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200)->cookie($cookie);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token',
            ], 500);
        }
    }

    /**
     * Register a new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // Rate limiting: 5 registrations per hour per IP
        $key = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Too many registration attempts. Please try again in ' . ceil($seconds / 3600) . ' hour(s).',
            ], 429);
        }
        RateLimiter::hit($key, 3600); // 3600 seconds (1 hour) decay

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:buyer,seller,both',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);

            // Generate access token
            $accessToken = JWTAuth::fromUser($user);

            // Generate refresh token
            $fingerprint = $this->generateFingerprint($request);
            $refreshToken = $this->createRefreshToken($user, $fingerprint, $request);

            // Set HTTP-only cookie for refresh token
            $cookie = cookie(
                'refresh_token',
                $refreshToken->token,
                config('jwt.refresh_ttl') * 60,
                '/',
                null,
                true,
                true,
                false,
                'Strict'
            );

            return response()->json([
                'message' => 'User successfully registered',
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 201)->cookie($cookie);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        // Rate limiting: 10 refresh attempts per minute per IP
        $key = 'refresh:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Too many refresh attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        // Get refresh token from cookie or request
        $refreshTokenValue = $request->cookie('refresh_token') ?? $request->input('refresh_token');

        if (!$refreshTokenValue) {
            return response()->json([
                'error' => 'Refresh token not provided',
            ], 401);
        }

        // Find refresh token in database
        $refreshToken = RefreshToken::where('token', $refreshTokenValue)
            ->where('is_revoked', false)
            ->first();

        if (!$refreshToken || !$refreshToken->isValid()) {
            return response()->json([
                'error' => 'Invalid or expired refresh token',
            ], 401);
        }

        // Verify fingerprint if provided
        $fingerprint = $this->generateFingerprint($request);
        if ($refreshToken->fingerprint && $refreshToken->fingerprint !== $fingerprint) {
            // Fingerprint mismatch - revoke token for security
            $refreshToken->revoke();
            return response()->json([
                'error' => 'Token fingerprint mismatch. Token revoked for security.',
            ], 401);
        }

        // Get user
        $user = $refreshToken->user;

        // Check if account is locked
        if ($user->isLocked()) {
            $refreshToken->revoke();
            return response()->json([
                'error' => 'Account locked',
            ], 423);
        }

        try {
            // Revoke old refresh token (token rotation)
            $refreshToken->revoke();

            // Generate new access token
            $accessToken = JWTAuth::fromUser($user);

            // Generate new refresh token
            $newRefreshToken = $this->createRefreshToken($user, $fingerprint, $request);

            // Update last used timestamp on old token (before revoking)
            $refreshToken->touchLastUsed();

            // Set HTTP-only cookie for new refresh token
            $cookie = cookie(
                'refresh_token',
                $newRefreshToken->token,
                config('jwt.refresh_ttl') * 60,
                '/',
                null,
                true,
                true,
                false,
                'Strict'
            );

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200)->cookie($cookie);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not refresh token',
            ], 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Get refresh token from cookie
        $refreshTokenValue = $request->cookie('refresh_token');

        if ($refreshTokenValue) {
            $refreshToken = RefreshToken::where('token', $refreshTokenValue)->first();
            if ($refreshToken) {
                $refreshToken->revoke();
            }
        }

        // Invalidate JWT token
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            // Token already invalid or not provided
        }

        // Clear refresh token cookie
        $cookie = cookie()->forget('refresh_token');

        return response()->json([
            'message' => 'Successfully logged out',
        ], 200)->cookie($cookie);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
            ],
        ]);
    }

    /**
     * Create a refresh token for the user.
     */
    private function createRefreshToken(User $user, string $fingerprint, Request $request): RefreshToken
    {
        return RefreshToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'fingerprint' => $fingerprint,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'expires_at' => now()->addMinutes(config('jwt.refresh_ttl')),
        ]);
    }

    /**
     * Generate a fingerprint from request headers.
     */
    private function generateFingerprint(Request $request): string
    {
        $data = [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];

        return hash('sha256', implode('|', array_filter($data)));
    }
}
