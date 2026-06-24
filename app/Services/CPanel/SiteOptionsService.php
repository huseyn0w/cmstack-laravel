<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelSiteOptionsRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for the global site-options singleton (row id = 1).
 * Owns all data access; the controller only maps results to HTTP responses.
 */
class SiteOptionsService extends BaseCrudService
{
    public function __construct(private CPanelSiteOptionsRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * The current (singleton) site-options record.
     */
    public function current()
    {
        return $this->repo->first();
    }
}
