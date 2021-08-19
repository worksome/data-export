<?php

namespace Worksome\DataExport\Processor;

class ContractProcessor extends SqlProcessor
{
    public $sql = 'Contracts';

    public $columns = [
        'hire_id' => 'a hire',
        'contract_id',
        'rate' => 'some rate'
    ];
}
