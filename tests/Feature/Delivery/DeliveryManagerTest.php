<?php

namespace Worksome\DataExport\Tests\Delivery;

use Worksome\DataExport\Delivery\Contracts\DeliveryDriver;
use Worksome\DataExport\Delivery\DeliveryManager;
use Worksome\DataExport\Tests\Fake\FakeDeliveryDriver;

it('can register and use a new delivery driver', function () {
    $manager = app(DeliveryManager::class);

    $manager->extend('foo', fn() => new FakeDeliveryDriver());

    expect($manager->driver('foo'))->toBeInstanceOf(DeliveryDriver::class);
    expect($manager->driver('foo'))->toBeInstanceOf(FakeDeliveryDriver::class);
});
