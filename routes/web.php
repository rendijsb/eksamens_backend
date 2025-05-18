<?php

use App\Http\Controllers\Newsletter\NewsletterController;
use Illuminate\Support\Facades\Route;

Route::prefix('newsletter')->group(function () {
    Route::get('unsubscribe/{token}', [NewsletterController::class, 'unsubscribeView'])
        ->name('newsletter.unsubscribe.view');
    Route::post('unsubscribe/{token}', [NewsletterController::class, 'confirmUnsubscribeWeb'])
        ->name('newsletter.unsubscribe.web');
});
