<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function(){
    Route::namespace('Api')->group(function(){
        Route::get('/generate/cookie','CookieController@create');
        Route::post('/register','AuthController@register');
        Route::post('/login','AuthController@login');
        Route::get('/get/categories','CategoryController@index');
        Route::post('/create/filer','FilerController@create');
        Route::middleware(['auth.apicookie','app_access_guard'])->group(function(){
            Route::post('/create/category','CategoryController@create');
            Route::put('/update/category','CategoryController@update');
            Route::put('/update/category_image','CategoryController@updateCategoryImage');
            Route::delete('/delete/category/{category_id}','CategoryController@delete');
            Route::post('/create/store','StoreController@create');
            Route::get('/get/user','UserController@show');
            Route::delete('/logout','AuthController@logout');
        });
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
