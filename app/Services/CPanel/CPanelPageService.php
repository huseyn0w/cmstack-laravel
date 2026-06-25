<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelPageRepository;
use App\Repositories\RevisionRepository;
use App\Services\BaseCrudService;
use App\Services\Concerns\ManagesRevisions;

/**
 * Domain service for CPanel page administration. Owns all data access for the
 * page controller via CPanelPageRepository; inherits generic CRUD from
 * BaseCrudService and returns domain results (never HTTP responses).
 */
class CPanelPageService extends BaseCrudService
{
    use ManagesRevisions;

    public function __construct(private CPanelPageRepository $repo, RevisionRepository $revisions)
    {
        parent::__construct($repo);
        $this->revisions = $revisions;
    }

    /**
     * Paginated listing of trashed pages for the index screen.
     */
    public function trashed($count)
    {
        return $this->repo->trashedPages($count);
    }

    /**
     * Dispatch a bulk action (restore/destroy) against the given pages.
     * Returns the underlying repository result, or false for an unknown action.
     */
    public function runBulkAction(string $action, $pages)
    {
        switch ($action) {
            case 'restore':
                return $this->restore($pages);
            case 'destroy':
                return $this->destroy($pages);
            default:
                return false;
        }
    }
}
