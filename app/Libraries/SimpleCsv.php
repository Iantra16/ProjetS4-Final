<?php
namespace App\Libraries;

class SimpleCsv
{
    public function download(string $filename, array $headers, array $rows, string $separator = ';'): void
    {
        if (function_exists('ob_get_level')) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $this->sanitizeFilename($filename) . '"');

        $output = fopen('php://output', 'w');

        // BOM UTF-8 pour que les accents s'affichent bien dans Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, $headers, $separator);

        foreach ($rows as $row) {
            fputcsv($output, $row, $separator);
        }

        fclose($output);
        exit;
    }

    private function sanitizeFilename(string $filename): string
    {
        return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);
    }
}