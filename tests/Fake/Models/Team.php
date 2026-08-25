<?php

namespace Worksome\DataExport\Tests\Fake\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $appends = ['member_names'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Stands in for the real models whose accessors reach into a relation
    public function getMemberNamesAttribute(): string
    {
        return $this->users->pluck('name')->implode(', ');
    }
}
