#!/usr/bin/env bash
set -euo pipefail
set -a
. /srv/rahtk/shared/staging.env
set +a
exec "$@"
