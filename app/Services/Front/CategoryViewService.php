<?php

namespace App\Services\Front;

use App\Repositories\CategoryRepository;
use App\Services\BaseCrudService;

/**
 * Front-end category view service: resolves the public category page (via the
 * inherited resolveBySlug) and lists its posts. All data access goes through
 * CategoryRepository — the controller never touches it directly.
 */
class CategoryViewService extends BaseCrudService
{
    public function __construct(private CategoryRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Paginated list of published posts belonging to the given category.
     */
    public function postsFor($categoryId, $page = 1)
    {
        return $this->repo->displayList($categoryId, $page);
    }
}
