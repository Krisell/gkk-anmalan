<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * @property int $year
 * @property int $number
 * @property Carbon $date
 * @property ?Carbon $next_meeting_at
 * @property array<int, array{name: string, present: bool}> $attendees
 * @property array<int, array{title: string, body: ?string}> $items
 */
class MeetingMinutes extends Model
{
    use SoftDeletes;

    protected $table = 'meeting_minutes';

    protected $guarded = [];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'next_meeting_at' => 'datetime:Y-m-d\TH:i',
        'review_requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'attendees' => 'array',
        'items' => 'array',
    ];

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<Document, $this> */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * draft → review (sent to the adjuster) → changes_requested or approved → published (in Dokument for members).
     */
    public function status(): string
    {
        return match (true) {
            $this->published_at !== null => 'published',
            $this->approved_at !== null => 'approved',
            $this->review_requested_at !== null && filled($this->review_comment) => 'changes_requested',
            $this->review_requested_at !== null => 'review',
            default => 'draft',
        };
    }

    /** Approved minutes are locked, so what the adjuster approved is what gets published. */
    public function isLocked(): bool
    {
        return $this->approved_at !== null;
    }

    /** The link for the adjuster, valid for two weeks. A new one is minted on every page load. */
    public function reviewUrl(): string
    {
        return URL::temporarySignedRoute('minutes.review', now()->addDays(14), ['minutes' => $this->id]);
    }

    public function publishedUrl(): string
    {
        return "/member-documents/minutes/{$this->id}";
    }

    /**
     * What the editor needs to show where the minutes are in the review and publishing flow.
     *
     * @return array<string, mixed>
     */
    public function workflow(): array
    {
        return [
            'status' => $this->status(),
            'review_url' => \in_array($this->status(), ['review', 'changes_requested']) ? $this->reviewUrl() : null,
            'review_comment' => $this->review_comment,
            'approved_at' => $this->approved_at?->format('Y-m-d'),
            'approved_by_name' => $this->approved_by_name,
            'published_at' => $this->published_at?->format('Y-m-d'),
            'published_url' => $this->published_at ? $this->publishedUrl() : null,
            'document_folder_id' => $this->document?->document_folder_id,
        ];
    }

    public static function nextNumberFor(int $year): int
    {
        return (int) static::where('year', $year)->max('number') + 1;
    }

    /**
     * The next number for each year that has minutes, so the editor can renumber when the date moves to another year.
     *
     * @return array<int, int>
     */
    public static function nextNumbers(?self $except = null): array
    {
        return static::query()
            ->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->selectRaw('year, max(number) as number')
            ->groupBy('year')
            ->pluck('number', 'year')
            ->map(fn ($number) => (int) $number + 1)
            ->all();
    }

    /**
     * A new, unsaved set of minutes that picks up where the latest meeting left off: its "next meeting" becomes
     * this meeting's date, time and place, and the same people are expected.
     *
     * With $copyOf, the content (type of meeting, people, roles and items) is copied from those minutes instead,
     * while the date, number and place still follow on from the latest meeting.
     */
    public static function draftFromLatest(?self $copyOf = null): self
    {
        $latest = static::orderByDesc('date')->orderByDesc('id')->first();
        $source = $copyOf ?? $latest;

        $date = $latest->next_meeting_at ?? now();
        $year = $date->year;

        return new self([
            'meeting_type' => $copyOf->meeting_type ?? 'Styrelsemöte',
            'year' => $year,
            'number' => static::nextNumberFor($year),
            'date' => $date->format('Y-m-d'),
            'start_time' => $latest?->next_meeting_at?->format('H:i'),
            'end_time' => null,
            'location' => $latest->next_meeting_location ?? $source?->location,
            'chair' => $source?->chair,
            'secretary' => $source?->secretary,
            // The adjuster usually rotates, so only a copy keeps it.
            'adjuster' => $copyOf?->adjuster,
            'attendees' => collect($source->attendees ?? [])
                ->map(fn ($attendee) => ['name' => $attendee['name'], 'present' => true])
                ->values()
                ->all(),
            'items' => $copyOf ? $copyOf->items : [['title' => '', 'body' => '']],
            'other_business' => $copyOf->other_business ?? '',
        ]);
    }

    public function title(): string
    {
        return "{$this->meeting_type} {$this->number}, verksamhetsåret {$this->year}";
    }

    public function timeSpan(): ?string
    {
        $format = fn (?string $time) => $time ? \str_replace(':', '.', $time) : null;

        return collect([$format($this->start_time), $format($this->end_time)])->filter()->implode('–') ?: null;
    }

    /** @return array<int, string> */
    public function presentNames(): array
    {
        return collect($this->attendees)->where('present', true)->pluck('name')->values()->all();
    }

    /** @return array<int, string> */
    public function absentNames(): array
    {
        return collect($this->attendees)->where('present', false)->pluck('name')->values()->all();
    }

    /**
     * Every item of the minutes, numbered: the standard opening items, the meeting's own items, "Övrigt" and
     * "Nästa möte". Bodies are HTML.
     *
     * @return array<int, array{number: int, title: string, body: string}>
     */
    public function numberedItems(): array
    {
        $e = fn (?string $text) => e($text ?: '…');

        $items = [
            ['title' => 'Mötets öppnande', 'body' => "<p>Mötesordförande {$e($this->chair)} öppnar mötet.</p>"],
            [
                'title' => 'Sekreterare och protokolljusterare',
                'body' => "<p>{$e($this->secretary)} väljs till sekreterare och {$e($this->adjuster)} till protokolljusterare.</p>",
            ],
            ['title' => 'Godkännande av föregående mötesprotokoll', 'body' => '<p>Föregående mötesprotokoll godkändes.</p>'],
            ['title' => 'Fastställande av dagordning', 'body' => '<p>Dagordningen fastställdes.</p>'],
            ...collect($this->items)->map(fn ($item) => ['title' => $item['title'], 'body' => $item['body'] ?? ''])->all(),
            ['title' => 'Övrigt', 'body' => \trim(\strip_tags($this->other_business ?? '', '<img>')) === '' ? '<p>Inga övriga frågor.</p>' : $this->other_business],
            ['title' => 'Nästa möte', 'body' => '<p>'.e($this->nextMeetingText()).'</p>'],
        ];

        return collect($items)->values()->map(fn ($item, $index) => ['number' => $index + 1, ...$item])->all();
    }

    public function nextMeetingText(): string
    {
        if (! $this->next_meeting_at) {
            return $this->next_meeting_note ?: 'Ej bestämt.';
        }

        $when = $this->next_meeting_at->locale('sv')->isoFormat('dddd D MMMM YYYY [kl.] HH.mm');

        return collect([
            $this->next_meeting_note,
            \ucfirst($when),
            $this->next_meeting_location,
        ])->filter()->implode(', ').'.';
    }
}
