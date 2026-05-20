<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index(): Response
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $jsonKeys = ['terms_and_conditions', 'home_usps', 'home_services', 'rental_requirements', 'home_brand_logos', 'seo_settings', 'home_faqs', 'home_testimonials'];
        
        foreach ($jsonKeys as $k) {
            if (isset($settings[$k])) {
                $decoded = @json_decode($settings[$k], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $settings[$k] = $decoded;
                } else {
                    $settings[$k] = $k === 'rental_requirements' ? ['tourist' => [], 'resident' => []] : [];
                }
            } else {
                if ($k === 'rental_requirements') {
                    $settings[$k] = ['tourist' => [], 'resident' => []];
                } else if ($k === 'terms_and_conditions') {
                    $settings[$k] = []; // Fallback for terms if it was old HTML string
                } else {
                    $settings[$k] = [];
                }
            }
        }

        return Inertia::render('admin/Settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update the settings in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_number' => ['required', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'company_address' => ['required', 'string', 'max:1000'],
            'company_email' => ['required', 'email', 'max:255'],
            'terms_and_conditions' => ['nullable', 'array'],
            'home_usps' => ['nullable', 'array'],
            'home_services' => ['nullable', 'array'],
            'rental_requirements' => ['nullable', 'array'],
            'home_brand_logos' => ['nullable', 'array'],
            'seo_settings' => ['nullable', 'array'],
            'favicon' => ['nullable', 'image', 'mimes:ico,png,jpg,jpeg,svg', 'max:2048'],
            'default_og_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'home_hero_title' => ['required', 'string', 'max:255'],
            'home_hero_highlight' => ['required', 'string', 'max:255'],
            'home_hero_subtitle' => ['required', 'string', 'max:1000'],
            'rental_requirements_footer' => ['required', 'string', 'max:1000'],
            'home_faqs' => ['nullable', 'array'],
            'home_testimonials' => ['nullable', 'array'],
            'founder_name' => ['nullable', 'string', 'max:255'],
            'founder_title' => ['nullable', 'string', 'max:255'],
            'founder_text' => ['nullable', 'string', 'max:5000'],
            'founder_photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        $seoSettings = $validated['seo_settings'] ?? [];

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            $seoSettings['favicon_path'] = $path;
        }

        if ($request->hasFile('default_og_image')) {
            $path = $request->file('default_og_image')->store('settings', 'public');
            $seoSettings['default_og_image_path'] = $path;
        }

        $validated['seo_settings'] = $seoSettings;

        unset($validated['favicon']);
        unset($validated['default_og_image']);

        if ($request->hasFile('founder_photo')) {
            $path = $request->file('founder_photo')->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'founder_photo_path'],
                ['value' => $path]
            );
        }
        unset($validated['founder_photo']);

        foreach ($validated as $key => $value) {
            $formattedValue = is_array($value) ? json_encode($value) : ($value ?? '');
            
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $formattedValue]
            );
        }

        \Illuminate\Support\Facades\Cache::forget('global_settings');

        return back()->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}
