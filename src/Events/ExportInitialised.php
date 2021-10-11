<?php

namespace Worksome\DataExport\Events;

use Worksome\DataExport\Models\Export;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportInitialised
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Export $export
    ) {
    }
}
