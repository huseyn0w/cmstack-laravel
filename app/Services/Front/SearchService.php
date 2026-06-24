<?php

namespace App\Services\Front;

use App\Repositories\PageRepository;
use App\Services\BaseCrudService;

/**
 * Front-end search service: runs the cross-entity search/pagination through
 * PageRepository (which dispatches per-filter queries). The controller only
 * assembles the view data around the returned paginator.
 */
class SearchService extends BaseCrudService
{
    public function __construct(private PageRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Run a search from the submitted SearchRequest (filter + query), returning
     * the paginated result set.
     */
    public function search($request, $page = 1, $count = 10)
    {
        return $this->repo->getSearchResult($request, $page, $count);
    }

    /**
     * Re-run a search from already-resolved query/filter values for paginated
     * navigation, returning the paginated result set.
     */
    public function paginate($string, $filter, $page)
    {
        return $this->repo->getPaginatedSearchResult($string, $filter, $page);
    }
}
