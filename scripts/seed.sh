#!/bin/bash
set -e

MODULE=${1:-"all"}

echo "MyScholar - Seeding"
echo ""

case $MODULE in
    "all")
        echo "→ Seeding all modules..."
        php artisan db:seed --class="Modules\\Config\\Seeders\\SystemSettingsSeeder" --force
        ;;
    "config")
        echo "→ Seeding Config module..."
        php artisan db:seed --class="Modules\\Config\\Seeders\\SystemSettingsSeeder" --force
        ;;
    *)
        echo "Usage: ./scripts/seed.sh [all|config]"
        ;;
esac

echo "✅ Seeding complete."
