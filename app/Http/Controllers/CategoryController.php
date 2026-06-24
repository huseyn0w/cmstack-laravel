<?php

namespace App\Http\Controllers;

use App\Services\Front\CategoryViewService;

class CategoryController extends BaseController
{
    public function __construct(CategoryViewService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index(string $category_slug, ?string $locale = null, int $page = 1)
    {
        $result = parent::index($category_slug, $locale);

        if (is_object($result)) {
            return $result;
        }

        $this->data->posts = $this->service->postsFor($this->data->id, $page);

        return view('default/categories/category', ['data' => $this->data]);
    }
}
