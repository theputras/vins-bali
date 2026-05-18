<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'global_settings' => function () {
                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
                $jsonKeys = ['terms_and_conditions', 'home_usps', 'home_services', 'rental_requirements', 'home_brand_logos', 'seo_settings'];
                foreach ($jsonKeys as $k) {
                    if (isset($settings[$k])) {
                        $decoded = @json_decode($settings[$k], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $settings[$k] = $decoded;
                        }
                    }
                }
                return $settings;
            },
            'exchange_rate' => \Illuminate\Support\Facades\Cache::remember('exchange_rate_idr_usd', 3600, function () {
                try {
                    $response = json_decode(file_get_contents('https://open.er-api.com/v6/latest/IDR'), true);
                    return $response['rates']['USD'] ?? 0.000063;
                } catch (\Exception $e) {
                    return 0.000063; // Fallback rate
                }
            }),
        ];
    }
}
