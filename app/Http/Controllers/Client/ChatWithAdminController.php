<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Models\ServiceRequest;
use App\Models\Category; // To find/create an "Admin Support" category

class ChatWithAdminController extends Controller
{
    protected function getAdminUser()
    {
        // Fetch the first user with the 'admin' role.
        // In a real application, you might have a more robust way to select the target admin
        // or even a dedicated support team assignment.
        return User::where('role', 'admin')->first();
    }

    /**
     * Find or create a ServiceRequest to act as the context for admin chat.
     * Redirect to the chat view for this ServiceRequest.
     */
    public function initiateOrShowChat()
    {
        $client = Auth::user();
        $admin = $this->getAdminUser();

        if (!$admin) {
            return redirect()->route('client.requests.my')->with('error', 'Admin support is currently unavailable.');
        }

        // Try to find an existing "Admin Support" service request for this client and admin
        $adminSupportCategory = Category::firstOrCreate(
            ['slug' => 'admin-support'],
            ['name' => 'Admin Support', 'description' => 'For direct communication with platform administrators.']
        );

        $chatContextRequest = ServiceRequest::firstOrCreate(
            [
                'client_id' => $client->id,
                'provider_id' => $admin->id, // Admin acts as the "provider" for this chat context
                'category_id' => $adminSupportCategory->id,
                // Adding a unique identifier or specific description if needed later
                // 'description' => 'Admin Support Chat for Client #' . $client->id
            ],
            [
                // Default values if creating new
                'description' => 'Admin Support Chat: ' . $client->name,
                'address' => $client->address ?? 'N/A',
                'city' => $client->city ?? 'N/A',
                'status' => 'admin_chat', // A new status to differentiate
            ]
        );

        // Redirect to the existing general chat view, passing this special ServiceRequest
        return redirect()->route('client.messages.chat', $chatContextRequest);
    }

    // Note: Storing messages for admin chat will use the existing
    // ClientDashboardController@storeMessage method, as it's generic enough
    // to handle any ServiceRequest context, including our "admin_chat" ones.
    // The route 'client.messages.chat' and 'client.messages.store' will be used.
}
