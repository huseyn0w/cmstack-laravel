<?php

namespace App\Services\Front;

use App\Repositories\PostRepository;
use App\Services\BaseCrudService;

/**
 * Front-end post view service: resolves the public post page (via the inherited
 * resolveBySlug) and owns the like toggle. All data access goes through
 * PostRepository — the controller never touches it directly.
 */
class PostViewService extends BaseCrudService
{
    public function __construct(private PostRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Toggle the authenticated user's like on a post, returning the repository
     * result (a localized message string, or false when not permitted).
     */
    public function like($postId, $userId)
    {
        return $this->repo->handleLike($postId, $userId);
    }
}
