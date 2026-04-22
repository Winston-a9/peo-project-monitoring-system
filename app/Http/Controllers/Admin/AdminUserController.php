<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    // ── Guard: only super admins (division === null) can access ──────────
    private function requireSuperAdmin(): void
    {
        if (auth()->user()->division !== null) {
            abort(403, 'Only super admins can manage users.');
        }
    }

    // ── List all users ───────────────────────────────────────────────────
    public function index()
    {
        $this->requireSuperAdmin();

        $users = User::orderBy('role')
            ->orderBy('division')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    // ── Store a new user ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->requireSuperAdmin();

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(['admin', 'user'])],
            // division is required only when role is admin
            'division' => [
                Rule::requiredIf(fn () => $request->role === 'admin' && $request->division !== 'super'),
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        // When role is admin and division field is 'super', store null (super admin)
        $division = null;
        if ($request->role === 'admin') {
            $division = ($request->division === 'super' || $request->division === '')
                ? null
                : $request->division;
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'division' => $division,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $request->name . '" created successfully.');
    }

    // ── Update an existing user ──────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $this->requireSuperAdmin();

        // Prevent super admin from demoting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot edit your own account here.');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'user'])],
            'division' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $division = null;
        if ($request->role === 'admin') {
            $division = ($request->division === 'super' || $request->division === '')
                ? null
                : $request->division;
        }

        $updateData = [
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'division' => $division,
        ];

        // Only update password if a new one was provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $request->name . '" updated successfully.');
    }

    // ── Delete a user ────────────────────────────────────────────────────
    public function destroy(User $user)
    {
        $this->requireSuperAdmin();

        // Prevent super admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $name . '" deleted successfully.');
    }
}