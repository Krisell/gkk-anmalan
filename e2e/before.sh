#!/bin/bash
APP_ENV=testing php artisan route:cache
DB_DATABASE=$(cd `dirname $0` && pwd)/../database/e2e.sqlite php artisan migrate:fresh
