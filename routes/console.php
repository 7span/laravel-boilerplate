<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('media:delete-temp-files')->daily();
Schedule::command('telescope:prune --hours=24')->daily();

// To permanently delete soft deleted records after site.soft_delete_retention_days
// Schedule::command('system:hard-delete-data')->daily();
