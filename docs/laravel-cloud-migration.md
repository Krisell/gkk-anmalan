# Moving from One.com to Laravel Cloud

How the site moves from One.com shared hosting to Laravel Cloud with Postgres.

## Decisions

- **Classic cutover with a short maintenance window.** The old site can't connect to the new database (One.com's
  PHP 8.4 has no `pdo_pgsql`), so the database is copied while the old site is in maintenance mode, and DNS is
  switched right after.
- **DNS stays at One.com** for now. It may move later (for example to Cloudflare), but not as part of this move.
- **TTL stays at 3600** until the cutover (One.com doesn't allow lowering it), so the switch takes up to an hour
  to propagate. The new records get TTL 600.
- **No IPv6.** The AAAA records are turned off during preparation.
- **Subdomains** (arr, display, sek) are not moved.
- **Nothing is stored on disk**, there are no queues, and nothing runs on a schedule, so only the database and the
  environment variables move.
- **Same `APP_KEY`** on Cloud as on One.com. Sessions are stored in cookies (`SESSION_DRIVER=cookie`), so with the
  same key members stay logged in through the move.

## Domains and DNS

All domains have their DNS at One.com (`ns01.one.com`, `ns02.one.com`).

| Domain                                                         | Today                                                                                                               | After the move                                            |
| -------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------- |
| `goteborgkk.se`, `www.goteborgkk.se`                           | The app, `46.30.213.62` / `2a02:2350:5:10a:80f7:e5bd:135b:dc05`                                                     | Laravel Cloud, www redirects to the apex                  |
| `gkk-styrkelyft.se`, `www.gkk-styrkelyft.se`                   | One.com redirect (302) to `https://goteborgkk.se/`, `46.30.215.190` / `2a02:2350:5:106:80f2:6470:58c1:287f`         | Laravel Cloud, the app redirects (301) to `goteborgkk.se` |
| `goteborgkraftsportklubb.se`, `www.goteborgkraftsportklubb.se` | One.com web forwards (302) to `http://www.goteborgkk.se/` (under _Egna DNS-inställningar_), `46.30.213.31`, no IPv6 | Laravel Cloud, the app redirects (301) to `goteborgkk.se` |

Only the A and AAAA records for `@` and `www` change. Everything else stays exactly as it is:

- `goteborgkk.se`: MX → Google Workspace (`aspmx.l.google.com` etc.) and the `google-site-verification` TXT record
- `gkk-styrkelyft.se`:
  - MX → Google Workspace, the club's email (`info@gkk-styrkelyft.se`)
  - `mg.gkk-styrkelyft.se` TXT (SPF) and `pic._domainkey.mg.gkk-styrkelyft.se` TXT (DKIM), which Mailgun uses for
    the app's outgoing mail
  - The `ghs.googlehosted.com` CNAMEs (calendar, docs, drive, mail, sites, start)
- `goteborgkraftsportklubb.se`: MX → One.com's own mail service (`mx1–4.pub.mailpod11-cph3.one.com`). It is not used
  (all mail goes through Google Workspace), so it doesn't matter if it ends with the hosting.

### TTL and IPv6

The A/AAAA records for `@` and `www` are One.com's default records ("Standardinställningar för DNS") with a fixed
TTL of 3600. The TTL can't be lowered before the cutover: One.com refuses custom records that point to its own
addresses ("You're trying to add a one.com IP as the destination"). So DNS takes up to an hour to propagate after the
switch. The old site stays in maintenance mode meanwhile, and its public pages keep working.

IPv6 is dropped during preparation. Every visitor can reach the site over IPv4, and an AAAA record left pointing at
One.com would keep IPv6 visitors on the old server after the switch. If Laravel Cloud offers an IPv6 address, it can
be added back afterwards ("no such record" answers are only cached for 600 seconds).

A few days before the cutover, for `goteborgkk.se` and `gkk-styrkelyft.se` (`goteborgkraftsportklubb.se` has no
AAAA records):

1. Under _Standardinställningar för DNS_, turn off the default AAAA records for the domain and for `www`. Leave the
   default A records and everything else on.
2. Run `bin/check-cutover.sh`. `ns01.one.com` should show AAAA `none` and the A records unchanged.

## Laravel Cloud setup

- App connected to `Krisell/gkk-anmalan`, deploying from `master`, EU region.
- Build: `composer install --no-dev`, `npm ci`, `npm run build`. Deploy: `php artisan migrate --force`.
- Postgres 18 (matches `PG_IMAGE` in `bin/mysql-to-pg.sh` and the CI tests), attached to the environment.
- The database is Laravel Serverless Postgres, which has no public access setting (unlike Laravel MySQL and RDS): it
  is always reachable from the internet, protected by its long random password and TLS. There is no VPN or IP
  allowlist either. Connect from TablePlus with _View credentials_ → _Open in database client_ on the database under
  Resources, keep the credentials only there, and rotate the password in Cloud if it may have leaked.
- Environment variables copied from the One.com `.env`: the same `APP_KEY`, Mailgun, Sentry, Fortnox, the
  Google/Microsoft OAuth keys. `DB_PREFIX` empty (the conversion script strips the `gkk_` prefix). Also
  `SERVED_BY=cloud`.
- `CACHE_STORE` set to `database` or Cloud's KV cache, not `file`.
- Domains, added under the environment's Network settings, one per domain (all three, including both redirect
  domains; the app redirects them to `goteborgkk.se`): _Redirect from www_, wildcard off, Cloudflare off, **Ensure
  uninterrupted domain transfer on**. Cloud then shows two `_cf-custom-hostname` TXT records and two `_acme-challenge`
  CNAME records (for the domain and for www). Add them under _Egna DNS-inställningar_ at One.com right away, typing
  only the part before the domain as the host name (for example `_acme-challenge.www`). They don't affect the old
  site or the web forwards. Cloud's "DNS only" warning only applies to Cloudflare. Cloud then verifies the domain and
  issues the certificate before any traffic moves, so there is no window without HTTPS. Don't add the A records Cloud
  shows until the cutover.

## Database maintenance mode

Set by `DATABASE_MAINTENANCE=true` in `.env`. While it is on:

- Any database query throws, and the request is answered with a maintenance page (503, `Retry-After: 600`), or with a
  JSON message for API requests. Pages that don't need the database (the landing page, /gkk, /medlem, /dm and so on)
  work as usual.
- Non-GET requests (forms, webhooks), login, registration, password reset, /insidan and /admin show the maintenance
  page directly.
- Logged-in members are treated as guests on the public pages. Their session cookie is left as it is, so they are
  logged in again once maintenance is off.
- Every request is logged to `storage/logs/maintenance-requests.log`, as one line with server, host, method, path,
  status, IP and user agent. On the old server this shows who still reaches it, and when it stops, DNS has propagated.

`SERVED_BY` (for example `onecom` or `cloud`) is sent as the `X-Served-By` header on every response, and is the first
field of each log line. `bin/check-cutover.sh` prints it.

On One.com the configuration is cached, so after changing `.env` run:

```bash
php artisan config:cache
```

The code is in `app/Http/Middleware/DatabaseMaintenanceMiddleware.php`, the page in
`resources/views/maintenance.blade.php`.

## Checking DNS and the servers

`bin/check-cutover.sh` (run locally) shows, for each domain and its www name, the A/AAAA records from `ns01.one.com`
and from 1.1.1.1, 8.8.8.8 and 9.9.9.9 with their TTL, marks One.com addresses, and requests each name over HTTPS to
show the status code and `X-Served-By`. `--watch 60` repeats every minute.

## Cutover runbook

### A few days before

1. Turn off the default AAAA records (above).
2. Add all domains in Cloud with pre-verification and add their TXT/`_acme-challenge` records at One.com. Wait until
   Cloud shows ownership and SSL as verified. Cloud then asks for the origin records (A `103.133.1.1` for `@` and
   `www`); cancel that dialog, they are added at the cutover. Keep the `_acme-challenge` CNAMEs permanently, since
   they renew the certificate. A day or two before the cutover, check that all domains still show as verified and
   retry any that timed out (the records are still there, so it goes through within minutes).
3. Deploy the maintenance mode code to One.com (`bin/update.sh`) and to Cloud. Set `SERVED_BY=onecom` in the One.com
   `.env` and run `php artisan config:cache`. Check with `bin/check-cutover.sh` that `served-by=onecom` shows up.
4. Make sure the redirect from the other domains (`RedirectToMainDomainMiddleware`, a 301 to `https://goteborgkk.se`
   keeping the path) is deployed on Cloud. It must be live there before their DNS changes.
5. Rehearse the data copy: a fresh dump from One.com, `bin/mysql-to-pg.sh` with `CLOUD_DATABASE_URL` set, and a smoke
   test on the `laravel.cloud` URL (log in with password, Google and Microsoft, sign up for a competition, outgoing
   mail, Fortnox, the PDF routes). Note how long the copy takes. Cloud is kept in maintenance mode
   (`DATABASE_MAINTENANCE=true`) until the cutover, so nobody who finds the `laravel.cloud` URL saves data there:
   turn it off for the smoke test and back on afterwards (each change is a redeploy of about 1.5 minutes, and the old
   instances keep answering for a few seconds after the deploy reports success).
6. Pick the time: an early weekday morning, maintenance mode on at about 05:00 Swedish time, done before 07:00.
   Target: Tuesday 3 or Wednesday 4 November 2026, after the 1 November sign-up deadline for DM officials and well
   before the DM on 14–15 November. Fallback: Tuesday 17 November or later; never the DM week.
   `bin/db-write-activity.sql` (run against production in October 2026) showed about 5 writes a day, almost all
   between 07:00 and 22:00, and in 90 days only 5 writes between 04:00 and 06:00 (winter time, UTC+1). Avoid days
   near a sign-up deadline or registration opening; the busiest hours were a burst of sign-ups on a Sunday evening.
   Tell the members beforehand.

### Cutover

1. On One.com: set `DATABASE_MAINTENANCE=true` in `.env` and run `php artisan config:cache`. Check that `/insidan`
   shows the maintenance page and that the landing page still works.
2. Take a fresh MySQL dump. Keep a copy: it is the rollback point.
3. Run `bin/mysql-to-pg.sh` with `CLOUD_DATABASE_URL` set and compare the row counts it prints.
4. On Cloud, set `DATABASE_MAINTENANCE=false` and redeploy (about 1.5 minutes). Then smoke-test quickly on the
   `laravel.cloud` URL (login and one sign-up).
5. For each domain at One.com: under _Egna DNS-inställningar_, create A records for `@` (host name empty) and `www`
   with the address Cloud shows and TTL 600. Then, straight away, turn off the default A records for the domain and
   `www`. Create first and turn off second: while both exist for a few seconds, visitors are only split between the
   servers, while the other order risks resolvers caching "no address" for 10 minutes.
   For `goteborgkraftsportklubb.se`, delete its two web forwards instead of turning off default records, and then
   create the A records.
6. Watch it propagate: run `bin/check-cutover.sh --watch 60` locally, and on One.com
   `tail -f storage/logs/maintenance-requests.log`. Visitors should start showing up with `served-by=cloud`, and the
   One.com log should go quiet within about an hour (the old TTL), apart from bots and the odd resolver that ignores
   the TTL.
7. Check the OAuth callbacks (Google, Microsoft, Fortnox) and the incoming webhooks (`WebhookController`).

Leave the old site in maintenance mode. It costs nothing, and anyone who still reaches it gets the maintenance page
instead of writing to the old database.

### Rollback

Until real data has been written on Cloud: turn the default A records back on and delete the custom ones, and set `DATABASE_MAINTENANCE=false` on One.com followed by `php artisan config:cache`.
After that, the data written on Cloud would have to be copied back by hand, so in practice fix forward.

### Afterwards

- Raise the TTL on the new A records (for example to 3600) once everything is stable.
- Remove the legacy deploy setup (`bin/update.sh`, the _Build and Release Assets_ workflow, the One.com part of
  README.md) and the `build-*` GitHub pre-releases.
- Optionally bring back the Fortnox payment check schedule in `bootstrap/app.php`. Cloud can run the scheduler.
- Cancel One.com hosting only after Cloud has run fine for a while, and keep the domains and their DNS active (or move
  DNS elsewhere first). The Google mail and Mailgun records live there.
- Remove `DatabaseMaintenanceMiddleware` and this document, if they are no longer useful.

### Later (optional): DNS to Cloudflare

After the move, One.com is only the registrar for the three domains (`goteborgkk.se` and `goteborgkraftsportklubb.se`
expire 2027-01-09, `gkk-styrkelyft.se` 2027-10-25) and hosts their DNS. The web forwards are replaced by the app's
redirect, and One.com's mail service is not used. DNS can move to Cloudflare's free plan (full TTL control, CNAME at
the apex, redirect rules). The registrations can stay at One.com; only the nameservers change. Cloudflare's own
registrar most likely doesn't handle `.se`, so moving the registrations would mean another registrar.

All three domains have DNSSEC (a DS record at `.se`). Switching nameservers without handling it makes the domains fail
to resolve for validating resolvers, which takes down both the site and the email. Per domain:

1. Turn off DNSSEC at One.com and wait until `dig +short <domain> DS` returns nothing (allow a day or two).
2. Add the domain in Cloudflare and copy every record from One.com: the A records, the Google MX records and
   CNAMEs, the Mailgun and `google-site-verification` TXT records, and the Laravel Cloud `_cf-custom-hostname` and
   `_acme-challenge` records. All as _DNS only_ (not proxied), since Laravel Cloud already runs behind Cloudflare.
3. Compare the records (`dig @<cloudflare nameserver> <name> <type>` against One.com) and switch the nameservers at
   One.com to Cloudflare's.
4. Once Cloudflare shows the domain as active, turn on DNSSEC in Cloudflare and add its DS record at One.com.
