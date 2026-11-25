#!/usr/bin/env bash
set -euo pipefail

su - rahtk -s /bin/bash -c '
set -euo pipefail
cd /srv/rahtk
if [ ! -d staging/.git ]; then
  rm -rf staging
  git clone --quiet https://github.com/ESLAM-MO1/RAHTK-APK.git staging
fi
cd staging
git fetch --quiet origin main
git reset --hard --quiet origin/main
git log --oneline -1
'
