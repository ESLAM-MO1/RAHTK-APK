<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:prune-failed', ['--hours' => 168])->daily();
