@props(['active' => 'login'])

<div class="relative isolate flex justify-center px-4 pb-12 pt-4 sm:pt-8">
  <div class="absolute left-1/2 top-1/3 -z-10 h-[28rem] w-[28rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-gkk/10 blur-3xl"></div>

  <div class="grid w-full max-w-md gap-6 lg:max-w-5xl lg:grid-cols-2 lg:gap-x-8">
    <aside class="relative isolate hidden overflow-hidden rounded-3xl bg-gkk p-10 text-white shadow-xl shadow-gkk/20 lg:flex lg:flex-col lg:justify-end">
      <img src="https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg" alt="" class="landing-hero-image absolute inset-0 -z-20 h-full w-full object-cover">
      <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gkk via-gkk/80 to-gkk/30"></div>

      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white">Insidan</p>
      <h2 class="mt-2 text-3xl font-bold tracking-tight">Allt för dig som medlem, på ett ställe</h2>
      <ul class="mt-8 space-y-4">
        @foreach ([
          ['fa-trophy', 'Anmäl dig till tävlingar'],
          ['fa-users', 'Ta funktionärsuppdrag'],
          ['fa-file-text-o', 'Hitta protokoll och dokument'],
          ['fa-user-circle-o', 'Håll din profil uppdaterad'],
        ] as [$icon, $feature])
          <li class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 backdrop-blur-sm">
              <i class="fa {{ $icon }}"></i>
            </span>
            <span class="text-white/90">{{ $feature }}</span>
          </li>
        @endforeach
      </ul>
    </aside>

    <gkk-auth-tabs initial="{{ $active }}">
      {{ $slot }}
    </gkk-auth-tabs>
  </div>
</div>
