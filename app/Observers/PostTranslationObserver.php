<?php

namespace App\Observers;

use App\Http\Models\PostTranslation;

class PostTranslationObserver extends CmstackLaravelObserver
{
    /**
     * Handle the post translation "saving" event.
     *
     * @return void
     */
    public function saving(PostTranslation $postTranslation)
    {
        $postTranslation->preview = clean($this->request->preview);
        $postTranslation->content = clean($this->request->content);
    }
}
