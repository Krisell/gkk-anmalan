@extends('minutes.layout')

@section('toolbar')
<div class="toolbar">
    <a href="/member-documents">← Dokument</a>
    <button onclick="window.print()">Skriv ut / spara som PDF</button>
</div>
@endsection
