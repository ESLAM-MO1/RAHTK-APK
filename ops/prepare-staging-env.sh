#!/usr/bin/env bash
set -euo pipefail

su - rahtk -s /bin/bash -c '
set -euo pipefail
cd /srv/rahtk/staging/backend
mkdir -p bootstrap/cache storage/framework/{cache/data,sessions,views} storage/logs storage/app/public
cp /srv/rahtk/shared/staging.env .env
php artisan key:generate --force
php artisan package:discover
'

echo "=== ENV READY ==="
