#!/usr/bin/env bash
set -uo pipefail

su - rahtk -s /bin/bash -c '
set -o pipefail
cd /srv/rahtk/staging/backend

echo "=== MIGRATE ==="
php artisan migrate --force 2>&1 | tail -20

echo "=== PINT ==="
./vendor/bin/pint --test 2>&1 | tail -25

echo "=== PHPSTAN ==="
./vendor/bin/phpstan analyse --no-progress --memory-limit=512M 2>&1 | tail -35
'
