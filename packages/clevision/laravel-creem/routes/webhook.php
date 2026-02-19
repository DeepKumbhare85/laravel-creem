<?php

use Clevision\Creem\Http\Controllers\WebhookController;
use Clevision\Creem\Http\Middleware\VerifyCreemSignature;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Creem Webhook Routes
|--------------------------------------------------------------------------
|
| This file registers the single webhook endpoint that Creem posts events
| to. The VerifyCreemSignature middleware ensures only authentic payloads
| from Creem are processed.
|
| Register the following URL in your Creem dashboard:
|   Developers › Webhooks › {your-app-url}/creem/webhook
|
*/

Route::post(
    config('creem.webhook_route', 'creem/webhook'),
    WebhookController::class,
)->middleware([
    VerifyCreemSignature::class,
    ...config('creem.webhook_middleware', []),
])->name('creem.webhook');
