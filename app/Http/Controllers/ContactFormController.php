<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUsMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactFormController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return redirect()->route('contact')
                        ->withErrors($validator)
                        ->withInput();
        }

        try {
            $fullName = $request->first_name . ' ' . $request->last_name;

            ContactUsMessage::create([
                'name'        => $fullName,
                'email'       => $request->email,
                'subject'     => $request->subject,
                'message'     => $request->message,
                'user_id'     => auth()->id(),
                'status'      => 'new',
            ]);

            return redirect()->route('contact')->with('success', 'Your message has been sent successfully! We will get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Error storing contact message: ' . $e->getMessage());

            return redirect()->route('contact')
                        ->with('error', 'Sorry, there was an issue sending your message. Please try again later.')
                        ->withInput();
        }
    }
}
