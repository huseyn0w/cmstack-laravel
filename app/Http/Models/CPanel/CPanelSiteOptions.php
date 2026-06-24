<?php

namespace App\Http\Models\CPanel;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class CPanelSiteOptions extends Model
{
    use Cachable;

    public $timestamps = false;

    protected $table = 'site_options';

    protected $fillable = [
        'logo_url',
        'copyright',
        'github_url',
        'linkedin_url',
    ];
}
