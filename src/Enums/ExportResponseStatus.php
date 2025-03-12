<?php

namespace Worksome\DataExport\Enums;

enum ExportResponseStatus: string
{
    case Success = 'success';
    case Error = 'error';
}
