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
            'totals' => $this->freights->totalsFromRows($rows),
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
        $rows = $this->freights->all($from, $to);
        (new ReportExportService())->pdf($rows, $this->freights->totalsFromRows($rows));
    }

    private function filters(): array
    {
        $fromInput = trim((string) ($_GET['from'] ?? ''));
        $toInput = trim((string) ($_GET['to'] ?? ''));
        $from = $this->parseDate($fromInput);
        $to = $this->parseDate($toInput);

        if ($fromInput !== '' && $from === null) {
            flash('error', 'Informe uma data inicial válida.');
            redirect('reports');
        }
        if ($toInput !== '' && $to === null) {
            flash('error', 'Informe uma data final válida.');
            redirect('reports');
        }
        if (!$from && $to) {
            $from = $to;
        }
        if ($from && !$to) {
            $to = $from;
        }
        if ($from && $to && $from > $to) {
            flash('error', 'A data inicial não pode ser posterior à data final.');
            redirect('reports');
        }
        return [$from, $to];
    }

    private function parseDate(string $value): ?string
    {
        if ($value === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return null;
        }

        return $date->format('Y-m-d') === $value ? $value : null;
    }
}

