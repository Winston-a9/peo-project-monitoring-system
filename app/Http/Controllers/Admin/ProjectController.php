<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectLog;
use App\Models\TimeExtension;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ProjectReportPdf;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('admin.projects.index', compact('projects'));
         $perPage = in_array((int)request('per_page', 10), [10, 25, 50]) ? (int)request('per_page', 10) : 10;
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
        'date_started'             => 'required|date',
        'original_contract_expiry' => 'required|date',
        'revised_contract_expiry'  => 'nullable|date',
        'as_planned'               => 'required|numeric|min:0|max:100',
        'work_done'                => 'required|numeric|min:0|max:100',
        'contract_amount'          => 'required|numeric|min:0',
        'status'                   => 'required|in:ongoing,completed,expired',
        'completed_at'             => 'nullable|date',
    ]);

    $data = $request->only([
    'in_charge', 'project_title', 'location', 'contractor',
    'date_started', 'original_contract_expiry',
    'as_planned', 'work_done',
    'status', 'completed_at', 'contract_amount',
        ]);
        $data['slippage'] = $request->work_done - $request->as_planned;
        // clear completed_at if ongoing
        if ($request->status === 'ongoing') {
            $data['completed_at'] = null;
        }

    $data['slippage'] = $request->work_done - $request->as_planned;
    

    Project::create($data);

    return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
}

    public function show(Project $project)
    {
        $project->load(['logs.user' => fn($q) => $q->select('id','name')]);
        $project->logs = $project->logs->sortByDesc('created_at');
    return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
    return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
{
    $request->validate([
        'in_charge'                => 'required|string|max:255',
        'project_title'            => 'required|string|max:255',
        'location'                 => 'required|string|max:255',
        'contractor'               => 'required|string|max:255',
        'date_started'             => 'required|date',
        'original_contract_expiry' => 'required|date',
        'status'                   => 'required|in:ongoing,completed,expired',
        'contract_amount'          => 'required|numeric|min:0',
        'as_planned'               => 'required|numeric|min:0|max:100',
        'work_done'                => 'required|numeric|min:0|max:100',
        'remarks_recommendation'   => 'nullable|string',
        'revised_contract_expiry'  => 'nullable|date',
        'completed_at'             => 'nullable|date',
        'issuances'                => 'nullable|array',
        'issuances.*'              => 'nullable|string|in:1st Notice of Negative Slippage,2nd Notice of Negative Slippage,3rd Notice of Negative Slippage,Liquidated Damages,Notice to Terminate,Notice of Expiry',
        'documents_pressed'        => 'nullable|array',
        'documents_pressed.*'      => 'nullable|string|in:Time Extension 1,Time Extension 2,Time Extension 3,Time Extension 4,Time Extension 5,Variation Order 1,Variation Order 2,Suspension Order',
        'time_extension'           => 'nullable|integer|min:0',
    ]);

    $data = $request->only([
        'in_charge', 'project_title', 'location', 'contractor',
        'date_started', 'original_contract_expiry', 'revised_contract_expiry',
        'as_planned', 'work_done',
        'remarks_recommendation',
        'status', 'completed_at', 'contract_amount',
    ]);

    $data['slippage'] = $request->work_done - $request->as_planned;

    if ($request->status === 'ongoing') {
        $data['completed_at'] = null;
    }

    // Filter out empty selections, then store as JSON
    $data['issuances']         = array_values(array_filter($request->input('issuances', []), fn($v) => !empty($v)));
    $data['documents_pressed'] = array_values(array_filter($request->input('documents_pressed', []), fn($v) => !empty($v)));
    $data['time_extension']    = $request->input('time_extension', 0); 
    $data['time_extension'] = collect($data['documents_pressed'])
    ->filter(fn($v) => str_starts_with($v, 'Time Extension'))
    ->map(fn($v) => (int) str_replace('Time Extension ', '', $v))
    ->max() ?? 0;
    $data['extension_days'] = array_values(
    array_filter($request->input('extension_days', []), fn($v) => $v !== null)
    );

    // Recompute revised_contract_expiry from ALL Time Extension days combined
    $documents = $request->input('documents_pressed', []);
    $days      = $request->input('extension_days', []);
    $totalDays = 0;

    foreach ($documents as $i => $doc) {
        if (str_starts_with($doc ?? '', 'Time Extension')) {
            $totalDays += (int) ($days[$i] ?? 0);
        }
    }

    if ($totalDays > 0) {
        $data['revised_contract_expiry'] = \Carbon\Carbon::parse($request->original_contract_expiry)
            ->addDays($totalDays)
            ->toDateString();
    } else {
        $data['revised_contract_expiry'] = null;
    }

    $project->update($data);

    return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
}
public function reports()
{
    $projects = Project::orderBy('date_started', 'desc')->get();

    $total     = $projects->count();
    $ongoing   = $projects->where('status', 'ongoing')->count();
    $completed = $projects->where('status', 'completed')->count();
    $expired   = $projects->where('status', 'expired')->count();

    return view('admin.reports.index', compact('projects', 'total', 'ongoing', 'completed', 'expired'));
}

public function generateReport()
{
    $clean = fn(string $s) => iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;

    // ── Filtered query (matches index blade logic) ──
    $query = \App\Models\Project::query();

    if (request('search')) {
        $search = request('search');
        $query->where(function($q) use ($search) {
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
              ->where(function($q) {
                  $q->whereNull('revised_contract_expiry')
                    ->where('original_contract_expiry', '>', now()->addDays(30))
                    ->orWhere('revised_contract_expiry', '>', now()->addDays(30));
              });
    } elseif ($status === 'expiring') {
        $query->where('status', '!=', 'completed')
              ->where(function($q) {
                  $q->whereNull('revised_contract_expiry')
                    ->whereBetween('original_contract_expiry', [now(), now()->addDays(30)])
                    ->orWhereBetween('revised_contract_expiry', [now(), now()->addDays(30)]);
              });
    } elseif ($status === 'expired') {
        $query->where('status', '!=', 'completed')
              ->where(function($q) {
                  $q->whereNull('revised_contract_expiry')
                    ->where('original_contract_expiry', '<', now())
                    ->orWhere('revised_contract_expiry', '<', now());
              });
    } elseif ($status === 'ongoing') {
        $query->where('status', 'ongoing')
              ->where(function($q) {
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

    // ── Filter label for PDF subtitle ──
    $filterParts = [];
    if (request('search'))           $filterParts[] = 'Search: "' . request('search') . '"';
    if (request('in_charge'))        $filterParts[] = 'In Charge: ' . request('in_charge');
    if ($status && $status !== 'all') $filterParts[] = 'Status: ' . ucfirst($status);
    $filterLabel = count($filterParts) ? implode('  |  ', $filterParts) : 'All Projects';

    // ── Build PDF ──
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
        $slip    = (float)($project->slippage ?? 0);
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
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
    
}