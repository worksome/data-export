<?php

namespace Worksome\DataExport\Services;

use Worksome\DataExport\Enums\ExportStatus;
use Worksome\DataExport\Models\Export;

class UpdateExport
{
    private UpdateExportDTO $dto;

    public function fromDTO(UpdateExportDTO $dto): self
    {
        $this->dto = $dto;

        return $this;
    }

    public function run(): Export
    {
        $generatorFile = $this->dto->getGeneratorFile();

        $export = $this->dto->getExport();
        $export->status = ExportStatus::COMPLETED;
        $export->path = $generatorFile->getPath();
        $export->size = $generatorFile->getSize();
        $export->mime_type = $generatorFile->getMimeType();

        $export->save();

        return $export;
    }
}
