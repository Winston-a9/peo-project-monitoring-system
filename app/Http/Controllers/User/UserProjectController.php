<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;

class UserProjectController extends Controller
{
    public function index()
    {
        $projects = Project::paginate(10);
        return view('user.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        return view('user.projects.show', compact('project'));
    }
}