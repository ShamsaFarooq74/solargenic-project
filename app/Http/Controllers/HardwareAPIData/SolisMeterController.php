<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;

use DateTime;
use DateTimeImmutable;
use Illuminate\Http\Request;
use App\Http\Models\InverterEnergyLog;

class SolisMeterController extends Controller
{
    public function meterData($plantID, $siteID, $smartInverter, $processedMaxCronJobID)
    {
        date_default_timezone_set('Asia/Karachi');
        $todayDate = date('Y-m-d');
//        $currentTime = date('Y-m-d H:i:s');
        $curl = curl_init();
        $siteData = [

            "stationId" => $siteID,
            "timeType" => 1,
            "startTime" => $todayDate,
            "endTime" => $todayDate,
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.solarmanpv.com/station/v1.0/history',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX25hbWUiOiIyMTQwNl9hc2ltQHZpcGVyLnBrXzMiLCJzY29wZSI6WyJhbGwiXSwiZGV0YWlsIjp7Im9yZ2FuaXphdGlvbklkIjoyMTQwNiwidG9wR3JvdXBJZCI6MTYzODAsImdyb3VwSWQiOjE2MzgwLCJyb2xlSWQiOjEsInVzZXJJZCI6OTQzOTgsInZlcnNpb24iOjEsImlkZW50aWZpZXIiOiJhc2ltQHZpcGVyLnBrIiwiaWRlbnRpdHlUeXBlIjozLCJhcHBJZCI6IjIwMjAxMTI1MjMzMzE0MCJ9LCJleHAiOjE2MzA0MDI4MDksImF1dGhvcml0aWVzIjpbImFsbCJdLCJqdGkiOiI4MTBjYWFlYi1iYzljLTRhYWYtOTgwMS02ZTZjNWJlOTQ3OGEiLCJjbGllbnRfaWQiOiJ0ZXN0In0.EhchHzVHiHX5Qf4jWwTXRpSP5iel2DdWePtgy5vtU96G8qNI_AdpdwIP5-ylQf4I7HmLJDNbWug7TaJ7FPpKueHGCmitTVGTQI59UXYVuy8Ei7yhR3zbnN4NsiYPMW4YEe4LG4AW8bw0wW8hTzBp51kbqxtPWKJyKUNBTKx73LSu-hbNlu9IH1tU9ySFlbev7B8GyZOa7NZyuo4BF1Q9Gjqpga_YUUnq681uBU8sxLUJh8jjFK7PS02TJwDMed-awCxP1rC9HjT5B0-ZqFHHR-x7IwFyjT6GWR0yZemrEOaybFuKcUvtScSXySgGO5aEmOUohnzrlADGMHahAxWoZQ',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
//        return $response;
        $solisGridData = json_decode($response);
//        return json_encode(gettype($solisGridData->stationDataItems));
//        date('Y-m-d H:i:s', substr($finalData3->collectTime, 0, -3));
        if (isset($solisGridData->stationDataItems)) {

            foreach ($solisGridData->stationDataItems as $key6 => $solisGridDetails) {
//                $dateData = date('Y-m-d H:i:s', strtotime($solisGridDetails->dateTime));
                $explodeData = explode('.', $solisGridDetails->dateTime);
                $collectTime = date(date('Y-m-d H:i:s', $explodeData[0]));
                $exportEnergy = '';
                $importEnergy = '';
//                if (strpos($solisGridDetails->gridPower, '-') !== false) {
//                    $exportEnergy = $solisGridDetails->gridPower;
//                } else {
//                    $importEnergy = $solisGridDetails->gridPower;
//                }
                $usePower = 0;
//                return gettype($solisGridDetails->usePower);
                if ($solisGridDetails->purchasePower !== null) {
                    $usePower = $this->unitConversion($solisGridDetails->purchasePower,'kW');
                }
                $todayLastTime = InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $collectTime)->orderBy('collect_time', 'desc')->first();
                if ($todayLastTime) {
//                    if ($todayLastTime->collect_time > date('Y-m-d H:i:s')) {

                    $inverterEnergyLog['plant_id'] = $plantID;
                    $inverterEnergyLog['site_id'] = $siteID;
                    $inverterEnergyLog['dv_inverter'] = $smartInverter;
                    $inverterEnergyLog['grid_power'] = $solisGridDetails->generationPower;
                    $inverterEnergyLog['import_energy'] = $usePower[0];
                    $inverterEnergyLog['export_energy'] = 0;
                    $inverterEnergyLog['cron_job_id'] = $processedMaxCronJobID;
                    $inverterEnergyLog['collect_time'] = $collectTime;

                    $inverterEnergyLogResponse = $todayLastTime->update($inverterEnergyLog);
                } else {
                    $inverterEnergyLog['plant_id'] = $plantID;
                    $inverterEnergyLog['site_id'] = $siteID;
                    $inverterEnergyLog['dv_inverter'] = $smartInverter;
                    $inverterEnergyLog['grid_power'] = $solisGridDetails->generationPower;
                    $inverterEnergyLog['import_energy'] = $usePower[0];
                    $inverterEnergyLog['export_energy'] = 0;
                    $inverterEnergyLog['cron_job_id'] = $processedMaxCronJobID;
                    $inverterEnergyLog['collect_time'] = $collectTime;

                    $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
                }
//                }
            }
//            if (str_contains($finalData3->gridPower, '-')) {
//                return 'hell though';
//            }
//            return json_encode($dateData);
        }
//        return json_encode($solisGridData);
    }



    public function unitConversion($num, $unit) {

        $num = (double)$num;

        if($num < 0) {

            $num = $num * (-1);
        }

        if($num < pow(10,3)) {
            if($unit == 'PKR') {
                $unit = ' PKR';
            }
            else if($unit == 'W') {
                $unit = 'W';
            }
        }

        else if($num >= pow(10,3) && $num < pow(10,6) ) {
            $num = $num / pow(10,3);

            if($unit == 'kWh') {
                $unit = 'MWh';
            }
            else if($unit == 'kW') {
                $unit = 'MW';
            }
            else if($unit == 'kWp') {
                $unit = 'MWp';
            }
            else if($unit == 'PKR') {
                $unit = 'K PKR';
            }
            else if($unit == 'W') {
                $unit = 'kW';
            }

        }

        else if($num >= pow(10,6) && $num < pow(10,9) ) {
            $num = $num / pow(10,6);

            if($unit == 'kWh') {
                $unit = 'GWh';
            }
            else if($unit == 'kW') {
                $unit = 'GW';
            }
            else if($unit == 'kWp') {
                $unit = 'GWp';
            }
            else if($unit == 'PKR') {
                $unit = 'M PKR';
            }
            else if($unit == 'W') {
                $unit = 'MW';
            }

        }

        else if($num >= pow(10,9) && $num < pow(10,12) ) {
            $num = $num / pow(10,9);

            if($unit == 'kWh') {
                $unit = 'TWh';
            }
            else if($unit == 'kW') {
                $unit = 'TW';
            }
            else if($unit == 'kWp') {
                $unit = 'TWp';
            }
            else if($unit == 'PKR') {
                $unit = 'B PKR';
            }
            else if($unit == 'W') {
                $unit = 'GW';
            }

        }

        else if($num >= pow(10,12) && $num < pow(10,15) ) {
            $num = $num / pow(10,12);

            if($unit == 'kWh') {
                $unit = 'PWh';
            }
            else if($unit == 'kW') {
                $unit = 'PW';
            }
            else if($unit == 'kWp') {
                $unit = 'PWp';
            }
            else if($unit == 'PKR') {
                $unit = 'T PKR';
            }
            else if($unit == 'W') {
                $unit = 'TW';
            }

        }

        else if($num >= pow(10,15) && $num < pow(10,18) ) {
            $num = $num / pow(10,15);

            if($unit == 'kWh') {
                $unit = 'EWh';
            }
            else if($unit == 'kW') {
                $unit = 'EW';
            }
            else if($unit == 'kWp') {
                $unit = 'EWp';
            }
            else if($unit == 'PKR') {
                $unit = 'Q PKR';
            }
            else if($unit == 'W') {
                $unit = 'PW';
            }

        }

        return [$num, $unit];
    }
}
