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
}
