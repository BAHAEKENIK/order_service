<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceRequest;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use App\Models\Message;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProviderDashboardController extends Controller
{
    public function requests(Request $request)
    {
        $provider = Auth::user();
        $query = $provider->providerServiceRequests()
            ->with(['client:id,name,profile_photo_path', 'service:id,title', 'category:id,name'])
            ->whereNotIn('status', ['cancelled_by_client', 'completed']);

        if ($request->filled('filter_status') && $request->input('filter_status') !== 'all') {
            $query->where('status', $request->input('filter_status'));
        } else {
            $query->whereIn('status', ['pending', 'inquiry', 'accepted', 'in_progress']);
        }

        $serviceRequests = $query->latest('created_at')->paginate(5);
        $totalOpportunities = $provider->providerServiceRequests()->whereIn('status', ['pending', 'inquiry'])->count();
        $statuses = ['all', 'pending', 'inquiry', 'accepted', 'in_progress', 'completed', 'rejected', 'cancelled_by_client'];

        return view('provider.dashboard.requests', compact('provider', 'serviceRequests', 'totalOpportunities', 'statuses'));
    }

    public function showRequestDetail(ServiceRequest $serviceRequest)
    {
        $provider = Auth::user();
        if ($serviceRequest->provider_id !== $provider->id) {
            abort(403);
        }

        $serviceRequest->load(['client:id,name,email,phone_number,profile_photo_path', 'service', 'category']);
        $updatableStatuses = [];

        switch ($serviceRequest->status) {
            case 'pending':
            case 'inquiry':
                $updatableStatuses = ['accepted' => 'Accept', 'rejected' => 'Ignore/Reject'];
                break;
            case 'accepted':
                $updatableStatuses = ['in_progress' => 'Mark In Progress', 'completed' => 'Mark as Completed', 'rejected' => 'Reject'];
                break;
            case 'in_progress':
                $updatableStatuses = ['completed' => 'Mark as Completed'];
                break;
        }

        return view('provider.dashboard.request-detail', compact('serviceRequest', 'provider', 'updatableStatuses'));
    }

    public function updateRequestStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $provider = Auth::user();
        if ($serviceRequest->provider_id !== $provider->id) {
            abort(403);
        }

        $allowedStatuses = ['accepted', 'rejected', 'in_progress', 'completed'];
        $newStatus = $request->input('status');
        $request->validate(['status' => ['required', 'string', \Illuminate\Validation\Rule::in($allowedStatuses)]]);

        $currentStatus = $serviceRequest->status;
        $validTransition = false;

        if (($currentStatus === 'pending' || $currentStatus === 'inquiry') && in_array($newStatus, ['accepted', 'rejected'])) {
            $validTransition = true;
        } elseif ($currentStatus === 'accepted' && in_array($newStatus, ['in_progress', 'completed', 'rejected'])) {
            $validTransition = true;
        } elseif ($currentStatus === 'in_progress' && $newStatus === 'completed') {
            $validTransition = true;
        }

        if (!$validTransition) {
            return redirect()->back()->with('error', "Cannot change status from '{$currentStatus}' to '{$newStatus}'.");
        }

        $serviceRequest->status = $newStatus;
        $serviceRequest->save();
        $message = 'Request status updated to ' . Str::title(str_replace('_', ' ', $newStatus)) . '.';

        if ($newStatus === 'accepted') {
            $message = 'Request accepted!';
        } elseif ($newStatus === 'rejected') {
            $message = 'Request rejected.';
        } elseif ($newStatus === 'completed') {
            $message = 'Request marked as completed.';
        }

        if (in_array($newStatus, ['accepted', 'rejected'])) {
            return redirect()->route('provider.requests.index')->with('success', $message);
        }

        return redirect()->route('provider.requests.detail', $serviceRequest)->with('success', $message);
    }

    public function myServices()
    {
        $provider = Auth::user();
        $services = $provider->services()->with('category')->latest()->paginate(10);
        return view('provider.dashboard.my-services', compact('provider', 'services'));
    }

    public function createService()
    {
        $provider = Auth::user();
        $categories = Category::orderBy('name')->get();
        return view('provider.dashboard.create-service', compact('provider', 'categories'));
    }

    public function storeService(Request $request)
    {
        $provider = Auth::user();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:20',
            'base_price' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => ['required', Rule::in(['available', 'unavailable'])],
        ]);

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('service-images/' . $provider->id, 'public');
        }

        Service::create([
            'user_id' => $provider->id,
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'] ?? null,
            'address' => $validated['address'] ?? $provider->address,
            'city' => $validated['city'] ?? $provider->city,
            'image_path' => $imagePath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('provider.services.index')->with('success', 'Service created successfully.');
    }

    public function editService(Service $service)
    {
        $provider = Auth::user();
        if ($service->user_id !== $provider->id) {
            abort(403, 'You are not authorized to edit this service.');
        }

        $categories = Category::orderBy('name')->get();
        return view('provider.dashboard.edit-service', compact('provider', 'service', 'categories'));
    }

    public function updateService(Request $request, Service $service)
    {
        $provider = Auth::user();
        if ($service->user_id !== $provider->id) {
            abort(403, 'You are not authorized to update this service.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:20',
            'base_price' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'new_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => ['required', Rule::in(['available', 'unavailable'])],
        ]);

        $imagePath = $service->image_path;
        if ($request->hasFile('new_image_path')) {
            if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
                Storage::disk('public')->delete($service->image_path);
            }
            $imagePath = $request->file('new_image_path')->store('service-images/' . $provider->id, 'public');
        } elseif ($request->boolean('remove_existing_image') && $service->image_path) {
            if (Storage::disk('public')->exists($service->image_path)) {
                Storage::disk('public')->delete($service->image_path);
            }
            $imagePath = null;
        }

        $service->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'] ?? null,
            'address' => $validated['address'] ?? $service->address,
            'city' => $validated['city'] ?? $service->city,
            'image_path' => $imagePath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('provider.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroyService(Service $service)
    {
        $provider = Auth::user();
        if ($service->user_id !== $provider->id) {
            abort(403, 'You are not authorized to delete this service.');
        }

        if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
            Storage::disk('public')->delete($service->image_path);
        }

        $service->delete();
        return redirect()->route('provider.services.index')->with('success', 'Service deleted successfully.');
    }

    public function inbox()
    {
        $providerId = Auth::id();
        $serviceRequestIds = Message::where(function ($query) use ($providerId) {
                $query->where('sender_id', $providerId)
                    ->orWhere('receiver_id', $providerId);
            })
            ->distinct()
            ->pluck('service_request_id');

        $serviceRequestsWithConversations = ServiceRequest::whereIn('id', $serviceRequestIds)
            ->where(function($query) use ($providerId) {
                $query->where('provider_id', $providerId)
                    ->orWhere(function($subQuery) use ($providerId) {
                        $subQuery->where('client_id', $providerId)
                            ->whereHas('provider', function($adminQuery) {
                                $adminQuery->where('role', 'admin');
                            });
                    });
            })
            ->with([
                'client' => function ($query) {
                    $query->select('id', 'name', 'profile_photo_path');
                },
                'provider' => function ($query) {
                    $query->select('id', 'name', 'profile_photo_path');
                },
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('service_request_id', 'service_requests.id')
                    ->latest()->limit(1)
            )
            ->paginate(10);

        return view('provider.dashboard.inbox', compact('serviceRequestsWithConversations'));
    }

    public function showChat(ServiceRequest $serviceRequest)
    {
        $currentUser = Auth::user();
        if (!($serviceRequest->provider_id === $currentUser->id ||
            ($serviceRequest->client_id === $currentUser->id && $serviceRequest->provider->isAdmin()))) {
            abort(403, 'Unauthorized action.');
        }

        $serviceRequest->load([
            'messages' => function ($query) {
                $query->with(['sender:id,name,profile_photo_path'])->orderBy('created_at', 'asc');
            },
            'client:id,name,profile_photo_path',
            'provider:id,name,profile_photo_path'
        ]);

        $serviceRequest->messages()
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($serviceRequest->provider_id === $currentUser->id) {
            $otherParty = $serviceRequest->client;
        } else {
            $otherParty = $serviceRequest->provider;
        }

        return view('provider.dashboard.chat', compact('serviceRequest', 'otherParty'));
    }

    public function storeMessage(Request $request, ServiceRequest $serviceRequest)
    {
        $currentUser = Auth::user();
        if (!($serviceRequest->provider_id === $currentUser->id || ($serviceRequest->client_id === $currentUser->id && $serviceRequest->provider->isAdmin()))) {
            abort(403);
        }

        $request->validate(['content' => 'required|string|max:2000']);
        $receiverId = null;

        if ($serviceRequest->provider_id === $currentUser->id) {
            $receiverId = $serviceRequest->client_id;
        } elseif ($serviceRequest->client_id === $currentUser->id && $serviceRequest->provider->isAdmin()) {
            $receiverId = $serviceRequest->provider_id;
        }

        if (!$receiverId) {
            return back()->with('error', 'Could not determine message recipient.');
        }

        Message::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $currentUser->id,
            'receiver_id' => $receiverId,
            'content' => $request->content,
        ]);

        return back();
    }

    public function chatWithAdmin(User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404, 'Admin user specified is not an admin.');
        }

        $provider = Auth::user();
        $descriptionForAdminChat = 'Support chat with Admin: ' . $provider->name;
        $categoryForSupport = Category::firstOrCreate(
            ['slug' => 'admin-support'],
            ['name' => 'Admin Support']
        );

        $serviceRequest = ServiceRequest::firstOrCreate(
            [
                'client_id' => $provider->id,
                'provider_id' => $admin->id,
                'category_id' => $categoryForSupport->id,
                'description' => $descriptionForAdminChat
            ],
            [
                'address' => $provider->address ?? 'N/A',
                'city' => $provider->city ?? 'N/A',
                'status' => 'inquiry',
            ]
        );

        return redirect()->route('provider.messages.chat', $serviceRequest);
    }

    public function profile()
    {
        $provider = Auth::user()->load('providerDetail');
        return view('provider.dashboard.profile', compact('provider'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $providerDetail = $user->providerDetail ?? new ProviderDetail(['user_id' => $user->id]);

        $validatedUserData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $validatedProviderData = $request->validate([
            'professional_description' => 'nullable|string|min:10',
            'is_available' => 'nullable|boolean',
        ]);

        if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('auth.password'),
            ]);
        }

        $user->name = $validatedUserData['name'];
        $user->email = $validatedUserData['email'];
        $user->phone_number = $validatedUserData['phone_number'] ?? $user->phone_number;
        $user->address = $validatedUserData['address'] ?? $user->address;
        $user->city = $validatedUserData['city'] ?? $user->city;

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos/' . $user->id, 'public');
            $user->profile_photo_path = $path;
        }

        if (!empty($validatedUserData['new_password'])) {
            $user->password = Hash::make($validatedUserData['new_password']);
        }
        $user->save();

        $providerDetail->professional_description = $validatedProviderData['professional_description'] ?? $providerDetail->professional_description;
        $providerDetail->is_available = $request->has('is_available') ? $request->boolean('is_available') : $providerDetail->is_available ?? true;

        $currentCertificates = is_array($providerDetail->certificates) ? $providerDetail->certificates : ($providerDetail->certificates ? $providerDetail->certificates->toArray() : []);

        if ($request->filled('certificates_to_remove')) {
            $urlsToRemove = $request->input('certificates_to_remove');
            $currentCertificates = array_filter($currentCertificates, function ($cert) use ($urlsToRemove) {
                if (isset($cert['file_url']) && in_array($cert['file_url'], $urlsToRemove)) {
                    $filePath = Str::after($cert['file_url'], Storage::url(''));
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                    return false;
                }
                return true;
            });
            $currentCertificates = array_values($currentCertificates);
        }

        if ($request->hasFile('new_certificates')) {
            foreach ($request->file('new_certificates') as $file) {
                $request->validate(['new_certificates.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048']);
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('certificates/' . $user->id, $filename, 'public');
                $currentCertificates[] = [
                    'name' => $file->getClientOriginalName(),
                    'file_url' => Storage::url($path),
                    'issued_date' => now()->toDateString(),
                ];
            }
        }

        $providerDetail->certificates = !empty($currentCertificates) ? $currentCertificates : null;
        $providerDetail->save();

        return redirect()->route('provider.profile.edit')->with('success', 'Profile and professional details updated successfully.');
    }

    public function destroyAccount(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'password_delete_provider' => 'required|string',
        ]);

        if (!Hash::check($request->password_delete_provider, $user->password)) {
            return back()->withErrors(['password_delete_provider' => 'The provided password does not match your current password.'])->with('show_delete_provider_modal', true);
        }

        if ($user->isProvider()) {
            foreach ($user->services as $service) {
                if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
                    Storage::disk('public')->delete($service->image_path);
                }
                $service->delete();
            }

            if ($user->providerDetail) {
                $certificates = is_array($user->providerDetail->certificates) ? $user->providerDetail->certificates : ($user->providerDetail->certificates ? $user->providerDetail->certificates->toArray() : []);
                foreach ($certificates as $certificate) {
                    if (isset($certificate['file_url'])) {
                        $filePath = Str::after($certificate['file_url'], Storage::url(''));
                        if (Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                    }
                }
                $user->providerDetail->delete();
            }
        }

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->deleteDirectory('profile-photos/' . $user->id);
        }

        if (Storage::disk('public')->exists('certificates/' . $user->id)) {
            Storage::disk('public')->deleteDirectory('certificates/' . $user->id);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->delete();

        return redirect()->route('welcome')->with('status', 'Your provider account has been successfully deleted.');
    }

    public function reviews()
    {
        $provider = Auth::user();
        $reviews = $provider->reviewsReceived()
            ->with([
                'client:id,name,profile_photo_path',
                'serviceRequest' => function ($query) {
                    $query->select('id', 'service_id', 'category_id', 'description')
                        ->with(['service:id,title', 'category:id,name']);
                }
            ])
            ->latest()
            ->paginate(10);

        return view('provider.dashboard.reviews', compact('provider', 'reviews'));
    }
}
