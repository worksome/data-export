<?php

namespace Worksome\DataExport\Processor;

use Worksome\DataExport\Processor\Contracts\Processor as ProcessorContract;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionResolverInterface;
use Worksome\DataExport\Enums\ExportType;
use Worksome\DataExport\Exceptions\InvalidProcessorTypeException;
use Worksome\DataExport\Models\Export;

class ProcessorManager implements ProcessorContract {
    public function __construct(
        private Container $container,
        private Repository $config,
        private ConnectionResolverInterface $connectionResolver,
    ) {}

    public function process(Export $export): ProcessorData
    {
        $processor = $this->getProcessor($export);
        return $processor->process($export);
    }

    protected function getProcessor(Export $export): ProcessorContract
    {
        $connection = $this->connectionResolver->connection($this->config->get('database.platform', 'platform'));

        return match ($export->type->value) {
            ExportType::CONTRACT => $this->container->make(ContractProcessor::class, ['connection' => $connection]),
            default => throw new InvalidProcessorTypeException('Invalid export type.')
        };
    }
}
