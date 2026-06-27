<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PasswordService;

class HashPassword extends Command
{
    protected $signature = 'password:hash {password? : Le mot de passe à hacher}';
    protected $description = 'Hash un mot de passe et affiche le résultat';

    public function handle(PasswordService $passwordService)
    {
        $password = $this->argument('password');

        if (!$password) {
            $password = $this->secret('Entrez le mot de passe à hacher');
        }

        if (empty($password)) {
            $this->error('Le mot de passe ne peut pas être vide');
            return 1;
        }

        // Valider la force du mot de passe
        $strength = $passwordService->validateStrength($password);
        if (!$strength['valid']) {
            $this->warn('Avertissement: le mot de passe ne respecte pas les critères de sécurité:');
            foreach ($strength['errors'] as $error) {
                $this->line('  ✗ ' . $error);
            }

            if (!$this->confirm('Continuer malgré tout?')) {
                return 1;
            }
        }

        $strengthLevel = $passwordService->getStrengthLevel($password);
        $hash = $passwordService->hash($password);

        $this->newLine();
        $this->info('Résultat du hachage:');
        $this->line('');
        $this->line('<fg=cyan>Mot de passe hashé:</> ' . $hash);
        $this->line('<fg=green>Force:</> ' . $strengthLevel);
        $this->line('');
        $this->info('✓ Hash généré avec succès');

        return 0;
    }
}
