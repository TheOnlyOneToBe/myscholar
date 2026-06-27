<?php

namespace Modules\Config\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SystemSetting;
use Modules\Config\Requests\UpdateSchoolInfoRequest;

class SchoolInfoController extends Controller
{
    public function show(): JsonResponse
    {
        $this->authorize('config.view');

        $school = SchoolInfo::current();

        if (!$school) {
            return response()->json([
                'message' => 'Aucune information du lycée configurée. Exécutez: php artisan school:setup',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => $school,
        ]);
    }

    public function update(UpdateSchoolInfoRequest $request): JsonResponse
    {
        $this->authorize('config.school_info.edit');

        $school = SchoolInfo::current();

        if (!$school) {
            $school = SchoolInfo::create($request->validated());
        } else {
            $school->update($request->validated());
        }

        return response()->json([
            'message' => 'Informations du lycée mises à jour.',
            'data' => $school->fresh(),
        ]);
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $this->authorize('config.school_info.logo');

        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
        ], [
            'logo.required' => 'Le fichier logo est obligatoire.',
            'logo.mimes' => 'Le logo doit être au format JPEG ou PNG.',
            'logo.max' => 'Le logo ne doit pas dépasser 2 Mo.',
        ]);

        $school = SchoolInfo::current();

        if (!$school) {
            return response()->json([
                'message' => 'Configurez d\'abord les informations du lycée.',
            ], 422);
        }

        $path = $request->file('logo')->store('logos', 'public');

        $school->update(['logo_path' => $path]);

        return response()->json([
            'message' => 'Logo mis à jour.',
            'data' => ['logo_path' => $path],
        ]);
    }

    public function settings(): JsonResponse
    {
        $this->authorize('config.view');

        $settings = SystemSetting::all()
            ->groupBy('group')
            ->map(fn ($group) => $group->pluck('value', 'key'));

        return response()->json([
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $this->authorize('config.settings.edit');

        $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($request->input('settings') as $item) {
            SystemSetting::set(
                $item['key'],
                $item['value'],
                $item['type'] ?? 'string',
                $item['group'] ?? 'general'
            );
        }

        return response()->json([
            'message' => 'Paramètres mis à jour.',
        ]);
    }
}
