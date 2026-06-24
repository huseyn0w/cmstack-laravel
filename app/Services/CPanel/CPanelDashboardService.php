<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelCommentRepository;
use App\Repositories\CPanelPostRepository;
use App\Repositories\CPanelUserRepository;

/**
 * Domain service for the admin dashboard (home) screen.
 *
 * Owns the dashboard read logic but never touches the ORM directly — every
 * query lives in a repository (Controller -> Service -> Repository -> Model).
 */
class CPanelDashboardService
{
    public function __construct(
        private CPanelPostRepository $posts,
        private CPanelUserRepository $users,
        private CPanelCommentRepository $comments,
    ) {}

    public function latestPosts($count)
    {
        return $this->posts->latestWithTitles($count);
    }

    public function latestUsers($count)
    {
        return $this->users->latestUsernames($count);
    }

    public function latestComments($count)
    {
        return $this->comments->latestComments($count);
    }
}
