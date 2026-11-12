<template>
  <div class="relative min-h-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-slate-100">
    <!-- The slideshow's soft light: a blue glow bottom left and a warm one top right, and the plate rings. -->
    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(49,66,112,0.10),transparent_60%),radial-gradient(ellipse_at_top_right,rgba(246,201,69,0.22),transparent_55%)]"
    ></div>
    <svg
      class="pointer-events-none absolute -right-48 -top-48 h-[36rem] w-[36rem] text-gkk/[0.05]"
      viewBox="0 0 200 200"
      fill="none"
      stroke="currentColor"
      aria-hidden="true"
    >
      <circle cx="100" cy="100" r="98" stroke-width="2" />
      <circle cx="100" cy="100" r="84" stroke-width="1" />
      <circle cx="100" cy="100" r="66" stroke-width="1" />
      <circle cx="100" cy="100" r="16" stroke-width="7" />
    </svg>

    <div class="relative mx-auto max-w-6xl px-4 py-10 sm:px-8 sm:py-16">
      <header class="flex flex-col items-start gap-6 sm:flex-row sm:items-center sm:gap-8">
        <a
          href="/"
          class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-white p-1.5 shadow-2xl shadow-gkk/20 ring-1 ring-gkk/10 sm:h-28 sm:w-28"
        >
          <img class="h-full w-full object-contain" :src="LOGO" alt="Göteborg Kraftsportklubb" />
        </a>
        <div class="min-w-0">
          <div
            class="inline-flex items-center gap-2 rounded-full bg-gkk px-4 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-white"
          >
            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
            Tävlingsdag
          </div>
          <h1 class="mt-3 text-5xl font-extrabold leading-[0.95] tracking-tight text-gkk sm:text-6xl">Sekretariatet</h1>
          <div class="mt-4 h-1.5 w-20 rounded-full bg-amber-300"></div>
        </div>
      </header>
      <p class="mt-6 max-w-2xl text-lg leading-snug text-gkk-light text-balance">
        Länkar och verktyg för tävlingssekreterare – allt som behövs vid sekretariatsbordet på ett ställe.
      </p>

      <!-- Links -->
      <section class="mt-12">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Länkar</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <a
            v-for="link in LINKS"
            :key="link.href"
            :href="link.href"
            target="_blank"
            rel="noopener"
            class="group flex items-start gap-4 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-gkk/20 hover:ring-gkk/25"
          >
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gkk text-amber-300">
              <component :is="link.icon" class="h-7 w-7" :stroke-width="1.75" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="flex items-center gap-1.5 text-lg font-bold leading-tight text-gkk">
                {{ link.title }}
                <ExternalLink
                  class="h-4 w-4 shrink-0 text-gkk-lightest transition group-hover:text-gkk"
                  :stroke-width="2"
                />
              </p>
              <p class="mt-1 text-sm leading-snug text-gkk-light">{{ link.text }}</p>
              <p class="mt-2 truncate text-xs font-medium text-gkk-lightest">{{ link.host }}</p>
            </div>
          </a>
        </div>
      </section>

      <!-- Start lists -->
      <section class="mt-14">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Startlistor</h2>
        <p class="mt-1 max-w-3xl text-gkk-light">
          A4 stående, svartvitt – två grupper per sida med lyftarna i lottnummerordning, så att listan också är
          invägningsordningen. Grupper och lottnummer hämtas från Powerlifting Live.
        </p>

        <div class="mt-5 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10">
          <form class="flex flex-wrap items-end gap-3" @submit.prevent="loadStartLists">
            <label class="block">
              <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Tävlings-id</span>
              <input
                v-model="startLists.competition"
                type="text"
                inputmode="numeric"
                pattern="[0-9]+"
                class="mt-1.5 block w-36 rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
              />
            </label>
            <button
              type="submit"
              :disabled="!/^\d+$/.test(startLists.competition.trim()) || draw.loading"
              class="inline-flex items-center gap-2 rounded-full bg-gkk/5 px-4 py-2.5 font-semibold text-gkk transition hover:bg-gkk hover:text-white disabled:cursor-not-allowed disabled:opacity-40"
            >
              <RefreshCw class="h-4 w-4" :class="{ 'animate-spin': draw.loading }" :stroke-width="2" />
              Hämta lottning
            </button>
            <p class="pb-2.5 text-sm text-gkk-light">
              <template v-if="draw.error">{{ draw.error }}</template>
              <template v-else-if="draw.groups.length">
                {{ liftersCount }} lyftare i {{ draw.groups.length }} grupper · hämtad {{ draw.drawnAt }}
                <span v-if="withoutLot" class="font-semibold text-red-700">· {{ withoutLot }} utan lottnummer</span>
              </template>
            </p>
          </form>

          <div v-if="draw.groups.length" class="mt-5 flex flex-wrap gap-x-5 gap-y-3">
            <label v-for="group in draw.groups" :key="group.name" class="block">
              <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light"
                >{{ group.name }} · invägning</span
              >
              <input
                v-model="startLists.weighIn[group.name]"
                type="time"
                class="mt-1.5 block w-36 rounded-xl border-0 bg-white px-4 py-2 text-gkk shadow-sm ring-1 ring-gkk/15 focus:ring-2 focus:ring-gkk"
              />
            </label>
          </div>
        </div>

        <div v-if="startListPages.length" class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <button
            v-for="(page, index) in startListPages"
            :key="index"
            type="button"
            class="group rounded-2xl bg-white/90 p-3 text-left shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-gkk/20 hover:ring-gkk/25 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gkk"
            @click="printStartLists([page])"
          >
            <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
              <StartListPage v-bind="startListProps(page)" preview />
            </div>
            <div class="mt-3 flex items-center justify-between gap-2 px-1">
              <span class="truncate font-bold text-gkk">{{ page.map((group) => group.name).join(' och ') }}</span>
              <span
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-gkk/5 px-3 py-1 text-sm font-semibold text-gkk transition group-hover:bg-gkk group-hover:text-white"
              >
                <Printer class="h-4 w-4" :stroke-width="2" />
                Skriv ut
              </span>
            </div>
          </button>
        </div>
        <div v-if="startListPages.length > 1" class="mt-5 flex justify-end">
          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-full bg-gkk px-5 py-2.5 font-bold text-white shadow-lg shadow-gkk/20 transition hover:bg-gkk-light"
            @click="printStartLists(startListPages)"
          >
            <Printer class="h-4 w-4 text-amber-300" :stroke-width="2" />
            Skriv ut alla ({{ startListPages.length }} sidor)
          </button>
        </div>
      </section>

      <!-- Competition cards -->
      <section v-if="draw.groups.length" class="mt-14">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Tävlingskort</h2>
        <p class="mt-1 max-w-3xl text-gkk-light">
          Kort att klippa isär – kroppsvikt, försök och resultat fylls i för hand. Korten kommer i grupp- och
          lottordning. Välj storlek: färre kort per sida ger större rutor att skriva i.
        </p>
        <p v-if="!draw.withLicense" class="mt-2 max-w-3xl text-sm font-semibold text-amber-700">
          Licensnumret kommer bara med när du är
          <a href="/login" class="underline hover:text-gkk">inloggad som admin</a> – annars lämnas fältet tomt.
        </p>

        <div
          class="mt-5 grid gap-6 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 md:grid-cols-[minmax(0,18rem)_1fr]"
        >
          <div class="self-start overflow-hidden rounded-lg ring-1 ring-black/10">
            <TavlingskortPage :cards="cardPages(draw.groups)[0] ?? []" :layout="cardsLayout" preview />
          </div>
          <div class="flex flex-col gap-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Storlek</p>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="layout in CARD_LAYOUTS"
                :key="layout.perPage"
                type="button"
                :aria-pressed="cardsLayout.perPage === layout.perPage"
                class="rounded-full px-4 py-2 text-sm font-semibold ring-1 transition"
                :class="
                  cardsLayout.perPage === layout.perPage
                    ? 'bg-gkk text-white ring-gkk'
                    : 'bg-white text-gkk ring-gkk/15 hover:bg-gkk/5'
                "
                @click="cardSettings.perPage = layout.perPage"
              >
                {{ layout.label }}
              </button>
            </div>
            <p class="mt-2 text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Skriv ut en grupp</p>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="group in draw.groups"
                :key="group.name"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-full bg-gkk/5 px-4 py-2 text-sm font-semibold text-gkk transition hover:bg-gkk hover:text-white"
                @click="printCards([group])"
              >
                <Printer class="h-4 w-4" :stroke-width="2" />
                {{ group.name }}
                <span class="font-normal opacity-70">({{ cardPages([group]).length }} s.)</span>
              </button>
            </div>
            <div class="mt-auto flex justify-end">
              <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full bg-gkk px-5 py-2.5 font-bold text-white shadow-lg shadow-gkk/20 transition hover:bg-gkk-light"
                @click="printCards(draw.groups)"
              >
                <Printer class="h-4 w-4 text-amber-300" :stroke-width="2" />
                Skriv ut alla ({{ cardPages(draw.groups).length }} sidor)
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Signs -->
      <section class="mt-14">
        <div class="flex flex-wrap items-end justify-between gap-2">
          <div>
            <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Platslappar</h2>
            <p class="mt-1 text-gkk-light">
              A4 liggande, svartvitt med klubbens logga. Klicka på en lapp för att skriva ut den.
            </p>
          </div>
        </div>

        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <button
            v-for="sign in SIGNS"
            :key="sign"
            type="button"
            class="group rounded-2xl bg-white/90 p-3 text-left shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-gkk/20 hover:ring-gkk/25 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gkk"
            @click="print(sign)"
          >
            <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
              <Platslapp :text="sign" preview />
            </div>
            <div class="mt-3 flex items-center justify-between px-1">
              <span class="font-bold text-gkk">{{ sign }}</span>
              <span
                class="inline-flex items-center gap-1.5 rounded-full bg-gkk/5 px-3 py-1 text-sm font-semibold text-gkk transition group-hover:bg-gkk group-hover:text-white"
              >
                <Printer class="h-4 w-4" :stroke-width="2" />
                Skriv ut
              </span>
            </div>
          </button>

          <!-- A sign of one's own -->
          <form
            class="rounded-2xl bg-gkk p-3 text-left shadow-xl shadow-gkk/20 ring-1 ring-gkk/10"
            @submit.prevent="print(custom.trim())"
          >
            <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
              <Platslapp :text="custom.trim()" placeholder="Egen text" preview />
            </div>
            <div class="mt-3 flex items-center gap-2">
              <label class="sr-only" for="custom-sign">Egen platslapp</label>
              <input
                id="custom-sign"
                v-model="custom"
                type="text"
                maxlength="60"
                placeholder="Skriv egen text…"
                class="min-w-0 flex-1 rounded-full border-0 bg-white/10 px-4 py-1.5 text-white placeholder:text-white/50 focus:bg-white/15 focus:ring-2 focus:ring-amber-300"
              />
              <button
                type="submit"
                :disabled="!custom.trim()"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-300 px-3 py-1.5 text-sm font-bold text-gkk transition hover:bg-amber-200 disabled:cursor-not-allowed disabled:opacity-40"
              >
                <Printer class="h-4 w-4" :stroke-width="2" />
                Skriv ut
              </button>
            </div>
          </form>
        </div>
      </section>
      <!-- Signpost -->
      <section class="mt-14">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Vägvisare</h2>
        <p class="mt-1 text-gkk-light">
          A4 liggande, svartvitt – välj åt vilket håll varje plats ligger från där skylten sitter. Platserna ordnas
          efter riktning, och mitten döljer en plats.
        </p>

        <form
          class="mt-5 grid gap-6 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 lg:grid-cols-[1.4fr_1fr]"
          @submit.prevent="printSignpost"
        >
          <div class="self-start overflow-hidden rounded-lg ring-1 ring-black/10">
            <Vagvisare :items="signpost" :kicker="stream.competition.trim()" preview />
          </div>
          <div class="flex flex-col gap-5">
            <div class="grid grid-cols-2 gap-x-4 gap-y-5">
              <fieldset v-for="item in signpost" :key="item.label">
                <legend class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">{{ item.label }}</legend>
                <div class="mt-2 grid w-max grid-cols-3 gap-1">
                  <button
                    v-for="choice in COMPASS"
                    :key="choice.direction"
                    type="button"
                    :aria-label="`${item.label}: ${choice.name}`"
                    :aria-pressed="item.direction === choice.direction"
                    class="flex h-9 w-9 items-center justify-center rounded-lg ring-1 transition"
                    :class="
                      item.direction === choice.direction
                        ? 'bg-gkk text-amber-300 ring-gkk'
                        : 'bg-white text-gkk-light ring-gkk/15 hover:bg-gkk/5 hover:text-gkk'
                    "
                    @click="item.direction = choice.direction"
                  >
                    <component :is="choice.icon" class="h-5 w-5" :stroke-width="2.25" />
                  </button>
                </div>
              </fieldset>
            </div>
            <div class="mt-auto flex items-center justify-between gap-3">
              <button type="button" class="text-sm font-medium text-gkk-light hover:text-gkk" @click="resetSignpost">
                Återställ
              </button>
              <button
                type="submit"
                :disabled="!signpost.some((item) => item.direction)"
                class="inline-flex items-center gap-2 rounded-full bg-gkk px-5 py-2.5 font-bold text-white shadow-lg shadow-gkk/20 transition hover:bg-gkk-light disabled:cursor-not-allowed disabled:opacity-40"
              >
                <Printer class="h-4 w-4 text-amber-300" :stroke-width="2" />
                Skriv ut
              </button>
            </div>
          </div>
        </form>
      </section>

      <!-- Welcome sign -->
      <section class="mt-14">
        <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Välkomstskylt</h2>
        <p class="mt-1 text-gkk-light">A4 liggande, svartvitt – till entrén eller tävlingsflaket.</p>

        <form
          class="mt-5 grid gap-6 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 lg:grid-cols-[1.4fr_1fr]"
          @submit.prevent="print(welcome.title.trim(), welcome.from.trim())"
        >
          <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
            <Platslapp :text="welcome.title.trim()" :sub-text="welcome.from.trim()" placeholder="Välkomna" preview />
          </div>
          <div class="flex flex-col gap-4">
            <label class="block">
              <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Rubrik</span>
              <textarea
                v-model="welcome.title"
                rows="3"
                class="mt-1.5 resize-none w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
              ></textarea>
            </label>
            <label class="block">
              <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Avsändare</span>
              <textarea
                v-model="welcome.from"
                rows="3"
                class="mt-1.5 resize-none w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
              ></textarea>
            </label>
            <div class="mt-auto flex items-center justify-between gap-3">
              <button type="button" class="text-sm font-medium text-gkk-light hover:text-gkk" @click="resetWelcome">
                Återställ
              </button>
              <button
                type="submit"
                :disabled="!welcome.title.trim()"
                class="inline-flex items-center gap-2 rounded-full bg-gkk px-5 py-2.5 font-bold text-white shadow-lg shadow-gkk/20 transition hover:bg-gkk-light disabled:cursor-not-allowed disabled:opacity-40"
              >
                <Printer class="h-4 w-4 text-amber-300" :stroke-width="2" />
                Skriv ut
              </button>
            </div>
          </div>
        </form>
      </section>

      <!-- Stream splash screens -->
      <section class="mt-14">
        <div class="flex flex-wrap items-start gap-2 sm:items-center">
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gkk text-amber-300">
            <Youtube class="h-5 w-5" :stroke-width="1.75" />
          </div>
          <div>
            <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-gkk-light">Bilder till sändningen</h2>
            <p class="text-gkk-light">
              1920×1080 PNG – som bild i OBS före start och i pauser, eller som miniatyr på YouTube.
            </p>
          </div>
        </div>

        <div
          class="mt-5 grid gap-4 rounded-2xl bg-white/90 p-5 shadow-xl shadow-gkk/10 ring-1 ring-gkk/10 md:grid-cols-[1fr_1fr_auto] md:items-end"
        >
          <label class="block">
            <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Tävling</span>
            <input
              v-model="stream.competition"
              type="text"
              class="mt-1.5 w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
            />
          </label>
          <label class="block">
            <span class="text-xs font-bold uppercase tracking-[0.14em] text-gkk-light">Datum och plats</span>
            <input
              v-model="stream.info"
              type="text"
              class="mt-1.5 w-full rounded-xl border-0 bg-white px-4 py-2.5 text-gkk shadow-sm ring-1 ring-gkk/15 placeholder:text-gkk-lightest focus:ring-2 focus:ring-gkk"
            />
          </label>
          <button type="button" class="py-2.5 text-sm font-medium text-gkk-light hover:text-gkk" @click="resetStream">
            Återställ
          </button>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
          <div
            v-for="(screen, index) in screens"
            :key="index"
            class="rounded-2xl p-3 shadow-xl ring-1 ring-gkk/10"
            :class="screen.custom ? 'bg-gkk shadow-gkk/20' : 'bg-white/90 shadow-gkk/10'"
          >
            <div class="overflow-hidden rounded-lg ring-1 ring-black/10">
              <StreamSplash
                :ref="(el) => (splashes[index] = el)"
                :kicker="stream.competition"
                :title="screen.custom ? customSplash : screen.title"
                :sub-text="stream.info"
                placeholder="Egen text"
              />
            </div>
            <div class="mt-3 flex items-center gap-2 px-1">
              <span v-if="!screen.custom" class="min-w-0 flex-1 truncate font-bold text-gkk">{{ screen.title }}</span>
              <input
                v-else
                v-model="customSplash"
                type="text"
                maxlength="60"
                placeholder="Skriv egen text…"
                aria-label="Egen bild till sändningen"
                class="min-w-0 flex-1 rounded-full border-0 bg-white/10 px-4 py-1.5 text-white placeholder:text-white/50 focus:bg-white/15 focus:ring-2 focus:ring-amber-300"
              />
              <button
                type="button"
                :disabled="screen.custom && !customSplash.trim()"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-40"
                :class="
                  screen.custom
                    ? 'bg-amber-300 text-gkk hover:bg-amber-200'
                    : 'bg-gkk/5 text-gkk hover:bg-gkk hover:text-white'
                "
                @click="splashes[index].download(fileName(screen.custom ? customSplash : screen.title))"
              >
                <Download class="h-4 w-4" :stroke-width="2" />
                Ladda ner
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- The sheet that is printed: off screen, and alone on the page when printing (see resources/views/sek.blade.php). -->
    <Teleport to="body">
      <div
        class="sign-print"
        :class="{ 'sign-print-pages': ['startlists', 'cards'].includes(printing) }"
        aria-hidden="true"
      >
        <template v-if="printing === 'startlists'">
          <div v-for="(page, index) in printPages" :key="index" class="print-page portrait">
            <StartListPage v-bind="startListProps(page)" />
          </div>
        </template>
        <template v-else-if="printing === 'cards'">
          <div v-for="(page, index) in printPages" :key="index" class="print-page" :class="cardsLayout.orientation">
            <TavlingskortPage :cards="page" :layout="cardsLayout" />
          </div>
        </template>
        <Vagvisare
          v-else-if="printing === 'signpost'"
          ref="printSheet"
          :items="signpost"
          :kicker="stream.competition.trim()"
        />
        <Platslapp v-else ref="printSheet" :text="printText" :sub-text="printSubText" />
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue'
import {
  ArrowDown,
  ArrowDownLeft,
  ArrowDownRight,
  ArrowLeft,
  ArrowRight,
  ArrowUp,
  ArrowUpLeft,
  ArrowUpRight,
  Database,
  Download,
  ExternalLink,
  EyeOff,
  MonitorPlay,
  Presentation,
  Printer,
  RefreshCw,
  Ticket,
  Youtube,
} from 'lucide-vue-next'
import Platslapp from './Platslapp.vue'
import StartListPage from './StartListPage.vue'
import StreamSplash from './StreamSplash.vue'
import TavlingskortPage from './TavlingskortPage.vue'
import { CARD_LAYOUTS, cardLayout } from './cardLayouts.js'
import Vagvisare from './Vagvisare.vue'
import { STREAM_IMAGES } from '../../live/streamImages.js'
import { normalizeStartLists } from '../../live/normalize.js'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const LINKS = [
  {
    icon: MonitorPlay,
    title: 'Powerlifting Live',
    text: 'Tävlingssystemet – lyftordning, försök och domarsignaler.',
    href: 'https://se.powerliftinglive.com',
    host: 'se.powerliftinglive.com',
  },
  {
    icon: Database,
    title: 'Styrkelyft Data',
    text: 'Anmälningar till tävlingen och rapportering av resultat.',
    href: 'https://data.styrkelyft.se',
    host: 'data.styrkelyft.se',
  },
  {
    icon: Ticket,
    title: 'Höjningskort',
    text: 'PDF att skriva ut när höjningskorten tar slut.',
    href: 'https://www.styrkelyft.se/wp-content/uploads/2026/06/Tavling-Hojningslapp-pdf.pdf',
    host: 'styrkelyft.se',
  },
  {
    icon: Presentation,
    title: 'Slideshow',
    text: 'Bildspelet för hallens skärm – resultat, rekord och meddelanden.',
    href: '/slides-for-screen',
    host: '/slides-for-screen',
  },
]

const SIGNS = ['Pant', 'Skräp', 'Invägning', 'Ställningshöjder']

const custom = ref('')
const printText = ref('')
const printSubText = ref('')
const printSheet = ref()
// Which sign is printed: a Platslapp or the signpost.
const printing = ref('')

async function printSheetAs(kind) {
  printing.value = kind
  await nextTick()
  await printSheet.value.fit()
  window.print()
}

async function print(text, subText = '') {
  if (!text) {
    return
  }
  printText.value = text
  printSubText.value = subText
  await printSheetAs('platslapp')
}

// The start lists: every group of the competition on Powerlifting Live, by lot number. The weigh-in times aren't in
// Powerlifting Live's feed, so they are typed here (and remembered, like the texts below); the start is two hours
// after the weigh-in opens.
const [startLists] = remembered('sek.startlists', {
  competition: '3261',
  weighIn: {
    'Grupp 1': '07:30',
    'Grupp 2': '07:30',
    'Grupp 3': '11:00',
    'Grupp 4': '11:00',
    'Grupp 5': '15:00',
    'Grupp 6': '15:00',
  },
})
// withLicense: fetched as an admin, with the lifters' license numbers (for the competition cards).
const draw = reactive({ groups: [], drawnAt: '', loading: false, error: '', withLicense: false })
const printPages = ref([])

const liftersCount = computed(() => draw.groups.reduce((sum, group) => sum + group.lifters.length, 0))
const withoutLot = computed(() =>
  draw.groups.reduce((sum, group) => sum + group.lifters.filter((lifter) => lifter.lot == null).length, 0),
)

async function loadStartLists() {
  const id = startLists.competition.trim()
  if (!/^\d+$/.test(id)) {
    return
  }
  draw.loading = true
  draw.error = ''
  try {
    // Admins get the license numbers too; anyone else the public results, without them.
    const headers = { Accept: 'application/json' }
    let response = await fetch(`/live/start-lists/${id}`, { headers })
    const withLicense = response.ok
    if ([401, 403].includes(response.status)) {
      response = await fetch(`/live/results/${id}`, { headers })
    }
    if (!response.ok) {
      throw new Error()
    }
    draw.groups = normalizeStartLists(await response.json())
    draw.withLicense = withLicense
    draw.drawnAt = new Date().toLocaleString('sv-SE', { dateStyle: 'medium', timeStyle: 'short' })
    if (!draw.groups.length) {
      draw.error = 'Tävlingen har inga lyftare på Powerlifting Live.'
    }
  } catch {
    draw.error = 'Kunde inte hämta tävlingen från Powerlifting Live.'
  } finally {
    draw.loading = false
  }
}
onMounted(loadStartLists)

// As few sheets as possible: two groups on a page, as long as both fit (a page holds about 22 lifters).
const LIFTERS_PER_PAGE = 22
const startListPages = computed(() => {
  const pages = []
  for (const group of draw.groups) {
    const page = pages.at(-1)
    if (page?.length === 1 && page[0].lifters.length + group.lifters.length <= LIFTERS_PER_PAGE) {
      page.push(group)
    } else {
      pages.push([group])
    }
  }
  return pages
})

// A page keeps its number when printed alone (the number in the whole set).
function startListProps(page) {
  return {
    groups: page.map((group) => ({ ...group, weighIn: startLists.weighIn[group.name] ?? '' })),
    competition: stream.competition.trim(),
    info: stream.info.trim(),
    page: startListPages.value.findIndex((other) => other[0].name === page[0].name) + 1,
    pages: startListPages.value.length,
  }
}

async function printPortrait(kind, pages) {
  printPages.value = pages
  printing.value = kind
  await nextTick()
  await document.fonts?.ready
  window.print()
}
const printStartLists = (pages) => printPortrait('startlists', pages)

// The competition cards: two, four or six to a page (remembered), by group and lot number, the groups one after
// another so that no paper is left blank.
const [cardSettings] = remembered('sek.cards', { perPage: 6 })
const cardsLayout = computed(() => cardLayout(cardSettings.perPage))
function cardPages(groups) {
  const perPage = cardsLayout.value.perPage
  const cards = groups.flatMap((group) => group.lifters.map((lifter) => ({ group: group.name, lifter })))
  return Array.from({ length: Math.ceil(cards.length / perPage) }, (_, index) =>
    cards.slice(index * perPage, (index + 1) * perPage),
  )
}
const printCards = (groups) => printPortrait('cards', cardPages(groups))

// The signpost: where each place is from where the sign is posted, which differs from sign to sign. In the order of
// the compass on the page, with no direction in the middle.
const COMPASS = [
  { direction: 'nw', name: 'snett framåt vänster', icon: ArrowUpLeft },
  { direction: 'n', name: 'rakt fram', icon: ArrowUp },
  { direction: 'ne', name: 'snett framåt höger', icon: ArrowUpRight },
  { direction: 'w', name: 'vänster', icon: ArrowLeft },
  { direction: '', name: 'visas inte', icon: EyeOff },
  { direction: 'e', name: 'höger', icon: ArrowRight },
  { direction: 'sw', name: 'snett bakåt vänster', icon: ArrowDownLeft },
  { direction: 's', name: 'bakåt', icon: ArrowDown },
  { direction: 'se', name: 'snett bakåt höger', icon: ArrowDownRight },
]
const SIGNPOST = [
  { label: 'Tävling', direction: 'n' },
  { label: 'Uppvärmning', direction: 'e' },
  { label: 'Invägning', direction: 'w' },
  { label: 'Omklädning', direction: 's' },
]
const signpost = reactive(SIGNPOST.map((item) => ({ ...item })))
const resetSignpost = () => SIGNPOST.forEach((item, index) => Object.assign(signpost[index], item))
const printSignpost = () => printSheetAs('signpost')

// The texts of the welcome sign and the stream's splash screens are remembered in this browser until the next
// competition, so they only need to be typed once.
function remembered(key, defaults) {
  let saved = {}
  try {
    saved = JSON.parse(localStorage.getItem(key) ?? '{}') ?? {}
  } catch {
    // Not remembered (e.g. a private window): the defaults.
  }
  const state = reactive({ ...defaults, ...saved })
  watch(state, () => {
    try {
      localStorage.setItem(key, JSON.stringify(state))
    } catch {
      // Not remembered, but still used on this page.
    }
  })
  const reset = () => Object.assign(state, defaults)
  return [state, reset]
}

const [welcome, resetWelcome] = remembered('sek.welcome', {
  title: 'Välkomna till Distriktsmästerskapen i Klassisk Styrkelyft',
  from: 'Göteborg Kraftsportklubb och Västra Götalands Styrkelyftförbund',
})

const [stream, resetStream] = remembered('sek.stream', {
  competition: 'DM i klassisk styrkelyft 2026',
  info: 'Lördag 14 november · Friskis & Svettis Majorna, Göteborg',
})

// The same images can be shown in the stream's overlay, from the control page (/live/control).
const screens = [...Object.values(STREAM_IMAGES).map((title) => ({ title })), { custom: true }]
const customSplash = ref('')
const splashes = []

function fileName(title) {
  return [stream.competition, title]
    .join(' ')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
}
</script>
