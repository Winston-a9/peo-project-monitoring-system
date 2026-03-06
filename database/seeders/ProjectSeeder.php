<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding projects...');

        // Mixed realistic spread
        Project::factory()->count(15)->create();           // random mix
        Project::factory()->ongoing()->count(10)->create();
        Project::factory()->completed()->count(8)->create();
        Project::factory()->expiring()->count(4)->create();
        Project::factory()->expired()->count(5)->create();
        Project::factory()->withExtension(1)->count(4)->create();
        Project::factory()->withExtension(2)->count(3)->create();
        Project::factory()->withExtension(3)->count(1)->create();

        $this->command->info('Done! ' . Project::count() . ' projects seeded.');
    }
}