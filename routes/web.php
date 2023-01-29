<?php

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/botman', function (Request $request) {

    $data = $request->all();
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
});

Route::post('/botman', function (Request $request) {

    Log::info($request->all());
    return response("", 200);
});

Route::get('/registor', function (Request $request) {
    $token = env("FACEBOOK_TOKEN");
    return Http::post("https://graph.facebook.com/v2.6/me/messenger_profile?access_token=$token", [
        'get_started' => [
            'payload' => 'Start'
        ]
    ]);
});
