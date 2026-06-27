#!/bin/bash
set -e

ACTION=${1:-"run"}

echo "MyScholar - Migration"
echo ""

case $ACTION in
    "run")
        echo "→ Running pending migrations..."
        php artisan migrate --force
        ;;
    "rollback")
        echo "→ Rolling back last migration..."
        php artisan migrate:rollback
        ;;
    "fresh")
        echo "⚠  This will DROP all tables and re-run migrations."
        read -p "Are you sure? (y/N) " confirm
        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            php artisan migrate:fresh --force
        else
            echo "Cancelled."
        fi
        ;;
    "status")
        php artisan migrate:status
        ;;
    *)
        echo "Usage: ./scripts/migrate.sh [run|rollback|fresh|status]"
        ;;
esac
