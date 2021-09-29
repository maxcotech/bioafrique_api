<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function(){
    Route::namespace('Api')->group(function(){
        Route::middleware(['set_access_cookie','ensure_currency_selected'])->group(function(){
            Route::get('/generate/cookie','CookieController@create');
            Route::get('/check/cookie',"CookieController@checkCookie");
            Route::post('/user/register','AuthController@register');
            Route::get('/user_profile','UserController@getUserProfile');
            Route::post('/user/login','AuthController@login');
            Route::get('/categories','CategoryController@index');
            Route::post('/create/filer','FilerController@create');
            Route::get('/ip_address','AuthController@getUserIpAddress');
            Route::middleware(['auth.apicookie','app_access_guard'])->group(function(){
                Route::post('/category','CategoryController@create')->middleware('sasom_access_guard');
                Route::put('/category','CategoryController@update')->middleware('super_admin_access_guard');
                Route::put('/category_image','CategoryController@updateCategoryImage')->middleware('super_admin_access_guard');
                Route::delete('/category/{category_id}','CategoryController@delete')->middleware('super_admin_access_guard');
                Route::post('/store','StoreController@create');
                Route::get('/user','UserController@show');
                Route::delete('/user/logout','AuthController@logout');
            });
        });
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
