<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends BaseController
{
    public function index(): JsonResponse
    {
        $permissions = Permission::all(['id', 'name', 'guard_name', 'created_at']);

        return $this->success(['permissions' => $permissions], 'Permissions retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'  => 'required|string|max:125|unique:permissions,name',
            'group' => 'nullable|string|max:100',
        ]);

        $name = $request->filled('group')
            ? $request->group . '.' . $request->name
            : $request->name;

        $permission = Permission::create([
            'name'       => $name,
            'guard_name' => 'web',
        ]);

        return $this->success([
            'permission' => [
                'id'   => $permission->id,
                'name' => $permission->name,
            ]
        ], 'Permission created successfully.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->error('Permission not found.', 404);
        }

        return $this->success(['permission' => $permission], 'Permission retrieved successfully.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->error('Permission not found.', 404);
        }

        $request->validate([
            'name' => 'required|string|max:125|unique:permissions,name,' . $id,
        ]);

        $permission->update(['name' => $request->name]);

        return $this->success(['permission' => $permission], 'Permission updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->error('Permission not found.', 404);
        }

        $permission->delete();

        return $this->success([], 'Permission deleted successfully.');
    }

    public function grouped(): JsonResponse
    {
        $permissions = Permission::all(['id', 'name', 'guard_name', 'created_at'])->groupBy(function ($p) {
            $parts = explode('.', $p->name, 2);
            return count($parts) > 1 ? $parts[0] : 'general';
        });

        return $this->success(['permissions' => $permissions], 'Grouped permissions retrieved.');
    }
}
