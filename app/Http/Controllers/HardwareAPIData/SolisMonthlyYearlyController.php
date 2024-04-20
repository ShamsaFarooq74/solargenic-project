<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SolisMonthlyYearlyController extends Controller
{

    public function SolisPlantMonthlyData($solisAPIBaseURL,$token,$siteId,$lastRecordDate)
    {
        $currentMonthDate = date('Y-m',strtotime($lastRecordDate));
        $siteSmartInverterData = [

            "stationId" => $siteId,
            "endTime" => $currentMonthDate,
            "startTime" => $currentMonthDate,
            "timeType" => 3
        ];


        $siteSmartInverterCurl = curl_init();

        curl_setopt_array($siteSmartInverterCurl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/history',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

        curl_close($siteSmartInverterCurl);
        return $siteSmartInverterResponse;

//        $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);
    }
    public function SolisInverterMonthlyData($solisAPIBaseURL,$token,$deviceSn,$lastRecordDate)
    {
        $currentMonthDate = date('Y-m',strtotime($lastRecordDate));
        $siteSmartInverterData = [

            "deviceSn" => $deviceSn,
            "endTime" => $currentMonthDate,
            "startTime" => $currentMonthDate,
            "timeType" => 3
        ];


        $siteSmartInverterCurl = curl_init();

        curl_setopt_array($siteSmartInverterCurl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/device/v1.0/historical',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

        curl_close($siteSmartInverterCurl);
        return $siteSmartInverterResponse;

//        $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);
    }
    public function SolisPlantYearlyData($solisAPIBaseURL,$token,$siteId,$lastRecordDate)
    {
        $currentMonthDate = date('Y',strtotime($lastRecordDate));
        $siteSmartInverterData = [

            "stationId" => $siteId,
            "endTime" => $currentMonthDate,
            "startTime" => $currentMonthDate,
            "timeType" => 4
        ];


        $siteSmartInverterCurl = curl_init();

        curl_setopt_array($siteSmartInverterCurl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/history',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

        curl_close($siteSmartInverterCurl);
        return $siteSmartInverterResponse;
    }
}
