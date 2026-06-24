<?php

namespace App\Observers;

use App\Http\Models\PostTranslation;
use App\Repositories\RevisionRepository;

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

    /**
     * Snapshot the pre-edit translation before an update is persisted. Side
     * effect of a write — delegated to the repository (no inline ORM here).
     *
     * @return void
     */
    public function updating(PostTranslation $postTranslation)
    {
        app(RevisionRepository::class)->snapshot($postTranslation);
    }
}
