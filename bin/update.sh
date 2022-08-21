#!/usr/bin/bash

git pull

cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/css/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/css
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/css/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/insidan/css
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/js/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan/js
cp /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/js/* /customers/d/f/2/gkk-styrkelyft.se/httpd.www/insidan/js
yes | cp -r /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/build /customers/d/f/2/gkk-styrkelyft.se/httpd.www/anmalan
yes | cp -r /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/public/build /customers/d/f/2/gkk-styrkelyft.se/httpd.www/insidan
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/artisan config:cache
php /customers/d/f/2/gkk-styrkelyft.se/httpd.private/anmalan/artisan route:cache

echo Assets updated!
