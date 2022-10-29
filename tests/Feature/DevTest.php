<?php

test('the superadmin may load dev routes', function () {
    $this->json('get', '/admin/dev/phpinfo')->assertUnauthorized();
    $this->json('get', '/admin/dev/opcache')->assertUnauthorized();

    login();

    $this->json('get', '/admin/dev/phpinfo')->assertUnauthorized();
    $this->json('get', '/admin/dev/opcache')->assertUnauthorized();

    loginAdmin();

    $this->json('get', '/admin/dev/phpinfo')->assertUnauthorized();
    $this->json('get', '/admin/dev/opcache')->assertUnauthorized();

    loginSuperadmin();

    \ob_start();
    $this->json('get', '/admin/dev/phpinfo')->assertOk();
    $this->json('get', '/admin/dev/opcache')->assertOk();
    \ob_end_clean();
});
