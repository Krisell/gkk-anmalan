@props(['title', 'eyebrow' => null, 'dark' => false])

<div {{ $attributes }}>
  @if ($eyebrow)
    <p class="text-sm font-semibold uppercase tracking-[0.2em] {{ $dark ? 'text-gkk-lightest' : 'text-gkk-light' }}">{{ $eyebrow }}</p>
  @endif
  <h2 class="{{ $eyebrow ? 'mt-2' : '' }} text-3xl font-bold tracking-tight sm:text-4xl {{ $dark ? 'text-white' : 'text-gray-900' }}">{{ $title }}</h2>
</div>
