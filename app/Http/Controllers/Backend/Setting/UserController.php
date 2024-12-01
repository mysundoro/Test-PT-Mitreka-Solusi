<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\{User, Notification};
use Spatie\Permission\Models\Role;
use Auth;

class UserController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('List User'), only:['index', 'data']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Create User'), only:['create', 'store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Edit User'), only:['update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Delete User'), only:['destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::orderBy('name', 'ASC')->get();

        return Inertia::render('Backend/Setting/Users/Index', [
            'roles' => $roles
        ]);
    }

    function data()
    {
        $data = User::select('users.*',
            \DB::raw('(SELECT string_agg(DISTINCT roles.name, \', \') 
                    FROM roles 
                    INNER JOIN model_has_roles ON roles.id = model_has_roles.role_id 
                    WHERE model_has_roles.model_id = users.id) as grup'))
            ->orderBy('users.id', 'desc')
            ->get();

        return response()->json($data);
    }

    public function create()
    {
        $roles = Role::orderBy('name', 'ASC')->get();

        return Inertia::render('Backend/Setting/Users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        // Kirim Notification
        Notification::create([
            'users_id' => Auth::user()->id,
            'type' => 'Create User',
            'data' => 'Anda telah membuat pengguna baru dengan nama '.$user->name,
        ]);

        sleep(1);

        return redirect()->route('users.index')->with('message', 'Data added succesfully');
    }

    public function edit($id)
    {
        $data = User::find($id);
        $roles = Role::all(['id', 'name'])->toArray();
        $userRole = $data->roles->pluck('id','name')->all();

        return Inertia::render('Backend/Setting/Users/Edit', [
            'data' => $data,
            'roles' => $roles,
            'userRole' => $userRole
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $user = User::find($id);
        $user->update($input);
        
        \DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        sleep(1);

        return redirect()->route('users.index')->with('message', 'Data updated succesfully');
    }

    public function destroy($id)
    {
        $data = User::find($id);
        $data->delete();

        sleep(1);

        return redirect()->route('users.index')->with('message', 'Data deleted succesfully');
    }
}
