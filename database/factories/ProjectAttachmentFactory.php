<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectAttachmentFactory extends Factory
{
    protected $model = ProjectAttachment::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'path' => 'photos/' . $this->faker->slug() . '.jpg',
            'original_name' => $this->faker->slug() . '.jpg',
            'caption' => $this->faker->sentence(),
        ];
    }
}
