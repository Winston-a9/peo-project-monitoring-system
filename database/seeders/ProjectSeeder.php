<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;


class ProjectSeeder extends Seeder
{
    public function run(): void
{
    // ✅ Disable foreign key checks before truncating
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    \App\Models\ProjectLog::truncate();
    Project::truncate();
    
    // ✅ Re-enable after truncating
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // Spread across all statuses
    Project::factory(8)->create();

    Project::factory(4)->create([
        'status'       => 'completed',
        'completed_at' => now()->subDays(rand(10, 90))->format('Y-m-d'),
        'as_planned'   => 100.00,
        'work_done'    => 100.00,
        'slippage'     => 0.00,
    ]);

    Project::factory(3)->create([
        'status'                   => 'expired',
        'completed_at'             => null,
        'original_contract_expiry' => now()->subDays(rand(10, 60))->format('Y-m-d'),
        'revised_contract_expiry'  => null,
    ]);

    Project::factory(5)->create([
        'status'                   => 'ongoing',
        'completed_at'             => null,
        'original_contract_expiry' => now()->addDays(rand(45, 300))->format('Y-m-d'),
    ]);

    $this->command->info('✅ Projects seeded: ' . Project::count() . ' total records');
    }
}