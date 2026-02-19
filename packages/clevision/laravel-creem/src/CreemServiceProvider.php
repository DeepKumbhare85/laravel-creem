<?php

namespace Clevision\Creem;

use Clevision\Creem\Console\Commands\SyncProductsCommand;
use Clevision\Creem\Console\Commands\WebhookSecretCommand;
use Clevision\Creem\Http\Middleware\VerifyCreemSignature;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class CreemServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package config with the application's published config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/creem.php',
            'creem',
        );

        // Bind the HTTP client as a singleton
        $this->app->singleton(CreemClient::class, function ($app) {
            $config = $app['config']['creem'];

            return new CreemClient(
                apiKey: $config['api_key'] ?? '',
                testMode: (bool) ($config['test_mode'] ?? false),
                timeout: (int) ($config['timeout'] ?? 30),
                retries: (int) ($config['retries'] ?? 3),
                retryDelay: (int) ($config['retry_delay'] ?? 500),
            );
        });

        // Bind the main Creem service as a singleton, injecting the client
        $this->app->singleton(Creem::class, function ($app) {
            return new Creem($app->make(CreemClient::class));
        });

        // Allow resolution by the string alias 'creem' for the Facade
        $this->app->alias(Creem::class, 'creem');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerMiddleware();
        $this->registerPublishing();

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * Register the Creem webhook route.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/webhook.php');
    }

    /**
     * Register the webhook signature verification middleware alias.
     */
    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('creem.verify', VerifyCreemSignature::class);
    }

    /**
     * Register the publishable resources.
     */
    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // php artisan vendor:publish --tag=creem-config
        $this->publishes([
            __DIR__ . '/../config/creem.php' => config_path('creem.php'),
        ], 'creem-config');
    }

    /**
     * Register the package's Artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            WebhookSecretCommand::class,
            SyncProductsCommand::class,
        ]);
    }
}
