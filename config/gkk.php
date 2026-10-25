<?php

return [
    'new-member-receivers' => env('NEW_MEMBER_RECEIVERS'),

    // Blocks every database query and shows a maintenance page, while pages that don't need the database keep
    // working. Used while moving the database between servers (see docs/laravel-cloud-migration.md).
    'database-maintenance' => (bool) env('DATABASE_MAINTENANCE', false),

    // Sent as the X-Served-By header, so bin/check-cutover.sh can tell which server answered.
    'served-by' => env('SERVED_BY'),
];
