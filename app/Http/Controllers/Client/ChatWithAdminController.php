<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Models\ServiceRequest;
use App\Models\Category;

class ChatWithAdminController extends Controller
{
    protected function getAdminUser()
    {
        return User::where('role', 'admin')->first();
    }

    public function initiateOrShowChat()
    {
        $client = Auth::user();
        $admin = $this->getAdminUser();

        if (!$admin) {
            return redirect()->route('client.requests.my')->with('error', 'Admin support is currently unavailable.');
        }

        $adminSupportCategory = Category::firstOrCreate(
            ['slug' => 'admin-support'],
            ['name' => 'Admin Support', 'description' => 'For direct communication with platform administrators.']
        );

        $chatContextRequest = ServiceRequest::firstOrCreate(
            [
                'client_id' => $client->id,
                'provider_id' => $admin->id,
                'category_id' => $adminSupportCategory->id,
            ],
            [
                'description' => 'Admin Support Chat: ' . $client->name,
                'address' => $client->address ?? 'N/A',
                'city' => $client->city ?? 'N/A',
                'status' => 'admin_chat',
            ]
        );

        return redirect()->route('client.messages.chat', $chatContextRequest);
    }
}
