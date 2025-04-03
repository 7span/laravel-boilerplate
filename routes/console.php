<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune')->daily();
Schedule::command('pulse:purge')->daily();
Schedule::command('app:clear-old-logs')->daily();
