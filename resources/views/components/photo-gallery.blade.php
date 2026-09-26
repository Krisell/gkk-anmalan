@php
  $photos = [
    ['mark-min.jpg', 'col-span-2 row-span-2'],
    ['bankpress-min.jpg', 'col-span-2'],
    ['bjornlyftare-min.jpg', ''],
    ['tavling-min.jpg', ''],
  ];
@endphp

<section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
  <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
    <x-section-heading title="Bilder" />
    <a href="https://www.instagram.com/goteborgkk/" target="_blank" class="inline-flex items-center gap-2 font-semibold text-gkk transition-colors hover:text-gkk-light">
      <i class="fa fa-instagram text-lg"></i> Fler bilder på Instagram
    </a>
  </div>
  <div class="grid auto-rows-[200px] grid-cols-2 gap-4 md:auto-rows-[240px] md:grid-cols-4">
    @foreach ($photos as [$file, $span])
      <div class="group relative overflow-hidden rounded-2xl bg-gray-200 shadow-md {{ $span }}">
        <img src="https://goteborg-kraftsportklubb.web.app/img/{{ $file }}" alt="" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
        <div class="absolute inset-0 bg-gkk/0 transition-colors duration-300 group-hover:bg-gkk/10"></div>
      </div>
    @endforeach
  </div>
</section>
