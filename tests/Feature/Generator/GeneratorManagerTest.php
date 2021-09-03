<?php

namespace Worksome\DataExport\Tests\Generator;

use Worksome\DataExport\Generator\Contracts\GeneratorDriver;
use Worksome\DataExport\Generator\GeneratorManager;
use Worksome\DataExport\Tests\Fake\FakeGeneratorDriver;

it('can register and use a new generator driver', function () {
    $manager = app(GeneratorManager::class);

    $manager->extend('foo', fn() => new FakeGeneratorDriver());

    expect($manager->driver('foo'))->toBeInstanceOf(GeneratorDriver::class);
    expect($manager->driver('foo'))->toBeInstanceOf(FakeGeneratorDriver::class);
});
