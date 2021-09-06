<?php

namespace Worksome\DataExport\Tests\Fake\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'is_admin' => 'bool',
    ];
}
