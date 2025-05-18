<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\ContactUsMessage;
use App\Models\ServiceRequest;
use App\Models\Service;
use App\Models\Message;
use App\Models\ProviderDetail;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminContactReply;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::where('role', '!=', 'admin')->count(),
            'totalClients' => User::where('role', 'client')->count(),
            'totalProviders' => User::where('role', 'provider')->count(),
            'totalServiceRequests' => ServiceRequest::count(),
            'totalServices' => Service::count(),
            'totalCategories' => Category::count(),
            'pendingContactMessages' => ContactUsMessage::where('status', 'new')->count(),
        ];
        return view('admin.dashboard.index', compact('stats'));
    }

    public function manageUsers(Request $request)
    {
        $query = User::query()
            ->where('id', '!=', Auth::id())
            ->where('role', '!=', 'admin');

        if ($request->filled('role_filter') && $request->input('role_filter') !== 'all') {
            $query->where('role', $request->input('role_filter'));
        }
        if ($request->filled('search_user')) {
            $search = $request->input('search_user');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->with('providerDetail')->latest()->paginate(10)->withQueryString();
        $roles = ['all' => 'All Users', 'client' => 'Clients', 'provider' => 'Providers'];
        return view('admin.dashboard.users.index', compact('users', 'roles'));
    }

    public function showUser(User $user)
    {
        if ($user->isAdmin() && $user->id !== Auth::id()) { abort(403); }
        if ($user->id === Auth::id()){ return redirect()->route('admin.profile.edit'); }
        if ($user->isProvider()) { $user->load(['providerDetail','services.category','reviewsReceived' => fn($q) => $q->with('client:id,name')->latest()]); }
        elseif ($user->isClient()) { $user->loadCount(['clientServiceRequests', 'reviewsGiven']); $user->load(['reviewsGiven' => fn($q) => $q->with('provider:id,name')->latest()]); }
        return view('admin.dashboard.users.show', compact('user'));
    }

    /**
     * Remove the specified user from storage (Admin action).
     */
    public function destroyUser(User $user) // No need for Request $request if not using password confirm here
    {
        $admin = Auth::user();
        if ($user->id === $admin->id || $user->isAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete this admin account or your own account.');
        }

        $userName = $user->name;
        try {
            DB::transaction(function () use ($user) {
                // Provider-specific cleanup
                if ($user->isProvider() && $user->providerDetail) {
                    foreach ($user->services as $service) {
                        if ($service->image_path && Storage::disk('public')->exists($service->image_path)) { Storage::disk('public')->delete($service->image_path); }
                        $service->serviceRequests()->update(['service_id' => null]);
                        $service->delete();
                    }
                    if (is_iterable($user->providerDetail->certificates)) {
                        foreach ($user->providerDetail->certificates as $certificate) {
                            if (isset($certificate['file_url'])) { // Use 'file_url' consistent with ProviderDetailFactory/Controller
                                $filePath = Str::after($certificate['file_url'], Storage::url(''));
                                if (Storage::disk('public')->exists($filePath)) Storage::disk('public')->delete($filePath);
                            }
                        }
                    }
                     if (Storage::disk('public')->exists('certificates/' . $user->id)) { Storage::disk('public')->deleteDirectory('certificates/' . $user->id); }
                    $user->providerDetail->delete();
                }

                // Generic User cleanup
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path); // Delete file
                    $profilePhotoDir = 'profile-photos/' . $user->id;
                    if (Storage::disk('public')->exists($profilePhotoDir) && count(Storage::disk('public')->allFiles($profilePhotoDir)) === 0) {
                        Storage::disk('public')->deleteDirectory($profilePhotoDir); // Delete directory if empty
                    }
                }

                // Update ServiceRequests: Set client_id/provider_id to null or handle differently
                ServiceRequest::where('client_id', $user->id)->update(['client_id' => null]); // Or delete
                ServiceRequest::where('provider_id', $user->id)->update(['provider_id' => null, 'status' => 'cancelled_provider_deleted']); // Or delete/reassign

                Message::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();
                Review::where('client_id', $user->id)->orWhere('provider_id', $user->id)->delete();
                // $user->contactUsMessages()->delete(); // Assuming relation exists

                $user->delete();
            });
            return redirect()->route('admin.users.index')->with('success', "User '{$userName}' has been successfully deleted.");
        } catch (\Exception $e) {
            Log::error("Error deleting user {$user->id} ('{$userName}'): " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('admin.users.index')->with('error', "Failed to delete user '{$userName}'. An error occurred.");
        }
    }

    /**
     * Handle bulk user deletion.
     */
    public function bulkDestroyUsers(Request $request)
    {
        $admin = Auth::user();

        if ($request->input('delete_all_flag') === 'true') {
            $request->validate(['delete_all_confirm_password' => 'required|string']);
            if (!Hash::check($request->input('delete_all_confirm_password'), $admin->password)) {
                return redirect()->route('admin.users.index')->with('error', 'Admin password incorrect for deleting all users.')->with('show_delete_all_users_modal_with_error', true);
            }
            $usersToDelete = User::where('role', '!=', 'admin')->where('id', '!=', $admin->id)->get();
        } elseif ($request->filled('user_ids')) {
            $validated = $request->validate(['user_ids' => 'required|array', 'user_ids.*' => ['integer', Rule::exists('users', 'id')->whereNot('role', 'admin')->where('id', '!=', $admin->id)], ]);
            $usersToDelete = User::whereIn('id', $validated['user_ids'])->get();
        } else {
            return redirect()->route('admin.users.index')->with('info', 'No users selected for deletion.');
        }

        if ($usersToDelete->isEmpty()) {
            return redirect()->route('admin.users.index')->with('info', 'No eligible users found or selected for deletion.');
        }

        $deletedCount = 0;
        $errorMessages = [];

        foreach ($usersToDelete as $user) {
            $userName = $user->name; // For logging/messaging
            try {
                DB::transaction(function () use ($user) {
                    if ($user->isProvider() && $user->providerDetail) {
                        // Provider data cleanup (services, certificate files)
                        foreach ($user->services as $service) {
                            if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
                                Storage::disk('public')->delete($service->image_path);
                            }
                            $service->serviceRequests()->update(['service_id' => null]); // Unlink SRs from this specific service
                            $service->delete(); // Delete the service
                        }
                        if (is_iterable($user->providerDetail->certificates)) {
                            foreach ($user->providerDetail->certificates as $certificate) {
                                if (isset($certificate['file_url'])) {
                                    $filePath = Str::after($certificate['file_url'], Storage::url(''));
                                    if (Storage::disk('public')->exists($filePath)) {
                                        Storage::disk('public')->delete($filePath);
                                    }
                                }
                            }
                        }
                        $user->providerDetail->delete(); // Delete the ProviderDetail record
                    }

                    // Generic user asset directory cleanup
                    if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                         // Attempt to delete the specific file first, then the directory.
                         // Simpler approach for bulk: just delete directory. Assuming profile_photo_path is a file within 'profile-photos/' . $user->id
                        $profilePhotoDir = 'profile-photos/' . $user->id;
                        if(Storage::disk('public')->exists($profilePhotoDir)) {
                            Storage::disk('public')->deleteDirectory($profilePhotoDir);
                        } elseif (Storage::disk('public')->exists($user->profile_photo_path)) { // Fallback if it's just a file not in a user-specific dir
                            Storage::disk('public')->delete($user->profile_photo_path);
                        }
                    }
                    if (Storage::disk('public')->exists('certificates/' . $user->id)) {
                        Storage::disk('public')->deleteDirectory('certificates/' . $user->id);
                    }

                    // Delete related records
                    // Note: This is different from single destroyUser which updates SRs. Bulk delete is more aggressive here.
                    ServiceRequest::where('client_id', $user->id)->delete();
                    ServiceRequest::where('provider_id', $user->id)->delete();
                    Message::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();
                    Review::where('client_id', $user->id)->orWhere('provider_id', $user->id)->delete();
                    // ContactUsMessage::where('user_id', $user->id)->delete(); // If applicable

                    $user->delete(); // Finally, delete the user
                });
                $deletedCount++;
            } catch (\Exception $e) {
                Log::error("Error bulk deleting user {$user->id} ('{$userName}'): " . $e->getMessage());
                $errorMessages[] = "Failed to delete user {$userName} ({$user->email}).";
            }
        }
        $feedback = [];
        if ($deletedCount > 0) { $feedback['success'] = "Successfully deleted {$deletedCount} user(s)."; }
        if (!empty($errorMessages)) { $feedback['error'] = "Some users could not be deleted. Check logs for details.<br>" . implode("<br>", $errorMessages); }
        if ($deletedCount === 0 && empty($errorMessages) && !$request->input('delete_all_flag') && $usersToDelete->isNotEmpty()) { $feedback['info'] = 'No selected users were ultimately deleted (they might have been admins or yourself).'; }
        elseif ($deletedCount === 0 && empty($errorMessages)) { $feedback['info'] = 'No users were deleted.'; }

        return redirect()->route('admin.users.index')->with($feedback);
    }

    /**
     * Display a listing of categories.
     */
    public function manageCategories()
    {
        // Eager load counts of services and service requests for each category
        $categories = Category::withCount(['services', 'serviceRequests'])
                              ->orderBy('name')
                              ->paginate(10);
        return view('admin.dashboard.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            // 'icon_path' => 'nullable|image|mimes:jpeg,png,svg|max:1024' // If you implement icon uploads
        ]);

        // Sanitize description if it contains HTML, or use a proper WYSIWYG editor and purifier
        $description = $validated['description'] ? strip_tags($validated['description']) : null;

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroyCategory(Category $category)
    {
        // Check if the category is associated with any services or service requests
        if ($category->services()->count() > 0 || $category->serviceRequests()->count() > 0) {
            return redirect()->route('admin.categories.index')
                             ->with('error', "Cannot delete category '{$category->name}'. It is currently associated with existing services or service requests. Please reassign or delete them first.");
        }

        // If you had category icons stored:
        // if ($category->icon_path && Storage::disk('public')->exists($category->icon_path)) {
        //     Storage::disk('public')->delete($category->icon_path);
        // }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', "Category '{$categoryName}' deleted successfully.");
    }


    /**
     * Display a listing of contact us messages.
     */
    public function contactMessages()
    {
        $messages = ContactUsMessage::with('user:id,name')->latest()->paginate(10); // Eager load user if message is from a logged-in user
        return view('admin.dashboard.contact-messages.index', compact('messages'));
    }

    /**
     * Show a specific contact message and form to reply.
     */
    public function showContactMessage(ContactUsMessage $contactMessage)
    {
        if ($contactMessage->status === 'new') {
            $contactMessage->status = 'read_by_admin'; // More specific status
            $contactMessage->save();
        }
        return view('admin.dashboard.contact-messages.show', compact('contactMessage'));
    }

    /**
     * Send a reply to a contact us message.
     */
    public function replyContactMessage(Request $request, ContactUsMessage $contactMessage)
    {
        $validated = $request->validate([
            'reply_content' => 'required|string|min:10',
            'reply_subject' => 'required|string|max:255',
        ]);

        try {
            Mail::to($contactMessage->email)->send(new AdminContactReply(
                $validated['reply_subject'],
                $validated['reply_content'],
                $contactMessage->name // Recipient name
            ));
            $contactMessage->status = 'replied';
            $contactMessage->admin_reply = $validated['reply_content']; // Storing reply itself
            $contactMessage->replied_at = now();
            $contactMessage->save();
            return redirect()->route('admin.contact-messages.show', $contactMessage)->with('success', 'Reply sent successfully to ' . $contactMessage->email);
        } catch (\Exception $e) {
            Log::error("Failed to send contact reply email to {$contactMessage->email}: " . $e->getMessage());
            return back()->with('error', 'Failed to send email. Please check mail configuration. Error: '. $e->getMessage())->withInput();
        }
    }


    /**
     * Display the admin's internal inbox (chats with users).
     */
    public function adminInbox()
    {
        $adminId = Auth::id();
        $serviceRequestIds = Message::where(function ($query) use ($adminId) {
            $query->where('sender_id', $adminId)
                  ->orWhere('receiver_id', $adminId);
            })
            ->distinct()
            ->pluck('service_request_id');

        $conversations = ServiceRequest::whereIn('id', $serviceRequestIds)
            ->where(function ($query) use ($adminId) {
                $query->where('provider_id', $adminId)
                      ->orWhere('client_id', $adminId);
            })
            ->with([
                'client' => function ($query) { $query->select('id', 'name', 'profile_photo_path', 'role'); },
                'provider' => function ($query) { $query->select('id', 'name', 'profile_photo_path', 'role'); },
                'messages' => function ($query) { $query->latest()->limit(1); },
                'category:id,name'
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('service_request_id', 'service_requests.id')
                    ->latest()
                    ->limit(1)
            )
            ->paginate(10);

        return view('admin.dashboard.inbox.index', compact('conversations', 'adminId'));
    }

    /**
     * Show a specific chat for the Admin.
     */
    public function showAdminChat(ServiceRequest $serviceRequest)
    {
        $admin = Auth::user();
        if ($serviceRequest->client_id !== $admin->id && $serviceRequest->provider_id !== $admin->id) {
             abort(403, 'Unauthorized chat access.');
        }

        $serviceRequest->load([
            'messages' => function ($query) { $query->with(['sender:id,name,profile_photo_path'])->orderBy('created_at', 'asc'); },
            'client:id,name,profile_photo_path,role',
            'provider:id,name,profile_photo_path,role',
            'category:id,name'
        ]);

        $serviceRequest->messages()->where('receiver_id', $admin->id)->whereNull('read_at')->update(['read_at' => now()]);

        if ($serviceRequest->client_id === $admin->id) {
            $otherParty = $serviceRequest->provider;
        } elseif ($serviceRequest->provider_id === $admin->id) {
            $otherParty = $serviceRequest->client;
        } else {
            $otherParty = null;
        }

        return view('admin.dashboard.inbox.chat', compact('serviceRequest', 'otherParty', 'admin'));
    }

    /**
     * Store a message sent by Admin.
     */
    public function storeAdminMessage(Request $request, ServiceRequest $serviceRequest)
    {
        $admin = Auth::user();
        if ($serviceRequest->client_id !== $admin->id && $serviceRequest->provider_id !== $admin->id) {
            abort(403, 'Unauthorized to send message in this chat.');
        }

        $request->validate(['content' => 'required|string|max:2000']);

        $receiverId = null;
        if ($serviceRequest->client_id === $admin->id) {
            $receiverId = $serviceRequest->provider_id;
        } elseif ($serviceRequest->provider_id === $admin->id) {
            $receiverId = $serviceRequest->client_id;
        }

        if (!$receiverId) {
            Log::error("Admin message store: Could not determine receiver for SR ID: {$serviceRequest->id}, Admin ID: {$admin->id}");
            return back()->with('error', 'Could not determine message recipient.');
        }

        Message::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $admin->id,
            'receiver_id' => $receiverId,
            'content' => $request->content,
        ]);
        $serviceRequest->touch();

        return back();
    }

    public function chatWithUser(User $user)
    {
        $admin = Auth::user();
        if ($user->isAdmin() || $user->id === $admin->id) {
            return redirect()->route('admin.inbox.index')->with('info', 'You cannot initiate a chat with this user account.');
        }

        $descriptionForChat = 'Admin support chat with ' . $user->name . ' (' . $user->role . ') initiated on ' . now()->toFormattedDateString();

        $supportCategory = Category::firstOrCreate(
            ['slug' => 'admin-direct-user-chat'],
            ['name' => 'Admin Direct User Chat', 'description' => 'Category for direct chats initiated by administrators with users.']
        );

        $chatContextRequest = ServiceRequest::firstOrCreate(
            [
                'client_id' => $admin->id,
                'provider_id' => $user->id,
                'category_id' => $supportCategory->id,
                'status' => 'admin_chat',
            ],
            [
                'description' => $descriptionForChat,
                'address' => 'N/A - Admin Chat',
                'city' => 'N/A',
                'preferred_time' => 'N/A',
                'budget' => null,
            ]
        );

        if ($chatContextRequest->wasRecentlyCreated || $chatContextRequest->description !== $descriptionForChat) {
             $chatContextRequest->description = $descriptionForChat;
             $chatContextRequest->touch();
             $chatContextRequest->save();
        }

        return redirect()->route('admin.inbox.chat', $chatContextRequest->id);
    }

    /**
     * Show admin's own profile for editing.
     */
    public function adminProfile()
    {
        $admin = Auth::user();
        return view('admin.dashboard.profile.edit', compact('admin'));
    }

    /**
     * Update admin's own profile.
     */
    public function updateAdminProfile(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->filled('current_password')) {
             if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => __('auth.password'),
                ]);
            }
            if (!empty($validatedData['new_password'])) {
                $user->password = Hash::make($validatedData['new_password']);
            }
        }

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos/' . $user->id, 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();
        return redirect()->route('admin.profile.edit')->with('success', 'Admin profile updated successfully.');
    }
}
