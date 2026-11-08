<?php

namespace App\Http\Controllers;

use App\Firebase;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\MeetingMinutes;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeetingMinutesController extends Controller
{
    public function index()
    {
        return view('admin.minutes', [
            'minutes' => MeetingMinutes::orderByDesc('date')->orderByDesc('id')
                ->get()
                ->map(fn (MeetingMinutes $minutes) => [
                    ...$minutes->only(['id', 'meeting_type', 'year', 'number']),
                    'date' => $minutes->date->format('Y-m-d'),
                    'status' => $minutes->status(),
                ]),
        ]);
    }

    public function create(Request $request)
    {
        $copyOf = $request->filled('copy') ? MeetingMinutes::findOrFail($request->integer('copy')) : null;

        return view('admin.minutes-editor', [
            'minutes' => MeetingMinutes::draftFromLatest($copyOf),
            'copiedFrom' => $copyOf?->title(),
            'workflow' => null,
            'folders' => $this->folders(),
            'nextNumbers' => MeetingMinutes::nextNumbers(),
            'members' => $this->memberNames(),
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function edit(MeetingMinutes $minutes)
    {
        return view('admin.minutes-editor', [
            'minutes' => $minutes,
            'copiedFrom' => null,
            'workflow' => $minutes->workflow(),
            'folders' => $this->folders(),
            'nextNumbers' => MeetingMinutes::nextNumbers($minutes),
            'members' => $this->memberNames(),
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function store(Request $request)
    {
        return MeetingMinutes::create([
            ...$this->validated($request),
            'created_by' => auth()->id(),
        ]);
    }

    public function update(Request $request, MeetingMinutes $minutes)
    {
        abort_if($minutes->isLocked(), 409, 'Protokollet är justerat. Lås upp det för att göra ändringar.');

        $minutes->update($this->validated($request));

        return $minutes;
    }

    public function destroy(MeetingMinutes $minutes)
    {
        $minutes->document?->delete();
        $minutes->delete();
    }

    /** Sends (or sends again, after changes were requested) the minutes to the adjuster. */
    public function requestReview(MeetingMinutes $minutes)
    {
        abort_if($minutes->isLocked(), 409, 'Protokollet är redan justerat.');
        abort_if(blank($minutes->adjuster), 422, 'Välj en protokolljusterare först.');

        $minutes->update(['review_requested_at' => now(), 'review_comment' => null]);

        return $minutes->workflow();
    }

    /** Takes the minutes back to a draft that can be edited. Approval has to be given again afterwards. */
    public function reopen(MeetingMinutes $minutes)
    {
        abort_if($minutes->published_at !== null, 409, 'Avpublicera protokollet först.');

        $minutes->update([
            'review_requested_at' => null,
            'review_comment' => null,
            'approved_at' => null,
            'approved_by_name' => null,
        ]);

        return $minutes->workflow();
    }

    public function publish(Request $request, MeetingMinutes $minutes)
    {
        abort_unless($minutes->isLocked(), 409, 'Protokollet måste justeras innan det publiceras.');
        abort_if($minutes->published_at !== null, 409, 'Protokollet är redan publicerat.');

        $data = $request->validate([
            'document_folder_id' => 'required|integer|exists:document_folders,id',
            'name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($minutes, $data) {
            $document = Document::create([...$data, 'url' => $minutes->publishedUrl()]);
            $minutes->update(['published_at' => now(), 'document_id' => $document->id]);
        });

        return $minutes->fresh()->workflow();
    }

    public function unpublish(MeetingMinutes $minutes)
    {
        DB::transaction(function () use ($minutes) {
            $minutes->document?->delete();
            $minutes->update(['published_at' => null, 'document_id' => null]);
        });

        return $minutes->fresh()->workflow();
    }

    public function print(MeetingMinutes $minutes)
    {
        return view('admin.minutes-print', ['minutes' => $minutes]);
    }

    /** @return Collection<int, DocumentFolder> */
    private function folders(): Collection
    {
        return DocumentFolder::orderBy('order')->get(['id', 'name', 'only_administrators']);
    }

    /**
     * Active members, to pick attendees from. Attendees are stored by name, so people from outside the club can be
     * added as free text too.
     *
     * @return array<int, string>
     */
    private function memberNames(): array
    {
        return User::whereNull('inactivated_at')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['first_name', 'last_name'])
            ->map(fn (User $user) => \trim("{$user->first_name} {$user->last_name}"))
            ->unique()
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'meeting_type' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:2100',
            'number' => 'required|integer|min:1',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'chair' => 'nullable|string|max:255',
            'secretary' => 'nullable|string|max:255',
            'adjuster' => 'nullable|string|max:255',
            'attendees' => 'present|array',
            'attendees.*.name' => 'required|string|max:255',
            'attendees.*.present' => 'required|boolean',
            'items' => 'present|array',
            'items.*.title' => 'required|string|max:255',
            'items.*.body' => 'nullable|string',
            'other_business' => 'nullable|string',
            'next_meeting_at' => 'nullable|date',
            'next_meeting_location' => 'nullable|string|max:255',
            'next_meeting_note' => 'nullable|string|max:255',
        ]);

        $data['attendees'] = collect($data['attendees'])
            ->map(fn ($attendee) => ['name' => \trim($attendee['name']), 'present' => (bool) $attendee['present']])
            ->values()
            ->all();
        $data['items'] = collect($data['items'])
            ->map(fn ($item) => ['title' => \trim($item['title']), 'body' => $item['body'] ?? ''])
            ->values()
            ->all();

        return $data;
    }
}
