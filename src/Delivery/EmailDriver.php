<?php

namespace Worksome\DataExport\Delivery;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Delivery\Contracts\DeliveryDriver;
use Illuminate\Support\Facades\Notification;
use Worksome\DataExport\Notifications\SuccessfulExportNotification;

class EmailDriver implements DeliveryDriver
{
    public function deliver(Export $export): void
    {
        $delivery = $export->getDeliveryFor('email');

        $notification = new SuccessfulExportNotification($export);

        Notification::route('mail', $delivery['value'])->notify($notification);
    }
}
