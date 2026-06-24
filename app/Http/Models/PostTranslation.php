<?php

namespace App\Http\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    use Cachable;

    protected $fillable = [
        'title',
        'post_id',
        'locale',
        'updated_at',
        'created_at',
        'likes',
        'author_id',
        'slug',
        'thumbnail',
        'preview',
        'content',
        'meta_description',
        'status',
        'meta_keywords',
        'canonical_url',
        'meta_noindex',
    ];
}
