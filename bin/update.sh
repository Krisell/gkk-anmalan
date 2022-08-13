#!/usr/bin/bash

git pull

cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/css/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/css
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/js/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/js
cp -r /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/build /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/build
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/mix-manifest.json /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/mix-manifest.json
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/manifest.json /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/manifest.json
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/artisan config:cache
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/artisan route:cache

echo Assets updated!
