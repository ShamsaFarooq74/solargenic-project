<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;
use App\Http\Models\CronJobTime;
use App\Http\Models\StationBattery;
use Illuminate\Http\Request;
use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterDetailHistory;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\Inverter;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Notification;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\DailyProcessedPlantEMIDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantEMIDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantEMIDetail;
use App\Http\Models\TotalProcessedPlantEMIDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\InverterEnergyLog;
use App\Http\Models\InverterEMIDetail;
use App\Http\Models\InverterGridMeterDetail;
use App\Http\Models\SiteInverterDetail;
use App\Http\Models\GenerationLog;
use App\Http\Models\MicrotechEnergyGenerationLog;
use App\Http\Models\MicrotechPowerGenerationLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\PlantType;
use App\Http\Models\PlantSite;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\SystemType;
use App\Http\Models\Setting;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\InverterStateDescription;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotificationController;
use Carbon\Carbon;
use App\Http\Controllers\LEDController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\PlantsController;

class TransferHistoryDataController extends Controller
{

    function transferData(){
        date_default_timezone_set('Asia/Karachi');
        $currentDate = date('Y-m-d');
        $Date = date('Y-m-d', strtotime('-2 day'));

        $cronJobTime = new CronJobTime();
        $cronJobTime->start_time = Date('Y-m-d H:i:s');
        $cronJobTime->type = 'Transfer-Data';
        $cronJobTime->status = 'in-progress';
        $cronJobTime->save();

        $inveterDetailQuery = DB::statement( "INSERT INTO `inverter_detail_history`(`id`, `plant_id`, `siteId`, `dv_inverter`, `serial_no`, `daily_generation`, `daily_consumption`, `monthly_generation`, `inverterPower`, `totalInverterPower`, `inverterEnergy`, `inverterLimitValue`, `inverterCommFail`, `inverterConfigFail`, `inverterUptime`, `numberOfInverters`, `inverterEfficieny`, `inverterTemperature`, `inverterState`, `inverterStateCode`, `mpptPower`, `frequency`, `start_time`, `phase_voltage_r`, `phase_voltage_s`, `phase_voltage_t`, `phase_current_r`, `phase_current_s`, `phase_current_t`, `inverter_cron_job_id`, `battery_capacity`, `battery_power`, `battery_type`, `total_grid_voltage`, `current_consumption`, `consumption_voltage`, `consumption_frequency`, `consumption_active_power_r`, `total_consumption_energy`, `inverter_output_voltage`, `ac_power_r_u_a`, `total_production`, `total_consumption`, `output_power_l1`, `output_power_l2`, `output_power_l3`, `load_voltage_l1`, `load_voltage_l2`, `load_voltage_l3`, `load_voltage_ln`, `total_output_power`, `inverter_output_power_ln`, `Gene_Input_Load_Enable`, `battery_temperature`, `dc_temperature`, `consump_apparent_power`, `load_frequency`, `lastUpdated`, `collect_time`, `created_at`, `updated_at`)  SELECT `id`, `plant_id`, `siteId`, `dv_inverter`, `serial_no`, `daily_generation`, `daily_consumption`, `monthly_generation`, `inverterPower`, `totalInverterPower`, `inverterEnergy`, `inverterLimitValue`, `inverterCommFail`, `inverterConfigFail`, `inverterUptime`, `numberOfInverters`, `inverterEfficieny`, `inverterTemperature`, `inverterState`, `inverterStateCode`, `mpptPower`, `frequency`, `start_time`, `phase_voltage_r`, `phase_voltage_s`, `phase_voltage_t`, `phase_current_r`, `phase_current_s`, `phase_current_t`, `inverter_cron_job_id`, `battery_capacity`, `battery_power`, `battery_type`, `total_grid_voltage`, `current_consumption`, `consumption_voltage`, `consumption_frequency`, `consumption_active_power_r`, `total_consumption_energy`, `inverter_output_voltage`, `ac_power_r_u_a`, `total_production`, `total_consumption`, `output_power_l1`, `output_power_l2`, `output_power_l3`, `load_voltage_l1`, `load_voltage_l2`, `load_voltage_l3`, `load_voltage_ln`, `total_output_power`, `inverter_output_power_ln`, `Gene_Input_Load_Enable`, `battery_temperature`, `dc_temperature`, `consump_apparent_power`, `load_frequency`, `lastUpdated`, `collect_time`, `created_at`, `updated_at` FROM inverter_details WHERE date(collect_time) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if($inveterDetailQuery == true){
            InverterDetail::whereDate('collect_time','<',$Date)->delete();
        }

       $processedCurrentQuery =  DB::statement("INSERT INTO `processed_current_variable_history`(`id`, `plant_id`, `current_generation`, `current_consumption`, `current_grid`, `grid_type`, `totalEnergy`, `current_saving`, `current_irradiance`, `battery_capacity`, `battery_power`, `battery_type`, `total_charge_energy`, `total_discharge_energy`, `battery_charge`, `battery_discharge`, `comm_failed`, `processed_cron_job_id`, `collect_time`, `created_at`, `updated_at`) SELECT `id`, `plant_id`, `current_generation`, `current_consumption`, `current_grid`, `grid_type`, `totalEnergy`, `current_saving`, `current_irradiance`, `battery_capacity`, `battery_power`, `battery_type`, `total_charge_energy`, `total_discharge_energy`, `battery_charge`, `battery_discharge`, `comm_failed`, `processed_cron_job_id`, `collect_time`, `created_at`, `updated_at` FROM processed_current_variables WHERE date(collect_time) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if($processedCurrentQuery == true){
            ProcessedCurrentVariable::whereDate('collect_time','<',$Date)->delete();
        }

        $processedPlantQuery =  DB::statement("INSERT INTO `processed_plant_detail_history`(`id`, `plant_id`, `siteId`, `dailyGeneration`, `monthlyGeneration`, `yearlyGeneration`, `dailyConsumption`, `monthlyConsumption`, `yearlyConsumption`, `dailyGridPower`, `monthlyGridPower`, `yearlyGridPower`, `dailyBoughtEnergy`, `monthlyBoughtEnergy`, `yearlyBoughtEnergy`, `dailySellEnergy`, `monthlySellEnergy`, `yearlySellEnergy`, `dailyMaxSolarPower`, `monthlyMaxSolarPower`, `yearlyMaxSolarPower`, `cron_job_id`, `lastUpdated`, `created_at`, `updated_at`) SELECT `id`, `plant_id`, `siteId`, `dailyGeneration`, `monthlyGeneration`, `yearlyGeneration`, `dailyConsumption`, `monthlyConsumption`, `yearlyConsumption`, `dailyGridPower`, `monthlyGridPower`, `yearlyGridPower`, `dailyBoughtEnergy`, `monthlyBoughtEnergy`, `yearlyBoughtEnergy`, `dailySellEnergy`, `monthlySellEnergy`, `yearlySellEnergy`, `dailyMaxSolarPower`, `monthlyMaxSolarPower`, `yearlyMaxSolarPower`, `cron_job_id`, `lastUpdated`, `created_at`, `updated_at` FROM processed_plant_detail WHERE date(created_at) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if($processedPlantQuery == true){
            ProcessedPlantDetail::whereDate('created_at','<',$Date)->delete();
        }

        $inverterMpptQuery = DB::statement("INSERT INTO `inverter_mppt_detail_history`(`id`, `plant_id`, `site_id`, `dv_inverter`, `mppt_number`, `mppt_voltage`, `mppt_current`, `mppt_power`, `collect_time`, `created_at`, `updated_at`) SELECT `id`, `plant_id`, `site_id`, `dv_inverter`, `mppt_number`, `mppt_voltage`, `mppt_current`, `mppt_power`, `collect_time`, `created_at`, `updated_at` FROM inverter_mppt_detail  WHERE date(collect_time) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if($inverterMpptQuery == true){
            InverterMPPTDetail::whereDate('collect_time','<',$Date)->delete();
        }

        $microtechEnergyGeneQuery = DB::statement("INSERT INTO `microtech_energy_generation_log_history`(`id`, `meter_serial_no`, `active_energy_pos_tl`, `active_energy_neg_tl`, `active_energy_abs_tl`, `reactive_energy_pos_tl`, `reactive_energy_neg_tl`, `reactive_energy_abs_tl`, `active_mdi_pos_tl`, `active_mdi_neg_tl`, `active_mdi_abs_tl`, `cumulative_mdi_pos_tl`, `cumulative_mdi_neg_tl`, `cumulative_mdi_abs_tl`, `meter_datetime`, `mdc_read_datetime`, `db_datetime`, `created_at`, `updated_at`) SELECT `id`, `meter_serial_no`, `active_energy_pos_tl`, `active_energy_neg_tl`, `active_energy_abs_tl`, `reactive_energy_pos_tl`, `reactive_energy_neg_tl`, `reactive_energy_abs_tl`, `active_mdi_pos_tl`, `active_mdi_neg_tl`, `active_mdi_abs_tl`, `cumulative_mdi_pos_tl`, `cumulative_mdi_neg_tl`, `cumulative_mdi_abs_tl`, `meter_datetime`, `mdc_read_datetime`, `db_datetime`, `created_at`, `updated_at` FROM microtech_energy_generation_log  WHERE date(mdc_read_datetime) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if($microtechEnergyGeneQuery == true){
            MicrotechEnergyGenerationLog::whereDate('mdc_read_datetime','<',$Date)->delete();
        }

        $microtechPowerGeneQuery = DB::statement("INSERT INTO `microtech_power_generation_log_history`(`id`, `meter_serial_no`, `current_tariff_register`, `signal_strength`, `frequency`, `meter_datetime`, `current_phase_a`, `current_phase_b`, `current_phase_c`, `voltage_phase_a`, `voltage_phase_b`, `voltage_phase_c`, `aggregate_active_pwr_pos`, `aggregate_active_pwr_neg`, `aggregate_reactive_pwr_pos`, `aggregate_reactive_pwr_neg`, `average_pf`, `mdc_read_datetime`, `db_datetime`, `created_at`, `updated_at`) SELECT `id`, `meter_serial_no`, `current_tariff_register`, `signal_strength`, `frequency`, `meter_datetime`, `current_phase_a`, `current_phase_b`, `current_phase_c`, `voltage_phase_a`, `voltage_phase_b`, `voltage_phase_c`, `aggregate_active_pwr_pos`, `aggregate_active_pwr_neg`, `aggregate_reactive_pwr_pos`, `aggregate_reactive_pwr_neg`, `average_pf`, `mdc_read_datetime`, `db_datetime`, `created_at`, `updated_at` FROM microtech_power_generation_log  WHERE date(mdc_read_datetime) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if($microtechPowerGeneQuery == true){
            MicrotechPowerGenerationLog::whereDate('mdc_read_datetime','<',$Date)->delete();
        }
        $stationBatteryQuery = DB::statement("INSERT INTO `station_battery_history`(`id`, `plant_id`, `site_id`, `battery_type`, `battery_power`, `battery_capacity`, `total_charge_energy`, `total_discharge_energy`, `dv_inverter`, `collect_time`, `daily_charge_energy`, `daily_discharge_energy`, `battery_voltage`, `inverter_real_time_consumption`, `rated_power`, `battery_temperature`, `battery_status`, `battery_current`, `battery_type_data`, `battery_charging_voltage`, `battery_bms_current`, `battery_bms_voltage`, `battery_bms_current_limiting_charging`, `battery_bms_temperature`, `battery_bms_current_limiting_discharging`, `battery_bms_soc`, `bms_discharge_voltage`, `created_at`, `updated_at`) SELECT `id`, `plant_id`, `site_id`, `battery_type`, `battery_power`, `battery_capacity`, `total_charge_energy`, `total_discharge_energy`, `dv_inverter`, `collect_time`, `daily_charge_energy`, `daily_discharge_energy`, `battery_voltage`, `inverter_real_time_consumption`, `rated_power`, `battery_temperature`, `battery_status`, `battery_current`, `battery_type_data`, `battery_charging_voltage`, `battery_bms_current`, `battery_bms_voltage`, `battery_bms_current_limiting_charging`, `battery_bms_temperature`, `battery_bms_current_limiting_discharging`, `battery_bms_soc`, `bms_discharge_voltage`, `created_at`, `updated_at` FROM station_battery  WHERE date(collect_time) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if($stationBatteryQuery == true){
            StationBattery::whereDate('collect_time','<',$Date)->delete();
        }
        GenerationLog::where('collect_time','<',$Date)->delete();
        $CronJobDetail = CronJobTime::where('id',$cronJobTime->id)->first();
        $CronJobDetail->end_time = date('Y-m-d H:i:s');
        $CronJobDetail->status = 'completed';
        $CronJobDetail->save();
        return "All Data Transfer Successfully";
    }
    function MicrotechDailyConsumption(){
        $plants = Plant::whereIn('meter_type', ['Microtech','Microtech-Goodwe'])->get();

        foreach($plants as $key => $plant){

            if($plant->meter_type == "Microtech-Goodwe"){
                $sum_Dailyproccessed_generation_Goodwe = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', '2022-05-10')->sum('dailyGeneration');
                $dailyGenerationVar = $sum_Dailyproccessed_generation_Goodwe;
            }
            $dailyGenerationVar = DailyInverterDetail::where('plant_id', $plant->id)->whereDate('created_at', '2022-05-10')->sum('daily_generation');

            $latest_micro_daily_record = DB::table('microtech_energy_generation_log_history')->where('meter_serial_no', $plant->meter_serial_no)->whereDate('db_datetime', '2022-05-10')->orderBy('db_datetime', 'DESC')->first();
            $previous_micro_daily_record = DB::table('microtech_energy_generation_log_history')->where('meter_serial_no', $plant->meter_serial_no)->whereDate('db_datetime', '2022-05-09')->orderBy('db_datetime', 'DESC')->first();

            $latest_energy_pos_tl_data = $latest_micro_daily_record ? $latest_micro_daily_record->active_energy_pos_tl : 0;
            $latest_energy_neg_tl_data = $latest_micro_daily_record ? $latest_micro_daily_record->active_energy_neg_tl : 0;
            $previous_energy_pos_tl_data = $previous_micro_daily_record ? $previous_micro_daily_record->active_energy_pos_tl : 0;
            $previous_energy_neg_tl_data = $previous_micro_daily_record ? $previous_micro_daily_record->active_energy_neg_tl : 0;

            $daily_active_energy_pos_tl = $latest_energy_pos_tl_data - $previous_energy_pos_tl_data > 0 ? $latest_energy_pos_tl_data - $previous_energy_pos_tl_data : 0;
            $daily_active_energy_neg_tl = $latest_energy_neg_tl_data - $previous_energy_neg_tl_data > 0 ? $latest_energy_neg_tl_data - $previous_energy_neg_tl_data : 0;
            //dd($daily_active_energy_pos_tl, $daily_active_energy_neg_tl);
            $dailyBoughtEnergyVar = $daily_active_energy_pos_tl * $plant->ratio_factor;
            $dailySellEnergyVar = $daily_active_energy_neg_tl * $plant->ratio_factor;
            $dailyGridVar = $dailyBoughtEnergyVar > $dailySellEnergyVar ? $dailyBoughtEnergyVar - $dailySellEnergyVar : $dailySellEnergyVar - $dailyBoughtEnergyVar;
            $dailyConsumptionVar = ($dailyGenerationVar + ($dailyBoughtEnergyVar - $dailySellEnergyVar)) > 0 ? ($dailyGenerationVar + ($dailyBoughtEnergyVar - $dailySellEnergyVar)) : 0;

            $daily_processed = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', '=','2022-05-10')->orderBy('created_at', 'DESC')->first();
            $daily_processed['dailyConsumption'] = $dailyConsumptionVar;
            $daily_processed['dailyGridPower'] = $dailyGridVar;
            $daily_processed['dailyBoughtEnergy'] = $dailyBoughtEnergyVar >= 0 ? $dailyBoughtEnergyVar : 0;
            $daily_processed['dailySellEnergy'] = $dailySellEnergyVar >= 0 ? $dailySellEnergyVar : 0;
            $daily_processed->save();

        }
    }
}
