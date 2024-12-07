<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artefact extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function historicSite(): BelongsTo
    {
        return $this->belongsTo(HistoricSite::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
