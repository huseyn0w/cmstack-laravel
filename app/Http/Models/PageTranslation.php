<?php

namespace App\Http\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    use Cachable;

    protected $fillable = [
        'title',
        'locale',
        'page_id',
        'template',
        'created_at',
        'updated_at',
        'slug',
        'author_id',
        'status',
        'custom_fields',
        'content',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_noindex',
    ];
}
