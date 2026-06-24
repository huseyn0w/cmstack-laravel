<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CPanelBaseController extends Controller
{
    /**
     * Domain service (a App\Services\BaseCrudService subclass) that owns all
     * data access. Controllers never call a repository directly — the chain is
     * Controller -> Service -> Repository. Left untyped (like the legacy
     * codebase) so each subclass can assign its own concrete domain service and
     * call domain-specific methods on it.
     */
    protected $service;

    protected $per_page = 10;

    protected $user;

    protected $result;

    protected $locale;

    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            if (! Auth::check()) {
                return redirect('/login');
            }
            $this->user = \Auth::user(); // you can access user id hereyou can access user id here

            return $next($request);
        });

        $this->locale = app()->getLocale();

    }

    protected function checkUserPermission($permissionName, $modelName)
    {
        if (Auth::user()->cannot($permissionName, $modelName)) {
            return false;
        }

        return true;

    }

    protected function redirectUnathorizedUser()
    {
        return redirect()->route('unathorized');
    }

    protected function deleteAjax(int $id)
    {
        if ($id <= 0) {
            echo trans('cpanel/controller.id_int');

            return;
        }

        $result = $this->service->delete($id);

        if ($result) {
            echo trans('cpanel/controller.ok');
        } else {
            echo $result;
            echo trans('cpanel/controller.problem');
        }

    }

    protected function delete($id)
    {
        $result = $this->service->delete($id);

        if (($result)) {
            $message = 'green';
        } else {
            $message = 'red';
        }

        return back()->with('message', $message);
    }

    protected function edit($id)
    {
        $this->result = $this->service->getById($id);

        if (! $this->result) {
            abort(404);
        }
    }

    protected function update($id, $data)
    {
        $this->result = $this->service->update($id, $data);

        return back()->with('message', ' ');
    }

    protected function create($request)
    {
        $this->result = $this->service->create($request);

        return $this->result;
    }

    protected function restore($id)
    {
        $this->result = $this->service->restore($id);

        return back()->with('post_restored', ' ');
    }

    protected function destroyAjax(int $id)
    {
        if ($id <= 0) {
            echo trans('cpanel/controller.id_int');

            return;
        }

        $result = $this->service->destroy($id);

        if ($result) {
            echo trans('cpanel/controller.ok');
        } else {
            echo trans('cpanel/controller.problem');
        }

    }

    protected function setLang($lang)
    {
        \Session::put('locale', $lang);

        return redirect()->refresh();
    }
}
