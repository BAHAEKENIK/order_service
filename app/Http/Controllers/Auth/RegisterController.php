<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProviderDetail;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // For file uploads
use App\Models\Category; // For fetching categories for provider form
use Illuminate\Auth\Events\Registered; // For sending verification email if enabled
use Illuminate\Support\Facades\Log; // For debugging if needed

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request (Step 1).
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_provider' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_provider')) {
            // Store basic data in session and redirect to provider details form
            // Flashed data persists only for the next request.
            $request->session()->put('registration_data', [ // Use put instead of flash
    'name' => $request->name,
    'email' => $request->email,
    'password' => $request->password,
]);
             // Optional: Log to see if it's flashed
            // Log::info('Flashed registration data:', session('registration_data'));
            return redirect()->route('provider.register.form');
        }

        // If not a provider, register as client
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);

        // event(new Registered($user)); // Optional: If you use email verification

        // To automatically log in the user after registration:
        // Auth::login($user);
        // return redirect(route('dashboard')); // or your client dashboard

        return redirect()->route('login')->with('status', 'Registration successful! Please log in.');
    }


    /**
     * Show the form for provider to complete registration (Step 2a).
     */
    public function showProviderRegistrationForm(Request $request)
    {
        // Log::info('Entering showProviderRegistrationForm. Session has registration_data: ' . ($request->session()->has('registration_data') ? 'Yes' : 'No'));
        // if ($request->session()->has('registration_data')) {
        //     Log::info('Session registration_data content:', $request->session()->get('registration_data'));
        // }


        if (!$request->session()->has('registration_data')) {
            // If no session data (e.g., direct access, refresh, session expired), redirect back to initial registration
            return redirect()->route('register')->withErrors(['session_expired' => 'Your registration session has expired. Please start over.']);
        }
        $categories = Category::orderBy('name')->get();
        return view('auth.provider-registration', compact('categories'));
    }


    /**
     * Handle the submission of the provider registration details (Step 2b).
     */
    public function storeProviderDetails(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register')->withErrors(['session_expired' => 'Your registration session has expired. Please start over.']);
        }

        $initialData = $request->session()->get('registration_data'); // Get data set in storeUser

        $validator = Validator::make($request->all(), [
            'provider_full_name' => ['required', 'string', 'max:255'],
            'provider_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'], // Check uniqueness here
            'company_name' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'], // Consider a regex for phone numbers
            'professional_description' => ['required', 'string', 'min:20'],
            'certificates.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        // Manually check if the email from provider form matches the one from session if necessary
        // This prevents tampering with the email between steps IF the hidden email field was somehow changed.
        // However, since we re-validate uniqueness on provider_email, this check is somewhat redundant if that validation passes.
        if ($request->provider_email !== $initialData['email']) {
            // $request->session()->flash('registration_data', $initialData); // Re-flash for the form
            // return redirect()->route('provider.register.form')
            //             ->withErrors(['provider_email' => 'The email address does not match the initial registration.'])
            //             ->withInput();
             // Or just rely on unique validation for 'provider_email'. If unique check fails due to existing user,
             // then it will throw that error, which is appropriate. If initialData['email'] was unique but provider_email is now
             // different AND unique, this means user changed it. The crucial part is initialData['password'].
        }


        if ($validator->fails()) {
             $request->session()->put('registration_data', $initialData); // Re-flash data for the form to prefill hidden fields
            return redirect()->route('provider.register.form')
                        ->withErrors($validator)
                        ->withInput(); // This re-populates form fields based on name attributes
        }

        // Create User (Provider)
        $user = User::create([
            'name' => $request->provider_full_name,
            'email' => $request->provider_email, // Using email from this form
            'password' => Hash::make($initialData['password']), // Using password from session (step 1)
            'role' => 'provider',
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            // city can be part of address or a separate field
        ]);

        // Handle certificate uploads
        $uploadedCertificatesData = [];
        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $file) {
                if ($file->isValid()) {
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('certificates/' . $user->id, $filename, 'public');
                    $uploadedCertificatesData[] = [
                        'name' => $file->getClientOriginalName(),
                        'file_url' => Storage::url($path), // This generates a URL like /storage/certificates/...
                    ];
                }
            }
        }

        // Create Provider Details
        ProviderDetail::create([
            'user_id' => $user->id,
            'professional_description' => $request->professional_description,
            'certificates' => !empty($uploadedCertificatesData) ? json_encode($uploadedCertificatesData) : null,
            // 'company_name' => $request->company_name, // Add if you have this field in ProviderDetail
        ]);

        // Assign one default service based on chosen category
        $category = Category::find($request->category_id);
        if ($category) { // Ensure category exists
            Service::create([
                'user_id' => $user->id,
                'category_id' => $request->category_id,
                'title' => 'Default ' . $category->name . ' Service',
                'description' => 'Initial service offering for ' . $category->name . '.',
                'city' => explode(',', $request->address)[0] ?? 'City Not Specified',
                'status' => 'available',
            ]);
        }

        // event(new Registered($user));

        $request->session()->forget('registration_data'); // Clean up the flashed data

        return redirect()->route('login')->with('status', 'Provider registration successful! Please log in.');
    }
}
