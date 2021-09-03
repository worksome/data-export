<?php

namespace Worksome\DataExport\Models;

use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $account_id
 * @property string $account_type
 * @property string $path
 * @property string $status
 * @property string $type
 * @property string $generator_type
 * @property Collection $deliveries
 * @property array $args
 * @property int $size
 * @property string $mime_type
 */
class Export extends Model
{
    use CastsEnums;

    protected $fillable = [
        'user_id',
        'account_id',
        'account_type',
        'path',
        'status',
        'type',
        'generator_type',
        'deliveries',
        'args',
        'size',
        'mime_type',
    ];

    protected $casts = [
        'args' => 'array',
        'deliveries' => AsCollection::class,
    ];

    public function getDeliveryChannels(): array
    {
        return $this->deliveries->pluck('type')->toArray();
    }

    public function getDeliveryFor(string $type): ?array
    {
        return $this->deliveries->where('type', $type)->first();
    }

    public function getFormattedSize(): string
    {
        $units = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        for ($i = 0; $this->size > 1024; $i++) {
            $this->size /= 1024;
        }

        return sprintf('%s%s', round($this->size, 2), $units[$i]);
    }
}
