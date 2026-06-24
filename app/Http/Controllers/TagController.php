<?php

namespace App\Http\Controllers;

use App\Services\Front\TagViewService;

class TagController extends BaseController
{
    public function __construct(TagViewService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index(string $tag_slug, ?string $locale = null, int $page = 1)
    {
        $result = parent::index($tag_slug, $locale);

        if (is_object($result)) {
            return $result;
        }

        $this->data->posts = $this->service->postsFor($this->data->id, $page);

        return view('default/tags/tag', ['data' => $this->data]);
    }
}
