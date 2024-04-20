<?php



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;



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

Route::get('/optimize-cache', function () {
    Artisan::call('optimize:clear');
    return "Cache is cleared";
});

Route::get('plant_site_data11', 'Api\PlantSiteDataController@plant_site_data');
Route::get('plant_site_data111', 'Api\PlantSiteDataController2@plant_site_data');

Route::get('push_notification', 'Api\NotificationController@push_notification');

Route::get('/about-us', 'Api\AuthController@about_us');

//Intrix App APi
Route::prefix('intrix')->group(function () {
    Route::get('/banners', 'Api\IntrixController@banners');
    Route::post('/banner-status', 'Api\IntrixController@bannerStatus');
    Route::get('/get-plant-id', 'Api\PlantController@getPlantID');
    Route::get('/system-info', 'Api\IntrixController@systemInfo');
    Route::get('/battery-info', 'Api\IntrixController@batteryInfo');
    Route::post('/environment-benefit', 'Api\IntrixController@plantEnvBenefitGraph');
    Route::post('/cost-saving', 'Api\IntrixController@plantCostSaving');
});
Route::group(['middleware' => ['guest:api']], function () {

    Route::post('login', 'Api\AuthController@login');

    Route::post('signup', 'Api\AuthController@signup');

    Route::post('forgot-password', 'Api\ForgotPasswordController@generateForgotPassCode');

});

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('logout', 'Api\AuthController@logout');

    //profile routes

    Route::post('update-profile', 'Api\AuthController@updateProfile');

    // send message
    Route::post('send-message', 'Api\NotificationController@sendMessage');

    //plant controller

    Route::get('dashboard-filter-data', 'Api\PlantController@dashboardFilterData');

    Route::get('dashboard', 'Api\PlantController@dashboard');

    Route::get('all-plants', 'Api\PlantController@allPlants');
    Route::get('plant-list', 'Api\PlantController@PlantsList');
    Route::get('plant-chart-hybrid-data', 'Api\PlantController@plantChartHybridData');
    Route::get('history-graph-data', 'Api\PlantController@HistoryGraphData');
//to be delete later
    Route::get('plant-details', 'Api\PlantController@plantDetails');
//New Latest
    Route::get('plant-detail', 'Api\PlantController@plantDetail');
    Route::get('plant-inverter-detail', 'Api\PlantController@plantInverterDetail');

    Route::post('add-plant', 'Api\PlantController@addPlant');

    Route::get('plant-chart', 'Api\PlantController@plant_chart');
    Route::get('outages-served', 'Api\PlantController@outagesServed');
    Route::get('consumption-in-peak-hours', 'Api\PlantController@consumptionInPeakHours');
    Route::get('energy-sources', 'Api\PlantController@energySources');
    Route::get('solar-energy-utilization', 'Api\PlantController@solarEnergyUtilization');

    Route::get('plant-expected-actual-chart', 'Api\PlantController@plantExpectedActualChart');

    Route::get('plant-environment-chart', 'Api\PlantController@plantEnvironmentChart');

    //notifications

    Route::get('/all-notifications', 'Api\NotificationController@allNotifications');

    Route::post('/update-readStatus', 'Api\NotificationController@updateReadStatus');

    Route::get('/check-notifications', 'Api\NotificationController@checkNotifications');
    Route::get('/notification-detail', 'Api\NotificationController@getNotificationDetail');

    //FCM-Notifications

    Route::get('/push-notification', 'Api\NotificationController@push_notification');
    Route::post('/ios-noti', 'Api\NotificationController@sendNotificationIOS');

    //inverter routes

    Route::post('/add-inverter', 'Api\InverterController@addInverter');

    //Complain

    Route::get('/all-complain', 'Api\ComplainController@allComplains');
    Route::get('/ticket-data', 'Api\ComplainController@ticketData');
    Route::post('/store-complain', 'Api\ComplainController@storeComplain');
    Route::post('/add-comments', 'Api\ComplainController@addComments');
    Route::get('/ticket-history', 'Api\ComplainController@ticketHistory');
    Route::post('/update-ticket-status', 'Api\ComplainController@updateTicketStatus');

});

Route::get('co2-reduction', 'Api\PlantController@PlantCo2Reduction');
Route::get('total-current-power', 'Api\PlantController@PlantTotalCurrentPower');
Route::get('total-generation', 'Api\PlantController@PlantTotalGeneration');
Route::get('plant-co2-reduction/{id}', 'Api\PlantController@singlePlantCo2Reduction');
Route::get('plant-total-current-power/{id}', 'Api\PlantController@singlePlantTotalCurrentPower');
Route::get('plant-total-generation/{id}', 'Api\PlantController@singlePlantTotalGeneration');
Route::post('get-saltec-data', 'Api\ComplainController@transferData');


//Station Routes

