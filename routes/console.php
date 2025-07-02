<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune')->daily();
Schedule::command('pulse:purge')->daily();
