#!/usr/bin/env bash
set -euo pipefail

FLUTTER_ROOT=/opt/flutter
FLUTTER_VERSION=3.27.4

export DEBIAN_FRONTEND=noninteractive
export NEEDRESTART_MODE=l

if [ ! -x "$FLUTTER_ROOT/bin/flutter" ]; then
  if ! command -v git >/dev/null; then
    apt-get install -y -qq --no-install-recommends git unzip xz-utils libglu1-mesa
  fi

  git clone --quiet --depth 1 --branch "$FLUTTER_VERSION" https://github.com/flutter/flutter.git "$FLUTTER_ROOT"
fi

git config --global --add safe.directory "$FLUTTER_ROOT" || true

chown -R rahtk:rahtk "$FLUTTER_ROOT"
su - rahtk -s /bin/bash -c 'git config --global --add safe.directory /opt/flutter'

"$FLUTTER_ROOT/bin/flutter" --version

echo "=== SERVICE COUNT (unchanged expected) ==="
systemctl list-units --state=running --type=service --no-legend | wc -l
