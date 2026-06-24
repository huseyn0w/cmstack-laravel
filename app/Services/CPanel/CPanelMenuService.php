<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelMenuRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for admin menu management.
 *
 * Owns all data access via CPanelMenuRepository. Returns domain results —
 * never redirects or views. The controller maps these results to HTTP
 * responses and assembles view data.
 */
class CPanelMenuService extends BaseCrudService
{
    public function __construct(private CPanelMenuRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Persist a new menu from the request, applying the domain rule that a
     * menu created within a translation context (route has an `id`) must not
     * carry its own slug. Returns the created entity.
     */
    public function createFromRequest($request)
    {
        if (! empty($request->route('id'))) {
            unset($request['slug']);
        }

        return $this->repo->create($request);
    }
}
