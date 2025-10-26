<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {
    // PDF Route harus di atas resource route
    Route::get('/adjustments/{adjustment}/pdf', 'AdjustmentController@pdf')
        ->name('adjustments.pdf');
    
    // Resource Route
    Route::resource('adjustments', 'AdjustmentController');
});
