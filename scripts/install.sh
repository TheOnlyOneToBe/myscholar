#!/bin/bash
set -e

MODULES=${1:-"Config,Auth,Audit,Notifications,Reporting,Students,Grades,Attendance,Classes,Billing"}
CLIENT_ID=${2:-"DEFAULT"}

echo "╔══════════════════════════════════════════╗"
echo "║     MyScholar - Installation Script      ║"
echo "╚══════════════════════════════════════════╝"
echo ""
echo "Modules: $MODULES"
echo "Client:  $CLIENT_ID"
echo ""

# 1. Install PHP dependencies
echo "→ Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --quiet

# 2. Environment setup
if [ ! -f .env ]; then
    echo "→ Creating .env file..."
    cp .env.example .env
    php artisan key:generate --quiet
fi

# 3. Install modules
echo "→ Installing modules..."
php artisan modules:install "$MODULES" --client="$CLIENT_ID"

# 4. Run setup
echo ""
echo "Installation terminée."
echo "Prochaine étape: php artisan school:setup"
