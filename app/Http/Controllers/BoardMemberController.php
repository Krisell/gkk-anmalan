<?php

namespace App\Http\Controllers;

use App\Firebase;
use App\Models\BoardMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardMemberController extends Controller
{
    public function index()
    {
        return view('admin.board', [
            'members' => BoardMember::ordered()->get(),
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function store(Request $request)
    {
        return BoardMember::create([
            ...$this->validated($request),
            'sort_order' => (BoardMember::max('sort_order') ?? -1) + 1,
        ]);
    }

    public function update(Request $request, BoardMember $member)
    {
        $member->update($this->validated($request));

        return $member;
    }

    public function destroy(BoardMember $member)
    {
        $member->delete();
    }

    /** Saves the order the members are shown in, given as a list of ids. */
    public function order(Request $request)
    {
        $ids = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:board_members,id',
        ])['ids'];

        DB::transaction(function () use ($ids) {
            foreach (\array_values($ids) as $index => $id) {
                BoardMember::whereKey($id)->update(['sort_order' => $index]);
            }
        });
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'photo_url' => 'nullable|url|max:2048',
            'areas' => 'nullable|array',
            'areas.*' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $data['areas'] = collect($data['areas'] ?? [])->map(fn ($area) => \trim((string) $area))->filter()->values()->all();

        return $data;
    }
}
