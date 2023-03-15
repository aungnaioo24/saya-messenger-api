<?php

namespace App\Repositories\Bot;

use App\Models\Bot;

class BotFlowRepository
{
    public function getBotFlow()
    {
        $bot = Bot::first();
        $botFlow = json_decode($bot->bot_flow, true);

        return response()->json([
            'bot_flow' => $botFlow
        ]);
    }
}
