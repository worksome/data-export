<?php

namespace Worksome\DataExport\Delivery;

use Worksome\DataExport\Models\Export;

class DeliverySender
{
    public function __construct(
        private DeliveryManager $deliveryManager,
    ) {}

    public function send(Export $export): void
    {
        $channels = $export->getDeliveryChannels();

        foreach ($channels as $channel) {
            $driver = $this->deliveryManager->driver($channel);

            $driver->deliver($export);
        }
    }
}
