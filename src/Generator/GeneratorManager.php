<?php

namespace Worksome\DataExport\Generator;

use Worksome\DataExport\Generator\Contracts\Generator as GeneratorContract;
use Illuminate\Contracts\Container\Container;
use Worksome\DataExport\Enums\GeneratorType;
use Worksome\DataExport\Exceptions\InvalidGeneratorTypeException;
use Worksome\DataExport\Processor\ProcessorData;

class GeneratorManager implements GeneratorContract {
    public function __construct(
        private Container $container,
        public string $format = 'CSV'
    ) {}

    public function generate(ProcessorData $processorData): GeneratorFile
    {
        $generator = $this->getGenerator();
        return $generator->generate($processorData);
    }

    protected function getGenerator(): GeneratorContract
    {
        return match ($this->format) {
            GeneratorType::CSV => $this->container->get(CsvGenerator::class),
            default => throw new InvalidGeneratorTypeException('Invalid generator type.')
        };
    }
}
