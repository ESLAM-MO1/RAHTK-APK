#!/usr/bin/env bash
set -euo pipefail

chown -R rahtk:rahtk /opt/flutter
su - rahtk -s /bin/bash -c 'git config --global --add safe.directory /opt/flutter'

su - rahtk -s /bin/bash -c 'export PATH=/opt/flutter/bin:$PATH; flutter --version'

echo "=== SERVICE COUNT ==="
systemctl list-units --state=running --type=service --no-legend | wc -l
