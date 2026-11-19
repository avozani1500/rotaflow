<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\FreightRepository;
use App\Services\ReportExportService;

final class ReportController extends Controller
{
    private FreightRepository $freights;

    public function __construct()
    {
        $this->freights = new FreightRepository(db());
    }

    public function index(): void
    {
        [$from, $to] = $this->filters();
        $rows = $this->freights->all($from, $to);
        render('reports/index', [
            'rows' => $rows,
            'totals' => $this->freights->totals($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function csv(): void
    {
        [$from, $to] = $this->filters();
        (new ReportExportService())->csv($this->freights->all($from, $to));
    }

    public function pdf(): void
    {
        [$from, $to] = $this->filters();
        (new ReportExportService())->pdf($this->freights->all($from, $to), $this->freights->totals($from, $to));
    }

    private function filters(): array
    {
        $from = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['from'] ?? '')) ? (string) $_GET['from'] : null;
        $to = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['to'] ?? '')) ? (string) $_GET['to'] : null;
        if (!$from && $to) {
            $from = $to;
        }
        if ($from && !$to) {
            $to = $from;
        }
        return [$from, $to];
    }
}

