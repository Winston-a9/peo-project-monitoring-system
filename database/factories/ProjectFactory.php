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
        $contractAmount = $this->faker->randomFloat(2, 500000, 50000000); // original amount

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
                $days             = $this->faker->numberBetween(15, 90);
                $cost             = $this->faker->boolean(60) ? $this->faker->randomFloat(2, 10000, 500000) : null;
                $documentsPressed[] = "Time Extension {$i}";
                $extensionDays[]  = $days;
                $costInvolved[]   = $cost;
                $dateRequested[]  = Carbon::instance($dateStarted)->addDays(rand(30, 180))->format('Y-m-d');
                $totalTeDays     += $days;
            }
        }

        if ($hasVo) {
            $voCount = $this->faker->numberBetween(1, 2);
            for ($i = 1; $i <= $voCount; $i++) {
                $days             = $this->faker->numberBetween(10, 60);
                $cost             = $this->faker->boolean(50) ? $this->faker->randomFloat(2, 5000, 200000) : null;
                $documentsPressed[] = "Variation Order {$i}";
                $voDays[]         = $days;
                $voCost[]         = $cost;
                $dateRequested[]  = Carbon::instance($dateStarted)->addDays(rand(60, 240))->format('Y-m-d');
                $totalVoDays     += $days;
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

        // ── Adjusted contract amount (original + TE/VO costs) ────────
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

        // ── Work progress ────────────────────────────────────────────
        $asPlanned = match ($status) {
            'completed' => 100.000,
            'expired'   => round($this->faker->randomFloat(3, 60, 95), 3),
            default     => round($this->faker->randomFloat(3, 10, 100), 3),
        };
        $workDone = match ($status) {
            'completed' => 100.000,
            'expired'   => round($this->faker->randomFloat(3, 40, $asPlanned), 3),
            default     => round($this->faker->randomFloat(3, 0, min($asPlanned + 20, 100)), 3),
        };
        $slippage = round($workDone - $asPlanned, 3);

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

        if ($this->faker->boolean(50)) {
            $billingCount = $this->faker->numberBetween(1, 4);
            for ($b = 0; $b < $billingCount; $b++) {
                $amt              = $this->faker->randomFloat(2, 50000, $adjustedAmount * 0.3);
                $billingAmounts[] = $amt;
                $billingDates[]   = Carbon::instance($dateStarted)->addDays(rand(30, 300))->format('Y-m-d');
                $totalBilled     += $amt;
            }
            $totalAmountBilled = round($totalBilled, 2);
            // Remaining balance uses ORIGINAL contract amount, not adjusted
            $remainingBalance  = round($contractAmount - $totalBilled, 2);
        }

        // ── Liquidated Damages ───────────────────────────────────────
        // Formula: LD/day = (unworked / 100) × ORIGINAL contract amount × 0.001
        // unworked = 100 - accomplished (full precision, NOT rounded)
        $ldAccomplished = null;
        $ldUnworked     = null;
        $ldPerDay       = null;
        $totalLd        = null;
        $ldDaysOverdue  = null;

        if ($status === 'expired' && $this->faker->boolean(60)) {
            $ldAccomplished = round($workDone, 3);                          // 3dp, matches migration 5,3
            $ldUnworked     = round(100 - $ldAccomplished, 2);             // 2dp for display
            $ldPerDayFull   = (100 - $ldAccomplished) / 100 * $contractAmount * 0.001; // full precision calc
            $ldPerDay       = round($ldPerDayFull, 2);
            $ldDaysOverdue  = $this->faker->numberBetween(5, 120);
            $totalLd        = round($ldPerDayFull * $ldDaysOverdue, 2);
        }

        return [
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
            'contract_amount'          => $adjustedAmount,           // adjusted (original + TE/VO costs)
            'original_contract_amount' => $contractAmount,           // always the raw original
            'as_planned'               => $asPlanned,
            'work_done'                => $workDone,
            'slippage'                 => $slippage,
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

            // Liquidated Damages
            'ld_accomplished'          => $ldAccomplished,
            'ld_unworked'              => $ldUnworked,
            'ld_per_day'               => $ldPerDay,
            'total_ld'                 => $totalLd,
            'ld_days_overdue'          => $ldDaysOverdue,
        ];
    }
}