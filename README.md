# GKK Anmälningssidor

<img src="https://user-images.githubusercontent.com/25909128/118098777-9b9b0a80-b3d4-11eb-8e96-8e4484b41c25.png" width="100">

Detta projekt hanterar både hemida och den interna "anmälningssidan" för Göteborg Kraftsportklubb (GKK). Projektet startades 2020 för att förenkla hanteringen vid anmälan till tävlingar (från medlem till styrelse) men har med tiden utökas till att idag omfatta både publik hemsida och medlemssidor med följande funktioner:

- Tävlingsanmälan
- Funktionärsanmälan
- Nyheter
- Epost-utskick
- Dokumenthantering (ex. protokoll eller länkar som publiceras för medlemmarna, eller interna dokument för styrelsen)
- Klubbrekord
- Medlemshantering med register, avtal, bokföring av funkionärshjälp mm

Systemet uppdateras löpande med nya funktioner efter behov.

## Teknisk översikt

Applikationen är helt webbaserad och bygger på ramverken Vue.js och Laravel (PHP). Data sparas i en klassisk MySQL-databas som också ligger under daglig backup.

## Uppdatera live-versionen

Efter att önskade ändringar är utvecklade, mergade till rätt branch och pushade hit används följande steg för att publicera en ny version:

- Logga in på servern
- I mappen för aktuell branch, kör kommandot `bin/update.sh` som automatiserar `git pull`, uppdatering av publika assets samt Laravel-specifika optimeringskommandon.
- Om PHP-beroenden har uppdaterats, kör också `php composer.phar install --no-dev`

Deploy är i nuläget inte helt atomär utan en kort period (några sekunder) av nertid kan upplevas (särskilt i samband med `composer install`). Detta är acceptabelt i nuläget men kan komma att förändras om användningen av systemet ökar.

Eftersom GKK ligger på One.com utan möjlighet att själv konfigurera virtuella hosts (subdomänen mappas automatiskt till en mapp) måste ev. uppdateringar till public-mappen kopieras till `webroot/anmalan/`, och detta sköts alltså automaiskt enligt ovan. Observera att Laravels `index.php` har justerats för att hitta rätt, så den ska _inte_ uppdateras. För tillfället är det endast innehållet i js/ och css/, samt frontend-manifest (för cache-busting), som kopieras.

## Utveckla lokalt

Se till att PHP och composer finns installerat på din enhet (eller ex. i din docker-kontainer).

Följande kommandon kan sedan användas för att sätta upp en utvecklingsmiljö:

- `git clone https://github.com/Krisell/gkk-anmalan.git`
- `cp .env.example .env` för att skapa en lokal .env-fil (lagrar hemlig data såsom API-nycklar och app-nyckel utanför ordinarie kodhantering)
- `composer install` för att installera beroenden baserat på `composer.lock`
- `php artisan key:generate` för att skapa en unik app-nyckel
- `php artisan serve` för att starta en lokal server

Om du vill göra ändringar i frontend krävs att `Node.js` är installerat, att du installerar beroenden med `npm install` samt att du bygger för utveckling (`npm run watch`) eller för produktion (`npm run production`).

## Konton

Registreringen är öppen så medlemmar sjävla kan skapa ett konto, men före användning krävs dels att våra medlemsavtal och antidopingavtal godkänns, dels att någon administratör (styrelsemedlem) godkänner det nya kontot. Det senare sker genom att en epostnotis skickas till styrelsen, och medlemmen får ett epost när godkännandet är genomfört. Observera att det i nuläget inte sker någon verifiering av angiven epostadress men det kan vi koppla på om det blir aktuellt, alternativt stänga anmälan och låta nya konton skapas av styrelsen.

## Inloggning via Google/Microsoft

Efter skapat konto kan inloggning ske enkelt via Google/Microsoft-knapparna, så att lösenord inte behöver avges. Det krävs dock att ett konto skapas först, och att lösenord då sätts till något. En framtida förbättring är att tillåta även kontoskapande via dessa knappar.

## Bidrag

Idéer och hjälp mottages tacksamt. Du kan antingen skapa en Issue/PR direkt här, eller kontakta mig via epost.
