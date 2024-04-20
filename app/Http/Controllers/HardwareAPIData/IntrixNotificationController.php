<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Models\IntrixBanner;
use App\Http\Models\NotificationManagement;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Models\SiteInverterDetail;
use Cassandra\Date;
use Illuminate\Http\Request;
use App\Http\Models\Plant;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\Inverter;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Notification;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\GenerationLog;
use App\Http\Models\MicrotechEnergyGenerationLog;
use App\Http\Models\MicrotechPowerGenerationLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\PlantSite;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\SystemType;
use App\Http\Models\Setting;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotificationController;
use Carbon\Carbon;
use App\Http\Controllers\LEDController;
use App\Http\Controllers\Admin\CommunicationController;

class IntrixNotificationController extends Controller
{

    public function AutoGenerate(){
        $this->RegularCleaning();
        $this->AchieveSolarGeneration();
        return "Notification Generated Successfully";
    }
    public function RegularCleaning(){

        $isSendNoti = NotificationManagement::where('type', 'Regular Cleaning')->where('send_app_noti', 'Y')->first();

        if($isSendNoti){
            $notiDate = $isSendNoti->app_schedule_date;

            if(Date("d",strtotime($notiDate)) == Date("d")){
                $allPlants = Plant::where('meter_type',"Solis")->get();

                foreach ($allPlants as $keu => $plant){

                    $CleaningNoti = IntrixBanner::firstOrCreate([
                        'plant_id'   => $plant->id,
                        'schedule_date'   => $notiDate,
                        'type'   => "reminder",
                    ],[
                        'plant_id'    =>  $plant->id,
                        'type'        =>  'reminder',
                        'title'       =>  $isSendNoti->mobile_app_title,
                        'description' => $isSendNoti->mobile_app_description,
                        'read_status' => 'N',
                        'schedule_date'  => $notiDate,
                    ]);
                }
            }
        }
    }

    public function AchieveSolarGeneration(){

        $isSendNoti = NotificationManagement::where('type', 'Acheive Solar Generation')->where('send_app_noti', 'Y')->first();

        if($isSendNoti){
            $allPlants = Plant::where('meter_type',"Solis")->get();
            foreach ($allPlants as $key => $plant){

                $plant_id = $plant->id;
                $date = Date('Y-m-d');
                $yearlyGene = YearlyProcessedPlantDetail::where('plant_id', $plant_id)->whereYear('created_at', $date)->sum('yearlyGeneration');

                $arr_sum = [];

                for ($i = 1; $i <= 12; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $dd = cal_days_in_month(CAL_GREGORIAN, $i, (int)$date);

                        for ($j = 1; $j <= $dd; $j++) {

                            if ($j < 10) {
                                $j = '0' . $j;
                            }

                            $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $plant_id)->where('created_at', '<=', Date('Y') . '-' . $i . '-' . $j . ' 23:59:59')->orderBy('created_at', 'DESC')->first();
                            $arr_sum[] = $yesterday_log_data_sum ? ($yesterday_log_data_sum->daily_expected_generation) : 0;
                        }
                }


                $ExpGene = array_sum($arr_sum);

                if($yearlyGene >= $ExpGene){

                    $achieveGeneration = IntrixBanner::firstOrCreate([
                        'plant_id'   => $plant->id,
                        'type'   => "success",
                    ],[
                        'plant_id'    =>  $plant->id,
                        'type'        =>  'success',
                        'title'       =>  $isSendNoti->mobile_app_title,
                        'description' => $isSendNoti->mobile_app_description,
                        'read_status' => 'N',
                        'schedule_date'  => Date('Y-m-d'),
                    ]);

                }
            }
        }
    }
}
