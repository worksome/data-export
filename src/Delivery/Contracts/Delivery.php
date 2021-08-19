<?php

namespace Worksome\DataExport\Delivery\Contracts;

use Worksome\DataExport\Models\Export;

interface Delivery {
    public function deliver(Export $export): void;
}
