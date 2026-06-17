<?php

namespace App\Repositories;

use App\Http\Models\CPanel\CPanelGeoSettings;

/**
 * Persistence for the global GEO settings singleton (row id = 1).
 * Mirrors CPanelSeoSettingsRepository.
 */
class CPanelGeoSettingsRepository extends BaseRepository
{
    public function __construct(CPanelGeoSettings $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    /**
     * Always return a model instance even on a fresh DB so the settings form
     * can bind to it (singleton row id = 1).
     */
    public function firstOrNew()
    {
        return $this->model::firstOrNew(['id' => 1]);
    }
}
