#!/usr/bin/env bash
set -euo pipefail

echo "=== BEFORE ==="
systemctl list-units --state=running --type=service --no-legend | wc -l
ss -tln | wc -l

export DEBIAN_FRONTEND=noninteractive
export NEEDRESTART_MODE=l

apt-get install -y -qq --no-install-recommends \
  postgresql-16-postgis-3 \
  postgresql-16-postgis-3-scripts

echo "=== AFTER ==="
systemctl list-units --state=running --type=service --no-legend | wc -l
ss -tln | wc -l
ls /usr/share/postgresql/16/extension/ | grep -c "^postgis"
