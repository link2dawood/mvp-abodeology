<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// Profile Routes (for all authenticated users)
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::put('/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/avatar', [App\Http\Controllers\ProfileController::class, 'removeAvatar'])->name('avatar.remove');
});

// Public Valuation Booking Routes (no auth required)
Route::prefix('valuation')->name('valuation.')->group(function () {
    Route::get('/book', [App\Http\Controllers\ValuationController::class, 'showBookingForm'])->name('booking');
    Route::post('/book', [App\Http\Controllers\ValuationController::class, 'storeBooking'])->name('booking.store');
    Route::get('/success', [App\Http\Controllers\ValuationController::class, 'bookingSuccess'])->name('booking.success');
});

// Admin Routes
Route::middleware(['auth', 'role.web:admin,agent'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    
    // Valuation Management Routes
    Route::get('/valuations', [App\Http\Controllers\AdminController::class, 'valuations'])->name('valuations.index');
    Route::get('/valuations/{id}', [App\Http\Controllers\AdminController::class, 'showValuation'])->name('valuations.show');
    Route::get('/valuations/{id}/onboarding', [App\Http\Controllers\AdminController::class, 'showValuationOnboarding'])->name('valuations.onboarding');
    Route::post('/valuations/{id}/onboarding', [App\Http\Controllers\AdminController::class, 'storeValuationOnboarding'])->name('valuations.onboarding.store');
    
    // Property Management Routes
    Route::get('/properties/{id}', [App\Http\Controllers\AdminController::class, 'showProperty'])->name('properties.show');
    Route::post('/properties/{id}/request-instruction', [App\Http\Controllers\AdminController::class, 'requestInstruction'])->name('properties.request-instruction');
});

// Buyer Routes
Route::middleware(['auth', 'role.web:buyer,both'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\BuyerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\BuyerController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\BuyerController::class, 'updateProfile'])->name('profile.update');
    Route::get('/property/{id}/offer', [App\Http\Controllers\BuyerController::class, 'makeOffer'])->name('make-offer');
    Route::post('/property/{id}/offer', [App\Http\Controllers\BuyerController::class, 'storeOffer'])->name('offer.store');
});

// Seller Routes
Route::middleware(['auth', 'role.web:seller,both'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SellerController::class, 'dashboard'])->name('dashboard');
    
    // Property Management Routes
    Route::get('/properties', [App\Http\Controllers\SellerController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [App\Http\Controllers\SellerController::class, 'createProperty'])->name('properties.create');
    Route::post('/properties', [App\Http\Controllers\SellerController::class, 'storeProperty'])->name('properties.store');
    Route::get('/properties/{id}', [App\Http\Controllers\SellerController::class, 'showProperty'])->name('properties.show');
    
    Route::get('/property/{id}/onboarding', [App\Http\Controllers\SellerController::class, 'showOnboarding'])->name('onboarding'); //signup
    Route::post('/property/{id}/onboarding', [App\Http\Controllers\SellerController::class, 'storeOnboarding'])->name('onboarding.store');
    Route::get('/property/{id}/instruct', [App\Http\Controllers\SellerController::class, 'instruct'])->name('instruct');
    Route::post('/property/{id}/instruct', [App\Http\Controllers\SellerController::class, 'storeInstruct'])->name('instruct.store');
    Route::get('/instruct', [App\Http\Controllers\SellerController::class, 'instruct'])->name('instruct.general'); // Fallback for non-property-specific
    Route::post('/instruct', [App\Http\Controllers\SellerController::class, 'storeInstruct'])->name('instruct.store.general'); // Fallback
    Route::get('/offer/{id}/decision', [App\Http\Controllers\SellerController::class, 'showOfferDecision'])->name('offer.decision');
    Route::put('/offer/{id}/decision', [App\Http\Controllers\SellerController::class, 'handleOfferDecision'])->name('offer.decision');
    Route::get('/property/{id}/homecheck', [App\Http\Controllers\SellerController::class, 'showRoomUpload'])->name('homecheck.upload');
    Route::post('/property/{id}/homecheck', [App\Http\Controllers\SellerController::class, 'storeRoomUpload'])->name('homecheck.store');
});

// PVA Routes
Route::middleware(['auth', 'role.web:pva'])->prefix('pva')->name('pva.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\PVAController::class, 'dashboard'])->name('dashboard');
});
