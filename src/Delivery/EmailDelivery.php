<?php

namespace Worksome\DataExport\Delivery;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Delivery\Contracts\Delivery as DeliveryContract;
use Illuminate\Support\Facades\Notification;
use Worksome\DataExport\Notifications\SuccessfulExportNotification;

class EmailDelivery implements DeliveryContract {
    public function deliver(Export $export): void
    {
        Notification::route('mail', [
            $export->getDeliveryEmail(),
        ])->notify(new SuccessfulExportNotification($export));
    }
}
