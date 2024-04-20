<?php
namespace App\Http\Traits;
use App\Http\Models\Plant;
use App\Http\Models\Setting;
use App\Http\Models\SiteInverterDetail;

trait sungrowRealTimeAnimationData {

    public function getRealtimeData($plant,$id,$pl_sites_array){

                //RealTime Data working Sungrow FB-energy Old
                $appKeyData = Setting::where('perimeter', 'sun_grow_api_app_key')->first();
                $appKey = '3yhg';
                $userAccount = '3yhg';
                $userPassword = '3yhg';
                if ($appKeyData) {
                    $appKey = $appKeyData->value;
                }
                $userAccountData = Setting::where('perimeter', 'sun_grow_api_user_account')->first();
                if ($userAccountData) {
                    $userAccount = $userAccountData->value;
                }
                $userPasswordData = Setting::where('perimeter', 'sun_grow_api_user_password')->first();
                if ($userPasswordData) {
                    $userPassword = $userPasswordData->value;
                }

                $accessKey = 'x0qapvhjzzhvy9byj0j38b3en9nacwk9';
                //            $appKey = '3A7715CEE8399D0FA61B23248997C093';
                $curl = curl_init();
                $userCredentials = [

                    "appkey" => $appKey,
                    "user_account" => $userAccount,
                    "user_password" => $userPassword,
                    "login_type" => "1"
                ];

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/userService/login',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($userCredentials),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'sys_code: 901',
                        'lang: _en_US',
                        'x-access-key: ' . $accessKey,
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                $curl = curl_init();
                $responseType = json_decode($response, true);
                //                    return json_encode($responseType);
                $userId = $responseType['result_data']['user_id'];
                $token = $responseType['result_data']['token'];
                $allPlantsData = Plant::where('meter_type', 'SunGrow')->where('id', $id)->get();
                $current_power = 0;
                $gridMeterPower = 0;
                $numItems = 0;
                $NewCollectStartTime = Date('YmdHis', strtotime('-10 minutes'));
                $NewCollectEndTime = Date('YmdHis');
                $plantInverters = SiteInverterDetail::where('plant_id', $id)->whereIn('site_id', $pl_sites_array)->where('dv_inverter_type', 1)->get();
                foreach ($plantInverters as $inverters) {
                    $smartInverter = $inverters->dv_inverter;
                    $inverterPowerArray = array();
                    $siteSmartInverterData = [

                        "appkey" => $appKey,
                        "token" => $token,
                        "start_time_stamp" => $NewCollectStartTime,
                        "end_time_stamp" => $NewCollectEndTime,
                        "minute_interval" => "5",
                        "points" => "p1,p2,p4,p5,p6,p7,p8,p9,p10,p14,p18,p19,p20,p21,p22,p23,p24,p27,p29,p45,p46,p47,p48,p49,p50,p51,p52,p53,p54,p55,p56,p57,p58",
                        "ps_key" => $smartInverter
                    ];
                    $siteSmartInverterCurl = curl_init();

                    curl_setopt_array($siteSmartInverterCurl, array(

                        CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/commonService/queryDevicePointMinuteDataList',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'sys_code: 901',
                            'lang: _en_US',
                            'x-access-key: ' . $accessKey,
                        ),
                    ));

                    $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

                    curl_close($siteSmartInverterCurl);

                    $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);
                    //                    return json_encode($siteSmartInverterResponseData);
                    if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->result_data)) {

                        $siteSmartInverterFinalData = $siteSmartInverterResponseData->result_data;

                        if ($siteSmartInverterFinalData) {

                            $numItems = count($siteSmartInverterFinalData);
                            $i = 0;
                            foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
//                                                                return json_encode($smartInverterFinalData);
                                if(++$i === $numItems) {
//                                    return [$smartInverterFinalData,$siteSmartInverterFinalData];
                                    $current_power += isset($smartInverterFinalData->p24) && $smartInverterFinalData->p24 != 0 ? ($smartInverterFinalData->p24 / 1000) : 0;
                                }
                            }
                        }
                    }
                }

                //Meter Working
                if ($plant->plant_has_grid_meter == 'Y') {
                    $plantMeter = SiteInverterDetail::where('plant_id', $id)->whereIn('site_id', $pl_sites_array)->where('dv_inverter_type', 7)->first();
                    //return $plantMeter;
                    $pskeyData = $plantMeter->dv_inverter;
                    $siteSmartMeterData = [

                        "appkey" => $appKey,
                        "token" => $token,
                        "start_time_stamp" => $NewCollectStartTime,
                        "end_time_stamp" => $NewCollectEndTime,
                        "minute_interval" => "5",
                        "ps_key" => $pskeyData,
                        "points" => "p8018,p8062,p8063",

                    ];

                    $siteSmartMeterCurl = curl_init();

                    curl_setopt_array($siteSmartMeterCurl, array(

                        CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/commonService/queryDevicePointMinuteDataList',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($siteSmartMeterData),
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'sys_code: 901',
                            'lang: _en_US',
                            'x-access-key: ' . $accessKey,
                        ),
                    ));

                    $siteSmartMeterResponse = curl_exec($siteSmartMeterCurl);

                    curl_close($siteSmartMeterCurl);

                    $siteSmartMeterResponseData = json_decode($siteSmartMeterResponse);
                    //                    return json_encode($siteSmartMeterResponseData);
                    if ($siteSmartMeterResponseData && isset($siteSmartMeterResponseData->result_data)) {
                        //                                            return json_encode($siteSmartMeterResponseData);
                        foreach ($siteSmartMeterResponseData->result_data as $key => $meterData) {
                            if (isset($meterData->p8018)) {
                                $gridMeterPower = $meterData->p8018 / 1000;
                            } else {
                                $gridMeterPower = 0;
                            }
                        }
                    }
                }
                $current_consump = ($current_power + $gridMeterPower) > 0 ? ($current_power + $gridMeterPower) : 0;

                $current_grid_type = $gridMeterPower >= 0 ? '+ve' : '-ve';
                $curr_gen_arr = $this->unitConversion($current_power, 'kW');
                $curr_con_arr = $this->unitConversion($current_consump, 'kW');
                $curr_grid_arr = $this->unitConversion($gridMeterPower, 'kW');


        $Data = [
            "current_power" => $current_power,
            "current_consump" => $current_consump,
            "gridMeterPower" => $gridMeterPower,
            "current_grid_type" => $current_grid_type,
            "current_power" => $current_power,
            "currentgeneration" => round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1],
            "currentconsumption" => round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1],
            "currentgrid" => round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1],
            "current_grid_type" => $current_grid_type,
            "current_dc_power" => round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1]
        ];

        return $Data;

    }
    public function getRealtimeDataV2($plant,$id,$pl_sites_array) {

            //RealTime Data working Sungrow
            $appKeyData = Setting::where('perimeter', 'sun_grow_api_app_key')->first();
            $appKey = '3yhg';
            $userAccount = '3yhg';
            $userPassword = '3yhg';
            if ($appKeyData) {
                $appKey = $appKeyData->value;
            }
            $userAccountData = Setting::where('perimeter', 'sun_grow_api_user_account')->first();
            if ($userAccountData) {
                $userAccount = $userAccountData->value;
            }
            $userPasswordData = Setting::where('perimeter', 'sun_grow_api_user_password')->first();
            if ($userPasswordData) {
                $userPassword = $userPasswordData->value;
            }

            $accessKey = 'rxyrr4gt34kqx4ggdrdg2vs82k234zny';

            $curl = curl_init();
            $userCredentials = [

                "appkey" => $appKey,
                "user_account" => $userAccount,
                "user_password" => $userPassword,
                "login_type" => "1"
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($userCredentials),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'sys_code: 901',
                    'lang: _en_US',
                    'x-access-key: ' . $accessKey,
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $curl = curl_init();
            $responseType = json_decode($response, true);

            $userId = $responseType['result_data']['user_id'];
            $token = $responseType['result_data']['token'];
            $allPlantsData = Plant::where('meter_type', 'SunGrow')->where('id', $id)->get();
            $current_power = 0;
            $gridMeterPower = 0;
            $numItems = 0;
            $NewCollectStartTime = Date('YmdHis', strtotime('-10 minutes'));
            $NewCollectEndTime = Date('YmdHis');
            $plantInverters = SiteInverterDetail::where('plant_id', $id)->whereIn('site_id', $pl_sites_array)->where('dv_inverter_type', 1)->get();
            foreach ($plantInverters as $inverters) {
                $smartInverter = $inverters->dv_inverter;
                $inverterPowerArray = array();
                $siteSmartInverterData = [

                    "appkey" => $appKey,
                    "token" => $token,
                    "start_time_stamp" => $NewCollectStartTime,
                    "end_time_stamp" => $NewCollectEndTime,
                    "minute_interval" => "5",
                    "points" => "p1,p2,p4,p5,p6,p7,p8,p9,p10,p14,p18,p19,p20,p21,p22,p23,p24,p27,p29,p45,p46,p47,p48,p49,p50,p51,p52,p53,p54,p55,p56,p57,p58",
                    "ps_key_list" => array($smartInverter)
                ];

                $siteSmartInverterCurl = curl_init();

                curl_setopt_array($siteSmartInverterCurl, array(

                    CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getDevicePointMinuteDataList',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'sys_code: 901',
                        'lang: _en_US',
                        'x-access-key: ' . $accessKey,
                    ),
                ));

                $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

                curl_close($siteSmartInverterCurl);

                $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);
//                                    return json_encode($siteSmartInverterResponseData);
                if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->result_data)) {

                    $siteSmartInverterFinalData = $siteSmartInverterResponseData->result_data;

                    if ($siteSmartInverterFinalData) {
                        $siteSmartInverterFinalData = $siteSmartInverterFinalData->$smartInverter;
                        $numItems = count($siteSmartInverterFinalData);
                        $i = 0;
                        foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
                            if(++$i === $numItems) {

                                $current_power += isset($smartInverterFinalData->p24) && $smartInverterFinalData->p24 != 0 ? ($smartInverterFinalData->p24 / 1000) : 0;
                            }
                        }
                    }
                }
            }

            //Meter Working
            if ($plant->plant_has_grid_meter == 'Y') {
                $plantMeter = SiteInverterDetail::where('plant_id', $id)->whereIn('site_id', $pl_sites_array)->where('dv_inverter_type', 7)->first();
                //return $plantMeter;
                $pskeyData = $plantMeter->dv_inverter;
                $siteSmartMeterData = [

                    "appkey" => $appKey,
                    "token" => $token,
                    "start_time_stamp" => $NewCollectStartTime,
                    "end_time_stamp" => $NewCollectEndTime,
                    "minute_interval" => "5",
                    "ps_key_list" => array($pskeyData),
                    "points" => "p8018,p8062,p8063",

                ];

                $siteSmartMeterCurl = curl_init();

                curl_setopt_array($siteSmartMeterCurl, array(

                    CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getDevicePointMinuteDataList',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($siteSmartMeterData),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'sys_code: 901',
                        'lang: _en_US',
                        'x-access-key: ' . $accessKey,
                    ),
                ));

                $siteSmartMeterResponse = curl_exec($siteSmartMeterCurl);

                curl_close($siteSmartMeterCurl);

                $siteSmartMeterResponseData = json_decode($siteSmartMeterResponse);
                //                    return json_encode($siteSmartMeterResponseData);
                if ($siteSmartMeterResponseData && isset($siteSmartMeterResponseData->result_data)) {
                    //                                            return json_encode($siteSmartMeterResponseData);
                    $siteSmartMeterFinalData = $siteSmartMeterResponseData->result_data;
                    if(!empty($siteSmartMeterFinalData) && isset($siteSmartMeterFinalData->$pskeyData)) {

                        $siteSmartMeterFinalData = $siteSmartMeterFinalData->$pskeyData;
//return $siteSmartMeterFinalData;
                        foreach ($siteSmartMeterFinalData as $key => $meterData) {
                            if (isset($meterData->p8018)) {
                                $gridMeterPower = $meterData->p8018 / 1000;
                            } else {
                                $gridMeterPower = 0;
                            }
                        }
                    }
                }
            }
            $current_consump = ($current_power + $gridMeterPower) > 0 ? ($current_power + $gridMeterPower) : 0;

            $current_grid_type = $gridMeterPower >= 0 ? '+ve' : '-ve';
            $curr_gen_arr = $this->unitConversion($current_power, 'kW');
            $curr_con_arr = $this->unitConversion($current_consump, 'kW');
            $curr_grid_arr = $this->unitConversion($gridMeterPower, 'kW');
//            $currentDataValues['generation'] = $current_power;
//            $currentDataValues['consumption'] = $current_consump;
//            $currentDataValues['grid'] = $gridMeterPower;
//            $currentDataValues['grid_type'] = $current_grid_type;
//            $currentDataValues['dc_power'] = $current_power;
//            $current['generation'] = round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1];
//            $current['consumption'] = round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1];
//            $current['grid'] = round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1];
//            $current['grid_type'] = $current_grid_type;
//            $current['dc_power'] = $current['generation'];


            $Data = [
                "current_power" => $current_power,
                "current_consump" => $current_consump,
                "gridMeterPower" => $gridMeterPower,
                "current_grid_type" => $current_grid_type,
                "current_power" => $current_power,
                "currentgeneration" => round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1],
                "currentconsumption" => round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1],
                "currentgrid" => round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1],
                "current_grid_type" => $current_grid_type,
                "current_dc_power" => round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1]
            ];

        return $Data;
    }

}
