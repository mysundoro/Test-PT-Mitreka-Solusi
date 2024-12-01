<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\{Menu, Configuration, LanguageCode};
use Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            'auth' => [
                'user' => $request->user(),
            ],
            'user.roles' => $request->user() ? $request->user()->roles->pluck('name') : [],
            'user.permissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name') : [],
            'user.permissions_id' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('id') : [],
            'menu' => Menu::all(),
            'configuration' => Configuration::all()->mapWithKeys(function ($item) {
                return [$item->key => [
                    'value' => $item->value,
                    'description' => $item->description
                ]];
            }),
            'locale' => fn () => app()->getLocale(),
            'translations' => fn () => session('translations', []),
            'language' => LanguageCode::orderBy('id', 'ASC')->get(),
        ];
    }
}
