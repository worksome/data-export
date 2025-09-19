<?php

namespace Worksome\DataExport\Enums;

use GraphQL\Type\Definition\Description;

#[Description('The types of export that can be generated.')]
enum GeneratorType: string
{
    #[Description('Comma-separated values (CSV).')]
    case CSV = 'CSV';
}
