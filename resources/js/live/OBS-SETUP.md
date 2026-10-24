# OBS setup for competition streams

How the club's streaming laptop (Windows, OBS) is configured. The laptop is used only for competition streams, so
everything below is set once and saved; per competition only the competition id and name change (see the checklist at
the end). Background on the overlay itself is in [README.md](README.md).

## Hardware

- Laptop: Acer Aspire Go 15 (Intel Core 5 120U, 8 GB RAM, 2× USB-C, 2× USB-A), always on the charger.
- Camera: an iPhone over USB with [Iriun Webcam](https://iriun.com) (app on the phone, Iriun on the laptop; Apple
  Devices from the Microsoft Store provides the USB driver). It appears in OBS as the device _Iriun Webcam_.
- Internet: at least 10 Mbit/s stable upload for 1080p (check at the venue with fast.com → _Show more info_). With
  less, use 720p (see Output).

## Windows (once)

- Settings → System → Power: never sleep or turn off the screen while plugged in.
- Settings → Windows Update: pause updates on competition days.
- Settings → Privacy & security → Camera: camera access and _Let desktop apps access your camera_ on.

## OBS settings (once)

Create a profile (_Profile → New_, "GKK tävling") and a scene collection (_Scene Collection → New_, "GKK tävling")
before changing anything, so that all of this is saved in them.

### Stream

- Service: **YouTube - RTMPS**, _Connect Account (recommended)_, signed in as (or as a manager of) the club channel.

### Output (Output Mode: Advanced)

Streaming tab:

| Setting           | Value                                        |
| ----------------- | -------------------------------------------- |
| Video Encoder     | QuickSync H.264 (the CPU's hardware encoder) |
| Rate Control      | CBR                                          |
| Bitrate           | 6000 Kbps (3500 Kbps for 720p)               |
| Keyframe Interval | 2 s                                          |

Recording tab:

| Setting          | Value                                                            |
| ---------------- | ---------------------------------------------------------------- |
| Recording Path   | e.g. `Videos\GKK`                                                |
| Recording Format | **Fragmented MP4** (stays playable if OBS or the laptop crashes) |
| Video Encoder    | Use stream encoder                                               |

### Video

- Base and Output resolution: **1920×1080** (Output 1280×720 on a weak connection).
- FPS: **30**.

### General

- _Show confirmation dialog when stopping streams_: on.
- _Automatically record when streaming_: on. A full-quality local copy, as a backup and for checking quality.

### Advanced

- _Automatically reconnect_: on, retry delay 2 s, maximum retries 50.

### Hotkeys

- Switch to each scene: F1–F5 (in the order of the scenes below).

## YouTube broadcast defaults (once)

_Controls → Manage Broadcast_:

| Setting       | Value                                                           |
| ------------- | --------------------------------------------------------------- |
| Title         | The competition's name, e.g. "DM Klassisk Styrkelyft 2026"      |
| Visibility    | Public (**Private** for tests: only the club account can watch) |
| Made for kids | No                                                              |
| Latency       | **Low latency** (can't be changed once live)                    |
| Thumbnail     | 1280×720 JPG/PNG under 2 MB                                     |

Check **Remember these settings** so the dialog is pre-filled next time. Private tests end up under YouTube Studio →
Content → Live; delete them when done.

## Scenes

The broadcast runs in **one scene**: the overlay follows the competition by itself, and everything else (start, pause
and end slates, messages, PB stamps, results of earlier groups, hiding all graphics) is done from the control page on an iPad
(see [Remote control](#remote-control-ipad)). OBS only needs touching to start and stop the stream.

`<site>` is `https://goteborgkk.se` and `<id>` the Powerlifting Live competition id.

| Hotkey | Scene   | Sources (top to bottom)                                                                      |
| ------ | ------- | -------------------------------------------------------------------------------------------- |
| F1     | Tävling | Browser `<site>/live/overlay?competition=<id>`, camera, microphone                           |
| F2     | Demo    | Browser `<site>/live/overlay?demo=true`, camera (for testing and rehearsals, no iPad needed) |

Every Browser source: width **1920**, height **1080**, _Shutdown source when not visible_ **off**, default custom CSS.

- **Add the camera once** (+ → Video Capture Device → _Iriun Webcam_, right-click → Transform → _Fit to screen_), and in
  the other scenes use + → Video Capture Device → **Add Existing**. Changing camera is then a matter of changing
  _Device_ in that one source.
- **More cameras**: one scene per camera ("Tävling – kamera 1", "Tävling – kamera 2"), each with the **same** overlay
  source added via _Add Existing_, so that switching camera doesn't restart a lifter card or result. Several iPhones
  through Iriun is not reliable on Windows (see README); test before relying on it, or use a USB webcam for the second
  angle.
- In the Demo scene, right-click the overlay → _Interact_ to use the demo toolbar and keys (S/N/R/K, P, Esc).
- Separate slate scenes (`?view=slate&slate=start|pause|end`) still work, e.g. without an iPad.

## Remote control (iPad)

1. Open `<site>/live/control` on the iPad (Safari → Share → _Add to Home Screen_ makes it full screen) and sign in
   with the PIN (ask Martin; it is set on the server as `LIVE_CONTROL_PIN`). The iPad stays signed in.
2. There is one broadcast at a time: the control page controls whichever overlay is running, and its preview shows the
   same competition as the overlay in OBS.
3. The top of the page shows what OBS is showing right now, and the status in the corner says whether the overlay in
   OBS is online.

| Section                 | What it does                                                                                                    |
| ----------------------- | --------------------------------------------------------------------------------------------------------------- |
| Visas nu                | Automatik (follows the competition), Resultatlista, Startar snart / Paus / Tack för idag (slates), Ingen grafik |
| Resultat per grupp      | The results list of any group, e.g. an earlier finished one (each group marked Klar, Pågår or Ej startad)       |
| Snabbknappar            | PB stamp for the lifter on screen, the results list for 10 s, skip what is shown                                |
| Meddelande              | A message top left (e.g. "Rekordförsök!"), shown until taken down                                               |
| Inställningar           | The competition's name on the slates, the big lifter card and the results list between rounds on/off            |
| Testa mot en videoström | Only for testing, only on that device: another broadcast (e.g. SM) behind the preview, with a delay             |

The pause slate shows _Nästa grupp startar_ with the time set under _Visas nu_. Several iPads or phones can be signed in
at once; they all see the same state.

### Scene transition

Scene Transitions → + → **Stinger**:

- Video file: `gkk-stinger.webm` (from `resources/js/live/obs/` in the repository).
- Transition Point Type: **Time**, Transition Point: **600 ms**.

## Back up the configuration

Once everything is set up, and after every change:

- _Profile → Export_ (stream, output, video and hotkey settings).
- _Scene Collection → Export_ (scenes and sources).

Keep both in the club's Drive. On a new laptop: install OBS and Iriun, import both, connect the YouTube account again,
and choose the camera.

## Per competition

1. Find the competition id: the `id` in Powerlifting Live URLs, or on <https://se.powerliftinglive.com/xml.php>.
2. Update `competition=` in the Tävling scene's overlay.
3. On the iPad (signed in), set the competition's name under _Inställningar_.
4. At the venue: test upload speed, connect the camera, check View → Stats (no dropped frames), and check that the
   iPad says the overlay in OBS is online.
5. On the iPad choose _Startar snart_. Manage Broadcast: check title, visibility and thumbnail, then _Create broadcast
   and start streaming_.
6. Choose _Automatik_ when the first lifter is called; _Paus_ between groups, _Tack för idag_ at the end.
