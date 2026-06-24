<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\CategoryListRequest;
use App\Http\Requests\CategoryRequest;
use App\Services\CPanel\CPanelCategoryService;

class CPanelCategoryController extends CPanelBaseController
{
    public function __construct(CPanelCategoryService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function createCategory(CategoryRequest $request)
    {
        $this->service->create($request);

        return redirect()->route('cpanel_category_list')->with('category_added', ' ');

    }

    public function edit($id)
    {
        $this->result = $this->service->getById($id);

        if (is_null($this->result)) {
            return $this->addCategory();
        }

        return view('cpanel.post_categories.edit_category',
            [
                'entity' => $this->result,
                'categories_list' => get_post_categories_list(),
                'translation_links' => get_entity_translation_links('categories', $id),
            ]
        );
    }

    public function index()
    {
        $categories_list = $this->service->list($this->per_page);

        return view('cpanel.post_categories.post_categories_list', compact('categories_list'));
    }

    public function addCategory()
    {
        $array = [
            'categories_list' => get_post_categories_list(),
        ];

        if (request()->route('lang')) {
            $array['translation_links'] = get_entity_translation_links('categories', request()->id);
        }

        return view('cpanel.post_categories.new_category', $array);
    }

    public function multipleDelete(CategoryListRequest $request)
    {
        $result = $this->service->delete($request->categories);

        return back()->with('message', $result);
    }

    public function updateCategory($id, CategoryRequest $request)
    {
        return parent::update($id, $request);
    }
}
