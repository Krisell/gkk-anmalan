# Göteborg Kraftsportklubb (GKK) Anmälningsweb

Mer information kommer.

## Uppdatera webben
Efter att önskade ändringar är pushade hit, logga in på servern och kör `git pull` från projektets mapp. Eftersom GKK ligger på One.com utan möjlighet att själv konfigurera virtuella hosts (subdomänen mappas automatiskt till en mapp) måste ev. uppdateringar till public-mappen kopieras till `webroot/anmalan/`. Observera också att index.php har justerats för att hitta rätt, så uppdatera *inte* den. Typiskt är det innehållet i js/ och css/ som behöver kopieras, ifall justeringar har gjorts i frontend. Ett deploy-script för att automatisera detta kommer.

## Köra lokalt
Klona projektet och kopiera innehållet i .env.example till .env. Denna fil ska inte läggas under source-control. Hosta sedan public/index.php på valfritt sätt, ex. genom att köra `php artisan serve`.
