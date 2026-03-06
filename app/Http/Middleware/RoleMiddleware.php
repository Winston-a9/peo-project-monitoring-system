<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
<<<<<<< HEAD
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, 'Unauthorized.');
=======
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user's role matches allowed roles
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized. You do not have access to this page.');
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
        }

        return $next($request);
    }
}