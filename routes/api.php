<?php

// Route::get('categories', 'Api\CategoryController@index');
// Route::post('categories', 'Api\CategoryController@store');
// Route::put('categories/{id}', 'Api\CategoryController@update');
// Route::delete('categories/{id}', 'Api\CategoryController@delete');

Route::apiResource('categories', 'Api\CategoryController');