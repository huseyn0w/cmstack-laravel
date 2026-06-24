<?php

namespace App\Http\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model implements TranslatableContract
{
    use Cachable;
    use Translatable;

    public $translatedAttributes = [
        'title',
        'menu_id',
        'author_id',
        'content',
    ];

    public $timestamps = false;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'author_id',
    ];

    public function author()
    {
        return $this->hasOne('App\Http\Models\User', 'id', 'author_id');
    }
}
