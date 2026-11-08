<?php

namespace App\Http\Controllers;

use App\Models\MeetingMinutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * The adjuster (protokolljusteraren) reviews the minutes through a signed link, shared by an admin e.g. on Discord.
 * Like signing links, the link itself is the permission: no login is needed.
 */
class MeetingMinutesReviewController extends Controller
{
    public function show(MeetingMinutes $minutes)
    {
        $postUrl = fn (string $route) => URL::temporarySignedRoute($route, now()->addHours(12), ['minutes' => $minutes->id]);

        return view('minutes-review', [
            'minutes' => $minutes,
            'approveUrl' => $postUrl('minutes.review.approve'),
            'changesUrl' => $postUrl('minutes.review.changes'),
        ]);
    }

    public function approve(MeetingMinutes $minutes)
    {
        $this->abortUnlessInReview($minutes);

        $minutes->update([
            'approved_at' => now(),
            'approved_by_name' => $minutes->adjuster,
            'review_comment' => null,
        ]);

        return redirect()->to(URL::temporarySignedRoute('minutes.review', now()->addDays(14), ['minutes' => $minutes->id]));
    }

    public function requestChanges(Request $request, MeetingMinutes $minutes)
    {
        $this->abortUnlessInReview($minutes);

        $data = $request->validate(['comment' => 'required|string|max:5000']);

        $minutes->update(['review_comment' => $data['comment']]);

        return redirect()->to(URL::temporarySignedRoute('minutes.review', now()->addDays(14), ['minutes' => $minutes->id]));
    }

    /** Published minutes, for members, linked from Dokument. */
    public function published(MeetingMinutes $minutes)
    {
        abort_if($minutes->published_at === null, 404);

        $adminOnly = (bool) $minutes->document?->document_folder?->only_administrators;
        abort_if($adminOnly && ! \in_array(auth()->user()->role, ['admin', 'superadmin']), 404);

        return view('minutes-published', ['minutes' => $minutes]);
    }

    private function abortUnlessInReview(MeetingMinutes $minutes): void
    {
        abort_unless(\in_array($minutes->status(), ['review', 'changes_requested']), 409, 'Protokollet väntar inte på justering.');
    }
}
