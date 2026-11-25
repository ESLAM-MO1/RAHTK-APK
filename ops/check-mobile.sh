#!/usr/bin/env bash
set -uo pipefail

export PATH=/opt/flutter/bin:$PATH
export PUB_CACHE=/srv/rahtk/shared/.pub-cache
export FLUTTER_ROOT=/opt/flutter

mkdir -p "$PUB_CACHE"
chown -R rahtk:rahtk "$PUB_CACHE"
chmod -R a+rX /opt/flutter

su - rahtk -s /bin/bash -c '
set -o pipefail
export PATH=/opt/flutter/bin:$PATH
export PUB_CACHE=/srv/rahtk/shared/.pub-cache
cd /srv/rahtk/staging/mobile

echo "=== PUB GET ==="
flutter pub get 2>&1 | tail -15

echo "=== FORMAT ==="
dart format --output=none --set-exit-if-changed lib test 2>&1 | tail -20

echo "=== ANALYSE ==="
flutter analyze --fatal-infos 2>&1 | tail -40

echo "=== TESTS ==="
flutter test 2>&1 | tail -30
'
