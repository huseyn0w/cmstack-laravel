<?php

namespace App\Services\Front;

use App\Repositories\PageRepository;
use App\Services\BaseCrudService;

/**
 * Front-end page view service: resolves the public page (via the inherited
 * resolveBySlug) for slug-routed rendering. All data access goes through
 * PageRepository — the controller never touches it directly.
 */
class PageViewService extends BaseCrudService
{
    public function __construct(private PageRepository $repo)
    {
        parent::__construct($repo);
    }
}
