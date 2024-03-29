@extends('layouts.app')

@section('content')
<div class="flex flex-col justify-center sm:px-6">
  <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-md">
    <h2 class="text-center text-2xl font-thin my-4">Logga in som medlem</h2>
    <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
      <form action="/login" method="POST">
        @csrf

        <div class="rounded-md shadow-sm">
          <div>
            <input aria-label="Epost" name="email" value="{{ old('email') }}" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Epost">
          </div>
          <div class="-mt-px">
            <input aria-label="Lösenord" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Lösenord">
          </div>
        </div>

        <div class="mt-6">
          <span class="block w-full rounded-md shadow-sm">
            <x-button :prevent="false" class="w-full">
              <div class="w-full text-center">
                Logga in
              </div>
            </x-button>
          </span>
        </div>

        @error('email')
          <div class="text-xs text-center mt-2 text-red-400">Kunde inte logga in med dessa användaruppgifter.</div>
        @enderror

        <div class="mt-6 flex items-center justify-between">
          <div class="text-sm leading-5 mx-auto">
            <a href="/password/reset" class="font-medium text-gkk hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">
              Glömt ditt lösenord?
            </a>
          </div>
        </div>

      </form>

      <div class="mt-6">
        <gkk-login to="{{ session('url.intended', '') }}"></gkk-login>
      </div>
    </div>
  </div>
</div>
@endsection
