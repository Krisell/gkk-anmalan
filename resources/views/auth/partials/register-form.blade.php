<div class="p-6 sm:p-8 lg:flex lg:flex-1 lg:flex-col lg:justify-center">
  <h1 class="text-2xl font-bold tracking-tight text-gray-900">Skapa konto</h1>
  <p class="mt-1 text-sm text-gray-500">För dig som är medlem i GKK.</p>

  {{-- v-pre: prefilled values come from the query string, so Vue must not compile them as template code --}}
  <form v-pre method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
    @csrf

    @if ($mode === 'register' && $errors->any())
      <div class="flex gap-3 rounded-xl bg-red-50 p-4 text-sm text-red-700 ring-1 ring-red-200">
        <i class="fa fa-exclamation-circle mt-0.5"></i>
        <ul class="space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
      <div>
        <label for="first_name" class="{{ $label }}">Förnamn</label>
        <input id="first_name" value="{{ $data->firstName ?? old('first_name') }}" name="first_name" type="text" autocomplete="given-name" required class="{{ $input }}">
      </div>
      <div>
        <label for="last_name" class="{{ $label }}">Efternamn</label>
        <input id="last_name" value="{{ $data->lastName ?? old('last_name') }}" name="last_name" type="text" autocomplete="family-name" required class="{{ $input }}">
      </div>
    </div>

    <div>
      <label for="birth_year" class="{{ $label }}">Födelseår</label>
      <input id="birth_year" value="{{ old('birth_year') }}" name="birth_year" type="text" inputmode="numeric" maxlength="4" required class="{{ $input }}" placeholder="4 siffror, ex. 2003">
    </div>

    <div>
      <label for="register_email" class="{{ $label }}">Epost</label>
      <input id="register_email" value="{{ $data->email ?? ($mode === 'register' ? old('email') : '') }}" name="email" type="email" autocomplete="email" required class="{{ $input }}">
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
      <div>
        <label for="register_password" class="{{ $label }}">Lösenord</label>
        <input id="register_password" name="password" type="password" autocomplete="new-password" required class="{{ $input }}">
      </div>
      <div>
        <label for="password_confirmation" class="{{ $label }}">Bekräfta lösenord</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="{{ $input }}">
      </div>
    </div>

    <div class="flex gap-3 rounded-xl bg-gkk/5 p-4 text-sm text-gray-600 ring-1 ring-gkk/10">
      <i class="fa fa-info-circle mt-0.5 text-gkk"></i>
      <span>Innan du kan börja använda ditt konto kommer det behöva godkännas av administratören. När du registrerar dig skickas automatiskt en epostnotis till administratören.</span>
    </div>

    <button type="submit" class="{{ $button }}">Skapa konto</button>
  </form>
</div>
