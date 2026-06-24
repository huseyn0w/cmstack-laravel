<?php

namespace App\Http\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use Cachable;

    protected $table = 'post_comments';

    protected $fillable = ['user_id', 'post_id', 'parent_id', 'comment', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comments::class, 'parent_id')
            ->where('status', 1)
            ->with('user');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($comment) {
            $comment->replies()->delete();
        });
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
