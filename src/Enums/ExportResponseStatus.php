<?php

namespace Worksome\DataExport\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SUCCESS()
 * @method static static ERROR()
 */
final class ExportResponseStatus extends Enum
{
    public const SUCCESS = 'success';

    public const ERROR = 'error';
}
