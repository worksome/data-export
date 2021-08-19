<?php

namespace Worksome\DataExport\Generator\Contracts;

use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Processor\ProcessorData;

interface Generator {
    public function generate(ProcessorData $processorData): GeneratorFile;
}
