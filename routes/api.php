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
            
            Route::get('/category/products/{category_param}','ProductController@getCategoryProducts');
            Route::get('/product/{slug}','ProductController@show');
            Route::get('/products/recently_viewed','ProductController@getRecentlyViewed');
            Route::get('/countries','CountryController@index');
            Route::get('/currencies','CurrencyController@index');
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

            Route::get('/reviews/{review_id?}','ProductReviewController@index');
            Route::delete('/review/{review_id}','ProductReviewController@delete');

            Route::get('/cities/{route_param?}','CityController@index');

            Route::get('/states/{route_params?}','StateController@index');

            Route::get('/stores','StoreController@index');

            Route::get('/widgets','WidgetController@index');

            Route::get('/banks/codes/{currency_id}','StoreBankAccountController@getBankCodes');
        
            Route::post('/support/message','ContactMessageController@create');

            Route::get('/home_banners','HomeBannerController@index');

            Route::post('/email_verification/send','AuthController@sendEmailVerification');
            Route::post('/email_verification/complete','AuthController@completeEmailVerification');
            Route::post('/reset_password/email/init','AuthController@initEmailResetPassword');
            Route::post('/reset_password/email/complete','AuthController@completeEmailPasswordReset');
        });

        Route::middleware(['auth.apicookie','app_access_guard','ensure_currency_selected','cors'])->group(function(){
            Route::post('/brand','BrandController@create')->middleware('sasom_access_guard');
            Route::post('/brand/logo','BrandController@uploadLogo')->middleware('super_admin_access_guard');
            Route::put('/brand','BrandController@update')->middleware('super_admin_access_guard');
            Route::delete('/brand/{brand_id}','BrandController@delete')->middleware('super_admin_access_guard');
            Route::patch('/brand/status','BrandController@updateBrandStatus')->middleware('super_admin_access_guard');
            Route::post('/variation_option','VariationOptionsController@create')->middleware('super_admin_access_guard');
            Route::patch('/product/status','ProductController@updateProductStatus')->middleware('super_admin_access_guard');
            Route::post('/product','ProductController@create')->middleware('store_staff_guard');
            Route::put('/product','ProductController@update')->middleware('store_staff_guard');
            Route::post('/product/gallery_image','ProductController@uploadGalleryImage')->middleware('store_staff_guard');
            Route::post('/product/image','ProductController@uploadProductImage')->middleware('store_staff_guard');
            Route::post('/product/variation_image','ProductController@uploadProductVariationImage')->middleware('store_staff_guard');
            Route::delete('/product/{product_id}','ProductController@delete')->middleware('sasom_access_guard');
            Route::post('/category','CategoryController@create')->middleware('sasom_access_guard');
            Route::put('/category','CategoryController@update')->middleware('super_admin_access_guard');
            Route::post('/category/image','CategoryController@updateCategoryImage')->middleware('super_admin_access_guard');
            Route::post('/category/icon','CategoryController@uploadCategoryIcon')->middleware('super_admin_access_guard');
            Route::delete('/category/{category_id}','CategoryController@delete')->middleware('super_admin_access_guard');
            Route::patch('/category/status','CategoryController@updateCategoryStatus')->middleware('super_admin_access_guard');
            Route::post('/store','StoreController@create')->middleware('store_owner_access_guard');
            Route::put('/store','StoreController@update')->middleware('store_owner_access_guard');
            Route::get('/store/wallet','StoreController@getWallet')->middleware('sasom_access_guard');
            Route::get('/store/dashboard','StoreController@getDashboardData');
            Route::patch('/store/status','StoreController@updateStoreStatus')->middleware('super_admin_access_guard');
            Route::delete('/store/{store_id}','StoreController@delete')->middleware('sasom_access_guard');
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
            Route::get('/store/users','UserController@getStoreUsers')->middleware('store_staff_guard');
            Route::get('/user','UserController@show');
            Route::get('/users','UserController@index');
            Route::delete('/user/account/{user_id}','UserController@delete')->middleware('super_admin_access_guard');
            Route::patch('/users/status','UserController@updateUserStatus')->middleware('super_admin_access_guard');
            Route::delete('/user/logout','AuthController@logout');
            Route::put('/user/password','AuthController@resetPassword');
            Route::post('/locations/populate','LocationController@populateLocations');

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
            Route::get('/billing/addresses/current','BillingAddressController@getCurrentAddress');
            Route::patch('/billing/current/{address_id}','BillingAddressController@changeCurrentAddress');
            Route::get('/checkout','CheckoutController@index');

            Route::post('/payment/init','PaymentController@create');
            Route::put('/payment/verify','PaymentController@verifyPayment');
            Route::get('/payment/methods','PaymentController@getPaymentMethods');

            Route::post('/review','ProductReviewController@create');
            Route::put('/review','ProductReviewController@update');
            Route::get('/pending/reviews','ProductReviewController@getPendingReviews');

            Route::get('/sub_orders/{sub_order_id?}','SubOrderController@index');
            Route::put('/sub_order/status','SubOrderController@updateStatus');
            Route::get('/order_items/{order_item_id?}','OrderItemController@index');

            Route::get('/admin/wallet','AdminController@getWallet')->middleware('super_admin_access_guard');
            Route::post('/admin/wallet/debit','AdminController@debitWallet')->middleware('super_admin_access_guard');
            Route::post('/admin/wallet/credit','AdminController@creditWallet')->middleware('super_admin_access_guard');
            Route::get('/admin/dashboard','AdminController@getDashboardData')->middleware('super_admin_access_guard');
            Route::get('/admin/preferences','AdminController@getAdminPreferences')->middleware('super_admin_access_guard');
            Route::put('/admin/preferences','AdminController@updateAdminPreferences')->middleware('super_admin_access_guard');
            
            Route::delete('/widget/{widget_id}','WidgetController@deleteWidget')->middleware('super_admin_access_guard');
            Route::post('/widget','WidgetController@upload')->middleware('super_admin_access_guard');
            Route::post('/widget/items','WidgetController@uploadItems')->middleware('super_admin_access_guard');
            Route::get('/widget/items','WidgetController@getWidgetItems')->middleware('super_admin_access_guard');
            Route::post('/widget/image','WidgetController@uploadImage')->middleware('super_admin_access_guard');
            Route::patch('/widget/status','WidgetController@updateWidgetStatus')->middleware('super_admin_access_guard');
            Route::patch('/widget/index','WidgetController@swapWidgetIndex')->middleware('super_admin_access_guard');
           
            Route::post('/store/bank_account','StoreBankAccountController@create')->middleware('store_owner_access_guard');
            Route::put('/store/bank_account','StoreBankAccountController@update')->middleware('store_owner_access_guard');
            Route::get('/store/bank_accounts','StoreBankAccountController@index');
            Route::delete('/store/bank_account/{account_id}','StoreBankAccountController@delete');

            Route::post('/country','CountryController@create')->middleware('super_admin_access_guard');
            Route::put('/country','CountryController@update')->middleware('super_admin_access_guard');
            Route::delete('/country/{country_id}','CountryController@delete')->middleware('super_admin_access_guard');
            Route::post('/country/upload_logo','CountryController@uploadLogo')->middleware('super_admin_access_guard');

            Route::post('/state','StateController@create')->middleware('super_admin_access_guard');
            Route::put('/state','StateController@update')->middleware('super_admin_access_guard');
            Route::patch('/state/status','StateController@updateStatus')->middleware('super_admin_access_guard');
            Route::delete('/state/{state_id}','StateController@delete')->middleware('super_admin_access_guard');

            Route::post('/city','CityController@create');
            Route::patch('/city/status','CityController@updateStatus')->middleware('super_admin_access_guard');
            Route::put('/city','CityController@update')->middleware('super_admin_access_guard');
            Route::delete('/city/{city_id}','CityController@delete')->middleware('super_admin_access_guard');

            Route::post('/currency','CurrencyController@create')->middleware('super_admin_access_guard');
            Route::put('/currency','CurrencyController@update')->middleware('super_admin_access_guard');
            Route::delete('/currency/{currency_id}','CurrencyController@delete')->middleware('super_admin_access_guard');
            
            Route::post('/withdrawal_request','WithdrawalRequestController@create')->middleware('store_owner_access_guard');
            Route::get('/withdrawal_requests','WithdrawalRequestController@index')->middleware('super_admin_access_guard');
            Route::post('/withdrawal_request/settle','WithdrawalRequestController@settle')->middleware('super_admin_access_guard');
            Route::post('/withdrawal_requests/settle','WithdrawalRequestController@massSettle')->middleware('super_admin_access_guard');
            Route::patch('/withdrawal_request/status','WithdrawalRequestController@updateStatus')->middleware('super_admin_access_guard');

            Route::get('/support/messages','ContactMessageController@index')->middleware('super_admin_access_guard');
            Route::patch('/support/message/status','ContactMessageController@updateStatus')->middleware('super_admin_access_guard');
            Route::post('/home_banner','HomeBannerController@create')->middleware('super_admin_access_guard');
            Route::put('/home_banner/text','HomeBannerController@uploadText')->middleware('super_admin_access_guard');
            Route::delete('/home_banner/{banner_id}','HomeBannerController@delete')->middleware('super_admin_access_guard');
        });
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
