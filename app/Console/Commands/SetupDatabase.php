<?php

namespace App\Console\Commands;

use App\Services\DatabaseConfigManager;
use Illuminate\Console\Command;

class SetupDatabase extends Command
{
    protected $signature = 'db:setup {--type=} {--interactive}';
    protected $description = 'Setup database configuration (MySQL or SQLite)';

    public function handle(): int
    {
        $manager = new DatabaseConfigManager();

        $type = $this->option('type');
        if (!$type || !in_array($type, ['mysql', 'sqlite'])) {
            $type = $this->choice(
                'Select database type:',
                ['sqlite', 'mysql'],
                0
            );
        }

        if ($type === 'sqlite') {
            return $this->setupSqlite($manager);
        }

        return $this->setupMysql($manager);
    }

    protected function setupSqlite(DatabaseConfigManager $manager): int
    {
        $this->info('Configuring SQLite database...');

        $path = $this->ask(
            'Database file path',
            database_path('database.sqlite')
        );

        $manager->configureSqlite($path);
        $manager->save();

        $this->info('✅ SQLite configured successfully');
        $this->line('Connection: ' . $manager->getConnectionString());

        return self::SUCCESS;
    }

    protected function setupMysql(DatabaseConfigManager $manager): int
    {
        $this->info('Configuring MySQL database...');

        $host = $this->ask('MySQL host', '127.0.0.1');
        $port = (int) $this->ask('MySQL port', '3306');
        $database = $this->ask('Database name', 'myscholar');
        $username = $this->ask('Username', 'root');
        $password = $this->secret('Password (hidden)');

        $charset = $this->ask('Charset', 'utf8mb4');
        $collation = $this->ask('Collation', 'utf8mb4_unicode_ci');

        $manager->configureMysql(
            host: $host,
            database: $database,
            username: $username,
            password: $password ?? '',
            port: $port,
            charset: $charset,
            collation: $collation
        );

        $manager->save();

        $this->info('✅ MySQL configured successfully');
        $this->line('Connection: ' . $manager->getConnectionString());

        return self::SUCCESS;
    }
}
