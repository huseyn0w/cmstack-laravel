<?php

namespace App\Services\Front;

use App\Repositories\UserRepository;
use App\Services\BaseCrudService;

/**
 * Front-end profile service: own-profile read/update and password change for the
 * authenticated user, plus public profile lookup by username. All data access
 * goes through UserRepository — the controller resolves the HTTP context (logged
 * user id/username) and delegates persistence here.
 */
class ProfileService extends BaseCrudService
{
    public function __construct(private UserRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Resolve a user by username (null when absent).
     */
    public function byUsername($username)
    {
        return $this->repo->getBy('username', $username);
    }

    /**
     * Change the authenticated user's password. Returns false when the current
     * password does not match (the repository enforces the auth check).
     */
    public function changePassword($request)
    {
        return $this->repo->changePassword($request);
    }
}
