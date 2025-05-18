<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Client\ClientDashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'storeUser'])->name('register.storeUser');
    Route::get('provider-registration', [RegisterController::class, 'showProviderRegistrationForm'])->name('provider.register.form');
    Route::post('provider-registration', [RegisterController::class, 'storeProviderDetails'])->name('provider.register.store');
    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});
Route::post('logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Contact Form & Learn More (Public)
Route::get('/contact-us', [ContactFormController::class, 'create'])->name('contact');
Route::post('/contact-us', [ContactFormController::class, 'store'])->name('contact.store');
Route::get('/learn-more', function () { return view('learn-more'); })->name('learn-more');

// Authenticated User Dashboards
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->isAdmin()) { return redirect()->route('admin.dashboard'); }
        if ($user->isProvider()) { return redirect()->route('provider.dashboard'); }
        return redirect()->route('client.requests.my');
    })->name('dashboard');

    // Client Specific Routes
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/my-requests', [ClientDashboardController::class, 'myRequests'])->name('requests.my');
        Route::get('/requests/{serviceRequest}', [ClientDashboardController::class, 'showServiceRequestDetail'])->name('requests.detail');
        Route::patch('/requests/{serviceRequest}/cancel', [ClientDashboardController::class, 'cancelServiceRequest'])->name('request.cancel');
        Route::patch('/requests/{serviceRequest}/update-status', [ClientDashboardController::class, 'updateServiceRequestStatusByClient'])->name('request.update-status');
        Route::get('/make-request', [ClientDashboardController::class, 'makeRequestIndex'])->name('request.make');
        Route::get('/request-service/provider/{provider}/{service?}', [ClientDashboardController::class, 'createServiceRequestForm'])->name('request.service.form');
        Route::post('/request-service/provider/{provider}', [ClientDashboardController::class, 'storeServiceRequest'])->name('request.service.store');
        Route::get('/provider-details/{provider}', [ClientDashboardController::class, 'showProviderForRequest'])->name('provider.details');
        Route::get('/messages', [ClientDashboardController::class, 'messages'])->name('messages.index');
        Route::get('/messages/chat/{serviceRequest}', [ClientDashboardController::class, 'showChat'])->name('messages.chat');
        Route::post('/messages/chat/{serviceRequest}', [ClientDashboardController::class, 'storeMessage'])->name('messages.store');

        // For contacting listed providers (from make-request or provider-details page)
        Route::get('/contact-provider/{provider}', [ClientDashboardController::class, 'chatWithProvider'])->name('messages.with-provider');

        // NEW: Route for client to contact admin (via notification bell)
        // This route will also use chatWithProvider, but we'll pass the Admin User object to it.
        // The controller will fetch the admin.
        Route::get('/contact-admin', function() {
            // Assuming your admin has a specific ID (e.g., 1) or a unique role identifier you query for.
            // If you have only one admin with 'admin' role:
            $admin = App\Models\User::where('role', 'admin')->first();
            if(!$admin){
                 // Fallback or error if admin not found
                return redirect()->back()->with('error', 'Admin user not found to initiate chat.');
            }
            // Now, we'll use the same controller method as contacting a provider, but pass the admin user.
            // For this to work cleanly without changing chatWithProvider signature too much,
            // it expects a User object. So, we just call it here directly for the specific admin route.
            $controller = app(ClientDashboardController::class);
            return $controller->chatWithProvider($admin); // Pass the Admin User object

        })->name('contact.admin');


        Route::get('/profile', [ClientDashboardController::class, 'profile'])->name('profile.edit');
        Route::post('/profile', [ClientDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::delete('/profile', [ClientDashboardController::class, 'destroyAccount'])->name('profile.destroy');
        Route::get('/review-service/{serviceRequest}', [ClientDashboardController::class, 'createReview'])->name('review.create');
        Route::post('/review-service/{serviceRequest}', [ClientDashboardController::class, 'storeReview'])->name('review.store');
    });

    Route::get('/admin/dashboard', function () { return "Welcome Admin!"; })->name('admin.dashboard');
    Route::get('/provider/dashboard', function () { return "Welcome Provider!"; })->name('provider.dashboard');
});
