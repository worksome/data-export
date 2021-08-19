<?php

namespace Worksome\DataExport\Delivery;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Delivery\Contracts\Delivery as DeliveryContract;
use Illuminate\Contracts\Container\Container;
use Worksome\DataExport\Enums\DeliveryType;
use Worksome\DataExport\Exceptions\InvalidDeliveryTypeException;

class DeliveryManager implements DeliveryContract {
    public function __construct(
        private Container $container
    ) {}

    public function deliver(Export $export): void
    {
        $delivery = $this->getDelivery($export);
        $delivery->deliver($export);
    }

    protected function getDelivery(Export $export): DeliveryContract
    {
        return match ($export->getDeliveryType()) {
            DeliveryType::EMAIL => $this->container->get(EmailDelivery::class),
            default => throw new InvalidDeliveryTypeException('Invalid delivery type.')
        };
    }
}
