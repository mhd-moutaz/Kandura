<?php

namespace App\Http\Services\SuperAdmin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;

class RoleManagementService
{
    /**
     * Get paginated list of roles with filters
     */
    public function index(array $filters = [])
    {
        $query = Role::with('permissions')
            ->where('guard_name', 'web'); // Only web guard roles for admin panel

        // Search filter
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        // Guard filter
        if (!empty($filters['guard_name'])) {
            $query->where('guard_name', $filters['guard_name']);
        }

        // Sorting
        $sortDir = $filters['sort_dir'] ?? 'asc';
        $query->orderBy('name', $sortDir);

        return $query->paginate(15);
    }

    /**
     * Get all permissions grouped by resource (last word)
     */
    public function getAllPermissions(string $guardName = 'web')
    {
        return Permission::where('guard_name', $guardName)
            ->orderBy('name')
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

    /**
     * Create new role with permissions
     */
    public function store(array $data)
    {
        try {
            DB::beginTransaction();



            // Validate: prevent duplicate role names per guard
            if (Role::where('name', $data['name'])->where('guard_name', 'web')->exists()) {
                throw new GeneralException('Role already exists.');
            }

            // Create role
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            // Assign permissions
            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Update role and its permissions
     */
    public function update(Role $role, array $data)
    {
        try {
            DB::beginTransaction();

            // Prevent editing system roles names
            if ($this->isSystemRole($role) && isset($data['name']) && $data['name'] !== $role->name) {
                throw new GeneralException('Cannot change system role name.');
            }

            // Update role name if provided
            if (isset($data['name'])) {
                $role->update(['name' => $data['name']]);
            }

            // Sync permissions
            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role->fresh('permissions');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update role: ' . $e->getMessage());
        }
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        try {
            // Prevent deletion of system roles
            if ($this->isSystemRole($role)) {
                throw new GeneralException('Cannot delete system role.');
            }

            // Check if role is assigned to any users
            if ($role->users()->count() > 0) {
                throw new GeneralException('Cannot delete role that is assigned to users.');
            }

            DB::beginTransaction();
            $role->delete();
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Get role with permissions
     */
    public function show(Role $role)
    {
        return $role->load('permissions', 'users');
    }

    /**
     * Check if role is a system role (cannot be deleted)
     */
    protected function isSystemRole(Role $role): bool
    {
        $systemRoles = ['admin', 'super_admin'];
        return in_array($role->name, $systemRoles);
    }

    /**
     * Get role permissions as array of IDs
     */
    public function getRolePermissionIds(Role $role): array
    {
        return $role->permissions()->pluck('id')->toArray();
    }

}
