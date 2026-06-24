<?php

namespace App\Services\CPanel;

use App\Http\Controllers\CPanel\CPanelRoleController;
use App\Repositories\CPanelUserRolesRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for admin role management.
 *
 * Owns all data access for {@see CPanelRoleController};
 * the controller never touches the repository directly.
 */
class CPanelRoleService extends BaseCrudService
{
    public function __construct(private CPanelUserRolesRepository $repo)
    {
        parent::__construct($repo);
    }
}
