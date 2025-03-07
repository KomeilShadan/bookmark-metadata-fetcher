<?php

use App\Http\Controllers\BookmarkController;
use Illuminate\Support\Facades\Route;

Route::controller(BookmarkController::class)->group(function () {
    Route::get('/bookmarks', 'index');
    Route::post('/bookmarks', 'store');
});
