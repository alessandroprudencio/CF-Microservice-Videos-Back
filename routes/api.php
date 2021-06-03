<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('App\Http\Controllers\Api')->group(function () {
    Route::apiResource('categories', 'CategoryController');
    Route::apiResource('genres', 'GenreController');
    Route::apiResource('cast_members', 'CastMemberController');
    Route::apiResource('videos', 'VideoController');
});
