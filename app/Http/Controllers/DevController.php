<?php

namespace App\Http\Controllers;

class DevController extends Controller
{
    public function phpinfo()
    {
        \phpinfo();
    }

    public function opcache()
    {
        if (! \function_exists('opcache_get_status')) {
            return 'Opcache is not enabled';
        }

        return \opcache_get_status(false);
    }
}
