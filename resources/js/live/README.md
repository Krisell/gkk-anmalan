# Live competition overlay (OBS HUD)

A transparent broadcast overlay showing the current lifter, attempt and requested weight, the next lifter, and the
result (with referee lights) of each attempt. It is rendered by OBS as a Browser Source on top of the camera.

```
/live/overlay?competition=3001     live data from Powerlifting Live competition 3001
/live/overlay?demo=true            simulated competition, no network requests
```

## How it works

```
OBS Browser Source ── polls every 1 s ──▶ /live/feed/{id} (Laravel, cached 1 s) ──▶ se.powerliftinglive.com/feed.php
```

- **Data source.** Powerlifting Live publishes live state as MQTT topics for its own screens. The latest
  message of each topic can be read over HTTP from `feed.php?key=competition/{id}/{topic}`, without any
  credentials. We use `next_lifters` (lifter on the platform and requested weight), `scoresheet/current`
  (current discipline and the most recently judged lifts) and `referee/decision` (referee lights).
- **Proxy.** `feed.php` sends no CORS headers, so the browser can't call it directly.
  `LiveOverlayController@feed` fetches the topics, trims them (the scoresheet is ~36 KB, the response ~3 KB), and
  caches the result for one second so that several open overlays share one upstream request.
  - The small topics (lifter order, referee decision) are fetched every second. The large scoresheet only when one of
    them changed (and for 3 seconds after), otherwise every 10 seconds: roughly one fetch per lift instead of one per
    second.
  - Requests carry the User-Agent `GKK live overlay (+<APP_URL>)` so that Powerlifting Live can see who is calling.
  - Only our server talks to Powerlifting Live. If it were ever blocked, only the overlay stops working; the network at
    the venue (and the competition administration) is unaffected.
  - Entries marked `next_group` (lifters of the following group) are left out, as on Powerlifting Live's own screens.
- **Client** (`resources/js/live/`), a separate Vite entry that doesn't load the regular site:
  - `sources/` – where data comes from: `httpSource.js` (the proxy) or `demoSource.js` (simulation).
  - `normalize.js` – converts the Powerlifting Live format to the internal model. Nothing else knows the external schema.
  - `useLiveFeed.js` – polling: never overlapping, 5 s request timeout, back-off on errors, keeps the last valid
    state, and only updates the UI when the normalized data actually changed.
  - `overlayState.js` – decides when to show a result. Attempts are identified by lifter and lift (e.g. `110845:BP2`)
    and referee decisions by their own id, so unchanged polls or a page reload never replay an old result.
  - `components/` and `overlay.css` – the visual design only. Colors and fonts are CSS variables in `overlay.css`,
    using the club colors from `resources/css/main.css` (navy `#314270`, `#45557e`, silver `#a6acb9`).
  - `assets/` – the GKK logo shown in the panel and the demo backdrop photo.

The referee lights are shown large in the bottom right corner (`RefereePanel.vue`, beside the lower third): each lamp
lights up as soon as its referee decides, before the other two (Powerlifting Live's own screens only reveal them
together), with reason cards under red lamps, and they stay with the result. The lower third shows the verdict.

When the referee lights arrive the result is shown immediately for the lifter on the platform. The result stays up for
10 seconds even if the next lifter has already been called, and then the overlay switches to the next lifter. If the
feed hasn't answered for 20 seconds the overlay hides itself rather than show outdated information, and it comes back
automatically when the feed recovers.

The next lifter (the second entry of `next_lifters`) is shown in a tab labelled _Därefter_ above the panel. While a result is still shown,
the lifter already called to the platform is shown as next.

When a new lifter is called to the platform, a large **lifter card** flips in at the center of the screen (name, club,
weight class, attempt and requested weight, attempts so far, total and placing). After 5 seconds it
travels down and flips over into the lower third. It is not shown on a page reload, when the same lifter takes
another attempt, or while a result is on screen (it waits until the result has been shown).

### Results list

`/live/overlay?competition=<id>&view=scoreboard` shows only a results list of the group on the platform: lifters per
weight class and category (places within each), finished disciplines as the best lift, the current discipline attempt
by attempt, disciplines still to come as empty columns, the total and IPF GL points (Powerlifting Live's `wilks`
field holds the GL points, which matches the GL formula on sampled competitions; but it always uses the powerlifting
formula, also in bench press competitions, so for a group that only benches the points are computed with the bench
press formula instead, see `ipfPoints.js`). A full competition therefore always has five
result columns. The lifter on the platform is highlighted. Until anything in the group has been judged, the list is
titled _Startlista_ (with the declared openers) instead of _Resultatlista – Pågående tävling_.

From the remote control, the results of any other group can be shown too (e.g. an earlier, finished one), titled
_Resultat_ with the best lift per discipline. They come from Powerlifting Live's `scoresheet` topic (the whole
competition, ~220 KB), which the proxy fetches only when asked (`/live/results/{id}`), trims (~75 KB) and caches for
30 s.

To use it during a broadcast, make a separate OBS scene with the camera and this page as a Browser Source, and switch
to it with a hotkey whenever you want to show the standings. Each time the scene goes live the list animates in: the
heading slides in from the left, and then the list folds down below it.
In the demo editor, choose _Resultatlista_ under _Läge_ to preview it.

### Warm-up hall screen

`/live/overlay?view=hall&competition=<id>` is a page for a TV in the warm-up hall, not for OBS: an opaque full screen
with the whole lifting order at the top (_Nästa lyftare_, from `next_lifters`: the lifter on the platform first, five
per line), the results list of the group on the platform (as above) and, below it in smaller text, the start order of
the group that lifts next.

Everything is always shown, also for groups of 20 lifters: the page is as high as its frame's shape allows (on the
club's TV the slideshow's bar takes some of the height), the lines of the lifting order and the next group get lower
as there are more of them, and when the results list's weight class headers don't fit, its heading gets smaller and
the class moves to a column of its own (`fit` in `Scoreboard.vue`). With 20 lifters everywhere the rows of the
results list get small (about 20 px on a 1080p TV); that is the price of showing everyone. Show it on the club's screen from the slideshow's live control (_Visa en webbsida_).

The start order is Powerlifting Live's own "Nästa grupp" list (the `next_lifters_next_group` topic), served by
`/live/next-group/{id}` (cached 5 s, polled every 10 s). It knows the schedule: when groups lift in turns (Grupp 01
squat, Grupp 02 squat, Grupp 01 bench, …) the next group can be the other group of the session, or the next discipline
of the same group, and the list says which group and discipline. It is hidden during the last block. When the data
is more than 30 s old the page keeps showing it, with a red "Ej uppdaterat sedan …" note, rather than going blank.

The referee lights are shown very large in the middle of the screen, as on Powerlifting Live's own screens, from the same
live referee feed as the broadcast: each lamp lights as its referee decides, and the decision stays up for 10 s.
Under the lights is the large lifter card of the lifter being judged. It must never look as if the next lifter has
already lifted, so `hallState.js` decides whose lift the lights belong to when they come on: the attempt on the
platform, unless it was replaced less than 5 s before (then the next lifter was called just before the lights reached
us). While the lights are up, only a newly recorded result can change it, never a change on the platform. The lifter on the platform is marked in the
list with an amber frame. The list also shows each lifter's bodyweight before the club (Powerlifting Live's `weight`).
`&lights=0` turns them off.

When a new lifter is called to the platform, the broadcast's large lifter card flips in, and after 6 s it fades out to
show the list during the lift (no flight down into a lower third). When the lift is done (the lights are the only sign
of that), the card fades in again, the lamps come just after it (so it's always clear whose lift it was), and when the
decision has been up for 10 s, card and lamps flip out together. As on the
broadcast, the card isn't shown for the lifter on the platform when the page opens, nor when the same lifter takes
another attempt. A newly called lifter is introduced 3 s after the call at the earliest, and (in a competition with
referee lights) only once the attempt before has had its lights, or after 8 s if they never reach us (Powerlifting Live
sometimes calls the next lifter just before the decision reaches us). `&intro=0` turns it off, `&introSeconds=N` sets how long it stays.

### Operator controls

| URL parameter    | Default         | Meaning                           |
| ---------------- | --------------- | --------------------------------- |
| `intro=0`        | on              | Never show the large lifter card  |
| `introSeconds=3` | 5 (3.5 in demo) | How long the lifter card stays up |

During a broadcast, right-click the browser source in OBS → _Interact_ and press **Esc** to cut short the lifter
card, a result or the PB stamp immediately. **P** shows a "PB ALERT" stamp diagonally across the screen for about
3 seconds, with the name and weight of the lifter on screen. To take all graphics off air at once, give the browser source a show/hide hotkey in
OBS (_Settings → Hotkeys_); with _Shutdown source when not visible_ unchecked the overlay keeps its state while hidden.

## Broadcast graphics

Modelled on how Swedish TV (SVT, TV4) presents individual sports:

- **Corner graphic.** Our logo, top right, on every view (lower third, lifter card, slates, replay and commentator
  pages) except the results list, which has our logo in its heading, and the stinger. The competition's name, `&title=DM Klassisk styrkelyft`, is shown on the
  slates.
- **Club crests.** As on TV, the crest next to a lifter means _their_ club, so the lower third and the lifter card
  show the lifter's club crest, or none. Add crests in `clubs.js` (logo in `assets/clubs/`, club names as Powerlifting
  Live writes them). Our logo is in the corner graphic.
- **Attempt clock.** Powerlifting Live's `clock` topic, shown above the weight while it runs. It disappears 10 seconds
  before the end, without turning red: it practically never runs out, and when it seems to, the secretariat has
  usually forgotten to stop it.
- **Results list between rounds.** When the last lift of a round has been judged (the next lifter called is on
  another attempt or discipline, or the group is done), the results list is shown automatically: the stinger starts 6 s after
  the lights, so that the lifter's reaction is seen first, and the list stays for 10 s. It comes and goes with the stinger, as an in-page transition. Esc ends it early; `&roundBoard=0`
  turns it off. In the demo, _Sista i omgången_ makes a result end the round.
- **Safe area.** Everything stays within about 5% of the edges (`--safe-x`, `--safe-y` in `overlay.css`), as broadcast
  graphics do, so nothing is cut off on TVs that crop the picture.
- **One motion language.** Shared easing curves and durations (`--motion-*` in `overlay.css`).

### Graphics for their own OBS scenes

Each is the same page with `?view=…`, as its own Browser Source in its own scene. They animate in each time their scene
goes live. None of them needs `?competition=`.

| URL                                                             | Shows                                                                             |
| --------------------------------------------------------------- | --------------------------------------------------------------------------------- |
| `/live/overlay?view=slate&slate=start&title=…`                  | "Sändningen startar snart" with today's date                                      |
| `/live/overlay?view=slate&slate=pause&time=14:30&title=…`       | "Paus – Nästa grupp startar 14:30" (without `time`: "Tävlingen fortsätter snart") |
| `/live/overlay?view=slate&slate=end&title=…`                    | "Tack för idag!" with where to find the results                                   |
| `/live/overlay?view=replay`                                     | A "REPRIS" label, for the scene that plays replays                                |
| `/live/overlay?view=commentators&names=Anna Svensson,Erik Berg` | A name strap for the commentators, for about 9 seconds                            |

In the demo editor, _Skylt_ (or the **K** key) previews the slates. Slates have the stinger's blue, slightly transparent,
as a full-screen background; in the demo the results list and slates come and go with the stinger.

### Scene transition (stinger)

`obs/gkk-stinger.webm` is a 1.2-second transition in club colours: diagonal bands sweep across, cover the whole
picture with our logo in the middle, and sweep out. In OBS: _Scene Transitions_ → **+** → _Stinger_, choose the file,
set _Transition Point Type_ to _Time_ and the point to **600 ms** (when the picture is fully covered). It is a video
with transparency, rendered from `?view=stinger` by
`node resources/js/live/tools/render-stinger.mjs https://<site>/live/overlay?view=stinger` (needs ffmpeg).

## Remote control

The overlay can be controlled from another device, typically an iPad at the venue, through **Firebase's Realtime
Database** (the club's existing Firebase project). How to use it is described in [OBS-SETUP.md](OBS-SETUP.md).

```
iPad /live/control ──writes──▶ Realtime Database live/… ──pushes──▶ overlay in OBS
       ▲  PIN → POST /live/control/token → Firebase custom token   ◀── presence (online, competition, what is shown)
```

- **Data** (`remote/controlState.js` normalizes all of it):
  - `control`: what is shown (`mode`: `auto`, `list` with `listGroup` for another group than the one on the platform,
    `slate` with `slate` start/pause/end and `pauseTime`, or `hidden`), the competition's name, a message, and whether
    the lifter card and the results list between rounds are on. (Commentator names are supported by the overlay but
    not offered on the control page for now.)
  - `commands`: one-off actions `{ type, at }` (`pb`, `list`, `commentators`, `skip`). The overlay runs only those given
    while it runs (never on a reload) and only within 15 s.
  - `presence/{session}`: each overlay in OBS reports `{ at, competition, showing, feed }`, removed when its
    connection closes. The control page previews the competition the latest overlay reports.
  - There is one broadcast at a time, so there is one set of control data, not one per competition.
- **Access** (`database.rules.json`): anyone may read `live/` (it is what the broadcast shows). Writing `control` and
  `commands` needs a Firebase sign-in with the `liveControl` claim, which `LiveControlController@token` hands out for
  the **PIN**. The PIN is `LIVE_CONTROL_PIN` in the server's environment, so it is neither in the (public) repository
  nor in the built JavaScript; without it remote control is off. Attempts are limited to 10 per minute per IP. The
  overlay itself never signs in; it may only write its own presence entry, in a fixed format.
- **Deploying the rules**: `database.rules.json` must be published to the project once (and after each change), either
  in the Firebase console (Realtime Database → Rules) or with `firebase deploy --only database --project
goteborg-kraftsportklubb`. Until then the database refuses everything and the overlay simply runs on its own.
- **Robustness**: without a connection the overlay keeps the last control state and the automation carries on. The
  control page's preview is the overlay itself (`&preview=1`, which doesn't report presence).
- **Testing locally** with the Firebase emulators (Java needed): `firebase emulators:start --only auth,database
--project goteborg-kraftsportklubb` in the project root, then add `firebaseEmulator=127.0.0.1` to the overlay's and
  the control page's URLs on the local site.

## Running locally

Run the site as usual (see the main README), build the frontend with `vp build` (or run `vp dev`), and open
<https://gkk.test/live/overlay?demo=true> (or whatever your local host is).

Tests: `vp test` (normalizing, result detection, polling, demo scenario) and
`vendor/bin/pest tests/Feature/LiveOverlayTest.php` (proxy).

## Demo mode

Two demos, neither of which makes any network requests:

- **`/live/overlay?demo=true`, the editor.** A toolbar at the top sets everything by hand: big lifter card, lower
  third or results list (switching runs the real animations; keys **S**, **N** and **R** also work while the toolbar
  is folded), name, club, weight class, discipline, attempt, requested weight, a result with referee lights (a
  dropdown; _Inget_ clears it) and the lifter after the current one (_Därefter_). The results list shows a made-up
  group of two weight classes with Senior and Junior lifters, ranked within each, with IPF GL points. Earlier attempts, earlier
  disciplines, total and placing are made up at random; _Slumpa om tidigare lyft_ rolls them again. Clicking _Demo_ folds the toolbar away (remembered in the browser).
- **`/live/overlay?demo=scenario`, a scripted competition.** Steps through a lifter preparing, a changed requested
  weight, good and failed lifts with referee lights and reason cards, a result recorded without lights, discipline
  changes and the end of a group.

| Parameter    | Default | Meaning                                                   |
| ------------ | ------- | --------------------------------------------------------- |
| `controls=0` | on      | Hide the editor/demo controls (for clean recordings)      |
| `autoplay=0` | on      | Scenario only: don't advance automatically (every 6 s)    |
| `debug=1`    | off     | Show feed status (also works with `competition=`)         |
| `backdrop=0` | on\*    | Hide the demo photo behind the overlay (\*off inside OBS) |

In a normal browser the demo shows a competition photo (`assets/demo-backdrop.jpg`) in place of the camera, so you can
see the complete picture. Inside OBS it is hidden automatically, so the real camera or test video shows through.

Keyboard: Esc skips the lifter card or result. In the scenario, `←`/`→` change step and space plays/pauses. In OBS,
right-click the source → _Interact_ to use the editor or the keys.

## Configuring the competition

- The competition id is the `id` in Powerlifting Live URLs, e.g. `screen_scoresheet.php?id=3001`. It is also listed
  on the _Dataflöde_ page (<https://se.powerliftinglive.com/xml.php>).
- No API key is needed. The Powerlifting Live host can be changed with `POWERLIFTINGLIVE_URL` in `.env`
  (default `https://se.powerliftinglive.com`).
- Add `&debug=1` to see whether the feed is live, stale or offline, plus the current attempt and result ids.

## Deploying

Nothing special: merge to `master` and Laravel Cloud deploys it, as for any other change. No new services, cron jobs or environment variables are required.

## Adding the overlay in OBS

The club laptop's complete OBS configuration (settings, scenes, YouTube defaults, backup) is in
[OBS-SETUP.md](OBS-SETUP.md).

1. In the scene, add the camera source first.
2. Add a **Browser** source _above_ the camera:
   - URL: `https://<our site>/live/overlay?competition=<id>&title=<the competition's name>`
   - Width `1920`, height `1080`
   - Keep the default custom CSS (it already sets a transparent background).
   - Leave _Shutdown source when not visible_ unchecked, so polling keeps the state warm.
3. No chroma key, filters or plugins are needed: the page itself is transparent.

If the canvas isn't 16:9 the overlay scales to fit and stays centered. To reload the page (e.g. after a deploy),
open the source's properties and click _Refresh cache of current page_.

## Testing with a prerecorded video

1. Create a test scene and add a **Media Source** with a recorded lifting video (check _Loop_).
2. Add the overlay Browser Source above it, using `/live/overlay?demo=true&controls=0`.
3. In _Settings → Output → Recording_ choose a path and format (e.g. MKV or MP4), then click _Start Recording_.
4. Let the demo run through a few results, stop the recording and check the file: the panel, lights and result
   colors should be crisp, and the video should be visible everywhere around them.

To test the real feed, use `?competition=<id>&debug=1` with a running (or recently finished) competition.

## Checking timing against someone else's live stream

To see how the HUD's timing compares with a competition that someone else streams (e.g. an SM on YouTube), no OBS
is needed:

```
/live/overlay?competition=<id>&debug=1&video=<YouTube link>&delay=<seconds>
```

- `video` plays the YouTube stream (muted) behind the overlay.
- `delay` holds the HUD back by that many seconds. A live stream runs behind reality (YouTube: roughly 5–30 s
  depending on the stream's latency setting), while the feed is only ~1–2 s behind. Adjust the delay until the result
  appears when the referee lights come on in the video. What remains is the HUD's own lag. After a (re)load the HUD
  stays empty for `delay` seconds, until it has data that old; showing the live state instead would put it ahead of
  the video.

When we stream ourselves this offset does not exist: OBS combines the HUD with the camera before the stream is
encoded, so the stream's latency applies to both equally.

## Known limitations and suggested improvements

- **`feed.php` is not a documented API.** The format was taken from Powerlifting Live's own screen pages and may
  change without notice. Everything that depends on it is in `normalize.js` and `LiveOverlayController`.
- **Seen live at SM Klassisk styrkelyft 2026** (competition 2939). `next_lifters` carries the attempt and lift
  directly. Without referee lights, Powerlifting Live publishes the new lifter order about 0.6 s before the scoresheet
  with the result, so the overlay keeps the lifter on the platform for up to 5 s while it waits for the result
  (`RESULT_WAIT_MS` in `overlayState.js`) instead of introducing the next lifter first.
- Referee lights only appear when the competition uses Powerlifting Live's integrated referee system. Otherwise the
  result is shown without lights, when the secretariat records it.
- **Referee decisions come over MQTT, not through the proxy.** `feed.php` never holds `referee/decision` (it stays
  `{}`): the decision only exists as a live message, sent about 0.3–0.5 s before the new lifter order and followed by
  `referee/clear` 1.5 s later. The overlay therefore subscribes to the referee topics itself over
  `wss://se.powerliftinglive.com/ws` (`sources/refereeFeed.js`), like a screen in the hall, and adds the latest
  decision to each proxy response, along with each referee's light so far (`referee/signal`, sent as each referee
  decides; at SM 2026 up to 4 s apart). Their broker accepts connections from our site. `&lights=0` turns this off;
  decisions published while the connection is down are lost, and the result is then shown without lights.
- Latency is up to ~2 seconds (1 s polling + 1 s cache). Powerlifting Live also offers MQTT over WebSocket
  (`wss://se.powerliftinglive.com/ws`), which could give instant updates if needed.
- Fonts load from Google Fonts. Without internet access they fall back to system fonts, but then there would be no
  data either.
- One platform/competition per overlay, and one broadcast (remote control) at a time.
- A hotkey in OBS can't reach the page directly (only via _Interact_); OBS WebSocket or a small control page would
  allow that, but was out of scope for this version.
- The attempt clock is counted from when its value changes. Powerlifting Live publishes the seconds left when the
  clock starts; if the overlay is (re)loaded in the middle of a countdown it only sees that start value, so the clock
  may run a few seconds long until the next attempt. Check at the next live competition.
- PBs are only shown manually (P). Powerlifting Live's lifter data has PB fields (`pr_sq`, `pr_bp`, `pr_dl`,
  `pr_total`), empty in all sampled competitions; if organizers fill them in, the stamp could be shown automatically
  when a good lift beats the PB.
- Ideas for later: information during breaks (e.g. "Paus – nästa grupp startar 14:30"), lifter photos and short
  lifter presentations, the attempt clock (`clock` topic). The feed also has record attempts and secretariat notices
  (`records`, `notice`), deliberately not shown on stream.
- Powerlifting Live's own screens get updates pushed via MQTT instead of polling. Doing the same on our server would
  remove the last second of delay and most of the requests, but needs a long-running process on the server.
