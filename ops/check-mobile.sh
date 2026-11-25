#!/usr/bin/env bash
set -uo pipefail

export PATH=/opt/flutter/bin:$PATH
export PUB_CACHE=/srv/rahtk/shared/.pub-cache

su - rahtk -s /bin/bash -c '
set -o pipefail
export PATH=/opt/flutter/bin:$PATH
export PUB_CACHE=/srv/rahtk/shared/.pub-cache
cd /srv/rahtk/staging/mobile

echo "=== PUB GET ==="
flutter pub get 2>&1 | tail -8

echo "=== FORMAT (write) ==="
dart format lib test 2>&1 | tail -20

echo "=== ANALYSE ==="
flutter analyze --fatal-infos 2>&1 | tail -40

echo "=== TESTS ==="
flutter test 2>&1 | tail -25
'
