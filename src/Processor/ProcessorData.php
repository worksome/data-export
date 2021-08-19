<?php

namespace Worksome\DataExport\Processor;

class ProcessorData
{
    public function __construct(
        private array $data
    ) {}

    public function getData(): array
    {
        return $this->data;
    }
}
