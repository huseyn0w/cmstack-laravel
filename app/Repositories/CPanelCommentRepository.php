<?php

/**
 * Cmstack-Laravel
 * File: CPanelUserRepository.phpCreated by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 09.08.2019
 */

namespace App\Repositories;

use App\Http\Models\Comments;

class CPanelCommentRepository extends BaseRepository
{
    public function __construct(Comments $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    /**
     * Latest N comments (comment body only) for the admin dashboard.
     */
    public function latestComments($count)
    {
        return $this->model->select('comment')->orderBy('id', 'desc')->take($count)->get();
    }

    public function approve(int $id)
    {
        $comment = $this->model::find($id);
        if (! $comment) {
            $result = false;
        }
        if ($comment->update(['status' => '1'])) {
            $result = true;
        }

        return $result;

    }

    public function unApprove(int $id)
    {
        $comment = $this->model::find($id);
        if (! $comment) {
            $result = 'salam';
        }

        if ($comment->update(['status' => '0'])) {
            $result = true;
        }

        return $result;
    }
}
