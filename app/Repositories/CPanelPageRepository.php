<?php
/**
 * Laravella CMS
 * File: CPanelUserRepository.phpCreated by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 09.08.2019
 */

namespace App\Repositories;

use App\Http\Models\Page;
use App\Http\Models\PageTranslation;

class CPanelPageRepository extends BaseRepository
{
    protected $main_table = 'pages';

    protected $translated_table = 'page_translations';

    protected $translated_table_join_column = 'page_id';

    protected $select_fields = [
        'id',
        'author_id',
        'title',
        'slug',
        'canonical_url',
        'meta_noindex',
        'status',
        'created_at',
        'updated_at'
    ];

    public function __construct(Page $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->translated_model = new PageTranslation;
    }



}