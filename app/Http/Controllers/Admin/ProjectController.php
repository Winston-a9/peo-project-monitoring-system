<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ProjectReportPdf;

class ProjectController extends Controller
{
    // ============================================================
    // SECTION 1: LISTING & DISPLAY
    // ============================================================

    /**
     * List all projects with pagination.
     */
    public function index()
    {
        $perPage  = in_array((int)request('per_page', 10), [10, 25, 50]) ? (int)request('per_page', 10) : 10;
        $projects = Project::paginate($perPage)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show a single project with its sorted activity logs.
     */
    public function show(Project $project)
    {
        $project->load(['logs.user' => fn($q) => $q->select('id', 'name')]);
        $project->logs = $project->logs->sortByDesc('created_at');

        return view('admin.projects.show', compact('project'));
    }


    // ============================================================
    // SECTION 2: CREATE
    // ============================================================

    /**
     * Show the create project form.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Validate and store a new project.
     * Auto-derives status from contract expiry date.
     */
    public function store(Request $request)
    {
        $request->validate([
            'in_charge'                => 'required|string|max:255',
            'project_title'            => 'required|string|max:255',
            'location'                 => 'required|string|max:255',
            'contractor'               => 'required|string|max:255',
            'contract_amount'          => 'required|numeric|min:0',
            'date_started'             => 'required|date',
            'contract_days'            => 'required|integer|min:1',
            'original_contract_expiry' => 'required|date',
            'as_planned'               => 'required|numeric|min:0|max:100',
            'work_done'                => 'required|numeric|min:0|max:100',
            'status'                   => 'nullable|string',
            'completed_at'             => 'nullable|date',
        ]);

        $data = $request->only([
            'in_charge', 'project_title', 'location', 'contractor',
            'contract_amount', 'date_started', 'contract_days',
            'original_contract_expiry',
            'as_planned', 'work_done',
            'status', 'completed_at',
        ]);

        // Snapshot original amount before any extensions/VOs alter it
        $data['original_contract_amount'] = $request->contract_amount;

        // Derive status from days remaining until expiry
        $expiry   = Carbon::parse($data['original_contract_expiry']);
        $daysLeft = now()->startOfDay()->diffInDays($expiry->startOfDay(), false);

        if ($daysLeft < 0)        $data['status'] = 'expired';
        elseif ($daysLeft <= 30)  $data['status'] = 'expiring';
        else                      $data['status'] = 'ongoing';

        $data['completed_at'] = null;

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }


    // ============================================================
    // SECTION 3: EDIT — FORM PREPARATION
    // ============================================================

    /**
     * Show the edit form, pre-populating:
     *  - Time Extension history (teHistory)
     *  - Variation Order history (voHistory)
     *  - Suspension Order presence (hasSO)
     */
    public function edit(Project $project)
    {
        $fresh = $project->fresh();

        // ── Normalize stored JSON arrays ──────────────────────────
        $existingDocs  = $fresh->documents_pressed ?? [];
        $existingDays  = $fresh->extension_days    ?? [];
        $existingCosts = $fresh->cost_involved     ?? [];

        $existingDocs  = is_array($existingDocs)  ? $existingDocs  : (json_decode($existingDocs  ?? '[]', true) ?? []);
        $existingDays  = is_array($existingDays)  ? $existingDays  : (json_decode($existingDays  ?? '[]', true) ?? []);
        $existingCosts = is_array($existingCosts) ? $existingCosts : (json_decode($existingCosts ?? '[]', true) ?? []);

        $existingDays  = array_map('intval', $existingDays);
        $existingCosts = array_map(fn($v) => $v !== null ? (float) $v : null, $existingCosts);

        $existingDates = $fresh->date_requested ?? [];
        $existingDates = is_array($existingDates) ? $existingDates : (json_decode($existingDates ?? '[]', true) ?? []);

        // ── Build Time Extension history ──────────────────────────
        $teHistory = [];
        $teIndex   = 0;
        foreach ($existingDocs as $doc) {
            if (str_starts_with((string) $doc, 'Time Extension')) {
                $teHistory[] = [
                    'label'          => $doc,
                    'days'           => $existingDays[$teIndex]  ?? 0,
                    'cost'           => $existingCosts[$teIndex] ?? null,
                    'date_requested' => $existingDates[$teIndex] ?? null,
                ];
                $teIndex++;
            }
        }

        $teCount      = count($teHistory);
        $nextTeNumber = $teCount + 1;

        // ── Build Variation Order history ─────────────────────────
        $existingVoDays  = $fresh->vo_days ?? [];
        $existingVoCosts = $fresh->vo_cost ?? [];
        $existingVoDays  = is_array($existingVoDays)  ? $existingVoDays  : (json_decode($existingVoDays  ?? '[]', true) ?? []);
        $existingVoCosts = is_array($existingVoCosts) ? $existingVoCosts : (json_decode($existingVoCosts ?? '[]', true) ?? []);
        $existingVoDays  = array_map('intval', array_filter((array) $existingVoDays));
        $existingVoCosts = array_map(fn($v) => $v !== null ? (float) $v : null, $existingVoCosts);

        $voHistory    = [];
        $voIndex      = 0;
        $voDateOffset = $teCount;   // VO dates are stored after TE dates in date_requested

        foreach ($existingDocs as $doc) {
            if (str_starts_with((string) $doc, 'Variation Order')) {
                $voHistory[] = [
                    'label'          => $doc,
                    'days'           => $existingVoDays[$voIndex]  ?? 0,
                    'cost'           => $existingVoCosts[$voIndex] ?? null,
                    'date_requested' => $existingDates[$voDateOffset + $voIndex] ?? null,
                ];
                $voIndex++;
            }
        }

        $voCount      = count($voHistory);
        $nextVoNumber = $voCount + 1;

        // ── Suspension Order flag ─────────────────────────────────
        $hasSO   = collect($existingDocs)->contains('Suspension Order');
        $soCount = $hasSO ? 1 : 0;

        return view('admin.projects.edit', [
            'project'      => $fresh,
            'teHistory'    => $teHistory,
            'teCount'      => $teCount,
            'nextTeNumber' => $nextTeNumber,
            'voHistory'    => $voHistory,
            'voCount'      => $voCount,
            'nextVoNumber' => $nextVoNumber,
            'hasSO'        => $hasSO,
            'soCount'      => $soCount,
        ]);
    }


    // ============================================================
    // SECTION 4: UPDATE — MAIN PROJECT SAVE
    // ============================================================

    /**
     * Validate and persist all project changes, including:
     *  - Time Extensions / Variation Orders / Suspension Orders
     *  - Billing amounts
     *  - Liquidated Damages
     *  - Contract amount adjustments
     *  - Revised expiry & contract days
     *  - Status auto-derivation (or manual completion / reactivation)
     */
    public function update(Request $request, Project $project)
    {
        // ── Validation ────────────────────────────────────────────
        $request->validate([
            'in_charge'                => 'required|string|max:255',
            'project_title'            => 'required|string|max:255',
            'location'                 => 'required|string|max:255',
            'contractor'               => 'required|string|max:255',
            'date_started'             => 'required|date',
            'contract_days'            => 'nullable|integer|min:1',
            'original_contract_expiry' => 'required|date',
            'status'                   => 'nullable|string',
            'contract_amount'          => 'required|numeric|min:0',
            'as_planned'               => 'required|numeric|min:0|max:100',
            'work_done'                => 'required|numeric|min:0|max:100',
            'remarks_recommendation'   => 'nullable|string',
            'completed_at'             => 'nullable|date',
            'issuances'                => 'nullable|array',
            'issuances.*'              => 'nullable|string|in:1st Notice of Negative Slippage,2nd Notice of Negative Slippage,3rd Notice of Negative Slippage,Liquidated Damages,Notice to Terminate,Notice of Expiry,Performance Bond',
            'new_te_days'              => 'nullable|integer|min:1|max:9999',
            'new_te_cost'              => 'nullable|numeric',
            'new_te_date'              => 'nullable|date',
            'new_vo_days'              => 'nullable|integer|min:1|max:9999',
            'new_vo_cost'              => 'nullable|numeric',
            'new_vo_date'              => 'nullable|date',
            'new_so_days'              => 'nullable|integer|min:1|max:9999',
            'ld_accomplished'          => 'nullable|numeric|min:0|max:100',
            'ld_days_overdue'          => 'nullable|integer|min:0',
            'performance_bond_date'    => 'nullable|date',
            'advance_billing_pct'      => 'nullable|numeric|min:0|max:100',
            'retention_pct'            => 'nullable|numeric|min:0|max:100',
            'edit_reason'              => 'nullable|string|max:1000',
        ]);

        // ── Step 1: Basic scalar fields ───────────────────────────
        $data = $request->only([
            'in_charge', 'project_title', 'location', 'contractor',
            'contract_amount', 'date_started', 'contract_days',
            'original_contract_expiry',
            'as_planned', 'work_done',
            'status', 'completed_at',
            'remarks_recommendation',
            'ld_accomplished', 'ld_days_overdue',
            'performance_bond_date',
        ]);

        $data['slippage'] = (float) $request->work_done - (float) $request->as_planned;

        // ── Step 2: Issuances ─────────────────────────────────────
        $data['issuances'] = array_values(
            array_filter($request->input('issuances', []), fn($v) => !empty($v))
        );

        // ── Step 3: Carry forward existing arrays from DB ─────────
        $fresh = $project->fresh();

        $existingDocs  = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $existingDays  = is_array($fresh->extension_days)    ? array_map('intval', $fresh->extension_days) : [];
        $existingCosts = is_array($fresh->cost_involved)     ? array_map(fn($v) => $v !== null ? (float) $v : null, $fresh->cost_involved) : [];

        $existingSuspDay = (int) ($fresh->suspension_days ?? 0);

        $existingVoDays  = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days))  : [];
        $existingVoCosts = is_array($fresh->vo_cost) ? array_map(fn($v) => $v !== null ? (float) $v : null, $fresh->vo_cost) : [];

        $existingDates = is_array($fresh->date_requested) ? $fresh->date_requested : [];

        $currentTECount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        // ── Step 3b: Carry forward & append billing ───────────────
        $existingBillingAmounts = is_array($fresh->billing_amounts) ? array_map('floatval', $fresh->billing_amounts) : [];
        $existingBillingDates   = is_array($fresh->billing_dates)   ? $fresh->billing_dates : [];

        $newBillingAmount = $request->input('new_billing_amount');
        $newBillingDate   = $request->input('new_billing_date');

        if ($newBillingAmount !== null && $newBillingAmount !== '' && (float)$newBillingAmount > 0) {
            $existingBillingAmounts[] = (float) $newBillingAmount;
            $existingBillingDates[]   = $newBillingDate ?: null;
        }

        $data['billing_amounts'] = array_values($existingBillingAmounts);
        $data['billing_dates']   = array_values($existingBillingDates);

        // ── Step 3c: Billing summary fields ──────────────────────
        $data['total_amount_billed'] = array_sum($data['billing_amounts']);
        $data['remaining_balance']   = (float) ($fresh->original_contract_amount ?? $fresh->contract_amount) - $data['total_amount_billed'];

        // ── Step 4: Append new Time Extension ────────────────────
        $newTEDays = (int) $request->input('new_te_days', 0);
        $newTECost = $request->input('new_te_cost');
        $newTEDate = $request->input('new_te_date');

        if ($newTEDays > 0) {
            $nextNumber      = $currentTECount + 1;
            $existingDocs[]  = "Time Extension {$nextNumber}";
            $existingDays[]  = $newTEDays;
            $existingCosts[] = ($newTECost !== null && $newTECost !== '') ? (float) $newTECost : null;

            // Insert TE date before VO dates in the shared date_requested array
            $teDates   = array_slice($existingDates, 0, $currentTECount);
            $voDates   = array_slice($existingDates, $currentTECount);
            $teDates[] = $newTEDate ?: null;
            $existingDates = array_merge($teDates, $voDates);
        }

        // ── Step 4b: Append new Variation Order ──────────────────
        $newVoDays = (int) $request->input('new_vo_days', 0);
        $newVoCost = $request->input('new_vo_cost');
        $newVODate = $request->input('new_vo_date');

        if ($newVoDays > 0) {
            $currentVOCount    = collect($existingDocs)
                ->filter(fn($d) => str_starts_with((string) $d, 'Variation Order'))
                ->count();
            $nextVONumber      = $currentVOCount + 1;
            $existingDocs[]    = "Variation Order {$nextVONumber}";
            $existingVoDays[]  = $newVoDays;
            $existingVoCosts[] = ($newVoCost !== null && $newVoCost !== '') ? (float) $newVoCost : null;
            $existingDates[]   = $newVODate ?: null;
        }

        $data['vo_days']        = array_values($existingVoDays);
        $data['vo_cost']        = array_values($existingVoCosts);
        $data['date_requested'] = empty($existingDates)
            ? null
            : array_values(array_map(fn($d) => ($d !== '' ? $d : null), $existingDates));

        // ── Step 5: Handle Suspension Order ──────────────────────
        $newSODays = (int) $request->input('new_so_days', 0);
        $hasSO     = collect($existingDocs)->contains('Suspension Order');

        if ($newSODays > 0) {
            if (!$hasSO) {
                $existingDocs[] = 'Suspension Order';
                $hasSO          = true;
            }
            $data['suspension_days'] = $existingSuspDay + $newSODays;
        } else {
            $data['suspension_days'] = $existingSuspDay ?: null;
        }

        $data['documents_pressed'] = array_values($existingDocs);
        $data['extension_days']    = array_values($existingDays);
        $data['cost_involved']     = array_values($existingCosts);

        // ── Step 6: Auto-count TE / VO totals ────────────────────
        $data['time_extension'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Time Extension'))
            ->count();

        $data['variation_order'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Variation Order'))
            ->count();

        // ── Step 7: Recompute revised expiry & contract days ─────
        $totalTEDays  = (int) array_sum($data['extension_days']);
        $totalVODays  = (int) array_sum(array_map('intval', array_filter((array) ($data['vo_days'] ?? []))));
        $totalSODays  = (int) ($data['suspension_days'] ?? 0);
        $baseExpiry   = Carbon::parse($request->original_contract_expiry);
        $totalExtDays = $totalTEDays + $totalVODays;

        // Back out previously stored TE/VO days to recover the base contract days
        $existingTEInDB   = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $existingVOInDB   = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays = (int) Carbon::parse($fresh->date_started)
            ->diffInDays(Carbon::parse($fresh->original_contract_expiry)) + 1;
        $baseContractDays = max(1, (int)($fresh->contract_days ?? $originalContractDays) - $existingTEInDB - $existingVOInDB);

        $data['contract_days'] = $baseContractDays + $totalTEDays + $totalVODays;

        if ($totalExtDays > 0) {
            $extra = $hasSO ? $totalSODays : 0;
            $data['revised_contract_expiry'] = $baseExpiry->copy()
                ->addDays($totalExtDays + $extra)
                ->toDateString();
        } elseif ($hasSO && $totalSODays > 0) {
            $data['revised_contract_expiry'] = $baseExpiry->copy()
                ->addDays($totalSODays)
                ->toDateString();
        } else {
            $data['revised_contract_expiry'] = null;
        }

        // ── Step 7b: Adjust contract amount (original ± cost adjustments) ──
        $originalAmount = (float) ($fresh->original_contract_amount ?? $request->contract_amount);

        // Advance billing percentage → amount
        $advPct = $request->input('advance_billing_pct');
        if ($advPct !== null && $advPct !== '') {
            $data['advance_billing_pct']    = (float) $advPct;
            $data['advance_billing_amount'] = round((float)$advPct / 100 * $originalAmount, 2);
        } else {
            $data['advance_billing_pct']    = null;
            $data['advance_billing_amount'] = null;
        }

        // Retention percentage → amount
        $retPct = $request->input('retention_pct');
        if ($retPct !== null && $retPct !== '') {
            $data['retention_pct']    = (float) $retPct;
            $data['retention_amount'] = round((float)$retPct / 100 * $originalAmount, 2);
        } else {
            $data['retention_pct']    = null;
            $data['retention_amount'] = null;
        }

        // Sum all TE & VO cost adjustments
        $totalAdjustment = 0;
        foreach ($data['cost_involved'] as $cost) {
            if ($cost !== null && (float)$cost != 0) $totalAdjustment += (float)$cost;
        }
        foreach (($data['vo_cost'] ?? []) as $cost) {
            if ($cost !== null && (float)$cost != 0) $totalAdjustment += (float)$cost;
        }

        $data['contract_amount'] = max(0, $originalAmount + $totalAdjustment);

        // ── Step 8: Liquidated Damages (server-side computation) ─
        $ldAccomplished = isset($data['ld_accomplished']) && $data['ld_accomplished'] !== null
            ? (float) $data['ld_accomplished']
            : 0.0;
        $contractAmount = (float) ($data['contract_amount'] ?? 0);
        $daysOverdue    = (int) $request->input('ld_days_overdue', 0);

        // Formula: LD per day = (unworked% / 100) × contract_amount × 0.001
        $ldUnworked = max(0, 100 - $ldAccomplished);
        $ldPerDay   = ($ldUnworked / 100) * $contractAmount * 0.001;

        $data['ld_per_day']      = $ldPerDay > 0 ? round($ldPerDay, 2)   : null;
        $data['ld_unworked']     = $ldPerDay > 0 ? round($ldUnworked, 2) : null;
        $data['ld_days_overdue'] = $daysOverdue > 0 ? $daysOverdue       : null;
        $data['total_ld']        = ($ldPerDay > 0 && $daysOverdue > 0)
            ? round($ldPerDay * $daysOverdue, 2)
            : null;

        // ── Step 9: Clear LD fields when inputs are blank ─────────
        // Note: LD is independent of issuances — do not wipe based on notification list.
        if ($ldAccomplished <= 0) {
            $data['ld_accomplished'] = null;
            $data['ld_unworked']     = null;
            $data['ld_per_day']      = null;
            $data['total_ld']        = null;
        }
        if ($daysOverdue <= 0) {
            $data['ld_days_overdue'] = null;
            if ($ldAccomplished <= 0) {
                $data['total_ld'] = null;
            }
        }

        // ── Step 10: Auto-derive status (with manual overrides) ───
        if ($request->input('status') === 'reactivate') {
            // Reactivation: clear completion date, re-derive from effective expiry
            $data['completed_at'] = null;
            $effectiveExpiry = $data['revised_contract_expiry'] ?? $data['original_contract_expiry'] ?? null;
            if ($effectiveExpiry) {
                $daysLeft = now()->startOfDay()->diffInDays(Carbon::parse($effectiveExpiry)->startOfDay(), false);
                if ($daysLeft < 0)       $data['status'] = 'expired';
                elseif ($daysLeft <= 30) $data['status'] = 'expiring';
                else                     $data['status'] = 'ongoing';
            } else {
                $data['status'] = 'ongoing';
            }
        } elseif ($request->filled('completed_at')) {
            // Manual completion
            $data['status']       = 'completed';
            $data['completed_at'] = $request->completed_at;
        } else {
            // Default: derive from effective expiry
            $data['completed_at'] = null;
            $effectiveExpiry = $data['revised_contract_expiry'] ?? $data['original_contract_expiry'] ?? null;
            if ($effectiveExpiry) {
                $daysLeft = now()->startOfDay()->diffInDays(Carbon::parse($effectiveExpiry)->startOfDay(), false);
                if ($daysLeft < 0)       $data['status'] = 'expired';
                elseif ($daysLeft <= 30) $data['status'] = 'expiring';
                else                     $data['status'] = 'ongoing';
            }
        }

        $project->update($data);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }


    // ============================================================
    // SECTION 5: BILLING
    // ============================================================

    /**
     * Edit an existing billing entry by index.
     * Recomputes total billed and remaining balance.
     */
    public function updateBilling(Request $request, Project $project)
    {
        $request->validate([
            'billing_index'  => 'required|integer|min:0',
            'billing_amount' => 'required|numeric|min:0',
            'billing_date'   => 'nullable|date',
        ]);

        $fresh  = $project->fresh();
        $index  = (int) $request->input('billing_index');
        $amount = (float) $request->input('billing_amount');
        $date   = $request->input('billing_date');

        $billingAmounts = is_array($fresh->billing_amounts) ? array_map('floatval', $fresh->billing_amounts) : [];
        $billingDates   = is_array($fresh->billing_dates)   ? $fresh->billing_dates : [];

        if (!isset($billingAmounts[$index])) {
            return back()->with('error', 'Billing entry not found.');
        }

        $billingAmounts[$index] = $amount;
        $billingDates[$index]   = $date ?: null;

        $originalAmount = (float) ($fresh->original_contract_amount ?? $fresh->contract_amount);
        $totalBilled    = array_sum($billingAmounts);

        $project->update([
            'billing_amounts'     => array_values($billingAmounts),
            'billing_dates'       => array_values($billingDates),
            'total_amount_billed' => $totalBilled,
            'remaining_balance'   => $originalAmount - $totalBilled,
        ]);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Billing No. ' . ($index + 1) . ' updated successfully.');
    }


    // ============================================================
    // SECTION 6: TIME EXTENSION / VARIATION ORDER — INLINE EDIT & DELETE
    // ============================================================

    /**
     * Edit an existing TE or VO entry by index.
     * Recomputes revised expiry, contract days, and contract amount.
     * Appends an edit reason to remarks_recommendation.
     */
    public function updateEntry(Request $request, Project $project)
    {
        $request->validate([
            'edit_entry_type'     => 'required|in:te,vo',
            'edit_entry_index'    => 'required|integer|min:0',
            'edit_days'           => 'required|integer|min:1|max:9999',
            'edit_cost'           => 'nullable|numeric',
            'edit_date_requested' => 'nullable|date',
            'edit_reason'         => 'required|string|max:1000',
        ]);

        $fresh  = $project->fresh();
        $type   = $request->input('edit_entry_type');
        $index  = (int) $request->input('edit_entry_index');
        $days   = (int) $request->input('edit_days');
        $cost   = $request->input('edit_cost');
        $date   = $request->input('edit_date_requested');
        $reason = trim($request->input('edit_reason'));

        $existingDocs  = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $dateRequested = is_array($fresh->date_requested)    ? $fresh->date_requested    : [];

        $teCount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        $data = [];

        // ── Mutate the correct array (TE or VO) ──────────────────
        if ($type === 'te') {
            $extensionDays = is_array($fresh->extension_days) ? array_map('intval', $fresh->extension_days) : [];
            $costInvolved  = is_array($fresh->cost_involved)  ? $fresh->cost_involved : [];

            if (!isset($extensionDays[$index])) {
                return back()->with('error', 'Time Extension entry not found.');
            }

            $extensionDays[$index] = $days;
            $costInvolved[$index]  = ($cost !== null && $cost !== '') ? (float) $cost : null;
            $dateRequested[$index] = $date ?: null;

            $data['extension_days'] = array_values($extensionDays);
            $data['cost_involved']  = array_values($costInvolved);
            $data['date_requested'] = array_values($dateRequested);

        } else {
            $voDays  = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days)) : [];
            $voCosts = is_array($fresh->vo_cost) ? $fresh->vo_cost : [];

            if (!isset($voDays[$index])) {
                return back()->with('error', 'Variation Order entry not found.');
            }

            $voDays[$index]  = $days;
            $voCosts[$index] = ($cost !== null && $cost !== '') ? (float) $cost : null;
            $dateRequested[$teCount + $index] = $date ?: null;  // VO dates offset by teCount

            $data['vo_days']        = array_values($voDays);
            $data['vo_cost']        = array_values($voCosts);
            $data['date_requested'] = array_values($dateRequested);
        }

        // ── Recompute revised_contract_expiry ─────────────────────
        $allExtDays = array_sum(array_map('intval', $data['extension_days'] ?? $fresh->extension_days ?? []));
        $allVODays  = array_sum(array_map('intval', $data['vo_days']        ?? $fresh->vo_days        ?? []));
        $sodays     = (int) ($fresh->suspension_days ?? 0);
        $hasSO      = collect($existingDocs)->contains('Suspension Order');
        $total      = $allExtDays + $allVODays + ($hasSO ? $sodays : 0);

        $data['revised_contract_expiry'] = $total > 0
            ? Carbon::parse($fresh->original_contract_expiry)->addDays($total)->toDateString()
            : null;

        // ── Recompute contract_days ───────────────────────────────
        $previousTEDays       = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $previousVODays       = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays = (int) ($fresh->contract_days ?? 0) - $previousTEDays - $previousVODays;
        $currentTEDays        = (int) array_sum(array_map('intval', $data['extension_days'] ?? $fresh->extension_days ?? []));
        $currentVODays        = (int) array_sum(array_map('intval', $data['vo_days']        ?? $fresh->vo_days        ?? []));
        $data['contract_days'] = $originalContractDays + $currentTEDays + $currentVODays;

        // ── Recompute contract amount from original ───────────────
        $originalAmount = (float) ($fresh->original_contract_amount ?? (float) $fresh->contract_amount);
        $allCosts = array_merge(
            array_values($data['cost_involved'] ?? $fresh->cost_involved ?? []),
            array_values($data['vo_cost']       ?? $fresh->vo_cost       ?? [])
        );
        $adjustment = collect($allCosts)->filter(fn($c) => $c !== null && (float)$c != 0)->sum();
        $data['contract_amount'] = max(0, $originalAmount + $adjustment);

        // ── Resolve the display label for this entry ──────────────
        $labelCounter  = 0;
        $resolvedLabel = ($type === 'te' ? 'Time Extension' : 'Variation Order') . ' ' . ($index + 1);
        foreach ($existingDocs as $doc) {
            $prefix = $type === 'te' ? 'Time Extension' : 'Variation Order';
            if (str_starts_with((string) $doc, $prefix)) {
                if ($labelCounter === $index) { $resolvedLabel = $doc; break; }
                $labelCounter++;
            }
        }

        // ── Append edit note to remarks_recommendation ────────────
        $existing  = trim($fresh->remarks_recommendation ?? '');
        $timestamp = now()->format('F d, Y \a\t h:i A');
        $note      = "[{$timestamp}] {$resolvedLabel} edited — Reason: {$reason}";
        $data['remarks_recommendation'] = $existing !== '' ? $existing . "\n\n" . $note : $note;

        $project->update($data);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', ucfirst($type === 'te' ? 'Time Extension' : 'Variation Order') . ' updated successfully.');
    }

    /**
     * Delete a TE or VO entry by index.
     * Renumbers remaining entries, recomputes expiry / contract days.
     * Appends a delete reason to remarks_recommendation.
     */
    public function destroyEntry(Request $request, Project $project)
    {
        $request->validate([
            'entry_type'    => 'required|in:te,vo',
            'entry_index'   => 'required|integer|min:0',
            'delete_reason' => 'required|string|max:1000',
        ]);

        $fresh  = $project->fresh();
        $type   = $request->input('entry_type');
        $index  = (int) $request->input('entry_index');
        $reason = trim($request->input('delete_reason'));

        $existingDocs    = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $existingDays    = is_array($fresh->extension_days)    ? array_map('intval', $fresh->extension_days) : [];
        $existingCosts   = is_array($fresh->cost_involved)     ? $fresh->cost_involved : [];
        $existingDates   = is_array($fresh->date_requested)    ? $fresh->date_requested : [];
        $existingVoDays  = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days)) : [];
        $existingVoCosts = is_array($fresh->vo_cost) ? $fresh->vo_cost : [];

        $teCount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        // ── Locate and splice out the correct entry ───────────────
        if ($type === 'te') {
            $tesSeen = 0;
            $docIndexToRemove = null;
            foreach ($existingDocs as $di => $doc) {
                if (str_starts_with((string) $doc, 'Time Extension')) {
                    if ($tesSeen === $index) { $docIndexToRemove = $di; break; }
                    $tesSeen++;
                }
            }
            if ($docIndexToRemove === null) {
                return back()->with('error', 'Time Extension entry not found.');
            }
            $deletedLabel = $existingDocs[$docIndexToRemove];

            array_splice($existingDocs,  $docIndexToRemove, 1);
            array_splice($existingDays,  $index, 1);
            array_splice($existingCosts, $index, 1);
            array_splice($existingDates, $index, 1);

            // Renumber remaining Time Extensions
            $teNum = 1;
            foreach ($existingDocs as &$doc) {
                if (str_starts_with((string) $doc, 'Time Extension')) {
                    $doc = "Time Extension {$teNum}";
                    $teNum++;
                }
            } unset($doc);

        } else {
            $vosSeen = 0;
            $docIndexToRemove = null;
            foreach ($existingDocs as $di => $doc) {
                if (str_starts_with((string) $doc, 'Variation Order')) {
                    if ($vosSeen === $index) { $docIndexToRemove = $di; break; }
                    $vosSeen++;
                }
            }
            if ($docIndexToRemove === null) {
                return back()->with('error', 'Variation Order entry not found.');
            }
            $deletedLabel = $existingDocs[$docIndexToRemove];

            array_splice($existingDocs,    $docIndexToRemove, 1);
            array_splice($existingVoDays,  $index, 1);
            array_splice($existingVoCosts, $index, 1);
            array_splice($existingDates,   $teCount + $index, 1);   // VO dates offset by teCount

            // Renumber remaining Variation Orders
            $voNum = 1;
            foreach ($existingDocs as &$doc) {
                if (str_starts_with((string) $doc, 'Variation Order')) {
                    $doc = "Variation Order {$voNum}";
                    $voNum++;
                }
            } unset($doc);
        }

        // ── Build updated data arrays ─────────────────────────────
        $data = [
            'documents_pressed' => array_values($existingDocs),
            'extension_days'    => array_values($existingDays),
            'cost_involved'     => array_values($existingCosts),
            'date_requested'    => empty($existingDates)
                ? null
                : array_values(array_map(fn($d) => ($d !== '' ? $d : null), $existingDates)),
            'vo_days'           => array_values($existingVoDays),
            'vo_cost'           => array_values($existingVoCosts),
        ];

        // ── Recount TE / VO totals ────────────────────────────────
        $data['time_extension'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Time Extension'))
            ->count();

        $data['variation_order'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Variation Order'))
            ->count();

        // ── Recompute revised_contract_expiry ─────────────────────
        $totalTE = (int) array_sum($data['extension_days']);
        $totalVO = (int) array_sum(array_map('intval', array_filter((array) ($data['vo_days'] ?? []))));
        $totalSO = (int) ($fresh->suspension_days ?? 0);
        $hasSO   = collect($data['documents_pressed'])->contains('Suspension Order');
        $total   = $totalTE + $totalVO + ($hasSO ? $totalSO : 0);

        $data['revised_contract_expiry'] = $total > 0
            ? Carbon::parse($fresh->original_contract_expiry)->addDays($total)->toDateString()
            : null;

        // ── Recompute contract_days ───────────────────────────────
        $previousTEDays       = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $previousVODays       = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays = (int) ($fresh->contract_days ?? 0) - $previousTEDays - $previousVODays;
        $data['contract_days'] = $originalContractDays + $totalTE + $totalVO;

        // ── Append delete note to remarks_recommendation ──────────
        $existing  = trim($fresh->remarks_recommendation ?? '');
        $timestamp = now()->format('F d, Y \a\t h:i A');
        $note      = "[{$timestamp}] {$deletedLabel} deleted — Reason: {$reason}";

        $data['remarks_recommendation'] = $existing !== ''
            ? $existing . "\n\n" . $note
            : $note;

        $project->update($data);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', "{$deletedLabel} deleted. Reason logged to remarks.");
    }


    // ============================================================
    // SECTION 7: STATUS MANAGEMENT
    // ============================================================

    /**
     * Reactivate a completed project.
     * Clears completed_at and re-derives status from effective expiry.
     */
    public function reactivate(Project $project)
    {
        $effectiveExpiry = $project->revised_contract_expiry ?? $project->original_contract_expiry;

        $daysLeft = now()->startOfDay()->diffInDays(
            Carbon::parse($effectiveExpiry)->startOfDay(),
            false
        );

        if ($daysLeft < 0)       $status = 'expired';
        elseif ($daysLeft <= 30) $status = 'expiring';
        else                     $status = 'ongoing';

        $project->update([
            'status'       => $status,
            'completed_at' => null,
        ]);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Project reactivated successfully. Status set to ' . ucfirst($status) . '.');
    }


    // ============================================================
    // SECTION 8: REPORTS & PDF GENERATION
    // ============================================================

    /**
     * Summary report page with aggregate counts (ongoing / completed / expired).
     */
    public function reports()
    {
        $projects  = Project::orderBy('date_started', 'desc')->get();
        $total     = $projects->count();
        $ongoing   = $projects->where('status', 'ongoing')->count();
        $completed = $projects->where('status', 'completed')->count();
        $expired   = $projects->where('status', 'expired')->count();

        return view('admin.reports.index', compact('projects', 'total', 'ongoing', 'completed', 'expired'));
    }

    /**
     * Generate and stream a filtered PDF report.
     * Supports filtering by search, in_charge, and status.
     */
    public function generateReport()
    {
        $clean = fn(string $s) => iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;

        // ── Apply filters ─────────────────────────────────────────
        $query = \App\Models\Project::query();

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('project_title', 'like', "%{$search}%")
                  ->orWhere('location',     'like', "%{$search}%")
                  ->orWhere('contractor',   'like', "%{$search}%");
            });
        }

        if (request('in_charge')) {
            $query->where('in_charge', request('in_charge'));
        }

        $status = request('status', 'all');
        if ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'active') {
            $query->where('status', 'ongoing')
                  ->where(function ($q) {
                      $q->whereNull('revised_contract_expiry')
                        ->where('original_contract_expiry', '>', now()->addDays(30))
                        ->orWhere('revised_contract_expiry', '>', now()->addDays(30));
                  });
        } elseif ($status === 'expiring') {
            $query->where('status', '!=', 'completed')
                  ->where(function ($q) {
                      $q->whereNull('revised_contract_expiry')
                        ->whereBetween('original_contract_expiry', [now(), now()->addDays(30)])
                        ->orWhereBetween('revised_contract_expiry', [now(), now()->addDays(30)]);
                  });
        } elseif ($status === 'expired') {
            $query->where('status', '!=', 'completed')
                  ->where(function ($q) {
                      $q->whereNull('revised_contract_expiry')
                        ->where('original_contract_expiry', '<', now())
                        ->orWhere('revised_contract_expiry', '<', now());
                  });
        } elseif ($status === 'ongoing') {
            $query->where('status', 'ongoing')
                  ->where(function ($q) {
                      $q->whereNull('revised_contract_expiry')
                        ->where('original_contract_expiry', '>=', now())
                        ->orWhere('revised_contract_expiry', '>=', now());
                  });
        }

        $projects  = $query->orderBy('date_started', 'desc')->get();
        $total     = $projects->count();
        $ongoing   = $projects->where('status', 'ongoing')->count();
        $completed = $projects->where('status', 'completed')->count();
        $expired   = $projects->where('status', 'expired')->count();

        // ── Build human-readable filter label ─────────────────────
        $filterParts = [];
        if (request('search'))            $filterParts[] = 'Search: "' . request('search') . '"';
        if (request('in_charge'))         $filterParts[] = 'In Charge: ' . request('in_charge');
        if ($status && $status !== 'all') $filterParts[] = 'Status: ' . ucfirst($status);
        $filterLabel = count($filterParts) ? implode('  |  ', $filterParts) : 'All Projects';

        // ── Initialize PDF ────────────────────────────────────────
        $pdf = new ProjectReportPdf('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->setGeneratedAt(now()->format('F d, Y  h:i A'));
        $pdf->setFilterLabel($filterLabel);
        $pdf->AddPage();

        // ── Table header ──────────────────────────────────────────
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetTextColor(107, 79, 53);
        $pdf->Cell(0, 5, 'PROJECT DETAILS - ' . $total . ' records', 0, 1, 'L');
        $pdf->Ln(2);

        $cols = [
            ['#',             8,  'C'],
            ['Project Title', 55, 'L'],
            ['In Charge',     32, 'L'],
            ['Location',      30, 'L'],
            ['Contractor',    32, 'L'],
            ['Contract Amt',  30, 'R'],
            ['Started',       25, 'C'],
            ['Expiry',        25, 'C'],
            ['Slippage',      20, 'C'],
            ['Status',        20, 'C'],
        ];

        $pdf->TableHeader($cols);

        // ── Table rows ────────────────────────────────────────────
        foreach ($projects as $i => $project) {
            if ($pdf->GetY() + 7 > 200) {
                $pdf->AddPage();
                $pdf->TableHeader($cols);
            }

            $expiry  = $project->revised_contract_expiry ?? $project->original_contract_expiry;
            $slip    = (float) ($project->slippage ?? 0);
            $slipStr = ($slip > 0 ? '+' : '') . number_format($slip, 2) . '%';
            $even    = $i % 2 === 0;

            if ($slip > 0)     $slipColor = [22, 163, 74];
            elseif ($slip < 0) $slipColor = [220, 38, 38];
            else               $slipColor = [107, 114, 128];

            $statusMap = [
                'completed' => [[240,253,244], [22,163,74],  'Completed'],
                'expired'   => [[254,242,242], [220,38,38],  'Expired'  ],
                'expiring'  => [[255,251,235], [217,119,6],  'Expiring' ],
                'ongoing'   => [[239,246,255], [37,99,235],  'Ongoing'  ],
            ];
            [$statusBg, $statusFg, $statusLabel] = $statusMap[$project->status] ?? $statusMap['ongoing'];

            $pdf->ProjectRow(
                $i + 1,
                mb_strimwidth($clean($project->project_title), 0, 35, '...'),
                mb_strimwidth($clean($project->in_charge),     0, 20, '...'),
                mb_strimwidth($clean($project->location),      0, 18, '...'),
                mb_strimwidth($clean($project->contractor),    0, 20, '...'),
                'P' . number_format($project->contract_amount, 2),
                $project->date_started->format('m/d/Y'),
                $expiry->format('m/d/Y'),
                $slipStr,
                $slipColor,
                $statusLabel,
                $statusBg,
                $statusFg,
                $even
            );
        }

        // ── Stream PDF to browser ─────────────────────────────────
        $filename = 'projects-report-' . now()->format('Y-m-d') . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }


    // ============================================================
    // SECTION 9: DELETE
    // ============================================================

    /**
     * Permanently delete a project record.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}