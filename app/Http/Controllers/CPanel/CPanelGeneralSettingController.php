<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\ValidateGeneralSettings;
use App\Services\CPanel\GeneralSettingsService;

class CPanelGeneralSettingController extends CPanelBaseController
{
    public function __construct(GeneralSettingsService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index()
    {
        $general_settings = $this->service->current();

        return view('cpanel.settings.general-settings', compact('general_settings'));
    }

    public function store(ValidateGeneralSettings $request)
    {
        $result = $this->service->update(1, $request);

        return back()->with('message', $result);
    }
}
