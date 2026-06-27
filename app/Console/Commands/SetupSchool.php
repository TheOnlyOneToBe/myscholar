<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupSchool extends Command
{
    protected $signature = 'school:setup';

    protected $description = 'Configure les informations du lycée (branding) de manière interactive';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   CONFIGURATION DU LYCÉE - MyScholar    ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->newLine();

        // Identité
        $this->info('── IDENTITÉ ──');
        $name = $this->ask('Nom du lycée', 'Lycée National');
        $acronym = $this->ask('Sigle / Acronyme (ex: LNY)', '');
        $motto = $this->ask('Devise du lycée', '');
        $schoolType = $this->choice(
            'Type d\'établissement',
            ['public', 'prive', 'confessionnel'],
            'prive'
        );

        $this->newLine();

        // Coordonnées
        $this->info('── COORDONNÉES ──');
        $address = $this->ask('Adresse', '');
        $city = $this->ask('Ville', 'Yaoundé');
        $region = $this->ask('Région', 'Centre');
        $phone = $this->ask('Téléphone (ex: +237 6XX XXX XXX)', '');
        $email = $this->ask('Email', '');
        $website = $this->ask('Site web', '');
        $poBox = $this->ask('Boîte postale (BP)', '');

        $this->newLine();

        // Infos administratives
        $this->info('── INFORMATIONS ADMINISTRATIVES ──');
        $approvalNumber = $this->ask('N° d\'agrément', '');
        $creationDecree = $this->ask('Arrêté de création', '');
        $founderName = $this->ask('Nom du fondateur', '');
        $directorName = $this->ask('Nom du directeur', '');
        $foundationYear = $this->ask('Année de fondation (ex: 1985)', '');

        // Résumé
        $this->newLine();
        $this->info('── RÉSUMÉ ──');
        $this->table(
            ['Champ', 'Valeur'],
            [
                ['Nom', $name],
                ['Sigle', $acronym ?: '—'],
                ['Devise', $motto ?: '—'],
                ['Type', $schoolType],
                ['Ville', $city ?: '—'],
                ['Région', $region ?: '—'],
                ['Téléphone', $phone ?: '—'],
                ['Email', $email ?: '—'],
                ['Directeur', $directorName ?: '—'],
                ['Fondation', $foundationYear ?: '—'],
            ]
        );

        if (!$this->confirm('Confirmer ces informations ?', true)) {
            $this->warn('Configuration annulée.');
            return self::SUCCESS;
        }

        $data = [
            'name' => $name,
            'acronym' => $acronym ?: null,
            'motto' => $motto ?: null,
            'school_type' => $schoolType,
            'address' => $address ?: null,
            'city' => $city ?: null,
            'region' => $region ?: null,
            'phone' => $phone ?: null,
            'email' => $email ?: null,
            'website' => $website ?: null,
            'po_box' => $poBox ?: null,
            'approval_number' => $approvalNumber ?: null,
            'creation_decree' => $creationDecree ?: null,
            'founder_name' => $founderName ?: null,
            'director_name' => $directorName ?: null,
            'foundation_year' => $foundationYear ?: null,
            'updated_at' => now(),
        ];

        $existing = DB::table('school_info')->first();

        if ($existing) {
            DB::table('school_info')->where('id', $existing->id)->update($data);
            $this->info('✅ Informations du lycée mises à jour.');
        } else {
            $data['created_at'] = now();
            DB::table('school_info')->insert($data);
            $this->info('✅ Informations du lycée enregistrées.');
        }

        // Seed default system settings
        $this->seedDefaultSettings();

        return self::SUCCESS;
    }

    protected function seedDefaultSettings(): void
    {
        $defaults = [
            ['key' => 'timezone', 'value' => 'Africa/Douala', 'type' => 'string', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'FCFA', 'type' => 'string', 'group' => 'general'],
            ['key' => 'language', 'value' => 'fr', 'type' => 'string', 'group' => 'general'],
            ['key' => 'date_format', 'value' => 'd/m/Y', 'type' => 'string', 'group' => 'general'],
            ['key' => 'max_students_per_class', 'value' => '45', 'type' => 'integer', 'group' => 'academic'],
            ['key' => 'current_academic_year', 'value' => null, 'type' => 'string', 'group' => 'academic'],
        ];

        foreach ($defaults as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->info('✅ Paramètres système par défaut initialisés.');
    }
}
