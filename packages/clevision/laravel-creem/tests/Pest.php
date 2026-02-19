<?php

use Clevision\Creem\CreemClient;
use Clevision\Creem\Facades\Creem;
use Clevision\Creem\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

it('resolves the Creem facade from the container', function () {
    expect(app('creem'))->toBeInstanceOf(\Clevision\Creem\Creem::class);
});

it('resolves the CreemClient singleton from the container', function () {
    $clientA = app(CreemClient::class);
    $clientB = app(CreemClient::class);

    expect($clientA)->toBeInstanceOf(CreemClient::class);
    expect($clientA)->toBe($clientB); // same singleton instance
});
