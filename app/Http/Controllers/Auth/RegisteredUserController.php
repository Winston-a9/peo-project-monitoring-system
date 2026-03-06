<?php

namespace App\Http\Controllers\Auth;

<<<<<<< HEAD
use App\Http\Controllers\Controller;
use App\Models\User;
=======
use App\Models\User;
use App\Http\Controllers\Controller;
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
<<<<<<< HEAD
=======
            'role' => 'user', // Default new users to 'user' role
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
        ]);

        event(new Registered($user));

        Auth::login($user);

<<<<<<< HEAD
        return redirect(route('dashboard', absolute: false));
=======
        return redirect()->route('user.dashboard');
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
    }
}
