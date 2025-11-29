<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ViewingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'environment' => app()->environment(),
        'version' => '1.0.0',
    ]);
});

// Public routes
Route::prefix('auth')->group(function () {
    // /auth/login: 5 per 10 minutes
    Route::post('/login', [AuthController::class, 'login']); // Rate limit handled in controller
    
    // /auth/register: 5 per hour
    Route::post('/register', [AuthController::class, 'register']); // Rate limit handled in controller
    
    // Refresh token (general rate limit applies)
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('throttle:10,1'); // 10 attempts per minute
});

// Protected routes - Using jwt.auth middleware
// jwt.auth handles: JWT signature check, expiry check, role check, adds user to request
// Global rate limit: 60 requests per minute per user, 200 requests per minute per IP
Route::middleware(['jwt.auth', 'api.ratelimit:60,1'])->group(function () {
    // Auth routes (no role restriction)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Dashboard routes (role-based using jwt.auth with role parameters)
    // jwt.auth middleware checks role automatically if provided as parameter
    // /admin/*: 100 per minute
    Route::middleware(['jwt.auth:admin', 'api.ratelimit:100,1'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Admin dashboard']);
        });
    });

    Route::middleware('jwt.auth:buyer')->prefix('buyer')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Buyer dashboard']);
        });
    });

    Route::middleware('jwt.auth:seller')->prefix('seller')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Seller dashboard']);
        });
    });

    Route::middleware('jwt.auth:pva')->prefix('pva')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'PVA dashboard']);
        });
    });

    // Property routes - Role + Ownership checks
    // All endpoints check BOTH role AND ownership
    Route::prefix('properties')->group(function () {
        // List properties (role-based filtering handled in controller)
        Route::get('/', [PropertyController::class, 'index']);
        
        // Search properties: 60 per minute
        Route::get('/search', [PropertyController::class, 'search'])
            ->middleware('api.ratelimit:60,1');
        
        // View specific property (role + ownership check in controller)
        Route::get('/{id}', [PropertyController::class, 'show']);

        // Create property (requires seller role, ownership auto-assigned)
        // jwt.auth checks role: seller, admin, or agent
        Route::middleware('jwt.auth:seller,admin,agent')->post('/', [PropertyController::class, 'store']);

        // Update property (requires seller role + ownership OR admin/agent)
        // jwt.auth checks role, ownership middleware checks ownership
        Route::middleware('jwt.auth:seller,admin,agent')->put('/{id}', [PropertyController::class, 'update'])
            ->middleware('ownership:Property,id,false'); // false = allow admin/agent to bypass

        // Delete property (requires seller role + ownership OR admin)
        Route::middleware('jwt.auth:seller,admin')->delete('/{id}', [PropertyController::class, 'destroy']);
    });

    // Buyer routes - Strict ownership enforcement
    // GET /buyers/{buyer_id}/offers - Buyers can only see their own offers
    // Backend enforces: JWT user_id must equal buyer_id OR role must be admin/agent
    Route::prefix('buyers')->group(function () {
        Route::middleware('user.ownership:buyer_id')->group(function () {
            // Get offers for a specific buyer
            // Ownership enforced: JWT user_id == buyer_id OR role is admin/agent
            Route::get('/{buyer_id}/offers', [OfferController::class, 'getBuyerOffers']);
        });
    });

    // Offer routes - Role + Ownership checks
    // /offers: 20 per hour
    Route::middleware('api.ratelimit:20,60')->prefix('offers')->group(function () {
        // List offers (role-based filtering handled in controller)
        Route::get('/', [OfferController::class, 'index']);
        
        // View specific offer (uses canBeViewedBy method)
        Route::get('/{id}', [OfferController::class, 'show']);

        // Create offer (requires buyer role, ownership auto-assigned)
        Route::middleware('jwt.auth:buyer,admin,agent')->post('/', [OfferController::class, 'store']);

        // Update/respond to offer (requires seller role + ownership of property OR admin/agent)
        Route::middleware('jwt.auth:seller,admin,agent')->put('/{id}', [OfferController::class, 'update']);

        // Withdraw offer (requires buyer role + ownership OR admin)
        Route::middleware('jwt.auth:buyer,admin')->post('/{id}/withdraw', [OfferController::class, 'withdraw']);
    });

    // Viewing routes - Role + Ownership checks
    Route::prefix('viewings')->group(function () {
        // List viewings (role-based filtering handled in controller)
        Route::get('/', [ViewingController::class, 'index']);
        
        // View specific viewing (uses canBeViewedBy method)
        Route::get('/{id}', [ViewingController::class, 'show']);

        // Book viewing (requires buyer role, ownership auto-assigned)
        Route::middleware('jwt.auth:buyer,admin,agent')->post('/', [ViewingController::class, 'store']);

        // Submit PVA feedback (requires pva role + ownership OR admin/agent)
        Route::middleware('jwt.auth:pva,admin,agent')->post('/{id}/feedback', [ViewingController::class, 'submitFeedback']);

        // Update viewing (role + ownership check in controller)
        Route::middleware('jwt.auth:buyer,seller,pva,admin,agent')->put('/{id}', [ViewingController::class, 'update']);
    });
});
