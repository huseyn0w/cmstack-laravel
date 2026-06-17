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
use Illuminate\Database\QueryException;
use Doctrine\DBAL\Driver\PDOException;
use App\Http\Models\Post;

class CategoryRepository extends BaseRepository
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

        $this->translated_table_model = new CategoryTranslation;

    }


    public function displayList(int $category_id, int $page = 1)
    {
        $this->locale = get_current_lang();

        $main_table_name = "posts";
        $translated_table_name= "post_translations";
        $join_column = "post_id";

        $select_fields = [
            'id',
            'title',
            'slug',
            'thumbnail',
            'preview',
            'likes',
            'created_at'
        ];

        $select = $this->generateSelectFieldsArray($select_fields, $main_table_name, $translated_table_name);

        $count = get_general_settings('posts_per_page');

        try{
            $data = Post::join($translated_table_name, $main_table_name.'.id', '=', $translated_table_name.'.'.$join_column)
                ->select($select)
                ->where($translated_table_name.'.locale', $this->locale)
                ->whereHas('categories', function($query) use($category_id) {
                    $query->select('category_id');
                    $query->where('category_id',$category_id);
                })
                ->with('author')->paginate($count, array('*'), 'page', $page);

        } catch (QueryException $e) {
//            dd($e->getMessage());
            throwAbort();
        } catch (PDOException $e) {
//            dd($e->getMessage());
            throwAbort();
        } catch (\Error $e) {
//            dd($e->getMessage());
            throwAbort();
        }


        return $data;
    }
    
}