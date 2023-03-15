<?php

namespace Database\Seeders;

use App\Models\Bot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $botflow = json_decode(file_get_contents(base_path('flow.json')));
        $botflow = json_encode($botflow);

        Bot::create([
            'user_id' => 1,
            'bot_flow' => $botflow
        ]);
    }
}
