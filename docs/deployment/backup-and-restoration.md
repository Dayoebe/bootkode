# Backup and restore without overwriting working records

Keep daily encrypted off-host database snapshots and private uploads, with retention and recovery objectives agreed by the operator. Include issued certificates, private assessments/submitted files, résumés and course resources, stable APP_KEY and required provider configuration through a separate encrypted secret backup. Git and `public/build` do not back up learner records or uploads. Database SQL can contain PII and payment references; never commit or publicly serve it.

## Create a database backup

Install a compatible `mariadb-dump` / `mysqldump` client. Create an operator-owned absolute 0600 client file outside the checkout containing a `[client]` section with the required host, port, user and password. Use a dedicated backup account with privileges necessary to read the schema, triggers/routines/events; the application account should have schema-scoped privileges, not server administration.

```sh
mkdir -m 700 /private/bootkode-backups
MYSQL_DEFAULTS_FILE=/private/bootkode-client.cnf \
MYSQL_DUMP_BINARY=/absolute/path/to/mariadb-dump \
  scripts/backup-database.sh bootkode /private/bootkode-backups
```

The script validates private ownership/modes, writes outside the checkout, streams a single-transaction/quick dump through gzip with pipe failure detection, verifies gzip integrity and writes a SHA-256 checksum. It does not print credentials, delete older backups or upload anything. Do not run concurrent schema migrations during a logical snapshot. Nontransactional tables need a separate consistent maintenance strategy. Encrypt the output with the operator's approved tooling and copy it off-host. Verify remote checksum, retention and a successful restore, not just a zero exit code.

Snapshot private uploads consistently with database references (maintenance window or provider volume snapshot). Preserve original paths and permissions. Application code restores from the reviewed Git SHA; media-provider objects need their own retention/export arrangement. Do not include local recovery/test evidence in a production upload archive. Log archives and database system tablespace copies are sensitive separate operational material.

## Rehearse restoration

1. Provision a new isolated database ending `_test` or `_testing`, never the working schema. Disable queue/scheduler/mail and outgoing payment/provider calls in the rehearsal environment. Confirm the DB host/schema explicitly.
2. Validate the checksum from its backup directory and `gzip -t` the selected archive. Read its SQL database directives before restoring. The supplied script dumps one schema without `--databases` so it does not choose a target database itself.
3. Restore into the explicitly selected empty target, using an absolute private client config file and a compatible client:

```sh
set -o pipefail
gzip -dc /private/bootkode-backups/SELECTED.sql.gz | \
  /absolute/path/to/mysql --defaults-extra-file=/private/restore-client.cnf bootkode_restore_test
```

4. Compare every table count and canonical row hash against the snapshot inventory; validate foreign keys, lesson/course/enrollment links, wallet totals/ledger/references and historical certificate statuses/tokens/files. Record the restored migration list. Verify required private upload hashes and decrypt session/encrypted values with the retained key.
5. Boot an isolated copy using the restored schema/key, rehearse pending **forward** migrations, then run guarded tests/browser acceptance against separate synthetic fixtures. Do not refresh a restored business-data schema with PHPUnit merely because its name ends `_test`.
6. Record restore duration, recovery point and the result. Keep the original database untouched until verification and the approved cutover. Protect/delete disposable fixture environments only under their operator's retention policy; this work does not delete original data.

The 2 November local recovery independently restored the then-current **138 tables / 605 rows**, checked **237 foreign keys**, and retained **133 business-table hashes/old fields** across the two additive migrations. The new backup script was also exercised with a dedicated restored-copy database. Host-level encrypted off-site restore, file/key recovery and timed disaster-recovery acceptance remain launch gates.
