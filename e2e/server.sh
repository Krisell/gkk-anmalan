#!/bin/bash
PORT=65456

DB_DATABASE=$(cd `dirname $0` && pwd)/../database/e2e.sqlite \
SESSION_SECURE_COOKIE=false \
MAIL_MAILER=array \
BCRYPT_ROUNDS=4 \
DEBUGBAR_ENABLED=false \
TELESCOPE_ENABLED=false \
APP_ENV=testing \
php artisan serve --port=$PORT > /dev/null &
