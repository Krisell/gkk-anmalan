@extends('layouts.app')

@section('content')
<div class="flex flex-col justify-center sm:px-6 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img class="mx-auto h-24 w-auto" src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png" alt="GKK Logo">
  </div>

  <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-md">
    <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="rounded-md shadow-sm">
          <div>
            <input aria-label="Epost" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Epost">
          </div>
        </div>

        <div class="mt-6">
          <span class="block w-full rounded-md shadow-sm">
            <ui-button type="submit" class="w-full">
                <div class="w-full text-center">Skicka länk för att återställa lösenord</div>
            </ui-button>
          </span>
        </div>

        @error('email')
        <div class="rounded-md bg-red-50 p-4 mt-2">
            <div class="flex">
                <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm leading-5 font-medium text-red-800">
                        {{ $message }}
                    </h3>
                </div>
            </div>
        </div>
        @enderror
    </form>
</div>
@endsection
