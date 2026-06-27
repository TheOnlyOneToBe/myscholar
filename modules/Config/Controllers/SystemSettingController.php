<?php

namespace Modules\Config\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Config\Models\SystemSetting;

class SystemSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = SystemSetting::all()
            ->groupBy('group')
            ->map(fn ($group) => $group->map(fn ($s) => [
                'id' => $s->id,
                'key' => $s->key,
                'value' => $s->value,
                'type' => $s->type,
                'group' => $s->group,
            ]));

        return response()->json([
            'data' => $settings,
        ]);
    }

    public function getByGroup(string $group): JsonResponse
    {
        $settings = SystemSetting::where('group', $group)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'key' => $s->key,
                'value' => $s->value,
                'type' => $s->type,
            ]);

        if ($settings->isEmpty()) {
            return response()->json([
                'message' => "Aucun paramètre trouvé pour le groupe: {$group}",
                'data' => [],
            ], 404);
        }

        return response()->json([
            'data' => $settings,
        ]);
    }

    public function getSetting(string $key): JsonResponse
    {
        $setting = SystemSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'message' => "Paramètre '{$key}' non trouvé",
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => $setting,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'key' => ['required', 'string', 'unique:system_settings'],
            'value' => ['nullable'],
            'type' => ['required', 'in:string,integer,boolean,json'],
            'group' => ['required', 'string'],
        ], [
            'key.unique' => 'Ce paramètre existe déjà.',
        ]);

        $setting = SystemSetting::create($request->validated());

        return response()->json([
            'message' => 'Paramètre créé avec succès.',
            'data' => $setting,
        ], 201);
    }

    public function update(Request $request, SystemSetting $setting): JsonResponse
    {
        $request->validate([
            'value' => ['nullable'],
            'type' => ['required', 'in:string,integer,boolean,json'],
        ]);

        $setting->update($request->validated());

        return response()->json([
            'message' => 'Paramètre mis à jour avec succès.',
            'data' => $setting->fresh(),
        ]);
    }

    public function destroy(SystemSetting $setting): JsonResponse
    {
        $setting->delete();

        return response()->json([
            'message' => 'Paramètre supprimé avec succès.',
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable'],
            'settings.*.type' => ['required', 'in:string,integer,boolean,json'],
            'settings.*.group' => ['required', 'string'],
        ]);

        $updated = [];
        foreach ($request->input('settings') as $item) {
            $setting = SystemSetting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'type' => $item['type'],
                    'group' => $item['group'],
                ]
            );
            $updated[] = $setting;
        }

        return response()->json([
            'message' => 'Paramètres mis à jour avec succès.',
            'data' => $updated,
        ]);
    }
}
