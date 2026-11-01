<?php

return [
    'new-member-receivers' => env('NEW_MEMBER_RECEIVERS'),

    // Blocks every database query and shows a maintenance page, while pages that don't need the database keep
    // working. For work on the database, such as moving it (see docs/hosting.md).
    'database-maintenance' => (bool) env('DATABASE_MAINTENANCE', false),
];
