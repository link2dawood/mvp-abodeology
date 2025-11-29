<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseValidation
{
    /**
     * Handle an incoming request and validate API responses.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only validate JSON responses
        if ($response->headers->get('Content-Type') === 'application/json' || 
            $request->expectsJson() || 
            $request->is('api/*')) {
            
            $content = $response->getContent();
            $data = json_decode($content, true);
            
            // Ensure consistent response structure
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                // If response doesn't have standard structure, wrap it
                if (!isset($data['data']) && !isset($data['error']) && !isset($data['message'])) {
                    // This is likely a successful response without wrapper
                    // Don't modify it, just ensure it's valid JSON
                }
                
                // Validate response has proper structure for errors
                if (isset($data['error']) && !isset($data['message'])) {
                    $data['message'] = $data['error'];
                }
                
                // Ensure status code matches response
                if (isset($data['error']) && $response->getStatusCode() < 400) {
                    // Error in response but status code is success - this shouldn't happen
                    // Log it but don't change response to avoid breaking things
                    \Log::warning('API response has error but status code is success', [
                        'url' => $request->fullUrl(),
                        'status' => $response->getStatusCode(),
                        'data' => $data
                    ]);
                }
            }
        }

        return $response;
    }
}

