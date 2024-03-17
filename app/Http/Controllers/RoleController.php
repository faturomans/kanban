<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission; // Ditambahkan
use Illuminate\Support\Facades\DB; // Ditambahkan

class RoleController extends Controller
{
    public function index()
    {
        $pageTitle = 'Role Lists';
        $roles = Role::all();
        Gate::authorize('viewAny', Role::class);
        return view('roles.index', [
            'pageTitle' => $pageTitle,
            'roles' => $roles,
        ]);
    }


    public function create()
    {
        $pageTitle = 'Add Role';
        $permissions = Permission::all();
        return view('roles.create', [
            'pageTitle' => $pageTitle,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'permissionIds' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
            ]);

            $role->permissions()->sync($request->permissionIds);

            DB::commit();

            return redirect()->route('roles.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $pageTitle = 'Edit Role';
        Gate::authorize('update', $role);
        $permissions = Permission::all();

        return view('roles.edit', [
            'pageTitle' => $pageTitle,
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }



    public function update($id, Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'permissionIds' => 'nullable|array',
            'permissionIds.*' => 'exists:permissions,id',
        ]);
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $validatedData['name'],
        ]);
        if (isset($validatedData['permissionIds'])) {
            $role->permissions()->sync($validatedData['permissionIds']);
        } else {
            $role->permissions()->detach();
        }
        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    public function delete($id)
    {
        $pageTitle = 'Delete Role';
        $role = Role::findOrFail($id);
        Gate::authorize('delete', $role); // Ditambahkan
        $permissions = Permission::all();

        return view('roles.delete', [
            'pageTitle' => $pageTitle,
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            return redirect()->route('roles.index')->with('message-error', 'Cannot delete the role. There are users associated with it.');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
