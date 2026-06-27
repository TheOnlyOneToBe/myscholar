<?php

namespace Modules\Students\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Config\Models\SystemSetting;
use Modules\Students\Requests\UpdateStudentIdFormatRequest;
use Modules\Students\ValueObjects\StudentIdFormatConfig;

class StudentIdFormatController extends Controller
{
    public function show(): JsonResponse
    {
        $setting = SystemSetting::where('key', 'student_id_format_config')->first();
        $config = $setting ? json_decode($setting->value, true) : null;

        $formatConfig = StudentIdFormatConfig::from($config ?? []);

        return response()->json([
            'elements' => $formatConfig->elements(),
            'separator' => $formatConfig->separator(),
            'pattern' => $formatConfig->toPattern(),
            'example' => $formatConfig->generateExample(),
            'available_elements' => $formatConfig->getAvailableElements(),
        ]);
    }

    public function update(UpdateStudentIdFormatRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $config = new StudentIdFormatConfig(
            $validated['elements'],
            $validated['separator'] ?? '-'
        );

        SystemSetting::updateOrCreate(
            ['key' => 'student_id_format_config'],
            [
                'value' => json_encode($config->toArray()),
                'type' => 'json',
                'group' => 'students',
            ]
        );

        return response()->json([
            'message' => 'Format des matricules mis à jour avec succès',
            'elements' => $config->elements(),
            'separator' => $config->separator(),
            'pattern' => $config->toPattern(),
            'example' => $config->generateExample(),
        ]);
    }

    public function getAvailableElements(): JsonResponse
    {
        $config = StudentIdFormatConfig::from([]);

        return response()->json([
            'available_elements' => $config->getAvailableElements(),
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'elements' => 'required|array|min:1',
            'elements.*' => 'string',
            'separator' => 'string',
        ]);

        try {
            $config = new StudentIdFormatConfig(
                $request->input('elements'),
                $request->input('separator', '-')
            );

            return response()->json([
                'pattern' => $config->toPattern(),
                'example' => $config->generateExample(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
