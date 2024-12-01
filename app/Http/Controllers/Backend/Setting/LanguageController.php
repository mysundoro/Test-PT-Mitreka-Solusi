<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Language;

class LanguageController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('List Language'), only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Create Language'), only:['create', 'store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Edit Language'), only:['update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('Delete Language'), only:['destroy']),
        ];
    }

    public function index()
    {
        $data = Language::all();

        return Inertia::render('Backend/Setting/Language/Index', [
            'data' => $data
        ]);
    }

    public function create()
    {
        return Inertia::render('Backend/Setting/Language/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'key' => 'required',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:2',
            'translations.*.value' => 'required|string',
        ]);

        foreach ($validatedData['translations'] as $translation) {
            Language::create([
                'key' => $validatedData['key'],
                'locale' => $translation['locale'],
                'value' => $translation['value'],
            ]);
        }

        sleep(1);

        return redirect()->route('language.index')->with('message', 'Data added succesfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'key' => 'required',
            'locale' => 'required|max:2',
            'value' => 'required|string',
        ]);

        $translation = Language::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $translation->update($validated);

        return response()->json(['message' => 'Data updated succesfully']);
    }
}