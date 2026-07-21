<?php

use App\Models\Project;
use App\Models\User;

test('regular users cannot access project creation routes', function () {
    $user = User::factory()->create(['role' => 'user']);

    actingAs($user)
        ->get('/user/projects/create')
        ->assertForbidden();
});

test('regular users cannot access project update routes', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::factory()->create();

    actingAs($user)
        ->get('/user/projects/' . $project->id . '/edit')
        ->assertForbidden();

    actingAs($user)
        ->put('/user/projects/' . $project->id, ['project_title' => 'Updated title'])
        ->assertForbidden();
});
