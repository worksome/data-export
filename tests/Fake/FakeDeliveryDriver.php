<?php

namespace Worksome\DataExport\Tests\Fake;

use Worksome\DataExport\Delivery\Contracts\DeliveryDriver;
use Worksome\DataExport\Models\Export;

class FakeDeliveryDriver implements DeliveryDriver
{
    public function deliver(Export $export): void
    {
    }
}
