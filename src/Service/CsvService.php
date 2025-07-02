<?php

declare(strict_types=1);

namespace App\Service;

use RuntimeException;
use Symfony\Component\Filesystem\Path;

class CsvService
{
    private string $filePath;
    private string $delimiter;

    public function __construct(string $filePath, string $delimiter)
    {
        if (!is_dir($filePath)) {
            throw new RuntimeException("Directory $filePath does not exist");
        }
        $this->filePath = rtrim($filePath, '/');
        $this->delimiter = $delimiter;
    }

    private function getFullPath(string $filename): string
    {
        return Path::join($this->filePath, $filename);
    }

    public function readAll(string $filename): array
    {
        $full = $this->getFullPath($filename);
        if (!file_exists($full)) {
            return [];
        }
        $lines = file($full, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_map(fn (string $line) => str_getcsv($line, $this->delimiter), $lines);
    }

    public function append(string $filename, array $row): void
    {
        $full = $this->getFullPath($filename);
        $h = fopen($full, 'a');
        fputcsv($h, $row, $this->delimiter);
        fclose($h);
    }

    public function overwriteRow(string $filename, int $id, array $newRow): void
    {
        $rows = $this->readAll($filename);
        $found = false;
        foreach ($rows as $i => $r) {
            if ((int)$r[0] === $id) {
                $rows[$i] = $newRow;
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new RuntimeException("Row with ID $id not found in $filename");
        }
        $full = $this->getFullPath($filename);
        $h = fopen($full, 'w');
        foreach ($rows as $r) {
            fputcsv($h, $r, $this->delimiter);
        }
        fclose($h);
    }
}
