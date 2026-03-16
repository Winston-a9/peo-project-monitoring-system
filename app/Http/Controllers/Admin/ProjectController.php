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
    public function index()
    {
        $perPage  = in_array((int)request('per_page', 10), [10, 25, 50]) ? (int)request('per_page', 10) : 10;
        $projects = Project::paginate($perPage)->withQueryString();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

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
            'status'                   => 'required|in:ongoing,completed,expired',
            'completed_at'             => 'nullable|date',
        ]);

        $data = $request->only([
            'in_charge', 'project_title', 'location', 'contractor',
            'contract_amount', 'date_started', 'contract_days',
            'original_contract_expiry',
            'as_planned', 'work_done',
            'status', 'completed_at',
        ]);

        $data['original_contract_amount'] = $request->contract_amount;

        if ($request->status === 'ongoing') {
            $data['completed_at'] = null;
        }

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['logs.user' => fn($q) => $q->select('id', 'name')]);
        $project->logs = $project->logs->sortByDesc('created_at');
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $fresh = $project->fresh();

        $existingDocs  = $fresh->documents_pressed ?? [];
        $existingDays  = $fresh->extension_days    ?? [];
        $existingCosts = $fresh->cost_involved     ?? [];

        $existingDocs  = is_array($existingDocs)  ? $existingDocs  : (json_decode($existingDocs  ?? '[]', true) ?? []);
        $existingDays  = is_array($existingDays)  ? $existingDays  : (json_decode($existingDays  ?? '[]', true) ?? []);
        $existingCosts = is_array($existingCosts) ? $existingCosts : (json_decode($existingCosts ?? '[]', true) ?? []);

        $existingDays  = array_map('intval', $existingDays);
        $existingCosts = array_map(fn($v) => $v !== null ? (float) $v : null, $existingCosts);

        // ── Date requested (parallel array) ──
        $existingDates = $fresh->date_requested ?? [];
        $existingDates = is_array($existingDates) ? $existingDates : (json_decode($existingDates ?? '[]', true) ?? []);

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

        $existingVoDays  = $fresh->vo_days ?? [];
        $existingVoCosts = $fresh->vo_cost ?? [];
        $existingVoDays  = is_array($existingVoDays)  ? $existingVoDays  : (json_decode($existingVoDays  ?? '[]', true) ?? []);
        $existingVoCosts = is_array($existingVoCosts) ? $existingVoCosts : (json_decode($existingVoCosts ?? '[]', true) ?? []);
        $existingVoDays  = array_map('intval', array_filter((array) $existingVoDays));
        $existingVoCosts = array_map(fn($v) => $v !== null ? (float) $v : null, $existingVoCosts);

        $voHistory = [];
        $voIndex   = 0;
        $voDateOffset = $teCount;
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

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'in_charge'                => 'required|string|max:255',
            'project_title'            => 'required|string|max:255',
            'location'                 => 'required|string|max:255',
            'contractor'               => 'required|string|max:255',
            'date_started'             => 'required|date',
            'contract_days'            => 'nullable|integer|min:1',
            'original_contract_expiry' => 'required|date',
            'status'                   => 'required|in:ongoing,completed,expired',
            'contract_amount'          => 'required|numeric|min:0',
            'as_planned'               => 'required|numeric|min:0|max:100',
            'work_done'                => 'required|numeric|min:0|max:100',
            'remarks_recommendation'   => 'nullable|string',
            'completed_at'             => 'nullable|date',
            'issuances'                => 'nullable|array',
            'issuances.*' => 'nullable|string|in:1st Notice of Negative Slippage,2nd Notice of Negative Slippage,3rd Notice of Negative Slippage,Liquidated Damages,Notice to Terminate,Notice of Expiry,Performance Bond',            'new_te_days'              => 'nullable|integer|min:1|max:9999',
            'new_te_cost'              => 'nullable|numeric',
            'new_te_date'              => 'nullable|date',
            'new_vo_days'              => 'nullable|integer|min:1|max:9999',
            'new_vo_cost'              => 'nullable|numeric',
            'new_vo_date'              => 'nullable|date',
            'new_so_days'              => 'nullable|integer|min:1|max:9999',
            'ld_accomplished'          => 'nullable|numeric|min:0|max:100',
            'ld_days_overdue'          => 'nullable|integer|min:0',
            'performance_bond_date'     => 'nullable|date',
        ]);

        // ── Step 1: Basic scalar fields ──
        $data = $request->only([
            'in_charge', 'project_title', 'location', 'contractor',
            'contract_amount', 'date_started', 'contract_days',
            'original_contract_expiry',
            'as_planned', 'work_done',
            'remarks_recommendation',
            'status', 'completed_at',
            'ld_accomplished', 'ld_days_overdue',
            'performance_bond_date',
        ]);

        $data['slippage'] = (float) $request->work_done - (float) $request->as_planned;

        if ($request->status === 'ongoing') {
            $data['completed_at'] = null;
        }

        // ── Step 2: Issuances ──
        $data['issuances'] = array_values(
            array_filter($request->input('issuances', []), fn($v) => !empty($v))
        );

        // ── Step 3: Carry forward existing arrays (always read fresh from DB) ──
        $fresh = $project->fresh();

        $existingDocs  = $fresh->documents_pressed ?? [];
        $existingDays  = $fresh->extension_days    ?? [];
        $existingCosts = $fresh->cost_involved     ?? [];

        $existingDocs  = is_array($existingDocs)  ? $existingDocs  : [];
        $existingDays  = is_array($existingDays)  ? array_map('intval', $existingDays) : [];
        $existingCosts = is_array($existingCosts) ? array_map(fn($v) => $v !== null ? (float) $v : null, $existingCosts) : [];

        $existingSuspDay = (int) ($fresh->suspension_days ?? 0);

        $existingVoDays  = $fresh->vo_days ?? [];
        $existingVoCosts = $fresh->vo_cost ?? [];
        $existingVoDays  = is_array($existingVoDays)  ? array_map('intval', array_filter((array) $existingVoDays))  : [];
        $existingVoCosts = is_array($existingVoCosts) ? array_map(fn($v) => $v !== null ? (float) $v : null, $existingVoCosts) : [];

        $existingDates = $fresh->date_requested ?? [];
        $existingDates = is_array($existingDates) ? $existingDates : [];

        $currentTECount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        // ── Step 4: Append new Time Extension ──
        $newTEDays = (int) $request->input('new_te_days', 0);
        $newTECost = $request->input('new_te_cost');
        $newTEDate = $request->input('new_te_date');

        if ($newTEDays > 0) {
            $nextNumber      = $currentTECount + 1;
            $existingDocs[]  = "Time Extension {$nextNumber}";
            $existingDays[]  = $newTEDays;
            $existingCosts[] = ($newTECost !== null && $newTECost !== '') ? (float) $newTECost : null;
            // Insert TE date before VO dates
            $teDates = array_slice($existingDates, 0, $currentTECount);
            $voDates = array_slice($existingDates, $currentTECount);
            $teDates[] = $newTEDate ?: null;
            $existingDates = array_merge($teDates, $voDates);
        }

        // ── Step 4b: Append new Variation Order ──
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

        // ── Step 5: Handle Suspension Order ──
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

        // ── Step 6: Auto-count ──
        $data['time_extension'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Time Extension'))
            ->count();

        $data['variation_order'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Variation Order'))
            ->count();

        // ── Step 7: Recompute revised_contract_expiry and contract_days ──
        $totalTEDays  = (int) array_sum($data['extension_days']);
        $totalVODays  = (int) array_sum(array_map('intval', array_filter((array) ($data['vo_days'] ?? []))));
        $totalSODays  = (int) ($data['suspension_days'] ?? 0);
        $baseExpiry   = Carbon::parse($request->original_contract_expiry);
        $totalExtDays = $totalTEDays + $totalVODays;

        $originalContractDays  = (int) Carbon::parse($request->date_started)
            ->diffInDays(Carbon::parse($request->original_contract_expiry)) + 1;
        $data['contract_days'] = $originalContractDays + $totalTEDays + $totalVODays;

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

        // ── Step 7b: Adjust contract amount based on cost involved ──
        $originalAmount = (float) ($fresh->original_contract_amount ?? $request->contract_amount);

        $totalAdjustment = 0;
        foreach ($data['cost_involved'] as $cost) {
            if ($cost !== null && (float)$cost != 0) {
                $totalAdjustment += (float)$cost;
            }
        }
        foreach (($data['vo_cost'] ?? []) as $cost) {
            if ($cost !== null && (float)$cost != 0) {
                $totalAdjustment += (float)$cost;
            }
        }

        $data['contract_amount'] = max(0, $originalAmount + $totalAdjustment);

        // ── Step 8: LD calculations (all computed server-side) ──
        $ldAccomplished = isset($data['ld_accomplished']) && $data['ld_accomplished'] !== null
            ? (float) $data['ld_accomplished']
            : 0.0;
        $contractAmount = (float) ($data['contract_amount'] ?? 0);
        $daysOverdue    = (int) $request->input('ld_days_overdue', 0);

        // ld_per_day = (ld_unworked / 100) * contract_amount * 0.001
        // i.e. (100 - ld_accomplished) / 100 * contract_amount * 0.001
        $ldUnworked = max(0, 100 - $ldAccomplished);
        $ldPerDay = ($ldUnworked / 100) * $contractAmount * 0.001;

        $data['ld_per_day']      = $ldPerDay > 0 ? round($ldPerDay, 2) : null;
        $data['ld_unworked']     = $ldPerDay > 0 ? round($ldUnworked, 2) : null;
        $data['ld_days_overdue'] = $daysOverdue > 0 ? $daysOverdue : null;
        $data['total_ld']        = ($ldPerDay > 0 && $daysOverdue > 0)
            ? round($ldPerDay * $daysOverdue, 2)
            : null;

        // ── Step 9: Clear LD fields only if ld_accomplished is empty ──
        // LD fields are independent of issuances — do not wipe based on notification list.
        if ($ldAccomplished <= 0 || $daysOverdue <= 0) {
            // Only clear computed fields if inputs are blank; preserve ld_accomplished if set
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
        }

        $project->update($data);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function reports()
    {
        $projects  = Project::orderBy('date_started', 'desc')->get();
        $total     = $projects->count();
        $ongoing   = $projects->where('status', 'ongoing')->count();
        $completed = $projects->where('status', 'completed')->count();
        $expired   = $projects->where('status', 'expired')->count();

        return view('admin.reports.index', compact('projects', 'total', 'ongoing', 'completed', 'expired'));
    }

    public function generateReport()
    {
        $clean = fn(string $s) => iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;

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

        $filterParts = [];
        if (request('search'))            $filterParts[] = 'Search: "' . request('search') . '"';
        if (request('in_charge'))         $filterParts[] = 'In Charge: ' . request('in_charge');
        if ($status && $status !== 'all') $filterParts[] = 'Status: ' . ucfirst($status);
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

        $filename = 'projects-report-' . now()->format('Y-m-d') . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }
    

    public function updateEntry(Request $request, Project $project)
    {
        $request->validate([
            'edit_entry_type'     => 'required|in:te,vo',
            'edit_entry_index'    => 'required|integer|min:0',
            'edit_days'           => 'required|integer|min:1|max:9999',
            'edit_cost'           => 'nullable|numeric',
            'edit_date_requested' => 'nullable|date',
        ]);

        $fresh = $project->fresh();
        $type  = $request->input('edit_entry_type');
        $index = (int) $request->input('edit_entry_index');
        $days  = (int) $request->input('edit_days');
        $cost  = $request->input('edit_cost');
        $date  = $request->input('edit_date_requested');

        $existingDocs  = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $dateRequested = is_array($fresh->date_requested)    ? $fresh->date_requested    : [];

        $teCount = collect($existingDocs)
            ->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))
            ->count();

        $data = [];

        if ($type === 'te') {
            $extensionDays = is_array($fresh->extension_days) ? array_map('intval', $fresh->extension_days) : [];
            $costInvolved  = is_array($fresh->cost_involved)  ? $fresh->cost_involved : [];

            if (!isset($extensionDays[$index])) {
                return back()->with('error', 'Time Extension entry not found.');
            }

            $extensionDays[$index]  = $days;
            $costInvolved[$index]   = ($cost !== null && $cost !== '') ? (float) $cost : null;
            $dateRequested[$index]  = $date ?: null;

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
            $dateRequested[$teCount + $index] = $date ?: null;

            $data['vo_days']        = array_values($voDays);
            $data['vo_cost']        = array_values($voCosts);
            $data['date_requested'] = array_values($dateRequested);
        }

        // Recompute revised_contract_expiry
        $allExtDays = array_sum(array_map('intval', $data['extension_days'] ?? $fresh->extension_days ?? []));
        $allVODays  = array_sum(array_map('intval', $data['vo_days']        ?? $fresh->vo_days        ?? []));
        $sodays     = (int) ($fresh->suspension_days ?? 0);
        $hasSO      = collect($existingDocs)->contains('Suspension Order');
        $total      = $allExtDays + $allVODays + ($hasSO ? $sodays : 0);

        $data['revised_contract_expiry'] = $total > 0
            ? Carbon::parse($fresh->original_contract_expiry)->addDays($total)->toDateString()
            : null;

        // Recompute contract_days
        $previousTEDays        = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $previousVODays        = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays  = (int) ($fresh->contract_days ?? 0) - $previousTEDays - $previousVODays;
        $currentTEDays         = (int) array_sum(array_map('intval', $data['extension_days'] ?? $fresh->extension_days ?? []));
        $currentVODays         = (int) array_sum(array_map('intval', $data['vo_days']        ?? $fresh->vo_days        ?? []));
        $data['contract_days'] = $originalContractDays + $currentTEDays + $currentVODays;
        // Recompute contract amount from original
        $originalAmount = (float) ($fresh->original_contract_amount ?? (float) $fresh->contract_amount);
        $allCosts = array_merge(
            array_values($data['cost_involved'] ?? $fresh->cost_involved ?? []),
            array_values($data['vo_cost']       ?? $fresh->vo_cost       ?? [])
        );
        $deduction = collect($allCosts)->filter(fn($c) => $c !== null && (float)$c < 0)->sum();
        $data['contract_amount'] = max(0, $originalAmount + $deduction);

        $project->update($data);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', ucfirst($type === 'te' ? 'Time Extension' : 'Variation Order') . ' updated successfully.');
    }

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
            array_splice($existingDates,   $teCount + $index, 1);

            $voNum = 1;
            foreach ($existingDocs as &$doc) {
                if (str_starts_with((string) $doc, 'Variation Order')) {
                    $doc = "Variation Order {$voNum}";
                    $voNum++;
                }
            } unset($doc);
        }

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

        $data['time_extension'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Time Extension'))
            ->count();

        $data['variation_order'] = collect($data['documents_pressed'])
            ->filter(fn($v) => str_starts_with($v ?? '', 'Variation Order'))
            ->count();

        $totalTE = (int) array_sum($data['extension_days']);
        $totalVO = (int) array_sum(array_map('intval', array_filter((array) ($data['vo_days'] ?? []))));
        $totalSO = (int) ($fresh->suspension_days ?? 0);
        $hasSO   = collect($data['documents_pressed'])->contains('Suspension Order');
        $total   = $totalTE + $totalVO + ($hasSO ? $totalSO : 0);

        $data['revised_contract_expiry'] = $total > 0
            ? Carbon::parse($fresh->original_contract_expiry)->addDays($total)->toDateString()
            : null;

        $previousTEDays        = (int) array_sum(array_map('intval', $fresh->extension_days ?? []));
        $previousVODays        = (int) array_sum(array_map('intval', array_filter((array) ($fresh->vo_days ?? []))));
        $originalContractDays  = (int) ($fresh->contract_days ?? 0) - $previousTEDays - $previousVODays;
        $data['contract_days'] = $originalContractDays + $totalTE + $totalVO;

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

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}