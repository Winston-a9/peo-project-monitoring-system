<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks before truncating
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\ProjectLog::truncate();
        Project::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── 8 random mixed-status projects ──────────────────────────
        Project::factory(8)->create();

        // ── 4 completed projects ─────────────────────────────────────
        Project::factory(4)->create([
            'status'       => 'completed',
            'completed_at' => now()->subDays(rand(10, 90))->format('Y-m-d'),
            'as_planned'   => 100.000,
            'work_done'    => 100.000,
            'slippage'     => 0.000,
            // Clear LD — completed projects don't accumulate LD
            'ld_accomplished' => null,
            'ld_unworked'     => null,
            'ld_per_day'      => null,
            'total_ld'        => null,
            'ld_days_overdue' => null,
        ]);

        // ── 3 expired projects (past original expiry, no revision) ───
        Project::factory(3)->create([
            'status'                   => 'expired',
            'completed_at'             => null,
            'original_contract_expiry' => now()->subDays(rand(10, 60))->format('Y-m-d'),
            'revised_contract_expiry'  => null,
        ]);

        // ── 3 expiring soon (within 30 days) ────────────────────────
        Project::factory(3)->create([
            'status'                   => 'ongoing',
            'completed_at'             => null,
            'original_contract_expiry' => now()->addDays(rand(5, 29))->format('Y-m-d'),
            'revised_contract_expiry'  => null,
            // Clear LD — not yet overdue
            'ld_accomplished' => null,
            'ld_unworked'     => null,
            'ld_per_day'      => null,
            'total_ld'        => null,
            'ld_days_overdue' => null,
        ]);

        // ── 5 healthy ongoing projects ───────────────────────────────
        Project::factory(5)->create([
            'status'                   => 'ongoing',
            'completed_at'             => null,
            'original_contract_expiry' => now()->addDays(rand(45, 300))->format('Y-m-d'),
            // Clear LD — not overdue
            'ld_accomplished' => null,
            'ld_unworked'     => null,
            'ld_per_day'      => null,
            'total_ld'        => null,
            'ld_days_overdue' => null,
        ]);

        $this->command->info('✅ Projects seeded: ' . Project::count() . ' total records');
    }
}