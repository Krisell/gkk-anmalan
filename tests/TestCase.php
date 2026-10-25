<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // No test checks the frontend assets, so the tests don't need a build (and CI skips it)
        $this->withoutVite();
    }
}
