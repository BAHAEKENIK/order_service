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
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_provider' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_provider')) {
            $request->session()->put('registration_data', [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
            return redirect()->route('provider.register.form');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);

        return redirect()->route('login')->with('status', 'Registration successful! Please log in.');
    }

    public function showProviderRegistrationForm(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register')->withErrors(['session_expired' => 'Your registration session has expired. Please start over.']);
        }

        $categories = Category::orderBy('name')->get();
        return view('auth.provider-registration', compact('categories'));
    }

    public function storeProviderDetails(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register')->withErrors(['session_expired' => 'Your registration session has expired. Please start over.']);
        }

        $initialData = $request->session()->get('registration_data');

        $validator = Validator::make($request->all(), [
            'provider_full_name' => ['required', 'string', 'max:255'],
            'provider_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'professional_description' => ['required', 'string', 'min:20'],
            'certificates.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->provider_email !== $initialData['email']) {
            // Do nothing; uniqueness check is sufficient
        }

        if ($validator->fails()) {
            $request->session()->put('registration_data', $initialData);
            return redirect()->route('provider.register.form')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = User::create([
            'name' => $request->provider_full_name,
            'email' => $request->provider_email,
            'password' => Hash::make($initialData['password']),
            'role' => 'provider',
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        $uploadedCertificatesData = [];
        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $file) {
                if ($file->isValid()) {
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('certificates/' . $user->id, $filename, 'public');
                    $uploadedCertificatesData[] = [
                        'name' => $file->getClientOriginalName(),
                        'file_url' => Storage::url($path),
                    ];
                }
            }
        }

        ProviderDetail::create([
            'user_id' => $user->id,
            'professional_description' => $request->professional_description,
            'certificates' => !empty($uploadedCertificatesData) ? json_encode($uploadedCertificatesData) : null,
        ]);

        $category = Category::find($request->category_id);
        if ($category) {
            Service::create([
                'user_id' => $user->id,
                'category_id' => $request->category_id,
                'title' => 'Default ' . $category->name . ' Service',
                'description' => 'Initial service offering for ' . $category->name . '.',
                'city' => explode(',', $request->address)[0] ?? 'City Not Specified',
                'status' => 'available',
            ]);
        }

        $request->session()->forget('registration_data');

        return redirect()->route('login')->with('status', 'Provider registration successful! Please log in.');
    }
}
