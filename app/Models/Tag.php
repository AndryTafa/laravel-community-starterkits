<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $guarded = [];

    public function starterkits(): BelongsToMany
    {
        return $this->belongsToMany(Starterkit::class);
    }
}
