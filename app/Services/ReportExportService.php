<?php

declare(strict_types=1);

namespace App\Services;

final class ReportExportService
{
    public function csv(array $rows): void
    {
        $filename = 'rotaflow-relatorio-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'wb');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['Código', 'Data', 'Origem', 'Destino', 'Transportador', 'Produtos', 'Unidades', 'Valor dos produtos (R$)'], ';');
        foreach ($rows as $row) {
            fputcsv($output, [
                'FR-' . $row['id'],
                date_br($row['data_frete']),
                $row['origem_cidade'] . '/' . $row['origem_estado'],
                $row['destino_cidade'] . '/' . $row['destino_estado'],
                $row['transportador_nome'],
                $row['tipos_produto'] . ' tipo(s)',
                $row['quantidade_total'],
                number_format((float) $row['valor_total'], 2, ',', '.'),
            ], ';');
        }
        fclose($output);
    }

    public function pdf(array $rows, array $totals): void
    {
        $lines = [
            'ROTAFLOW - RELATORIO DE FRETES',
            'Gerado em ' . date('d/m/Y H:i'),
            'Fretes: ' . $totals['freights'] . ' | Unidades: ' . $totals['quantity'] . ' | Valor: ' . money($totals['value']),
            str_repeat('-', 96),
        ];
        foreach ($rows as $row) {
            $lines[] = sprintf(
                'FR-%d | %s | %s -> %s | %s | %s un. | %s',
                $row['id'],
                date_br($row['data_frete']),
                $row['origem_cidade'] . '/' . $row['origem_estado'],
                $row['destino_cidade'] . '/' . $row['destino_estado'],
                $row['transportador_nome'],
                $row['quantidade_total'],
                money($row['valor_total'])
            );
        }
        if (count($lines) === 4) {
            $lines[] = 'Nenhum frete encontrado para os filtros informados.';
        }

        $stream = "BT\n/F1 16 Tf\n40 800 Td\n" . $this->text($lines[0]) . " Tj\n/F1 9 Tf\n0 -22 Td\n";
        foreach (array_slice($lines, 1) as $line) {
            $stream .= $this->text($line) . " Tj\n0 -15 Td\n";
        }
        $stream .= "ET\n";
        $pdf = $this->buildPdf($stream);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="rotaflow-relatorio-' . date('Y-m-d') . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
    }

    private function text(string $value): string
    {
        $value = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $value) ?: $value;
        return '(' . str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value) . ')';
    }

    private function buildPdf(string $stream): string
    {
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "endstream",
        ];
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $number => $object) {
            $offsets[$number + 1] = strlen($pdf);
            $pdf .= ($number + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf('%010d 00000 n \n', $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
        return $pdf;
    }
}

