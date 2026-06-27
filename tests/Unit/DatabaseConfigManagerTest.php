<?php

namespace Tests\Unit;

use App\Services\DatabaseConfigManager;
use Tests\TestCase;

class DatabaseConfigManagerTest extends TestCase
{
    protected DatabaseConfigManager $manager;
    protected string $testConfigPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary config file path
        $this->testConfigPath = tempnam(sys_get_temp_dir(), 'db_config_');
        unlink($this->testConfigPath);

        // Create manager instance
        $this->manager = new DatabaseConfigManager();

        // Override the config path using reflection to point to test temp file
        $reflection = new \ReflectionClass($this->manager);
        $property = $reflection->getProperty('configPath');
        $property->setAccessible(true);
        $property->setValue($this->manager, $this->testConfigPath);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testConfigPath)) {
            unlink($this->testConfigPath);
        }
        parent::tearDown();
    }

    public function test_can_configure_sqlite(): void
    {
        $this->manager->configureSqlite('/path/to/db.sqlite');

        $this->assertTrue($this->manager->isSqlite());
        $this->assertFalse($this->manager->isMysql());
        $this->assertEquals('sqlite', $this->manager->getDriver());
    }

    public function test_can_configure_mysql(): void
    {
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'test_db',
            username: 'root',
            password: 'secret'
        );

        $this->assertTrue($this->manager->isMysql());
        $this->assertFalse($this->manager->isSqlite());
        $this->assertEquals('mysql', $this->manager->getDriver());
    }

    public function test_is_configured_returns_true_when_configured(): void
    {
        $this->assertFalse($this->manager->isConfigured());

        $this->manager->configureSqlite();
        $this->assertTrue($this->manager->isConfigured());
    }

    public function test_can_get_config(): void
    {
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'test_db',
            username: 'root',
            password: 'secret',
            port: 3307
        );

        $config = $this->manager->getConfig();

        $this->assertEquals('mysql', $config['driver']);
        $this->assertEquals('localhost', $config['host']);
        $this->assertEquals('test_db', $config['database']);
        $this->assertEquals(3307, $config['port']);
    }

    public function test_can_get_individual_config_value(): void
    {
        $this->manager->configureMysql(
            host: 'example.com',
            database: 'mydb',
            username: 'user'
        );

        $this->assertEquals('example.com', $this->manager->get('host'));
        $this->assertEquals('mydb', $this->manager->get('database'));
        $this->assertNull($this->manager->get('nonexistent'));
        $this->assertEquals('default', $this->manager->get('nonexistent', 'default'));
    }

    public function test_can_save_configuration(): void
    {
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'test_db',
            username: 'root',
            password: 'secret'
        );

        $this->assertTrue($this->manager->save());
        $this->assertTrue(file_exists($this->testConfigPath));
    }

    public function test_can_load_configuration(): void
    {
        // Save configuration
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'test_db',
            username: 'root',
            password: 'secret'
        );
        $this->manager->save();

        // Create new manager and load from test config file
        $newManager = new DatabaseConfigManager();
        $reflection = new \ReflectionClass($newManager);
        $property = $reflection->getProperty('configPath');
        $property->setAccessible(true);
        $property->setValue($newManager, $this->testConfigPath);

        $this->assertTrue($newManager->load());
        $this->assertTrue($newManager->isMysql());
        $this->assertEquals('test_db', $newManager->get('database'));
    }

    public function test_clear_removes_configuration(): void
    {
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'test_db',
            username: 'root'
        );
        $this->manager->save();

        $this->assertTrue(file_exists($this->testConfigPath));
        $this->assertTrue($this->manager->isConfigured());

        $this->manager->clear();

        $this->assertFalse($this->manager->isConfigured());
        $this->assertFalse(file_exists($this->testConfigPath));
    }

    public function test_fluent_interface(): void
    {
        $result = $this->manager->configureSqlite();

        $this->assertInstanceOf(DatabaseConfigManager::class, $result);
        $this->assertTrue($this->manager->isSqlite());
    }

    public function test_to_laravel_config_mysql(): void
    {
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'mydb',
            username: 'root',
            password: 'secret',
            port: 3307,
            charset: 'utf8',
            collation: 'utf8_unicode_ci'
        );

        $laravelConfig = $this->manager->toLaravelConfig();

        $this->assertEquals('mysql', $laravelConfig['driver']);
        $this->assertEquals('localhost', $laravelConfig['host']);
        $this->assertEquals('mydb', $laravelConfig['database']);
        $this->assertEquals(3307, $laravelConfig['port']);
        $this->assertEquals('root', $laravelConfig['username']);
    }

    public function test_to_laravel_config_sqlite(): void
    {
        $this->manager->configureSqlite('/path/to/db.sqlite');

        $laravelConfig = $this->manager->toLaravelConfig();

        $this->assertEquals('sqlite', $laravelConfig['driver']);
        $this->assertEquals('/path/to/db.sqlite', $laravelConfig['database']);
    }

    public function test_connection_string_mysql(): void
    {
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'mydb',
            username: 'root',
            password: 'secret',
            port: 3306
        );

        $connStr = $this->manager->getConnectionString();

        $this->assertStringContainsString('mysql://', $connStr);
        $this->assertStringContainsString('root@localhost', $connStr);
        $this->assertStringContainsString('3306', $connStr);
        $this->assertStringContainsString('mydb', $connStr);
    }

    public function test_connection_string_sqlite(): void
    {
        $this->manager->configureSqlite('/path/to/db.sqlite');

        $connStr = $this->manager->getConnectionString();

        $this->assertStringContainsString('sqlite:///', $connStr);
        $this->assertStringContainsString('/path/to/db.sqlite', $connStr);
    }

    public function test_connection_string_not_configured(): void
    {
        $connStr = $this->manager->getConnectionString();

        $this->assertEquals('not configured', $connStr);
    }

    public function test_save_throws_exception_when_not_configured(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database configuration not set');

        $this->manager->save();
    }

    public function test_sqlite_uses_default_path(): void
    {
        $this->manager->configureSqlite();

        $config = $this->manager->getConfig();

        $this->assertArrayHasKey('database', $config);
        $this->assertNotEmpty($config['database']);
    }

    public function test_mysql_uses_defaults(): void
    {
        $this->manager->configureMysql(
            host: 'localhost',
            database: 'test',
            username: 'user'
        );

        $config = $this->manager->getConfig();

        $this->assertEquals(3306, $config['port']);
        $this->assertEquals('utf8mb4', $config['charset']);
        $this->assertEquals('utf8mb4_unicode_ci', $config['collation']);
        $this->assertEquals('', $config['password']);
    }
}
