<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function(){
    Route::namespace('Api')->group(function(){
        Route::middleware(['set_access_cookie','ensure_currency_selected'])->group(function(){
            Route::get('/generate/cookie','CookieController@create');
            Route::get('/check/cookie',"CookieController@checkCookie");
            Route::post('/user/register','AuthController@register');
            Route::get('/user/profile','UserController@getUserProfile');
            Route::post('/user/login','AuthController@login');
            Route::get('/categories','CategoryController@index');
            Route::post('/create/filer','FilerController@create');
            Route::get('/ip_address','AuthController@getUserIpAddress');
            Route::get('/brands','BrandController@index');
            Route::middleware(['auth.apicookie','app_access_guard'])->group(function(){
                Route::post('/brand','BrandController@create')->middleware('sasom_access_guard');
                Route::post('/brand/logo','BrandController@uploadLogo')->middleware('super_admin_access_guard');
                Route::put('/brand','BrandController@update')->middleware('super_admin_access_guard');
                Route::delete('/brand/{brand_id}','BrandController@delete')->middleware('super_admin_access_guard');
                
                Route::post('/category','CategoryController@create')->middleware('sasom_access_guard');
                Route::put('/category','CategoryController@update')->middleware('super_admin_access_guard');
                Route::post('/category/image','CategoryController@updateCategoryImage')->middleware('super_admin_access_guard');
                Route::post('/category/icon','CategoryController@uploadCategoryIcon')->middleware('super_admin_access_guard');
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
