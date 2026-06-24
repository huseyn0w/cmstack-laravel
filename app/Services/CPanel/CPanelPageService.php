<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelPageRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for CPanel page administration. Owns all data access for the
 * page controller via CPanelPageRepository; inherits generic CRUD from
 * BaseCrudService and returns domain results (never HTTP responses).
 */
class CPanelPageService extends BaseCrudService
{
    public function __construct(private CPanelPageRepository $repo)
    {
        parent::__construct($repo);
    }
}
