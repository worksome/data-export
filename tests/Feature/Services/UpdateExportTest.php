<?php

namespace Worksome\DataExport\Tests\Feature\Services;

use Worksome\DataExport\Enums\ExportStatus;
use Worksome\DataExport\Services\UpdateExport;
use Worksome\DataExport\Services\UpdateExportDTO;
use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Tests\Factories\ExportFactory;

it('can update an export', function () {
    $generatorFile = new GeneratorFile(
        path: '/',
        size: 1,
        url: 'https://worksome.eu',
        count: 1,
        mimeType: 'application/text',
    );

    $export = ExportFactory::new()->create();

    $dto = new UpdateExportDTO($export, $generatorFile);

    $service = new UpdateExport();
    $service->fromDTO($dto);

    $result = $service->run();

    expect($result->status)->toBe(ExportStatus::COMPLETED);
    expect($result->path)->not->toBeNull();
    expect($result->size)->not->toBeNull();
    expect($result->mime_type)->not->toBeNull();
});
