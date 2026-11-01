# Hosting

The site runs on Laravel Cloud with Postgres. It moved there from One.com shared hosting in October 2026; One.com is
now only the registrar and DNS host for the domains.

## Laravel Cloud

- App connected to `Krisell/gkk-anmalan`, deploying from `master` (push to deploy), EU region.
- Build: `composer install --no-dev`, `npm ci`, `npm run build`. Deploy: `php artisan migrate --force` and
  `php artisan gkk:publish-slideshow`.
- Postgres 18 (the same version as the CI tests), attached to the environment. `DB_PREFIX` is empty.
- The database is Laravel Serverless Postgres, which has no public access setting: it is always reachable from the
  internet, protected by its long random password and TLS. There is no VPN or IP allowlist either. Connect from
  TablePlus with _View credentials_ → _Open in database client_ on the database under Resources, keep the credentials
  only there, and rotate the password in Cloud if it may have leaked.
- `CACHE_STORE` is `database` or Cloud's KV cache, not `file`. Sessions are stored in cookies
  (`SESSION_DRIVER=cookie`).
- Nothing is stored on disk and there are no queues.
- Startup and readiness health checks use `/up`, which answers without touching the database.

### Scale to zero

Cloud stops billing for compute while the app sleeps. The app sleeps once it has had no request for the sleep timeout,
and any request wakes it. What lets it sleep:

1. **Cloud's scheduler.** The scheduled tasks are in `bootstrap/app.php` (in UTC; the comment there explains why).
   Cloud reads `schedule:list` at deploy time and wakes the app when a task is due, so changes to the schedule need a
   deploy.
2. **Slideshow updates through Firebase.** The screen (`/slides-for-screen`) loads its data from the app once and then
   subscribes to `slideshow/version` in Firebase's Realtime Database, and reloads when it changes. The app publishes
   that version whenever slides or results change, daily at 06:20 UTC, and on deploy.
   - `FIREBASE_DATABASE_URL=https://goteborg-kraftsportklubb.firebaseio.com` is set on Cloud only (a local `.env` with
     the production service account would otherwise write to the production database).
   - The database rules (`firebase deploy --only database`) let the screen read `slideshow`.
3. **The sleep timeout** should be long enough that the 06:20 and 06:30 UTC tasks share one wake.

## Database maintenance mode

For work on the database (for example moving it), set `DATABASE_MAINTENANCE=true` in the environment variables on
Cloud and redeploy. While it is on:

- Any database query throws, and the request is answered with a maintenance page (503, `Retry-After: 600`), or with a
  JSON message for API requests. Pages that don't need the database (the landing page, /gkk, /medlem, /dm and so on)
  work as usual, and so does the `/up` health check.
- Non-GET requests (forms, webhooks), login, registration, password reset, /insidan and /admin show the maintenance
  page directly.
- Logged-in members are treated as guests on the public pages. Their session cookie is left as it is, so they are
  logged in again once maintenance is off.
- Every request (except `/up`) is logged to `storage/logs/maintenance-requests.log`, as one line with host, method,
  path, status, IP and user agent.

The code is in `app/Http/Middleware/DatabaseMaintenanceMiddleware.php`, the page in
`resources/views/maintenance.blade.php`.

## Domains and DNS

All domains have their DNS at One.com (`ns01.one.com`, `ns02.one.com`). Each is added in Cloud under the environment's
Network settings, with _Redirect from www_ on. The app redirects the other domains (301) to `goteborgkk.se`
(`RedirectToMainDomainMiddleware`).

| Domain                                                         | Points to                                   |
| -------------------------------------------------------------- | ------------------------------------------- |
| `goteborgkk.se`, `www.goteborgkk.se`                           | Laravel Cloud, www redirects to the apex    |
| `gkk-styrkelyft.se`, `www.gkk-styrkelyft.se`                   | Laravel Cloud, redirects to the main domain |
| `goteborgkraftsportklubb.se`, `www.goteborgkraftsportklubb.se` | Laravel Cloud, redirects to the main domain |

The A records for `@` and `www` are custom records under _Egna DNS-inställningar_, next to the Laravel Cloud
`_cf-custom-hostname` TXT and `_acme-challenge` CNAME records. There are no AAAA records. The other records:

- `goteborgkk.se`: MX → Google Workspace (`aspmx.l.google.com` etc.) and the `google-site-verification` TXT record
- `gkk-styrkelyft.se`:
  - MX → Google Workspace, the club's email (`info@gkk-styrkelyft.se`)
  - `mg.gkk-styrkelyft.se` TXT (SPF) and `pic._domainkey.mg.gkk-styrkelyft.se` TXT (DKIM), which Mailgun uses for
    the app's outgoing mail
  - The `ghs.googlehosted.com` CNAMEs (calendar, docs, drive, mail, sites, start)
- `goteborgkraftsportklubb.se`: MX → One.com's own mail service (`mx1–4.pub.mailpod11-cph3.one.com`). It is not used
  (all mail goes through Google Workspace).

When cancelling the One.com hosting, keep the domains and their DNS active (or move DNS elsewhere first): the Google
mail and Mailgun records live there.

## Later (optional): DNS to Cloudflare

One.com is only the registrar for the three domains (`goteborgkk.se` and `goteborgkraftsportklubb.se` expire
2027-01-09, `gkk-styrkelyft.se` 2027-10-25) and hosts their DNS. DNS can move to Cloudflare's free plan (full TTL
control, CNAME at the apex, redirect rules). The registrations can stay at One.com; only the nameservers change.
Cloudflare's own registrar most likely doesn't handle `.se`, so moving the registrations would mean another registrar.

All three domains have DNSSEC (a DS record at `.se`). Switching nameservers without handling it makes the domains fail
to resolve for validating resolvers, which takes down both the site and the email. Per domain:

1. Turn off DNSSEC at One.com and wait until `dig +short <domain> DS` returns nothing (allow a day or two).
2. Add the domain in Cloudflare and copy every record from One.com: the A records, the Google MX records and
   CNAMEs, the Mailgun and `google-site-verification` TXT records, and the Laravel Cloud `_cf-custom-hostname` and
   `_acme-challenge` records. All as _DNS only_ (not proxied), since Laravel Cloud already runs behind Cloudflare.
3. Compare the records (`dig @<cloudflare nameserver> <name> <type>` against One.com) and switch the nameservers at
   One.com to Cloudflare's.
4. Once Cloudflare shows the domain as active, turn on DNSSEC in Cloudflare and add its DS record at One.com.
