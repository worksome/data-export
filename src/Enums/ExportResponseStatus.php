<?php

namespace Worksome\DataExport\Enums;

use GraphQL\Type\Definition\Description;
use Worksome\GraphQLHelpers\Definition\Concerns\GraphQLConvertable;

#[Description('The status of the export.')]
enum ExportResponseStatus: string
{
    use GraphQLConvertable;

    #[Description('The export was successful.')]
    case Success = 'success';

    #[Description('The export experienced one or more errors.')]
    case Error = 'error';
}
