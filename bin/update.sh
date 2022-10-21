#!/usr/bin/bash

git pull

cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/next/public/css/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/next/css
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/next/public/js/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/next/js
yes | cp -r /customers/d/f/2/gkk-styrkelyft.se/httpd.private/next/public/build /customers/d/f/2/gkk-styrkelyft.se/httpd.www/next
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/next/artisan config:cache
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/next/artisan route:cache

echo Assets updated!
