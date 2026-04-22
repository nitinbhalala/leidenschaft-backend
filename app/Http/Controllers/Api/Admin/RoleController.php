<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends BaseController
{
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
                'created_at'  => $role->created_at,
            ];
        });

        return $this->success(['roles' => $roles], 'Roles retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:125|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name'       => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->success([
            'role' => [
                'id'          => $role->id,
                'name'        => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ]
        ], 'Role created successfully.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return $this->error('Role not found.', 404);
        }

        return $this->success([
            'role' => [
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
                'created_at'  => $role->created_at,
            ]
        ], 'Role retrieved successfully.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error('Role not found.', 404);
        }

        $request->validate([
            'name'          => 'sometimes|string|max:125|unique:roles,name,' . $id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($request->filled('name')) {
            $role->update(['name' => $request->name]);
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions ?? []);
        }

        return $this->success([
            'role' => [
                'id'          => $role->id,
                'name'        => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ]
        ], 'Role updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error('Role not found.', 404);
        }

        $role->delete();

        return $this->success([], 'Role deleted successfully.');
    }

    public function assignPermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error('Role not found.', 404);
        }

        $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->givePermissionTo($request->permissions);

        return $this->success([
            'permissions' => $role->fresh()->permissions->pluck('name'),
        ], 'Permissions assigned to role successfully.');
    }

    public function revokePermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error('Role not found.', 404);
        }

        $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        foreach ($request->permissions as $permission) {
            $role->revokePermissionTo($permission);
        }

        return $this->success([
            'permissions' => $role->fresh()->permissions->pluck('name'),
        ], 'Permissions revoked from role successfully.');
    }

    public function syncPermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error('Role not found.', 404);
        }

        $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions);

        return $this->success([
            'permissions' => $role->fresh()->permissions->pluck('name'),
        ], 'Role permissions synced successfully.');
    }

    public function assignToUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->assignRole($request->roles);

        return $this->success([
            'roles' => $user->fresh()->getRoleNames(),
        ], 'Roles assigned to user successfully.');
    }

    public function revokeFromUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        foreach ($request->roles as $role) {
            $user->removeRole($role);
        }

        return $this->success([
            'roles' => $user->fresh()->getRoleNames(),
        ], 'Roles revoked from user successfully.');
    }

    public function syncUserRoles(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->syncRoles($request->roles);

        return $this->success([
            'roles' => $user->fresh()->getRoleNames(),
        ], 'User roles synced successfully.');
    }

    public function userRoles(int $userId): JsonResponse
    {
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return $this->error('User not found.', 404);
        }

        return $this->success([
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ], 'User roles and permissions retrieved.');
    }
}
