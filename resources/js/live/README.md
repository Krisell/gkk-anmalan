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

When the referee lights arrive the result is shown immediately for the lifter on the platform. The result stays up for
7 seconds even if the next lifter has already been called, and then the overlay switches to the next lifter. If the
feed hasn't answered for 20 seconds the overlay hides itself rather than show outdated information, and it comes back
automatically when the feed recovers.

The next lifter (the second entry of `next_lifters`) is shown in a tab above the panel. While a result is still shown,
the lifter already called to the platform is shown as next.

When a new lifter is called to the platform, a large **lifter card** flips in at the center of the screen (name, club,
weight class, attempt and requested weight, attempts so far, total, placing and projected placing). After 5 seconds it
travels down and flips over into the lower third. It is not shown on a page reload, when the same lifter takes
another attempt, or while a result is on screen (it waits until the result has been shown).

### Operator controls

| URL parameter    | Default         | Meaning                           |
| ---------------- | --------------- | --------------------------------- |
| `intro=0`        | on              | Never show the large lifter card  |
| `introSeconds=3` | 5 (2.5 in demo) | How long the lifter card stays up |

During a broadcast, right-click the browser source in OBS → _Interact_ and press **Esc** to cut short the lifter
card or a result immediately. To take all graphics off air at once, give the browser source a show/hide hotkey in
OBS (_Settings → Hotkeys_); with _Shutdown source when not visible_ unchecked the overlay keeps its state while hidden.

## Running locally

Run the site as usual (see the main README), build the frontend with `vp build` (or run `vp dev`), and open
<https://gkk.test/live/overlay?demo=true> (or whatever your local host is).

Tests: `vp test` (normalizing, result detection, polling, demo scenario) and
`vendor/bin/pest tests/Feature/LiveOverlayTest.php` (proxy).

## Demo mode

`/live/overlay?demo=true` steps through a simulated competition: a lifter preparing, a changed requested weight,
good and failed lifts with referee lights and reason cards, a result recorded without lights, discipline changes and
the end of a group. It makes no network requests.

| Parameter    | Default | Meaning                                                   |
| ------------ | ------- | --------------------------------------------------------- |
| `autoplay=0` | on      | Don't advance automatically (every 6 s)                   |
| `controls=0` | on      | Hide the demo control panel (for clean recordings)        |
| `debug=1`    | off     | Show feed status (also works with `competition=`)         |
| `backdrop=0` | on\*    | Hide the demo photo behind the overlay (\*off inside OBS) |

In a normal browser the demo shows a competition photo (`assets/demo-backdrop.jpg`, an optimized copy of `public/example-squat.JPG`) in
place of the camera, so you can see the complete picture. Inside OBS it is hidden automatically, so the real camera or
test video shows through.

Keyboard: `←`/`→` previous/next step, space to play/pause, Esc to skip the lifter card or result. In OBS, right-click the source → _Interact_ to use them.

## Configuring the competition

- The competition id is the `id` in Powerlifting Live URLs, e.g. `screen_scoresheet.php?id=3001`. It is also listed
  on the _Dataflöde_ page (<https://se.powerliftinglive.com/xml.php>).
- No API key is needed. The Powerlifting Live host can be changed with `POWERLIFTINGLIVE_URL` in `.env`
  (default `https://se.powerliftinglive.com`).
- Add `&debug=1` to see whether the feed is live, stale or offline, plus the current attempt and result ids.

## Deploying

Nothing special: merge to `master`, wait for the _Build and Release Assets_ action, then run `bin/update.sh` on the
server as for any other change. No new services, cron jobs or environment variables are required.

## Adding the overlay in OBS

1. In the scene, add the camera source first.
2. Add a **Browser** source _above_ the camera:
   - URL: `https://<our site>/live/overlay?competition=<id>`
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
  appears when the referee lights come on in the video. What remains is the HUD's own lag.

When we stream ourselves this offset does not exist: OBS combines the HUD with the camera before the stream is
encoded, so the stream's latency applies to both equally.

## Known limitations and suggested improvements

- **`feed.php` is not a documented API.** The format was taken from Powerlifting Live's own screen pages and may
  change without notice. Everything that depends on it is in `normalize.js` and `LiveOverlayController`.
- **Not yet verified during a live competition.** When this was built no competition was running, so the
  `next_lifters` entry was seen in an empty state only. The attempt number is derived from the lifter's lifts
  (first attempt with a weight but no result), falling back to `current_round`. Check with `&debug=1` at the next
  competition.
- Referee lights only appear when the competition uses Powerlifting Live's integrated referee system. Otherwise
  the result is shown without lights.
- Latency is up to ~2 seconds (1 s polling + 1 s cache). Powerlifting Live also offers MQTT over WebSocket
  (`wss://se.powerliftinglive.com/ws`), which could give instant updates if needed.
- Fonts load from Google Fonts. Without internet access they fall back to system fonts, but then there would be no
  data either.
- One platform/competition per overlay.
- A hotkey in OBS can't reach the page directly (only via _Interact_); OBS WebSocket or a small control page would
  allow that, but was out of scope for this version.
- Ideas for later: information during breaks (e.g. "Paus – nästa grupp startar 14:30"), lifter photos and short
  lifter presentations, the attempt clock (`clock` topic). The feed also has record attempts and secretariat notices
  (`records`, `notice`), deliberately not shown on stream.
- Powerlifting Live's own screens get updates pushed via MQTT instead of polling. Doing the same on our server would
  remove the last second of delay and most of the requests, but needs a long-running process on the server.
