<?php

use Illuminate\Support\Facades\Route;
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
    Route::match(['put', 'post'], '/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/avatar', [App\Http\Controllers\ProfileController::class, 'removeAvatar'])->name('avatar.remove');
});

// Public Valuation Booking Routes (no auth required)
Route::prefix('valuation')->name('valuation.')->group(function () {
    Route::get('/booking', [App\Http\Controllers\ValuationController::class, 'showBookingForm'])->name('booking');
    Route::post('/booking', [App\Http\Controllers\ValuationController::class, 'storeBooking'])->name('booking.store');
    Route::get('/success', [App\Http\Controllers\ValuationController::class, 'bookingSuccess'])->name('booking.success');
});

// Admin Routes
Route::middleware(['auth', 'role.web:admin,agent'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    
    // Agent Dashboard (separate from admin)
    Route::prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'agentDashboard'])->name('dashboard');
    });
    
    // User Management Routes (Admin Only)
    Route::middleware(['role.web:admin'])->group(function () {
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users.index');
    });
    
    // Valuation Management Routes
    Route::get('/valuations', [App\Http\Controllers\AdminController::class, 'valuations'])->name('valuations.index');
    Route::get('/valuations/{id}', [App\Http\Controllers\AdminController::class, 'showValuation'])->name('valuations.show');
    Route::post('/valuations/{id}/schedule', [App\Http\Controllers\AdminController::class, 'updateValuationSchedule'])->name('valuations.schedule');
    Route::get('/valuations/{id}/valuation-form', [App\Http\Controllers\AdminController::class, 'showValuationForm'])->name('valuations.valuation-form');
    Route::get('/valuations/{id}/onboarding', [App\Http\Controllers\AdminController::class, 'showValuationForm'])->name('valuations.onboarding');
    Route::post('/valuations/{id}/valuation-form', [App\Http\Controllers\AdminController::class, 'storeValuationForm'])->name('valuations.valuation-form.store');
    Route::post('/valuations/{id}/onboarding', [App\Http\Controllers\AdminController::class, 'storeValuationForm'])->name('valuations.onboarding.store');
    
    // Property Management Routes
    Route::get('/properties', [App\Http\Controllers\AdminController::class, 'properties'])->name('properties.index');
    Route::get('/properties/{id}', [App\Http\Controllers\AdminController::class, 'showProperty'])->name('properties.show');
    Route::post('/properties/{id}/request-instruction', [App\Http\Controllers\AdminController::class, 'requestInstruction'])->name('properties.request-instruction');
    Route::post('/properties/{id}/send-post-valuation-email', [App\Http\Controllers\AdminController::class, 'sendPostValuationEmail'])->name('properties.send-post-valuation-email');
    
    // HomeCheck Management Routes
    Route::get('/properties/{id}/schedule-homecheck', [App\Http\Controllers\AdminController::class, 'showScheduleHomeCheck'])->name('properties.schedule-homecheck');
    Route::post('/properties/{id}/schedule-homecheck', [App\Http\Controllers\AdminController::class, 'storeScheduleHomeCheck'])->name('properties.schedule-homecheck.store');
    Route::get('/properties/{id}/complete-homecheck', [App\Http\Controllers\AdminController::class, 'showCompleteHomeCheck'])->name('properties.complete-homecheck');
    Route::post('/properties/{id}/complete-homecheck', [App\Http\Controllers\AdminController::class, 'storeCompleteHomeCheck'])->name('properties.complete-homecheck.store');
    
    // Listing Management Routes
    Route::get('/properties/{id}/listing-upload', [App\Http\Controllers\AdminController::class, 'showListingUpload'])->name('properties.listing-upload');
    Route::post('/properties/{id}/listing-upload', [App\Http\Controllers\AdminController::class, 'storeListingUpload'])->name('properties.listing-upload.store');
    
    // RTDF Generation Routes
    Route::get('/properties/{id}/generate-rtdf', [App\Http\Controllers\AdminController::class, 'generateRTDF'])->name('properties.generate-rtdf');
    
    // AML Document Management Routes
    Route::get('/aml-checks', [App\Http\Controllers\AdminController::class, 'amlChecks'])->name('aml-checks.index');
    Route::get('/aml-checks/{id}', [App\Http\Controllers\AdminController::class, 'showAmlCheck'])->name('aml-checks.show');
    Route::post('/aml-checks/{id}/verify', [App\Http\Controllers\AdminController::class, 'verifyAmlCheck'])->name('aml-checks.verify');
    Route::get('/aml-documents/{documentId}/serve', [App\Http\Controllers\AdminController::class, 'serveAmlDocument'])->name('aml-documents.serve');
    Route::post('/properties/{id}/publish', [App\Http\Controllers\AdminController::class, 'publishListing'])->name('properties.publish');
});

// Allow sellers to view their own live listings (must be before buyer routes to take precedence)
Route::middleware(['auth'])->get('/buyer/property/{id}/viewing-request', [App\Http\Controllers\BuyerController::class, 'showViewingRequest'])->name('buyer.viewing.request');

// Buyer Routes
Route::middleware(['auth', 'role.web:buyer,both'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\BuyerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\BuyerController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\BuyerController::class, 'updateProfile'])->name('profile.update');
    Route::get('/property/{id}/offer', [App\Http\Controllers\BuyerController::class, 'makeOffer'])->name('make-offer');
    Route::post('/property/{id}/offer', [App\Http\Controllers\BuyerController::class, 'storeOffer'])->name('offer.store');
    Route::get('/offer/{id}/confirmation', [App\Http\Controllers\BuyerController::class, 'offerConfirmation'])->name('offer.confirmation');
    
    // AML Document Upload Routes
    Route::get('/aml-upload', [App\Http\Controllers\BuyerController::class, 'showAmlUpload'])->name('aml.upload');
    Route::post('/aml-upload', [App\Http\Controllers\BuyerController::class, 'storeAmlUpload'])->name('aml.upload.store');
    
    // Viewing Request Routes
    Route::get('/property/{id}/viewing-request', [App\Http\Controllers\BuyerController::class, 'showViewingRequest'])->name('viewing.request');
    Route::post('/property/{id}/viewing-request', [App\Http\Controllers\BuyerController::class, 'storeViewingRequest'])->name('viewing.request.store');
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
    Route::get('/offer/{id}/decision/success', [App\Http\Controllers\SellerController::class, 'showOfferDecisionSuccess'])->name('offer.decision.success');
    Route::get('/property/{id}/homecheck', [App\Http\Controllers\SellerController::class, 'showRoomUpload'])->name('homecheck.upload');
    Route::get('/property/{id}/homecheck-report', [App\Http\Controllers\SellerController::class, 'showHomecheckReport'])->name('homecheck.report');
    Route::post('/property/{id}/homecheck', [App\Http\Controllers\SellerController::class, 'storeRoomUpload'])->name('homecheck.store');
    
    // AML Documents & Solicitor Details Routes
    Route::get('/property/{id}/aml-upload', [App\Http\Controllers\SellerController::class, 'showAmlUpload'])->name('aml.upload');
    Route::post('/property/{id}/aml-upload', [App\Http\Controllers\SellerController::class, 'storeAmlUpload'])->name('aml.upload.store');
    Route::get('/property/{id}/solicitor-details', [App\Http\Controllers\SellerController::class, 'showSolicitorDetails'])->name('solicitor.details');
    Route::post('/property/{id}/solicitor-details', [App\Http\Controllers\SellerController::class, 'storeSolicitorDetails'])->name('solicitor.details.store');
});

// PVA Routes
Route::middleware(['auth', 'role.web:pva'])->prefix('pva')->name('pva.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\PVAController::class, 'dashboard'])->name('dashboard');
    
    // Viewing Management Routes
    Route::get('/viewings', [App\Http\Controllers\PVAController::class, 'viewings'])->name('viewings.index');
    Route::get('/viewings/{id}', [App\Http\Controllers\PVAController::class, 'showViewing'])->name('viewings.show');
    Route::post('/viewings/{id}/confirm', [App\Http\Controllers\PVAController::class, 'confirmViewing'])->name('viewings.confirm');
    Route::get('/viewings/{id}/feedback', [App\Http\Controllers\PVAController::class, 'showFeedback'])->name('viewings.feedback');
    Route::post('/viewings/{id}/feedback', [App\Http\Controllers\PVAController::class, 'storeFeedback'])->name('viewings.feedback.store');
});
