<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('zler.router.prefix'),'middleware' => [] ], function (){
    Route::get('/token', '\Zler\Wechat\Controller\GzhController@token');
});