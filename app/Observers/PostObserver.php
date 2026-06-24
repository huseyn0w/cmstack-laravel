<?php

namespace App\Observers;

use App\Http\Models\Category;
use App\Http\Models\Post;

class PostObserver extends CmstackLaravelObserver
{
    /**
     * Handle the post "saving" event.
     *
     * @return void
     */
    public function saving(Post $post)
    {
        if (! is_null($post->post_id)) {
            $this->detachCategory($post);
            $this->attachCategory($post);
        }
    }

    /**
     * Handle the post "created" event.
     *
     * @return void
     */
    public function created(Post $post)
    {
        $this->attachCategory($post);
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  Post  $post
     * @return void
     */
    public function forceDeleted($post)
    {
        $this->detachCategory($post);
    }

    private function attachCategory($post)
    {
        $categories_list = $this->request->category;
        //        dd($categories_list);
        $category = Category::find($categories_list);
        $post->categories()->attach($category);
    }

    private function detachCategory($post)
    {
        $post->categories()->detach();
    }
}
