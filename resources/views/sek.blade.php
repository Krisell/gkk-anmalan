@extends('layouts.full')

@section('css')
<style>
  /* The full layout is for screens that fill the window; this page scrolls. */
  body { overflow-y: auto; }

  /* The sign that is printed (a Platslapp or the signpost) is kept off screen, so its text can be fitted before printing. */
  .sign-print { position: fixed; left: -10000px; top: 0; }

  @page { size: A4 landscape; margin: 0; }
  /* Sheets printed several at a time (the start lists, the competition cards), one per page, portrait or landscape. */
  @page portrait { size: A4 portrait; margin: 0; }
  @page landscape { size: A4 landscape; margin: 0; }
  .print-page.portrait { page: portrait; }
  .print-page.landscape { page: landscape; }

  @media print {
    body { background: #fff; }
    body > *:not(.sign-print) { display: none !important; }
    .sign-print { position: static; width: 297mm; height: 210mm; overflow: hidden; }
    .sign-print.sign-print-pages { width: auto; height: auto; overflow: visible; }
    .print-page { break-after: page; }
    .print-page:last-child { break-after: auto; }
  }
</style>
@endsection

@section('content')
  <gkk-secretariat></gkk-secretariat>
@endsection
