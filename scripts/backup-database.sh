#!/usr/bin/env bash
set -euo pipefail
umask 077
# Credentials belong in an operator-owned 0600 [client] file, never argv or Git.
defaults_file="${MYSQL_DEFAULTS_FILE:?Set MYSQL_DEFAULTS_FILE to a private absolute client configuration path}"
database_name="${1:?Usage: backup-database.sh DATABASE /private/backup-directory}"
backup_directory="${2:?Provide an absolute private backup directory}"
[[ "$database_name" =~ ^[A-Za-z0-9_]+$ ]] || { echo 'Invalid database identifier.' >&2; exit 64; }
[[ "$defaults_file" = /* && -f "$defaults_file" && "$backup_directory" = /* ]] || { echo 'Absolute private paths are required.' >&2; exit 64; }
file_mode="$(stat -c %a -- "$defaults_file")"
[[ "$file_mode" = 600 || "$file_mode" = 400 ]] || { echo 'Client configuration must have mode 0600 or 0400.' >&2; exit 77; }
[[ -O "$defaults_file" ]] || { echo 'Client configuration must be owned by the current user.' >&2; exit 77; }
repository_root="$(cd "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd -P)"
mkdir -p -- "$backup_directory"
backup_directory="$(cd "$backup_directory" && pwd -P)"
case "$backup_directory/" in "$repository_root/"*) echo 'Use a backup directory outside the web application checkout.' >&2; exit 77 ;; esac
[[ "$(stat -c %a -- "$backup_directory")" = 700 && -O "$backup_directory" ]] || { echo 'Backup directory must be owned by the current user with mode 0700.' >&2; exit 77; }
dump_binary="${MYSQL_DUMP_BINARY:-mariadb-dump}"
command -v "$dump_binary" >/dev/null || { echo 'Install a compatible dump client or set MYSQL_DUMP_BINARY to its absolute path.' >&2; exit 69; }
backup_file="$backup_directory/${database_name}-$(date -u +%Y%m%dT%H%M%SZ)-$$.sql.gz"
partial_file="$(mktemp "$backup_directory/.database-backup.XXXXXX")"
trap 'rm -f -- "$partial_file"' EXIT
"$dump_binary" --defaults-extra-file="$defaults_file" --single-transaction --quick \
  --routines --triggers --events --hex-blob --skip-lock-tables -- "$database_name" | gzip -c > "$partial_file"
gzip -t -- "$partial_file"
[[ -s "$partial_file" ]] || { echo 'Empty backup refused.' >&2; exit 74; }
mv -- "$partial_file" "$backup_file"
sha256sum -- "$backup_file" > "$backup_file.sha256"
echo "Database backup written: $backup_file"
echo 'Copy off-host using encryption; verify by restoring into a separate database.'
