<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Starterkit extends Model
{
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $appends = ['is_bookmarked'];

    protected $casts = [
        'bookmark_count' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'starterkit_bookmarks');
    }

    public function isBookmarked(): Attribute
    {
        return Attribute::get(
            fn (): bool => Auth::check() && $this->whereRelation('bookmarks', 'user_id', Auth::id())->exists()
        );
    }
}
