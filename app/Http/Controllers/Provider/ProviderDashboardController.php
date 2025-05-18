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
use Illuminate\Validation\Rule; // Added for Rule::in

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
        if ($serviceRequest->provider_id !== $provider->id) { abort(403); }
        $serviceRequest->load(['client:id,name,email,phone_number,profile_photo_path', 'service', 'category']);
        $updatableStatuses = [];
        switch ($serviceRequest->status) {
            case 'pending': case 'inquiry': $updatableStatuses = ['accepted' => 'Accept', 'rejected' => 'Ignore/Reject']; break;
            case 'accepted': $updatableStatuses = ['in_progress' => 'Mark In Progress', 'completed' => 'Mark as Completed', 'rejected' => 'Reject']; break;
            case 'in_progress': $updatableStatuses = ['completed' => 'Mark as Completed']; break;
        }
        return view('provider.dashboard.request-detail', compact('serviceRequest', 'provider', 'updatableStatuses'));
    }

    public function updateRequestStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $provider = Auth::user(); if ($serviceRequest->provider_id !== $provider->id) { abort(403); }
        $allowedStatuses = ['accepted', 'rejected', 'in_progress', 'completed'];
        $newStatus = $request->input('status');
        $request->validate(['status' => ['required', 'string', \Illuminate\Validation\Rule::in($allowedStatuses)],]);
        $currentStatus = $serviceRequest->status; $validTransition = false;
        if (($currentStatus === 'pending' || $currentStatus === 'inquiry') && in_array($newStatus, ['accepted', 'rejected'])) { $validTransition = true;}
        elseif ($currentStatus === 'accepted' && in_array($newStatus, ['in_progress', 'completed', 'rejected'])) { $validTransition = true; }
        elseif ($currentStatus === 'in_progress' && $newStatus === 'completed') { $validTransition = true; }
        if (!$validTransition) { return redirect()->back()->with('error', "Cannot change status from '{$currentStatus}' to '{$newStatus}'.");}
        $serviceRequest->status = $newStatus; $serviceRequest->save();
        $message = 'Request status updated to ' . Str::title(str_replace('_', ' ', $newStatus)) . '.';
        if ($newStatus === 'accepted') { $message = 'Request accepted!';} elseif ($newStatus === 'rejected') { $message = 'Request rejected.'; } elseif ($newStatus === 'completed') { $message = 'Request marked as completed.';}
        if (in_array($newStatus, ['accepted', 'rejected'])) { return redirect()->route('provider.requests.index')->with('success', $message); }
        return redirect()->route('provider.requests.detail', $serviceRequest)->with('success', $message);
    }

    /**
     * Display the provider's services.
     */
    public function myServices()
    {
        $provider = Auth::user();
        $services = $provider->services()->with('category')->latest()->paginate(10);
        return view('provider.dashboard.my-services', compact('provider', 'services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function createService()
    {
        $provider = Auth::user();
        $categories = Category::orderBy('name')->get();
        return view('provider.dashboard.create-service', compact('provider', 'categories'));
    }

    /**
     * Store a newly created service in storage.
     */
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
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Added webp
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
            'address' => $validated['address'] ?? $provider->address, // Default to provider's address
            'city' => $validated['city'] ?? $provider->city,          // Default to provider's city
            'image_path' => $imagePath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('provider.services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function editService(Service $service)
    {
        $provider = Auth::user();
        // Authorization: Ensure provider owns this service
        if ($service->user_id !== $provider->id) {
            abort(403, 'You are not authorized to edit this service.');
        }
        $categories = Category::orderBy('name')->get();
        return view('provider.dashboard.edit-service', compact('provider', 'service', 'categories'));
    }

    /**
     * Update the specified service in storage.
     */
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

        $imagePath = $service->image_path; // Keep old image by default
        if ($request->hasFile('new_image_path')) {
            // Delete old image if it exists and a new one is uploaded
            if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
                Storage::disk('public')->delete($service->image_path);
            }
            $imagePath = $request->file('new_image_path')->store('service-images/' . $provider->id, 'public');
        } elseif ($request->boolean('remove_existing_image') && $service->image_path) {
             // If remove checkbox is checked and there's an image
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
            'address' => $validated['address'] ?? $service->address, // Keep old if not provided
            'city' => $validated['city'] ?? $service->city,
            'image_path' => $imagePath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('provider.services.index')->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroyService(Service $service)
    {
        $provider = Auth::user();
        if ($service->user_id !== $provider->id) {
            abort(403, 'You are not authorized to delete this service.');
        }

        // Delete associated image from storage
        if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
            Storage::disk('public')->delete($service->image_path);
        }
        // TODO: Consider what happens to ServiceRequests linked to this service.
        // By default, service_requests.service_id is nullable, so it will be set to null.
        // You might want to disallow deleting services with active/pending requests or handle them differently.
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
            // For provider inbox, we're interested in requests they are the provider for, OR admin chats
            ->where(function($query) use ($providerId) {
                $query->where('provider_id', $providerId) // Chats with clients for their service requests
                      ->orWhere(function($subQuery) use ($providerId) { // Chats with admin
                          $subQuery->where('client_id', $providerId) // Provider is the "client" in SR
                                   ->whereHas('provider', function($adminQuery){
                                       $adminQuery->where('role', 'admin'); // The "provider" is an admin
                                   });
                      });
            })
            ->with([
                'client' => function ($query) { $query->select('id', 'name', 'profile_photo_path');},
                'provider' => function ($query) { $query->select('id', 'name', 'profile_photo_path');}, // Could be client or admin
                'messages' => function ($query) { $query->latest()->limit(1); }
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
        // Provider is either the provider_id of the SR (chat with client)
        // Or provider is the client_id of the SR and the provider_id is an admin (chat with admin)
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

        // Determine the "other party" for the chat header
        if ($serviceRequest->provider_id === $currentUser->id) { // Current user (provider) is chatting with client
            $otherParty = $serviceRequest->client;
        } else { // Current user (provider) is chatting with admin (who is set as provider_id on SR)
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

        // Determine receiver
        $receiverId = null;
        if($serviceRequest->provider_id === $currentUser->id) { // Provider sending to client
            $receiverId = $serviceRequest->client_id;
        } elseif ($serviceRequest->client_id === $currentUser->id && $serviceRequest->provider->isAdmin()) { // Provider sending to Admin
            $receiverId = $serviceRequest->provider_id;
        }

        if(!$receiverId){
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

    /**
     * Initiate or find a chat with the Admin for the Provider.
     */
    public function chatWithAdmin(User $admin) // $admin is passed by the route closure
    {
        if (!$admin->isAdmin()) {
            abort(404, 'Admin user specified is not an admin.');
        }

        $provider = Auth::user(); // Current authenticated provider

        // For provider-admin chat, provider is 'client_id', admin is 'provider_id' in ServiceRequest
        $descriptionForAdminChat = 'Support chat with Admin: ' . $provider->name; // Make it unique per provider
        $categoryForSupport = Category::firstOrCreate(
            ['slug' => 'admin-support'],
            ['name' => 'Admin Support']
        );

        $serviceRequest = ServiceRequest::firstOrCreate(
            [
                'client_id' => $provider->id, // Provider is the initiator (client role in this SR)
                'provider_id' => $admin->id,    // Admin is the recipient (provider role in this SR)
                'category_id' => $categoryForSupport->id,
                // Adding a unique element to description to differentiate from other admin chats for *other* providers
                'description' => $descriptionForAdminChat
            ],
            [
                // Fields to set if creating new:
                'address' => $provider->address ?? 'N/A',
                'city' => $provider->city ?? 'N/A',
                'status' => 'inquiry', // Special status for admin chats
            ]
        );

        return redirect()->route('provider.messages.chat', $serviceRequest);
    }


    public function profile()
    {
        $provider = Auth::user()->load('providerDetail'); // Eager load providerDetail
        return view('provider.dashboard.profile', compact('provider'));
    }

    /**
     * Update the provider's profile and professional details.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        // Ensure providerDetail exists or create it
        $providerDetail = $user->providerDetail ?? new ProviderDetail(['user_id' => $user->id]);

        $validatedUserData = $request->validate([
            'name' => 'required|string|max:255', // Full name here
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
            // 'company_name' is not directly in ProviderDetail, might be User.name or a custom field
            // For 'certificates_to_remove' and 'new_certificates', validation will be handled below
        ]);

        // Password check
        if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('auth.password'),
            ]);
        }

        // Update User model
        $user->name = $validatedUserData['name'];
        $user->email = $validatedUserData['email'];
        $user->phone_number = $validatedUserData['phone_number'] ?? $user->phone_number;
        $user->address = $validatedUserData['address'] ?? $user->address; // Provider's main operating address
        $user->city = $validatedUserData['city'] ?? $user->city;         // Provider's main operating city

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

        // Update or Create ProviderDetail
        $providerDetail->professional_description = $validatedProviderData['professional_description'] ?? $providerDetail->professional_description;
        $providerDetail->is_available = $request->has('is_available') ? $request->boolean('is_available') : $providerDetail->is_available ?? true;


        // Certificate Management
        $currentCertificates = is_array($providerDetail->certificates) ? $providerDetail->certificates : ($providerDetail->certificates ? $providerDetail->certificates->toArray() : []);

        // Remove marked certificates
        if ($request->filled('certificates_to_remove')) {
            $urlsToRemove = $request->input('certificates_to_remove');
            $currentCertificates = array_filter($currentCertificates, function ($cert) use ($urlsToRemove) {
                if (isset($cert['file_url']) && in_array($cert['file_url'], $urlsToRemove)) {
                    $filePath = Str::after($cert['file_url'], Storage::url(''));
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                    return false; // Remove from array
                }
                return true; // Keep in array
            });
            $currentCertificates = array_values($currentCertificates); // Re-index array
        }

        // Add new certificates
        if ($request->hasFile('new_certificates')) {
            foreach ($request->file('new_certificates') as $file) {
                $request->validate(['new_certificates.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048']); // Validate each new file
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

    /**
     * Handle account deletion for Provider.
     * This is very similar to client's destroyAccount, adapt as needed for provider specifics.
     */
    public function destroyAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_delete_provider' => 'required|string', // Use a different name for provider modal if on same page for any reason
        ]);

        if (!Hash::check($request->password_delete_provider, $user->password)) {
             return back()->withErrors(['password_delete_provider' => 'The provided password does not match your current password.'])->with('show_delete_provider_modal', true);
        }

        // Perform provider-specific cleanup
        if ($user->isProvider()) {
            // Delete associated services (and their images)
            foreach ($user->services as $service) {
                if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
                    Storage::disk('public')->delete($service->image_path);
                }
                // ServiceRequests linked to this service will have service_id set to null (due to migration `onDelete('set null')`)
                // Or you might choose to disallow deleting provider if they have active service requests.
                $service->delete();
            }

            // Delete ProviderDetail record (and its certificates)
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
             // Consider what happens to service requests where this user is the provider.
            // Default behavior: service_requests records remain, provider_id remains, but user is gone.
            // You might want to anonymize, reassign, or notify clients. For simplicity now, they remain linked to a non-existent user.
            // ServiceRequest::where('provider_id', $user->id)->update(['status' => 'cancelled_provider_deleted']); // Example
        }


        // Delete user profile photo
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->deleteDirectory('profile-photos/' . $user->id); // Delete the whole folder for this user
        }
         // Also, delete certificate folder for this user
        if (Storage::disk('public')->exists('certificates/' . $user->id)) {
            Storage::disk('public')->deleteDirectory('certificates/' . $user->id);
        }


        // Log the user out
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Delete the user
        $user->delete(); // Triggers cascading deletes defined in migrations or model events

        return redirect()->route('welcome')->with('status', 'Your provider account has been successfully deleted.');
    }


    public function reviews()
    {
        $provider = Auth::user();
        // Eager load client details and service request details for context
        $reviews = $provider->reviewsReceived()
                            ->with([
                                'client:id,name,profile_photo_path', // Client who wrote the review
                                'serviceRequest' => function ($query) { // The related service request
                                    $query->select('id', 'service_id', 'category_id', 'description') // Select only needed fields
                                          ->with(['service:id,title', 'category:id,name']); // And details from SR
                                }
                            ])
                            ->latest() // Show newest reviews first
                            ->paginate(10); // Paginate reviews

        return view('provider.dashboard.reviews', compact('provider', 'reviews'));
    }
}
