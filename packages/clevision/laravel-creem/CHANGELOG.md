# Changelog

All notable changes to `clevision/laravel-creem` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of the Laravel Creem package
- `Creem` facade with full API surface (checkouts, products, subscriptions, customers, orders, discounts, license keys, refunds)
- `CreemClient` HTTP client with automatic retry on 5xx errors
- `CreemServiceProvider` with auto-discovery support
- `VerifyCreemSignature` middleware for HMAC-SHA256 webhook verification
- `WebhookController` that dispatches typed Laravel events for all Creem event types
- 12 webhook event classes covering the full Creem event lifecycle
- `creem:webhook-secret` Artisan command for setup guidance
- `creem:sync-products` Artisan command with optional caching
- `config/creem.php` with full environment variable support
- `verifyWebhookSignature()` and `verifyRedirectSignature()` helpers
- Test suite with Pest covering signature verification and webhook dispatching
- Support for Laravel 10, 11, and 12 with PHP 8.1+
