<?php

namespace App\Http\Controllers\SuperAdmin;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\SuperAdmin\RoleManagementService;
use App\Http\Requests\SuperAdmin\StoreRoleRequest;
use App\Http\Requests\SuperAdmin\UpdateRoleRequest;
use App\Http\Requests\SuperAdmin\RoleIndexRequest;

class RoleManagementController extends Controller
{
    protected $roleService;

    public function __construct(RoleManagementService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of roles
     */
    public function index(RoleIndexRequest $request)
    {
        $filters = $request->validated();
        $roles = $this->roleService->index($filters);

        return view('super-admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = $this->roleService->getAllPermissions('web');

        return view('super-admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            $this->roleService->store($request->validated());

            return redirect()->route('super-admin.roles.index')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role = $this->roleService->show($role);
        $permissions = $this->roleService->getAllPermissions($role->guard_name);

        return view('super-admin.roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $permissions = $this->roleService->getAllPermissions($role->guard_name);
        $rolePermissionIds = $this->roleService->getRolePermissionIds($role);

        return view('super-admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified role
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            $this->roleService->update($role, $request->validated());

            return redirect()->route('super-admin.roles.index')
                ->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        try {
            $this->roleService->destroy($role);

            return redirect()->route('super-admin.roles.index')
                ->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
