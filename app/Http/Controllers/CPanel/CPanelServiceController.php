<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\ServiceListRequest;
use App\Http\Requests\ValidateServiceData;
use App\Services\CPanel\CPanelServiceService;

class CPanelServiceController extends CPanelBaseController
{
    public function __construct(CPanelServiceService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function index()
    {
        $services_list = $this->service->list($this->per_page);

        return view('cpanel.services.services_list', compact('services_list'));
    }

    public function trashedServices()
    {
        $services_list = $this->service->trashed($this->per_page);

        return view('cpanel.services.services_list', compact('services_list'));
    }

    public function multipleActions(ServiceListRequest $request)
    {
        $action = $request->services_action;

        switch ($action) {
            case 'restore':
                $this->service->runBulkAction($action, $request->services);

                return back()->with('restored', true);
            case 'destroy':
                $this->service->runBulkAction($action, $request->services);

                return back()->with('destroyed', true);
            case 'delete':
                $this->service->runBulkAction($action, $request->services);

                return back()->with('deleted', true);
            default:
                return redirect()->back();
        }
    }

    public function restore($id)
    {
        $this->service->restore($id);

        return back()->with('restored', true);
    }

    public function editService($id)
    {
        $this->result = $this->service->getById($id);

        if (is_null($this->result)) {
            return $this->addService();
        }

        return view('cpanel.services.edit_service',
            [
                'entity' => $this->result,
                'translation_links' => get_entity_translation_links('services', $id),
            ]
        );
    }

    public function createService(ValidateServiceData $request)
    {
        parent::create($request);

        return redirect()->route('cpanel_services_list')->with('service_added', ' ');
    }

    public function updateService($id, ValidateServiceData $request)
    {
        return parent::update($id, $request);
    }

    public function addService()
    {
        $array = [];

        if (request()->route('lang')) {
            $array['translation_links'] = get_entity_translation_links('services', request()->id);
        }

        return view('cpanel.services.new_service', $array);
    }
}
