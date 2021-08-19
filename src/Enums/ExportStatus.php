<?php

namespace Worksome\DataExport\Enums;

use BenSampo\Enum\Enum;

final class ExportStatus extends Enum
{
    public const AWAITING = 'awaiting';
    public const COMPLETED = 'completed';
}
