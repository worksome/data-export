<?php

namespace Worksome\DataExport\Models;

use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Worksome\DataExport\Enums\DeliveryType;
use Worksome\DataExport\Enums\ExportType;

class Export extends Model
{
    use HasFactory;
    use CastsEnums;

    protected $fillable = [
        'user_id', 'account_id', 'account_type', 'delivery', 'path', 'status',
        'type', 'args', 'size', 'mime_type'
    ];

    protected $casts = [
        'args' => 'array',
        'delivery' => 'array'
    ];

    protected $enumCasts = [
        'type' => ExportType::class,
    ];

    public function getDeliveryType(): string
    {
        return $this->delivery['type'];
    }

    public function getDeliveryEmail(): ?string
    {
        if ($this->getDeliveryType() === DeliveryType::EMAIL) {
            return $this->delivery['value'];
        }

        return null;
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
