# Göteborg Kraftsportklubb (GKK) Anmälningsweb

## Teknisk översikt
Applikationen använder ramverken Vue.js och Laravel (php). Hostingen sker på One.com där ordinarie GKK-hemsida ligger. Datan sparas i den MySQL-databas som ingår i One-hostingen.

Vissa grafiska delar använder https://github.com/Naartti/vue-el-element, tack @naartti!

## Kort om namngivning
En `Competition` är ett tävlingstillfälle som GKK-medlemmar kan anmäla sig till.
Administratören skapar tillfället och respektive medlem skapar en `CompetitionRegistration`.

Ett `Event` är ett tillfälle där medlemmar kan anmäla sig till att hjälpa till som funktionär.
Administratören skapar tillfället och respektive medlem skapar en `EventRegistration`.

Ett `Cooperation` är ett tillfälle där andra föreningar kan anmäla intresse att delta (ex. skicka tävlande till våra serieomgångar).
Administratören skapar tillfället och besökare skapar en `CooperationRegistration`. Detta bör till skillnad från de andra två fallen inte kräva inlogg.
**Detta är inte implementerat ännu.**

## Uppdatera webben
Efter att önskade ändringar är pushade hit, logga in på servern och kör kommanot `deployanm` som i sin tur kör `git pull` från projektets mapp samt uppdaterar publika assets. Eftersom GKK ligger på One.com utan möjlighet att själv konfigurera virtuella hosts (subdomänen mappas automatiskt till en mapp) måste ev. uppdateringar till public-mappen kopieras till `webroot/anmalan/`, och detta sköts alltså automaiskt. Observera att index.php har justerats för att hitta rätt, så den ska *inte* uppdateras. För tillfället är det endast innehållet i js/ och css/, samt `mix-manifest.json`, som kopieras.

## Köra lokalt
Se till att php och composer finns installerat på din enhet (eller ex. i din docker-kontainer).

Följande kommandon kan sedan användas för att sätta upp en utvecklingsmiljö:
 * `git clone https://github.com/Krisell/gkk-anmalan.git` i den mapp du vill ha projektet i
 * `cp .env.example .env` för att skapa en lokal .env-fil (innehåller hemlig data såsom API-nycklar och app-nyckel)
 * `composer install` för att installera beroenden baserat på `composer.lock`
 * `php artisan key:generate` för att skapa en unik app-nyckel
 * `php artisan serve` för att starta en lokal server

Om du vill göra ändringar i frontend krävs att `Node.js` är installerat, att du installerar beroenden med `npm install` samt att du bygger för utveckling (`npm run watch`) eller för produktion (`npm run production`).

## Konton
Registreringen är i nuläget helt öppen, och innebär att medlemmarna sjävla kan registrera sig. Det sker ingen verifiering av angiven epostadress, men det kan vi koppla på om det blir aktuellt. I ett senare skede vill vi kanske stänga registreringen och istället låta administratörer enkelt maila ut en inbjudan till nya medlemmar.

## Epost
Just nu används Mailgun för att skicka epost (ex. vid registrering, ifall vi vill göra det). Mailgun är gratis upp till 10 000 epost per månad om jag förstår det rätt, vilket vi aldrig kommer uppnå. API-nycklar för mailgun behöver sättas i .env-filen för att aktiveras (se .env.example).

## Bidrag
Idéer och hjälp mottages tacksamt. Du kan antingen skapa en Issue/PR direkt här, eller kontakta mig via epost.
