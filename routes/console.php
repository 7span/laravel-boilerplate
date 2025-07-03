<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune --hours=24')->daily();
Schedule::command('pulse:purge')->daily();
Schedule::command('log:cleanup')->daily();
