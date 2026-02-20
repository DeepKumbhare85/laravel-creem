<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creem Test Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 min-h-screen font-sans antialiased">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">Creem Test Dashboard</h1>
                    <p class="text-xs text-gray-500">laravel-creem package</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if ($apiStatus['test_mode'])
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                        TEST MODE
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        LIVE MODE
                    </span>
                @endif

                <span class="text-xs text-gray-400 font-mono truncate max-w-[180px]"
                    title="{{ $apiStatus['base_url'] }}">
                    {{ $apiStatus['base_url'] }}
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8 space-y-8">

        {{-- ── Flash messages ──────────────────────────────────────────────── --}}
        @if (session('creem_error'))
            <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <strong class="font-semibold">API Error:</strong>
                    {{ session('creem_error') }}
                    @if (session('creem_response'))
                        <pre class="mt-2 text-xs bg-red-100 rounded p-2 overflow-auto">{{ json_encode(session('creem_response'), JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>
        @endif

        @if (session('success'))
            <div
                class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ── API Status Cards ─────────────────────────────────────────────── --}}
        <section>
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Connection Status</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                {{-- API Key --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
                    @if ($apiStatus['api_key_set'])
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">API Key</p>
                            <p class="text-sm font-semibold text-green-700">Set ✓</p>
                        </div>
                    @else
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">API Key</p>
                            <p class="text-sm font-semibold text-red-600">Missing</p>
                        </div>
                    @endif
                </div>

                {{-- Webhook Secret --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
                    @if ($apiStatus['webhook_secret_set'])
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Webhook Secret</p>
                            <p class="text-sm font-semibold text-green-700">Set ✓</p>
                        </div>
                    @else
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Webhook Secret</p>
                            <p class="text-sm font-semibold text-red-600">Missing</p>
                        </div>
                    @endif
                </div>

                {{-- Mode --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
                    <div
                        class="w-9 h-9 {{ $apiStatus['test_mode'] ? 'bg-amber-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 {{ $apiStatus['test_mode'] ? 'text-amber-600' : 'text-blue-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Mode</p>
                        <p
                            class="text-sm font-semibold {{ $apiStatus['test_mode'] ? 'text-amber-700' : 'text-blue-700' }}">
                            {{ $apiStatus['test_mode'] ? 'Test' : 'Live' }}
                        </p>
                    </div>
                </div>

                {{-- Products loaded --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
                    @if ($error)
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">API Call</p>
                            <p class="text-sm font-semibold text-red-600">Failed</p>
                        </div>
                    @else
                        <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Products</p>
                            <p class="text-sm font-semibold text-indigo-700">{{ count($products) }} loaded</p>
                        </div>
                    @endif
                </div>

            </div>
        </section>

        {{-- ── API Error Detail ─────────────────────────────────────────────── --}}
        @if ($error)
            <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-red-800 mb-1">Failed to connect to Creem API</h3>
                <p class="text-sm text-red-700 font-mono break-all">{{ $error }}</p>
                <p class="text-xs text-red-500 mt-2">Check that your <code
                        class="bg-red-100 px-1 rounded">CREEM_API_KEY</code> and <code
                        class="bg-red-100 px-1 rounded">CREEM_TEST_MODE</code> values are correct in your <code
                        class="bg-red-100 px-1 rounded">.env</code> file.</p>
            </div>
        @endif

        {{-- ── Products Grid ────────────────────────────────────────────────── --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Products</h2>
                <a href="{{ route('creem.api', ['endpoint' => 'products']) }}" target="_blank"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                    Raw JSON
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            @if (count($products) === 0 && !$error)
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-10 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-sm font-medium">No products found</p>
                    <p class="text-xs mt-1">Create a product in your <a href="https://creem.io/dashboard"
                            target="_blank" class="text-indigo-500 hover:underline">Creem dashboard</a> first.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($products as $product)
                        @php
                            $name = $product['name'] ?? ($product['title'] ?? 'Unnamed Product');
                            $productId = $product['id'] ?? '';
                            $desc = $product['description'] ?? null;
                            $status = $product['status'] ?? null;

                            // Real Creem API: price in cents, currency & billing_period at top level
                            $amount = $product['price'] ?? null;
                            $currency = strtoupper($product['currency'] ?? 'USD');
                            $billingType = $product['billing_type'] ?? null; // recurring | onetime
                            $billingPeriod = $product['billing_period'] ?? null; // every-month | every-year …

                            $formattedPrice =
                                $amount !== null ? number_format($amount / 100, 2) . ' ' . $currency : null;
                        @endphp

                        <div
                            class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-4 hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                            {{-- Product info --}}
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3 class="font-semibold text-gray-900 leading-snug">{{ $name }}</h3>
                                    @if ($status)
                                        <span
                                            class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium
                                            {{ $status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $status }}
                                        </span>
                                    @endif
                                </div>

                                @if ($desc)
                                    <p class="text-sm text-gray-500 leading-relaxed line-clamp-3">{{ $desc }}
                                    </p>
                                @endif

                                <div class="mt-3 space-y-1.5">
                                    @if ($formattedPrice)
                                        <div class="flex items-baseline gap-1">
                                            <span
                                                class="text-2xl font-bold text-gray-900">{{ $formattedPrice }}</span>
                                            @if ($billingPeriod)
                                                <span class="text-sm text-gray-400">/
                                                    {{ str_replace('every-', '', $billingPeriod) }}</span>
                                            @elseif($billingType === 'onetime')
                                                <span class="text-sm text-gray-400">one-time</span>
                                            @endif
                                        </div>
                                    @endif

                                    <p class="text-xs text-gray-400 font-mono">{{ $productId }}</p>
                                </div>
                            </div>

                            {{-- Checkout button --}}
                            @if ($productId)
                                <form action="{{ route('creem.checkout') }}" method="POST" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $productId }}">
                                    <input type="hidden" name="success_url" value="{{ $successUrl }}">
                                    <button type="submit"
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition-colors duration-150 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Create Checkout Session
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── Quick API Explorer ───────────────────────────────────────────── --}}
        <section>
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Quick API Explorer</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach (['products' => '/v1/products/search', 'transactions' => '/v1/transactions/search', 'customers' => '/v1/customers/list'] as $ep => $path)
                    <a href="{{ route('creem.api', ['endpoint' => $ep]) }}" target="_blank"
                        class="bg-white border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 rounded-xl p-4 text-center transition-all duration-150">
                        <p class="text-sm font-semibold text-gray-700 capitalize">{{ $ep }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">GET {{ $path }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ── Custom Checkout Form ─────────────────────────────────────────── --}}
        <section>
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Manual Checkout Test</h2>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <p class="text-sm text-gray-600 mb-5">Paste any product ID to create a checkout session directly.</p>
                <form action="{{ route('creem.checkout') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="text" name="product_id" value="{{ old('product_id') }}"
                        placeholder="prod_xxxxxxxxxxxxxxxx" required
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-400">
                    <input type="hidden" name="success_url" value="{{ $successUrl }}">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors duration-150 whitespace-nowrap">
                        Generate Checkout →
                    </button>
                </form>
                @error('product_id')
                    <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- ── Create Product ───────────────────────────────────────────────── --}}
        <section>
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Create Product</h2>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <p class="text-sm text-gray-600 mb-5">Create a one-time purchase or subscription product directly via
                    the Creem API.</p>

                <form action="{{ route('creem.create-product') }}" method="POST" class="space-y-5"
                    x-data="{ billingType: '{{ old('billing_type', 'onetime') }}' }">
                    @csrf

                    {{-- Billing type toggle --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Product
                            Type</label>
                        <div class="flex gap-2">
                            <label
                                class="flex-1 flex items-center gap-3 border rounded-lg px-4 py-3 cursor-pointer transition-all"
                                :class="billingType === 'onetime'
                                    ?
                                    'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' :
                                    'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="billing_type" value="onetime" x-model="billingType"
                                    class="accent-indigo-600">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">One-Time Purchase</p>
                                    <p class="text-xs text-gray-500">Customer pays once and owns the product</p>
                                </div>
                            </label>
                            <label
                                class="flex-1 flex items-center gap-3 border rounded-lg px-4 py-3 cursor-pointer transition-all"
                                :class="billingType === 'recurring'
                                    ?
                                    'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' :
                                    'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="billing_type" value="recurring" x-model="billingType"
                                    class="accent-indigo-600">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Subscription</p>
                                    <p class="text-xs text-gray-500">Recurring billing on a set schedule</p>
                                </div>
                            </label>
                        </div>
                        @error('billing_type')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name & Description --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Product Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Pro Plan, Lifetime Access"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-400"
                                required>
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                            <input type="text" name="description" value="{{ old('description') }}"
                                placeholder="Short product description"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-400">
                        </div>
                    </div>

                    {{-- Price, Currency, Billing Period --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Price <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                                <input type="number" name="price" value="{{ old('price') }}"
                                    placeholder="29.00" min="1" step="0.01"
                                    class="w-full border border-gray-300 rounded-lg pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-400"
                                    required>
                            </div>
                            @error('price')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Currency <span
                                    class="text-red-500">*</span></label>
                            <select name="currency"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                @foreach (['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF', 'SEK', 'NOK', 'DKK'] as $cur)
                                    <option value="{{ $cur }}"
                                        {{ old('currency', 'USD') === $cur ? 'selected' : '' }}>
                                        {{ $cur }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="billingType === 'recurring'" x-cloak>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Billing Period <span
                                    class="text-red-500">*</span></label>
                            <select name="billing_period"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                <option value="every-month"
                                    {{ old('billing_period', 'every-month') === 'every-month' ? 'selected' : '' }}>
                                    Monthly</option>
                                <option value="every-year"
                                    {{ old('billing_period') === 'every-year' ? 'selected' : '' }}>Yearly</option>
                                <option value="every-week"
                                    {{ old('billing_period') === 'every-week' ? 'selected' : '' }}>Weekly</option>
                                <option value="every-3-months"
                                    {{ old('billing_period') === 'every-3-months' ? 'selected' : '' }}>Quarterly
                                </option>
                            </select>
                            @error('billing_period')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tax Category --}}
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tax Category</label>
                            <select name="tax_category"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                <option value="">— None / Default —</option>
                                <option value="saas" {{ old('tax_category') === 'saas' ? 'selected' : '' }}>SaaS
                                </option>
                                <option value="digital-goods-service"
                                    {{ old('tax_category') === 'digital-goods-service' ? 'selected' : '' }}>Digital
                                    Goods / Service</option>
                                <option value="ebooks" {{ old('tax_category') === 'ebooks' ? 'selected' : '' }}>eBooks
                                </option>
                            </select>
                        </div>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors duration-150 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- ── Webhook HMAC Verifier ────────────────────────────────────────── --}}
        <section>
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Webhook Signature Verifier
            </h2>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <p class="text-sm text-gray-600 mb-4">Paste a webhook payload and signature to verify the HMAC.</p>
                <form id="sig-verifier" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Raw Payload (JSON)</label>
                        <textarea id="sig-payload" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                            placeholder='{"eventType":"checkout.completed"}'></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">creem-signature header
                            value</label>
                        <input type="text" id="sig-header"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="abc123...">
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="verifySig()"
                            class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                            Verify Signature
                        </button>
                        <span id="sig-result" class="text-sm font-semibold hidden"></span>
                    </div>
                </form>

                <script>
                    function verifySig() {
                        const payload = document.getElementById('sig-payload').value;
                        const sig = document.getElementById('sig-header').value;
                        const result = document.getElementById('sig-result');

                        if (!payload || !sig) {
                            result.textContent = 'Fill in both fields.';
                            result.className = 'text-sm font-semibold text-gray-500';
                            result.classList.remove('hidden');
                            return;
                        }

                        // Simple client-side: call /creem/api with a verify param
                        fetch('/creem/verify-signature', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    payload,
                                    signature: sig
                                }),
                            })
                            .then(r => r.json())
                            .then(data => {
                                result.classList.remove('hidden');
                                if (data.valid) {
                                    result.textContent = '✓ Signature is VALID';
                                    result.className = 'text-sm font-semibold text-green-600';
                                } else {
                                    result.textContent = '✗ Signature is INVALID';
                                    result.className = 'text-sm font-semibold text-red-600';
                                }
                            })
                            .catch(() => {
                                result.textContent = 'Request failed.';
                                result.className = 'text-sm font-semibold text-gray-500';
                                result.classList.remove('hidden');
                            });
                    }
                </script>
            </div>
        </section>

    </main>

    {{-- ── Footer ─────────────────────────────────────────────────────────── --}}
    <footer
        class="max-w-6xl mx-auto px-6 py-6 mt-4 border-t border-gray-200 flex items-center justify-between text-xs text-gray-400">
        <span>laravel-creem · clevision/laravel-creem</span>
        <a href="https://docs.creem.io" target="_blank" class="hover:text-indigo-500 transition-colors">Creem Docs
            →</a>
    </footer>

    {{-- Success toast if redirected back from checkout --}}
    @if (request('checkout') === 'success')
        <div id="checkout-toast"
            class="fixed bottom-5 right-5 bg-green-600 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Checkout completed successfully!
        </div>
        <script>
            setTimeout(() => document.getElementById('checkout-toast')?.remove(), 5000);
        </script>
    @endif

</body>

</html>
