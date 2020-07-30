@extends('layouts.app')

@section('content')
<div class="bg-gray-50 flex flex-col justify-center sm:px-6 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img class="mx-auto h-24 w-auto" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png" alt="GKK Logo">
  </div>

  <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-md">
    <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
      <form action="/login" method="POST">
        @csrf
        <div>
          <div class="mt-1 rounded-md shadow-sm">
            <input id="email" type="email" placeholder="Epost" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
          </div>
        </div>

        <div class="mt-6">
          <div class="mt-1 rounded-md shadow-sm">
            <input id="password" type="password" placeholder="Lösenord" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" name="password" required autocomplete="current-password">
          </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
          <div class="text-sm leading-5">
            <a href="/password/reset" class="font-medium text-gkk hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">
              Glömt ditt lösenord?
            </a>
          </div>
        </div>

        <div class="mt-6">
          <span class="block w-full rounded-md shadow-sm">
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gkk hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
              Logga in
            </button>
          </span>
        </div>
      </form>

      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm leading-5">
            <span class="px-2 bg-white text-gray-500"></span>
          </div>
        </div>

        <gkk-login to="{{ session('url.intended', '') }}"></gkk-login>

        </div>
    </div>
  </div>
</div>
@endsection
