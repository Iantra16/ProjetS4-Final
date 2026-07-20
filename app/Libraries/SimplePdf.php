<?php

namespace App\Libraries;

class SimplePdf
{
    private const PAGE_WIDTH = 595;
    private const PAGE_HEIGHT = 842;
    private const LEFT_MARGIN = 50;
    private const TOP_MARGIN = 60;
    private const FONT_SIZE = 11;
    private const LINE_HEIGHT = 16;
    private const MAX_CHARS_PER_LINE = 92;
    private const MAX_LINES_PER_PAGE = 42;

    public function download(string $filename, array $lines): void
    {
        $pdf = $this->render($lines);

        if (function_exists('ob_get_level') && ob_get_level() > 0) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $this->sanitizeFilename($filename) . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    public function render(array $lines): string
    {
        $pages = $this->paginate($lines);
        $objects = [];
        $maxObjectId = 3 + (count($pages) * 2);

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        $pageIds = [];
        $contentIds = [];
        $nextObjectId = 4;

        foreach ($pages as $pageLines) {
            $pageId = $nextObjectId++;
            $contentId = $nextObjectId++;

            $pageIds[] = $pageId;
            $contentIds[] = $contentId;

            $stream = $this->buildContentStream($pageLines);
            $objects[$contentId] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . self::PAGE_WIDTH . ' ' . self::PAGE_HEIGHT . '] /Resources << /Font << /F1 3 0 R >> >> /Contents ' . $contentId . ' 0 R >>';
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', array_map(static fn (int $id): string => $id . ' 0 R', $pageIds)) . '] /Count ' . count($pageIds) . ' >>';

        $output = "%PDF-1.4\n";
        $offsets = [0 => 0];

        for ($i = 1; $i <= $maxObjectId; $i++) {
            if (! isset($objects[$i])) {
                continue;
            }
            $offsets[$i] = strlen($output);
            $output .= $i . " 0 obj\n" . $objects[$i] . "\nendobj\n";
        }

        $xrefOffset = strlen($output);
        $output .= 'xref' . "\n";
        $output .= '0 ' . ($maxObjectId + 1) . "\n";
        $output .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $maxObjectId; $i++) {
            $offset = $offsets[$i] ?? 0;
            $output .= sprintf('%010d 00000 n %s', $offset, "\n");
        }

        $output .= 'trailer << /Size ' . ($maxObjectId + 1) . ' /Root 1 0 R >>' . "\n";
        $output .= 'startxref' . "\n" . $xrefOffset . "\n%%EOF";

        return $output;
    }

    private function paginate(array $lines): array
    {
        $pages = [];
        $currentPage = [];

        foreach ($lines as $line) {
            $wrappedLines = $this->wrapLine((string) $line);
            foreach ($wrappedLines as $wrappedLine) {
                $currentPage[] = $wrappedLine;
                if (count($currentPage) >= self::MAX_LINES_PER_PAGE) {
                    $pages[] = $currentPage;
                    $currentPage = [];
                }
            }
        }

        if ($currentPage !== []) {
            $pages[] = $currentPage;
        }

        return $pages === [] ? [['']] : $pages;
    }

    private function wrapLine(string $line): array
    {
        $wrapped = wordwrap($line, self::MAX_CHARS_PER_LINE, "\n", true);
        return array_map(static fn (string $value): string => $value, explode("\n", $wrapped));
    }

    private function buildContentStream(array $lines): string
    {
        $stream = "BT\n";
        $stream .= "/F1 " . self::FONT_SIZE . " Tf\n";
        $x = self::LEFT_MARGIN;
        $y = self::PAGE_HEIGHT - self::TOP_MARGIN;
        $stream .= sprintf('1 0 0 1 %d %d Tm' . "\n", $x, $y);

        foreach ($lines as $line) {
            $stream .= '(' . $this->escapeText($line) . ") Tj\n";
            $stream .= sprintf('0 -%d Td' . "\n", self::LINE_HEIGHT);
        }

        $stream .= "ET";
        return $stream;
    }

    private function escapeText(string $text): string
    {
        $encoded = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($encoded === false) {
            $encoded = $text;
        }
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encoded);
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $filename) ?? 'document.pdf';
        return $filename === '' ? 'document.pdf' : $filename;
    }
}