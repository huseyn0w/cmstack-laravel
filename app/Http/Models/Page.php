<?php

namespace App\Http\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model implements TranslatableContract
{
    use Cachable;
    use SoftDeletes;
    use Translatable;

    public $timestamps = false;

    public $translatedAttributes = [
        'title',
        'template',
        'page_id',
        'created_at',
        'updated_at',
        'slug',
        'author_id',
        'status',
        'custom_fields',
        'content',
        'meta_keywords',
        'meta_description',
    ];

    protected $fillable = [
        'title',
        'slug',
        'author_id',
        'page_id',
        'status',
        'template',
        'content',
        'custom_fields',
        'meta_keywords',
        'meta_description',
        'updated_at',
        'created_at',
    ];

    public function author()
    {
        return $this->hasOne('App\Http\Models\User', 'id', 'author_id');
    }
}
