<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * The five official divisions — must match ProjectController::DIVISIONS exactly.
     */
    private const DIVISIONS = [
        'Maintenance',
        'Construction',
        'Water Works',
        'Material Testing and Quality Control (MTQA)',
        'Motorpool',
    ];

    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\ProjectLog::truncate();
        Project::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── Seed projects per division so every division admin has data ──
        foreach (self::DIVISIONS as $division) {

            // 2 random mixed-status projects per division
            Project::factory(2)->create(['division' => $division]);

            // 1 completed project per division
            Project::factory(1)->create([
                'division'        => $division,
                'status'          => 'completed',
                'completed_at'    => now()->subDays(rand(10, 90))->format('Y-m-d'),
                'as_planned'      => 100.00,
                'work_done'       => 100.00,
                'slippage'        => 0.00,
                'ld_accomplished' => null,
                'ld_unworked'     => null,
                'ld_per_day'      => null,
                'total_ld'        => null,
                'ld_days_overdue' => null,
            ]);

            // 1 expiring project per division
            Project::factory(1)->create([
                'division'                 => $division,
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

            // 1 healthy ongoing project per division
            Project::factory(1)->create([
                'division'                 => $division,
                'status'                   => 'ongoing',
                'completed_at'             => null,
                'original_contract_expiry' => now()->addDays(rand(45, 300))->format('Y-m-d'),
                'ld_accomplished'          => null,
                'ld_unworked'              => null,
                'ld_per_day'               => null,
                'total_ld'                 => null,
                'ld_days_overdue'          => null,
            ]);
        }

        // ── Expired projects with LD (spread across divisions) ───────
        $expiredDivisions = array_merge(self::DIVISIONS, self::DIVISIONS); // allow repeats
        shuffle($expiredDivisions);
        $expiredDivisions = array_slice($expiredDivisions, 0, 5); // 5 expired total

        foreach ($expiredDivisions as $division) {
            Project::factory(1)->create(['division' => $division])->each(function ($project) use ($division) {
                $contractAmount = $project->original_contract_amount;
                $workDone       = round(fake()->randomFloat(2, 40, 90), 2);
                $asPlanned      = round(fake()->randomFloat(2, max($workDone, 60), 95), 2);
                $ldAccomplished = round($workDone, 3);
                $ldUnworked     = round(100 - $ldAccomplished, 2);
                $ldPerDayFull   = (100 - $ldAccomplished) / 100 * $contractAmount * 0.001;
                $ldDaysOverdue  = fake()->numberBetween(5, 120);

                $project->update([
                    'division'                 => $division,
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
        }

        $this->command->info('✅ Projects seeded: ' . Project::count() . ' total records');
        $this->command->info('   Divisions covered: ' . implode(', ', self::DIVISIONS));

        foreach (self::DIVISIONS as $division) {
            $count = Project::where('division', $division)->count();
            $this->command->info("   {$division}: {$count} projects");
        }
    }
}