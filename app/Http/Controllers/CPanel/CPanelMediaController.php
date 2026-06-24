<?php

namespace App\Http\Controllers\CPanel;

class CPanelMediaController extends CPanelBaseController
{
    public function index()
    {
        return view('cpanel.media.media');
    }
}
