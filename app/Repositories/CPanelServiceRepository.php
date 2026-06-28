<?php

namespace App\Repositories;

use App\Http\Models\Service;
use App\Http\Models\ServiceTranslation;

class CPanelServiceRepository extends BaseRepository
{
    protected $main_table = 'services';

    protected $translated_table = 'service_translations';

    protected $translated_table_join_column = 'service_id';

    protected $select_fields = [
        'id',
        'sort_order',
        'slug',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Services have no author relation; clear the default eager-load list so
     * the BaseRepository translatedOnly() path doesn't try to load 'author'.
     */
    protected $eager_relations = [];

    public function __construct(Service $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->translated_model = new ServiceTranslation;
    }

    /**
     * Paginated list of soft-deleted services for the trash tab. Services have
     * no author relation, so this is a plain ordered onlyTrashed paginate.
     */
    public function trashed($count)
    {
        return $this->model::onlyTrashed()->ordered()->paginate($count);
    }

    public function delete($id)
    {
        $result = false;

        if (is_array($id)) {
            foreach ($id as $service_id) {
                $result = $this->deleteService($service_id);
            }
        } else {
            $result = $this->deleteService($id);
        }

        if (! $result) {
            throwAbort();
        }

        return $result;
    }

    private function deleteService($id)
    {
        $result = false;
        $service = $this->model::findOrFail($id);
        if ($service->delete()) {
            $result = true;
        }

        return $result;
    }

    public function destroy($id)
    {
        $result = false;

        if (is_array($id)) {
            foreach ($id as $service_id) {
                $result = $this->destroyService($service_id);
            }
        } else {
            $result = $this->destroyService($id);
        }

        if (! $result) {
            throwAbort();
        }

        return $result;
    }

    private function destroyService($id)
    {
        $result = false;
        // onlyTrashed: a permanent delete may only target an already-trashed
        // service, so destroy can never nuke a live service in one step.
        $service = Service::onlyTrashed()->find($id);
        // forceDelete cascades to service_translations via the FK onDelete(cascade).
        if ($service && $service->forceDelete()) {
            $result = true;
        }

        return $result;
    }

    public function restore($id)
    {
        $result = false;

        if (is_array($id)) {
            foreach ($id as $service_id) {
                $result = $this->restoreService($service_id);
            }
        } else {
            $result = $this->restoreService($id);
        }

        if (! $result) {
            throwAbort();
        }

        return $result;
    }

    private function restoreService($id)
    {
        $result = false;
        if ($this->model::withTrashed()->where('id', $id)->restore()) {
            $result = true;
        }

        return $result;
    }
}
