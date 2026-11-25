#!/usr/bin/env bash
set -uo pipefail

su - rahtk -s /bin/bash -c '
set -o pipefail
cd /srv/rahtk/staging/backend

echo "=== PINT ==="
./vendor/bin/pint --test 2>&1 | tail -12

echo "=== PHPSTAN ==="
./vendor/bin/phpstan analyse --no-progress --memory-limit=1G 2>&1 | tail -40

echo "=== COMMENT SCAN ==="
php ../tooling/comment-scan.php

set -a
. /srv/rahtk/shared/staging.env
set +a

umask 077
sed -e "s/^APP_ENV=.*/APP_ENV=testing/" \
    -e "s/^DB_DATABASE=.*/DB_DATABASE=rahtk_test/" \
    -e "s/^CACHE_STORE=.*/CACHE_STORE=array/" \
    -e "s/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/" \
    /srv/rahtk/shared/staging.env > .env.testing
grep -q "^OTP_EXPOSE=" .env.testing || echo "OTP_EXPOSE=true" >> .env.testing
sed -i "s/^OTP_EXPOSE=.*/OTP_EXPOSE=true/" .env.testing

echo "=== TESTS ==="
php artisan test --env=testing 2>&1 | tail -40
'
