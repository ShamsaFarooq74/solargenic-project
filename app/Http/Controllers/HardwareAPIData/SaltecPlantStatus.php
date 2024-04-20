<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;
use App\Http\Models\CronJobTime;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\GenerationLog;
use App\Http\Models\Inverter;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\Plant;
use App\Http\Models\PlantSite;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Setting;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\Weather;
use App\Http\Models\SaltecPushData;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class SaltecPlantStatus extends Controller
{

    function plantStatus(Request $request){
        date_default_timezone_set('Asia/Karachi');
        $cronJobTime = new CronJobTime();
        $cronJobTime->start_time = Date('Y-m-d H:i:s');
        $cronJobTime->type = 'Saltec-Plant-Status';
        $cronJobTime->status = 'in-progress';
        $cronJobTime->save();
        $allPlants = Plant::whereIn('meter_type', ['Microtech', 'Saltec', 'Saltec-Goodwe', 'Microtech-Goodwe'])->get();
        foreach ($allPlants as $key => $plant){
            $plantSites = PlantSite::Where('plant_id',$plant->id)->get();
            foreach ($plantSites as $key1 => $plantSite) {   
                $Time  = Date("Y-m-d H:i:s", strtotime('-10 minutes'));
                $plantProccessedData = SaltecPushData::where('site_id',$plantSite->site_id)->where('collect_time', '>=', $Time)->first();
                if($plantProccessedData){
                    $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plant->id, 'site_id' => $plantSite->site_id])->update(['online_status' => 'Y']);
                }else{
                    $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plant->id, 'site_id' => $plantSite->site_id])->update(['online_status' => 'N']);
                }
            }
            $plantSites = PlantSite::Where('plant_id',$plant->id)->get('online_status');
            if(count($plantSites) > 0){
                if($plantSites->contains('online_status', 'Y') && $plantSites->contains('online_status', 'N')){
                    $updatePlantStatus['is_online'] = 'P_Y';
                }else{
                    $updatePlantStatus['is_online'] = $plantSites[0]['online_status'];
                }

            }else{
                $updatePlantStatus['is_online'] = $plantSites[0]['online_status'];
            }

            $plantRes = Plant::where('id', $plant->id)->update($updatePlantStatus);

        }
        $CronJobDetail = CronJobTime::where('id',$cronJobTime->id)->first();
        $CronJobDetail->end_time = date('Y-m-d H:i:s');
        $CronJobDetail->status = 'completed';
        $CronJobDetail->save();
    }

}
