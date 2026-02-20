<?php

use App\Http\Controllers\CreemDemoController;
use Illuminate\Support\Facades\Route;

// Creem test dashboard
Route::get('/', [CreemDemoController::class, 'index'])->name('creem.demo');
Route::get('/checkout/success', [CreemDemoController::class, 'checkoutSuccess'])->name('creem.checkout.success');
Route::post('/creem/checkout', [CreemDemoController::class, 'checkout'])->name('creem.checkout');
Route::get('/creem/api', [CreemDemoController::class, 'api'])->name('creem.api');
Route::post('/creem/verify-signature', [CreemDemoController::class, 'verifySignature'])->name('creem.verify-signature');
