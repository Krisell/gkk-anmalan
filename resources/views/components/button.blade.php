<button
    {{ $attributes->merge(['class' => 'relative inline-flex items-center px-4 py-2 border leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out bg-gkk hover:bg-gkk-light text-white border-transparent text-sm focus:border-indigo-600 active:bg-gkk']) }}
  >
    {!! $slot !!}
  </button>
  