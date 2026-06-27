<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $modules = $this->getInstalledModules();

        foreach ($modules as $moduleName) {
            $this->loadModule($moduleName);
        }

        $this->loadMigrationsFrom(base_path('bridges'));
    }

    protected function getInstalledModules(): array
    {
        $configPath = config_path('modules.json');

        if (!File::exists($configPath)) {
            return [];
        }

        $config = json_decode(File::get($configPath), true);

        return $config['installedModules'] ?? [];
    }

    protected function loadModule(string $moduleName): void
    {
        $modulePath = base_path("modules/{$moduleName}");

        if (!File::isDirectory($modulePath)) {
            return;
        }

        $migrationsPath = "{$modulePath}/migrations";
        if (File::isDirectory($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }

        $routesFile = "{$modulePath}/Routes/api.php";
        if (File::exists($routesFile)) {
            $this->loadRoutesFrom($routesFile);
        }
    }

    protected function loadBridges(): void
    {
        $bridgesPath = base_path('bridges');

        if (!File::isDirectory($bridgesPath)) {
            return;
        }

        $bridgeFiles = File::files($bridgesPath);
        $bridgeFiles = collect($bridgeFiles)
            ->sortBy(fn ($file) => $file->getFilename())
            ->map(fn ($file) => $bridgesPath . '/' . $file->getFilename());

        foreach ($bridgeFiles as $file) {
            if ($file->endsWith('.php')) {
                $this->loadMigrationsFrom(dirname($file));
                break;
            }
        }

        // Alternative: load all bridges directly
        $this->loadMigrationsFrom($bridgesPath);
    }

    public static function getAllModuleDirectories(): array
    {
        $modulesPath = base_path('modules');

        if (!File::isDirectory($modulesPath)) {
            return [];
        }

        return collect(File::directories($modulesPath))
            ->map(fn (string $path) => basename($path))
            ->toArray();
    }
}
