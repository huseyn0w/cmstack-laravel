<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\PageListRequest;
use App\Http\Requests\ValidatePageData;
use App\Services\CPanel\CPanelPageService;

class CPanelPageController extends CPanelBaseController
{
    private $users_list;

    private $page_templates;

    public function __construct(CPanelPageService $service)
    {
        parent::__construct();
        $this->service = $service;
        $this->users_list = get_authors_list();
        $this->page_templates = get_page_templates_list();
    }

    public function index()
    {
        $pages_list = $this->service->list($this->per_page);

        return view('cpanel.pages.pages_list', compact('pages_list'));
    }

    public function multipleDelete(PageListRequest $request)
    {
        $result = $this->service->delete($request->pages);

        return back()->with('message', $result);
    }

    public function editPage($id)
    {
        $this->result = $this->service->getById($id);

        if (is_null($this->result)) {
            return $this->addPage();
        }

        return view('cpanel.pages.edit_page',
            [
                'entity' => $this->result,
                'users_list' => $this->users_list,
                'page_templates' => $this->page_templates,
                'categories_list' => get_post_categories_list(['category_id', 'title']),
                'translation_links' => get_entity_translation_links('pages', $id),
            ]
        );
    }

    public function createPage(ValidatePageData $request)
    {
        parent::create($request);

        return redirect()->route('cpanel_pages_list')->with('page_added', ' ');

    }

    public function updatePage($id, ValidatePageData $request)
    {
        return parent::update($id, $request);
    }

    public function addPage()
    {
        $array = [
            'users_list' => $this->users_list,
            'page_templates' => $this->page_templates,
            'categories_list' => get_post_categories_list(['category_id', 'title']),
        ];

        if (request()->route('lang')) {
            $array['translation_links'] = get_entity_translation_links('pages', request()->id);
        }

        return view('cpanel.pages.new_page', $array);
    }
}
