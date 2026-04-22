<?php

namespace App\Traits;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

/**
 * DivisionScope
 *
 * Provides a reusable query scope for division-based access control.
 * Usage: call $this->divisionQuery() anywhere in a controller that
 * uses this trait to get a pre-scoped Project query builder.
 *
 * Rules:
 *  - Super admin  (division === null)  → sees ALL projects
 *  - Division admin (division set)     → sees ONLY projects whose
 *                                        `division` matches their own
 */
trait DivisionScope
{
    /**
     * Returns a Project query builder already scoped to the
     * authenticated admin's division (or unscoped for super admins).
     */
    protected function divisionQuery(): Builder
    {
        $user = auth()->user();

        $query = Project::query();

        if ($user->division !== null) {
            $query->where('division', $user->division);
        }

        return $query;
    }

    /**
     * Abort with 403 if the given project is outside the
     * authenticated division admin's scope.
     */
    protected function authorizeProjectAccess(\App\Models\Project $project): void
    {
        $user = auth()->user();

        if ($user->division !== null && $project->division !== $user->division) {
            abort(403, 'You do not have access to this project.');
        }
    }

    /**
     * Returns true if the current admin is a super admin.
     */
    protected function isSuperAdmin(): bool
    {
        return auth()->user()->division === null;
    }

    /**
     * Returns the current admin's division (null for super admin).
     */
    protected function currentDivision(): ?string
    {
        return auth()->user()->division;
    }
}