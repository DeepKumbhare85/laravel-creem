<?php

namespace Clevision\Creem\Http\Middleware;

use Clevision\Creem\Exceptions\CreemException;
use Clevision\Creem\Facades\Creem;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCreemSignature
{
    /**
     * Verify the HMAC-SHA256 webhook signature sent by Creem in the
     * `creem-signature` request header before passing the request along.
     *
     * Returns HTTP 401 if the signature is missing or invalid.
     *
     * @throws \Clevision\Creem\Exceptions\CreemException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('creem-signature');

        if (! $signature) {
            abort(401, 'Missing creem-signature header.');
        }

        $rawPayload = $request->getContent();

        if (! Creem::verifyWebhookSignature($rawPayload, $signature)) {
            abort(401, 'Invalid creem-signature — webhook verification failed.');
        }

        return $next($request);
    }
}
