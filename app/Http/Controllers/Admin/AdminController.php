<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\DivisionScope;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use DivisionScope;

    public function dashboard()
    {
        $data = [
            'totalUsers'  => User::where('role', 'user')->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
        ];

        return view('admin.dashboard', compact('data'));
    }
}