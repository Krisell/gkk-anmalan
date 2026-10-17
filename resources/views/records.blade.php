@extends('layouts.app')

@section('content')
<x-page-hero image="https://goteborg-kraftsportklubb.web.app/img/bankpress-min.jpg" eyebrow="Tunga lyft" title="Klubbrekord">
  Bästa GKK-resultatet i varje viktklass och gren.
</x-page-hero>

<div class="pt-6 pb-16">
  <gkk-records :results='@json($results)'></gkk-records>
</div>
@endsection
