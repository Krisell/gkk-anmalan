@extends('layouts.full')

@section('css')
<style>
  /* The full layout is for screens that fill the window; this page scrolls. */
  body { overflow-y: auto; }

  /* The sign that is printed (a Platslapp) is kept off screen, so its text can be fitted before printing. */
  .platslapp-print { position: fixed; left: -10000px; top: 0; }

  @page { size: A4 landscape; margin: 0; }

  @media print {
    body { background: #fff; }
    body > *:not(.platslapp-print) { display: none !important; }
    .platslapp-print { position: static; width: 297mm; height: 210mm; overflow: hidden; }
  }
</style>
@endsection

@section('content')
  <gkk-secretariat></gkk-secretariat>
@endsection
