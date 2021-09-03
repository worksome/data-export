<?php

namespace Worksome\DataExport\Tests\Feature\Processor;

use Illuminate\Container\Container;
use Worksome\DataExport\Exceptions\InvalidProcessorException;
use Worksome\DataExport\Processor\Contracts\ProcessorDriver;
use Worksome\DataExport\Processor\ProcessorRepository;
use Worksome\DataExport\Tests\Fake\FakeProcessorDriver;

it('can register and retrieve a processor', function () {
    $container = new Container();

    $repository = new ProcessorRepository($container);

    $repository->register('foo', FakeProcessorDriver::class);

    expect($repository->get('foo'))->toBeInstanceOf(ProcessorDriver::class);
    expect($repository->get('foo'))->toBeInstanceOf(FakeProcessorDriver::class);
});

it('will throw an exception when trying to retrieve a non registered processor', function () {
    $container = new Container();

    $repository = new ProcessorRepository($container);

    $repository->get('foo');
})->throws(InvalidProcessorException::class);
