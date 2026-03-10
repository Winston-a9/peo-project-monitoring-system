<?php

namespace Database\Factories;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function randomContractAmount(): float
    {
        return round($this->faker->randomElement([
            $this->faker->numberBetween(500_000,  5_000_000),
            $this->faker->numberBetween(5_000_000, 50_000_000),
            $this->faker->numberBetween(50_000_000, 200_000_000),
        ]), 2);
    }

    private function inChargeOptions(): array
    {
        return [
            'Engr. Santos',
            'Engr. Reyes',
            'Engr. Cruz',
            'Engr. Dela Torre',
            'Engr. Villanueva',
            'Engr. Mendoza',
            'Engr. Garcia',
        ];
    }

    private function locationOptions(): array
    {
        return [
            'Cagayan de Oro City',
            'Iligan City',
            'Bukidnon',
            'Misamis Oriental',
            'Misamis Occidental',
            'Lanao del Norte',
            'Camiguin',
        ];
    }

    private function contractorOptions(): array
    {
        return [
            'ABC Construction Corp.',
            'XYZ Builders Inc.',
            'Prime Infra Solutions',
            'Cornerstone Development Co.',
            'Apex Contracting Services',
            'Meridian Engineering Works',
            'Summit Construction Group',
            'Horizon Civil Works Corp.',
        ];
    }

    private function projectTitles(): array
    {
        return [
            'Construction of Road and Drainage Improvement',
            'Rehabilitation of Multi-Purpose Building',
            'Installation of Solar Street Lights',
            'Construction of Flood Control Structure',
            'Repair of School Building Facilities',
            'Construction of Barangay Health Center',
            'Upgrading of Water Supply System',
            'Construction of Box Culvert',
            'Concreting of Barangay Roads',
            'Construction of Community Center',
            'Slope Protection and Retaining Wall',
            'Rehabilitation of Public Market',
            'Construction of Bridge Structure',
            'Installation of CCTV System',
            'Construction of Evacuation Center',
        ];
    }

    // ── Definition ─────────────────────────────────────────────────────────────

    public function definition(): array
    {
        $dateStarted     = $this->faker->dateTimeBetween('-2 years', '-3 months');
        $contractDays    = $this->faker->randomElement([90, 120, 150, 180, 240, 270, 365]);
        $originalExpiry  = (clone $dateStarted)->modify("+{$contractDays} days");

        $asPlanned = $this->faker->randomFloat(2, 10, 100);
        $workDone  = $this->faker->randomFloat(2, max(0, $asPlanned - 20), min(100, $asPlanned + 20));
        $slippage  = round($workDone - $asPlanned, 2);

        return [
            'in_charge'                => $this->faker->randomElement($this->inChargeOptions()),
            'project_title'            => $this->faker->randomElement($this->projectTitles()),
            'location'                 => $this->faker->randomElement($this->locationOptions()),
            'contractor'               => $this->faker->randomElement($this->contractorOptions()),
            'contract_amount'          => $this->randomContractAmount(),
            'date_started'             => $dateStarted->format('Y-m-d'),
            'contract_days'            => $contractDays,
            'original_contract_expiry' => $originalExpiry->format('Y-m-d'),
            'revised_contract_expiry'  => null,
            'status'                   => 'ongoing',
            'completed_at'             => null,
            'as_planned'               => $asPlanned,
            'work_done'                => $workDone,
            'slippage'                 => $slippage,
            'remarks_recommendation'   => $this->faker->optional(0.3)->sentence(),

            // Documents & Extensions (empty by default)
            'issuances'                => null,
            'documents_pressed'        => null,
            'time_extension'           => null,
            'extension_days'           => null,
            'cost_involved'            => null,
            'suspension_days'          => null,
            'variation_order'          => null,
            'date_requested'           => null,
            'vo_days'                  => null,
            'vo_cost'                  => null,

            // Liquidated Damages (empty by default)
            'ld_accomplished'          => null,
            'ld_unworked'              => null,
            'ld_per_day'               => null,
            'total_ld'                 => null,
            'ld_days_overdue'          => null,
        ];
    }

    // ── States ──────────────────────────────────────────────────────────────────

    public function ongoing(): static
    {
        return $this->state(function () {
            $dateStarted  = $this->faker->dateTimeBetween('-1 year', '-2 months');
            $contractDays = $this->faker->randomElement([120, 150, 180, 240, 365]);
            $expiry       = (clone $dateStarted)->modify("+{$contractDays} days");

            // Make sure expiry is in the future (more than 31 days away)
            if ($expiry <= now()->modify('+31 days')->toDateTime()) {
                $expiry = now()->modify('+60 days')->toDateTime();
            }

            $asPlanned = $this->faker->randomFloat(2, 20, 85);
            $workDone  = $this->faker->randomFloat(2, max(0, $asPlanned - 15), min(100, $asPlanned + 10));

            return [
                'date_started'             => $dateStarted->format('Y-m-d'),
                'contract_days'            => $contractDays,
                'original_contract_expiry' => $expiry->format('Y-m-d'),
                'revised_contract_expiry'  => null,
                'status'                   => 'ongoing',
                'completed_at'             => null,
                'as_planned'               => $asPlanned,
                'work_done'                => $workDone,
                'slippage'                 => round($workDone - $asPlanned, 2),
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function () {
            $dateStarted  = $this->faker->dateTimeBetween('-2 years', '-8 months');
            $contractDays = $this->faker->randomElement([90, 120, 150, 180, 240]);
            $expiry       = (clone $dateStarted)->modify("+{$contractDays} days");
            $completedAt  = $this->faker->dateTimeBetween($dateStarted, $expiry);

            return [
                'date_started'             => $dateStarted->format('Y-m-d'),
                'contract_days'            => $contractDays,
                'original_contract_expiry' => $expiry->format('Y-m-d'),
                'revised_contract_expiry'  => null,
                'status'                   => 'completed',
                'completed_at'             => $completedAt->format('Y-m-d'),
                'as_planned'               => 100.00,
                'work_done'                => 100.00,
                'slippage'                 => 0.00,
            ];
        });
    }

    public function expiring(): static
    {
        return $this->state(function () {
            $dateStarted  = $this->faker->dateTimeBetween('-1 year', '-2 months');
            $contractDays = $this->faker->randomElement([120, 150, 180, 240]);
            // Expiry within the next 1–30 days
            $daysUntil    = $this->faker->numberBetween(1, 30);
            $expiry       = now()->modify("+{$daysUntil} days");

            $asPlanned = $this->faker->randomFloat(2, 60, 95);
            $workDone  = $this->faker->randomFloat(2, max(0, $asPlanned - 20), min(100, $asPlanned + 5));

            return [
                'date_started'             => $dateStarted->format('Y-m-d'),
                'contract_days'            => $contractDays,
                'original_contract_expiry' => $expiry->format('Y-m-d'),
                'revised_contract_expiry'  => null,
                'status'                   => 'ongoing',
                'completed_at'             => null,
                'as_planned'               => $asPlanned,
                'work_done'                => $workDone,
                'slippage'                 => round($workDone - $asPlanned, 2),
            ];
        });
    }

    public function expired(): static
    {
        return $this->state(function () {
            $dateStarted  = $this->faker->dateTimeBetween('-2 years', '-6 months');
            $contractDays = $this->faker->randomElement([90, 120, 150, 180]);
            $expiry       = $this->faker->dateTimeBetween('-5 months', '-1 month');

            $asPlanned = $this->faker->randomFloat(2, 50, 100);
            $workDone  = $this->faker->randomFloat(2, 30, min(99, $asPlanned + 5));

            return [
                'date_started'             => $dateStarted->format('Y-m-d'),
                'contract_days'            => $contractDays,
                'original_contract_expiry' => $expiry->format('Y-m-d'),
                'revised_contract_expiry'  => null,
                'status'                   => 'expired',
                'completed_at'             => null,
                'as_planned'               => $asPlanned,
                'work_done'                => $workDone,
                'slippage'                 => round($workDone - $asPlanned, 2),
            ];
        });
    }

    /**
     * withExtension($count) — adds $count Time Extension entries.
     * Automatically recomputes contract_days and revised_contract_expiry.
     */
    public function withExtension(int $count = 1): static
    {
        return $this->state(function (array $attributes) use ($count) {
            $faker = $this->faker;

            $dateStarted    = $attributes['date_started']             ?? now()->subYear()->format('Y-m-d');
            $originalExpiry = $attributes['original_contract_expiry'] ?? now()->subMonths(3)->format('Y-m-d');
            $contractDays   = $attributes['contract_days']            ?? 180;

            $docs          = [];
            $teDays        = [];
            $teCosts       = [];
            $dateRequested = [];
            $totalTEDays   = 0;

            for ($i = 1; $i <= $count; $i++) {
                $days          = $faker->randomElement([30, 45, 60, 90, 120]);
                $cost          = $faker->optional(0.6)->randomFloat(2, 50_000, 500_000);
                $requestDate   = Carbon::parse($originalExpiry)
                    ->subDays($faker->numberBetween(10, 60))
                    ->format('Y-m-d');

                $docs[]          = "Time Extension {$i}";
                $teDays[]        = $days;
                $teCosts[]       = $cost;
                $dateRequested[] = $requestDate;
                $totalTEDays    += $days;
            }

            // Original base days = date_started → original_contract_expiry
            $originalDays    = (int) Carbon::parse($dateStarted)->diffInDays(Carbon::parse($originalExpiry));
            $newContractDays = $originalDays + $totalTEDays;
            $revisedExpiry   = Carbon::parse($originalExpiry)->addDays($totalTEDays)->format('Y-m-d');

            // Add a random issuance on some projects with extensions
            $issuances = null;
            if ($count >= 2 && $faker->boolean(40)) {
                $issuances = [$faker->randomElement([
                    '1st Notice of Negative Slippage',
                    '2nd Notice of Negative Slippage',
                    'Notice of Expiry',
                ])];
            }

            return [
                'documents_pressed'       => $docs,
                'time_extension'          => $count,
                'extension_days'          => $teDays,
                'cost_involved'           => $teCosts,
                'date_requested'          => $dateRequested,
                'contract_days'           => $newContractDays,
                'revised_contract_expiry' => $revisedExpiry,
                'issuances'               => $issuances,
                'variation_order'         => null,
                'vo_days'                 => null,
                'vo_cost'                 => null,
                'suspension_days'         => null,
            ];
        });
    }
}