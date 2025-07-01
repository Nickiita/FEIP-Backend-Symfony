<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\CsvService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CsvServiceTest extends TestCase
{
    private string $dir;
    private string $file;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаём временную директорию для тестов
        $this->dir = sys_get_temp_dir() . '/csvtest_' . uniqid();
        mkdir($this->dir, 0775, true);
        $this->file = 'test.csv';
    }

    protected function tearDown(): void
    {
        // Удаляем файлы и директорию
        $path = "$this->dir/{$this->file}";
        if (file_exists($path)) {
            unlink($path);
        }
        rmdir($this->dir);
        parent::tearDown();
    }

    public function testConstructorThrowsIfDirectoryNotExists(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Directory .* does not exist/');
        new CsvService('/nonexistent/path', ',');
    }

    public function testReadAllReturnsEmptyWhenFileNotExists(): void
    {
        $service = new CsvService($this->dir, ',');
        $this->assertSame([], $service->readAll($this->file));
    }

    public function testReadAllParsesCsvIntoArray(): void
    {
        $content = "a,b,c\n1,2,3\n";
        file_put_contents("{$this->dir}/{$this->file}", $content);

        $service = new CsvService($this->dir, ',');
        $rows = $service->readAll($this->file);

        $this->assertCount(2, $rows);
        $this->assertEquals(['a','b','c'], $rows[0]);
        $this->assertEquals(['1','2','3'], $rows[1]);
    }

    public function testAppendCreatesFileAndAddsRow(): void
    {
        $service = new CsvService($this->dir, ',');
        $service->append($this->file, ['x', 'y', 'z']);

        $lines = file("{$this->dir}/{$this->file}", FILE_IGNORE_NEW_LINES);
        $this->assertCount(1, $lines);
        $this->assertSame('x,y,z', $lines[0]);
    }

    public function testOverwriteRowUpdatesExistingRow(): void
    {
        // Подготовим файл с двумя строками
        file_put_contents("{$this->dir}/{$this->file}", "0,foo,bar\n1,baz,qux\n");

        $service = new CsvService($this->dir, ',');
        $service->overwriteRow($this->file, 1, ['1','UPDATED','ROW']);

        $rows = array_map(fn ($l) => str_getcsv($l, ','), file("{$this->dir}/{$this->file}", FILE_IGNORE_NEW_LINES));
        $this->assertEquals(['0','foo','bar'], $rows[0]);
        $this->assertEquals(['1','UPDATED','ROW'], $rows[1]);
    }

    public function testOverwriteRowThrowsWhenIdNotFound(): void
    {
        file_put_contents("{$this->dir}/{$this->file}", "0,foo\n");
        $service = new CsvService($this->dir, ',');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Row with ID 99 not found');
        $service->overwriteRow($this->file, 99, ['99','nope']);
    }
}
