<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Immutable snapshot of a translatable content row (PostTranslation /
 * PageTranslation) captured before each admin update. Revisions are append-only:
 * they carry a created_at but never an updated_at.
 */
class Revision extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'revisionable_type',
        'revisionable_id',
        'user_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
