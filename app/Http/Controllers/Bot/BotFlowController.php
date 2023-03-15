<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Repositories\Bot\BotFlowRepository;

/**
 * @group Bot Flow
 * APIs for bot flow page
 */
class BotFlowController extends Controller
{
    /*
     * Get Bot Flow
     */
    public function getBotFlow(BotFlowRepository $botFlowRepository)
    {
        $response = $botFlowRepository->getBotFlow();
        return $response;
    }
}
