<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PermissionService;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync {--roles : Synchroniser aussi les rôles}';
    protected $description = 'Synchroniser les permissions et rôles depuis la configuration';

    public function handle(PermissionService $permissionService)
    {
        $this->info('Synchronisation des permissions et rôles...');
        $this->newLine();

        try {
            $this->info('📋 Synchronisation des permissions...');
            $permissionService->syncPermissionsFromConfig();
            $this->line('<fg=green>✓ Permissions synchronisées</>');

            if ($this->option('roles')) {
                $this->newLine();
                $this->info('👤 Synchronisation des rôles...');
                $permissionService->syncRolesFromConfig();
                $this->line('<fg=green>✓ Rôles synchronisés</>');
            }

            $this->newLine();
            $this->info('✓ Synchronisation terminée avec succès!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Erreur: ' . $e->getMessage());
            return 1;
        }
    }
}
