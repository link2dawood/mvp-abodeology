<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * Implements global rate limiting:
     * - 60 requests per minute per user (for authenticated users)
     * - 200 requests per minute per IP (for all users)
     * - Lower limits for anonymous users
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $maxAttemptsPerUser  Maximum attempts per user (default: 60)
     * @param  int  $decayMinutes  Decay time in minutes (default: 1)
     */
    public function handle(Request $request, Closure $next, int $maxAttemptsPerUser = 60, int $decayMinutes = 1): Response
    {
        $ip = $request->ip();
        $isAuthenticated = false;
        $userId = null;

        // Try to get authenticated user
        try {
            $user = $request->user() ?? JWTAuth::parseToken()->authenticate();
            if ($user) {
                $isAuthenticated = true;
                $userId = $user->id;
            }
        } catch (\Exception $e) {
            // User not authenticated or invalid token
            $isAuthenticated = false;
        }

        // IP-based rate limiting (applies to all users)
        $ipKey = 'api:ip:' . $ip;
        $ipLimit = $isAuthenticated ? 200 : 100; // Higher limit for logged-in users

        if (RateLimiter::tooManyAttempts($ipKey, $ipLimit)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return $this->buildRateLimitResponse(
                'Too many requests from this IP address. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
                $seconds,
                $ipLimit,
                $decayMinutes * 60
            );
        }

        // User-based rate limiting (only for authenticated users)
        if ($isAuthenticated && $userId) {
            $userKey = 'api:user:' . $userId;
            
            if (RateLimiter::tooManyAttempts($userKey, $maxAttemptsPerUser)) {
                $seconds = RateLimiter::availableIn($userKey);
                return $this->buildRateLimitResponse(
                    'Too many requests. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
                    $seconds,
                    $maxAttemptsPerUser,
                    $decayMinutes * 60
                );
            }

            RateLimiter::hit($userKey, $decayMinutes * 60);
        }

        // Hit IP-based rate limiter
        RateLimiter::hit($ipKey, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers to response
        return $this->addRateLimitHeaders(
            $response,
            $isAuthenticated ? $maxAttemptsPerUser : $ipLimit,
            $isAuthenticated ? RateLimiter::remaining('api:user:' . $userId, $maxAttemptsPerUser) : RateLimiter::remaining($ipKey, $ipLimit),
            RateLimiter::availableIn($isAuthenticated ? 'api:user:' . $userId : $ipKey)
        );
    }

    /**
     * Build rate limit error response.
     */
    private function buildRateLimitResponse(string $message, int $retryAfter, int $limit, int $decay): Response
    {
        return response()->json([
            'error' => 'Too Many Requests',
            'message' => $message,
            'retry_after' => $retryAfter,
            'limit' => $limit,
            'decay' => $decay,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $limit,
            'X-RateLimit-Remaining' => 0,
        ]);
    }

    /**
     * Add rate limit headers to response.
     */
    private function addRateLimitHeaders(Response $response, int $limit, int $remaining, int $retryAfter): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $limit,
            'X-RateLimit-Remaining' => max(0, $remaining),
            'X-RateLimit-Reset' => time() + $retryAfter,
        ]);

        return $response;
    }
}
