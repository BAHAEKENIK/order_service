<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Service;
use App\Models\Review;
use App\Models\Category;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ClientDashboardController extends Controller
{
    public function myRequests()
    {
        $user = Auth::user();
        $serviceRequests = $user->clientServiceRequests()
            ->with([
                'provider:id,name,profile_photo_path',
                'service:id,title,base_price',
                'category:id,name'
            ])
            ->latest()
            ->paginate(10);

        return view('client.dashboard.my-requests', compact('serviceRequests', 'user'));
    }

    public function makeRequestIndex(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $providersQuery = User::where('role', 'provider')
            ->whereHas('providerDetail')
            ->with(['providerDetail', 'services' => function ($query) {
                $query->with('category')
                    ->where('status', 'available')
                    ->select('user_id', 'category_id', 'title')
                    ->limit(1);
            }]);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $providersQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('services', function ($serviceQuery) use ($searchTerm) {
                        $serviceQuery->where('title', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('providerDetail', function ($pdQuery) use ($searchTerm) {
                        $pdQuery->where('professional_description', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $categoryId = $request->input('category');
            $providersQuery->whereHas('services', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }

        $providers = $providersQuery->orderByDesc(
            optional(\App\Models\ProviderDetail::select('average_rating')
                ->whereColumn('user_id', 'users.id')
                ->limit(1))->average_rating ?? 'users.created_at'
        )->paginate(6)->withQueryString();

        return view('client.dashboard.make-request', compact('categories', 'providers'));
    }

    public function showProviderForRequest(User $provider)
    {
        if (!$provider->isProvider() || !$provider->providerDetail) {
            abort(404, 'Provider not found or not fully set up.');
        }

        $provider->load([
            'providerDetail',
            'services' => function ($query) {
                $query->where('status', 'available')
                    ->with('category')
                    ->orderBy('title');
            },
            'reviewsReceived' => function ($query) {
                $query->with('client:id,name,profile_photo_path')
                    ->latest()
                    ->take(5);
            }
        ]);

        $averageRating = $provider->reviewsReceived()->avg('rating');
        $reviewCount = $provider->reviewsReceived()->count();

        return view('client.dashboard.provider-profile-request', compact('provider', 'averageRating', 'reviewCount'));
    }

    public function createServiceRequestForm(User $provider, Service $service = null)
    {
        if (!$provider->isProvider()) {
            abort(404);
        }

        $client = Auth::user();
        $selectedCategory = null;

        if ($service) {
            $selectedCategory = $service->category;
        } elseif ($provider->services()->where('status', 'available')->exists()) {
            $selectedCategory = $provider->services()->where('status', 'available')->first()->category;
        }

        $categories = Category::orderBy('name')->get();

        return view('client.dashboard.create-service-request', compact(
            'provider',
            'client',
            'service',
            'categories',
            'selectedCategory'
        ));
    }

    public function storeServiceRequest(Request $request, User $provider)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'service_id' => 'nullable|exists:services,id',
            'service_title_display' => 'nullable|string|max:255',
            'desired_date_time' => 'nullable|date|after_or_equal:today',
            'proposed_budget' => 'nullable|numeric|min:0',
            'description' => 'required|string|min:10',
        ]);

        ServiceRequest::create([
            'client_id' => Auth::id(),
            'provider_id' => $provider->id,
            'service_id' => $validated['service_id'] ?? null,
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'address' => $request->address ?? Auth::user()->address,
            'city' => $request->city ?? Auth::user()->city,
            'desired_date_time' => $validated['desired_date_time'] ?? null,
            'proposed_budget' => $validated['proposed_budget'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('client.requests.my')
            ->with('success', 'Service request sent to ' . $provider->name . ' successfully!');
    }

    public function messages()
    {
        $userId = Auth::id();
        $involvedServiceRequestIds = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->distinct()
            ->pluck('service_request_id');

        $serviceRequestsWithConversations = ServiceRequest::whereIn('id', $involvedServiceRequestIds)
            ->with([
                'provider:id,name,profile_photo_path',
                'client:id,name,profile_photo_path',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('service_request_id', 'service_requests.id')
                    ->latest()
                    ->limit(1)
            )
            ->paginate(10);

        return view('client.dashboard.messages', compact('serviceRequestsWithConversations', 'userId'));
    }

    public function showChat(ServiceRequest $serviceRequest)
    {
        $currentUser = Auth::user();
        if ($serviceRequest->client_id !== $currentUser->id && $serviceRequest->provider_id !== $currentUser->id) {
            abort(403, 'Unauthorized action.');
        }

        $serviceRequest->load([
            'messages' => function ($query) {
                $query->with(['sender:id,name,profile_photo_path'])
                    ->orderBy('created_at', 'asc');
            },
            'client:id,name,profile_photo_path',
            'provider:id,name,profile_photo_path'
        ]);

        $serviceRequest->messages()
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $otherParty = null;
        if ($serviceRequest->client_id === $currentUser->id) {
            $otherParty = $serviceRequest->provider;
        } elseif ($serviceRequest->provider_id === $currentUser->id) {
            $otherParty = $serviceRequest->client;
        }

        if (!$otherParty) {
            Log::warning("Could not determine other party for chat on ServiceRequest ID: " . $serviceRequest->id);
            $otherParty = new User(['name' => 'Site Support']);
        }

        return view('client.dashboard.chat', compact('serviceRequest', 'otherParty'));
    }

    public function storeMessage(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id() && $serviceRequest->provider_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['content' => 'required|string|max:2000']);
        $currentUser = Auth::user();
        $receiverId = ($serviceRequest->client_id === $currentUser->id)
            ? $serviceRequest->provider_id
            : $serviceRequest->client_id;

        Message::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $currentUser->id,
            'receiver_id' => $receiverId,
            'content' => $request->content,
        ]);

        return back();
    }

    public function chatWithProvider(User $provider)
    {
        if (!$provider->isProvider() && !$provider->isAdmin()) {
            abort(404, 'User not found or not a service provider/admin.');
        }

        $client = Auth::user();
        $descriptionForAdminChat = 'Support chat with Admin';

        $existingRequest = ServiceRequest::where('client_id', $client->id)
            ->where('provider_id', $provider->id)
            ->when($provider->isAdmin(), function ($query) use ($descriptionForAdminChat) {
                return $query->where('description', $descriptionForAdminChat);
            })
            ->when(!$provider->isAdmin(), function ($query) {
                return $query->whereNotIn('status', ['completed', 'cancelled_by_client', 'cancelled_by_provider', 'rejected']);
            })
            ->latest('updated_at')
            ->first();

        if ($existingRequest) {
            return redirect()->route('client.messages.chat', $existingRequest);
        }

        $categoryForChat = $provider->isAdmin()
            ? (Category::where('slug', 'site-support')->first() ?? Category::firstOrCreate([
                'name' => 'Site Support',
                'slug' => 'site-support'
            ]))
            : ($provider->services()->where('status', 'available')->first()->category ?? Category::first());

        if (!$categoryForChat) {
            return redirect()->back()->with('error', 'Cannot initiate chat. System configuration error.');
        }

        $newServiceRequest = ServiceRequest::create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'category_id' => $categoryForChat->id,
            'description' => $provider->isAdmin()
                ? $descriptionForAdminChat
                : 'Initial contact with ' . $provider->name,
            'address' => $client->address ?? 'N/A',
            'city' => $client->city ?? 'N/A',
            'status' => 'inquiry',
        ]);

        return redirect()->route('client.messages.chat', $newServiceRequest);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('client.dashboard.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'surname' => 'nullable|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages(['current_password' => __('auth.password')]);
        }

        $fullName = $validated['name'] . (!empty($validated['surname']) ? ' ' . $validated['surname'] : '');
        $user->name = $fullName;
        $user->email = $validated['email'];

        if (!empty($validated['phone_number'])) $user->phone_number = $validated['phone_number'];
        if (!empty($validated['address'])) $user->address = $validated['address'];
        if (!empty($validated['city'])) $user->city = $validated['city'];

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos/' . $user->id, 'public');
            $user->profile_photo_path = $path;
        }

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('client.profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function destroyAccount(Request $request)
    {
        $user = Auth::user();
        $request->validate(['password_delete' => 'required|string']);

        if (!Hash::check($request->password_delete, $user->password)) {
            return back()->withErrors(['password_delete' => 'The provided password does not match.'])
                ->with('show_delete_modal', true);
        }

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->deleteDirectory('profile-photos/' . $user->id);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->delete();

        return redirect()->route('welcome')->with('status', 'Your account has been deleted.');
    }

    public function showServiceRequestDetail(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $serviceRequest->load(
            'provider:id,name',
            'provider.providerDetail:user_id,professional_description,average_rating',
            'service:id,title,description,base_price',
            'category:id,name',
            'review',
            'messages.sender:id,name'
        );

        $cancellableStatuses = ['pending', 'accepted', 'inquiry'];
        $completableStatuses = ['accepted', 'in_progress'];

        return view('client.dashboard.service-request-detail', compact(
            'serviceRequest',
            'cancellableStatuses',
            'completableStatuses'
        ));
    }

    public function cancelServiceRequest(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $cancellableStatuses = ['pending', 'accepted', 'inquiry'];
        if (!in_array($serviceRequest->status, $cancellableStatuses)) {
            return redirect()->route('client.requests.detail', $serviceRequest)
                ->with('error', 'This request cannot be cancelled.');
        }

        $serviceRequest->status = 'cancelled_by_client';
        $serviceRequest->save();

        return redirect()->route('client.requests.detail', $serviceRequest)
            ->with('success', 'Service request cancelled.');
    }

    public function updateServiceRequestStatusByClient(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['status' => ['required', 'string', 'in:completed']]);
        $completableStatuses = ['accepted', 'in_progress'];

        if (!in_array($serviceRequest->status, $completableStatuses)) {
            return redirect()->route('client.requests.detail', $serviceRequest)
                ->with('error', 'Cannot mark as completed from current status.');
        }

        $serviceRequest->status = $request->status;
        $serviceRequest->save();

        return redirect()->route('client.requests.detail', $serviceRequest)
            ->with('success', 'Service request marked as completed.');
    }

    public function createReview(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id()) {
            abort(403);
        }

        if ($serviceRequest->status !== 'completed') {
            return redirect()->route('client.requests.detail', $serviceRequest)
                ->with('error', 'This service request is not yet completed.');
        }

        if ($serviceRequest->review()->exists()) {
            return redirect()->route('client.requests.detail', $serviceRequest)
                ->with('info', 'You have already reviewed this service.');
        }

        return view('client.dashboard.create-review', compact('serviceRequest'));
    }

    public function storeReview(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->client_id !== Auth::id() ||
            $serviceRequest->status !== 'completed' ||
            $serviceRequest->review()->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'service_request_id' => $serviceRequest->id,
            'client_id' => Auth::id(),
            'provider_id' => $serviceRequest->provider_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        $providerUser = User::find($serviceRequest->provider_id);
        if ($providerUser && $providerUser->providerDetail) {
            $newAvgRating = $providerUser->reviewsReceived()->avg('rating');
            $providerUser->providerDetail->update(['average_rating' => round($newAvgRating, 1)]);
        }

        return redirect()->route('client.requests.detail', $serviceRequest)
            ->with('success', 'Thank you for your review!');
    }
}
