<?php



use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\PlantsController;


/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/


Route::get('/dump', function()
{
    exec('composer dump-autoload');
});
Route::get('/pdf-library', function()
{
    exec('composer require barryvdh/laravel-dompdf');
});
Route::get('/cache-clear', function()
{
    exec('config:cache');
});
Route::get('/composer-update', function()
{
    exec('composer update');
});
Route::get('/optimize-clear', function () {

    \Artisan::call('optimize:clear');
});


Auth::routes();
Route::get('/dashboardSystem', function() {

return view('system_dashboard');
});

Route::get('/daily-data', 'Admin\CompanyController@UpdatedData');

Route::get('/reset-password/{id}', 'Api\ForgotPasswordController@validateEmailToken');

Route::post('/change-user-password', 'Api\ForgotPasswordController@changePassword');

Route::post('/generateForgotPassCode', 'Api\ForgotPasswordController@generateForgotPassCode');

Route::get('/term-and-condition', 'HomeController@term_and_condition');

Route::get('/privacy-policy', 'HomeController@privacy_policy');

Route::get('/led-update-variable', 'LEDController@updateVariable')->name('led.update.variable');

Route::get('send-app-notification', 'Admin\CommunicationController@send_comm_app_notification')->name('admin.communication.send-app-notification');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'HomeController@index');

    Route::get('/home', 'HomeController@index')->name('home');



    Route::prefix('admin')->group(function () {
        Route::get('/plant/{type}/{plantId}/inverter/{id}/detail', 'Admin\PlantsController@plantInverterDetail')->name('admin.plant.inverter.detail');

        Route::post('inverter/mppt/number', 'Admin\PlantsController@plantInverterMPPTNumber')->name('admin.inverter.mppt.number');
        Route::get('plant/energy/flow/data', 'Admin\PlantsController@plantEnergyFlowData')->name('admin.plant.energy.flow.data');
        Route::get('/plant/history/graph', 'Admin\PlantsController@plantBelHistoryGraph')->name('admin.plant.history.graph');
        Route::get('/plant-inverter-detail/{id}', 'Admin\PlantsController@plantInverterDetails');
        Route::get('/plant-battery-detail/{id}', 'Admin\PlantsController@plantBatteryDetails')->name('admin.plant.battery.detail');
        Route::post('/energy-analytical-report', 'Admin\PlantsController@energyAnalyticalReport')->name('energy.analytical.report');
        Route::get('/energy-analytical-options', 'Admin\PlantsController@AnalyticReportOption')->name('energy.analytical.options');
//        Route::get('/build-plant', 'Admin\PlantsController@buildPlant')->name('admin.build.plant')->middleware('not_company_user');

        //Graph Routes

        Route::prefix('graph')->group(function () {

            Route::get('plant/actual-expected', 'Admin\PlantsController@plantActualExpectedGraph')->name('admin.graph.plant.actual.expected');
            Route::get('plant/environmental-benefit', 'Admin\PlantsController@plantEnvironmentalBenefitsGraph')->name('admin.graph.plant.environmental.benefits');
            Route::get('plant/history', 'Admin\PlantsController@plantHistoryGraph')->name('admin.graph.plant.history');
            Route::get('plant/inverter/ajax', 'Admin\PlantsController@plantInverterGraphAjax')->name('admin.graph.plant.inverter.ajax');
            Route::get('plant/battery/ajax', 'Admin\PlantsController@plantBatteryGraphAjax')->name('admin.graph.plant.battery.ajax');
            Route::get('plant/weather', 'Admin\PlantsController@plantWeatherGraph')->name('admin.graph.plant.weather');
            Route::get('plant/consumption/peak-hours', 'Admin\PlantsController@plantConsumptionPeakHoursGraph')->name('admin.graph.consumption.peak.hours');
            Route::get('plant/solar/utilization', 'Admin\PlantsController@plantSolarUtilizationGraph')->name('admin.graph.solar.utilization.energy');
            Route::get('plant/grid/outages', 'Admin\PlantsController@gridOutagesVoltage')->name('admin.graph.outages_grid_voltages');
            Route::get('plant/energy/source', 'Admin\PlantsController@plantEnergySourceGraph')->name('admin.graph.solar.energy.sources');
            Route::get('plant/cost/saving/data', 'Admin\PlantsController@plantCostSavingData')->name('admin.graph.cost.savings');
			Route::get('plant/alert', 'Admin\PlantsController@plantAlertGraph')->name('admin.graph.plant.alert');
			Route::get('plant/inverter', 'Admin\PlantsController@plantInverterGraph')->name('admin.graph.plant.inverter');
			Route::get('plant/emi', 'Admin\PlantsController@plantEMIGraph')->name('admin.graph.plant.emi');
			Route::get('plant/pv', 'Admin\PlantsController@plantPVGraph')->name('admin.graph.plant.pv');
		});

        /******************* Settings *********************/

        //Role routes


        Route::get('/roles-&-permissions', 'Admin\Settings\RolesController@index');

        Route::get('/create-role', 'Admin\Settings\RolesController@create');

        Route::get('/delete-role/{id}', 'Admin\Settings\RolesController@destroy');

        Route::get('/edit-role/{id}', 'Admin\Settings\RolesController@edit');

        Route::post('/update-role/{id}', 'Admin\Settings\RolesController@update');

        Route::post('/store-role', 'Admin\Settings\RolesController@store');

        //ExportGraph Route

        //Route::get('/ExportInvertergraph', 'Admin\PlantsControllerCopy@exportinverterCsv')->name('export.inverter.graph');
        Route::get('/ExportInvertergraph', 'Admin\PlantsController@exportinverterCsv')->name('export.inverter.graph');
        Route::get('/ExportInverterHybridgraph', 'Admin\PlantsController@exportInverterHybridCsv')->name('export.inverter.hybrid.graph');
        Route::get('/export/inverter-battery-data', 'Admin\PlantsController@exportBatteryHybridCsv')->name('export.battery.hybrid.graph');
        Route::get('/user-plant-detail-copy/{id}', 'Admin\PlantsControllerCopy@userPlantDetailCopy')->name('admin.plant.details.copy');


        //Permission routes

        Route::get('/create-permission', 'Admin\Settings\PermissionsController@create');

        Route::get('/edit-permission/{id}', 'Admin\Settings\PermissionsController@edit');

        Route::get('/delete-permission/{id}', 'Admin\Settings\PermissionsController@destroy');

        Route::post('/update-permission/{id}', 'Admin\Settings\PermissionsController@update');

        Route::post('/store-permission', 'Admin\Settings\PermissionsController@store');

        Route::get('/permissions', 'Admin\Settings\PermissionsController@index');



        //Plants routes
        Route::get('/dashboard/energy/sources/graph', 'Admin\PlantsController@plantDashboardEnergySourceGraph')->name('admin.dashboard.solar.energy.sources');
        Route::get('/dashboard/energy/outages/graph', 'Admin\PlantsController@dashboardOutagesVoltage')->name('admin.dashboard.outages_grid_voltages');
        Route::get('/dashboard/consumption/peak-hours', 'Admin\PlantsController@dashboardConsumpitonPeakHours')->name('admin.dashboard.consumption.peak.hours');
        Route::get('/dashboard/specific-yield/graph', 'Admin\PlantsController@specificYieldGraph')->name('admin.dashboard.specific.yield.graph');
        Route::get('/dashboard/maximum-power-achieved/graph', 'Admin\PlantsController@maximumPowerAchievedGraph')->name('admin.dashboard.maximum.power.achieved.graph');
        Route::get('/dashboard/solar-energy/graph', 'Admin\PlantsController@solarEnergyGraph')->name('admin.dashboard.solar.energy.graph');
        Route::get('/dashboard/cost/savings', 'Admin\PlantsController@dashboardCostSavings')->name('admin.dashboard.cost_savings');
        Route::get('/dashboard/reporting/center', 'Admin\PlantsController@dashboardReportingCenter')->name('admin.dashboard.reporting.center');
        Route::get('/pdf', 'Admin\PlantsController@fun_pdf');
        Route::get('/dashboard', 'Admin\PlantsController@allPlants')->name('admin.dashboard')->middleware('not_company_user');
        Route::get('/user-dashboard', 'Admin\PlantsController@userDashboard')->name('user.dashboard')->middleware('company_user');

        Route::get('/dashboard/energy-ajax/graph', 'Admin\PlantsController@dashboardEnergyGraph')->name('admin.dashboard.energy.graph');
        Route::get('/dashboard/expected-generation/graph', 'Admin\PlantsController@dashboardExpectedGenerationGraph')->name('admin.dashboard.expected.generation.graph');
        Route::get('main/dashboard/expected-generation/graph', 'Admin\PlantsController@mainDashboardExpectedGenerationGraph')->name('admin.main.dashboard.expected.generation.graph');
        Route::get('/dashboard/alert/graph', 'Admin\PlantsController@dashboardAlertGraph')->name('admin.dashboard.alert.graph');
        Route::get('/dashboard/saving/graph', 'Admin\PlantsController@dashboardSavingGraph')->name('admin.dashboard.saving.graph');
        Route::get('/dashboard/env/graph', 'Admin\PlantsController@dashboardENVGraph')->name('admin.dashboard.env.graph');

        Route::get('/new-dashboard', 'HomeController@newDashboard')->name('new.dashboard');

        Route::get('/Plants', 'Admin\PlantsController@Plants')->name('admin.plants');
        Route::get('/plants/hybrid/report', 'Admin\PlantsController@hybridReport')->name('admin.plant.hybrid.report');
        Route::get('/Plants/BEL', 'Admin\PlantsController@PlantsBEL')->name('admin.bel.plants');
        Route::get('/Plants/Data/{type}', 'Admin\PlantsController@PlantsData')->name('admin.plants.data');
        Route::get('/plant/status-graph/data', 'Admin\PlantsController@plantStatusGraphs')->name('admin.plants.status.data');
        Route::get('/plant/power-graph/data', 'Admin\PlantsController@plantPowerGraphs')->name('admin.plants.power.data');
        Route::get('/plant/saving-graph/data', 'Admin\PlantsController@plantSavingGraphs')->name('admin.plants.saving.details');
        Route::get('/plant/energy-graph/data', 'Admin\PlantsController@plantEnergyFlowData')->name('admin.plants.energy.graph.details');

        Route::get('/plant/env/graph', 'Admin\PlantsController@plantENVGraph')->name('admin.plant.env.graph');
        Route::get('/plant/alert/graph', 'Admin\PlantsController@plantAlertGraph')->name('admin.plant.alert.graph');

        Route::get('/plant-ex/{type}', 'Admin\PlantsController@buildPlant')->name('admin.build.plant')->middleware('not_company_user');
        Route::get('/build-plant-lat-long', 'Admin\PlantsController@buildPlantLatLong')->name('admin.build.plant.getLatLong');
        Route::post('/get-site-ids', 'Admin\PlantsController@getSiteIDs')->name('admin.get.site.ids');
        Route::post('/get-site-inverters', 'Admin\PlantsController@getSiteInverters')->name('admin.get.site.inverters');

        Route::post('/store-plant', 'Admin\PlantsController@storePlant')->name('admin.store.plant');
        Route::post('/store/build-plant', 'Admin\PlantsController@storeBuildPlant')->name('admin.store.build.plant');




        Route::get('/plant-inverter-graphs/{msn}/{time}/{date}', 'Admin\PlantsController@plantInverterGraphs')->name('admin.plant.inverter.graphs');

        Route::get('/plant-profile/{id}', 'Admin\PlantsController@plantProfile');

        Route::get('/{type}/user-plant-detail/{id}', 'Admin\PlantsController@userPlantDetail')->name('admin.plant.details');
        Route::get('/dashboardSystem/{plantId}/{siteId}', 'Admin\BelPlantSiteDashboard@userPlantSiteDetail')->name('admin.plant.site.details');
        Route::get('/plant/real-time-data', 'Admin\PlantsController@plantRealTimeData')->name('plant.real.time.data');

        Route::get('/edit-plant/{type}/{id}', 'Admin\PlantsController@editPlant')->name('admin.edit.plant')->middleware('not_company_user');

        Route::post('/update-plant', 'Admin\PlantsController@updatePlant')->name('admin.update.plant');
        Route::post('/update-plant/bel', 'Admin\PlantsController@updateBelPlant')->name('admin.update.bel.plant');

        Route::get('/act_exp_gen/{id}', 'Admin\PlantsController@act_exp_gen');

        Route::get('/revenue/{id}', 'Admin\PlantsController@revenue');

        Route::get('/tree_planting/{id}', 'Admin\PlantsController@tree_planting');

        Route::get('/emission_reduction/{id}', 'Admin\PlantsController@emission_reduction');

        Route::get('/energy_bought_sell/{id}', 'Admin\PlantsController@energy_bought_sell');

        Route::get('/alert/{id}', 'Admin\PlantsController@alert');

        Route::get('/faults_and_warning/{id}', 'Admin\PlantsController@faults_and_warning');

        Route::get('/faults_and_warning_option/{id}', 'Admin\PlantsController@faults_and_warning_option');

        Route::post('/history', 'Admin\PlantsController@history');

        Route::get('/city/{id}', 'Admin\PlantsController@get_city');
		Route::get('plant/daily-generation/graph', 'SpeedoGraphController@getPlantGenerationData');
		Route::get('plant/daily-power/graph', 'SpeedoGraphController@getPlantDailyPower');
        Route::get('plant/detail/power/graph', 'Admin\PlantsController@plantsPowerGraph')->name('admin.plant.detail.power.graph');
        Route::get('plant/detail/generation-data/graph', 'Admin\PlantsController@plantsGenerationGraph')->name('admin.plant.detail.generation.graph');
        //Route::get('plant/daily-generation/graph', 'App\Http\Controllers\Admin\SpeedoGraphController@getPlantGenerationData');

        //Export CSV
        Route::get('/export-plant-data', 'Admin\PlantsController@exportPlantHistoryData')->name('export.plant.data');


        //Companies routes

        Route::get('/all-company', 'Admin\CompanyController@allcompanies')->name('admin.company.all')->middleware('super_admin');
        Route::get('/all-company/filter', 'Admin\CompanyController@allcompaniesfilter')->name('admin.company.all.filter')->middleware('super_admin');

        Route::post('/add-company', 'Admin\CompanyController@addcompany')->name('add.company')->middleware('super_admin');

        Route::post('/update-company', 'Admin\CompanyController@updatecompany')->name('update.company')->middleware('super_admin');;

        Route::post('/delete-company', 'Admin\CompanyController@deletecompany');
        Route::get('/get-company', 'Admin\CompanyController@getCompany');

        Route::group(['middleware' => ['super_company_admin']], function()
        {
            Route::get('/all-user', 'Admin\UserController@allusers')->name('admin.user.all');
            Route::get('all-user/company-plant', 'Admin\UserController@getUserCompanyPlant')->name('admin.user.company.plants');
        });


        //Users routes
        Route::post('/add-user', 'Admin\UserController@adduser')->name('add.user')->middleware('super_admin');

        Route::post('/update-user', 'Admin\UserController@updateuser')->name('update.user')->middleware('super_admin');

        Route::get('/get-users', 'Admin\UserController@getUsers')->name('update.user')->middleware('super_admin');

        Route::post('/delete-user', 'Admin\UserController@deleteuser')->name('delete.user')->middleware('super_admin');

        Route::get('/unblockUser/{id}', 'Admin\UserController@unblockUser');

        Route::get('/blockUser/{id}', 'Admin\UserController@blockUser');

        Route::get('/myAccount/{id}', 'Admin\UserController@my_account');

        Route::post('/update-profile', 'Admin\UserController@update_profile');

        Route::post('/update-password', 'Admin\UserController@update_password');
        Route::get('/plant-details', 'Admin\UserController@companyPlantDetails');



        //Inverter routes

        Route::get('/all-inverter', 'Admin\InverterController@allinverters');



        //Alert routes

        Route::get('/all-alerts', 'Admin\AlertCenter@allalerts')->name('admin.all.alerts');
        Route::get('/all-alerts/get-filter', 'Admin\AlertCenter@getAlertFilters')->name('admin.alerts.filter');
        Route::get('/fetch_data', 'Admin\AlertCenter@fetch_data');



        //Report routes

        Route::get('/all-reports', 'Admin\ReportCenter@allreports');

        Route::get('/allNotifications', 'Admin\NotificationController@allNotifications');

        //Communication

        Route::prefix('communication')->group(function () {

            Route::get('index', 'Admin\CommunicationController@index')->name('admin.communication.index');
            Route::post('email/store', 'Admin\CommunicationController@storeEmail')->name('admin.communication.email.store');
            Route::post('sms/store', 'Admin\CommunicationController@storeSMS')->name('admin.communication.sms.store');
            Route::post('app-notification/store', 'Admin\CommunicationController@storeAppNotification')->name('admin.communication.app-notification.store');
            Route::get('send-email', 'Admin\CommunicationController@send_comm_email')->name('admin.communication.send-email');
            Route::get('send-sms', 'Admin\CommunicationController@send_comm_sms')->name('admin.communication.send-sms');
            Route::get('send-app-notification', 'Admin\CommunicationController@send_comm_app_notification')->name('admin.communication.send-app-notification');
        });

        //Cron-job Time
        Route::get('/cronjob-history', 'Admin\SettingController@cronJobDetal');

        //Settings

        Route::get('/all-setting', 'Admin\SettingController@allSetting');

        Route::post('/update-setting', 'Admin\SettingController@updateSetting');

        Route::prefix('complain')->group(function () {

            Route::get('/complain-mgm-system', 'Admin\ComplainController@complain_mgm_system')->name('admin.complain.mgm.system');
            Route::get('/list-ticket', 'Admin\ComplainController@list_ticket')->name('admin.ticket.list');
            Route::get('/add-ticket', 'Admin\ComplainController@add_ticket')->name('admin.ticket.add');
            Route::get('/ticket-plant-detail', 'Admin\ComplainController@ticket_plant_detail')->name('admin.ticket.plant.details');
            Route::post('/store-ticket', 'Admin\ComplainController@store_ticket')->name('admin.ticket.store');
            Route::get('/view-edit-ticket/{id}', 'Admin\ComplainController@view_edit_ticket')->name('admin.view.edit.ticket');
            Route::post('/update-ticket/{id}', 'Admin\ComplainController@update_ticket')->name('admin.update-ticket');
            Route::get('/ticket/download/{name}', 'Admin\ComplainController@downloadAttachment')->name('admin.ticket.attachment.download');
            Route::get('/update-ticket-status/{id}', 'Admin\ComplainController@updateTicketStatus')->name('admin.ticket.status.update');
            Route::get('/re-open-ticket/{id}', 'Admin\ComplainController@reOpenTicket')->name('admin.ticket.re.open');
            Route::post('/update-ticket-feedback', 'Admin\ComplainController@updateTicketFeedback')->name('admin.ticket.feedback.update');
            Route::post('/update-ticket-feedback-ajax', 'Admin\ComplainController@updateTicketFeedbackAjax')->name('admin.ticket.feedback.update.ajax');
            Route::get('complain-mgm-system/priority/graph', 'Admin\ComplainController@ticketPriorityGraph')->name('admin.ticket.priority.graph');
            Route::get('complain-mgm-system/plantticket/graph', 'Admin\ComplainController@plantTicketGraph')->name('admin.plant.ticket.graph');
            Route::get('complain-mgm-system/status/graph', 'Admin\ComplainController@ticketStatusGraph')->name('admin.ticket.status.graph');
            Route::get('complain-mgm-system/medium/graph', 'Admin\ComplainController@ticketMediumGraph')->name('admin.ticket.medium.graph');
            Route::get('complain-mgm-system/approach/graph', 'Admin\ComplainController@ticketApproachGraph')->name('admin.ticket.approach.graph');

            Route::prefix('priority')->group(function () {

                Route::get('index', 'Admin\Complain\PriorityController@index')->name('admin.complain.priority.index');
                Route::post('store', 'Admin\Complain\PriorityController@store')->name('admin.complain.priority.store');
                Route::post('update', 'Admin\Complain\PriorityController@update')->name('admin.complain.priority.update');
                Route::post('delete', 'Admin\Complain\PriorityController@delete')->name('admin.complain.priority.delete');
            });

            Route::prefix('source')->group(function () {

                Route::get('index', 'Admin\Complain\SourceController@index')->name('admin.complain.source.index');
                Route::post('store', 'Admin\Complain\SourceController@store')->name('admin.complain.source.store');
                Route::post('update', 'Admin\Complain\SourceController@update')->name('admin.complain.source.update');
                Route::post('delete', 'Admin\Complain\SourceController@delete')->name('admin.complain.source.delete');
            });

            Route::prefix('category')->group(function () {

                Route::get('index', 'Admin\Complain\CategoryController@index')->name('admin.complain.category.index');
                Route::post('store', 'Admin\Complain\CategoryController@store')->name('admin.complain.category.store');
                Route::post('update', 'Admin\Complain\CategoryController@update')->name('admin.complain.category.update');
                Route::post('delete', 'Admin\Complain\CategoryController@delete')->name('admin.complain.category.delete');
            });

            Route::prefix('sub-category')->group(function () {

                Route::get('index', 'Admin\Complain\SubCategoryController@index')->name('admin.complain.sub-category.index');
                Route::post('store', 'Admin\Complain\SubCategoryController@store')->name('admin.complain.sub-category.store');
                Route::post('update', 'Admin\Complain\SubCategoryController@update')->name('admin.complain.sub-category.update');
                Route::post('delete', 'Admin\Complain\SubCategoryController@delete')->name('admin.complain.sub-category.delete');
            });

        });

        Route::get('/notification-setting', function() {

            return view('admin.notification.notificationSetting');
        });

    });
    Route::get('delete-data/{date}/{id}', 'Admin\DeleteDataController@deleteData');
    Route::get('delete-plant/{id}', 'Admin\DeletePlantDataController@DeletePlantData');

});

Route::get('update_variable', 'LEDController@updateVariable');
Route::get('get_weather', 'Admin\PlantsController@get_weather');


//-----------------------------------//
//     Danger Zone - Keep Away       //
//-----------------------------------//

Route::get('cron-job-controller', 'CronJobController@index')->name('cron.job.controller');

Route::prefix('hardware-api-data')->group(function () {
     Route::get('solisCloudPlantsDetail','HardwareAPIData\SolisCloud\SolisCloudController@solis');
     Route::get('solisCloudAutoBuildPlant','HardwareAPIData\SolisCloud\AutoBuildSolisCronJobController@AutoBuildPlant')->name('hardware.api.data.autobuild.solisCloud');
    Route::get('huawei', 'HardwareAPIData\HuaweiController@index')->name('hardware.api.data.huawei.index');
    Route::get('huawei-real-time', 'HardwareAPIData\HuaweiRealTimeController@index')->name('hardware.api.data.huawei.real.time.index');
    Route::get('huawei66', 'HardwareAPIData\Huawei66Controller@index')->name('hardware.api.data.huawei66.index');
    Route::get('sun-grow', 'HardwareAPIData\SunGrowController@sunGrow')->name('hardware.api.data.sungrow');
    Route::get('sun-grow-mppt', 'HardwareAPIData\SunGrowMPPTController@sunGrow')->name('hardware.api.data.sungrow.mppt');
    Route::get('solis', 'HardwareAPIData\SolisController@solis')->name('hardware.api.data.solis');
    Route::get('solis1', 'HardwareAPIData\SolisController1@solis')->name('hardware.api.data.solis1');
    Route::get('solis-real-time', 'HardwareAPIData\SolisRealTimeController@solis')->name('hardware.api.data.solis');
    Route::get('testing', 'HardwareAPIData\TestingController@index')->name('hardware.api.data.testing');
    Route::get('solis-grid-data', 'HardwareAPIData\SolisMeterController@meterData')->name('hardware.api.solis.meter.data');
     Route::get('updateBuildPlantDate','HardwareAPIData\SolisCloud\AutoBuildSolisCronJobController@updateBuildPlant');
    //Transfer Data to history table
    Route::get('transfer-data', 'HardwareAPIData\TransferHistoryDataController@transferData');
    Route::get('saltec-push', 'HardwareAPIData\FetchResponseFromSaltec@saltec');
    Route::get('saltec-fault-alarm', 'HardwareAPIData\FetchResponseFromSaltec@faultAndAlarm');
    Route::get('saltec-unbuild-sites', 'HardwareAPIData\FetchResponseFromSaltec@newSite');
    Route::get('saltec-trasnfer-data', 'HardwareAPIData\FetchResponseFromSaltec@transferDataSaltec');
    Route::get('saltec-plant-status', 'HardwareAPIData\SaltecPlantStatus@plantStatus');

    //Intrix Notification
    Route::get('intrix-noti', 'HardwareAPIData\IntrixNotificationController@AutoGenerate');

    //SolisCopiedController Route
    Route::get('soliscopied', 'HardwareAPIData\SolisCopiedController@solis')->name('hardware.api.data.solis.copied');
    // Route::get('sun-grow-fault-and-alarm', 'HardwareAPIData\SungrowFaultAndAlarmController@AlarmAndFault')->name('hardware.api.data.solis.fault');

});




Route::get('weather-data', 'Admin\PlantsController@get_weather');
//Route::get('weather-data', function() {
//
//    $weatherController = new PlantsController();
//    $weatherController->get_weather();
//});
