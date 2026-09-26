<div class="p-6 sm:p-8 lg:flex lg:flex-1 lg:flex-col lg:justify-center">
  <h1 class="text-2xl font-bold tracking-tight text-gray-900">Välkommen tillbaka</h1>
  <p class="mt-1 text-sm text-gray-500">Logga in för att komma till Insidan.</p>

  {{-- v-pre: the page is compiled by Vue, so user-provided values must not be treated as template code --}}
  <form v-pre action="/login" method="POST" class="mt-6 space-y-5">
    @csrf

    @if ($mode === 'login')
      @error('email')
        <div class="flex gap-3 rounded-xl bg-red-50 p-4 text-sm text-red-700 ring-1 ring-red-200">
          <i class="fa fa-exclamation-circle mt-0.5"></i>
          <span>Kunde inte logga in med dessa användaruppgifter.</span>
        </div>
      @enderror
    @endif

    <div>
      <label for="email" class="{{ $label }}">Epost</label>
      <input id="email" name="email" value="{{ old('email') }}" type="email" autocomplete="email" required @if ($mode === 'login') autofocus @endif class="{{ $input }}">
    </div>

    {{-- The reset link comes after the input in the DOM so Tab goes email → password; flex order keeps it beside the label --}}
    <div class="flex flex-wrap items-center justify-between">
      <label for="password" class="order-1 {{ $label }}">Lösenord</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required class="order-3 {{ $input }}">
      <a href="/password/reset" class="order-2 mb-1.5 text-sm font-medium text-gkk hover:underline">Glömt lösenordet?</a>
    </div>

    <button type="submit" class="{{ $button }}">Logga in</button>
  </form>

  <div class="my-6 flex items-center gap-4 text-xs font-medium uppercase tracking-wider text-gray-400">
    <span class="h-px flex-1 bg-gray-200"></span>
    eller
    <span class="h-px flex-1 bg-gray-200"></span>
  </div>

  <gkk-login to="{{ session('url.intended', '') }}"></gkk-login>
</div>

<div class="border-t border-gray-100 bg-gray-50/60 px-6 py-5 text-center text-sm text-gray-500 sm:px-8">
  Har du inget konto än? <a href="/register" data-auth-tab="register" class="font-semibold text-gkk hover:underline">Skapa konto</a>
</div>
