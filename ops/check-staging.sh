#!/usr/bin/env bash
set -uo pipefail

su - rahtk -s /bin/bash -c '
set -o pipefail
cd /srv/rahtk/staging/backend

echo "=== PINT ==="
./vendor/bin/pint --test 2>&1 | tail -12

echo "=== PHPSTAN ==="
./vendor/bin/phpstan analyse --no-progress --memory-limit=1G 2>&1 | tail -30

echo "=== COMMENT SCAN ==="
php ../tooling/comment-scan.php

umask 077
(
  set -a
  . /srv/rahtk/shared/staging.env
  set +a
  sed -e "s/^APP_ENV=.*/APP_ENV=testing/" \
      -e "s/^DB_DATABASE=.*/DB_DATABASE=rahtk_test/" \
      -e "s/^CACHE_STORE=.*/CACHE_STORE=array/" \
      -e "s/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/" \
      -e "s/^SESSION_DRIVER=.*/SESSION_DRIVER=array/" \
      -e "s/^OTP_EXPOSE=.*/OTP_EXPOSE=true/" \
      /srv/rahtk/shared/staging.env > .env.testing
)
grep -q "^OTP_EXPOSE=" .env.testing || echo "OTP_EXPOSE=true" >> .env.testing

echo "=== TESTS ==="
env -u APP_ENV -u DB_DATABASE -u CACHE_STORE -u QUEUE_CONNECTION -u SESSION_DRIVER -u OTP_EXPOSE -u REDIS_DB \
  php artisan test --env=testing 2>&1 | tail -30
'
