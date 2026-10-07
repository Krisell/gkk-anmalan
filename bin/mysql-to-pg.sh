#!/bin/bash

# Converts a production MySQL dump (One.com) into the Postgres database used on Laravel Cloud.
#
#   bin/mysql-to-pg.sh path/to/dump.sql
#   CLOUD_DATABASE_URL='postgresql://user:pass@host:5432/db?sslmode=require' bin/mysql-to-pg.sh path/to/dump.sql
#
# Without CLOUD_DATABASE_URL it stops after building and verifying the local Postgres copy and
# writing gkk.pgdump. With it, it also replaces the Cloud database contents (asks first).
#
# Requires docker and pgloader (brew install pgloader). The two local containers are throwaway
# and are recreated on every run. Nothing here touches the production MySQL database.

set -euo pipefail

DUMP="${1:?Usage: bin/mysql-to-pg.sh path/to/dump.sql}"
[ -f "$DUMP" ] || { echo "Dump not found: $DUMP"; exit 1; }

MYSQL_CONTAINER=gkk-mysql
PG_CONTAINER=gkk-pg
MYSQL_PORT="${MYSQL_PORT:-3308}"
PG_PORT="${PG_PORT:-5434}"
PG_IMAGE="${PG_IMAGE:-postgres:17}" # match the major version of the Cloud database
PASSWORD=secret
OUT="${OUT:-gkk.pgdump}"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

cd "$(dirname "$0")/.."

step() { printf '\n==> %s\n' "$*"; }
mysql_q() { docker exec -i -e MYSQL_PWD="$PASSWORD" "$MYSQL_CONTAINER" mysql -h127.0.0.1 -uroot -N "$@"; }
pg_q() { docker exec -i "$PG_CONTAINER" psql -v ON_ERROR_STOP=1 -U postgres -At gkk "$@"; }

if [ -f bootstrap/cache/config.php ]; then
    echo "Config is cached (bootstrap/cache/config.php); run 'php artisan config:clear' first."
    exit 1
fi

command -v pgloader >/dev/null || { echo "pgloader not found (brew install pgloader)"; exit 1; }

step "Starting fresh local MySQL and Postgres containers"
docker rm -f "$MYSQL_CONTAINER" "$PG_CONTAINER" >/dev/null 2>&1 || true
docker run -d --name "$MYSQL_CONTAINER" -e MYSQL_ROOT_PASSWORD="$PASSWORD" -e MYSQL_DATABASE=gkk \
    -p "$MYSQL_PORT":3306 mysql:8.0 --default-authentication-plugin=mysql_native_password >/dev/null
docker run -d --name "$PG_CONTAINER" -e POSTGRES_PASSWORD="$PASSWORD" -e POSTGRES_DB=gkk \
    -p "$PG_PORT":5432 "$PG_IMAGE" >/dev/null

# Checked over TCP: both images first run a socket-only init server that would pass a socket check.
until mysql_q -e "SELECT 1" >/dev/null 2>&1; do sleep 2; done
until docker exec "$PG_CONTAINER" pg_isready -h 127.0.0.1 -U postgres >/dev/null 2>&1; do sleep 1; done

step "Importing $DUMP into MySQL"
mysql_q gkk < "$DUMP"

# The One.com dump creates and uses its own database; find whichever one holds the users table.
SRC=$(mysql_q -e "SELECT table_schema FROM information_schema.tables WHERE table_name IN ('gkk_users', 'users') AND table_schema NOT IN ('mysql', 'sys', 'performance_schema', 'information_schema') LIMIT 1")
[ -n "$SRC" ] || { echo "No users table found in the dump"; exit 1; }
echo "Source database: $SRC"

step "Creating the Postgres schema from the migrations"
DB_CONNECTION=pgsql DB_HOST=127.0.0.1 DB_PORT="$PG_PORT" DB_DATABASE=gkk \
    DB_USERNAME=postgres DB_PASSWORD="$PASSWORD" DB_PREFIX= \
    php artisan migrate --force

step "Preparing the MySQL copy"
# Leftover from the removed Musikhjälpen feature; not in the migrations.
mysql_q -e "DROP TABLE IF EXISTS \`$SRC\`.gkk_music_help_sets, \`$SRC\`.music_help_sets"

# Strip the gkk_ table prefix; the Cloud database is not shared, so DB_PREFIX is empty there.
mysql_q -e "SELECT CONCAT('RENAME TABLE \`$SRC\`.\`', table_name, '\` TO \`$SRC\`.\`', SUBSTRING(table_name, 5), '\`;')
    FROM information_schema.tables WHERE table_schema = '$SRC' AND table_name LIKE 'gkk\\_%'" | mysql_q

# Removed from the code in cc4b5a2, but never dropped in production.
if [ -n "$(mysql_q -e "SELECT 1 FROM information_schema.columns WHERE table_schema = '$SRC' AND table_name = 'competition_registrations' AND column_name = 'licence_number'")" ]; then
    mysql_q -e "ALTER TABLE \`$SRC\`.competition_registrations DROP COLUMN licence_number"
fi

# Postgres rejects 0000-00-00. Such values become NULL when the Postgres column allows it
# (MySQL's own column may not, so it is relaxed first); otherwise the run aborts.
# Loops read from fd 3: docker exec -i inside the loop would otherwise swallow the remaining lines.
mysql_q -e "SELECT table_name, column_name, column_type, is_nullable FROM information_schema.columns
    WHERE table_schema = '$SRC' AND data_type IN ('date', 'datetime', 'timestamp')" > "$WORK/date-columns"
while IFS=$'\t' read -r table column type nullable <&3; do
    zero="CAST(\`$column\` AS CHAR) LIKE '0000-00-00%'"
    count=$(mysql_q -e "SET SESSION sql_mode = ''; SELECT COUNT(*) FROM \`$SRC\`.\`$table\` WHERE $zero")
    [ "$count" -eq 0 ] && continue
    pg_nullable=$(pg_q -c "SELECT is_nullable FROM information_schema.columns
        WHERE table_schema = 'public' AND table_name = '$table' AND column_name = '$column'")
    if [ "$pg_nullable" != "YES" ]; then
        echo "$table.$column has $count zero dates but is NOT NULL in Postgres; decide how to handle it."
        exit 1
    fi
    if [ "$nullable" != "YES" ]; then
        mysql_q -e "SET SESSION sql_mode = ''; ALTER TABLE \`$SRC\`.\`$table\` MODIFY \`$column\` $type NULL"
    fi
    mysql_q -e "SET SESSION sql_mode = ''; UPDATE \`$SRC\`.\`$table\` SET \`$column\` = NULL WHERE $zero"
    echo "$table.$column: $count zero dates set to NULL"
done 3< "$WORK/date-columns"

step "Checking that the schemas match"
mysql_q -e "SELECT CONCAT(table_name, '.', column_name) FROM information_schema.columns
    WHERE table_schema = '$SRC' AND table_name <> 'migrations'" | sort > "$WORK/mysql-columns"
pg_q -c "SELECT table_name || '.' || column_name FROM information_schema.columns
    WHERE table_schema = 'public' AND table_name <> 'migrations'" | sort > "$WORK/pg-columns"
MISSING=$(comm -23 "$WORK/mysql-columns" "$WORK/pg-columns")
if [ -n "$MISSING" ]; then
    echo "Columns in production that the migrations don't create:"
    echo "$MISSING"
    exit 1
fi

step "Copying data with pgloader"
cat > "$WORK/migrate.load" <<EOF
LOAD DATABASE
  FROM mysql://root:$PASSWORD@127.0.0.1:$MYSQL_PORT/$SRC
  INTO postgresql://postgres:$PASSWORD@127.0.0.1:$PG_PORT/gkk
WITH data only, truncate, disable triggers, reset sequences
EXCLUDING TABLE NAMES MATCHING 'migrations'
ALTER SCHEMA '$SRC' RENAME TO 'public';
EOF
pgloader "$WORK/migrate.load" > "$WORK/pgloader.log" 2>&1 || true
grep -vE " (LOG|WARNING) " "$WORK/pgloader.log"
# pgloader keeps going (and may exit 0) after row errors; the summary is the reliable signal.
if ! grep -qE "Total import time +✓" "$WORK/pgloader.log"; then
    echo "pgloader reported errors; see above."
    exit 1
fi

step "Lowercasing emails"
DUPLICATES=$(pg_q -c "SELECT lower(email) FROM users GROUP BY 1 HAVING count(*) > 1")
if [ -n "$DUPLICATES" ]; then
    echo "Users whose emails differ only in case; merge them first:"
    echo "$DUPLICATES"
    exit 1
fi
pg_q -c "UPDATE users SET email = lower(email) WHERE email <> lower(email)"

step "Verifying row counts"
pg_q -c "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name <> 'migrations' ORDER BY 1" > "$WORK/tables"
FAILED=0
while read -r table <&3; do
    mysql_count=$(mysql_q -e "SELECT COUNT(*) FROM \`$SRC\`.\`$table\`")
    pg_count=$(pg_q -c "SELECT count(*) FROM \"$table\"")
    printf '%-28s %8s\n' "$table" "$pg_count"
    if [ "$mysql_count" != "$pg_count" ]; then
        echo "  MISMATCH: MySQL has $mysql_count"
        FAILED=1
    fi
done 3< "$WORK/tables"
[ "$(wc -l < "$WORK/tables")" -gt 20 ] || { echo "Expected 20+ tables, found $(wc -l < "$WORK/tables")"; exit 1; }
[ "$FAILED" -eq 0 ] || exit 1

step "Writing $OUT"
docker exec "$PG_CONTAINER" pg_dump -U postgres -Fc --no-owner --no-acl gkk > "$OUT"
ls -lh "$OUT"

if [ -z "${CLOUD_DATABASE_URL:-}" ]; then
    printf '\nDone. Local Postgres is on 127.0.0.1:%s. Set CLOUD_DATABASE_URL to also restore to Cloud.\n' "$PG_PORT"
    exit 0
fi

step "Restoring to Cloud"
read -r -p "This replaces ALL data in the Cloud database. Type 'yes' to continue: " CONFIRM
[ "$CONFIRM" = "yes" ] || { echo "Aborted."; exit 1; }

docker exec -i "$PG_CONTAINER" pg_restore --clean --if-exists --no-owner --no-acl --exit-on-error \
    -d "$CLOUD_DATABASE_URL" < "$OUT"

for table in users competitions payments; do
    printf '%-28s local %8s  cloud %8s\n' "$table" \
        "$(pg_q -c "SELECT count(*) FROM \"$table\"")" \
        "$(docker exec "$PG_CONTAINER" psql -At "$CLOUD_DATABASE_URL" -c "SELECT count(*) FROM \"$table\"")"
done

printf '\nDone. Remember to turn off public access on the Cloud database.\n'
