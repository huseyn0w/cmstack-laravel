<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelCommentRepository;
use App\Services\BaseCrudService;

/**
 * Domain service for admin comment management.
 *
 * Owns all data access via CPanelCommentRepository. Returns domain results
 * (booleans/entities) — never redirects or views. The controller maps these
 * results to HTTP responses.
 */
class CPanelCommentService extends BaseCrudService
{
    public function __construct(private CPanelCommentRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Mark a comment as approved.
     */
    public function approve(int $id)
    {
        return $this->repo->approve($id);
    }

    /**
     * Mark a comment as not approved.
     */
    public function unApprove(int $id)
    {
        return $this->repo->unApprove($id);
    }
}
