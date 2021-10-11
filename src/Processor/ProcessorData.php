<?php

namespace Worksome\DataExport\Processor;

class ProcessorData
{
    public function __construct(
        private array $data,
        private string $type
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
