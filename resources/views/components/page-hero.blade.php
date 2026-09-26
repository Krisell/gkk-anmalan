@props(['image', 'title', 'eyebrow' => null, 'position' => 'center'])

<section class="relative isolate overflow-hidden bg-gkk">
  <img src="{{ $image }}" alt="" class="landing-hero-image absolute inset-0 -z-20 h-full w-full object-cover" style="object-position: center {{ $position }}">
  <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gkk via-gkk/70 to-black/40"></div>
  <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/50 to-transparent"></div>

  <div class="mx-auto flex max-w-7xl flex-col justify-end px-4 pt-14 pb-12 sm:h-[44svh] sm:min-h-[340px] sm:max-h-[480px] sm:px-6 sm:pt-16 sm:pb-16 lg:px-8">
    <div class="landing-rise max-w-3xl">
      @if ($eyebrow)
        <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.15em] text-white shadow-sm ring-1 ring-white/40 backdrop-blur-md sm:text-xs sm:tracking-[0.25em]">
          <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
          {{ $eyebrow }}
        </div>
      @endif
      <h1 class="text-[2.75rem] font-extrabold leading-[0.95] tracking-tight text-white drop-shadow-lg sm:text-6xl lg:text-7xl">{{ $title }}</h1>
      @if ($slot->isNotEmpty())
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-white/85 sm:text-xl">{{ $slot }}</p>
      @endif
    </div>
  </div>
</section>
