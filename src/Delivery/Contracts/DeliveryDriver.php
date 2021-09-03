<?php

namespace Worksome\DataExport\Delivery\Contracts;

use Worksome\DataExport\Models\Export;

interface DeliveryDriver
{
    public function deliver(Export $export): void;
}
