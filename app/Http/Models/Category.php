<?php

namespace App\Http\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements TranslatableContract
{
    use Cachable;
    use Translatable;

    public $translatedAttributes = [
        'title',
        'category_id',
        'author_id',
        'slug',
        'description',
        'parent_category_id',
        'meta_keywords',
        'meta_description',
    ];

    public $timestamps = false;

    protected $fillable = [
        'title',
        'slug',
        'parent_category_id',
        'author_id',
        'description',
        'meta_keywords',
        'meta_description',
    ];

    public function author()
    {
        return $this->hasOne('App\Http\Models\User', 'id', 'author_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'category_post')->with('comments');
    }
}
