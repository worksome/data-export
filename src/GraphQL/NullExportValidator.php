<?php

namespace Worksome\DataExport\GraphQL;

use Worksome\DataExport\GraphQL\Contracts\ExportValidator;
use Worksome\DataExport\Services\CreateExportDTO;

class NullExportValidator implements ExportValidator
{
    public function validate(CreateExportDTO $dto): void
    {
    }
}
