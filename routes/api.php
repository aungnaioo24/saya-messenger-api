<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Bot\BotFlowController;
use App\Http\Controllers\Botman\WebhookController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Authentication
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');


// Webhook and Messenger API Setups
Route::get('/webhook', [WebhookController::class, 'setWebhook']);
Route::post('/webhook', [WebhookController::class, 'handle']);

Route::get('/register-get-started', function (Request $request) {
    $token = env("FACEBOOK_TOKEN");
    return Http::post("https://graph.facebook.com/v2.6/me/messenger_profile?access_token=$token", [
        'get_started' => [
            'payload' => 'GET_STARTED'
        ]
    ]);
});


// Bot Template
Route::get('/bot-flow', [BotFlowController::class, 'getBotFlow']);
Route::get('/dashboard/engagements', [DashboardController::class, 'engageDatas']);
