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
        return \opcache_get_status(false);
    }
}
