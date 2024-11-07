<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('List Menu'), only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Create Menu'), only:['create', 'store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Edit Menu'), only:['update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Delete Menu'), only:['destroy']),
        ];
    }

    public function index()
    {
        return Inertia::render('Backend/Setting/Menu/Index');
    }

    public function create()
    {
        $permissions = Permission::orderBy('id_permissions', 'asc')->get();

        return Inertia::render('Backend/Setting/Menu/Create', [
            'permissions' => $permissions
        ]);
    }

    public function getMenu($category)
    {
        $data = Menu::where('category', $category)->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'category' => 'required'
        ]);

        $input = $request->all();

        $data = Menu::create($input);

        sleep(1);

        return redirect()->route('menu.index')->with('message', 'Data added succesfully');
    }

    public function edit($id)
    {
        $data = Menu::find($id);
        $permissions = Permission::orderBy('id_permissions', 'asc')->get();

        return Inertia::render('Backend/Setting/Menu/Edit', [
            'data' => $data,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'category' => 'required'
        ]);

        $input = $request->all();

        Menu::find($id)->update($input);

        sleep(1);

        return redirect()->route('menu.index')->with('message', 'Data updated succesfully');
    }

    public function destroy($id)
    {
        $data = Menu::find($id);
        $data->delete();

        sleep(1);

        return redirect()->route('menu.index')->with('message', 'Data deleted succesfully');
    }
}
