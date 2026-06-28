<?php

namespace Modules\Dashboard\Services;

use App\Services\ModuleManager;

class ModuleAvailabilityService
{
    public function __construct(
        private ModuleManager $moduleManager
    ) {}

    /**
     * Get list of available modules for a specific role's dashboard
     */
    public function getAvailableModulesForRole(string $role): array
    {
        $availableModules = [];

        $modulesByRole = $this->getModulesByRole($role);

        foreach ($modulesByRole as $moduleName) {
            if ($this->moduleManager->canUseModule($moduleName)) {
                $status = $this->moduleManager->getModuleStatus($moduleName);
                $availableModules[$moduleName] = [
                    'name' => $moduleName,
                    'active' => $status['active'],
                    'available' => true,
                    'error' => null,
                ];
            } else {
                $availableModules[$moduleName] = [
                    'name' => $moduleName,
                    'active' => false,
                    'available' => false,
                    'error' => $this->moduleManager->getModuleError($moduleName),
                ];
            }
        }

        return $availableModules;
    }

    /**
     * Check if a specific module is available for a role
     */
    public function isModuleAvailableForRole(string $moduleName, string $role): bool
    {
        if (!$this->moduleManager->canUseModule($moduleName)) {
            return false;
        }

        $modulesByRole = $this->getModulesByRole($role);
        return in_array($moduleName, $modulesByRole);
    }

    /**
     * Get modules by role
     */
    private function getModulesByRole(string $role): array
    {
        $modules = [
            'student' => [
                'Students',
                'Grades',
                'Attendance',
                'Classes',
                'Billing',
                'Notifications',
                'Reporting',
            ],
            'chef_classe' => [
                'Students',
                'Grades',
                'Attendance',
                'Classes',
                'Notifications',
            ],
            'enseignant' => [
                'Students',
                'Grades',
                'Attendance',
                'Classes',
                'Notifications',
            ],
            'surveillant' => [
                'Students',
                'Attendance',
                'Classes',
            ],
            'prof_principal' => [
                'Students',
                'Grades',
                'Attendance',
                'Classes',
                'Notifications',
                'Reporting',
            ],
            'censeur' => [
                'Students',
                'Grades',
                'Attendance',
                'Classes',
                'Audit',
                'Reporting',
            ],
            'proviseur' => [
                'Students',
                'Grades',
                'Attendance',
                'Classes',
                'Billing',
                'Audit',
                'Reporting',
                'Notifications',
            ],
            'parent' => [
                'Students',
                'Grades',
                'Attendance',
                'Billing',
            ],
        ];

        return $modules[$role] ?? [];
    }

    /**
     * Get all modules with their availability status
     */
    public function getModulesStatus(): array
    {
        return $this->moduleManager->getAllModulesStatus();
    }

    /**
     * Verify if a module can be used before displaying dashboard component
     */
    public function canDisplayComponent(string $moduleName): bool
    {
        return $this->moduleManager->canUseModule($moduleName);
    }

    /**
     * Get module error message
     */
    public function getModuleErrorMessage(string $moduleName): string
    {
        return $this->moduleManager->getModuleError($moduleName) ?? "Module not available";
    }

    /**
     * Check if all required modules for a feature are available
     */
    public function areModulesAvailable(array $requiredModules): bool
    {
        foreach ($requiredModules as $moduleName) {
            if (!$this->moduleManager->canUseModule($moduleName)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check which modules are missing for a feature
     */
    public function getMissingModules(array $requiredModules): array
    {
        $missing = [];
        foreach ($requiredModules as $moduleName) {
            if (!$this->moduleManager->canUseModule($moduleName)) {
                $missing[] = $moduleName;
            }
        }
        return $missing;
    }

    /**
     * Get feature availability status
     *
     * Returns: ['available' => bool, 'missing_modules' => array]
     */
    public function checkFeatureAvailability(string $feature): array
    {
        $featureRequirements = [
            'grades_charts' => ['Grades'],
            'subject_analysis' => ['Grades'],
            'progression_timeline' => ['Grades'],
            'class_comparison' => ['Grades'],
            'term_comparison' => ['Grades'],
            'bulletins' => ['Grades'],
            'attendance_tracking' => ['Attendance'],
            'billing_info' => ['Billing'],
            'notifications' => ['Notifications'],
            'alerts' => ['Grades', 'Attendance', 'Billing'],
            'schedule' => ['Classes'],
        ];

        if (!isset($featureRequirements[$feature])) {
            return ['available' => false, 'missing_modules' => [$feature . ' feature not found']];
        }

        $required = $featureRequirements[$feature];
        $missing = $this->getMissingModules($required);

        return [
            'available' => empty($missing),
            'missing_modules' => $missing,
            'required_modules' => $required,
        ];
    }

    /**
     * Log when a feature is unavailable due to disabled modules
     */
    public function logFeatureUnavailable(string $feature, array $missingModules): void
    {
        \Illuminate\Support\Facades\Log::warning(
            "Fonctionnalité indisponible: $feature - Modules désactivés: " . implode(', ', $missingModules)
        );
    }
}
