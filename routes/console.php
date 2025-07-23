<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('media:delete-temp-files')->daily();
Schedule::command('telescope:prune --hours=24')->daily();
Schedule::command('pulse:purge')->daily();
