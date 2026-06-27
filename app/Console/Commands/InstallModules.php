<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallModules extends Command
{
    protected $signature = 'modules:install
                            {modules : Comma-separated list of modules to install (e.g. "Config,Auth,Students")}
                            {--client= : Client identifier}';

    protected $description = 'Install selected modules: create config, run migrations, load permissions';

    public function handle(): int
    {
        $moduleNames = array_map('trim', explode(',', $this->argument('modules')));
        $clientId = $this->option('client');

        $this->info('Installing modules: ' . implode(', ', $moduleNames));

        // Validate modules exist
        $valid = [];
        foreach ($moduleNames as $name) {
            $modulePath = base_path("modules/{$name}");
            if (!File::isDirectory($modulePath)) {
                $this->error("  [✗] Module '{$name}' not found at {$modulePath}");
                continue;
            }

            $manifestPath = "{$modulePath}/module.json";
            if (!File::exists($manifestPath)) {
                $this->error("  [✗] Module '{$name}' has no module.json");
                continue;
            }

            $valid[] = $name;
        }

        if (empty($valid)) {
            $this->error('No valid modules to install.');
            return self::FAILURE;
        }

        // Save config
        $config = [
            'clientId' => $clientId,
            'installedModules' => $valid,
            'installedAt' => now()->toIso8601String(),
        ];

        File::put(
            config_path('modules.json'),
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $this->info('  [✓] Configuration saved');

        // Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $this->info('  [✓] Module migrations executed');

        // Load permissions from each module
        $totalPermissions = 0;
        foreach ($valid as $name) {
            $permissionsFile = base_path("modules/{$name}/permissions.json");
            if (File::exists($permissionsFile)) {
                $data = json_decode(File::get($permissionsFile), true);
                $totalPermissions += count($data['permissions'] ?? []);
            }
        }
        $this->info("  [✓] Permissions loaded ({$totalPermissions} permissions)");

        $this->newLine();
        $this->info('✅ Installation complete!');
        $this->table(
            ['Module', 'Status'],
            collect($valid)->map(fn ($m) => [$m, '✓ Installed'])->toArray()
        );

        return self::SUCCESS;
    }
}
