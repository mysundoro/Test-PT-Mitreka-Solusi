<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Configuration'), only:['index', 'store']),
        ];
    }

    public function index()
    {
        $data = Configuration::all()->mapWithKeys(function ($item) {
            return [$item->key => [
                'value' => $item->value,
                'description' => $item->description
            ]];
        });

        return Inertia::render('Backend/Setting/Configuration/Index', [
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_name' => 'required',
            'logo_system' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'login_logo' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'favicon' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'timezone' => 'required',
        ]);

        $datas = [
            ['key' => 'application_name', 'value' => $request->input('application_name'), 'description' => null],
            ['key' => 'timezone', 'value' => $request->input('timezone'), 'description' => null],
            ['key' => 'maintenance_mode', 'value' => $request->input('maintenance_mode'), 'description' => null],
        ];

        if ($request->hasFile('logo_system')) {
            $logoPath = $request->file('logo_system')->store('configuration', 'public');
            $datas[] = ['key' => 'logo_system', 'value' => $logoPath, 'description' => null];
        }

        if ($request->hasFile('login_logo')) {
            $logo2Path = $request->file('login_logo')->store('configuration', 'public');
            $datas[] = ['key' => 'login_logo', 'value' => $logo2Path, 'description' => null];
        }

        if ($request->hasFile('favicon')) {
            $logo3Path = $request->file('favicon')->store('configuration', 'public');
            $datas[] = ['key' => 'favicon', 'value' => $logo3Path, 'description' => null];
        }

        foreach ($datas as $data) {
            Configuration::updateOrCreate(
                ['key' => $data['key']],
                ['value' => $data['value'], 'description' => $data['description']]
            );
        }

        sleep(1);

        return redirect()->route('configuration.index')->with('message', 'Data updated succesfully');
    }
}
