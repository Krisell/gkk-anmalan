@extends('minutes.layout')

@section('toolbar')
<div class="panel">
    @switch($minutes->status())
        @case('review')
        @case('changes_requested')
            <h2>Justering av protokoll</h2>
            <p>Hej {{ $minutes->adjuster }}! Läs igenom protokollet nedan och justera det om det stämmer.</p>
            @if ($minutes->status() === 'changes_requested')
                <p class="warn">Du har bett om ändringar: ”{{ $minutes->review_comment }}”. Protokollet kan ha uppdaterats sedan dess.</p>
            @endif
            <div class="actions">
                <form method="post" action="{{ $approveUrl }}">
                    @csrf
                    <button class="primary">Justera protokollet</button>
                </form>
                <details>
                    <summary>Något behöver ändras</summary>
                    <form method="post" action="{{ $changesUrl }}">
                        @csrf
                        <textarea name="comment" required placeholder="Vad behöver ändras?"></textarea>
                        <button class="secondary">Skicka tillbaka till sekreteraren</button>
                    </form>
                </details>
            </div>
            @break
        @case('approved')
        @case('published')
            <h2 class="ok">✓ Protokollet är justerat</h2>
            <p>Justerat {{ $minutes->approved_at->format('Y-m-d') }} av {{ $minutes->approved_by_name }}. Tack!</p>
            @break
        @default
            <h2>Protokollet väntar inte på justering</h2>
            <p>Sekreteraren har tagit tillbaka protokollet för ändringar. Du får en ny länk när det är dags att justera.</p>
    @endswitch
</div>
@endsection
