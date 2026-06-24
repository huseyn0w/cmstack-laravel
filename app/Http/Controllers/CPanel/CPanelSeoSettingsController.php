<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\ValidateSeoSettings;
use App\Services\CPanel\SeoSettingsService;

/**
 * Phase 7 (SEO/GEO): admin SEO settings page (global, singleton row id = 1).
 * Gated by the manage_general_settings middleware on the route group.
 */
class CPanelSeoSettingsController extends CPanelBaseController
{
    public function __construct(SeoSettingsService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index()
    {
        $seo_settings = $this->service->currentOrNew();

        return view('cpanel.settings.seo-settings', compact('seo_settings'));
    }

    public function store(ValidateSeoSettings $request)
    {
        $result = $this->service->save($request);

        return back()->with('message', $result);
    }
}
