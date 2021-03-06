<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecordsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function records_page_can_be_viewed()
    {
        $this->get('/records')->assertViewHas('results');
    }
}
