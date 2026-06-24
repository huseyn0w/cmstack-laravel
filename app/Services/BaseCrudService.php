<?php

namespace App\Services;

use App\Repositories\BaseRepository;

/**
 * Generic CRUD orchestration sitting between controllers and repositories.
 *
 * Controllers must never touch a repository directly (the HTTP boundary calls a
 * service; the service owns data access via the repository). This base wraps a
 * repository and returns DOMAIN results — entities, paginators, booleans —
 * never redirects or views; mapping a result to an HTTP response stays in the
 * controller.
 *
 * Domain services extend this class and pass their concrete repository to
 * parent::__construct(), adding domain-specific methods on top.
 */
class BaseCrudService
{
    public function __construct(protected BaseRepository $repository) {}

    /**
     * Paginated/limited listing for an index screen.
     */
    public function list(int $perPage, int $page = 1)
    {
        return $this->repository->only($perPage, $page);
    }

    /**
     * Fetch a single record by primary key (null when absent).
     */
    public function getById($id)
    {
        return $this->repository->getBy('id', $id);
    }

    /**
     * Persist a new record from validated/whitelisted input.
     */
    public function create($request)
    {
        return $this->repository->create($request);
    }

    /**
     * Update a record by id from validated/whitelisted input.
     */
    public function update(int $id, $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Soft-delete (or delete) one or more records. Returns truthy on success.
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Permanently delete one or more records.
     */
    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }

    /**
     * Restore one or more soft-deleted records.
     */
    public function restore($id)
    {
        return $this->repository->restore($id);
    }
}
