<?php

namespace Worksome\DataExport\GraphQL\Mutations;

use Worksome\DataExport\Enums\ExportResponseStatus;
use Worksome\DataExport\Events\ExportInitialised;
use Worksome\DataExport\Export\CreateExport as CreateExportService;
use Worksome\DataExport\Export\CreateExportDTO;

class CreateExport
{
    public function __construct(
        public CreateExportService $createExportService
    ) {}

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $dto = CreateExportDTO::fromArgs($args['input']);
        $export = $this->createExportService->fromDTO($dto)->run();

        event(new ExportInitialised($export));

        return [
            'status' => ExportResponseStatus::SUCCESS,
            'message' => __('Export initialised. You\'ll receive an email when the export is completed.'),
        ];
    }
}
