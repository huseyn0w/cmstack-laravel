<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMail as ContactRequest;
use App\Http\Requests\SearchRequest;
use App\Services\Front\ContactService;
use App\Services\Front\PageViewService;
use App\Services\Front\SearchService;

class PageController extends BaseController
{
    public function __construct(
        PageViewService $service,
        private SearchService $searchService,
        private ContactService $contactService,
    ) {
        parent::__construct();
        $this->service = $service;
    }

    public function index($slug = '/', ?string $locale = null)
    {
        $result = parent::index($slug, $locale);

        if (is_object($result)) {
            return $result;
        }

        $data = ['data' => $this->data];

        if (! empty($this->data->custom_fields)) {
            $custom_fields = json_decode($this->data->custom_fields, true);
            $data['custom_fields'] = $custom_fields;
        }

        return view('default.pages.'.$this->data->template, $data);
    }

    public function sendMail(ContactRequest $request)
    {
        $this->contactService->send($request);

        return back()->with('success', \Lang::get('default/page.contact_message_success'));
    }

    public function search()
    {
        return view('default.pages.search');
    }

    public function searchResult(SearchRequest $request, $page = 1, $count = 10)
    {
        $searchData['query'] = $request->get('query');
        $searchData['type'] = $request->get('filter');
        $searchData['result'] = $this->searchService->search($request, $page, $count);

        return view('default.pages.search', compact('searchData'));
    }

    public function paginatedResult($string, $filter, $page)
    {
        $searchData['query'] = $string;
        $searchData['type'] = $filter;
        $searchData['result'] = $this->searchService->paginate($string, $filter, $page);

        return view('default.pages.search', compact('searchData'));
    }
}
