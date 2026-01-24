<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use App\Enum\UserRoleEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Http\Services\SuperAdmin\AdminManagementService;
use App\Http\Requests\SuperAdmin\StoreAdminRequest;
use App\Http\Requests\SuperAdmin\UpdateAdminRequest;

class AdminManagementController extends Controller
{
    protected $adminService;

    public function __construct(AdminManagementService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'is_active', 'sort_dir']);
        $admins = $this->adminService->index($filters);
        return view('super-admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $permissions = $this->adminService->getAllPermissions();
        return view('super-admin.admins.create', compact('permissions'));
    }

    public function show(User $admin)
    {
        $this->adminService->validateAdmin($admin);

        $admin->load('permissions');
        $permissions = $this->adminService->getAllPermissions();

        return view('super-admin.admins.show', compact('admin', 'permissions'));
    }

    public function store(StoreAdminRequest $request)
    {
        $this->adminService->store($request->validated());

        return redirect()->route('super-admin.admins.index')
            ->with('success', 'Admin created successfully with custom permissions');
    }

    public function edit(User $admin)
    {
        $this->adminService->validateAdmin($admin);

        $permissions = $this->adminService->getAllPermissions();
        $adminPermissions = $this->adminService->getAdminPermissions($admin);

        return view('super-admin.admins.edit', compact('admin', 'permissions', 'adminPermissions'));
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {
        $this->adminService->validateAdmin($admin);
        $this->adminService->update($admin, $request->validated());

        return redirect()->route('super-admin.admins.index')
            ->with('success', 'Admin updated successfully');
    }

    public function destroy(User $admin)
    {
        $this->adminService->validateAdmin($admin);
        $this->adminService->destroy($admin);

        return redirect()->route('super-admin.admins.index')
            ->with('success', 'Admin deleted successfully');
    }
}
