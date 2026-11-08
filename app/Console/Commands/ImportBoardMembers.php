<?php

namespace App\Console\Commands;

use App\Models\BoardMember;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Fills in the board shown on the Om GKK page. The names live outside the repository, since it is public: pass a JSON
 * array of {name, role, photo_url?, areas?, email?} in the order the board is shown.
 */
class ImportBoardMembers extends Command
{
    protected $signature = 'gkk:import-board-members
        {source : A path to a JSON file, an https URL to one, or the JSON itself}
        {--replace : Remove the current board first}';

    protected $description = 'Fill in the board on the Om GKK page from a JSON list';

    public function handle(): int
    {
        $members = \json_decode($this->read($this->argument('source')), true);

        if (! \is_array($members) || ! \array_is_list($members) || $members === []) {
            $this->error('The source must be a non-empty JSON array.');

            return self::FAILURE;
        }

        try {
            $members = Validator::make(['members' => $members], [
                'members.*.name' => ['required', 'string', 'max:255'],
                'members.*.role' => ['required', 'string', 'max:255'],
                'members.*.photo_url' => ['nullable', 'url'],
                'members.*.areas' => ['nullable', 'array'],
                'members.*.areas.*' => ['string', 'max:255'],
                'members.*.email' => ['nullable', 'email', 'max:255'],
            ])->validate()['members'];
        } catch (ValidationException $e) {
            foreach ($e->errors() as $messages) {
                $this->error($messages[0]);
            }

            return self::FAILURE;
        }

        if (BoardMember::exists() && ! $this->option('replace')) {
            $this->error('The board already has members. Run with --replace to remove them first.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($members) {
            BoardMember::query()->delete();

            foreach ($members as $index => $member) {
                BoardMember::create([
                    'name' => $member['name'],
                    'role' => $member['role'],
                    'photo_url' => $member['photo_url'] ?? null,
                    'areas' => $member['areas'] ?? [],
                    'email' => $member['email'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        });

        $this->info('Imported '.\count($members).' board members.');

        return self::SUCCESS;
    }

    private function read(string $source): string
    {
        $trimmed = \ltrim($source);

        if (\str_starts_with($trimmed, '[')) {
            return $source;
        }

        if (\str_starts_with($trimmed, 'https://')) {
            return Http::get($trimmed)->throw()->body();
        }

        return \is_file($source) ? (string) \file_get_contents($source) : '';
    }
}
