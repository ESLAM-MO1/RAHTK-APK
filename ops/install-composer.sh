#!/usr/bin/env bash
set -euo pipefail

if [ ! -x /usr/local/bin/composer ]; then
  EXPECTED=$(curl -sS https://composer.github.io/installer.sig)
  php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
  ACTUAL=$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")

  if [ "$EXPECTED" != "$ACTUAL" ]; then
    rm -f /tmp/composer-setup.php
    echo "installer signature mismatch"
    exit 1
  fi

  php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet
  rm -f /tmp/composer-setup.php
fi

composer --version

echo "=== SWAP ==="
if ! swapon --show | grep -q '/swapfile'; then
  fallocate -l 2G /swapfile
  chmod 600 /swapfile
  mkswap /swapfile >/dev/null
  swapon /swapfile
  grep -q '^/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
  sysctl -w vm.swappiness=10 >/dev/null || true
  echo "swap on"
else
  echo "swap already on"
fi
swapon --show
free -m | head -2
