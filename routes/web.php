<?php

use App\Enums\Images\ImageTypeEnum;
use App\Http\Controllers\Newsletter\NewsletterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;


Route::get('/{type}/image/{filename}', function ($type, $filename) {
    try {
        $path = "public/{$type}/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('local')->get($path);
        $mimeType = Storage::disk('local')->mimeType($path);

        return new Response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=86400'
        ]);
    } catch (\ValueError $e) {
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
