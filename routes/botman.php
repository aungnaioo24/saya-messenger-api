<?php

use Illuminate\Support\Facades\Log;

$botman->hears('{payload}', function ($payload, $bot) {
    Log::info($payload);
});
