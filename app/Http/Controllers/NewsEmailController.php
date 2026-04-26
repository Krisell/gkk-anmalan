<?php

namespace App\Http\Controllers;

use App\Mail\NewsMail;
use App\Models\EmailSend;
use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsEmailController extends Controller
{
    public function show(NewsItem $item)
    {
        return view('news-email', [
            'item' => $item,
        ]);
    }

    public function preview()
    {
        $item = new NewsItem([
            'body' => request('body'),
            'title' => request('title'),
        ]);

        return new NewsMail($item);
    }

    public function test()
    {
        $item = new NewsItem([
            'body' => request('body'),
            'title' => request('title'),
        ]);

        Mail::to(auth()->user())->send(new NewsMail($item));
    }

    public function recipients(NewsItem $item)
    {
        $sentUserIds = EmailSend::where('subject_type', NewsItem::class)
            ->where('subject_id', $item->id)
            ->pluck('sent_at', 'user_id');

        $users = User::whereNull('inactivated_at')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        return response()->json([
            'recipients' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => \trim("{$user->first_name} {$user->last_name}"),
                'email' => $user->email,
                'sent_at' => $sentUserIds->get($user->id)?->toIso8601String(),
            ])->values(),
            'sent_count' => $sentUserIds->count(),
        ]);
    }

    public function send(Request $request, NewsItem $item)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $payload = new NewsItem([
            'id' => $item->id,
            'title' => $data['title'],
            'body' => $data['body'],
        ]);
        $payload->exists = true;

        $query = User::whereNull('inactivated_at');

        if (! empty($data['user_ids'])) {
            $query->whereIn('id', $data['user_ids']);
        }
        $users = $query->get();

        $sentCount = 0;

        foreach ($users as $user) {
            if (empty($user->email)) {
                continue;
            }

            Mail::to($user->email)->send(new NewsMail($payload));

            EmailSend::create([
                'subject_type' => NewsItem::class,
                'subject_id' => $item->id,
                'user_id' => $user->id,
                'email' => $user->email,
                'mailable' => NewsMail::class,
                'sent_at' => now(),
            ]);

            $sentCount++;
        }

        return response()->json([
            'count' => $sentCount,
            'message' => "{$sentCount} mail skickade.",
        ]);
    }
}
