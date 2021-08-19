<?php

namespace Worksome\DataExport\Processor\Contracts;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Processor\ProcessorData;

interface Processor {
    public function process(Export $export): ProcessorData;
}
