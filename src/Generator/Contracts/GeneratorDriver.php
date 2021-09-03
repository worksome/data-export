<?php

namespace Worksome\DataExport\Generator\Contracts;

use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Processor\ProcessorData;

interface GeneratorDriver
{
    public function generate(ProcessorData $processorData): GeneratorFile;
}
