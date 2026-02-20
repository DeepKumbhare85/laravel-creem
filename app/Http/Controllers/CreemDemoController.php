<?php

namespace App\Http\Controllers;

use Clevision\Creem\Facades\Creem;
use Illuminate\Http\Request;

class CreemDemoController extends Controller
{
    /**
     * Show the Creem test dashboard — lists products and API status.
     */
    public function index()
    {
        $products  = [];
        $error     = null;
        $apiStatus = [
            'api_key_set'        => ! empty(config('creem.api_key')),
            'webhook_secret_set' => ! empty(config('creem.webhook_secret')),
            'test_mode'          => (bool) config('creem.test_mode'),
            'base_url'           => config('creem.test_mode')
                ? config('creem.test_api_url', 'https://test-api.creem.io')
                : config('creem.api_url', 'https://api.creem.io'),
        ];

        $successUrl = env('CREEM_SUCCESS_URL', 'https://example.com');

        try {
            $response = Creem::listProducts();

            // Creem returns { data: [...], ... }  or  { items: [...] }
            $products = $response['data'] ?? $response['items'] ?? $response;

            if (! is_array($products)) {
                $products = [];
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('creem-demo', compact('products', 'error', 'apiStatus', 'successUrl'));
    }

    /**
     * Show the checkout success page.
     */
    public function checkoutSuccess()
    {
        return redirect()->route('creem.demo')->with('success', 'Checkout completed successfully! 🎉');
    }

    /**
     * Create a Creem checkout session and redirect to the checkout URL.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'product_id'  => ['required', 'string'],
            'success_url' => ['nullable', 'url'],
        ]);

        try {
            $checkout = Creem::createCheckout([
                'product_id'  => $validated['product_id'],
                'success_url' => $validated['success_url'] ?? env('CREEM_SUCCESS_URL', 'https://example.com'),
                'request_id'  => (string) now()->timestamp,
            ]);

            $checkoutUrl = $checkout['checkout_url']
                ?? $checkout['url']
                ?? $checkout['checkoutUrl']
                ?? null;

            if (! $checkoutUrl) {
                return back()
                    ->with('creem_error', 'Checkout created but no checkout_url returned: ' . json_encode($checkout))
                    ->with('creem_response', $checkout);
            }

            return redirect()->away($checkoutUrl);
        } catch (\Throwable $e) {
            return back()
                ->with('creem_error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create a new product via the Creem API (one-time or subscription).
     */
    public function createProduct(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'price'          => ['required', 'numeric', 'min:1'],
            'currency'       => ['required', 'string', 'size:3'],
            'billing_type'   => ['required', 'in:onetime,recurring'],
            'billing_period' => ['required_if:billing_type,recurring', 'nullable', 'string'],
            'tax_category'   => ['nullable', 'in:saas,digital-goods-service,ebooks'],
        ]);

        // Convert price from dollars to cents
        $params = [
            'name'         => $validated['name'],
            'price'        => (int) round($validated['price'] * 100),
            'currency'     => strtoupper($validated['currency']),
            'billing_type' => $validated['billing_type'],
        ];

        if (! empty($validated['description'])) {
            $params['description'] = $validated['description'];
        }

        if ($validated['billing_type'] === 'recurring' && ! empty($validated['billing_period'])) {
            $params['billing_period'] = $validated['billing_period'];
        }

        if (! empty($validated['tax_category'])) {
            $params['tax_category'] = $validated['tax_category'];
        }

        try {
            $product = Creem::createProduct($params);

            return back()->with('success', 'Product "' . ($product['name'] ?? $validated['name']) . '" created! ID: ' . ($product['id'] ?? 'unknown'));
        } catch (\Throwable $e) {
            return back()
                ->with('creem_error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Verify a webhook signature (AJAX call from the UI verifier widget).
     */
    public function verifySignature(Request $request)
    {
        $payload   = $request->input('payload', '');
        $signature = $request->input('signature', '');

        $valid = Creem::verifyWebhookSignature($payload, $signature);

        return response()->json(['valid' => $valid]);
    }

    /**
     * Show a raw JSON dump of any Creem API endpoint (for quick debugging).
     */
    public function api(Request $request)
    {
        $endpoint = $request->get('endpoint', 'products');
        $result   = [];
        $error    = null;

        try {
            $result = match ($endpoint) {
                'products'     => Creem::listProducts(),
                'transactions' => Creem::listOrders(),
                'customers'    => Creem::listCustomers(),
                default        => ['error' => "Unknown endpoint: {$endpoint}"],
            };
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return response()->json([
            'endpoint' => $endpoint,
            'result'   => $result,
            'error'    => $error,
        ]);
    }
}
