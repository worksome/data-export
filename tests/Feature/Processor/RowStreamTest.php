<?php

namespace Worksome\DataExport\Tests\Feature\Processor;

use Worksome\DataExport\Processor\RowStream;

it('fills every row out to the final column list, fixed columns first', function () {
    $rows = new RowStream();
    $rows->push(['id' => '1', 'name' => 'One'], [['UK' => 'applies']]);
    $rows->push(['id' => '2', 'name' => 'Two'], [['US' => 'none'], ['UK' => 'exempt']]);
    $rows->push(['id' => '3', 'name' => 'Three'], []);

    expect($rows->columns())->toBe(['id', 'name', 'UK', 'US'])
        ->and(count($rows))->toBe(3)
        ->and(iterator_to_array($rows, false))->toBe([
            ['id' => '1', 'name' => 'One', 'UK' => 'applies', 'US' => ''],
            ['id' => '2', 'name' => 'Two', 'UK' => 'exempt', 'US' => 'none'],
            ['id' => '3', 'name' => 'Three', 'UK' => '', 'US' => ''],
        ]);
});

it('lets an optional value take precedence over a fixed column of the same name', function () {
    $rows = new RowStream();
    $rows->push(['id' => '1', 'status' => 'fixed'], [['status' => 'optional']]);
    $rows->push(['id' => '2', 'status' => 'fixed'], []);

    // The column keeps its fixed position, and a row without the optional value is blank
    expect($rows->columns())->toBe(['id', 'status'])
        ->and(iterator_to_array($rows, false))->toBe([
            ['id' => '1', 'status' => 'optional'],
            ['id' => '2', 'status' => ''],
        ]);
});

it('can be read more than once', function () {
    $rows = new RowStream();
    $rows->push(['a' => '1'], []);

    expect(iterator_to_array($rows, false))->toBe([['a' => '1']])
        ->and(iterator_to_array($rows, false))->toBe([['a' => '1']]);
});

it('yields nothing and no columns when empty', function () {
    $rows = new RowStream();

    expect($rows->columns())->toBe([])
        ->and(count($rows))->toBe(0)
        ->and(iterator_to_array($rows, false))->toBe([]);
});

it('does not hold the rows in memory', function () {
    $rows = new RowStream();
    $row = array_fill_keys(array_map(fn ($i) => "column_$i", range(1, 20)), str_repeat('x', 40));

    $before = memory_get_usage(true);
    for ($i = 0; $i < 50000; $i++) {
        $rows->push($row, [['extra' => 'y']]);
    }
    $count = 0;
    foreach ($rows as $filled) {
        $count++;
    }
    $grown = (memory_get_peak_usage(true) - $before) / 1048576;

    // 50k rows of ~1 KB each would be ~50 MB if kept in memory. The buffer spills
    // to disk past 8 MB, so growth stays a small constant however many rows there are.
    expect($count)->toBe(50000)
        ->and($grown)->toBeLessThan(24);
});
