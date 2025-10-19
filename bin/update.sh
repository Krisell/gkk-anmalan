#!/usr/bin/bash

git pull

# Note: Build assets are now deployed automatically via GitHub Actions
# No need to copy them manually anymore

php /customers/9/a/3/goteborgkk.se/httpd.private/web/artisan config:cache
php /customers/9/a/3/goteborgkk.se/httpd.private/web/artisan route:cache

echo Backend updated! Frontend assets are deployed automatically via GitHub Actions.
