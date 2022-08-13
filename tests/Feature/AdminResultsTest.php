<?php

namespace Tests\Feature;

use App\Result;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResultsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_administrator_can_add_a_result()
    {
        auth()->login(User::factory()->admin()->create());

        $data = [
            'gender' => 'M',
            'competition_date' => '2021-03-02',
            'weight_class' => '74',
            'event' => 'Knäböj',
            'user_id' => 1,
            'result' => '150',
        ];

        $this->post('/admin/results', $data)->assertCreated();

        $this->assertDatabaseHas('results', $data);
    }

    /** @test */
    public function an_administrator_can_delete_a_result()
    {
        auth()->login(User::factory()->admin()->create());

        $result = Result::factory()->create();

        $this->delete("/admin/results/{$result->id}")->assertOk();

        $this->assertEmpty(Result::all());
    }
}
