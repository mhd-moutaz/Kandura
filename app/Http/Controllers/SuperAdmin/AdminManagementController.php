<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use App\Enum\UserRoleEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\SuperAdmin\AdminManagementService;
use App\Http\Requests\SuperAdmin\StoreAdminRequest;
use App\Http\Requests\SuperAdmin\UpdateAdminRequest;
use App\Http\Requests\SuperAdmin\FilterAdminRequest;

class AdminManagementController extends Controller
{
    protected $adminService;

    public function __construct(AdminManagementService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(FilterAdminRequest $request)
    {
        $admins = $this->adminService->index($request->validated());
        return view('super-admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = $this->adminService->getAllRoles();
        $permissions = $this->adminService->getAllPermissions();
        return view('super-admin.admins.create', compact('roles', 'permissions'));
    }

    public function show(User $admin)
    {
        $admin = $this->adminService->show($admin);
        $roles = $this->adminService->getAllRoles();
        $permissions = $this->adminService->getAllPermissions();
        return view('super-admin.admins.show', compact('admin', 'roles', 'permissions'));
    }

    public function store(StoreAdminRequest $request)
    {
        $this->adminService->store($request->validated());

        return redirect()->route('super-admin.admins.index')
            ->with('success', 'Admin created successfully');
    }

    public function edit(User $admin)
    {

        $roles = $this->adminService->getAllRoles();
        $permissions = $this->adminService->getAllPermissions();
        return view('super-admin.admins.edit', compact('admin', 'roles', 'permissions'));
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {

        $this->adminService->update($admin, $request->validated());

        return redirect()->route('super-admin.admins.index')
            ->with('success', 'Admin updated successfully');
    }

    public function destroy(User $admin)
    {

        $this->adminService->destroy($admin);

        return redirect()->route('super-admin.admins.index')
            ->with('success', 'Admin deleted successfully');
    }
}
