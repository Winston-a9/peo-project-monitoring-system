<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
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
            'as_planned'   => 100.00,
            'work_done'    => 100.00,
            'slippage'     => 0.00,
            'ld_accomplished' => null,
            'ld_unworked'     => null,
            'ld_per_day'      => null,
            'total_ld'        => null,
            'ld_days_overdue' => null,
        ]);

        // ── 3 expired projects with LD ───────────────────────────────
        Project::factory(3)->create()->each(function ($project) {
            // Re-resolve a contract amount since factory already saved the record;
            // pull it back so LD math is consistent.
            $contractAmount = $project->original_contract_amount;
            $workDone       = round(fake()->randomFloat(2, 40, 90), 2);
            $asPlanned      = round(fake()->randomFloat(2, max($workDone, 60), 95), 2);
            $ldAccomplished = round($workDone, 3);
            $ldUnworked     = round(100 - $ldAccomplished, 2);
            $ldPerDayFull   = (100 - $ldAccomplished) / 100 * $contractAmount * 0.001;
            $ldDaysOverdue  = fake()->numberBetween(5, 120);

            $project->update([
                'status'                   => 'expired',
                'completed_at'             => null,
                'original_contract_expiry' => now()->subDays(rand(10, 60))->format('Y-m-d'),
                'revised_contract_expiry'  => null,
                'as_planned'               => $asPlanned,
                'work_done'                => $workDone,
                'slippage'                 => round($workDone - $asPlanned, 2),
                'ld_accomplished'          => $ldAccomplished,
                'ld_unworked'              => $ldUnworked,
                'ld_per_day'               => round($ldPerDayFull, 2),
                'total_ld'                 => round($ldPerDayFull * $ldDaysOverdue, 2),
                'ld_days_overdue'          => $ldDaysOverdue,
            ]);
        });

        // ── 3 expiring soon (within 30 days) ────────────────────────
        Project::factory(3)->create([
            'status'                   => 'ongoing',
            'completed_at'             => null,
            'original_contract_expiry' => now()->addDays(rand(5, 29))->format('Y-m-d'),
            'revised_contract_expiry'  => null,
            'ld_accomplished'          => null,
            'ld_unworked'              => null,
            'ld_per_day'               => null,
            'total_ld'                 => null,
            'ld_days_overdue'          => null,
        ]);

        // ── 5 healthy ongoing projects ───────────────────────────────
        Project::factory(5)->create([
            'status'                   => 'ongoing',
            'completed_at'             => null,
            'original_contract_expiry' => now()->addDays(rand(45, 300))->format('Y-m-d'),
            'ld_accomplished'          => null,
            'ld_unworked'              => null,
            'ld_per_day'               => null,
            'total_ld'                 => null,
            'ld_days_overdue'          => null,
        ]);

        $this->command->info('✅ Projects seeded: ' . Project::count() . ' total records');
    }
}