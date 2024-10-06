<?php

use Illuminate\Support\Facades\Facade;

return [

    'mix_url' => env('MIX_URL'),

    'aliases' => Facade::defaultAliases()->merge([
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
