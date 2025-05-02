<?php


use Illuminate\Support\Facades\Schedule;

Schedule::command('attendance:mark-absent')->everyFifteenMinutes();
