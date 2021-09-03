<?php

namespace Worksome\DataExport\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Worksome\DataExport\Enums\ExportStatus;
use Worksome\DataExport\Models\Export;

class ExportFactory extends Factory
{
    protected $model = Export::class;

    public function definition(): array
    {
        return [
            'status'       => ExportStatus::AWAITING,
            'user_id'      => 1,
            'account_id'   => 1,
            'account_type' => 'user',
            'type'        => 'contract',
            'generator_type' => 'csv',
            'deliveries'    => [
                ['type' => 'email', 'value' => 'john@doe.com'],
            ],
            'args' => [
                'dateFrom' => '2021-01-01',
                'dateTo' => '2021-01-02',
            ],
        ];
    }
}
