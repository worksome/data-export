<?php

namespace Worksome\DataExport\GraphQL\Contracts;

use Nuwave\Lighthouse\Exceptions\ValidationException;
use Worksome\DataExport\Services\CreateExportDTO;

interface ExportValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(CreateExportDTO $dto): void;
}
