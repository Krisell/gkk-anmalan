#!/usr/bin/bash

git pull

cp /customers/9/a/3/goteborgkk.se/httpd.private/web/public/css/* /customers/9/a/3/goteborgkk.se/httpd.www/css
cp /customers/9/a/3/goteborgkk.se/httpd.private/web/public/js/* /customers/9/a/3/goteborgkk.se/httpd.www/js
yes | cp -r /customers/9/a/3/goteborgkk.se/httpd.private/web/public/build /customers/9/a/3/goteborgkk.se/httpd.www
php /customers/9/a/3/goteborgkk.se/httpd.private/web/artisan config:cache
php /customers/9/a/3/goteborgkk.se/httpd.private/web/artisan route:cache

echo Assets updated!
