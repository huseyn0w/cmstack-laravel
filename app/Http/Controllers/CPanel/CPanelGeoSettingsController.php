<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\ValidateGeoSettings;
use App\Repositories\CPanelGeoSettingsRepository;

/**
 * Admin GEO settings page (global, singleton row id = 1).
 * Gated by the manage_general_settings middleware on the route group.
 */
class CPanelGeoSettingsController extends CPanelBaseController
{
    public function __construct(CPanelGeoSettingsRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function index()
    {
        $geo_settings = $this->repository->firstOrNew();

        return view('cpanel.settings.geo-settings', compact('geo_settings'));
    }

    public function store(ValidateGeoSettings $request)
    {
        $instance = $this->repository->firstOrNew();
        $instance->fill($request->validated());
        $result = $instance->save();

        return back()->with('message', $result);
    }
}
