<?php

namespace App\Http\Models\CPanel;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class CPanelGeneralSettings extends Model
{
    use Cachable;

    protected $table = 'general_settings';

    public $timestamps = false;

    protected $fillable = [
        'website_name',
        'tagline',
        'contact_email',
        'posts_per_page',
        'membership',
        'comments_per_page',
        'active_template_name',
    ];
}
