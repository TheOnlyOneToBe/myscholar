# Database Configuration System

Guide for configuring MySQL or SQLite databases through code configuration.

## Overview

The database configuration system allows you to:
- Configure MySQL or SQLite databases **in code** (not just .env)
- Persist configuration to `storage/database-config.json`
- Switch databases without environment variables
- Use environment variables as fallback
- Configure databases programmatically via API or Artisan commands

## Configuration Priority

1. **Code Config** (`storage/database-config.json`) - Highest priority
2. **Environment Variables** (`.env`) - Fallback
3. **Defaults** - Final fallback

## Artisan Command: Interactive Setup

### Setup SQLite

```bash
php artisan db:setup --type=sqlite
```

Prompts for:
- Database file path (default: `database/database.sqlite`)

### Setup MySQL

```bash
php artisan db:setup --type=mysql
```

Prompts for:
- MySQL host (default: `127.0.0.1`)
- MySQL port (default: `3306`)
- Database name (default: `myscholar`)
- Username (default: `root`)
- Password (hidden)
- Charset (default: `utf8mb4`)
- Collation (default: `utf8mb4_unicode_ci`)

### Interactive Selection

Run without options for interactive menu:

```bash
php artisan db:setup
```

Presents choice:
```
Select database type:
 [0] > sqlite
 [1]   mysql
```

## Programmatic Usage

### Helper Functions

```php
// Get database config manager
$config = database_config();

// Check if database is configured
if (is_database_configured()) {
    // Configuration exists
}

// Get database driver
$driver = get_database_driver(); // 'mysql' or 'sqlite'

// Check database type
if (is_mysql()) {
    // MySQL configuration
} elseif (is_sqlite()) {
    // SQLite configuration
}
```

### Service Injection

```php
use App\Services\DatabaseConfigManager;

class MyController extends Controller
{
    public function __construct(
        protected DatabaseConfigManager $dbConfig
    ) {}

    public function setup()
    {
        // Configure SQLite
        $this->dbConfig->configureSqlite('/path/to/database.sqlite');
        $this->dbConfig->save();

        // Or configure MySQL
        $this->dbConfig->configureMysql(
            host: 'localhost',
            database: 'myscholar',
            username: 'root',
            password: 'password',
            port: 3306,
            charset: 'utf8mb4',
            collation: 'utf8mb4_unicode_ci'
        );
        $this->dbConfig->save();
    }
}
```

## API Endpoints

### Get Current Configuration

```http
GET /api/config/database
```

**Response:**
```json
{
  "configured": true,
  "driver": "mysql",
  "connection_string": "mysql://root@localhost:3306/myscholar",
  "config": {
    "driver": "mysql",
    "host": "localhost",
    "port": 3306,
    "database": "myscholar",
    "username": "root",
    "charset": "utf8mb4",
    "collation": "utf8mb4_unicode_ci"
  }
}
```

### Setup SQLite via API

```http
POST /api/config/database/sqlite
Content-Type: application/json

{
  "database": "/path/to/database.sqlite"
}
```

**Optional Parameters:**
- `database` - Path to SQLite database file (default: `database/database.sqlite`)

**Response:**
```json
{
  "success": true,
  "message": "SQLite configured successfully",
  "driver": "sqlite",
  "connection_string": "sqlite:////path/to/database.sqlite"
}
```

### Setup MySQL via API

```http
POST /api/config/database/mysql
Content-Type: application/json

{
  "host": "localhost",
  "port": 3306,
  "database": "myscholar",
  "username": "root",
  "password": "secret",
  "charset": "utf8mb4",
  "collation": "utf8mb4_unicode_ci"
}
```

**Required Parameters:**
- `host` - MySQL server hostname
- `port` - MySQL server port
- `database` - Database name
- `username` - Database username

**Optional Parameters:**
- `password` - Database password
- `charset` - Character set (default: `utf8mb4`)
- `collation` - Collation (default: `utf8mb4_unicode_ci`)

**Response:**
```json
{
  "success": true,
  "message": "MySQL configured successfully",
  "driver": "mysql",
  "connection_string": "mysql://root@localhost:3306/myscholar"
}
```

### Clear Configuration

```http
DELETE /api/config/database
```

**Response:**
```json
{
  "success": true,
  "message": "Database configuration cleared"
}
```

### Get Configuration File Path

```http
GET /api/config/database/path
```

**Response:**
```json
{
  "config_path": "/home/user/myscholar/storage/database-config.json",
  "config_exists": true
}
```

## Configuration File Format

The configuration is stored as JSON in `storage/database-config.json`:

### SQLite

```json
{
  "driver": "sqlite",
  "database": "/path/to/database.sqlite"
}
```

### MySQL

```json
{
  "driver": "mysql",
  "host": "localhost",
  "port": 3306,
  "database": "myscholar",
  "username": "root",
  "password": "secret",
  "charset": "utf8mb4",
  "collation": "utf8mb4_unicode_ci"
}
```

## Use Cases

### 1. Initial Setup Wizard

During installation, use the Artisan command to configure the database:

```bash
php artisan db:setup
```

### 2. Multi-Client Installation

Each client installation gets its own database configuration:

```bash
# Client 1
php artisan db:setup --type=mysql
# Configures for Client 1's database

# Client 2
php artisan db:setup --type=mysql
# Configures for Client 2's database
```

### 3. Development vs Production

Development uses SQLite (lightweight):
```bash
php artisan db:setup --type=sqlite
```

Production uses MySQL (scalable):
```bash
php artisan db:setup --type=mysql
```

### 4. API-Based Setup

Build a web-based installation interface:

```javascript
// Setup MySQL via API
fetch('/api/config/database/mysql', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        host: 'mysql.example.com',
        port: 3306,
        database: 'myscholar_client_1',
        username: 'client1_user',
        password: 'secure_password'
    })
}).then(res => res.json())
  .then(data => console.log(data));
```

### 5. Check Database Type

In controllers or services:

```php
if (is_mysql()) {
    // Use MySQL-specific features
    // (like full-text search)
} else {
    // Use SQLite alternatives
}
```

## How It Works

1. **Load Configuration**
   - `DatabaseConfigManager` loads `storage/database-config.json` on instantiation
   - If file doesn't exist, configuration is empty

2. **Configure**
   - Call `configureMysql()` or `configureSqlite()`
   - Stores configuration in memory

3. **Save**
   - Call `save()` to write configuration to JSON file
   - Creates `storage/` directory if it doesn't exist

4. **Apply**
   - `config/database.php` checks for `storage/database-config.json`
   - Uses code configuration if present, falls back to `.env`
   - Laravel uses merged configuration for all database operations

## Security Considerations

### Password Storage

- Passwords are stored in `storage/database-config.json` (unencrypted)
- Add `storage/` to `.gitignore` to prevent committing passwords
- Consider file permissions on production:

```bash
chmod 600 storage/database-config.json
```

### File Permissions

Ensure only the application user can read the configuration:

```bash
chmod 700 storage/
chmod 600 storage/database-config.json
```

### API Access Control

In production, restrict the database configuration API to administrators only:

```php
Route::middleware(['auth', 'admin'])->prefix('api/config')->group(function () {
    // Database configuration routes
});
```

## Troubleshooting

### Configuration Not Loading

Check if the file exists:
```bash
php artisan db:setup --type=sqlite
php artisan tinker
> database_config()->getConfigPath()
```

### Using Environment Variables Instead

If you want to use `.env` instead of code configuration, don't run the setup command and rely on `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myscholar
DB_USERNAME=root
DB_PASSWORD=
```

### Switching Databases

Clear old configuration and set new one:

```bash
# SQLite → MySQL
php artisan db:setup --type=mysql
```

Configuration file is overwritten automatically.

## Examples

### Example 1: Setup During Installation

```bash
#!/bin/bash

# Install dependencies
composer install

# Create .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database interactively
php artisan db:setup

# Run migrations
php artisan migrate

# Done
echo "Installation complete!"
```

### Example 2: API-Based Setup (JavaScript)

```javascript
async function setupDatabase() {
    // Check current configuration
    const response = await fetch('/api/config/database');
    const data = await response.json();

    if (data.configured) {
        console.log('Database already configured:', data.driver);
        return;
    }

    // Setup MySQL
    const setupResponse = await fetch('/api/config/database/mysql', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            host: 'localhost',
            port: 3306,
            database: 'myscholar',
            username: 'root',
            password: 'secret'
        })
    });

    const setupData = await setupResponse.json();
    console.log(setupData.message);
}
```

### Example 3: Programmatic Setup in Service Provider

```php
// app/Providers/DatabaseSetupProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DatabaseConfigManager;

class DatabaseSetupProvider extends ServiceProvider
{
    public function boot()
    {
        $dbConfig = app(DatabaseConfigManager::class);

        if (!$dbConfig->isConfigured()) {
            // Auto-setup development database
            if (app()->environment('local')) {
                $dbConfig->configureSqlite();
                $dbConfig->save();
            }
        }
    }
}
```

## Migration Path

From environment variables to code configuration:

1. **Current State:** Using `.env` with `DB_CONNECTION=mysql` etc.
2. **Step 1:** Run `php artisan db:setup --type=mysql`
3. **Step 2:** Configuration is stored in `storage/database-config.json`
4. **Step 3:** Laravel uses code config, ignores `.env` for database
5. **Optional:** Remove database variables from `.env`

## Configuration Manager API Reference

```php
class DatabaseConfigManager {
    // Setup
    public function configureMysql(...): self
    public function configureSqlite(string $path = null): self

    // Query
    public function getConfig(): array
    public function get(string $key, $default = null)
    public function isConfigured(): bool
    public function getDriver(): ?string
    public function isMysql(): bool
    public function isSqlite(): bool

    // Persistence
    public function save(): bool
    public function load(): bool
    public function clear(): self

    // Export
    public function toLaravelConfig(): array
    public function getConnectionString(): string
    public function getConfigPath(): string
}
```

## Summary

The database configuration system provides:
- ✅ Code-based database configuration (not just .env)
- ✅ Support for MySQL and SQLite
- ✅ Interactive Artisan command
- ✅ RESTful API endpoints
- ✅ Helper functions for global access
- ✅ Service injection support
- ✅ JSON persistence
- ✅ Environment variable fallback
- ✅ Easy switching between databases
- ✅ Multi-client support

**Use this for flexible, programmatic database configuration!**
