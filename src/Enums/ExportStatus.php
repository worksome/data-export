<?php

namespace Worksome\DataExport\Enums;

enum ExportStatus: string
{
    case Awaiting = 'awaiting';
    case Completed = 'completed';
}
