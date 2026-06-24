<?php

namespace App\Services\CPanel;

use App\Http\Controllers\CPanel\CPanelUserController;
use App\Repositories\CPanelUserRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for admin user management.
 *
 * Owns all data access for {@see CPanelUserController};
 * the controller never touches the repository directly.
 */
class CPanelUserService extends BaseCrudService
{
    public function __construct(private CPanelUserRepository $repo)
    {
        parent::__construct($repo);
    }
}
