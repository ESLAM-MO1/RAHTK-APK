#!/usr/bin/env bash
set -euo pipefail

export DEBIAN_FRONTEND=noninteractive

apt-get update -qq

apt-get install -y -qq --no-install-recommends \
  php8.3-cli \
  php8.3-pgsql \
  php8.3-bcmath \
  php8.3-intl \
  php8.3-redis \
  php8.3-zip \
  php8.3-gd \
  php8.3-mbstring \
  php8.3-xml \
  php8.3-curl \
  php8.3-sqlite3 \
  unzip \
  git \
  jq

echo "=== PHP ==="
php -v | head -1
php -m | grep -E "pdo_pgsql|bcmath|intl|redis|zip|gd|sqlite3" | tr "\n" " "
echo
echo "=== NOTE: php-fpm was not touched ==="
systemctl is-active php8.3-fpm || true
