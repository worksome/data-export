<?php

namespace Worksome\DataExport\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $impersonator_id
 * @property int $account_id
 * @property string $account_type
 * @property string $path
 * @property string $status
 * @property string $type
 * @property string $generator_type
 * @property array $deliveries
 * @property array $args
 * @property float $size
 * @property int $total_rows
 * @property string $mime_type
 */
class Export extends Model
{
    protected $fillable = [
        'user_id',
        'impersonator_id',
        'account_id',
        'account_type',
        'path',
        'status',
        'type',
        'generator_type',
        'deliveries',
        'args',
        'size',
        'total_rows',
        'mime_type',
    ];

    protected $casts = [
        'args'       => 'array',
        'deliveries' => 'array',
        'size'       => 'float',
        'total_rows' => 'integer',
    ];

    public function getFormattedSize(): string
    {
        $units = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        for ($i = 0; $this->size > 1024; $i++) {
            $this->size /= 1024;
        }

        return sprintf('%s%s', round($this->size, 2), $units[$i]);
    }
}
