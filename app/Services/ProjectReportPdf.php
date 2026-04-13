<?php

namespace App\Services;

use FPDF;


class ProjectReportPdf extends FPDF
{
    private $reportTitle = 'PEO Project Monitoring Report';
    private $generatedAt = '';
    private $filterLabel = 'All Projects';

    public function setGeneratedAt(string $date): void
    {
        $this->generatedAt = $date;
    }

    public function setFilterLabel(string $label): void
    {
        $this->filterLabel = $label;
    }
    

    // ── Page header ──────────────────────────────────────────
    public function Header()
{
    // Orange accent bar at top
    $this->SetFillColor(249, 115, 22);
    $this->Rect(0, 0, 297, 3, 'F');

    // Title
    $this->SetXY(10, 8);
    $this->SetFont('Helvetica', 'B', 16);
    $this->SetTextColor(26, 15, 0);
    $this->Cell(180, 8, $this->reportTitle, 0, 0, 'L');

    // Generated date (same line, right aligned)
    $this->SetFont('Helvetica', '', 8);
    $this->SetTextColor(156, 163, 175);
    $this->Cell(0, 8, 'Generated: ' . $this->generatedAt, 0, 1, 'R');

    // Subtitle on next line
    $this->SetX(10);
    $this->SetFont('Helvetica', '', 8);
    $this->SetTextColor(107, 79, 53);
    $this->Cell(0, 5, 'Filter: ' . $this->filterLabel, 0, 1, 'L');

    // Divider
    $this->SetDrawColor(249, 115, 22);
    $this->SetLineWidth(0.3);
    $this->Line(10, 24, 287, 24);
    $this->Ln(5);
}

    // ── Page footer ──────────────────────────────────────────
    public function Footer()
    {
        $this->SetY(-12);
        $this->SetDrawColor(249, 115, 22);
        $this->SetLineWidth(0.2);
        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(1);
        $this->SetFont('Helvetica', '', 7.5);
        $this->SetTextColor(156, 163, 175);
        $this->Cell(0, 5, 'Project Monitoring System  -  Confidential', 0, 0, 'L');
        $this->Cell(0, 5, 'Page ' . $this->PageNo(), 0, 0, 'R');
    }

    // ── Summary cards row ────────────────────────────────────
    public function SummaryCards(int $total, int $ongoing, int $completed, int $expired): void
    {
        $cards = [
            [$total,     'TOTAL PROJECTS', [255, 247, 237], [249, 115, 22]],
            [$ongoing,   'ONGOING',        [239, 246, 255], [37,  99,  235]],
            [$completed, 'COMPLETED',      [240, 253, 244], [22,  163, 74]],
            [$expired,   'EXPIRED',        [254, 242, 242], [220, 38,  38]],
        ];

        $startX = 10;
        $cardW  = 66;
        $cardH  = 20;
        $gap    = 2.5;
        $startY = $this->GetY();

        foreach ($cards as $i => [$val, $label, $bg, $accent]) {
            $x = $startX + $i * ($cardW + $gap);

            $this->SetFillColor($bg[0], $bg[1], $bg[2]);
            $this->SetDrawColor(220, 200, 180);
            $this->SetLineWidth(0.2);
            $this->RoundedRect($x, $startY, $cardW, $cardH, 2, 'DF');

            $this->SetFillColor($accent[0], $accent[1], $accent[2]);
            $this->Rect($x, $startY, 2.5, $cardH, 'F');

            $this->SetFont('Helvetica', 'B', 20);
            $this->SetTextColor($accent[0], $accent[1], $accent[2]);
            $this->SetXY($x + 6, $startY + 2);
            $this->Cell($cardW - 8, 10, (string)$val, 0, 0, 'L');

            $this->SetFont('Helvetica', 'B', 6.5);
            $this->SetTextColor(107, 79, 53);
            $this->SetXY($x + 6, $startY + 13);
            $this->Cell($cardW - 8, 5, $label, 0, 0, 'L');
        }

        $this->SetY($startY + $cardH + 6);
    }

    // ── Table header row ─────────────────────────────────────
    public function TableHeader(array $cols): void
    {
        $this->SetFillColor(249, 115, 22);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 7);
        $this->SetLineWidth(0);

        foreach ($cols as [$label, $width, $align]) {
            $this->Cell($width, 7, strtoupper($label), 0, 0, $align, true);
        }
        $this->Ln();
    }

    // ── Table row ────────────────────────────────────────────
    public function TableRow(array $cells, bool $even): void
    {
        if ($even) $this->SetFillColor(255, 250, 245);
        else        $this->SetFillColor(255, 255, 255);

        $this->SetTextColor(58, 26, 0);
        $this->SetDrawColor(240, 210, 185);
        $this->SetLineWidth(0.1);

        foreach ($cells as [$text, $width, $align, $bold]) {
            $this->SetFont('Helvetica', $bold ? 'B' : '', 7.5);
            $this->Cell($width, 7, $text, 'B', 0, $align, true);
        }
        $this->Ln();
    }

    // ── Project row ──────────────────────────────────────────
    public function ProjectRow(
    int $num,
    string $title, string $inCharge, string $location,
    string $contractor, string $amount,
    string $started, string $expiry,
    string $slipStr, array $slipColor,
    string $statusLabel, array $statusBg, array $statusFg,
    bool $even): void {
    $rowH = 7;

    if ($even) $this->SetFillColor(255, 250, 245);
    else        $this->SetFillColor(255, 255, 255);

    $this->SetDrawColor(240, 210, 185);
    $this->SetLineWidth(0.1);

    $widths = [8, 55, 32, 30, 32, 30, 25, 25, 20, 20];
    $aligns = ['C','L','L','L','L','R','C','C','C','C'];
    $texts  = [$num, $title, $inCharge, $location, $contractor, $amount, $started, $expiry, '', ''];

    $rowY = $this->GetY();

    // Draw all cells
    foreach ($texts as $j => $text) {
        $this->SetFont('Helvetica', $j === 1 ? 'B' : '', 7.5);
        $this->SetTextColor(58, 26, 0);
        $this->Cell($widths[$j], $rowH, (string)$text, 'B', 0, $aligns[$j], true);
    }
    $this->Ln();

    // Slippage — columns 0-7 summed for X position
    $slipX = 10;
    foreach (array_slice($widths, 0, 8) as $w) $slipX += $w;

    $this->SetFont('Helvetica', 'B', 7.5);
    $this->SetTextColor($slipColor[0], $slipColor[1], $slipColor[2]);
    $this->SetXY($slipX, $rowY);
    $this->Cell(20, $rowH, $slipStr, 0, 0, 'C');

    // Status badge
    $statusX = $slipX + 20;
    $this->SetFillColor($statusBg[0], $statusBg[1], $statusBg[2]);
    $this->SetTextColor($statusFg[0], $statusFg[1], $statusFg[2]);
    $this->SetFont('Helvetica', 'B', 6.5);
    $this->SetXY($statusX + 1, $rowY + 1);
    $this->Cell(18, $rowH - 2, $statusLabel, 0, 0, 'C', true);

    // Reset cursor
    $this->SetXY(10, $rowY + $rowH);
    $this->SetTextColor(58, 26, 0);
}

    // ── Status badge helper ──────────────────────────────────
    public function StatusBadge(string $status, float $x, float $y): void
    {
        $map = [
            'completed' => [[240,253,244], [22,163,74],  'Completed'],
            'expired'   => [[254,242,242], [220,38,38],  'Expired'  ],
            'ongoing'   => [[239,246,255], [37,99,235],  'Ongoing'  ],
        ];

        [$bg, $fg, $label] = $map[$status] ?? $map['ongoing'];

        $this->SetFillColor($bg[0], $bg[1], $bg[2]);
        $this->SetTextColor($fg[0], $fg[1], $fg[2]);
        $this->SetFont('Helvetica', 'B', 6.5);
        $this->SetXY($x + 1, $y + 1);
        $this->Cell(21, 5, $label, 0, 0, 'C', true);
    }

    // ── Rounded rectangle helper ─────────────────────────────
    public function RoundedRect(float $x, float $y, float $w, float $h, float $r, string $style = ''): void
    {
        $k  = $this->k;
        $hp = $this->h;
        $style = $style === 'F' ? 'f' : ($style === 'DF' || $style === 'FD' ? 'B' : 'S');

        $this->_out(sprintf(
            'q %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %s Q',
            ($x+$r)*$k,    ($hp-$y)*$k,
            ($x+$r)*$k,    ($hp-$y)*$k,
            $x*$k,         ($hp-$y)*$k,
            $x*$k,         ($hp-($y+$r))*$k,
            $x*$k,         ($hp-($y+$h-$r))*$k,
            $x*$k,         ($hp-($y+$h))*$k,
            ($x+$r)*$k,    ($hp-($y+$h))*$k,
            ($x+$w-$r)*$k, ($hp-($y+$h))*$k,
            ($x+$w)*$k,    ($hp-($y+$h))*$k,
            ($x+$w)*$k,    ($hp-($y+$h-$r))*$k,
            ($x+$w)*$k,    ($hp-($y+$r))*$k,
            ($x+$w)*$k,    ($hp-$y)*$k,
            ($x+$w-$r)*$k, ($hp-$y)*$k,
            ($x+$r)*$k,    ($hp-$y)*$k,
            $style
        ));
    }
}