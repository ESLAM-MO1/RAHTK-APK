#!/usr/bin/env bash
set -uo pipefail

su - rahtk -s /bin/bash -c '
set -o pipefail
cd /srv/rahtk/staging/backend

echo "=== PINT ==="
./vendor/bin/pint --test 2>&1 | tail -12

echo "=== PHPSTAN ==="
./vendor/bin/phpstan analyse --no-progress --memory-limit=512M 2>&1 | tail -25

echo "=== COMMENT SCAN ==="
php ../../tooling/comment-scan.php

echo "=== TESTS ==="
php artisan test 2>&1 | tail -30
'
