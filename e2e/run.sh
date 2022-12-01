#!/bin/bash

NOCOLOR='\033[0m'
RED='\033[0;31m'
GREEN='\033[0;32m'
ORANGE='\033[0;33m'
BLUE='\033[0;34m'

PORT=65456

# Kill any already running server
ALREADY_RUNNING_SERVERS=$(lsof -t -i:$PORT)
echo $ALREADY_RUNNING_SERVERS
if [ ! -z "$ALREADY_RUNNING_SERVERS" ]
then
    echo "Killing server"
    kill "$ALREADY_RUNNING_SERVERS"
fi

APP_ENV=testing php artisan route:cache

echo "Starting test server on port $PORT..."

DB_DATABASE=$(cd `dirname $0` && pwd)/../database/e2e.sqlite \
SESSION_SECURE_COOKIE=false \
MAIL_MAILER=array \
BCRYPT_ROUNDS=4 \
DEBUGBAR_ENABLED=false \
TELESCOPE_ENABLED=false \
APP_ENV=testing \
php artisan serve --port=$PORT > /dev/null &

SERVER_PID=$!

echo -e "${GREEN}Test server started at http://localhost:${PORT}${NOCOLOR}"

echo 'Migrating test database'
TELESCOPE_ENABLED=false DB_DATABASE=$(cd `dirname $0` && pwd)/../database/e2e.sqlite php artisan migrate > /dev/null
echo -e "${GREEN}Test database migrated${NOCOLOR}"

npx playwright test

php artisan route:clear

kill $SERVER_PID
echo -e "${GREEN}Test server stopped${NOCOLOR}"
