<?php

namespace Worksome\DataExport\Tests\Feature\Generator;

use Illuminate\Support\Facades\Storage;
use Worksome\DataExport\Generator\CsvDriver;
use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Processor\ProcessorData;

it('can export data to csv format', function () {
    $data = [
        ['name' => 'John Doe'],
        ['name' => 'Jane Doe'],
    ];

    $processorData = new ProcessorData($data, 'people');

    $csvDriver = new CsvDriver();

    expect($csvDriver->exportToCsv($processorData))->toBe("name\r\nJohn Doe\r\nJane Doe\r\n");
});

it('can save csv on storage', function () {
    Storage::fake();

    $data = [
        ['name' => 'John Doe'],
        ['name' => 'Jane Doe'],
    ];

    $processorData = new ProcessorData($data, 'people');

    $csvDriver = new CsvDriver();

    $savedFile = $csvDriver->saveToStorage('foo', $csvDriver->toStream($processorData), $processorData);

    Storage::assertExists($savedFile->getPath());

    expect($savedFile)->toBeInstanceOf(GeneratorFile::class);
});

it('can fully generate the csv file', function () {
    Storage::fake('default');

    $data = [
        ['name' => 'John Doe'],
        ['name' => 'Jane Doe'],
    ];

    $processorData = new ProcessorData($data, 'people');

    $csvDriver = new CsvDriver();

    $savedFile = $csvDriver->generate($processorData);

    Storage::assertExists($savedFile->getPath());

    expect($savedFile)->toBeInstanceOf(GeneratorFile::class);
});

it('writes values exactly as given', function () {
    // A spreadsheet writer turns these into numbers and loses the formatting, and
    // reads a leading = as a formula. A CSV holds text, so nothing is inferred.
    $data = [[
        'decimal' => '8.00',
        'money' => '1234.50',
        'small' => '0.10',
        'formula' => '=SUM(A1)',
        'signed' => '+4512',
        'exponent' => '1e3',
        'zeros' => '0000123',
    ]];

    $csv = (new CsvDriver())->exportToCsv(new ProcessorData($data, 'values'));

    $lines = explode("\r\n", trim($csv));
    $header = str_getcsv($lines[0], ',', '"', '');
    $values = str_getcsv($lines[1], ',', '"', '');

    expect(array_combine($header, $values))->toBe([
        'decimal' => '8.00',
        'money' => '1234.50',
        'small' => '0.10',
        'formula' => '=SUM(A1)',
        'signed' => '+4512',
        'exponent' => '1e3',
        'zeros' => '0000123',
    ]);
});

it('keeps every row aligned to the header when the keys differ', function () {
    $data = [
        ['a' => 'a1', 'b' => 'b1'],
        ['b' => 'b2', 'a' => 'a2'],
        ['a' => 'a3'],
        ['a' => 'a4', 'b' => 'b4', 'c' => 'c4'],
    ];

    $csv = (new CsvDriver())->exportToCsv(new ProcessorData($data, 'shapes'));

    expect($csv)->toBe("a,b,c\r\na1,b1,\r\na2,b2,\r\na3,,\r\na4,b4,c4\r\n");
});

it('quotes only the values that need it', function () {
    $data = [[
        'plain' => 'no quotes needed',
        'comma' => 'a,b',
        'quote' => 'say "hi"',
        'newline' => "line1\nline2",
        'backslash' => 'a\b',
    ]];

    $csv = (new CsvDriver())->exportToCsv(new ProcessorData($data, 'quoting'));

    expect($csv)
        ->toContain('no quotes needed')
        ->toContain('"a,b"')
        ->toContain('"say ""hi"""')
        ->toContain("\"line1\nline2\"")
        ->toContain('a\b');
});

it('returns an empty string when there is nothing to export', function () {
    expect((new CsvDriver())->exportToCsv(new ProcessorData([], 'empty')))->toBe('');
});

it('writes a row stream without loading it, with the header from its columns', function () {
    $rows = new \Worksome\DataExport\Processor\RowStream();
    $rows->push(['id' => '1', 'name' => 'One'], [['UK' => 'applies']]);
    $rows->push(['id' => '2', 'name' => 'Two'], []);

    $csv = (new CsvDriver())->exportToCsv(new ProcessorData($rows, 'people'));

    expect($csv)->toBe("id,name,UK\r\n1,One,applies\r\n2,Two,\r\n");
});
