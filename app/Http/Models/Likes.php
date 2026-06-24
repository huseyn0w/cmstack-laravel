<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    public $timestamps = false;

    protected $table = 'user_post_likes';

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
