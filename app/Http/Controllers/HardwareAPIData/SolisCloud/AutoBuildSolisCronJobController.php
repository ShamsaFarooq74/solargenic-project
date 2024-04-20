<?php

namespace App\Http\Controllers\HardwareAPIData\SolisCloud;
use App\Http\Controllers\HardwareAPIData\SungrowFaultAndAlarmController;
use App\Http\Models\InverterStatusCode;
use App\Http\Models\NotificationEmail;
use App\Http\Models\NotificationManagement;
use App\Http\Models\PopupNotificationDetail;
use App\Http\Models\User;
use App\Http\Models\UserCompany;
use App\Http\Traits\emailFormat;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use App\Http\Models\SiteInverterDetail;
use Cassandra\Date;
use App\Http\Models\Plant;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\Inverter;
use App\Http\Models\CronJobTime;
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
use Illuminate\Support\Str;
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
use App\Http\Models\InverterEnergyLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoBuildSolisCronJobController extends Controller
{
         public $keySecret;
         public $baseUrl;
         public $key;
         public function __construct(){
            $this->keySecret = Setting::where('perimeter', 'solis_cloud_secret_key')->value('value');
            $this->baseUrl = Setting::where('perimeter', 'solis_cloud_api_url')->value('value');
            $this->key = Setting::where('perimeter', 'solis_cloud_api_key')->value('value');
        
         }

         public function AutoBuildPlant(){ 
           date_default_timezone_set('Asia/Karachi');
            $currentTime = date('Y-m-d H:i:s');
            $cronJobTime = new CronJobTime();
            $cronJobTime->start_time = $currentTime;
            $cronJobTime->status = "in-progress";
            $cronJobTime->type = 'Solis-Cloud Auto Build';
            $defaultCompanyId = Setting::where('perimeter', 'default_company')->value('value');
            $ExpectedDefault = Setting::where('perimeter', 'expect_generation_1Kw')->value('value');
            $CreatedByID = Setting::where('perimeter', 'plant_auto_build_id')->value('value');
            $cronJobTime->save();
               $plantsArray = Plant::whereIn('meter_type', ['Solis-Cloud'])->pluck('id')->toArray();
               $plantOccupiedSitesArray = PlantSite::whereIn('plant_id', $plantsArray)->pluck('site_id')->toArray();
               $pageNo = '1';
               $mergedArray = [];
               $planAlltList = $this->getPlantList($pageNo);
               $plantList = json_decode($planAlltList);
              
            if(isset($plantList->data)){
               $pageIndex = ceil($plantList->data->stationStatusVo->all/100);
               for($i=1 ; $i<= $pageIndex; $i++) {
                  $pageNo=$i; $planAlltList=$this->getPlantList($pageNo);
                  $plantList = json_decode($planAlltList);
                  // return json_encode($plantList);
                  $plantListResponse = $plantList->data->page->records;
                     foreach ($plantListResponse as $key => $site) {
                        if (in_array($site->id, $plantOccupiedSitesArray)) {
                        unset($plantListResponse[$key]);
                        }
                     }
                     $mergedArray = array_merge($mergedArray, $plantListResponse);
               }
                $plantListResponse = $mergedArray;
                if(count($plantListResponse) == 0){
                  return 'All plants Already Build!';
                }
                 $plantListResponse = array_values($plantListResponse);

                foreach ($plantListResponse as $key1 => $plantListFinalResponse) {
                try {
                 
                  DB::beginTransaction();
                  if(isset($plantListFinalResponse->createDateStr)){
                       $dateString = $plantListFinalResponse->createDate;
                       $milliseconds = substr($dateString, 0, 10);
                       $createDate = date("Y-m-d",$milliseconds);
                  }
                  $city = $plantListFinalResponse->cityStr ?? "";
                  $region = $plantListFinalResponse->regionStr ?? "";
                  $country = $plantListFinalResponse->countryStr ?? "";
                  $loaction = $plantListFinalResponse->countyStr ?? "".", ".$city.", ".$region.", ".$country;
                  $plant = new Plant();
                  $plant->company_id = $defaultCompanyId;
                  $plant->plant_name = $plantListFinalResponse->stationName;
                  $plant->timezone = $plantListFinalResponse->timeZoneName;
                  $plant->currency = $plantListFinalResponse->money;
                  $plant->azimuth = isset($plantListFinalResponse->azimuth) ? $plantListFinalResponse->azimuth:" " ;
                  $plant->angle = isset($plantListFinalResponse->dip)? $plantListFinalResponse->dip:" ";
                  $plant->location = $loaction;
                  $plant->city = $city;
                  $plant->province = $region;
                  $plant->capacity = $plantListFinalResponse->capacity;
                  $plant->benchmark_price = $plantListFinalResponse->price;
                  $plant->plant_type = 1;
                  if($plantListFinalResponse->stationTypeNew == 4){
                        $systemType = 2;
                  }else if($plantListFinalResponse->stationTypeNew == 1){
                           $systemType = 4;
                  }else if($plantListFinalResponse->stationTypeNew == 0){
                           $systemType = 1;
                  }
               
                     $plantDailyExpectedGene = $plantListFinalResponse->capacity * $ExpectedDefault;
                     $plant->system_type = $systemType;
                     $plant->expected_generation = $plantDailyExpectedGene;
                     // $plant->daily_expected_saving = $plantDailyExpectedGene * $plantListFinalResponse->price;
                     $plant->meter_type = 'Solis-Cloud';
                     $plant->build_date = $createDate;
                     $remark1 = $plantListFinalResponse->remark1 ?? " ";
                     $remark2 = $plantListFinalResponse->remark2 ?? " ";
                     $remark3 = $plantListFinalResponse->remark3 ?? " ";
                     $plant->description = $remark1." ".$remark2." ".$remark3;
                     $plant->data_collect_date = $createDate;
                  if ($systemType == 2) {
                        $plant->plant_has_grid_meter = 'Y';
                  } elseif ($systemType == 1) {
                         $plant->plant_has_grid_meter = 'N';
                  }
                 
                  $plant->created_by = $CreatedByID;
                  $plant->yearly_expected_generation = $plantDailyExpectedGene * 365;
                  $plant->plant_pic = "";
                  $plant->save();

                //PlantSite
                     $plantSite = new PlantSite();
                     $plantSite['plant_id'] = $plant->id;
                     $plantSite['site_id'] = $plantListFinalResponse->id;
                     $plantSite['online_status'] = 'Y';
                     $plantSite['created_by'] = $CreatedByID;
                     $plantSite->save();

                //Create Build Plant Notification
                $Noc_users = User::whereIn('roles', ['1', '2'])->get();

                if ($Noc_users) {
                     $checkNoti = NotificationManagement::where('type', 'Build Plant Notification')->first();
                if ($checkNoti->send_app_noti == "Y") {

                  foreach ($Noc_users as $key => $usr) {
                        $patterns = [
                        '/\{(user_name)}]?/',
                        '/\{(plant_name)}]?/',
                        '/\{(user_email)}]?/',
                        ];

                     $replacements = [
                           $plantListFinalResponse->stationName,
                           $plantListFinalResponse->stationName,
                           isset($plantListFinalResponse->email) ? $plantListFinalResponse->email : ""
                     ];
                     $title = preg_replace($patterns, $replacements, $checkNoti->mobile_app_title);
                     $Description = preg_replace($patterns, $replacements, $checkNoti->mobile_app_description);

                     $noti_app = new Notification();
                  }
                }
                }
                //Expected Generation Log
                $expected_generation['plant_id'] = $plant->id;
                $expected_generation['daily_expected_generation'] = $plantDailyExpectedGene;
                $expected_generation['created_at'] = date('Y-m-d H:i:s');
                $expected_generation['updated_at'] = date('Y-m-d H:i:s');

                $expected_generation_exist = ExpectedGenerationLog::where('plant_id',
                $plant->id)->whereDate('created_at', date('Y-m-d'))->first();

                if ($expected_generation_exist) {

                     $expected_generation_log = $expected_generation_exist->fill($expected_generation)->save();
                } else {

                      $expected_generation_log = ExpectedGenerationLog::create($expected_generation);
                }
                   DB::commit();
                } catch (\Exception $e) {
                  DB::rollback();
                  return "Something Went Wrong : ".$e->getMessage();
                }
                }
                  $CronJobDetail = CronJobTime::where('id',$cronJobTime->id)->first();
                  $CronJobDetail->end_time = date('Y-m-d H:i:s');
                  $CronJobDetail->status = 'completed';
                  $CronJobDetail->save();
              return "All Plants Build Successfully";
            }else{
             return 'Error: plant list Null.';
            }
         }
   

        

              public function getPlantList($pageNo) {
                     $data['pageNo'] = $pageNo;
                     $data['pageSize'] = '100';
                     $path = '/v1/api/userStationList';
                     $body = json_encode($data);
                     $contentMd5 = base64_encode(md5($body, true));
                     $date = gmdate('D, d M Y H:i:s \G\M\T');
                     $param = "POST\n{$contentMd5}\napplication/json\n{$date}\n{$path}";
                     $sign = $this->sha1Encrypt($param, $this->keySecret);
                     $auth = "API {$this->key}:{$sign}";
                     $Content_type = 'application/json;charset=UTF-8';
                     $apiPath = $this->baseUrl.$path;
                     $curl = curl_init();
                     curl_setopt_array($curl, array(
                     CURLOPT_URL => $apiPath,
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_ENCODING => '',
                     CURLOPT_MAXREDIRS => 10,
                     CURLOPT_TIMEOUT => 0,
                     CURLOPT_FOLLOWLOCATION => true,
                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                     CURLOPT_CUSTOMREQUEST => 'POST',
                     CURLOPT_POSTFIELDS =>$body,
                     CURLOPT_HTTPHEADER => array(
                        'Authorization:'.$auth,
                        'Date:'.$date,
                        'Content-MD5:'.$contentMd5,
                        'Content-Type: application/json;charset=UTF-8',
                        'Cookie: aliyungf_tc=86e5f010ac48375aa18c16560ae66ab80ab2718d47bc970471c4995d7036ef9e'
                     ),
                  ));

                  $response = curl_exec($curl);
                  if ($response === false) {
                  $error = curl_error($curl);
                  return "cURL error: $error";
                  }
                  curl_close($curl);
                  return $response;
              }
              public function PlantBasicInfo($ps_id) {
                     $data['id'] = $ps_id;
                     $path = '/v1/api/stationDetail';
                     $body = json_encode($data);
                     $contentMd5 = base64_encode(md5($body, true));
                     $date = gmdate('D, d M Y H:i:s \G\M\T');
                     $param = "POST\n{$contentMd5}\napplication/json\n{$date}\n{$path}";
                     $sign = $this->sha1Encrypt($param, $this->keySecret);
                     $auth = "API {$this->key}:{$sign}";
                     $Content_type = 'application/json;charset=UTF-8';
                     $apiPath = $this->baseUrl.$path;
                     $curl = curl_init();
                     curl_setopt_array($curl, array(
                     CURLOPT_URL => $apiPath,
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_ENCODING => '',
                     CURLOPT_MAXREDIRS => 10,
                     CURLOPT_TIMEOUT => 0,
                     CURLOPT_FOLLOWLOCATION => true,
                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                     CURLOPT_CUSTOMREQUEST => 'POST',
                     CURLOPT_POSTFIELDS =>$body,
                     CURLOPT_HTTPHEADER => array(
                        'Authorization:'.$auth,
                        'Date:'.$date,
                        'Content-MD5:'.$contentMd5,
                        'Content-Type: application/json;charset=UTF-8',
                        'Cookie: aliyungf_tc=86e5f010ac48375aa18c16560ae66ab80ab2718d47bc970471c4995d7036ef9e'
                     ),
                  ));

                  $response = curl_exec($curl);
                  if ($response === false) {
                  $error = curl_error($curl);
                  return "cURL error: $error";
                  }
                  curl_close($curl);
                  return $response;
              }

            private function sha1Encrypt(string $encryptText, string $keySecret): string{
               return base64_encode(hash_hmac('sha1', $encryptText, $keySecret, true));
            }

   public function updateBuildPlant(){
      $plantsArray = Plant::where('meter_type', 'Solis-Cloud')->pluck('id')->toArray();
      $plantOccupiedSitesArray = PlantSite::whereIn('plant_id', $plantsArray)->pluck('site_id')->toArray();
      $mergedArray = [];
      $pageNo = '1';
      $planAlltList = $this->getPlantList($pageNo);
      $plantList = json_decode($planAlltList);
      $pageIndex = ceil($plantList->data->stationStatusVo->all/100);

      for($i = 1; $i <= $pageIndex; $i++) {
         $pageNo=$i;
         $planAlltList=$this->getPlantList($pageNo);
            $plantList = json_decode($planAlltList);
            $plantListResponse = $plantList->data->page->records;

            foreach ($plantListResponse as $key => $site) {
               $plantSite = PlantSite::where('site_id', $site->id)->first();
               $plant = Plant::where('id', $plantSite->plant_id)->first();
               $dateString = $site->createDate;
               $milliseconds = substr($dateString, 0, 10);
               $createDate = date("Y-m-d H:i:s", $milliseconds);

                  if($plant->build_date != $createDate){
                     $mergedArray[] = $site; 
                     $plant->build_date = $createDate;
                  }
                  if($plant->data_collect_date != $createDate){
                     $plant->data_collect_date = $createDate;
                     $plant->last_cron_job_date = $createDate;
                  }
                     $plant->save();
               }
            }

        return $mergedArray;
   }


   // public function updateBuildPlant(){
   //    $plantsArray = Plant::whereIn('meter_type', ['Solis-Cloud'])->get();
   //    $pageNo = '1';
   //    $planAlltList = $this->getPlantList($pageNo);
   //    $plantList = json_decode($planAlltList);
   //       if (isset($plantList->data)) {
   //          $pageIndex = ceil($plantList->data->stationStatusVo->all / 100);
   //          $plantIndex = 0;
   //          for ($i = 1; $i <= $pageIndex; $i++) {
   //             $pageNo=$i; $planAlltList=$this->getPlantList($pageNo);
   //             $plantList = json_decode($planAlltList);
   //             $plantListResponse = $plantList->data->page->records;
               
   //             foreach ($plantListResponse as $key1 => $plantListFinalResponse) {
   //                if (isset($plantListFinalResponse->createDateStr)) {
   //                      $dateString = $plantListFinalResponse->createDateStr;
   //                      $dateWithoutTimezone = strtok($dateString, '(');
   //                      $createDate = Carbon::createFromFormat('d/m/Y H:i:s', trim($dateWithoutTimezone));
   //                      $plant = $plantsArray[$plantIndex];
   //                      $plant->build_date = $createDate;
   //                      if($plant->data_collect_date != $createDate){
   //                         $plant->data_collect_date = $createDate;
   //                         $plant->last_cron_job_date = $createDate;
   //                      }
   //                      $plant->save();
   //                      $plantIndex++;
   //                   }
   //             }
   //             return "Plants Build-date Updated Successfully";
   //          }
   //       }
   // }


}



