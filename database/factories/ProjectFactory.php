<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $inChargeList = [
            'Engr. Santos', 'Engr. Reyes', 'Engr. Cruz', 'Engr. Dela Torre',
            'Engr. Villanueva', 'Engr. Mendoza', 'Engr. Bautista', 'Engr. Garcia',
        ];

        $locationList = [
            'Cagayan de Oro City', 'Iligan City', 'Bukidnon', 'Misamis Oriental',
            'Lanao del Norte', 'Camiguin', 'Misamis Occidental', 'Davao del Norte',
        ];

        $contractorList = [
            'ABC Construction Corp.',
            'XYZ Builders Inc.',
            'Prime Infrastructure Co.',
            'Delta Engineering Works',
            'Sunrise Constructions',
            'Apex Development Group',
            'Metro Builders Corp.',
            'Golden Gate Construction',
        ];

        $projectTypes = [
            'Construction of Road', 'Rehabilitation of Bridge', 'Installation of Drainage System',
            'Construction of Multi-Purpose Building', 'Improvement of Farm-to-Market Road',
            'Construction of Seawall', 'Rehabilitation of Public Market',
            'Construction of School Building', 'Flood Control Project',
            'Construction of Water Supply System',
        ];

        $barangays = [
            'Brgy. Lapasan', 'Brgy. Macabalan', 'Brgy. Nazareth', 'Brgy. Carmen',
            'Brgy. Agusan', 'Brgy. Bulua', 'Brgy. Canitoan', 'Brgy. Iponan',
        ];

        $issuanceOptions = [
            'Notice to Proceed', 'Suspension Order', 'Resume Order',
            'Notice of Default', 'Show Cause Letter', 'Warning Letter',
        ];

        $documentOptions = [
            'Time Extension 1', 'Time Extension 2', 'Time Extension 3',
            'Variation Order 1', 'Variation Order 2',
            'Notice to Proceed', 'Certificate of Completion',
        ];

        // Date logic
        $dateStarted      = $this->faker->dateTimeBetween('-2 years', '-3 months');
        $originalExpiry   = (clone $dateStarted)->modify('+' . $this->faker->numberBetween(6, 18) . ' months');
        $hasExtension     = $this->faker->boolean(35);
        $extensionDays    = [];
        $revisedExpiry    = null;
        $timeExtension    = 0;

        if ($hasExtension) {
            $numExtensions = $this->faker->numberBetween(1, 3);
            $totalDays     = 0;
            for ($i = 0; $i < $numExtensions; $i++) {
                $days           = $this->faker->numberBetween(15, 60);
                $extensionDays[] = $days;
                $totalDays      += $days;
            }
            $timeExtension = $numExtensions;
            $revisedExpiry = (clone $originalExpiry)->modify("+{$totalDays} days");
        }

        $effectiveExpiry = $revisedExpiry ?? $originalExpiry;
        $isExpired       = $effectiveExpiry < now();

        // Status
        if ($isExpired && $this->faker->boolean(60)) {
            $status      = 'completed';
            $completedAt = $this->faker->dateTimeBetween($dateStarted, $effectiveExpiry);
            $workDone    = 100;
            $asPlanned   = 100;
        } elseif ($isExpired) {
            $status      = 'ongoing';
            $completedAt = null;
            $workDone    = $this->faker->numberBetween(60, 95);
            $asPlanned   = 100;
        } else {
            $status      = 'ongoing';
            $completedAt = null;
            $asPlanned   = $this->faker->numberBetween(20, 95);
            $workDone    = $asPlanned + $this->faker->numberBetween(-15, 15);
            $workDone    = max(0, min(100, $workDone));
        }

        $slippage = round($workDone - $asPlanned, 2);

        // Documents & issuances
        $numDocs      = $this->faker->numberBetween(0, count($extensionDays) + 2);
        $docs         = [];
        $docDays      = [];
        $teIndex      = 0;

        for ($i = 0; $i < $numDocs; $i++) {
            $doc = $this->faker->randomElement($documentOptions);
            $docs[] = $doc;
            if (str_starts_with($doc, 'Time Extension') && isset($extensionDays[$teIndex])) {
                $docDays[] = $extensionDays[$teIndex];
                $teIndex++;
            } else {
                $docDays[] = null;
            }
        }

        $numIssuances = $this->faker->numberBetween(0, 3);
        $issuances    = $numIssuances > 0
            ? $this->faker->randomElements($issuanceOptions, $numIssuances)
            : [];

        return [
            'in_charge'                => $this->faker->randomElement($inChargeList),
            'project_title'            => $this->faker->randomElement($projectTypes) . ' at ' . $this->faker->randomElement($barangays) . ', ' . $this->faker->randomElement($locationList),
            'location'                 => $this->faker->randomElement($locationList),
            'contractor'               => $this->faker->randomElement($contractorList),
            'contract_amount'          => $this->faker->randomFloat(2, 500_000, 50_000_000),
            'date_started'             => $dateStarted->format('Y-m-d'),
            'original_contract_expiry' => $originalExpiry->format('Y-m-d'),
            'revised_contract_expiry'  => $revisedExpiry?->format('Y-m-d'),
            'as_planned'               => $asPlanned,
            'work_done'                => $workDone,
            'slippage'                 => $slippage,
            'status'                   => $status,
            'completed_at'             => $completedAt ?? null,
            'time_extension'           => (string) $timeExtension,
            'extension_days'           => json_encode($extensionDays ?: []),
            'documents_pressed'        => json_encode($docs ?: []),
            'issuances'                => json_encode($issuances ?: []),
            'remarks_recommendation'   => $this->faker->boolean(50)
                ? $this->faker->sentence($this->faker->numberBetween(10, 30))
                : null,
        ];
    }

    // ── States ──

    public function completed(): static
    {
        return $this->state(fn(array $attr) => [
            'status'      => 'completed',
            'work_done'   => 100,
            'as_planned'  => 100,
            'slippage'    => 0,
            'completed_at'=> now()->subDays(rand(1, 90))->format('Y-m-d'),
        ]);
    }

    public function ongoing(): static
    {
        return $this->state(function (array $attr) {
            $asPlanned = $this->faker->numberBetween(20, 90);
            $workDone  = max(0, min(100, $asPlanned + $this->faker->numberBetween(-10, 10)));
            return [
                'status'       => 'ongoing',
                'as_planned'   => $asPlanned,
                'work_done'    => $workDone,
                'slippage'     => round($workDone - $asPlanned, 2),
                'completed_at' => null,
                'original_contract_expiry' => now()->addMonths(rand(2, 12))->format('Y-m-d'),
            ];
        });
    }

    public function expiring(): static
    {
        return $this->state(fn(array $attr) => [
            'status'                   => 'ongoing',
            'original_contract_expiry' => now()->addDays(rand(1, 29))->format('Y-m-d'),
            'revised_contract_expiry'  => null,
            'completed_at'             => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn(array $attr) => [
            'status'                   => 'ongoing',
            'original_contract_expiry' => now()->subMonths(rand(1, 6))->format('Y-m-d'),
            'revised_contract_expiry'  => null,
            'completed_at'             => null,
        ]);
    }

    public function withExtension(int $extensions = 1): static
    {
        return $this->state(function (array $attr) use ($extensions) {
            $days  = [];
            $total = 0;
            for ($i = 0; $i < $extensions; $i++) {
                $d     = rand(15, 60);
                $days[]= $d;
                $total+= $d;
            }
            $base    = \Carbon\Carbon::parse($attr['original_contract_expiry']);
            $revised = $base->copy()->addDays($total);
            return [
                'time_extension'          => $extensions,
                'extension_days'          => $days,
                'revised_contract_expiry' => $revised->format('Y-m-d'),
            ];
        });
    }
}