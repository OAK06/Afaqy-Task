<?php

use Illuminate\Http\Request;

Route::fallback(function(){
    return response()->json(['error' => 'Resource not found.'], 404);
})->name('fallback');

Route::group(['middleware' => 'throttle:5,1'], function () {
	Route::post('getVehicleExpenses','VehicleController@getVehicleExpenses');
});