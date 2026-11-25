#!/usr/bin/env bash
set -euo pipefail

ROOT=/srv/rahtk
SERVICE_USER=rahtk

echo "[1/5] swap"
if ! swapon --show | grep -q '/swapfile'; then
  if [ ! -f /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile >/dev/null
  fi
  swapon /swapfile
  grep -q '^/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
  sysctl -w vm.swappiness=10 >/dev/null
  grep -q '^vm.swappiness' /etc/sysctl.conf || echo 'vm.swappiness=10' >> /etc/sysctl.conf
  echo "swap added"
else
  echo "swap already present"
fi

echo "[2/5] service user"
if ! id -u "$SERVICE_USER" >/dev/null 2>&1; then
  useradd --system --create-home --home-dir "$ROOT" --shell /bin/bash "$SERVICE_USER"
  echo "user created"
else
  echo "user exists"
fi

echo "[3/5] directory layout"
mkdir -p "$ROOT"/{staging,prod,shared/storage,shared/backups,logs}
mkdir -p "$ROOT/staging/storage"/{app,logs,framework/cache/data,framework/sessions,framework/views}
mkdir -p "$ROOT/prod/storage"/{app,logs,framework/cache/data,framework/sessions,framework/views}
chown -R "$SERVICE_USER:$SERVICE_USER" "$ROOT"
chmod -R 750 "$ROOT"
echo "layout ready"

echo "[4/5] database role and schemas"
DB_PASS=$(head -c 24 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 24)
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
SELECT 'CREATE DATABASE rahtk_prod OWNER rahtk'
WHERE NOT EXISTS (SELECT 1 FROM pg_database WHERE datname = 'rahtk_prod')\gexec
SQL
echo "databases ready"

echo "[5/5] staging environment file"
ENV_FILE="$ROOT/shared/staging.env"
umask 077
cat > "$ENV_FILE" <<ENV
APP_NAME=Rahtk
APP_ENV=staging
APP_DEBUG=true
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
chown "$SERVICE_USER:$SERVICE_USER" "$ENV_FILE"
chmod 600 "$ENV_FILE"
echo "environment file written"

echo "=== RESULT ==="
swapon --show
free -m | head -2
df -h / | tail -1
su - postgres -c "psql -tc \"select datname from pg_database where datname like 'rahtk%'\""
