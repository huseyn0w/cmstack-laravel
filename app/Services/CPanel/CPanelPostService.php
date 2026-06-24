<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelPostRepository;
use App\Services\BaseCrudService;

class CPanelPostService extends BaseCrudService
{
    public function __construct(private CPanelPostRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Paginated listing of trashed posts for the index screen.
     */
    public function trashed($count)
    {
        return $this->repo->trashedPosts($count);
    }

    /**
     * Dispatch a bulk action (restore/destroy) against the given posts.
     * Returns the underlying repository result, or false for an unknown action.
     */
    public function runBulkAction(string $action, $posts)
    {
        switch ($action) {
            case 'restore':
                return $this->restore($posts);
            case 'destroy':
                return $this->destroy($posts);
            default:
                return false;
        }
    }
}
