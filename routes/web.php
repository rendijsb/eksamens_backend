<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;


Route::get('/products/image/{filename}', function ($filename) {
    if (!Storage::disk('local')->exists('public/products/' . $filename)) {
        abort(404);
    }

    $file = Storage::disk('local')->get('public/products/' . $filename);

    $mimeType = Storage::disk('local')->mimeType('public/products/' . $filename);

    return new Response($file, 200, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
        'Cache-Control' => 'public, max-age=86400'
    ]);
})->where('filename', '.*');
