<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('sites:check-health')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer();
