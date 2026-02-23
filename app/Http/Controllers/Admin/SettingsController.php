<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->middleware('role.web:admin');
        $this->settingsService = $settingsService;
    }

    /**
     * Display a listing of all settings grouped by category.
     */
    public function index(): View
    {
        $settings = Settings::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Show the form for editing settings.
     */
    public function edit(string $group = 'general'): View
    {
        $settings = Settings::where('group', $group)
            ->orderBy('key')
            ->get();

        $groups = Settings::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();

        return view('admin.settings.edit', compact('settings', 'group', 'groups'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request, string $group): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Settings::where('key', $key)
                ->where('group', $group)
                ->first();

            if ($setting) {
                // Handle file uploads for logo_url
                if ($key === 'logo_url' && $request->hasFile('settings.logo_url_file')) {
                    $file = $request->file('settings.logo_url_file');
                    $path = $file->store('settings', 'public');
                    $value = asset('storage/' . $path);
                }

                // Convert arrays/objects to JSON if needed
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }

                $this->settingsService->set(
                    $key,
                    $value,
                    $setting->type,
                    $setting->group,
                    $setting->description
                );
            }
        }

        return redirect()
            ->route('admin.settings.edit', $group)
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Update a single setting via AJAX.
     */
    public function updateSingle(Request $request, string $key)
    {
        $request->validate([
            'value' => 'nullable',
        ]);

        $setting = Settings::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], 404);
        }

        $value = $request->input('value');

        // Handle file uploads
        if ($request->hasFile('value')) {
            $file = $request->file('value');
            $path = $file->store('settings', 'public');
            $value = asset('storage/' . $path);
        }

        // Convert arrays/objects to JSON if needed
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $this->settingsService->set(
            $key,
            $value,
            $setting->type,
            $setting->group,
            $setting->description
        );

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'value' => $value,
        ]);
    }
}


