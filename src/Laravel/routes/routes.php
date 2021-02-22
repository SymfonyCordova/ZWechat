<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('zler-wechat.router.prefix'),'middleware' => [] ], function (){
    Route::get('/token', '\Zler\Wechat\Laravel\Controller\GzhController@token');
});