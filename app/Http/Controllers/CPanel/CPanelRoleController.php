<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\ValidateUserRoles;
use App\Services\CPanel\CPanelRoleService;

class CPanelRoleController extends CPanelBaseController
{
    private $user_roles;

    private $countries;

    private $role_permissions;

    public function __construct(CPanelRoleService $service)
    {
        parent::__construct();
        $this->service = $service;
        $this->user_roles = get_user_roles();
        $this->countries = get_countries_array();
        $this->role_permissions = get_user_role_permissions();
    }

    public function index()
    {
        $roles_list = $this->service->list($this->per_page);

        return view('cpanel.roles.roles_list', compact('roles_list'));
    }

    public function addRole()
    {
        return view('cpanel.roles.new_role', ['user_roles' => $this->user_roles, 'countries' => $this->countries, 'role_permissions' => $this->role_permissions]);
    }

    public function createRole(ValidateUserRoles $request)
    {
        parent::create($request);

        return redirect()->route('cpanel_user_roles')->with('role_added', '-');
    }

    public function editRole($id)
    {
        parent::edit($id);

        return view('cpanel.roles.edit_role', ['role' => $this->result, 'user_roles' => $this->user_roles, 'countries' => $this->countries, 'role_permissions' => $this->role_permissions]);
    }

    public function updateRole($id, ValidateUserRoles $request)
    {
        return parent::update($id, $request);
    }
}
