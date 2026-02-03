<?php

namespace App\Http\Services\SuperAdmin;

use App\Models\User;
use App\Enum\UserRoleEnum;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminManagementService
{
    public function index(array $filters = [])
    {
        $query = User::where('role', UserRoleEnum::ADMIN)
            ->with(['roles']);

        // استخدام scope الفلترة
        if (!empty($filters)) {
            $query->filter($filters);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate(10)->withQueryString();
    }

    public function getAllPermissions()
    {
        // Get allowed permissions for admin role from config
        $allowedPermissions = config('role_permissions.admin');

        return Permission::where('guard_name', 'web')
            ->whereIn('name', $allowedPermissions)
            ->get()
            ->groupBy(function ($permission) {
                // Split permission name by space
                $parts = explode(' ', trim($permission->name));

                // Get the last word (resource name)
                $resource = end($parts);

                // Normalize and capitalize
                return ucfirst(strtolower($resource));
            })
            ->sortKeys();
    }

    public function getAllRoles()
    {
        return Role::where('guard_name', 'web')
            ->where('name', '!=', 'user') // Exclude user role from admin assignment
            ->where('name', '!=', 'super_admin') // Exclude super_admin role from assignment
            ->orderBy('name')
            ->get();
    }

    public function store(array $data)
    {
        if (empty($data['roles'])) {
            abort(403, 'At least one role must be assigned');
        }

        if (!empty($data['roles']) && in_array('super_admin', $data['roles'])) {
            abort(403, 'Cannot assign super_admin role');
        }

        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'role' => UserRoleEnum::ADMIN,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $admin->assignRole($data['roles']);
        return $admin;
    }

    public function update(User $admin, array $data)
    {
        if (!empty($data['roles']) && in_array('super_admin', $data['roles'])) {
            abort(403, 'Cannot assign super_admin role');
        }

        $admin->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'is_active' => $data['is_active'] ?? false,
        ]);

        if (!empty($data['password'])) {
            $admin->update(['password' => Hash::make($data['password'])]);
        }

        $admin->syncRoles($data['roles']);

        return $admin;
    }

    public function destroy(User $admin)
    {
        return $admin->delete();
    }

    public function show(User $admin)
    {
        $admin->load('roles.permissions');
        return $admin;
    }
}
