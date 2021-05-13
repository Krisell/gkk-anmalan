# GKK Anmälningssidor

<img src="https://user-images.githubusercontent.com/25909128/118098777-9b9b0a80-b3d4-11eb-8e96-8e4484b41c25.png" width="100">

Detta projekt hanterar "anmälningssidan" för Göteborg Kraftsportklubb (GKK). Projektet startades 2020 för att förenkla hanteringen vid anmälan till tävlingar (från medlem till styrelse) men har med tiden utökas till att idag omfatta följande funktioner:

- Tävlingsanmälan
- Funktionärsanmälan
- Nyheter
- Dokumenthantering (ex. protokoll eller länkar som publiceras för medlemmarna)
- Klubbrekord (administration under utveckling)
- Medlemshantering med avtal, bokföring av funkionärshjälp mm.

Systemet uppdateras löpande med nya funktioner efter behov.

## Teknisk översikt

Applikationen är helt webbaserad och bygger på ramverken Vue.js och Laravel (PHP). Hostingen sker på One.com där ordinarie GKK-hemsida ligger. Datan sparas i den MySQL-databas som ingår i One-hostingen, som också ligger under daglig backup.

## Uppdatera live-versionen

Efter att önskade ändringar är utvecklade, mergade till master och pushade hit används följande steg för att publicera en ny version:

- Logga in på servern
- I mappen `anmalan`, kör kommandot `deployanm` som automatiserar `git pull`, uppdatering av publika assets samt Laravel-specifika optimeringskommandon.
- Om PHP-beroenden har uppdaterats, kör också `php composer.phar update`

Deploy är i nuläget inte helt atomär utan en kort period (några sekunder) av nertid kan upplevas (särskilt i samband med `composer update`). Detta är acceptabelt i nuläget men kan komma att förändras om användningen av systemet ökar.

Eftersom GKK ligger på One.com utan möjlighet att själv konfigurera virtuella hosts (subdomänen mappas automatiskt till en mapp) måste ev. uppdateringar till public-mappen kopieras till `webroot/anmalan/`, och detta sköts alltså automaiskt (av `deployanm` ovan). Observera att Laravels `index.php` har justerats för att hitta rätt, så den ska _inte_ uppdateras. För tillfället är det endast innehållet i js/ och css/, samt `mix-manifest.json`, som kopieras.

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

Registreringen är i nuläget öppen, och innebär att medlemmar sjävla kan registrera sig. Före användning behöver medlemsavtal godkännas. Det sker i nuläget ingen verifiering av angiven epostadress, men det kan vi koppla på om det blir aktuellt, alternativt stänga anmälan och låta nya konton skapas av styrelsen.

## Bidrag

Idéer och hjälp mottages tacksamt. Du kan antingen skapa en Issue/PR direkt här, eller kontakta mig via epost.
