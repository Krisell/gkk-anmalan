@extends('minutes.layout')

@section('toolbar')
<div class="toolbar">
    <a href="/admin/minutes/{{ $minutes->id }}">← Tillbaka till redigering</a>
    <button onclick="window.print()">Skriv ut / spara som PDF</button>
</div>
@endsection
