<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\{LanguageCode, Language};

class LanguageCodeController extends Controller
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
        return Inertia::render('Backend/Setting/LanguageCode/Index');
    }

    function data()
    {
        $data = LanguageCode::orderBy('name', 'asc')->get();

        // Map through language codes and check if the code exists in the Language table
        $result = $data->map(function ($languageCode) {
            // Check if the code exists in the Language table
            $exists = Language::where('locale', $languageCode->code)->exists();
            
            // Return the language code along with the existence status
            return [
                'id' => $languageCode->id,
                'code' => $languageCode->code,
                'name' => $languageCode->name,
                'exists_in_language' => $exists // Boolean indicating if the code exists in Language table
            ];
        });

        return response()->json($result);
    }

    public function create()
    {
        return Inertia::render('Backend/Setting/LanguageCode/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|unique:language_code,code',
            'name' => 'required',
        ]);

        $input = $request->all();

        LanguageCode::create($input);

        sleep(1);

        return redirect()->route('language_code.index')->with('message', 'Data added succesfully');
    }

    public function edit($id)
    {
        $data = LanguageCode::find($id);

        return Inertia::render('Backend/Setting/LanguageCode/Edit', [
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'code' => 'required|unique:language_code,code,' . $id,
            'name' => 'required',
        ]);

        $data = LanguageCode::find($id);
        $input = $request->all();
        $data->update($input);

        sleep(1);

        return redirect()->route('language_code.index')->with('message', 'Data updated succesfully');
    }

    public function destroy($id)
    {
        $data = LanguageCode::find($id);
        
        if ($data) {
            Language::where('locale', $data->code)->delete();

            $data->delete();

            sleep(1);

            return redirect()->route('language_code.index')->with('message', 'Data deleted successfully');
        }

        return redirect()->route('language_code.index')->with('error', 'Data not found');
    }

    // Create Language if Not Exist
    function createLanguage($code)
    {
        // Check if the language already exists
        $languageExists = Language::where('locale', $code)->exists();

        if ($languageExists) {
            return redirect()->route('language_code.index')->with('error', 'Language already exist');
        }

        // Get all rows from the 'en' locale to use as template
        $englishLanguageRows = Language::where('locale', 'en')->get();

        // Insert new rows for the new locale based on 'en' template
        foreach ($englishLanguageRows as $row) {
            Language::create([
                'key'    => $row->key, // Copy the key from 'en'
                'locale' => $code,     // Set the new locale code
                'value'  => $row->value, // Copy the value from 'en'
            ]);
        }

        return redirect()->route('language_code.index')->with('error', 'Language created successfully');
    }
}
