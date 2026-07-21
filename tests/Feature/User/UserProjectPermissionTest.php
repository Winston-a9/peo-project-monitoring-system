<?php

use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\User;

test('regular users cannot access project creation routes', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get('/user/projects/create')
        ->assertForbidden();
});

test('regular users cannot access project update routes', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get('/user/projects/' . $project->id . '/edit')
        ->assertForbidden();

    $this->actingAs($user)
        ->put('/user/projects/' . $project->id, ['project_title' => 'Updated title'])
        ->assertForbidden();
});

test('regular users can view the project detail page with the updated layout', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::factory()->create([
        'project_title' => 'Sample Road Project',
        'location' => 'Davao City',
        'contractor' => 'ABC Construction Corp.',
        'remarks_recommendation' => 'Proceed with monitoring.',
        'issuances' => ['1st Notice of Negative Slippage'],
    ]);

    ProjectAttachment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'path' => 'photos/sample.jpg',
        'original_name' => 'sample.jpg',
        'caption' => 'Latest progress',
    ]);

    $this->actingAs($user)
        ->get('/user/projects/' . $project->id)
        ->assertOk()
        ->assertSee('Project Snapshot')
        ->assertSee('Overview')
        ->assertSee('Activity');
});
