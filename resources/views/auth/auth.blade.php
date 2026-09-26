{{-- Shared by /login and /register: both forms are on the page and the tabs switch between them without a reload --}}
@extends('layouts.app')

@section('content')
@php
  $input = 'block w-full rounded-xl border-gray-300 px-4 py-3 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-gkk focus:ring-gkk sm:text-sm';
  $label = 'mb-1.5 block text-sm font-medium text-gray-700';
  $button = 'w-full rounded-xl bg-gkk px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-gkk/20 transition hover:-translate-y-0.5 hover:bg-gkk-light focus:outline-none focus:ring-2 focus:ring-gkk focus:ring-offset-2';
@endphp

<x-auth-card :active="$mode">
  <template v-slot:login>
    @include('auth.partials.login-form')
  </template>

  <template v-slot:register>
    @include('auth.partials.register-form')
  </template>

  <template v-slot:register-after>
    <p>Inte medlem i GKK än? <a href="/medlem" class="font-semibold text-gkk hover:underline">Läs om medlemskap</a></p>
  </template>
</x-auth-card>
@endsection
