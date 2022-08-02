<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\MiscellaneousController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\PaymentGatewayController;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1', 'middleware' => 'setlang'],function(){
    
    //todo: amount settings
    Route::get('/amount-settings',[MiscellaneousController::class,'amountSettings']);
    
    //todo: currency
    Route::get('/currency',[MiscellaneousController::class,'currencyInfo']);
    
    //todo: languages
    Route::get('/language',[LanguageController::class,'languageInfo']);
    Route::post('/translate-string',[LanguageController::class,'translateString']);
    
    Route::get('/country',[UserController::class,'country']);
    Route::get('country/service-city/{id}',[UserController::class,'serviceCity']);
    Route::get('country/service-city/service-area/{country_id}/{city_id}',[UserController::class,'serviceArea']);
    
    Route::post('/register',[UserController::class,'register']);
    Route::post('/login',[UserController::class,'login']);
    Route::post('social/login',[UserController::class,'socialLogin']);
    Route::post('/send-otp-in-mail',[UserController::class,'sendOTP']);
    Route::post('/reset-password',[UserController::class,'resetPassword']);
   
    /*--------------------------
        Buyer Api Routes
    --------------------------*/
    Route::group(['prefix' => 'user/','middleware' => 'auth:sanctum'],function (){
        Route::post('logout',[UserController::class,'logout']);
        Route::get('profile',[UserController::class,'profile']);
        Route::post('change-password',[UserController::class,'changePassword']);
        Route::post('update-profile',[UserController::class,'updateProfile']);
        Route::post('/add-service-rating/{id}',[ServiceController::class,'serviceRating']);
        Route::post('/my-orders',[UserController::class,'myOrders']);
        Route::post('/my-orders/{id}',[UserController::class,'singleOrder']);
        
        //new added in sanctom - by sharifur
        Route::post('/support-tickets',[UserController::class,'allTickets']);
        Route::get('/view-ticket/{id}',[UserController::class,'viewTickets']);
        Route::post('ticket/message-send',[UserController::class,'sendMessage']);
        Route::post('/send-otp-in-mail/success',[UserController::class,'sendOTPSuccess']);
        Route::post('ticket/create',[UserController::class,'createTicket']);
        Route::post('payment-status-update',[ServiceController::class,'paymentStatusUpdate']);
        
        //payment gateway list
        Route::post('payment-gateway-list',[PaymentGatewayController::class,'gatewayList']);
    });

    Route::get('/slider',[SliderController::class,'slider']);
    Route::get('/category',[CategoryController::class,'category']);
    Route::get('/category/sub-category/{category_id}',[CategoryController::class,'subCategory']);

    Route::get('/top-services',[ServiceController::class,'topService']);
    Route::get('/latest-services',[ServiceController::class,'latestService']);
    Route::get('/service-details/{id}',[ServiceController::class,'serviceDetails']);
    
    Route::get('/service-list/all-services',[ServiceController::class,'allServices']);
    Route::get('/service-list/search-by-category/{category_id}',[ServiceController::class,'searchByCategory']);
    Route::get('/service-list/category-subcategory-search/{category_id}/{subcategory_id}',[ServiceController::class,'searchBySubCategory']);
    Route::get('/service-list/category-subcategory-rating-search/{category_id?}/{subcategory_id?}/{rating?}',[ServiceController::class,'searchByRating']);
    Route::get('/service-list/category-subcategory-rating-sort-by-search/{category_id?}/{subcategory_id?}/{rating?}/{sort_by?}',[ServiceController::class,'searchBySort']);
    Route::get('service-list/service-book/{id?}',[ServiceController::class,'serviceBook']);
    Route::get('service-list/service-schedule/{day?}/{seller_id?}',[ServiceController::class,'scheduleByDay']);
    Route::post('service-list/coupon-apply',[ServiceController::class,'couponApply']);

    Route::get('city/service-city',[ServiceController::class,'serviceCity']);
    Route::post('home/home-search', [ServiceController::class,'homeSearch']);
    
    //verify 
    Route::group(['middleware' => 'auth:sanctum'],function (){
        Route::post('service/order', [ServiceController::class,'order']);
        Route::post('image/upload', [ServiceController::class,'imageUpload']);
        Route::post('payment-image/manual-payment', [ServiceController::class,'manualPaymentImage']);
        
    });
    
    /*--------------------------
        Seller Api Routes
    --------------------------*/
    Route::group(['prefix' => 'seller','middleware' => 'auth:sanctum'],function (){
        Route::post('dashboard-info', [SellerController::class,'dashboardInfo']);
        Route::post('recent-orders', [SellerController::class,'recentOrders']);
         
         //profile 
        Route::group(['prefix' => 'profile'],function(){
            Route::post('/',[SellerController::class,'profileInfo']);
            Route::post('/edit',[SellerController::class,'profileEdit']);
            Route::post('/verify',[SellerController::class,'profileVerify']);
            Route::post('/deactivate',[SellerController::class,'profileDeactivate']);
        }); 
        
        //my orders
        Route::group(['prefix' => 'my-orders'],function(){
             Route::post('/',[SellerController::class,'myOrders']);
            Route::post('/{id}',[SellerController::class,'singleOrder']);
        });
      
        
        
        Route::post('/support-tickets',[SellerController::class,'allTickets']);
        Route::get('/view-ticket/{id}',[SellerController::class,'viewTickets']);
        Route::post('ticket/message-send',[SellerController::class,'sendMessage']);
        
        
        //payment history
        Route::group(['prefix' => 'payment-history'],function(){
            Route::post('/',[SellerController::class,'paymentHistory']);
            Route::post('/create',[SellerController::class,'createPaymentRequest']);
            Route::post('/details/{id}',[SellerController::class,'paymentRequestDetails']);
        });
        
        
    });
  

});
