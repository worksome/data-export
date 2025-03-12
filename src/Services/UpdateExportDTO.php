<?php

namespace Worksome\DataExport\Services;

use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Models\Export;

class UpdateExportDTO
{
    public function __construct(
        private Export $export,
        private GeneratorFile $generatorFile,
    ) {
    }

    public static function fromGeneratorFile(GeneratorFile $generatorFile, Export $export): self
    {
        return new self(
            export: $export,
            generatorFile: $generatorFile,
        );
    }

    public function getExport(): Export
    {
        return $this->export;
    }

    public function getGeneratorFile(): GeneratorFile
    {
        return $this->generatorFile;
    }
}
