<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\ValidateSiteOptions;
use App\Services\CPanel\SiteOptionsService;

class CPanelSiteOptionsController extends CPanelBaseController
{
    public function __construct(SiteOptionsService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index()
    {
        $site_options = $this->service->current();

        return view('cpanel.settings.site-options', compact('site_options'));
    }

    public function store(ValidateSiteOptions $request)
    {
        $result = $this->service->update(1, $request);

        return back()->with('message', $result);
    }
}
