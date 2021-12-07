<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function(){
    Route::namespace('Api')->group(function(){
        Route::middleware(['add_auth_header','set_access_cookie','ensure_currency_selected','cors'])->group(function(){
            Route::get('/generate/cookie','CookieController@create');
            Route::get('/check/cookie',"CookieController@checkCookie");
            Route::post('/user/register','AuthController@register');
            Route::post('/user/login','AuthController@login');
            Route::put('/user/currency','UserController@updateUserCurrency');
            Route::get('/categories','CategoryController@index');
            Route::post('/create/filer','FilerController@create');
            Route::get('/ip_address','AuthController@getUserIpAddress');
            Route::get('/brands','BrandController@index');
            Route::get('/user/profile','UserController@getUserProfile');
            Route::get('/store/search','StoreController@search');
            Route::get('/catalog','ProductController@index');
            
            Route::get('/product/{slug}','ProductController@show');
            Route::get('/countries','CountryController@index');
            Route::get('/search/{search_type}','SearchController@index');
            Route::get('/variation_options','VariationOptionsController@index');

            Route::post('/shopping_cart/item','ShoppingCartController@create');
            Route::put('/shopping_cart/item','ShoppingCartController@update');
            Route::get('/shopping_cart/items','ShoppingCartController@index');
            Route::delete('/shopping_cart/item/{cart_id}','ShoppingCartController@delete');
            Route::get('/shopping_cart/item/count','ShoppingCartController@getCartCount');

            Route::post('/wish_list','ProductWishListController@create');
            Route::get('/wish_list','ProductWishListController@index');
            Route::delete('/wish_list','ProductWishListController@delete');
        });

        Route::middleware(['auth.apicookie','app_access_guard','ensure_currency_selected','cors'])->group(function(){
            Route::post('/brand','BrandController@create')->middleware('sasom_access_guard');
            Route::post('/brand/logo','BrandController@uploadLogo')->middleware('super_admin_access_guard');
            Route::put('/brand','BrandController@update')->middleware('super_admin_access_guard');
            Route::delete('/brand/{brand_id}','BrandController@delete')->middleware('super_admin_access_guard');

            Route::post('/variation_option','VariationOptionsController@create')->middleware('super_admin_access_guard');

            Route::post('/product','ProductController@create')->middleware('store_staff_guard');
            Route::put('/product','ProductController@update')->middleware('store_staff_guard');
            Route::post('/product/gallery_image','ProductController@uploadGalleryImage')->middleware('store_staff_guard');
            Route::post('/product/image','ProductController@uploadProductImage')->middleware('store_staff_guard');
            Route::post('/product/variation_image','ProductController@uploadProductVariationImage')->middleware('store_staff_guard');
            Route::delete('/product/{product_id}','ProductController@delete')->middleware('store_staff_guard');
            Route::post('/category','CategoryController@create')->middleware('sasom_access_guard');
            Route::put('/category','CategoryController@update')->middleware('super_admin_access_guard');
            Route::post('/category/image','CategoryController@updateCategoryImage')->middleware('super_admin_access_guard');
            Route::post('/category/icon','CategoryController@uploadCategoryIcon')->middleware('super_admin_access_guard');
            Route::delete('/category/{category_id}','CategoryController@delete')->middleware('super_admin_access_guard');
            Route::post('/store','StoreController@create')->middleware('store_owner_access_guard');
            Route::put('/store','StoreController@update')->middleware('store_owner_access_guard');
            Route::post('/store/logo','StoreController@uploadStoreLogo')->middleware('store_owner_access_guard');
            Route::get('/store/products','ProductController@getStoreProducts');
            Route::post('/store/add_user','StoreController@addUserToStore')->middleware('store_staff_guard');
            Route::post('/store/staff/token','StoreStaffTokenController@create')->middleware('sasom_access_guard');
            Route::get('/store/staff/tokens','StoreStaffTokenController@index')->middleware('sasom_access_guard');
            Route::get('/store/staff/type','StoreStaffController@getStoreStaffType');
            Route::get('/store/staffs','StoreStaffController@getStoreStaffs')->middleware('sasom_access_guard');
            Route::delete('/store/staff/token/{id}','StoreStaffTokenController@delete')->middleware('sasom_access_guard');
            Route::patch('/store/staff/token/{id}/toggle_expiry','StoreStaffTokenController@toggleExpiry')->middleware('sasom_access_guard');
            Route::put('/store/staff/position','StoreStaffController@changeStaffPosition')->middleware('store_owner_access_guard');
            Route::patch('/store/staff/{staff_id}/toggle_status','StoreStaffController@toggleStaffStatus')->middleware('store_staff_guard');
            Route::delete('/store/staff/{staff_id}','StoreStaffController@removeStoreStaff')->middleware('sasom_access_guard');
            Route::get('/user','UserController@show');
            Route::delete('/user/logout','AuthController@logout');

            Route::post('/locations/populate','LocationController@populateLocations');

            Route::get('/cities/{route_param?}','CityController@index');

            Route::get('/states/{route_params?}','StateController@index');

            Route::post('/shipping/group','ShippingGroupController@create')->middleware('sasom_access_guard');
            Route::get('/shipping/groups','ShippingGroupController@index')->middleware('store_staff_guard');
            Route::put('/shipping/group','ShippingGroupController@update')->middleware('sasom_access_guard');
            Route::delete('/shipping/group/{group_id}','ShippingGroupController@delete')->middleware('sasom_access_guard');
            Route::post('/shipping/location','ShippingLocationController@create')->middleware('sasom_access_guard');
            Route::get('/shipping/locations','ShippingLocationController@index')->middleware('store_staff_guard');
            Route::put('/shipping/location','ShippingLocationController@update')->middleware('sasom_access_guard');
            Route::delete('/shipping/location/{location_id}','ShippingLocationController@delete')->middleware('sasom_access_guard');

            Route::post('/billing/address','BillingAddressController@create');
            Route::put('/billing/address','BillingAddressController@update');
            Route::delete('/billing/address/{address_id}','BillingAddressController@delete');
            Route::get('/billing/addresses','BillingAddressController@index');

            Route::get('/checkout','CheckoutController@index');

            Route::post('/payment/init','PaymentController@create');
            Route::put('/payment/verify','PaymentController@verifyPayment');
        });
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
