<?php

namespace Worksome\DataExport\Services;

use Worksome\DataExport\Models\Export;
use JetBrains\PhpStorm\Pure;
use Worksome\DataExport\Generator\GeneratorFile;

class UpdateExportDTO
{
    #[Pure]
    public function __construct(
        private Export $export,
        private GeneratorFile $generatorFile,
    )
    {}

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
