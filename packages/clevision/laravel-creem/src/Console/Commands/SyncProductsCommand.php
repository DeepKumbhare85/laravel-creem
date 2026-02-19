<?php

namespace Clevision\Creem\Console\Commands;

use Clevision\Creem\Facades\Creem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'creem:sync-products
                            {--cache : Cache the product list after fetching}
                            {--clear-cache : Clear the cached product list}
                            {--ttl=3600 : Cache TTL in seconds (default: 1 hour)}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch and display all products from your Creem account';

    public function handle(): int
    {
        if ($this->option('clear-cache')) {
            Cache::forget('creem_products');
            $this->components->info('Creem product cache cleared.');

            return self::SUCCESS;
        }

        $this->components->info('Fetching products from Creem…');

        try {
            $response = Creem::listProducts();
            $products = $response['data'] ?? $response;

            if (empty($products)) {
                $this->components->warn('No products found in your Creem account.');

                return self::SUCCESS;
            }

            if ($this->option('cache')) {
                $ttl = (int) $this->option('ttl');
                Cache::put('creem_products', $products, $ttl);
                $this->components->info("Products cached for {$ttl} seconds (key: creem_products).");
            }

            $this->table(
                ['ID', 'Name', 'Status', 'Price', 'Currency', 'Type'],
                collect($products)->map(fn(array $p) => [
                    $p['id']       ?? '—',
                    $p['name']     ?? '—',
                    $p['status']   ?? '—',
                    isset($p['price']) ? number_format($p['price'] / 100, 2) : '—',
                    strtoupper($p['currency'] ?? '—'),
                    $p['type']     ?? '—',
                ])->toArray(),
            );

            $this->newLine();
            $this->components->info('Synced ' . count($products) . ' product(s) from Creem.');
        } catch (\Throwable $e) {
            $this->components->error('Failed to fetch products: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
