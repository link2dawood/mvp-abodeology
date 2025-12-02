<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWebRole
{
    /**
     * Handle an incoming request.
     * 
     * Checks if the authenticated user has the required role(s) to access the route.
     * This middleware is for web routes using session-based authentication.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  The required roles (can be multiple)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();
        $userRole = $user->role;

        if (!$userRole) {
            return redirect()->route('login')->with('error', 'Your account does not have a valid role assigned.');
        }

        // Split comma-separated roles (e.g., "seller,both" becomes ["seller", "both"])
        $parsedRoles = [];
        foreach ($roles as $role) {
            $parsedRoles = array_merge($parsedRoles, explode(',', $role));
        }
        $parsedRoles = array_map('trim', $parsedRoles);

        // Expand role aliases to handle special cases
        $allowedRoles = $this->expandRoleAliases($parsedRoles);

        // Check if user has required role
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // User doesn't have the required role - redirect to their dashboard
        return $this->redirectToRoleDashboard($userRole);
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
            if ($role === 'buyer') {
                if (!in_array('both', $expanded)) {
                    $expanded[] = 'both';
                }
            }
            
            if ($role === 'seller') {
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

    /**
     * Redirect user to their role-specific dashboard.
     *
     * @param string $userRole
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectToRoleDashboard(string $userRole): Response
    {
        $user = auth()->user();
        
        $dashboardRoutes = [
            'admin' => route('admin.dashboard'),
            'agent' => route('admin.agent.dashboard'),
            'buyer' => route('buyer.dashboard'),
            'seller' => route('seller.dashboard'),
            'both' => route('combined.dashboard'),
            'pva' => route('pva.dashboard'),
        ];

        $dashboardRoute = $dashboardRoutes[$userRole] ?? route('home');
        
        // Handle callable routes (if any)
        if (is_callable($dashboardRoute)) {
            $dashboardRoute = $dashboardRoute();
        }

        return redirect($dashboardRoute)->with('error', 'You do not have permission to access this page.');
    }
}

