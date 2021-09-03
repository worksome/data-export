<?php

namespace Worksome\DataExport\Tests\Feature\Services;

use Worksome\DataExport\Services\UpdateExportDTO;
use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Models\Export;

it('can initialize the dto from the constructor', function () {
    $generatorFile = new GeneratorFile(
        path: '/',
        size: 1,
        url: 'https://worksome.eu',
        count: 1,
        mimeType: 'application/text',
    );

    $export = new Export();

    $dto = new UpdateExportDTO($export, $generatorFile);

    expect($dto->getExport())->toBe($export);
    expect($dto->getGeneratorFile())->toBe($generatorFile);
});

it('can initialize the dto from the from generator file method', function () {
    $generatorFile = new GeneratorFile(
        path: '/',
        size: 1,
        url: 'https://worksome.eu',
        count: 1,
        mimeType: 'application/text',
    );

    $export = new Export();

    $dto = UpdateExportDTO::fromGeneratorFile($generatorFile, $export);

    expect($dto->getExport())->toBe($export);
    expect($dto->getGeneratorFile())->toBe($generatorFile);
});
