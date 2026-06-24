<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelCategoryRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for CPanel category administration. Owns all data access for
 * the category controller via CPanelCategoryRepository; inherits generic CRUD
 * from BaseCrudService and returns domain results (never HTTP responses).
 */
class CPanelCategoryService extends BaseCrudService
{
    public function __construct(private CPanelCategoryRepository $repo)
    {
        parent::__construct($repo);
    }
}
