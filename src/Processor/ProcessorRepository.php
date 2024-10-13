<?php

namespace Worksome\DataExport\Processor;

use Illuminate\Container\Container;
use Worksome\DataExport\Exceptions\InvalidProcessorException;
use Worksome\DataExport\Processor\Contracts\ProcessorDriver;

class ProcessorRepository
{
    /**
     * @var array<string, class-string<ProcessorDriver>>
     */
    private array $processors = [];

    public function __construct(
        private Container $container,
    ) {
    }

    public function get(string $processor): ProcessorDriver
    {
        if (! isset($this->processors[$processor])) {
            throw new InvalidProcessorException(
                sprintf('The processor [%s] is not registered!', $processor)
            );
        }

        return $this->container->make($this->processors[$processor]);
    }

    /**
     * @param string                        $processorName
     * @param class-string<ProcessorDriver> $processorClass
     */
    public function register(string $processorName, string $processorClass): void
    {
        $this->processors[$processorName] = $processorClass;
    }
}
