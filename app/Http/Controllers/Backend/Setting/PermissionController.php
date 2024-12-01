<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('List Permission'), only:['index', 'data']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Create Permission'), only:['create', 'store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Edit Permission'), only:['update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Delete Permission'), only:['destroy']),
        ];
    }

    public function index()
    {
        return Inertia::render('Backend/Setting/Permissions/Index');
    }

    function data()
    {
        $data = Permission::select('*')
            ->orderBy('id', 'desc')
            ->get();

        $rowNumber = 1;
        $data->each(function ($item) use (&$rowNumber) {
            $item->row_number = $rowNumber;
            $rowNumber++;
        });

        return response()->json($data);
    }

    public function create()
    {
        return Inertia::render('Backend/Setting/Permissions/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->input('name')]);

        sleep(1);

        return redirect()->route('permissions.index')->with('message', 'Data added succesfully');
    }

    public function edit($id)
    {
        $data = Permission::find($id);

        return Inertia::render('Backend/Setting/Permissions/Edit', [
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $permission = Permission::find($id);
        $permission->name = $request->input('name');
        $permission->save();

        sleep(1);

        return redirect()->route('permissions.index')->with('message', 'Data updated succesfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        if($permission) {
            return redirect()->route('permissions.index')->with('message', 'Data deleted succesfully');
        }
    }
}