<?php

use App\Enums\Images\ImageTypeEnum;
use App\Http\Controllers\Newsletter\NewsletterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;


Route::get('/{type}/image/{filename}', function ($type, $filename) {
    try {
        $path = "{$type}/{$filename}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);

        return new Response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=86400'
        ]);
    } catch (\Exception $e) {
        abort(404);
    }
})->where('filename', '.*')
    ->where('type', ImageTypeEnum::getRegexPattern());

Route::prefix('newsletter')->group(function () {
    Route::get('unsubscribe/{token}', [NewsletterController::class, 'unsubscribeView'])
        ->name('newsletter.unsubscribe.view');
    Route::post('unsubscribe/{token}', [NewsletterController::class, 'confirmUnsubscribeWeb'])
        ->name('newsletter.unsubscribe.web');
});
