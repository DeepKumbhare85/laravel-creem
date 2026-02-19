<?php

namespace Clevision\Creem\Console\Commands;

use Illuminate\Console\Command;

class WebhookSecretCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'creem:webhook-secret
                            {--show : Display the current webhook secret without generating a new one}';

    /**
     * The console command description.
     */
    protected $description = 'Display or generate instructions for setting the CREEM_WEBHOOK_SECRET environment variable';

    public function handle(): int
    {
        if ($this->option('show')) {
            $current = config('creem.webhook_secret');

            if ($current) {
                $this->components->info('Current CREEM_WEBHOOK_SECRET:');
                $this->line('  ' . $current);
            } else {
                $this->components->warn('CREEM_WEBHOOK_SECRET is not set.');
            }

            return self::SUCCESS;
        }

        $this->components->info('Setting up Creem Webhook Secret');
        $this->newLine();

        $this->line('Follow these steps to configure your webhook secret:');
        $this->newLine();

        $this->components->bulletList([
            'Log in to your <href=https://creem.io/dashboard/developers>Creem Dashboard</>.',
            'Navigate to <comment>Developers › Webhooks</>.',
            'Register your webhook endpoint URL: <comment>' . url(config('creem.webhook_route', 'creem/webhook')) . '</>',
            'Copy the <comment>Webhook Secret</> shown after saving.',
            'Add it to your <comment>.env</> file:',
        ]);

        $this->newLine();
        $this->line('CREEM_WEBHOOK_SECRET=<your_secret_here>');
        $this->newLine();

        $this->line('Then clear your config cache:');
        $this->line('  php artisan config:clear');
        $this->newLine();

        $this->components->info('Your webhook endpoint is registered at:');
        $this->line('  ' . url(config('creem.webhook_route', 'creem/webhook')));
        $this->newLine();

        $this->components->info('To verify the current secret is loaded, run:');
        $this->line('  php artisan creem:webhook-secret --show');

        return self::SUCCESS;
    }
}
