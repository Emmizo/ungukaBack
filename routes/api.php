<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'cors'], function (){
    
Route::post('login', 'UserController@login');
Route::post('registration', 'UserController@register');
Route::post('sendCode','UserController@sendCode');
Route::post('viewCrops','UserController@viewCrops');
Route::group(['middleware' => 'auth.jwt'], function () {

// });
// Route::group(['middleware' => 'jwt.verify'], function() {
    
Route::group(['middleware' => 'farmers'], function () { 
    Route::post('logout', 'UserController@logout');
    Route::post('complete', 'FarmerController@store');
    Route::post('user', 'UserController@getAuthUser');
    Route::post('createFarms', 'FarmController@store');
    Route::post('viewFarms', 'FarmController@index');
    Route::post('ShowFarmer','FarmerController@show');
    Route::post('ShowFarm','FarmController@show');
    Route::post('UpdateFarmer','FarmerController@update');
    Route::post('UpdateFarms','FarmController@update');
    Route::post('AddExpense','ExpenseController@store');
    Route::post('ViewExpenses','ExpenseController@show');
    Route::post('AllFarms', 'ExpenseController@index');
    Route::post('addStock','StockController@store');
    Route::post('stock','StockController@index');
    Route::post('updateStock','StockController@update');
    Route::post('SignleStockDetails','StockController@show');
    Route::post('StockWithExpense','StockController@Profit');
    Route::post('publish','StockController@publish');
    Route::post('pending2','FarmerController@myOrders');
    Route::post('Season','FarmerController@seasons');
    Route::post('changePassword','FarmerController@changePassword');
    Route::post('checkProfit','FarmerController@checkProfit');
    Route::post('process','FarmerController@processing');
    Route::post('delivered2','FarmerController@delivered');
    Route::post('cancelled2','FarmerController@cancelled');
    Route::post('accept','FarmerController@accept');
    Route::post('decline','FarmerController@cancel');
    Route::post('markAsDelivered','FarmerController@markAsDelivered');
    Route::post('restore','FarmerController@restore');
    Route::post('changePhone','FarmerController@changePhone');
    Route::post('createCurrentCrop','ExpenseController@newCrop');
    Route::post('feeds','StockController@feeds');
    Route::post('insideCrop','FarmController@farmsUsed');
    Route::post('insideProfile','FarmController@insideProfile');
    route::post('insideFeed','StockController@insideFeed');
   
});
Route::group(['middleware'=>'customers'], function(){
    Route::post('DetailCustomer','CustomerController@index');
    Route::post('CustomerProfile','CustomerController@store');
    Route::post('UpdateCustomer','CustomerController@update');
    Route::post('ViewHarvest','CustomerController@ViewHarvest');
    Route::post('makeOrder','CustomerController@makeOrder');
    Route::post('pending','CustomerController@myOrder');
    Route::post('CustomerChangePassword','CustomerController@changePassword');
    Route::post('delivered','CustomerController@delivered');
    Route::post('reorder','CustomerController@reorder');
    Route::post('processed','CustomerController@process');
    Route::post('cancelled','CustomerController@cancelled');
    Route::post('cancel','CustomerController@cancel');
    Route::post('logout', 'UserController@logout');
    Route::post('changePhone2','FarmerController@changePhone');
    route::post('allOrdered','CustomerController@allOrdered');
    Route::post('display','CustomerController@fillQuantity');
     Route::post('supplier','CustomerController@supplier');
});
Route::group(['middleware'=>'admin'], function(){
    route::post('farmers','AdminController@index');
    route::post('customers','AdminController@allCustomers');
    route::post('farms','AdminController@FarmerWithFarms');
    route::post('crops','AdminController@showCrops');
    Route::post('storeCrops','AdminController@storeCrops');
    Route::post('updateCrops','FarmController@updateCrops');
    Route::post('suspended','AdminController@farmerSuspended');
    Route::post('allow','AdminController@allowAccess');
    Route::post('updateSeason','AdminController@updateSeason');
    });
});
});