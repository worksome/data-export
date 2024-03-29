<?php

namespace Worksome\DataExport\GraphQL\Mutations;

use Worksome\DataExport\Enums\ExportResponseStatus;
use Worksome\DataExport\Events\ExportInitialised;
use Worksome\DataExport\GraphQL\Contracts\ExportValidator;
use Worksome\DataExport\Services\CreateExport as CreateExportService;
use Worksome\DataExport\Services\CreateExportDTO;

class CreateExport
{
    public function __construct(
        private CreateExportService $createExportService,
        private ExportValidator $exportValidator,
    ) {
    }

    /**
     * @param null                 $rootValue
     * @param array<string, mixed> $args
     */
    public function __invoke($rootValue, array $args)
    {
        $dto = CreateExportDTO::fromArgs($args['input']);

        $this->exportValidator->validate($dto);

        $export = $this->createExportService->fromDTO($dto)->run();

        event(new ExportInitialised($export));

        return [
            'status' => ExportResponseStatus::SUCCESS,
            'message' => __('Export initialised. You\'ll receive an email when the export is completed.'),
        ];
    }
}
