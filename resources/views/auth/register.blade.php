@extends('layouts.app')

@section('content')
<div class="flex flex-col justify-center sm:px-6 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img class="mx-auto h-24 w-auto" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png" alt="GKK Logo">
  </div>

  <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-md">
    <h2 class="text-center text-2xl font-hairline my-4">Skapa konto</h2>
    <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="rounded-md shadow-sm">
                <div>
                    <input value="{{ $data->firstName ?? old('first_name') }}" aria-label="Förnamn" name="first_name" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Förnamn">
                </div>
                <div class="-mt-px">
                    <input value="{{ $data->lastName ?? old('last_name') }}" aria-label="Efternamn" name="last_name" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Efternamn">
                </div>
                <div class="-mt-px">
                    <input value="{{ $data->email ?? old('email') }}" aria-label="Epost" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Epost">
                </div>
                <div class="-mt-px">
                    <input aria-label="Lösenord" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Lösenord">
                </div>
                <div class="-mt-px">
                    <input aria-label="Bekräfta lösenord" name="password_confirmation" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Bekräfta lösenord">
                </div>
            </div>

            <ui-button class="mt-4 w-full">
                <div class="text-center w-full">
                    Skapa konto
                </div>
            </ui-button>
            <div class="text-center mt-4 text-xs text-gray-400">
                Innan du kan börja använda ditt konto kommer det behöva godkännas av administratören. När du registrerar dig skickas automatiskt en epostnotis till administratören.
            </div>
        </form>
    </div>
</div>
@endsection
