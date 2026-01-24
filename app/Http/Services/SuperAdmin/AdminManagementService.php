<?php

namespace App\Http\Services\SuperAdmin;

use App\Models\User;
use App\Enum\UserRoleEnum;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminManagementService
{
    public function index(array $filters = [])
    {
        $query = User::where('role', UserRoleEnum::ADMIN)
            ->with('roles', 'permissions');

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
        return Permission::where('guard_name', 'web')->get()->groupBy(function($permission) {
            // تجميع الصلاحيات حسب آخر كلمة في الاسم (مثل: "create coupon" -> "coupon")
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? ucfirst(end($parts)) : ucfirst($parts[0]);
        });
    }

    public function store(array $data)
    {
        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'role' => UserRoleEnum::ADMIN,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        if (!empty($data['permissions'])) {
            $admin->givePermissionTo($data['permissions']);
        }

        return $admin;
    }

    public function update(User $admin, array $data)
    {
        $admin->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'is_active' => $data['is_active'] ?? false,
        ]);

        if (!empty($data['password'])) {
            $admin->update(['password' => Hash::make($data['password'])]);
        }

        $admin->syncPermissions($data['permissions'] ?? []);

        return $admin;
    }

    public function destroy(User $admin)
    {
        return $admin->delete();
    }

    public function getAdminPermissions(User $admin)
    {
        return $admin->permissions->pluck('name')->toArray();
    }

    public function validateAdmin(User $admin)
    {
        if ($admin->role !== UserRoleEnum::ADMIN) {
            abort(404);
        }
    }
}
