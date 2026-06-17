<?php
/**
 * Laravella CMS
 * File: PageRepository.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 24.10.2019
 */

namespace App\Repositories;


use App\Http\Models\Category;
use App\Http\Models\CategoryTranslation;

class CPanelCategoryRepository extends BaseRepository
{
    protected $main_table = 'categories';

    protected $translated_table = 'category_translations';

    protected $translated_table_join_column = 'category_id';

    protected $select_fields = [
        'id',
        'author_id',
        'title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_noindex',
        'description',
        'slug'
    ];

    public function __construct(Category $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->translated_model = new CategoryTranslation;
    }

    
}