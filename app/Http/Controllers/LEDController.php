<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Http\Models\Plant;
use Illuminate\Http\Request;
use App\Http\Models\PlantSite;
use App\Http\Models\InverterDetail;
use App\Http\Models\Inverter;
use App\Http\Models\Setting;
use App\Http\Controllers\Controller;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\TotalProcessedPlantDetail;

class LEDController extends Controller
{
    public function updateVariable() {

        try {

            $commulativeInverterPower = [];
            $commulativeInverterEnergy = [];
            $commulativeReduction = [];
            $field1 = 0;
            $field2 = 0;
            $field3 = 0;
            $field4 = 0;
            $field5 = 0;
            $field6 = 0;


            $plants = Plant::all();

            if($plants) {

                foreach($plants as $key => $plant) {

                    $field1 = TotalProcessedPlantDetail::where('plant_id', $plant->id)->sum('plant_total_current_power');

                    $field2 = (TotalProcessedPlantDetail::where('plant_id', $plant->id)->sum('plant_total_generation')) / 1000;

                    $field3 = TotalProcessedPlantDetail::where('plant_id', $plant->id)->sum('plant_total_reduction');

                    if($plant->api_key != null) {

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api.thingspeak.com/update?api_key=".$plant->api_key."&field1=".$field1."&field2=".$field2."&field3=".$field3,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_HTTPHEADER => array(
                                // Set Here Your Requested Headers
                                'Content-Type: application/json',
                            ),
                        ));
                        $response1 = curl_exec($curl);
                        $err = curl_error($curl);
                        curl_close($curl);

                        if ($err) {
                            echo "cURL Error #:" . $err;
                        }
                    }
                }
            }
			
			$comm_api_key = Setting::where('perimeter', 'think_speak_commulative_data_api_key')->pluck('value')[0];
			
			$plant_data_ids = Plant::where('company_id', 7)->pluck('id')->toArray();

            if(!empty($plant_data_ids)) {

                $field4 = TotalProcessedPlantDetail::whereIn('plant_id', $plant_data_ids)->sum('plant_total_current_power');

                $field5 = (TotalProcessedPlantDetail::whereIn('plant_id', $plant_data_ids)->sum('plant_total_generation')) / 1000;

                $field6 = TotalProcessedPlantDetail::whereIn('plant_id', $plant_data_ids)->sum('plant_total_reduction');
            
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.thingspeak.com/update?api_key=".$comm_api_key."&field1=".$field4."&field2=".$field5."&field3=".$field6,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPHEADER => array(
                        // Set Here Your Requested Headers
                        'Content-Type: application/json',
                    ),
                ));
                $response1 = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($response1) {
                    echo "cURL Response #:" . $response1;
                }
                if ($err) {
                    echo "cURL Error #:" . $err;
                }
            
            }
            
            /*$comm_data = AccumulativeProcessedDetail::all();

			$field4 = $comm_data && $comm_data[0] ? $comm_data[0]->total_current_power : 0;
			$field5 = $comm_data && $comm_data[0] ? ($comm_data[0]->total_generation) / 1000 : 0;
			$field6 = $comm_data && $comm_data[0] ? $comm_data[0]->total_reduction : 0;*/
        }

        catch (Exception $e) {

            return $e->getMessage();
        }

    }
}
