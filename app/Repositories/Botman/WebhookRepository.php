<?php

namespace App\Repositories\Botman;

use App\Models\Bot;
use App\Models\BotUser;
use App\Models\Chat;
use App\Models\Engagement;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookRepository
{
    public function setWebhook($data)
    {
        Log::info($data);

        $config = [
            'facebook' => [
                'token' => env('FACEBOOK_TOKEN'),
                'app_secret' => env('FACEBOOK_APP_SECRET'),
                'verification'=> env('FACEBOOK_VERIFICATION'),
            ]
        ];

        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
        BotManFactory::create($config);

        $mode = array_key_exists('hub_mode', $data) ? $data['hub_mode'] : '';
        $challenge = array_key_exists('hub_challenge', $data) ? $data['hub_challenge'] : '';
        $verifyToken = array_key_exists('hub_verify_token', $data) ? $data['hub_verify_token'] : '';

        if ($mode == 'subscribe' && $verifyToken == env('FACEBOOK_VERIFICATION')) {
            Log::info('WEBHOOK_VERIFIED');

            return response($challenge, 200);
        } else {
            Log::info('WEBHOOK_NOT_VERIFIED');
            return response("", 403);
        }
    }

    public function handle($data)
    {
        Log::info($data);
        $bot = Bot::first();
        $messengerUserId = $data['entry'][0]['messaging'][0]['sender']['id'];
        $botUser = BotUser::where('messenger_user_id', $messengerUserId)->first();

        if (array_key_exists('postback',  $data['entry'][0]['messaging'][0])) {
            if (array_key_exists('payload',  $data['entry'][0]['messaging'][0]['postback'])) {
                if ($data['entry'][0]['messaging'][0]['postback']['payload'] == 'GET_STARTED') {
                    Http::post('https://graph.facebook.com/v16.0/me/messages?access_token=' . env('FACEBOOK_TOKEN'), [
                        "recipient" => [
                            'id' => $messengerUserId
                        ],
                        "message" => [
                            'text' => 'How can I help you?'
                        ]
                    ]);
                } else if ($data['entry'][0]['messaging'][0]['postback']['payload'] == 'uuid123') {
                    Http::post('https://graph.facebook.com/v16.0/me/messages?access_token=' . env('FACEBOOK_TOKEN'), [
                        "recipient" => [
                            'id' => $messengerUserId
                        ],
                        "message" => [
                            'text' => 'Yo! dude!!! You nailed ittt!!'
                        ]
                    ]);
                }
            }
        }

        if (!array_key_exists('message',  $data['entry'][0]['messaging'][0])){
            return response("", 200);
        }

        if (!$botUser) {
            $botUser = $this->storeBotUser($messengerUserId, $bot);
        }

        $message = $data['entry'][0]['messaging'][0]['message'];
        $this->storeChat($botUser, $bot, $data);
        $this->logEngagement($botUser, $bot);

        if (!array_key_exists('text',  $message)){
            return response("", 200);
        }

        $textMessage = $message['text'];

        $botFlow = json_decode($bot->bot_flow, true);

        if (array_key_exists($textMessage, $botFlow)) {

            $botFlow[$textMessage]['recipient'] = [
                'id' => $messengerUserId
            ];

            Log::info($botFlow[$textMessage]);

            Http::post('https://graph.facebook.com/v16.0/me/messages?access_token=' . env('FACEBOOK_TOKEN'), $botFlow[$textMessage]);
        }

        return response("", 200);
    }

    private function storeBotUser($messengerUserId, $bot)
    {
        $response = Http::get('https://graph.facebook.com/' . $messengerUserId . '?fields=first_name,last_name,profile_pic&access_token=' . env('FACEBOOK_TOKEN'));
        $botUserName = $response['first_name'] . ' ' . $response['last_name'];
        $botUserProfilePhoto = $response['profile_pic'];

        $botUser = new BotUser();
        $botUser->fill([
            'messenger_user_id' => $messengerUserId,
            'name' => $botUserName,
            'profile_photo' => $botUserProfilePhoto
        ]);
        $botUser->bot()->associate($bot);
        return $botUser->save();
    }

    private function storeChat($botUser, $bot, $data)
    {
        $message = $data['entry'][0]['messaging'][0]['message'];

        if (array_key_exists('text', $message)) {
            $type = 'text';
            $text = $message['text'];

            $chat = new Chat();
            $chat->fill([
                'sender' => 'bot_user',
                'message' => $text,
                'type' => $type
            ]);

            $chat->bot()->associate($bot);
            $chat->botUser()->associate($botUser);
            $chat->save();
        }
    }

    private function logEngagement($botUser, $bot)
    {
        $engagement = new Engagement();
        $engagement->bot()->associate($bot);
        $engagement->botUser()->associate($botUser);
        $engagement->save();
    }
}
