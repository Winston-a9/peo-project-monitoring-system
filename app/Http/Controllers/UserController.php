<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function dashboard(): View
    {
        return view('user.dashboard');
    }
}
