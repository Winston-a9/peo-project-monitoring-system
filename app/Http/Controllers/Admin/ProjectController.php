<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectLog;
use App\Models\TimeExtension;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    // sort newest first
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
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
    
}