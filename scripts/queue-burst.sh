#!/usr/bin/env bash
set -euo pipefail
umask 077
cd "$(dirname -- "${BASH_SOURCE[0]}")/.."
php_binary="${PHP_BINARY:-php}"
queue_kind="${1:-default}"
case "$queue_kind" in
  default) queue_connection=""; queue_names="default,webhooks"; job_timeout=60 ;;
  course-content|bulk-enrollment) queue_connection="$queue_kind"; queue_names="$queue_kind"; job_timeout=600 ;;
  *) echo 'Use default, course-content or bulk-enrollment.' >&2; exit 64 ;;
esac
command -v flock >/dev/null || { echo 'flock is required; use the hosting panel overlap lock instead.' >&2; exit 69; }
# One worker across all three burst classes on this single-node 2 GB profile.
# The kernel releases this lock after a crash; it has no stale-file lifetime.
mkdir -p storage/framework
exec 9>storage/framework/production-queue-burst.lock
flock -n 9 || exit 0
queue_args=()
if [[ -n "$queue_connection" ]]; then queue_args+=("$queue_connection"); fi
exec "$php_binary" -d memory_limit=256M artisan queue:work "${queue_args[@]}" \
  --queue="$queue_names" --stop-when-empty --max-time=50 --max-jobs=10 \
  --sleep=1 --tries=3 --backoff=60 --timeout="$job_timeout" --memory=192 --no-interaction
