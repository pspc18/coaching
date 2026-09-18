<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'islogin'], function () {
    


});

Route::match(['get', 'post'], 'helpAndUpdate', 'initial\InitialController@helpAndUpdate');
Route::match(['get', 'post'], 'backup', 'initial\InitialController@backup');
Route::match(['get', 'post'], 'updateInitialConfig', 'initial\InitialController@updateInitialConfig');
Route::match(['get', 'post'], 'optimizeImages', 'initial\InitialController@optimizeImages');