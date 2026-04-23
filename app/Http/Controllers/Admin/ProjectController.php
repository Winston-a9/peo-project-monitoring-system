<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectLog;
use App\Traits\DivisionScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ProjectReportPdf;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    use DivisionScope;

    // Available divisions — single source of truth used by create/edit forms
    public const DIVISIONS = [
        'First District Engineering Office',
        'Second District Engineering Office',
        'Third District Engineering Office',
        'Fourth District Engineering Office',
        'Fifth District Engineering Office',
    ];

    // ============================================================
    // SECTION 1: LISTING & DISPLAY
    // ============================================================

    /**
     * List projects — division admins only see their own division.
     */
    public function index()
    {
        $perPage = in_array((int) request('per_page', 10), [10, 25, 50])
            ? (int) request('per_page', 10)
            : 10;

        $projects = $this->divisionQuery()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show a single project — enforce division access.
     */
    public function show(Project $project)
    {
        $this->authorizeProjectAccess($project);

        $project->load(['logs.user' => fn($q) => $q->select('id', 'name')]);
        $project->logs = $project->logs->sortByDesc('created_at');

        return view('admin.projects.show', compact('project'));
    }


    // ============================================================
    // SECTION 2: CREATE
    // ============================================================

    public function create()
    {
        $divisions = self::DIVISIONS;
        $currentDivision = $this->currentDivision(); // null for super admin

        return view('admin.projects.create', compact('divisions', 'currentDivision'));
    }

    /**
     * Validate and store a new project.
     * Division admins can only create projects for their own division.
     */
    public function store(Request $request)
    {
        $request->validate([
            'in_charge' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'contract_id' => ['required', 'string', 'regex:/^[\d\-]+$/', 'unique:projects,contract_id'],
            'project_title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contractor' => 'required|string|max:255',
            'original_contract_amount' => 'required|numeric|min:0',
            'date_started' => 'required|date',
            'contract_days' => 'required|integer|min:1',
            'original_contract_expiry' => 'required|date',
            'as_planned' => 'required|numeric|min:0|max:100',
            'work_done' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|string',
            'completed_at' => 'nullable|date',
        ]);

        // Division admins can ONLY create projects for their own division
        $division = $request->division;
        if (!$this->isSuperAdmin() && $division !== $this->currentDivision()) {
            abort(403, 'You can only create projects for your own division.');
        }

        $data = $request->only([
            'in_charge',
            'division',
            'contract_id',
            'project_title',
            'location',
            'contractor',
            'original_contract_amount',
            'date_started',
            'contract_days',
            'original_contract_expiry',
            'as_planned',
            'work_done',
            'status',
            'completed_at',
        ]);

        // Auto-derive status from expiry
        $expiry = Carbon::parse($data['original_contract_expiry']);
        $daysLeft = now()->startOfDay()->diffInDays($expiry->startOfDay(), false);

        $data['status'] = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'expiring' : 'ongoing');
        $data['completed_at'] = null;

        Project::create($data);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }


    // ============================================================
    // SECTION 3: EDIT — FORM PREPARATION
    // ============================================================

    public function edit(Project $project)
    {
        $this->authorizeProjectAccess($project);

        $fresh = $project->fresh();

        $divisions = self::DIVISIONS;
        $currentDivision = $this->currentDivision();

        // ── Normalize stored JSON arrays ──────────────────────────
        $existingDocs = $fresh->documents_pressed ?? [];
        $existingDays = $fresh->extension_days ?? [];
        $existingCosts = $fresh->cost_involved ?? [];

        $existingDocs = is_array($existingDocs) ? $existingDocs : (json_decode($existingDocs ?? '[]', true) ?? []);
        $existingDays = is_array($existingDays) ? $existingDays : (json_decode($existingDays ?? '[]', true) ?? []);
        $existingCosts = is_array($existingCosts) ? $existingCosts : (json_decode($existingCosts ?? '[]', true) ?? []);

        $existingDays = array_map('intval', $existingDays);
        $existingCosts = array_map(fn($v) => $v !== null ? (float) $v : null, $existingCosts);

        $existingDates = $fresh->date_requested ?? [];
        $existingDates = is_array($existingDates) ? $existingDates : (json_decode($existingDates ?? '[]', true) ?? []);

        // ── Build Time Extension history ──────────────────────────
        // FIX BUG 1: Use a dedicated $teIndex counter that only increments for TE entries.
        // $existingDays, $existingCosts are parallel arrays to TE entries only.
        // $existingDates stores TE dates first (0..teCount-1), then VO dates after.
        // Using a separate counter ensures correct alignment even if other doc types
        // are interspersed in $existingDocs.
        $teHistory = [];
        $teIndex = 0;
        foreach ($existingDocs as $doc) {
            if (str_starts_with((string) $doc, 'Time Extension')) {
                $teHistory[] = [
                    'label' => $doc,
                    'days' => $existingDays[$teIndex] ?? 0,
                    'cost' => $existingCosts[$teIndex] ?? null,
                    'date_requested' => $existingDates[$teIndex] ?? null,  // TE dates at 0..teCount-1
                ];
                $teIndex++;
            }
        }

        $teCount = count($teHistory); // Now accurately equals $teIndex after loop
        $nextTeNumber = $teCount + 1;

        // ── Build Variation Order history ─────────────────────────
        $existingVoDays = $fresh->vo_days ?? [];
        $existingVoCosts = $fresh->vo_cost ?? [];
        $existingVoDays = is_array($existingVoDays) ? $existingVoDays : (json_decode($existingVoDays ?? '[]', true) ?? []);
        $existingVoCosts = is_array($existingVoCosts) ? $existingVoCosts : (json_decode($existingVoCosts ?? '[]', true) ?? []);
        $existingVoDays = array_map('intval', array_filter((array) $existingVoDays));
        $existingVoCosts = array_map(fn($v) => $v !== null ? (float) $v : null, $existingVoCosts);

        // FIX BUG 4: Guard against missing date entries before accessing offset.
        // VO dates are stored at indices $teCount, $teCount+1, ... in $existingDates.
        // We validate the offset exists before reading to prevent silent null misassignment.
        $voHistory = [];
        $voIndex = 0;
        foreach ($existingDocs as $doc) {
            if (str_starts_with((string) $doc, 'Variation Order')) {
                $voDateIndex = $teCount + $voIndex;
                $voHistory[] = [
                    'label' => $doc,
                    'days' => $existingVoDays[$voIndex] ?? 0,
                    'cost' => $existingVoCosts[$voIndex] ?? null,
                    'date_requested' => array_key_exists($voDateIndex, $existingDates)
                        ? $existingDates[$voDateIndex]
                        : null,
                ];
                $voIndex++;
            }
        }

        $voCount = count($voHistory);
        $nextVoNumber = $voCount + 1;

        // ── Suspension Order flag ─────────────────────────────────
        $hasSO = collect($existingDocs)->contains('Suspension Order');
        $soCount = $hasSO ? 1 : 0;

        $remarksText = $fresh->remarks_recommendation ?? '';
        $split = $this->splitRemarks($fresh->remarks_recommendation ?? '');
        $remarksManual = $split['manual'];
        $remarksAutoHidden = $split['auto'];

        $teReasonMap = [];
        $voReasonMap = [];

        preg_match_all(
            '/(?:\[.*?\]\s*)?(?:●\s*\d{1,2}:\d{2}\s+(?:AM|PM)(?:\s+•\s*[^
]+)?\n)?\s*(Time Extension\s+\d+|Extension\s+#\d+)\s+(?:added|edited|updated|deleted)\s*\n(?:Justification|Reason):\s*(.+?)(?=\n\n|\z)/si',
            $remarksText,
            $teMatches,
            PREG_SET_ORDER
        );
        foreach ($teMatches as $match) {
            $teReasonMap[trim($match[1])] = trim($match[2]);
        }

        preg_match_all(
            '/(?:\[.*?\]\s*)?(?:●\s*\d{1,2}:\d{2}\s+(?:AM|PM)(?:\s+•\s*[^
]+)?\n)?\s*(Variation Order\s+\d+|Variation\s+#\d+)\s+(?:added|edited|updated|deleted)\s*\n(?:Justification|Reason):\s*(.+?)(?=\n\n|\z)/si',
            $remarksText,
            $voMatches,
            PREG_SET_ORDER
        );
        foreach ($voMatches as $match) {
            $voReasonMap[trim($match[1])] = trim($match[2]);
        }

        foreach ($teHistory as &$entry) {
            $entry['reason'] = $teReasonMap[$entry['label']] ?? null;
        }
        unset($entry);

        foreach ($voHistory as &$entry) {
            $entry['reason'] = $voReasonMap[$entry['label']] ?? null;
        }
        unset($entry);

        return view('admin.projects.edit', [
            'project' => $fresh,
            'teHistory' => $teHistory,
            'teCount' => $teCount,
            'nextTeNumber' => $nextTeNumber,
            'voHistory' => $voHistory,
            'voCount' => $voCount,
            'nextVoNumber' => $nextVoNumber,
            'hasSO' => $hasSO,
            'soCount' => $soCount,
            'remarksManual' => $remarksManual,
            'remarksAutoHidden' => $remarksAutoHidden,
            'divisions' => $divisions,
            'currentDivision' => $currentDivision,
        ]);
    }


    // ============================================================
    // SECTION 4: UPDATE — MAIN PROJECT SAVE
    // ============================================================

    public function update(Request $request, Project $project)
    {
        $this->authorizeProjectAccess($project);

        // ── Validation ────────────────────────────────────────────
        $request->validate([
            'in_charge' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'project_title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contractor' => 'required|string|max:255',
            'date_started' => 'required|date',
            'contract_days' => 'nullable|integer|min:1',
            'original_contract_expiry' => 'required|date',
            'status' => 'nullable|string',
            'original_contract_amount' => 'required|numeric|min:0',
            'as_planned' => 'required|numeric|min:0|max:100',
            'work_done' => 'required|numeric|min:0|max:100',
            'remarks_recommendation' => 'nullable|string',
            'completed_at' => 'nullable|date',
            'issuances' => 'nullable|array',
            'issuances.*' => 'nullable|string|in:1st Notice of Negative Slippage,2nd Notice of Negative Slippage,3rd Notice of Negative Slippage,Liquidated Damages,Notice to Terminate,Notice of Expiry,Performance Bond',
            'new_te_days' => 'nullable|integer|min:1|max:9999',
            'new_te_cost' => 'nullable|numeric',
            'new_te_date' => 'nullable|date',
            'new_vo_days' => 'nullable|integer|min:1|max:9999',
            'new_vo_cost' => 'nullable|numeric',
            'new_vo_date' => 'nullable|date',
            'new_so_days' => 'nullable|integer|min:1|max:9999',
            'ld_accomplished' => 'nullable|numeric|min:0|max:100',
            'ld_days_overdue' => 'nullable|integer|min:0',
            'performance_bond_date' => 'nullable|date',
            'advance_billing_pct' => 'nullable|numeric|min:0|max:100',
            'retention_pct' => 'nullable|numeric|min:0|max:100',
            'edit_reason' => 'nullable|string|max:1000',
            'new_te_reason' => 'nullable|string|max:1000',
            'new_vo_reason' => 'nullable|string|max:1000',
            'new_so_reason' => 'nullable|string|max:1000',
            'ld_start_date' => 'nullable|date',
            'ld_end_date' => 'nullable|date|after_or_equal:ld_start_date',
        ]);

        // Division admins cannot reassign a project to a different division
        $newDivision = $request->division;
        if (!$this->isSuperAdmin() && $newDivision !== $this->currentDivision()) {
            abort(403, 'You cannot reassign a project to a different division.');
        }

        // ── Step 1: Basic scalar fields ───────────────────────────
        $data = $request->only([
            'in_charge',
            'division',
            'project_title',
            'location',
            'contractor',
            'original_contract_amount',
            'date_started',
            'contract_days',
            'original_contract_expiry',
            'as_planned',
            'work_done',
            'status',
            'completed_at',
            'remarks_recommendation',
            'ld_accomplished',
            'performance_bond_date',
        ]);

        $data['slippage'] = (float) $request->work_done - (float) $request->as_planned;

        // ── Step 1b: Reconstruct remarks ──────────────────────────
        $manualRemarks = trim($request->input('remarks_recommendation', ''));
        $autoHidden = trim($request->input('remarks_auto_hidden', ''));

        $data['remarks_recommendation'] = collect([$manualRemarks, $autoHidden])
            ->filter()
            ->implode("\n\n");

        // ── Step 2: Issuances ─────────────────────────────────────
        $data['issuances'] = array_values(
            array_filter($request->input('issuances', []), fn($v) => !empty($v))
        );

        // ── Step 3: Carry forward existing arrays from DB ─────────
        $fresh = $project->fresh();

        $existingDocs = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $existingDays = is_array($fresh->extension_days) ? array_map('intval', $fresh->extension_days) : [];
        $existingCosts = is_array($fresh->cost_involved) ? array_map(fn($v) => $v !== null ? (float) $v : null, $fresh->cost_involved) : [];
        $existingSuspDay = (int) ($fresh->suspension_days ?? 0);
        $existingVoDays = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days)) : [];
        $existingVoCosts = is_array($fresh->vo_cost) ? array_map(fn($v) => $v !== null ? (float) $v : null, $fresh->vo_cost) : [];
        $existingDates = is_array($fresh->date_requested) ? $fresh->date_requested : [];

        $currentTECount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        // ── Step 3b: Carry forward & append billing ───────────────
        $existingBillingAmounts = is_array($fresh->billing_amounts)
            ? array_map('floatval', $fresh->billing_amounts) : [];
        $existingBillingDates = is_array($fresh->billing_dates)
            ? $fresh->billing_dates : [];

        $newBillingAmount = $request->input('new_billing_amount');
        $newBillingDate = $request->input('new_billing_date');

        if ($newBillingAmount !== null && $newBillingAmount !== '' && (float) $newBillingAmount > 0) {
            $existingBillingAmounts[] = (float) $newBillingAmount;
            $existingBillingDates[] = $newBillingDate ?: null;
        }

        $data['billing_amounts'] = array_values($existingBillingAmounts);
        $data['billing_dates'] = array_values($existingBillingDates);

        // ── Step 3c: Billing summary fields ──────────────────────
        $data['total_amount_billed'] = array_sum($data['billing_amounts']);

        $allCurrentCosts = array_merge(array_values($existingCosts), array_values($existingVoCosts));
        $totalCostAdj = collect($allCurrentCosts)->filter(fn($c) => $c !== null && (float) $c !== 0.0)->sum();
        $adjustedContractAmount = max(0, (float) $fresh->original_contract_amount + $totalCostAdj);
        $data['remaining_balance'] = $adjustedContractAmount - $data['total_amount_billed'];

        // ── Step 4: Append new Time Extension ────────────────────
        $newTEDays = (int) $request->input('new_te_days', 0);
        $newTECost = $request->input('new_te_cost');
        $newTEDate = $request->input('new_te_date');
        $newTEReason = trim($request->input('new_te_reason', ''));

        if ($newTEDays > 0) {
            $nextNumber = $currentTECount + 1;
            $newTELabel = "Time Extension {$nextNumber}";
            $existingDocs[] = $newTELabel;
            $existingDays[] = $newTEDays;
            $existingCosts[] = ($newTECost !== null && $newTECost !== '') ? (float) $newTECost : null;

            if ($newTEReason !== '') {
                $existing = trim($data['remarks_recommendation'] ?? '');
                $note = $this->formatEntryRemark($newTELabel, 'added', $newTEReason);
                $data['remarks_recommendation'] = $existing !== '' ? $existing . "\n\n" . $note : $note;
            }

            $teDates = array_slice($existingDates, 0, $currentTECount);
            $voDates = array_slice($existingDates, $currentTECount);
            $teDates[] = $newTEDate ?: null;
            $existingDates = array_merge($teDates, $voDates);
        }

        // ── Step 4b: Append new Variation Order ──────────────────
        $newVoDays = (int) $request->input('new_vo_days', 0);
        $newVoCost = $request->input('new_vo_cost');
        $newVODate = $request->input('new_vo_date');
        $newVOReason = trim($request->input('new_vo_reason', ''));

        if ($newVoDays > 0) {
            $currentVOCount = collect($existingDocs)
                ->filter(fn($d) => str_starts_with((string) $d, 'Variation Order'))
                ->count();
            $nextVONumber = $currentVOCount + 1;
            $newVOLabel = "Variation Order {$nextVONumber}";
            $existingDocs[] = $newVOLabel;
            $existingVoDays[] = $newVoDays;
            $existingVoCosts[] = ($newVoCost !== null && $newVoCost !== '') ? (float) $newVoCost : null;
            $existingDates[] = $newVODate ?: null;

            if ($newVOReason !== '') {
                $existing = trim($data['remarks_recommendation'] ?? '');
                $note = $this->formatEntryRemark($newVOLabel, 'added', $newVOReason);
                $data['remarks_recommendation'] = $existing !== '' ? $existing . "\n\n" . $note : $note;
            }
        }

        $data['vo_days'] = array_values($existingVoDays);
        $data['vo_cost'] = array_values($existingVoCosts);
        $data['date_requested'] = empty($existingDates)
            ? null
            : array_values(array_map(fn($d) => ($d !== '' ? $d : null), $existingDates));

        // ── Step 4c: Recalculate billing summary ──────────────────
        $data['total_amount_billed'] = array_sum($data['billing_amounts']);

        $allCurrentCosts = array_merge(array_values($existingCosts), array_values($existingVoCosts));
        $totalCostAdj = collect($allCurrentCosts)
            ->filter(fn($c) => $c !== null && (float) $c !== 0.0)
            ->sum();

        $adjustedContractAmount = max(0, (float) $request->original_contract_amount + $totalCostAdj);
        $data['remaining_balance'] = $adjustedContractAmount - $data['total_amount_billed'];

        // ── Step 5: Handle Suspension Order ──────────────────────
        $newSODays = (int) $request->input('new_so_days', 0);
        $newSOReason = trim($request->input('new_so_reason', ''));
        $hasSO = collect($existingDocs)->contains('Suspension Order');

        if ($newSODays > 0) {
            if (!$hasSO) {
                $existingDocs[] = 'Suspension Order';
                $hasSO = true;
            }
            $data['suspension_days'] = $existingSuspDay + $newSODays;

            if ($newSOReason !== '') {
                $existing = trim($data['remarks_recommendation'] ?? '');
                $note = $this->formatEntryRemark('Suspension Order', 'added', $newSOReason);
                $data['remarks_recommendation'] = $existing !== '' ? $existing . "\n\n" . $note : $note;
            }
        } else {
            $data['suspension_days'] = $existingSuspDay ?: null;
        }

        $data['documents_pressed'] = array_values($existingDocs);
        $data['extension_days'] = array_values($existingDays);
        $data['cost_involved'] = array_values($existingCosts);

        // ── Step 6: Auto-count TE / VO totals ────────────────────
        $data['time_extension'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Time Extension'))
            ->count();

        $data['variation_order'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Variation Order'))
            ->count();

        // ── Step 7: Recompute revised expiry & contract days ─────
        $totalTEDays = (int) array_sum($data['extension_days']);
        $totalVODays = (int) array_sum(array_map('intval', array_filter((array) ($data['vo_days'] ?? []))));
        $totalSODays = (int) ($data['suspension_days'] ?? 0);
        $baseExpiry = Carbon::parse($request->original_contract_expiry);
        $totalExtDays = $totalTEDays + $totalVODays;

        $existingTEInDB = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $existingVOInDB = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays = (int) Carbon::parse($fresh->date_started)->diffInDays(Carbon::parse($fresh->original_contract_expiry)) + 1;
        $baseContractDays = max(1, (int) ($fresh->contract_days ?? $originalContractDays) - $existingTEInDB - $existingVOInDB);

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

        // ── Step 7b: Advance billing & retention ─────────────────
        $advPct = $request->input('advance_billing_pct');
        $advAmt = $request->input('advance_billing_amount');
        if ($advAmt !== null && $advAmt !== '') {
            $data['advance_billing_pct'] = ($advPct !== null && $advPct !== '') ? (float) $advPct : null;
            $data['advance_billing_amount'] = round((float) $advAmt, 2);
        } else {
            $data['advance_billing_pct'] = null;
            $data['advance_billing_amount'] = null;
        }

        $retPct = $request->input('retention_pct');
        $retAmt = $request->input('retention_amount');
        if ($retAmt !== null && $retAmt !== '') {
            $data['retention_pct'] = ($retPct !== null && $retPct !== '') ? (float) $retPct : null;
            $data['retention_amount'] = round((float) $retAmt, 2);
        } else {
            $data['retention_pct'] = null;
            $data['retention_amount'] = null;
        }

        // ── Step 8: Liquidated Damages ────────────────────────────
        // FIX BUG 2: LD must use the adjusted contract amount computed from scratch here,
        // NOT $data['remaining_balance'] which was set in Step 4c using old costs.
        // The final billing recalc at the bottom will overwrite remaining_balance anyway,
        // so we compute the LD basis independently to guarantee correctness.
        $ldAccomplished = isset($data['ld_accomplished']) && $data['ld_accomplished'] !== null
            ? (float) $data['ld_accomplished']
            : 0.0;

        $allCostsForLd = array_merge(
            array_values($data['cost_involved'] ?? []),
            array_values($data['vo_cost'] ?? [])
        );
        $totalCostAdjForLd = collect($allCostsForLd)
            ->filter(fn($c) => $c !== null && (float) $c !== 0.0)
            ->sum();
        $adjustedAmountForLd = max(0, (float) $request->original_contract_amount + $totalCostAdjForLd);
        $totalBilledForLd = array_sum($data['billing_amounts'] ?? []);
        $ldBasisAmount = max(0, $adjustedAmountForLd - $totalBilledForLd);

        $ldUnworked = max(0, 100 - $ldAccomplished);
        $ldPerDay = ($ldUnworked / 100) * $ldBasisAmount * 0.001;

        $data['ld_per_day'] = $ldPerDay > 0 ? round($ldPerDay, 2) : null;
        $data['ld_unworked'] = $ldPerDay > 0 ? round($ldUnworked, 2) : null;

        $ldStartDate = $request->input('ld_start_date');
        $ldEndDate = $request->input('ld_end_date');
        $workDone = (float) $request->input('work_done', 0);
        $today = now()->startOfDay();

        $data['ld_start_date'] = $ldStartDate ?: null;
        $data['ld_end_date'] = $ldEndDate ?: null;

        // ── LD Termination override ───────────────────────────────
        $ldAction = $request->input('ld_action');

        if ($ldAction === 'terminate' && in_array($fresh->ld_status, ['active'])) {
            $data['ld_status'] = 'terminated';
            $data['ld_end_date'] = $ldEndDate ?: now()->toDateString();
            $data['ld_start_date'] = $ldStartDate ?: $fresh->ld_start_date?->toDateString();
        } else {
            // ── Step 8: ld_status derivation ─────────────────────
            if (!$ldStartDate) {
                $data['ld_status'] = 'inactive';
            } else {
                $start = \Carbon\Carbon::parse($ldStartDate)->startOfDay();

                if ($today->lt($start)) {
                    $data['ld_status'] = 'inactive';
                } elseif ($workDone >= 100) {
                    $data['ld_status'] = 'completed';
                    $data['ld_end_date'] = $ldEndDate ?: $today->toDateString();
                } elseif ($ldEndDate && $today->gte(\Carbon\Carbon::parse($ldEndDate)->startOfDay())) {
                    $data['ld_status'] = 'terminated';
                } else {
                    $data['ld_status'] = 'active';
                }
            }
        }

        $daysOverdue = 0;
        if (
            in_array($data['ld_status'], ['active', 'terminated', 'completed'])
            && !empty($data['ld_start_date'])
        ) {
            $start = \Carbon\Carbon::parse($data['ld_start_date'])->startOfDay();
            $end = !empty($data['ld_end_date'])
                ? \Carbon\Carbon::parse($data['ld_end_date'])->startOfDay()
                : $today;
            $daysOverdue = (int) max(0, $start->diffInDays($end, false));
        }

        $data['ld_days_overdue'] = $daysOverdue > 0 ? $daysOverdue : null;
        $data['total_ld'] = ($ldPerDay > 0 && $daysOverdue > 0)
            ? round($ldPerDay * $daysOverdue, 2)
            : null;

        // ── Step 9: Clear LD fields when blank ───────────────────
        if ($ldAccomplished <= 0) {
            $data['ld_accomplished'] = null;
            $data['ld_unworked'] = null;
            $data['ld_per_day'] = null;
            $data['total_ld'] = null;
        }

        // ── Step 10: Auto-derive status ───────────────────────────
        if ($request->input('status') === 'reactivate') {
            $data['completed_at'] = null;
            $effectiveExpiry = $data['revised_contract_expiry'] ?? $data['original_contract_expiry'] ?? null;
            if ($effectiveExpiry) {
                $daysLeft = now()->startOfDay()->diffInDays(Carbon::parse($effectiveExpiry)->startOfDay(), false);
                $data['status'] = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'expiring' : 'ongoing');
            } else {
                $data['status'] = 'ongoing';
            }
        } elseif ($request->filled('completed_at')) {
            $data['status'] = 'completed';
            $data['completed_at'] = $request->completed_at;
        } else {
            $data['completed_at'] = null;
            $effectiveExpiry = $data['revised_contract_expiry'] ?? $data['original_contract_expiry'] ?? null;
            if ($effectiveExpiry) {
                $daysLeft = now()->startOfDay()->diffInDays(Carbon::parse($effectiveExpiry)->startOfDay(), false);
                $data['status'] = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'expiring' : 'ongoing');
            }
        }

        // ── Final billing summary ─────────────────────────────────
        $allCostsAfterEdit = array_merge(
            array_values($data['cost_involved'] ?? []),
            array_values($data['vo_cost'] ?? [])
        );
        $costAdj = collect($allCostsAfterEdit)
            ->filter(fn($c) => $c !== null && (float) $c != 0)
            ->sum();

        $data['total_amount_billed'] = array_sum($data['billing_amounts']);
        $data['remaining_balance'] = (float) $request->original_contract_amount + $costAdj - $data['total_amount_billed'];

        $project->update($data);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }


    // ============================================================
    // SECTION 5: BILLING
    // ============================================================

    public function updateBilling(Request $request, Project $project)
    {
        $this->authorizeProjectAccess($project);

        $request->validate([
            'billing_index' => 'required|integer|min:0',
            'billing_amount' => 'required|numeric|min:0',
            'billing_date' => 'nullable|date',
        ]);

        $fresh = $project->fresh();
        $index = (int) $request->input('billing_index');
        $amount = (float) $request->input('billing_amount');
        $date = $request->input('billing_date');

        $billingAmounts = is_array($fresh->billing_amounts) ? array_map('floatval', $fresh->billing_amounts) : [];
        $billingDates = is_array($fresh->billing_dates) ? $fresh->billing_dates : [];

        if (!isset($billingAmounts[$index])) {
            return back()->with('error', 'Billing entry not found.');
        }

        $billingAmounts[$index] = $amount;
        $billingDates[$index] = $date ?: null;

        $totalBilled = array_sum($billingAmounts);
        $allSavedCosts = array_merge(
            is_array($fresh->cost_involved) ? $fresh->cost_involved : [],
            is_array($fresh->vo_cost) ? $fresh->vo_cost : []
        );
        $totalCostAdj = collect($allSavedCosts)
            ->filter(fn($c) => $c !== null && (float) $c !== 0.0)
            ->sum();
        $adjustedContractAmount = max(0, (float) $fresh->original_contract_amount + $totalCostAdj);

        $project->update([
            'billing_amounts' => array_values($billingAmounts),
            'billing_dates' => array_values($billingDates),
            'total_amount_billed' => $totalBilled,
            'remaining_balance' => $adjustedContractAmount - $totalBilled,
        ]);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Billing No. ' . ($index + 1) . ' updated successfully.');
    }


    // ============================================================
    // SECTION 6: TIME EXTENSION / VARIATION ORDER — INLINE EDIT & DELETE
    // ============================================================

    public function updateEntry(Request $request, Project $project)
    {
        $this->authorizeProjectAccess($project);

        $request->validate([
            'edit_entry_type' => 'required|in:te,vo',
            'edit_entry_index' => 'required|integer|min:0',
            'edit_days' => 'required|integer|min:1|max:9999',
            'edit_cost' => 'nullable|decimal:0,4',
            'edit_date_requested' => 'nullable|date',
            'edit_reason' => 'required|string|max:1000',
        ]);

        $fresh = $project->fresh();
        $type = $request->input('edit_entry_type');
        $index = (int) $request->input('edit_entry_index');
        $days = (int) $request->input('edit_days');
        $cost = $request->input('edit_cost');
        $date = $request->input('edit_date_requested');
        $reason = trim($request->input('edit_reason'));

        $existingDocs = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $dateRequested = is_array($fresh->date_requested) ? $fresh->date_requested : [];

        $teCount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        $data = [];

        if ($type === 'te') {
            $extensionDays = is_array($fresh->extension_days) ? array_map('intval', $fresh->extension_days) : [];
            $costInvolved = is_array($fresh->cost_involved) ? $fresh->cost_involved : [];

            if (!isset($extensionDays[$index])) {
                return back()->with('error', 'Time Extension entry not found.');
            }

            $extensionDays[$index] = $days;
            $costInvolved[$index] = ($cost !== null && $cost !== '') ? (float) $cost : null;
            $dateRequested[$index] = $date ?: null;

            $data['extension_days'] = array_values($extensionDays);
            $data['cost_involved'] = array_values($costInvolved);
            $data['date_requested'] = array_values($dateRequested);
        } else {
            $voDays = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days)) : [];
            $voCosts = is_array($fresh->vo_cost) ? $fresh->vo_cost : [];

            if (!isset($voDays[$index])) {
                return back()->with('error', 'Variation Order entry not found.');
            }

            $voDays[$index] = $days;
            $voCosts[$index] = ($cost !== null && $cost !== '') ? (float) $cost : null;
            $dateRequested[$teCount + $index] = $date ?: null;

            $data['vo_days'] = array_values($voDays);
            $data['vo_cost'] = array_values($voCosts);
            $data['date_requested'] = array_values($dateRequested);
        }

        // Recompute revised expiry
        $allExtDays = array_sum(array_map('intval', $data['extension_days'] ?? $fresh->extension_days ?? []));
        $allVODays = array_sum(array_map('intval', $data['vo_days'] ?? $fresh->vo_days ?? []));
        $sodays = (int) ($fresh->suspension_days ?? 0);
        $hasSO = collect($existingDocs)->contains('Suspension Order');
        $total = $allExtDays + $allVODays + ($hasSO ? $sodays : 0);

        $data['revised_contract_expiry'] = $total > 0
            ? Carbon::parse($fresh->original_contract_expiry)->addDays($total)->toDateString()
            : null;

        // Recompute contract_days
        $previousTEDays = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $previousVODays = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays = (int) ($fresh->contract_days ?? 0) - $previousTEDays - $previousVODays;
        $currentTEDays = (int) array_sum(array_map('intval', $data['extension_days'] ?? $fresh->extension_days ?? []));
        $currentVODays = (int) array_sum(array_map('intval', $data['vo_days'] ?? $fresh->vo_days ?? []));
        $data['contract_days'] = $originalContractDays + $currentTEDays + $currentVODays;

        // Resolve label
        $labelCounter = 0;
        $resolvedLabel = ($type === 'te' ? 'Time Extension' : 'Variation Order') . ' ' . ($index + 1);
        foreach ($existingDocs as $doc) {
            $prefix = $type === 'te' ? 'Time Extension' : 'Variation Order';
            if (str_starts_with((string) $doc, $prefix)) {
                if ($labelCounter === $index) {
                    $resolvedLabel = $doc;
                    break;
                }
                $labelCounter++;
            }
        }

        // Append edit note
        $existing = trim($fresh->remarks_recommendation ?? '');
        $note = $this->formatEntryRemark($resolvedLabel, 'updated', $reason);
        $data['remarks_recommendation'] = $existing !== '' ? $existing . "\n\n" . $note : $note;

        $project->update($data);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', ucfirst($type === 'te' ? 'Time Extension' : 'Variation Order') . ' updated successfully.');
    }

    private function formatEntryRemark(string $label, string $action, string $reason): string
    {
        $timestamp = now();
        $time = $timestamp->format('h:i A');
        $date = $timestamp->format('F d, Y');
        $cleanAction = $action === 'edited' ? 'updated' : $action;
        $cleanReason = trim(preg_replace('/\s+/', ' ', $reason));
        $shortLabel = preg_match('/^(Time Extension|Variation Order)\s+(\d+)$/', $label, $matches)
            ? sprintf('%s #%s', $matches[1] === 'Time Extension' ? 'Extension' : 'Variation', $matches[2])
            : $label;

        return "● {$time} • {$date}\n  {$shortLabel} {$cleanAction}\n  Justification: {$cleanReason}";
    }

    private function splitRemarks(string $raw): array
    {
        $pattern = '/(?:^|\n\n)(●\s+\d{1,2}:\d{2}\s+(?:AM|PM)\s+•\s+[^\n]+(?:\n[ \t]+[^\n]+)*)/m';

        $auto = [];
        preg_match_all($pattern, $raw, $matches);
        foreach ($matches[1] as $m) {
            $auto[] = trim($m);
        }

        $manual = trim(preg_replace($pattern, '', $raw));
        $manual = trim(preg_replace('/\n{3,}/', "\n\n", $manual));

        return [
            'manual' => $manual,
            'auto' => implode("\n\n", $auto),
        ];
    }

    public function destroyEntry(Request $request, Project $project)
    {
        $this->authorizeProjectAccess($project);

        $request->validate([
            'entry_type' => 'required|in:te,vo',
            'entry_index' => 'required|integer|min:0',
            'delete_reason' => 'required|string|max:1000',
        ]);

        $fresh = $project->fresh();
        $type = $request->input('entry_type');
        $index = (int) $request->input('entry_index');
        $reason = trim($request->input('delete_reason'));

        $existingDocs = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $existingDays = is_array($fresh->extension_days) ? array_map('intval', $fresh->extension_days) : [];
        $existingCosts = is_array($fresh->cost_involved) ? $fresh->cost_involved : [];
        $existingDates = is_array($fresh->date_requested) ? $fresh->date_requested : [];
        $existingVoDays = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days)) : [];
        $existingVoCosts = is_array($fresh->vo_cost) ? $fresh->vo_cost : [];

        $teCount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        if ($type === 'te') {
            // FIX BUG 3: Validate array bounds BEFORE splicing to prevent silent data corruption.
            // If $existingDays or $existingCosts are shorter than expected (data corruption),
            // we bail early rather than splice wrong indices and silently destroy unrelated entries.
            if ($index >= count($existingDays) || $index >= count($existingCosts)) {
                return back()->with('error', 'Array mismatch detected: Time Extension data may be corrupted. Please contact your administrator.');
            }

            $tesSeen = 0;
            $docIndexToRemove = null;
            foreach ($existingDocs as $di => $doc) {
                if (str_starts_with((string) $doc, 'Time Extension')) {
                    if ($tesSeen === $index) {
                        $docIndexToRemove = $di;
                        break;
                    }
                    $tesSeen++;
                }
            }
            if ($docIndexToRemove === null) {
                return back()->with('error', 'Time Extension entry not found.');
            }
            $deletedLabel = $existingDocs[$docIndexToRemove];

            array_splice($existingDocs, $docIndexToRemove, 1);
            array_splice($existingDays, $index, 1);
            array_splice($existingCosts, $index, 1);
            array_splice($existingDates, $index, 1);

            $teNum = 1;
            foreach ($existingDocs as &$doc) {
                if (str_starts_with((string) $doc, 'Time Extension')) {
                    $doc = "Time Extension {$teNum}";
                    $teNum++;
                }
            }
            unset($doc);
        } else {
            // FIX BUG 3 (VO side): Same bounds check for Variation Order arrays.
            if ($index >= count($existingVoDays) || $index >= count($existingVoCosts)) {
                return back()->with('error', 'Array mismatch detected: Variation Order data may be corrupted. Please contact your administrator.');
            }

            $vosSeen = 0;
            $docIndexToRemove = null;
            foreach ($existingDocs as $di => $doc) {
                if (str_starts_with((string) $doc, 'Variation Order')) {
                    if ($vosSeen === $index) {
                        $docIndexToRemove = $di;
                        break;
                    }
                    $vosSeen++;
                }
            }
            if ($docIndexToRemove === null) {
                return back()->with('error', 'Variation Order entry not found.');
            }
            $deletedLabel = $existingDocs[$docIndexToRemove];

            array_splice($existingDocs, $docIndexToRemove, 1);
            array_splice($existingVoDays, $index, 1);
            array_splice($existingVoCosts, $index, 1);
            array_splice($existingDates, $teCount + $index, 1);

            $voNum = 1;
            foreach ($existingDocs as &$doc) {
                if (str_starts_with((string) $doc, 'Variation Order')) {
                    $doc = "Variation Order {$voNum}";
                    $voNum++;
                }
            }
            unset($doc);
        }

        $data = [
            'documents_pressed' => array_values($existingDocs),
            'extension_days' => array_values($existingDays),
            'cost_involved' => array_values($existingCosts),
            'date_requested' => empty($existingDates)
                ? null
                : array_values(array_map(fn($d) => ($d !== '' ? $d : null), $existingDates)),
            'vo_days' => array_values($existingVoDays),
            'vo_cost' => array_values($existingVoCosts),
        ];

        $data['time_extension'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Time Extension'))
            ->count();
        $data['variation_order'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Variation Order'))
            ->count();

        $totalTE = (int) array_sum($data['extension_days']);
        $totalVO = (int) array_sum(array_map('intval', array_filter((array) ($data['vo_days'] ?? []))));
        $totalSO = (int) ($fresh->suspension_days ?? 0);
        $hasSO = collect($data['documents_pressed'])->contains('Suspension Order');
        $total = $totalTE + $totalVO + ($hasSO ? $totalSO : 0);

        $data['revised_contract_expiry'] = $total > 0
            ? Carbon::parse($fresh->original_contract_expiry)->addDays($total)->toDateString()
            : null;

        $previousTEDays = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $previousVODays = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays = (int) ($fresh->contract_days ?? 0) - $previousTEDays - $previousVODays;
        $data['contract_days'] = $originalContractDays + $totalTE + $totalVO;

        $existing = trim($fresh->remarks_recommendation ?? '');
        $note = $this->formatEntryRemark($deletedLabel, 'deleted', $reason);
        $data['remarks_recommendation'] = $existing !== '' ? $existing . "\n\n" . $note : $note;

        $project->update($data);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', "{$deletedLabel} deleted. Reason logged to remarks.");
    }


    // ============================================================
    // SECTION 7: STATUS MANAGEMENT
    // ============================================================

    public function reactivate(Project $project)
    {
        $this->authorizeProjectAccess($project);

        $effectiveExpiry = $project->revised_contract_expiry ?? $project->original_contract_expiry;

        $daysLeft = now()->startOfDay()->diffInDays(
            Carbon::parse($effectiveExpiry)->startOfDay(),
            false
        );

        $status = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'expiring' : 'ongoing');

        $project->update([
            'status' => $status,
            'completed_at' => null,
        ]);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Project reactivated successfully. Status set to ' . ucfirst($status) . '.');
    }


    // ============================================================
    // SECTION 8: REPORTS & PDF GENERATION
    // ============================================================

    public function reports()
    {
        $projects = $this->divisionQuery()->orderBy('date_started', 'desc')->get();
        $total = $projects->count();
        $ongoing = $projects->where('status', 'ongoing')->count();
        $completed = $projects->where('status', 'completed')->count();
        $expired = $projects->where('status', 'expired')->count();

        return view('admin.reports.index', compact('projects', 'total', 'ongoing', 'completed', 'expired'));
    }

    public function generateReport()
    {
        $clean = fn(string $s) => iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;

        // Start from division-scoped base
        $query = $this->divisionQuery();

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('project_title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('contractor', 'like', "%{$search}%");
            });
        }

        if (request('in_charge')) {
            $query->where('in_charge', request('in_charge'));
        }

        $status = request('status', 'all');
        if ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'active') {
            $query->where('status', 'ongoing')->where(function ($q) {
                $q->whereNull('revised_contract_expiry')->where('original_contract_expiry', '>', now()->addDays(30))
                    ->orWhere('revised_contract_expiry', '>', now()->addDays(30));
            });
        } elseif ($status === 'expiring') {
            $query->where('status', '!=', 'completed')->where(function ($q) {
                $q->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry', [now(), now()->addDays(30)])
                    ->orWhereBetween('revised_contract_expiry', [now(), now()->addDays(30)]);
            });
        } elseif ($status === 'expired') {
            $query->where('status', '!=', 'completed')->where(function ($q) {
                $q->whereNull('revised_contract_expiry')->where('original_contract_expiry', '<', now())
                    ->orWhere('revised_contract_expiry', '<', now());
            });
        } elseif ($status === 'ongoing') {
            $query->where('status', 'ongoing')->where(function ($q) {
                $q->whereNull('revised_contract_expiry')->where('original_contract_expiry', '>=', now())
                    ->orWhere('revised_contract_expiry', '>=', now());
            });
        }

        $projects = $query->orderBy('date_started', 'desc')->get();
        $total = $projects->count();
        $ongoing = $projects->where('status', 'ongoing')->count();
        $completed = $projects->where('status', 'completed')->count();
        $expired = $projects->where('status', 'expired')->count();

        $filterParts = [];
        if (request('search'))
            $filterParts[] = 'Search: "' . request('search') . '"';
        if (request('in_charge'))
            $filterParts[] = 'In Charge: ' . request('in_charge');
        if ($status && $status !== 'all')
            $filterParts[] = 'Status: ' . ucfirst($status);

        // Always include division in the label for division admins
        if (!$this->isSuperAdmin()) {
            $filterParts[] = 'Division: ' . $this->currentDivision();
        }

        $filterLabel = count($filterParts) ? implode('  |  ', $filterParts) : 'All Projects';

        $pdf = new ProjectReportPdf('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->setGeneratedAt(now()->format('F d, Y  h:i A'));
        $pdf->setFilterLabel($filterLabel);
        $pdf->AddPage();

        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetTextColor(107, 79, 53);
        $pdf->Cell(0, 5, 'PROJECT DETAILS - ' . $total . ' records', 0, 1, 'L');
        $pdf->Ln(2);

        $cols = [
            ['#', 8, 'C'],
            ['Project Title', 55, 'L'],
            ['In Charge', 32, 'L'],
            ['Location', 30, 'L'],
            ['Contractor', 32, 'L'],
            ['Contract Amt', 30, 'R'],
            ['Started', 25, 'C'],
            ['Expiry', 25, 'C'],
            ['Slippage', 20, 'C'],
            ['Status', 20, 'C'],
        ];

        $pdf->TableHeader($cols);

        foreach ($projects as $i => $project) {
            if ($pdf->GetY() + 7 > 200) {
                $pdf->AddPage();
                $pdf->TableHeader($cols);
            }

            $expiry = $project->revised_contract_expiry ?? $project->original_contract_expiry;
            $slip = (float) ($project->slippage ?? 0);
            $slipStr = ($slip > 0 ? '+' : '') . number_format($slip, 2) . '%';
            $even = $i % 2 === 0;

            $slipColor = $slip > 0 ? [22, 163, 74] : ($slip < 0 ? [220, 38, 38] : [107, 114, 128]);
            $statusMap = [
                'completed' => [[240, 253, 244], [22, 163, 74], 'Completed'],
                'expired' => [[254, 242, 242], [220, 38, 38], 'Expired'],
                'expiring' => [[255, 251, 235], [217, 119, 6], 'Expiring'],
                'ongoing' => [[239, 246, 255], [37, 99, 235], 'Ongoing'],
            ];
            [$statusBg, $statusFg, $statusLabel] = $statusMap[$project->status] ?? $statusMap['ongoing'];

            $pdf->ProjectRow(
                $i + 1,
                mb_strimwidth($clean($project->project_title), 0, 35, '...'),
                mb_strimwidth($clean($project->in_charge), 0, 20, '...'),
                mb_strimwidth($clean($project->location), 0, 18, '...'),
                mb_strimwidth($clean($project->contractor), 0, 20, '...'),
                'P' . number_format($project->original_contract_amount, 2),
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

        $filename = 'projects-report-' . now()->format('Y-m-d') . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }


    // ============================================================
    // SECTION 9: SINGLE PROJECT PDF
    // ============================================================

    public function exportPdf(Project $project)
    {
        $this->authorizeProjectAccess($project);

        $clean = fn(string $s) => iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;
        $fresh = $project->fresh();

        $docs = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $teDays = is_array($fresh->extension_days) ? array_map('intval', $fresh->extension_days) : [];
        $teCosts = is_array($fresh->cost_involved) ? $fresh->cost_involved : [];
        $dates = is_array($fresh->date_requested) ? $fresh->date_requested : [];
        $voDays = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days)) : [];
        $voCosts = is_array($fresh->vo_cost) ? $fresh->vo_cost : [];
        $issuances = is_array($fresh->issuances) ? $fresh->issuances : [];
        $billingAmounts = is_array($fresh->billing_amounts) ? $fresh->billing_amounts : [];
        $billingDates = is_array($fresh->billing_dates) ? $fresh->billing_dates : [];

        $allCosts = array_merge(
            is_array($fresh->cost_involved) ? $fresh->cost_involved : [],
            is_array($fresh->vo_cost) ? $fresh->vo_cost : []
        );
        $totalAdj = collect($allCosts)->filter(fn($c) => $c !== null && (float) $c != 0)->sum();
        $adjusted = max(0, (float) $fresh->original_contract_amount + $totalAdj);
        $hasSO = collect($docs)->contains('Suspension Order');
        $teCount = collect($docs)->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))->count();

        $pdf = new ProjectReportPdf('P', 'mm', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->setGeneratedAt(now()->format('F d, Y  h:i A'));
        $pdf->suppressAutoHeader(true);
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 9);

        $pdf->DetailHeader();

        $sectionHeader = function (string $title) use ($pdf) {
            $pdf->Ln(4);
            $pdf->SetFillColor(107, 79, 53);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(0, 7, strtoupper($title), 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Ln(1);
        };

        $labelValue = function (string $label, string $value) use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(65, 6, $label . ':', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(0, 6, $value, 0, 'L');
        };

        $twoCol = function (string $l1, string $v1, string $l2, string $v2) use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(50, 6, $l1 . ($l1 !== '' ? ':' : ''), 0, 0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(45, 6, $v1, 0, 0);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(50, 6, $l2 . ($l2 !== '' ? ':' : ''), 0, 0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(0, 6, $v2, 0, 1);
        };

        $sectionHeader('Project Information');
        $twoCol('Contract ID', $clean($fresh->contract_id ?? ''), 'Status', ucfirst($fresh->status ?? ''));
        $labelValue('Project Title', $clean($fresh->project_title ?? ''));
        $twoCol('In Charge', $clean($fresh->in_charge ?? ''), 'Contractor', $clean($fresh->contractor ?? ''));
        $labelValue('Location', $clean($fresh->location ?? ''));
        $labelValue('Division', $clean($fresh->division ?? 'N/A'));

        $sectionHeader('Contract Schedule');
        $twoCol('Date Started', optional($fresh->date_started)->format('M d, Y') ?? 'N/A', 'Contract Days', (string) ($fresh->contract_days ?? 'N/A'));
        $twoCol('Original Contract Expiry', optional($fresh->original_contract_expiry)->format('M d, Y') ?? 'N/A', 'Revised Contract Expiry', optional($fresh->revised_contract_expiry)?->format('M d, Y') ?? 'N/A');
        $twoCol('Completed At', optional($fresh->completed_at)?->format('M d, Y') ?? 'N/A', 'Suspension Days', $hasSO ? (string) (int) ($fresh->suspension_days ?? 0) : 'N/A');

        $sectionHeader('Contract Amounts');
        $twoCol('Original Contract Amount', 'PHP ' . number_format((float) $fresh->original_contract_amount, 2), 'Total Cost Adjustments', 'PHP ' . number_format($totalAdj, 2));
        $twoCol('Remaining Balance', 'PHP ' . number_format((float) ($fresh->remaining_balance ?? 0), 2), 'Advance Billing Amount', $fresh->advance_billing_amount !== null ? 'PHP ' . number_format((float) $fresh->advance_billing_amount, 2) : 'N/A');
        $twoCol('Advance Billing %', $fresh->advance_billing_pct !== null ? $fresh->advance_billing_pct . '%' : 'N/A', 'Retention Amount', $fresh->retention_amount !== null ? 'PHP ' . number_format((float) $fresh->retention_amount, 2) : 'N/A');
        $labelValue('Retention %', $fresh->retention_pct !== null ? $fresh->retention_pct . '%' : 'N/A');

        $sectionHeader('Progress');
        $twoCol('As Planned', $fresh->as_planned . '%', 'Work Done', $fresh->work_done . '%');
        $slip = (float) ($fresh->slippage ?? 0);
        $slipStr = ($slip > 0 ? '+' : '') . number_format($slip, 2) . '%';
        $labelValue('Slippage', $slipStr);

        $sectionHeader('Billing History');
        if (count($billingAmounts) > 0) {
            $pdf->SetFillColor(230, 220, 210);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->Cell(15, 6, '#', 'B', 0, 'C', true);
            $pdf->Cell(100, 6, 'Amount', 'B', 0, 'L', true);
            $pdf->Cell(0, 6, 'Date', 'B', 1, 'L', true);
            $pdf->SetFont('helvetica', '', 8);
            foreach ($billingAmounts as $i => $amount) {
                $even = $i % 2 === 0;
                $pdf->SetFillColor($even ? 250 : 255, $even ? 248 : 255, $even ? 245 : 255);
                $date = $billingDates[$i] ?? null;
                $pdf->Cell(15, 6, (string) ($i + 1), 0, 0, 'C', true);
                $pdf->Cell(100, 6, 'PHP ' . number_format((float) $amount, 2), 0, 0, 'L', true);
                $pdf->Cell(0, 6, $date ? Carbon::parse($date)->format('M d, Y') : 'N/A', 0, 1, 'L', true);
            }
        } else {
            $pdf->Cell(0, 6, 'No billing entries recorded.', 0, 1);
        }

        $sectionHeader('Time Extensions');
        if ($teCount > 0) {
            $pdf->SetFillColor(230, 220, 210);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->Cell(8, 6, '#', 'B', 0, 'C', true);
            $pdf->Cell(55, 6, 'Label', 'B', 0, 'L', true);
            $pdf->Cell(20, 6, 'Days', 'B', 0, 'C', true);
            $pdf->Cell(55, 6, 'Cost Involved', 'B', 0, 'R', true);
            $pdf->Cell(0, 6, 'Date Requested', 'B', 1, 'L', true);
            $pdf->SetFont('helvetica', '', 8);
            $teIndex = 0;
            foreach ($docs as $doc) {
                if (str_starts_with((string) $doc, 'Time Extension')) {
                    $even = $teIndex % 2 === 0;
                    $pdf->SetFillColor($even ? 250 : 255, $even ? 248 : 255, $even ? 245 : 255);
                    $cost = isset($teCosts[$teIndex]) && $teCosts[$teIndex] !== null ? 'PHP ' . number_format((float) $teCosts[$teIndex], 2) : 'N/A';
                    $date = isset($dates[$teIndex]) && $dates[$teIndex] ? Carbon::parse($dates[$teIndex])->format('M d, Y') : 'N/A';
                    $pdf->Cell(8, 6, (string) ($teIndex + 1), 0, 0, 'C', true);
                    $pdf->Cell(55, 6, $clean($doc), 0, 0, 'L', true);
                    $pdf->Cell(20, 6, (string) ($teDays[$teIndex] ?? 0), 0, 0, 'C', true);
                    $pdf->Cell(55, 6, $cost, 0, 0, 'R', true);
                    $pdf->Cell(0, 6, $date, 0, 1, 'L', true);
                    $teIndex++;
                }
            }
        } else {
            $pdf->Cell(0, 6, 'No time extensions recorded.', 0, 1);
        }

        $sectionHeader('Variation Orders');
        $voCount = collect($docs)->filter(fn($d) => str_starts_with((string) $d, 'Variation Order'))->count();
        if ($voCount > 0) {
            $pdf->SetFillColor(230, 220, 210);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->Cell(8, 6, '#', 'B', 0, 'C', true);
            $pdf->Cell(55, 6, 'Label', 'B', 0, 'L', true);
            $pdf->Cell(20, 6, 'Days', 'B', 0, 'C', true);
            $pdf->Cell(55, 6, 'Cost Involved', 'B', 0, 'R', true);
            $pdf->Cell(0, 6, 'Date Requested', 'B', 1, 'L', true);
            $pdf->SetFont('helvetica', '', 8);
            $voIndex = 0;
            foreach ($docs as $doc) {
                if (str_starts_with((string) $doc, 'Variation Order')) {
                    $even = $voIndex % 2 === 0;
                    $pdf->SetFillColor($even ? 250 : 255, $even ? 248 : 255, $even ? 245 : 255);
                    $cost = isset($voCosts[$voIndex]) && $voCosts[$voIndex] !== null ? 'PHP ' . number_format((float) $voCosts[$voIndex], 2) : 'N/A';
                    $date = isset($dates[$teCount + $voIndex]) && $dates[$teCount + $voIndex] ? Carbon::parse($dates[$teCount + $voIndex])->format('M d, Y') : 'N/A';
                    $pdf->Cell(8, 6, (string) ($voIndex + 1), 0, 0, 'C', true);
                    $pdf->Cell(55, 6, $clean($doc), 0, 0, 'L', true);
                    $pdf->Cell(20, 6, (string) ($voDays[$voIndex] ?? 0), 0, 0, 'C', true);
                    $pdf->Cell(55, 6, $cost, 0, 0, 'R', true);
                    $pdf->Cell(0, 6, $date, 0, 1, 'L', true);
                    $voIndex++;
                }
            }
        } else {
            $pdf->Cell(0, 6, 'No variation orders recorded.', 0, 1);
        }

        $sectionHeader('Issuances');
        if (count($issuances) > 0) {
            foreach ($issuances as $i => $issuance) {
                $pdf->Cell(8, 6, ($i + 1) . '.', 0, 0);
                $pdf->Cell(0, 6, $clean($issuance), 0, 1);
            }
        } else {
            $pdf->Cell(0, 6, 'No issuances recorded.', 0, 1);
        }

        $sectionHeader('Liquidated Damages');
        $twoCol('Accomplished (%)', $fresh->ld_accomplished !== null ? $fresh->ld_accomplished . '%' : 'N/A', 'Unworked (%)', $fresh->ld_unworked !== null ? $fresh->ld_unworked . '%' : 'N/A');
        $twoCol('LD Per Day', $fresh->ld_per_day !== null ? 'PHP ' . number_format((float) $fresh->ld_per_day, 2) : 'N/A', 'Days Overdue', $fresh->ld_days_overdue !== null ? (string) $fresh->ld_days_overdue : 'N/A');
        $twoCol('Total LD', $fresh->total_ld !== null ? 'PHP ' . number_format((float) $fresh->total_ld, 2) : 'N/A', '', '');

        $sectionHeader('Remarks & Recommendations');
        $remarks = trim($fresh->remarks_recommendation ?? '');
        if ($remarks !== '') {
            $pdf->SetFont('helvetica', '', 8);
            $pdf->MultiCell(0, 5, $clean($remarks), 0, 'L');
        } else {
            $pdf->Cell(0, 6, 'No remarks recorded.', 0, 1);
        }

        $filename = 'project-' . str_replace(['/', ' '], '-', $fresh->contract_id) . '-' . now()->format('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }


    // ============================================================
    // SECTION 10: DELETE
    // ============================================================

    public function destroy(Project $project)
    {
        $this->authorizeProjectAccess($project);

        // Only super admins can delete projects
        if (!$this->isSuperAdmin()) {
            abort(403, 'Only super admins can delete projects.');
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}