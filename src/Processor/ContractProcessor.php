<?php

namespace Worksome\DataExport\Processor;

class ContractProcessor extends SqlProcessor
{
    public $sql = 'Sql/Contracts.sql';

    public $columns = [
        'hire_id' => 'a hire',
        'contract_id',
        'rate' => 'some rate'
    ];
}
