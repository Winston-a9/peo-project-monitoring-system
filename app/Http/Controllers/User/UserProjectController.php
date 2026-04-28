<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectReportPdf;
use Carbon\Carbon;

class UserProjectController extends Controller
{
    public function index()
    {
        $projects = Project::paginate(10);
        return view('user.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        return view('user.projects.show', compact('project'));
    }

    public function exportPdf(Project $project): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $clean = function (string $s): string {
            if (!extension_loaded('iconv')) {
                return preg_replace('/[^\x20-\x7E]/', '?', $s);
            }
            $result = iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s);
            return ($result !== false && $result !== '') ? $result : preg_replace('/[^\x20-\x7E]/', '?', $s);
        };

        $fresh = $project->fresh();

        $docs = is_array($fresh->documents_pressed) ? $fresh->documents_pressed : [];
        $teDays = is_array($fresh->extension_days) ? array_map('intval', $fresh->extension_days) : [];
        $teCosts = is_array($fresh->cost_involved) ? $fresh->cost_involved : [];
        $dates = is_array($fresh->date_requested) ? $fresh->date_requested : [];
        $voDays = is_array($fresh->vo_days) ? array_map('intval', array_filter((array) $fresh->vo_days)) : [];
        $voCosts = is_array($fresh->vo_cost) ? $fresh->vo_cost : [];
        $issuances = is_array($fresh->issuances) ? array_values(array_filter($fresh->issuances)) : [];
        $billingAmts = is_array($fresh->billing_amounts) ? array_map('floatval', $fresh->billing_amounts) : [];
        $billingDts = is_array($fresh->billing_dates) ? $fresh->billing_dates : [];

        $allCosts = array_merge(
            is_array($fresh->cost_involved ?? null) ? $fresh->cost_involved : [],
            is_array($fresh->vo_cost ?? null) ? $fresh->vo_cost : []
        );
        $totalAdj = collect($allCosts)->filter(fn($c) => $c !== null && (float) $c != 0)->sum();
        $adjusted = max(0, (float) $fresh->original_contract_amount + $totalAdj);
        $hasSO = collect($docs)->contains('Suspension Order');
        $teCount = collect($docs)->filter(fn($d) => str_starts_with((string) $d, 'Time Extension'))->count();

        $pdf = new ProjectReportPdf('P', 'mm', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->setGeneratedAt(now(config('app.timezone'))->format('F d, Y  h:i A'));
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
        if (count($billingAmts) > 0) {
            $pdf->SetFillColor(230, 220, 210);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->Cell(15, 6, '#', 'B', 0, 'C', true);
            $pdf->Cell(100, 6, 'Amount', 'B', 0, 'L', true);
            $pdf->Cell(0, 6, 'Date', 'B', 1, 'L', true);
            $pdf->SetFont('helvetica', '', 8);
            foreach ($billingAmts as $i => $amount) {
                $even = $i % 2 === 0;
                $pdf->SetFillColor($even ? 250 : 255, $even ? 248 : 255, $even ? 245 : 255);
                $date = $billingDts[$i] ?? null;
                $pdf->Cell(15, 6, (string) ($i + 1), 0, 0, 'C', true);
                $pdf->Cell(100, 6, 'PHP ' . number_format((float) $amount, 2), 0, 0, 'L', true);
                $pdf->Cell(0, 6, $date ? Carbon::parse($date, config('app.timezone'))->format('M d, Y') : 'N/A', 0, 1, 'L', true);
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
                    $date = isset($dates[$teIndex]) && $dates[$teIndex] ? Carbon::parse($dates[$teIndex], config('app.timezone'))->format('M d, Y') : 'N/A';
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
                    $date = isset($dates[$teCount + $voIndex]) && $dates[$teCount + $voIndex] ? Carbon::parse($dates[$teCount + $voIndex], config('app.timezone'))->format('M d, Y') : 'N/A';
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

        $filename = 'project-' . str_replace(['/', ' '], '-', $fresh->contract_id) . '-' . now(config('app.timezone'))->format('Y-m-d') . '.pdf';
        return response()->streamDownload(function () use ($pdf, $filename) {
            $pdf->Output($filename, 'D');
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function generateReport(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $clean = function (string $s): string {
            if (!extension_loaded('iconv')) {
                return preg_replace('/[^\x20-\x7E]/', '?', $s);
            }
            $result = iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s);
            return ($result !== false && $result !== '') ? $result : preg_replace('/[^\x20-\x7E]/', '?', $s);
        };

        $query = Project::query();

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
        $expiryThreshold = now(config('app.timezone'))->addDays(30);
        $today = now(config('app.timezone'));

        if ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'active') {
            $query->where('status', 'ongoing')->where(function ($q) use ($expiryThreshold) {
                $q->whereNull('revised_contract_expiry')->where('original_contract_expiry', '>', $expiryThreshold)
                    ->orWhere('revised_contract_expiry', '>', $expiryThreshold);
            });
        } elseif ($status === 'expiring') {
            $query->where('status', '!=', 'completed')->where(function ($q) use ($today, $expiryThreshold) {
                $q->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry', [$today, $expiryThreshold])
                    ->orWhereBetween('revised_contract_expiry', [$today, $expiryThreshold]);
            });
        } elseif ($status === 'expired') {
            $query->where('status', '!=', 'completed')->where(function ($q) use ($today) {
                $q->whereNull('revised_contract_expiry')->where('original_contract_expiry', '<', $today)
                    ->orWhere('revised_contract_expiry', '<', $today);
            });
        } elseif ($status === 'ongoing') {
            $query->where('status', 'ongoing')->where(function ($q) use ($today) {
                $q->whereNull('revised_contract_expiry')->where('original_contract_expiry', '>=', $today)
                    ->orWhere('revised_contract_expiry', '>=', $today);
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
        $filterLabel = count($filterParts) ? implode('  |  ', $filterParts) : 'All Projects';

        $pdf = new ProjectReportPdf('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->setGeneratedAt(now(config('app.timezone'))->format('F d, Y  h:i A'));
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

        $filename = 'projects-report-' . now(config('app.timezone'))->format('Y-m-d') . '.pdf';
        return response()->streamDownload(function () use ($pdf, $filename) {
            $pdf->Output($filename, 'D');
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function reports(): \Illuminate\View\View
    {
        $projects  = Project::orderBy('date_started', 'desc')->get();
        $total     = $projects->count();
        $ongoing   = $projects->where('status', 'ongoing')->count();
        $completed = $projects->where('status', 'completed')->count();
        $expired   = $projects->where('status', 'expired')->count();

        return view('user.reports.index', compact('projects', 'total', 'ongoing', 'completed', 'expired'));
    }
}