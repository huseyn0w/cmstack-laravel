<?php

namespace App\Http\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class MenuTranslation extends Model
{
    use Cachable;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'menu_id',
        'locale',
        'slug',
        'author_id',
        'content',
    ];
}
