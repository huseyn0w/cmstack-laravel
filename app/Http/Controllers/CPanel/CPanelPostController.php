<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\PostListRequest;
use App\Http\Requests\ValidatePostData;
use App\Services\CPanel\CPanelPostService;

class CPanelPostController extends CPanelBaseController
{
    private $users_list;

    public function __construct(CPanelPostService $service)
    {
        parent::__construct();
        $this->service = $service;
        $this->users_list = get_authors_list();
    }

    public function index()
    {
        $posts_list = $this->service->list($this->per_page);

        return view('cpanel.posts.posts_list', compact('posts_list'));
    }

    public function trashedPosts()
    {
        $posts_list = $this->service->trashed($this->per_page);

        return view('cpanel.posts.posts_list', compact('posts_list'));
    }

    public function multipleDelete(PostListRequest $request)
    {
        $this->service->delete($request->posts);

        return back()->with('deleted', true);
    }

    public function multipleDestroy(PostListRequest $request)
    {
        $this->service->destroy($request->posts);

        return back()->with('destroyed', true);
    }

    public function multipleRestore(PostListRequest $request)
    {

        $this->service->restore($request->posts);

        return back()->with('restored', true);
    }

    public function multipleActions(PostListRequest $request)
    {
        $action = $request->posts_action;

        switch ($action) {
            case 'restore':
                $this->service->runBulkAction($action, $request->posts);

                return back()->with('restored', true);
            case 'destroy':
                $this->service->runBulkAction($action, $request->posts);

                return back()->with('destroyed', true);
            default:
                return redirect()->back();
        }
    }

    public function editPost($id)
    {
        $this->result = $this->service->getById($id);

        if (is_null($this->result)) {
            return $this->addPost();
        }

        return view('cpanel.posts.edit_post',
            [
                'entity' => $this->result,
                'users_list' => $this->users_list,
                'categories_list' => get_post_categories_list(),
                'translation_links' => get_entity_translation_links('posts', $id),
            ]
        );
    }

    public function createPost(ValidatePostData $request)
    {
        $this->service->create($request);

        return redirect()->route('cpanel_posts_list')->with('post_added', true);
    }

    public function updatePost($id, ValidatePostData $request)
    {
        return parent::update($id, $request);
    }

    public function addPost()
    {
        $array = [
            'users_list' => $this->users_list,
            'categories_list' => get_post_categories_list(),

        ];

        if (request()->route('lang')) {
            $array['translation_links'] = get_entity_translation_links('posts', request()->id);
        }

        return view('cpanel.posts.new_post', $array);
    }
}
