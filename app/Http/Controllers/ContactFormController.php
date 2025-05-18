<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUsMessage; // Make sure this path is correct for your model
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Optional: for logging errors


class ContactFormController extends Controller
{
    /**
     * Display the contact form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('contact'); // Assuming your view file is resources/views/contact.blade.php
    }

    /**
     * Store a newly created contact message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            return redirect()->route('contact') // Or back()
                        ->withErrors($validator)
                        ->withInput();
        }

        try {
            $fullName = $request->first_name . ' ' . $request->last_name;

            ContactUsMessage::create([
                'name' => $fullName, // Combine first and last name for the 'name' field
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'user_id' => auth()->id(), // This will be null if the user is not logged in
                'status' => 'new', // Default status
            ]);

            return redirect()->route('contact')->with('success', 'Your message has been sent successfully! We will get back to you soon.');

        } catch (\Exception $e) {
            // Optional: Log the error for debugging
            Log::error('Error storing contact message: ' . $e->getMessage());

            // Redirect back with a generic error message
            return redirect()->route('contact')
                        ->with('error', 'Sorry, there was an issue sending your message. Please try again later.')
                        ->withInput();
        }
    }
}
