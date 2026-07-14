<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\LdHistory;

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
        LdHistory::truncate();
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
                'ld_status'       => 'inactive',
                'ld_start_date'   => null,
                'ld_end_date'     => null,
                'ld_status'       => 'inactive',
                'ld_start_date'   => null,
                'ld_end_date'     => null,
                'ld_status'       => 'inactive',
                'ld_start_date'   => null,
                'ld_end_date'     => null,
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
            Project::factory(1)->create([
                'division' => $division,
                'geotagged_location' => sprintf(
                    'GPS %.4f, %.4f',
                    fake()->latitude(8.5, 14.0),
                    fake()->longitude(120.0, 127.0)
                ),
                'fund_source' => fake()->randomElement([
                    'General Fund',
                    '20% Development Fund',
                    'Supplemental Budget 1',
                    'Supplemental Budget 2',
                    'National Fund',
                ]),
            ])->each(function ($project) use ($division) {
                $contractAmount = $project->original_contract_amount;
                $workDone       = round(fake()->randomFloat(2, 40, 90), 2);
                $asPlanned      = round(fake()->randomFloat(2, max($workDone, 60), 95), 2);
                $ldAccomplished = round($workDone, 3);
                $ldUnworked     = round(100 - $ldAccomplished, 2);
                $ldPerDayFull   = (100 - $ldAccomplished) / 100 * $contractAmount * 0.001;
                $ldDaysOverdue  = fake()->numberBetween(30, 120); // min 30 so we get a few months of history
                $expiry         = now()->subDays(rand(40, 90))->format('Y-m-d');
                $ldStartDate    = $expiry; // LD clock starts at expiry, matching controller logic
                $ldStatus       = 'active';

                $project->update([
                    'division'                 => $division,
                    'status'                   => 'expired',
                    'completed_at'             => null,
                    'original_contract_expiry' => $expiry,
                    'revised_contract_expiry'  => null,
                    'as_planned'               => $asPlanned,
                    'work_done'                => $workDone,
                    'slippage'                 => round($workDone - $asPlanned, 2),
                    'ld_accomplished'          => $ldAccomplished,
                    'ld_unworked'              => $ldUnworked,
                    'ld_per_day'               => round($ldPerDayFull, 2),
                    'total_ld'                 => round($ldPerDayFull * $ldDaysOverdue, 2),
                    'ld_days_overdue'          => $ldDaysOverdue,
                    'ld_status'                => $ldStatus,
                    'ld_start_date'            => $ldStartDate,
                    'ld_end_date'              => null,
                ]);

                $this->seedLdHistory($project->fresh());
            });
        }

        $this->command->info('✅ Projects seeded: ' . Project::count() . ' total records');
        $this->command->info('   Divisions covered: ' . implode(', ', self::DIVISIONS));

        foreach (self::DIVISIONS as $division) {
            $count = Project::where('division', $division)->count();
            $this->command->info("   {$division}: {$count} projects");
        }
    }

    /**
     * Backfill monthly LD snapshots for a project that already has
     * ld_start_date / ld_per_day set, mirroring the cumulative-snapshot
     * approach used in ProjectController::update() Step 11.
     */
    private function seedLdHistory(Project $project): void
    {
        if (!$project->ld_start_date || !$project->ld_per_day) {
            return;
        }

        $ldStart = Carbon::parse($project->ld_start_date);
        $end = $project->ld_end_date ? Carbon::parse($project->ld_end_date) : now(config('app.timezone'));

        $cursor = $ldStart->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            $monthEnd = $cursor->copy()->endOfMonth();
            $cappedEnd = $monthEnd->gt($end) ? $end : $monthEnd;
            $daysAtMonth = max(0, (int) $ldStart->diffInDays($cappedEnd, false) - 1);

            LdHistory::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'month' => $cursor->copy()->startOfMonth()->toDateString(),
                ],
                [
                    'ld_accomplished' => $project->ld_accomplished,
                    'ld_unworked' => $project->ld_unworked,
                    'ld_per_day' => $project->ld_per_day,
                    'days_overdue' => $daysAtMonth,
                    'ld_amount' => round((float) $project->ld_per_day * $daysAtMonth, 2),
                    'remarks' => 'Seeded monthly LD snapshot for demo data.',
                    'updated_by' => null,
                ]
            );

            $cursor->addMonth();
        }
    }
}