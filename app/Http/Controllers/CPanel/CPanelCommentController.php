<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\CPanelCommentsRequest;
use App\Services\CPanel\CPanelCommentService;

class CPanelCommentController extends CPanelBaseController
{
    public function __construct(CPanelCommentService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index()
    {
        $comments_list = $this->service->list($this->per_page);

        return view('cpanel.comments.comments_list', compact('comments_list'));
    }

    public function approve(int $id)
    {
        $this->validateCommentID($id);

        $result = $this->service->approve($id);

        if ($result) {
            echo trans('cpanel/controller.ok');
        } else {
            echo $result;
        }

    }

    public function unApprove(int $id)
    {

        $this->validateCommentID($id);

        $result = $this->service->unApprove($id);

        if ($result) {
            echo trans('cpanel/controller.ok');
        } else {
            echo $result;
            echo trans('cpanel/controller.problem');
        }

    }

    public function multipleDelete(CPanelCommentsRequest $request)
    {
        $result = $this->service->delete($request->comments);

        return back()->with('deleted', $result);
    }

    public function validateCommentID($id)
    {
        if ($id <= 0) {
            echo trans('cpanel/controller.id_int');

            return false;
        }

        return true;
    }
}
