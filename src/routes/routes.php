<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('zler.router.prefix')], function (){
    Route::get('/token', '');
});