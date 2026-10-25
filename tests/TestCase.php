<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, LazilyRefreshDatabase;

    private static ?string $firebaseTestKey = null;

    protected function setUp(): void
    {
        parent::setUp();

        // No test checks the frontend assets, so the tests don't need a build (and CI skips it)
        $this->withoutVite();

        // Firebase JWTs are signed with a throwaway key, so the tests don't depend on the real key in .env (CI has
        // none). Generated once per process, since RSA key generation is slow.
        config(['firebase.private_key' => self::$firebaseTestKey ??= self::makeRsaKey()]);
    }

    private static function makeRsaKey(): string
    {
        \openssl_pkey_export(\openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]), $key);

        return $key;
    }
}
