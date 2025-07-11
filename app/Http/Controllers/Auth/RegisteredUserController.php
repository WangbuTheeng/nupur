<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,operator'],
        ];

        // Add operator-specific validation rules
        if ($request->role === 'operator') {
            $validationRules['company_name'] = ['required', 'string', 'max:255'];
            $validationRules['company_address'] = ['required', 'string', 'max:500'];
            $validationRules['company_license'] = ['required', 'string', 'max:100'];
            $validationRules['contact_person'] = ['required', 'string', 'max:255'];
        }

        $request->validate($validationRules);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ];

        // Add operator-specific fields
        if ($request->role === 'operator') {
            $userData['company_name'] = $request->company_name;
            $userData['company_address'] = $request->company_address;
            $userData['company_license'] = $request->company_license;
            $userData['contact_person'] = $request->contact_person;
        }

        $user = User::create($userData);

        // Assign role using Spatie Permission
        $user->assignRole($request->role);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
