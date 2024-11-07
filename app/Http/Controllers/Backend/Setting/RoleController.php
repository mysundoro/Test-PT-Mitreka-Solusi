<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('List Role'), only:['index', 'data']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Create Role'), only:['create', 'store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Edit Role'), only:['update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Delete Role'), only:['destroy']),
        ];
    }

    public function index()
    {
        return Inertia::render('Backend/Setting/Roles/Index');
    }

    function data()
    {
        $data = Role::select('*')->orderBy('id', 'desc')->get();

        return response()->json($data);
    }

    public function create()
    {
        $permissions = Permission::orderBy('id_permissions', 'asc')->get();

        return Inertia::render('Backend/Setting/Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required'
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        sleep(1);

        return redirect()->route('roles.index')->with('message', 'Data added succesfully');
    }

    public function edit($id)
    {
        $data = Role::find($id);
        $permissions = Permission::orderBy('id', 'asc')->get();
        $rolePermission = \DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return Inertia::render('Backend/Setting/Roles/Edit', [
            'data' => $data,
            'permissions' => $permissions,
            'rolePermission' => $rolePermission
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required'
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permissions'));

        sleep(1);

        return redirect()->route('roles.index')->with('message', 'Data updated succesfully');
    }

    public function destroy($id)
    {
        $data = Role::find($id);
        $data->delete();

        sleep(1);

        return redirect()->route('roles.index')->with('message', 'Data deleted succesfully');
    }
}
