@extends('layouts.app')

@section('content')
<div class="flex flex-col justify-center sm:px-6 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img class="mx-auto h-24 w-auto" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-transparent%20(1).png?alt=media&token=4973abb5-6670-4aec-b036-3d14c30a2584" alt="GKK Logo">
  </div>

  <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-md">
    <h2 class="text-center text-2xl font-thin my-4">Återställ lösenord</h2>
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="rounded-md shadow-sm">
                    <div>
                        <input aria-label="Epost" name="email" type="email" value="{{ $email ?? old('email') }}" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Epost">
                    </div>
                    <div class="-mt-px">
                        <input aria-label="Lösenord" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Lösenord">
                    </div>
                    <div class="-mt-px">
                        <input aria-label="Bekräfta lösenord" name="password_confirmation" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5" placeholder="Bekräfta lösenord">
                    </div>
                </div>

                <x-button class="mt-6">
                    Återställ lösenord
                </x-button>
            </form>
        </div>
    </div>
</div>
@endsection
