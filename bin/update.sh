#!/usr/bin/bash

git pull

cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/css/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/css
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/js/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/js
yes | cp -r /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/build /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/artisan config:cache
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/artisan route:cache

echo Assets updated!
