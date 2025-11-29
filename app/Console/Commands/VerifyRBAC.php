<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class VerifyRBAC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify all role-based access control (RBAC) permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying RBAC Permissions...');
        $this->newLine();

        $issues = [];
        $passed = 0;

        // Test 1: Verify all roles exist
        $this->info('1. Verifying user roles...');
        $validRoles = ['admin', 'agent', 'buyer', 'seller', 'pva', 'both'];
        $users = User::all();
        
        foreach ($users as $user) {
            if (!in_array($user->role, $validRoles)) {
                $issues[] = "User {$user->id} ({$user->email}) has invalid role: {$user->role}";
            } else {
                $passed++;
            }
        }
        $this->info("   ✓ Checked {$users->count()} users");
        $this->newLine();

        // Test 2: Verify middleware exists
        $this->info('2. Verifying middleware registration...');
        $requiredMiddleware = [
            'jwt.auth',
            'role.web',
            'role',
            'ownership',
            'user.ownership',
            'api.ratelimit',
        ];

        foreach ($requiredMiddleware as $middleware) {
            if (Route::hasMiddlewareAlias($middleware)) {
                $this->info("   ✓ Middleware '{$middleware}' is registered");
                $passed++;
            } else {
                $issues[] = "Middleware '{$middleware}' is not registered";
            }
        }
        $this->newLine();

        // Test 3: Verify role-based routes
        $this->info('3. Verifying role-based routes...');
        $roleRoutes = [
            'admin' => ['admin.dashboard', 'admin.properties.index'],
            'buyer' => ['buyer.dashboard', 'buyer.viewing.request'],
            'seller' => ['seller.dashboard', 'seller.properties.index'],
            'pva' => ['pva.dashboard', 'pva.viewings.index'],
        ];

        foreach ($roleRoutes as $role => $routes) {
            foreach ($routes as $routeName) {
                if (Route::has($routeName)) {
                    $this->info("   ✓ Route '{$routeName}' exists for role '{$role}'");
                    $passed++;
                } else {
                    $issues[] = "Route '{$routeName}' does not exist for role '{$role}'";
                }
            }
        }
        $this->newLine();

        // Test 4: Verify API routes have proper middleware
        $this->info('4. Verifying API route protection...');
        $apiRoutes = Route::getRoutes()->getRoutes();
        $unprotectedApiRoutes = [];

        foreach ($apiRoutes as $route) {
            if (str_starts_with($route->uri(), 'api/')) {
                $middleware = $route->middleware();
                // Public routes like /api/health and /api/auth/* are allowed
                if (!in_array('jwt.auth', $middleware) && 
                    !str_contains($route->uri(), 'health') && 
                    !str_contains($route->uri(), 'auth/login') &&
                    !str_contains($route->uri(), 'auth/register') &&
                    !str_contains($route->uri(), 'auth/refresh')) {
                    $unprotectedApiRoutes[] = $route->uri();
                } else {
                    $passed++;
                }
            }
        }

        if (!empty($unprotectedApiRoutes)) {
            $issues[] = "Unprotected API routes found: " . implode(', ', $unprotectedApiRoutes);
        } else {
            $this->info('   ✓ All API routes are properly protected');
        }
        $this->newLine();

        // Summary
        $this->info('=== Summary ===');
        $this->info("Passed: {$passed}");
        $this->info("Issues: " . count($issues));
        $this->newLine();

        if (!empty($issues)) {
            $this->error('Issues found:');
            foreach ($issues as $issue) {
                $this->error("  - {$issue}");
            }
            return 1;
        }

        $this->info('✓ All RBAC checks passed!');
        return 0;
    }
}

