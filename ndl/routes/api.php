<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JourneyLogController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RouteMappingController;
use App\Http\Controllers\RouteAssigningController;
use App\Http\Controllers\WastageCountController;

use App\Http\Controllers\Masters\WastageMasterController;
use App\Http\Controllers\Masters\RouteMasterController;
use App\Http\Controllers\Masters\UnloadPointMasterController;
use App\Http\Controllers\Masters\WeightUnitMasterController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\WastageLogController;

 

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('settings',[SettingsController::class,'index']);

Route::post('auth/login' ,[AuthController::class,'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::match(['GET', 'POST'],'dashboard',[DashboardController::class,'index']);


    Route::match(['GET', 'POST'],'users',[UserController::class,'list']);
    Route::match(['GET', 'POST'],'users/list',[UserController::class,'listNoPagination']);
    Route::post('users/create',[UserController::class,'create']);
    Route::post('users/delete',[UserController::class,'delete']);
    Route::post('users/edit',[UserController::class,'edit']);
   

//Route::middleware(['basicAuth'])->group(function () {

 

    Route::match(['GET', 'POST'],'apartment',[ApartmentController::class,'list']);
    Route::match(['GET', 'POST'],'apartment/list',[ApartmentController::class,'listNoPagination']);
    Route::post('apartment/create',[ApartmentController::class,'create']);
    Route::post('apartment/delete',[ApartmentController::class,'delete']);
    Route::post('apartment/edit',[ApartmentController::class,'edit']);

    Route::match(['GET', 'POST'],'vehicle',[VehicleController::class,'list']);
    Route::match(['GET', 'POST'],'vehicle/list',[VehicleController::class,'listNoPagination']);
    Route::post('vehicle/create',[VehicleController::class,'create']);
    Route::post('vehicle/delete',[VehicleController::class,'delete']);
    Route::post('vehicle/edit',[VehicleController::class,'edit']);

    Route::post('vehicle/save-tracking',[VehicleController::class,'saveTracking']);
    Route::match(['GET', 'POST'],'vehicle/list-tracking',[VehicleController::class,'listTracking']);

    Route::match(['GET', 'POST'],'route-mapping',[RouteMappingController::class,'list']);
    Route::match(['GET', 'POST'],'route-mapping/list',[RouteMappingController::class,'listNoPagination']);
    Route::post('route-mapping/create',[RouteMappingController::class,'create']);
    Route::post('route-mapping/edit',[RouteMappingController::class,'edit']);
    Route::post('route-mapping/delete',[RouteMappingController::class,'delete']);

    Route::match(['GET', 'POST'],'route-assigning',[RouteAssigningController::class,'list']);
    Route::match(['GET', 'POST'],'route-assigning/list',[RouteAssigningController::class,'listNoPagination']);
    Route::post('route-assigning/create',[RouteAssigningController::class,'create']);
    Route::post('route-assigning/edit',[RouteAssigningController::class,'edit']);
    Route::post('route-assigning/delete',[RouteAssigningController::class,'delete']);
    Route::match(['GET', 'POST'],'route-assigning/get-driver-assigned-route',[RouteAssigningController::class,'getDriverAssignedRoute']);
    Route::match(['GET', 'POST'],'route-assigning/get-driver-assigned-apartment',[RouteAssigningController::class,'getDriverAssignedApartment']);
    Route::match(['GET', 'POST'],'route-assigning/check-open-journey',[RouteAssigningController::class,'checkOpenJourney']);
    Route::match(['GET', 'POST'],'route-assigning/get-completed-apartment-list-day',[RouteAssigningController::class,'getCompletedApartmentList']);
    
    Route::match(['GET', 'POST'],'journey-log',[JourneyLogController::class,'list']);
    Route::match(['GET', 'POST'],'journey-log/list',[JourneyLogController::class,'listNoPagination']);
    Route::post('journey-log/create',[JourneyLogController::class,'create']);
    Route::post('journey-log/edit',[JourneyLogController::class,'edit']);
    Route::post('journey-log/delete',[JourneyLogController::class,'delete']);

    Route::match(['GET', 'POST'],'wastage-count',[WastageCountController::class,'list']);
    Route::match(['GET', 'POST'],'wastage-count/list',[WastageCountController::class,'listNoPagination']);
    Route::post('wastage-count/create',[WastageCountController::class,'create']);
    Route::post('wastage-count/delete',[WastageCountController::class,'delete']);
    Route::post('wastage-count/edit',[WastageCountController::class,'edit']);

    Route::match(['GET', 'POST'],'wastage-log',[WastageLogController::class,'list']);
    Route::match(['GET', 'POST'],'wastage-log/list',[WastageLogController::class,'listNoPagination']);
    Route::post('wastage-log/create',[WastageLogController::class,'create']);
    Route::post('wastage-log/delete',[WastageLogController::class,'delete']);
    Route::post('wastage-log/edit',[WastageLogController::class,'edit']);
    
    //REPORTS

    Route::match(['GET', 'POST'],'reports/date-wise',[ReportsController::class,'dateWiseList']);
    Route::match(['GET', 'POST'],'reports/wastage-type-wise',[ReportsController::class,'wastageTypeList']);
    Route::match(['GET', 'POST'],'reports/apartment-wise',[ReportsController::class,'apartmentWise']);
    Route::match(['GET', 'POST'],'reports/date-wise-report',[ReportsController::class,'dateWiseList_Report']);
    
    //MASTERS
    Route::match(['GET', 'POST'],'wastage',[WastageMasterController::class,'list']);
    Route::match(['GET', 'POST'],'wastage/list',[WastageMasterController::class,'listNoPagination']);
    Route::post('wastage/create',[WastageMasterController::class,'create']);
    Route::post('wastage/delete',[WastageMasterController::class,'delete']);
    Route::post('wastage/edit',[WastageMasterController::class,'edit']);

    Route::match(['GET', 'POST'],'route',[RouteMasterController::class,'list']);
    Route::match(['GET', 'POST'],'route/list',[RouteMasterController::class,'listNoPagination']);
    Route::post('route/create',[RouteMasterController::class,'create']);
    Route::post('route/delete',[RouteMasterController::class,'delete']);
    Route::post('route/edit',[RouteMasterController::class,'edit']);


    Route::match(['GET', 'POST'],'unload-point',[UnloadPointMasterController::class,'list']);
    Route::match(['GET', 'POST'],'unload-point/list',[UnloadPointMasterController::class,'listNoPagination']);
    Route::post('unload-point/create',[UnloadPointMasterController::class,'create']);
    Route::post('unload-point/delete',[UnloadPointMasterController::class,'delete']);
    Route::post('unload-point/edit',[UnloadPointMasterController::class,'edit']);

    Route::match(['GET', 'POST'],'weight-unit',[WeightUnitMasterController::class,'list']);
    Route::match(['GET', 'POST'],'weight-unit/list',[WeightUnitMasterController::class,'listNoPagination']);
    Route::post('weight-unit/create',[WeightUnitMasterController::class,'create']);
    Route::post('weight-unit/delete',[WeightUnitMasterController::class,'delete']);
    Route::post('weight-unit/edit',[WeightUnitMasterController::class,'edit']);

 

});

Route::get('/cache', function() {
     
      Artisan::call('config:clear'); 
      Artisan::call('cache:clear'); 
     // Artisan::call('route:clear');
    //  Artisan::call('view:clear');

    
});
