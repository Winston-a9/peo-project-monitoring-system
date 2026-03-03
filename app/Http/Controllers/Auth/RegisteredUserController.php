<?php

namespace App\Http\Controllers\Auth;

<<<<<<< HEAD
use App\Models\User;
use App\Http\Controllers\Controller;
=======
use App\Http\Controllers\Controller;
use App\Models\User;
>>>>>>> 33baeb89651948608801199ef8dceec70f723e41
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
<<<<<<< HEAD
=======
     *
     * @throws \Illuminate\Validation\ValidationException
>>>>>>> 33baeb89651948608801199ef8dceec70f723e41
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
<<<<<<< HEAD
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
=======
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
>>>>>>> 33baeb89651948608801199ef8dceec70f723e41
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
<<<<<<< HEAD
            'role' => 'user', // Set default role to 'user'
=======
>>>>>>> 33baeb89651948608801199ef8dceec70f723e41
        ]);

        event(new Registered($user));

        Auth::login($user);

<<<<<<< HEAD
        return redirect()->route('user.dashboard'); // Redirect to user dashboard
    }
}
=======
        return redirect(route('dashboard', absolute: false));
    }
}
>>>>>>> 33baeb89651948608801199ef8dceec70f723e41
