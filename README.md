# Göteborg Kraftsportklubb (GKK) Anmälningsweb

## Inlogg
Tanken är just nu att kräva att GKK-medlemmar loggar in för att kunna anmäla sig. Detta ger fördelen att de inte kommer behöva ange persondata och licensnummer varje gång, utan endast ev. viktklass.
Vi får diskutera om medlemmarna ska kunna registrera sig själva, eller om föreningen ska skapa konton (ex. att en registreringslänk skickas automatiskt via epost).

## Kort om namngivning
En `Competition` är ett tävlingstillfälle som GKK-medlemmar kan anmäla sig till.
Administratören skapar tillfället och respektive medlem skapar en `CompetitionRegistration`.
Detta är inte implementerat ännu.

Ett `Event` är ett tillfälle där medlemmar kan anmäla sig till att hjälpa till som funktionär.
Administratören skapar tillfället och respektive medlem skapar en `EventRegistration`.
Detta är delvis implementerat.

Ett `Cooperation` är ett tillfälle där andra föreningar kan anmäla intresse att delta (ex. skicka tävlande till våra serieomgångar).
Administratören skapar tillfället och besökare skapar en `CooperationRegistration`. Detta bör till skillnad från de andra två fallen inte kräva inlogg.
Detta är inte implementerat ännu.

## Uppdatera webben
Efter att önskade ändringar är pushade hit, logga in på servern och kör `git pull` från projektets mapp. Eftersom GKK ligger på One.com utan möjlighet att själv konfigurera virtuella hosts (subdomänen mappas automatiskt till en mapp) måste ev. uppdateringar till public-mappen kopieras till `webroot/anmalan/`. Observera också att index.php har justerats för att hitta rätt, så uppdatera *inte* den. Typiskt är det innehållet i js/ och css/ som behöver kopieras, ifall justeringar har gjorts i frontend. Dessa flyttas automatiskt genom att köra `bash bin/update.sh`.

## Köra lokalt
Klona projektet och kopiera innehållet i .env.example till .env. Denna fil ska inte läggas under source-control. Hosta sedan public-mappen på valfritt sätt, ex. genom att köra `php artisan serve`.
