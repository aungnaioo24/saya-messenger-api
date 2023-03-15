<?php

namespace App\Http\Controllers\Botman;

use App\Http\Controllers\Controller;
use App\Repositories\Botman\WebhookRepository;
use Illuminate\Http\Request;

/**
 * @group Webhook
 * APIs for messenger webhook
 */
class WebhookController extends Controller
{
    /*
     * Setting Up Webhook
     */
    public function setWebhook(Request $request, WebhookRepository $webhookRepository)
    {
        $data = $request->all();
        $response = $webhookRepository->setWebhook($data);

        return $response;
    }

    /*
     * Hearing bot response
     */
    public function handle(Request $request, WebhookRepository $webhookRepository)
    {
        $data = $request->all();
        $response = $webhookRepository->handle($data);

        return $response;
    }
}
