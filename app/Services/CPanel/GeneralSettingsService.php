<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelGeneralSettingRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for the global general-settings singleton (row id = 1).
 * Owns all data access; the controller only maps results to HTTP responses.
 */
class GeneralSettingsService extends BaseCrudService
{
    public function __construct(private CPanelGeneralSettingRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * The current (singleton) general-settings record.
     */
    public function current()
    {
        return $this->repo->first();
    }
}
