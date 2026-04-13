<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $inChargeOptions = ['Engr. Santos', 'Engr. Reyes', 'Engr. Cruz', 'Engr. Bautista', 'Engr. Garcia'];
        $locationOptions = ['Davao City', 'Tagum City', 'Digos City', 'Mati City', 'Panabo City', 'Samal Island'];
        $contractorOptions = [
            'ABC Construction Corp.',
            'XYZ Builders Inc.',
            'Prime Infrastructure Co.',
            'Solid Works Construction',
            'Delta Engineering Services',
            'Metro Build Solutions',
        ];

        $dateStarted    = $this->faker->dateTimeBetween('-2 years', '-3 months');
        $contractDays   = $this->faker->numberBetween(90, 365);
        $originalExpiry = Carbon::instance($dateStarted)->addDays($contractDays);
        $contractAmount = $this->faker->randomFloat(2, 500000, 50000000);

        // ── Extensions ──────────────────────────────────────────────
        $hasTe = $this->faker->boolean(40);
        $hasVo = $this->faker->boolean(30);
        $hasSo = $this->faker->boolean(20);

        $documentsPressed = [];
        $extensionDays    = [];
        $costInvolved     = [];
        $dateRequested    = [];
        $voDays           = [];
        $voCost           = [];
        $suspensionDays   = null;
        $revisedExpiry    = null;
        $totalTeDays      = 0;
        $totalVoDays      = 0;
        $totalSoDays      = 0;

        if ($hasTe) {
            $teCount = $this->faker->numberBetween(1, 3);
            for ($i = 1; $i <= $teCount; $i++) {
                $days               = $this->faker->numberBetween(15, 90);
                $cost               = $this->faker->boolean(60) ? $this->faker->randomFloat(2, 10000, 500000) : null;
                $documentsPressed[] = "Time Extension {$i}";
                $extensionDays[]    = $days;
                $costInvolved[]     = $cost;
                $dateRequested[]    = Carbon::instance($dateStarted)->addDays(rand(30, 180))->format('Y-m-d');
                $totalTeDays       += $days;
            }
        }

        if ($hasVo) {
            $voCount = $this->faker->numberBetween(1, 2);
            for ($i = 1; $i <= $voCount; $i++) {
                $days               = $this->faker->numberBetween(10, 60);
                $cost               = $this->faker->boolean(50) ? $this->faker->randomFloat(2, 5000, 200000) : null;
                $documentsPressed[] = "Variation Order {$i}";
                $voDays[]           = $days;
                $voCost[]           = $cost;
                $dateRequested[]    = Carbon::instance($dateStarted)->addDays(rand(60, 240))->format('Y-m-d');
                $totalVoDays       += $days;
            }
        }

        if ($hasSo) {
            $documentsPressed[] = 'Suspension Order';
            $totalSoDays        = $this->faker->numberBetween(10, 45);
            $suspensionDays     = $totalSoDays;
        }

        $totalExtDays = $totalTeDays + $totalVoDays + $totalSoDays;
        if ($totalExtDays > 0) {
            $revisedExpiry = $originalExpiry->copy()->addDays($totalExtDays)->format('Y-m-d');
        }

        // ── Adjusted contract days & amount ──────────────────────────
        $adjustedContractDays = $contractDays + $totalTeDays + $totalVoDays;
        $totalCostAdjustment  = array_sum(array_filter(array_merge($costInvolved, $voCost)));
        $adjustedAmount       = max(0, $contractAmount + $totalCostAdjustment);

        // ── Status ───────────────────────────────────────────────────
        $effectiveExpiry = $revisedExpiry ?? $originalExpiry->format('Y-m-d');
        $isExpired       = Carbon::parse($effectiveExpiry)->isPast();
        $statusOptions   = $isExpired
            ? ['expired', 'completed']
            : ['ongoing', 'ongoing', 'ongoing', 'completed'];
        $status = $this->faker->randomElement($statusOptions);

        $completedAt = null;
        if ($status === 'completed') {
            $completedAt = Carbon::parse($effectiveExpiry)
                ->subDays($this->faker->numberBetween(0, 30))
                ->format('Y-m-d');
        }

        // ── Work progress (decimal 5,2 — max 999.99, use 0–100) ──────
        $asPlanned = match ($status) {
            'completed' => 100.00,
            'expired'   => round($this->faker->randomFloat(2, 60, 95), 2),
            default     => round($this->faker->randomFloat(2, 10, 100), 2),
        };
        $workDone = match ($status) {
            'completed' => 100.00,
            'expired'   => round($this->faker->randomFloat(2, 40, $asPlanned), 2),
            default     => round($this->faker->randomFloat(2, 0, min($asPlanned + 20, 100)), 2),
        };
        $slippage = round($workDone - $asPlanned, 2);

        // ── Issuances ────────────────────────────────────────────────
        $issuanceOptions = [
            '1st Notice of Negative Slippage',
            '2nd Notice of Negative Slippage',
            '3rd Notice of Negative Slippage',
            'Liquidated Damages',
            'Notice to Terminate',
            'Notice of Expiry',
            'Performance Bond',
        ];
        $issuances = $slippage < -10
            ? $this->faker->randomElements($issuanceOptions, $this->faker->numberBetween(1, 3))
            : ($this->faker->boolean(25) ? [$this->faker->randomElement($issuanceOptions)] : []);

        // ── Billing ──────────────────────────────────────────────────
        $billingAmounts    = [];
        $billingDates      = [];
        $totalBilled       = 0;
        $remainingBalance  = null;
        $totalAmountBilled = null;

        // Advance billing & retention (only when there is billing activity)
        $advanceBillingPct    = null;
        $advanceBillingAmount = null;
        $retentionPct         = null;
        $retentionAmount      = null;

        if ($this->faker->boolean(50)) {
            $billingCount = $this->faker->numberBetween(1, 4);
            for ($b = 0; $b < $billingCount; $b++) {
                $amt              = $this->faker->randomFloat(2, 50000, $adjustedAmount * 0.3);
                $billingAmounts[] = $amt;
                $billingDates[]   = Carbon::instance($dateStarted)->addDays(rand(30, 300))->format('Y-m-d');
                $totalBilled     += $amt;
            }
            $totalAmountBilled = round($totalBilled, 2);
            $remainingBalance  = round($contractAmount - $totalBilled, 2);

            // Advance billing: typically 15% or 20% of contract amount
            if ($this->faker->boolean(60)) {
                $advanceBillingPct    = $this->faker->randomElement([10.00, 15.00, 20.00]);
                $advanceBillingAmount = round($contractAmount * $advanceBillingPct / 100, 2);
            }

            // Retention: typically 5% or 10%
            if ($this->faker->boolean(70)) {
                $retentionPct    = $this->faker->randomElement([5.00, 10.00]);
                $retentionAmount = round($totalBilled * $retentionPct / 100, 2);
            }
        }

        // ── Liquidated Damages ───────────────────────────────────────
        $ldAccomplished = null;
        $ldUnworked     = null;
        $ldPerDay       = null;
        $totalLd        = null;
        $ldDaysOverdue  = null;

        if ($status === 'expired' && $this->faker->boolean(60)) {
            $ldAccomplished = round($workDone, 3);                          // decimal(5,3)
            $ldUnworked     = round(100 - $ldAccomplished, 2);             // decimal(5,2)
            $ldPerDayFull   = (100 - $ldAccomplished) / 100 * $contractAmount * 0.001;
            $ldPerDay       = round($ldPerDayFull, 2);
            $ldDaysOverdue  = $this->faker->numberBetween(5, 120);
            $totalLd        = round($ldPerDayFull * $ldDaysOverdue, 2);
        }

        // ── contract_id ───────────────────────────────────────────────
        // Format: PROJ-{YEAR}-{5-digit sequence}, unique via static counter + random suffix
        static $sequence = 0;
        $sequence++;
        $year       = Carbon::instance($dateStarted)->year;
        $contractId = sprintf('PROJ-%d-%05d', $year, $sequence);

        return [
            'contract_id'              => $contractId,
            'in_charge'                => $this->faker->randomElement($inChargeOptions),
            'project_title'            => 'Construction of ' . $this->faker->randomElement([
                'Barangay Road', 'Multi-Purpose Hall', 'Water System', 'Drainage Canal',
                'School Building', 'Health Center', 'Bridge', 'Market Stall', 'Sports Complex',
            ]) . ' in ' . $this->faker->randomElement($locationOptions),
            'location'                 => $this->faker->randomElement($locationOptions),
            'contractor'               => $this->faker->randomElement($contractorOptions),
            'date_started'             => Carbon::instance($dateStarted)->format('Y-m-d'),
            'contract_days'            => $adjustedContractDays,
            'original_contract_expiry' => $originalExpiry->format('Y-m-d'),
            'revised_contract_expiry'  => $revisedExpiry,
            'status'                   => $status,
            'completed_at'             => $completedAt,
            'original_contract_amount' => $contractAmount,
            'as_planned'               => $asPlanned,
            'work_done'                => $workDone,
            'slippage'                 => $slippage,
            'progress_updated_at'      => Carbon::instance($dateStarted)
                                            ->addDays(rand(30, 300))
                                            ->format('Y-m-d'),
            'remarks_recommendation'   => $this->faker->boolean(30) ? $this->faker->sentences(2, true) : null,

            // Documents & Extensions
            'issuances'                => empty($issuances) ? null : $issuances,
            'documents_pressed'        => empty($documentsPressed) ? null : $documentsPressed,
            'time_extension'           => $hasTe ? count($extensionDays) : null,
            'extension_days'           => empty($extensionDays) ? null : $extensionDays,
            'cost_involved'            => empty($costInvolved) ? null : $costInvolved,
            'suspension_days'          => $suspensionDays,
            'variation_order'          => $hasVo ? count($voDays) : null,
            'date_requested'           => empty($dateRequested) ? null : $dateRequested,
            'vo_days'                  => empty($voDays) ? null : $voDays,
            'vo_cost'                  => empty($voCost) ? null : $voCost,
            'performance_bond_date'    => $this->faker->boolean(20)
                ? Carbon::instance($dateStarted)->addDays(rand(180, 730))->format('Y-m-d')
                : null,

            // Billing
            'billing_amounts'          => empty($billingAmounts) ? null : $billingAmounts,
            'billing_dates'            => empty($billingDates) ? null : $billingDates,
            'total_amount_billed'      => $totalAmountBilled,
            'remaining_balance'        => $remainingBalance,
            'advance_billing_pct'      => $advanceBillingPct,
            'advance_billing_amount'   => $advanceBillingAmount,
            'retention_pct'            => $retentionPct,
            'retention_amount'         => $retentionAmount,

            // Liquidated Damages
            'ld_accomplished'          => $ldAccomplished,
            'ld_unworked'              => $ldUnworked,
            'ld_per_day'               => $ldPerDay,
            'total_ld'                 => $totalLd,
            'ld_days_overdue'          => $ldDaysOverdue,
        ];
    }
}