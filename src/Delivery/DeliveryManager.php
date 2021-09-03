<?php

namespace Worksome\DataExport\Delivery;

use Illuminate\Support\Manager;
use Worksome\DataExport\Delivery\Contracts\DeliveryDriver as DeliveryDriverContract;

class DeliveryManager extends Manager
{
    public function createEmailDriver(): DeliveryDriverContract
    {
        return new EmailDriver();
    }

    public function getDefaultDriver(): string
    {
        return 'email';
    }
}
