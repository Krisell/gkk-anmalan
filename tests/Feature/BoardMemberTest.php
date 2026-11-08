<?php

use App\Models\BoardMember;

describe('the import command', function () {
    test('fills in the board in the given order', function () {
        $json = \json_encode([
            ['name' => 'Anna Andersson', 'role' => 'Ordförande', 'photo_url' => 'https://example.com/anna.jpg'],
            ['name' => 'Bo Berg', 'role' => 'Ledamot', 'areas' => ['Klubbkläder'], 'email' => 'bo@example.com'],
        ]);

        $this->artisan('gkk:import-board-members', ['source' => $json])->assertSuccessful();

        expect(BoardMember::ordered()->pluck('name')->all())->toBe(['Anna Andersson', 'Bo Berg'])
            ->and(BoardMember::firstWhere('name', 'Bo Berg'))
            ->areas->toBe(['Klubbkläder'])
            ->email->toBe('bo@example.com');
    });

    test('reads a file', function () {
        $path = \tempnam(\sys_get_temp_dir(), 'board');
        \file_put_contents($path, \json_encode([['name' => 'Anna Andersson', 'role' => 'Ordförande']]));

        $this->artisan('gkk:import-board-members', ['source' => $path])->assertSuccessful();
        \unlink($path);

        expect(BoardMember::sole()->name)->toBe('Anna Andersson');
    });

    test('keeps an existing board unless told to replace it', function () {
        BoardMember::create(['name' => 'Gammal', 'role' => 'Ledamot']);
        $json = \json_encode([['name' => 'Ny', 'role' => 'Ledamot']]);

        $this->artisan('gkk:import-board-members', ['source' => $json])->assertFailed();
        expect(BoardMember::sole()->name)->toBe('Gammal');

        $this->artisan('gkk:import-board-members', ['source' => $json, '--replace' => true])->assertSuccessful();
        expect(BoardMember::sole()->name)->toBe('Ny');
    });

    test('rejects invalid input without changing anything', function () {
        $this->artisan('gkk:import-board-members', ['source' => '[{"name": "Anna"}]'])->assertFailed();
        $this->artisan('gkk:import-board-members', ['source' => 'missing.json'])->assertFailed();

        expect(BoardMember::count())->toBe(0);
    });
});

describe('starting from an empty board', function () {
    test('an admin can add a board member', function () {
        loginAdmin();

        $this->postJson('/admin/board', [
            'name' => 'Anna Andersson',
            'role' => 'Ledamot',
            'photo_url' => 'https://example.com/anna.jpg',
            'areas' => ['Ungdomsträning', ' ', ' Klubbkläder '],
            'email' => 'ungdom@gkk-styrkelyft.se',
        ])->assertCreated();

        $member = BoardMember::sole();
        expect($member->name)->toBe('Anna Andersson')
            ->and($member->areas)->toBe(['Ungdomsträning', 'Klubbkläder'])
            ->and($member->email)->toBe('ungdom@gkk-styrkelyft.se');
    });

    test('new board members are added last', function () {
        loginAdmin();
        BoardMember::create(['name' => 'Först', 'role' => 'Ordförande', 'sort_order' => 4]);

        $this->postJson('/admin/board', ['name' => 'Sist', 'role' => 'Ledamot'])->assertCreated();

        expect(BoardMember::ordered()->pluck('name')->all())->toBe(['Först', 'Sist']);
    });

    test('an admin can update and remove a board member', function () {
        loginAdmin();
        $member = BoardMember::create(['name' => 'Anna', 'role' => 'Ledamot']);

        $this->putJson("/admin/board/{$member->id}", [
            'name' => 'Anna Andersson',
            'role' => 'Kassör',
            'areas' => ['Medlemsavgifter'],
            'email' => null,
        ])->assertOk();

        expect($member->fresh())
            ->role->toBe('Kassör')
            ->areas->toBe(['Medlemsavgifter']);

        $this->deleteJson("/admin/board/{$member->id}")->assertOk();
        expect(BoardMember::count())->toBe(0);
    });

    test('an admin can reorder the board', function () {
        loginAdmin();
        $a = BoardMember::create(['name' => 'A', 'role' => 'Ledamot', 'sort_order' => 0]);
        $b = BoardMember::create(['name' => 'B', 'role' => 'Ledamot', 'sort_order' => 1]);

        $this->postJson('/admin/board/order', ['ids' => [$b->id, $a->id]])->assertOk();

        expect(BoardMember::ordered()->pluck('name')->all())->toBe(['B', 'A']);
    });

    test('name and role are required and email must be valid', function () {
        loginAdmin();

        $this->postJson('/admin/board', ['name' => '', 'role' => '', 'email' => 'not-an-email'])
            ->assertJsonValidationErrors(['name', 'role', 'email']);
    });

    test('a non admin cant change the board', function () {
        login();
        $member = BoardMember::create(['name' => 'Anna', 'role' => 'Ledamot']);

        $this->post('/admin/board', ['name' => 'X', 'role' => 'Ledamot'])->assertStatus(401);
        $this->delete("/admin/board/{$member->id}")->assertStatus(401);
        $this->get('/admin/board')->assertStatus(401);

        expect(BoardMember::count())->toBe(1);
    });

    test('the about page shows the board with areas and contacts', function () {
        BoardMember::create([
            'name' => 'Anna Andersson',
            'role' => 'Ledamot',
            'areas' => ['Ungdomsträning'],
            'email' => 'ungdom@gkk-styrkelyft.se',
        ]);
        BoardMember::create(['name' => 'Bo Berg', 'role' => 'Suppleant']);

        $this->get('/gkk')
            ->assertOk()
            ->assertSee('Anna Andersson')
            ->assertSee('AA')
            ->assertSee('Bo Berg')
            ->assertSee('Vem kontaktar jag om')
            ->assertSee('Ungdomsträning')
            ->assertSee('mailto:ungdom@gkk-styrkelyft.se', false);
    });

    test('the about page hides the contact box when no areas are set', function () {
        BoardMember::create(['name' => 'Anna Andersson', 'role' => 'Ledamot']);

        $this->get('/gkk')->assertOk()->assertSee('Anna Andersson')->assertDontSee('Vem kontaktar jag om');
    });
});
