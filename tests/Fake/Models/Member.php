<?php

namespace Worksome\DataExport\Tests\Fake\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'users';

    // Stands in for a consumer that reshapes a declared column when serialising
    public function toArray(): array
    {
        $data = parent::toArray();

        $data['email'] = 'redacted';

        return $data;
    }
}
