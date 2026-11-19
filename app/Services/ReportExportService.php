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

        $wrappedLines = [];
        foreach ($lines as $line) {
            foreach (explode("\n", wordwrap($line, 96, "\n", true)) as $wrappedLine) {
                $wrappedLines[] = $wrappedLine;
            }
        }

        $pageStreams = [];
        foreach (array_chunk($wrappedLines, 48) as $pageLines) {
            $stream = "BT\n/F1 16 Tf\n40 800 Td\n" . $this->text($pageLines[0]) . " Tj\n/F1 9 Tf\n0 -22 Td\n";
            foreach (array_slice($pageLines, 1) as $line) {
                $stream .= $this->text($line) . " Tj\n0 -15 Td\n";
            }
            $stream .= "ET\n";
            $pageStreams[] = $stream;
        }

        $pdf = $this->buildPdf($pageStreams);

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

    private function buildPdf(array $pageStreams): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        ];
        $pageReferences = [];
        foreach ($pageStreams as $index => $stream) {
            $pageObject = 4 + ($index * 2);
            $contentObject = $pageObject + 1;
            $pageReferences[] = $pageObject . ' 0 R';
            $objects[$pageObject] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents ' . $contentObject . ' 0 R >>';
            $objects[$contentObject] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
        }
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $pageReferences) . '] /Count ' . count($pageStreams) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= $number . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
        return $pdf;
    }
}

