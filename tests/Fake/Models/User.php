<?php

namespace Worksome\DataExport\Tests\Fake\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model
{
    protected $casts = [
        'is_admin' => 'bool',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
