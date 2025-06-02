<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Provider\ProviderDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () { return view('welcome'); })->name('welcome');
Route::get('/contact-us', [ContactFormController::class, 'create'])->name('contact');
Route::post('/contact-us', [ContactFormController::class, 'store'])->name('contact.store');
Route::get('/learn-more', function () { return view('learn-more'); })->name('learn-more');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'storeUser'])->name('register.storeUser');
    Route::get('provider-registration', [RegisterController::class, 'showProviderRegistrationForm'])->name('provider.register.form');
    Route::post('provider-registration', [RegisterController::class, 'storeProviderDetails'])->name('provider.register.storeDetails');
    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->isAdmin()) { return redirect()->route('admin.dashboard'); }
        if ($user->isProvider()) { return redirect()->route('provider.requests.index'); }
        return redirect()->route('client.requests.my');
    })->name('dashboard');

    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/my-requests', [ClientDashboardController::class, 'myRequests'])->name('requests.my');
        Route::get('/requests/{serviceRequest}', [ClientDashboardController::class, 'showServiceRequestDetail'])->name('requests.detail');
        Route::patch('/requests/{serviceRequest}/cancel', [ClientDashboardController::class, 'cancelServiceRequest'])->name('request.cancel');
        Route::patch('/requests/{serviceRequest}/update-status', [ClientDashboardController::class, 'updateServiceRequestStatusByClient'])->name('request.update-status');
        Route::get('/make-request', [ClientDashboardController::class, 'makeRequestIndex'])->name('request.make');
        Route::get('/provider-details/{provider}', [ClientDashboardController::class, 'showProviderForRequest'])->name('provider.details');
        Route::get('/request-service/provider/{provider}/{service?}', [ClientDashboardController::class, 'createServiceRequestForm'])->name('request.service.form');
        Route::post('/request-service/provider/{provider}', [ClientDashboardController::class, 'storeServiceRequest'])->name('request.service.store');
        Route::get('/messages', [ClientDashboardController::class, 'messages'])->name('messages.index');
        Route::get('/messages/chat/{serviceRequest}', [ClientDashboardController::class, 'showChat'])->name('messages.chat');
        Route::post('/messages/chat/{serviceRequest}', [ClientDashboardController::class, 'storeMessage'])->name('messages.store');
        Route::get('/contact-provider/{provider}', [ClientDashboardController::class, 'chatWithProvider'])->name('messages.with-provider');
        Route::get('/contact-admin', function() { $admin = App\Models\User::where('role', 'admin')->first(); if(!$admin){ return redirect()->back()->with('error', 'Admin user not found.'); } $controller = app(ClientDashboardController::class); return $controller->chatWithProvider($admin); })->name('contact.admin');
        Route::get('/profile', [ClientDashboardController::class, 'profile'])->name('profile.edit'); Route::post('/profile', [ClientDashboardController::class, 'updateProfile'])->name('profile.update'); Route::delete('/profile', [ClientDashboardController::class, 'destroyAccount'])->name('profile.destroy');
        Route::get('/review-service/{serviceRequest}', [ClientDashboardController::class, 'createReview'])->name('review.create'); Route::post('/review-service/{serviceRequest}', [ClientDashboardController::class, 'storeReview'])->name('review.store');
    });

    Route::prefix('provider')->name('provider.')->group(function () {
        Route::get('/requests', [ProviderDashboardController::class, 'requests'])->name('requests.index');
        Route::get('/requests/{serviceRequest}', [ProviderDashboardController::class, 'showRequestDetail'])->name('requests.detail');
        Route::patch('/requests/{serviceRequest}/status', [ProviderDashboardController::class, 'updateRequestStatus'])->name('requests.update-status');
        Route::get('/my-services', [ProviderDashboardController::class, 'myServices'])->name('services.index'); Route::get('/my-services/create', [ProviderDashboardController::class, 'createService'])->name('services.create'); Route::post('/my-services', [ProviderDashboardController::class, 'storeService'])->name('services.store'); Route::get('/my-services/{service}/edit', [ProviderDashboardController::class, 'editService'])->name('services.edit'); Route::put('/my-services/{service}', [ProviderDashboardController::class, 'updateService'])->name('services.update'); Route::delete('/my-services/{service}', [ProviderDashboardController::class, 'destroyService'])->name('services.destroy');
        Route::get('/inbox', [ProviderDashboardController::class, 'inbox'])->name('messages.index'); Route::get('/inbox/chat/{serviceRequest}', [ProviderDashboardController::class, 'showChat'])->name('messages.chat'); Route::post('/inbox/chat/{serviceRequest}', [ProviderDashboardController::class, 'storeMessage'])->name('messages.store');
        Route::get('/contact-admin', function() { $admin = \App\Models\User::where('role', 'admin')->first(); if(!$admin) { return redirect()->route('provider.messages.index')->with('error', 'Admin not found.'); } $controller = app(ProviderDashboardController::class); return $controller->chatWithAdmin($admin); })->name('contact.admin');
        Route::get('/profile', [ProviderDashboardController::class, 'profile'])->name('profile.edit'); Route::post('/profile', [ProviderDashboardController::class, 'updateProfile'])->name('profile.update'); Route::delete('/profile', [ProviderDashboardController::class, 'destroyAccount'])->name('profile.destroy');
        Route::get('/reviews', [ProviderDashboardController::class, 'reviews'])->name('reviews.index');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminDashboardController::class, 'manageUsers'])->name('users.index');
        Route::get('/users/{user}', [AdminDashboardController::class, 'showUser'])->name('users.show');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy');
        Route::delete('/users', [AdminDashboardController::class, 'bulkDestroyUsers'])->name('users.bulk-destroy');
        Route::get('/categories', [AdminDashboardController::class, 'manageCategories'])->name('categories.index');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
        Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');
        Route::get('/contact-messages', [AdminDashboardController::class, 'contactMessages'])->name('contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [AdminDashboardController::class, 'showContactMessage'])->name('contact-messages.show');
        Route::post('/contact-messages/{contactMessage}/reply', [AdminDashboardController::class, 'replyContactMessage'])->name('contact-messages.reply');
        Route::get('/inbox', [AdminDashboardController::class, 'adminInbox'])->name('inbox.index');
        Route::get('/inbox/chat/{serviceRequest}', [AdminDashboardController::class, 'showAdminChat'])->name('inbox.chat');
        Route::post('/inbox/chat/{serviceRequest}', [AdminDashboardController::class, 'storeAdminMessage'])->name('inbox.store');
        Route::get('/chat-with/{user}', [AdminDashboardController::class, 'chatWithUser'])->name('users.chat');
        Route::get('/profile', [AdminDashboardController::class, 'adminProfile'])->name('profile.edit');
        Route::post('/profile', [AdminDashboardController::class, 'updateAdminProfile'])->name('profile.update');
    });
});
