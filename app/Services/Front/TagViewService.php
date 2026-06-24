<?php

namespace App\Services\Front;

use App\Repositories\TagRepository;
use App\Services\BaseCrudService;

/**
 * Front-end tag view service: resolves the public tag page (via the inherited
 * resolveBySlug) and lists its posts. All data access goes through
 * TagRepository — the controller never touches it directly.
 */
class TagViewService extends BaseCrudService
{
    public function __construct(private TagRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Paginated list of posts carrying the given tag.
     */
    public function postsFor($tagId, $page = 1)
    {
        return $this->repo->postsForTag($tagId, $page);
    }
}
