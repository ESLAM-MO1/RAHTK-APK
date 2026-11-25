#!/usr/bin/env bash
set -euo pipefail

ROOT=/srv/rahtk
SERVICE_USER=rahtk

mkdir -p "$ROOT"/{staging,prod,shared/storage,shared/backups,logs}

if ! id -u "$SERVICE_USER" >/dev/null 2>&1; then
  useradd --system --create-home --home-dir /home/rahtk --shell /bin/bash "$SERVICE_USER"
fi

DB_PASS=$(head -c 32 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 28)

su - postgres -c "psql -v ON_ERROR_STOP=1 -q" <<SQL
DO \$\$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'rahtk') THEN
    CREATE ROLE rahtk LOGIN PASSWORD '$DB_PASS';
  ELSE
    ALTER ROLE rahtk LOGIN PASSWORD '$DB_PASS';
  END IF;
END
\$\$;
SELECT 'CREATE DATABASE rahtk_staging OWNER rahtk'
WHERE NOT EXISTS (SELECT 1 FROM pg_database WHERE datname = 'rahtk_staging')\gexec
SELECT 'CREATE DATABASE rahtk_test OWNER rahtk'
WHERE NOT EXISTS (SELECT 1 FROM pg_database WHERE datname = 'rahtk_test')\gexec
SQL

su - postgres -c "psql -q -d rahtk_staging -c 'CREATE EXTENSION IF NOT EXISTS postgis' -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto' -c 'CREATE EXTENSION IF NOT EXISTS pg_trgm'"
su - postgres -c "psql -q -d rahtk_test -c 'CREATE EXTENSION IF NOT EXISTS postgis' -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto' -c 'CREATE EXTENSION IF NOT EXISTS pg_trgm'"

umask 077
cat > "$ROOT/shared/staging.env" <<ENV
APP_NAME=Rahtk
APP_ENV=staging
APP_DEBUG=true
APP_LOCALE=ar
APP_URL=https://staging.rahtk.sa
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=rahtk_staging
DB_USERNAME=rahtk
DB_PASSWORD=$DB_PASS
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=10
REDIS_CACHE_DB=12
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
ENV

chown -R "$SERVICE_USER:$SERVICE_USER" "$ROOT"
chmod 750 "$ROOT"
chmod 600 "$ROOT/shared/staging.env"
chown "$SERVICE_USER:$SERVICE_USER" "$ROOT/shared/staging.env"

echo "=== DB ==="
su - postgres -c "psql -tc \"select datname from pg_database where datname like 'rahtk%'\""
echo "=== USER ==="
id rahtk | cut -d' ' -f1
echo "=== ROOT ==="
ls -1 "$ROOT"
