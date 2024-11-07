<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Audit;

class AuditController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('List Audit'), only:['index', 'data']),
        ];
    }

    public function index()
    {
        return Inertia::render('Backend/Audit');
    }

    function data()
    {
        $data = Audit::with('user')->get();

        return response()->json($data);
    }
}
