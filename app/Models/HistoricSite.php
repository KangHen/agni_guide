<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class HistoricSite extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function scopePublish(Builder $builder): void
    {
        $builder->where('is_show', 1);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function artefacts(): HasMany
    {
        return $this->hasMany(Artefact::class);
    }
}
