<?php

namespace App\Http\Controllers;
use App\Http\Models\Plant;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\Weather;
use App\Models\SpeedoGraph;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\PlantsController;

class SpeedoGraphController extends Controller
{
    public function getSpeedoGraph()
    {
        $dailyProcessed = ProcessedCurrentVariable::where('plant_id' ,8)->get();
		$areaGraph = [];
		for($i=0;$i<count($dailyProcessed);$i++)
		{
			array_push($areaGraph,$dailyProcessed[$i]['created_at']);
			array_push($areaGraph['yAxis'],(int)$dailyProcessed[$i]['totalEnergy']);
		}
		$areaGraphString = json_encode($areaGraph);
		return view('chart-js',compact('areaGraphString'));
    }
    public function getPlantGenerationData(Request $request)
    {
        $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $request->plantId)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $request->plantId)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time','desc')->first()->collect_time : date('Y-m-d 00:10:00');

        $plantsController = new PlantsController();
        $finalCurrentDataDateTime = $plantsController->previousTenMinutesDateTime($currentDataLogTime);

        $totalProcessedData =  ProcessedCurrentVariable::Select('current_generation','current_irradiance','collect_time','plant_id')->where('plant_id',$request->plantId)->whereBetween('collect_time', [date('Y-m-d 00:00:00'),$finalCurrentDataDateTime])->get();
        $plantsGraph = [];
        $totalEnergy = [];
        $totalIrradiance = [];
        $createdAt = [];
        $time = '';
        $sunriseHour = '';
        for($i=0;$i<count($totalProcessedData);$i++)
        {
            $plant = Plant::where('id',$totalProcessedData[$i]['plant_id'])->first();
            $date = strtotime($totalProcessedData[$i]['collect_time']);
            $processedDate = date("H:i A", $date);
            $processedHour =  date("H", $date);

            if(Weather::where('city',$plant['city'])->whereDate('created_at', Carbon::today())->exists())
            {
                $weather = Weather::where('city',$plant['city'])->whereDate('created_at', date('Y-m-d'))->first();

                if($processedDate < $weather['sunrise'])
                {
                    $totalProcessedData[$i]['current_generation'] = null;
                    $totalProcessedData[$i]['current_irradiance'] = null;
                }

                if($weather['sunrise'])
                {
                    $timeSunrise =  explode(':',$weather['sunrise']);
                    $sunriseHour = $timeSunrise[0];
                }
                else
                {
                    $sunriseHour = '';
                }

            }
            /*else
            {
                $totalProcessedData[$i]['totalEnergy'] = null;
            }*/
            $time =  24 - (int)$processedHour;
            array_push($totalEnergy,$totalProcessedData[$i]['current_generation']);
            array_push($totalIrradiance,$totalProcessedData[$i]['current_irradiance']);
            array_push($createdAt,$processedDate);

        }
        if($time)
        {
            for($j=0;$j<$time;$j++) {
                array_push($totalEnergy, null);
                array_push($totalIrradiance, null);
                array_push($createdAt, null);
            }
        }
        if(!empty($sunriseHour))
        {
            $value = (int)$sunriseHour;
            for($j=0;$j<$value;$j++)
            {
                array_unshift($totalEnergy,null);
                array_unshift($totalIrradiance,null);
                array_unshift($createdAt,null);
            }

        }
        return $totalIrradiance;
        array_push($plantsGraph,['totalEnergy' => $totalEnergy,'totalIrradiance' => $totalIrradiance,'createdAt' => $createdAt,'arraySize' => count($totalEnergy),'time' => $time]);
        return $plantsGraph;
    }
    public function getPlantDailyPower(Request $request)
    {
        $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $request->plantId)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time','desc')->first()->collect_time;

        $plantsController = new PlantsController();
        $finalCurrentDataDateTime = $plantsController->previousTenMinutesDateTime($currentDataLogTime);

        if(ProcessedCurrentVariable::where('plant_id',$request->plantId)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->exists()) {

            $currentGeneration = ProcessedCurrentVariable::Select('current_generation')->where('plant_id', $request->plantId)->orderBy('collect_time', 'DESC')->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->first();

            $capacity = Plant::Select('capacity')->where('id', $request->plantId)->first();
            if ($currentGeneration) {
                if ($currentGeneration['current_generation'] > 0 && $currentGeneration['current_generation'] < $capacity['capacity']) {
                    $capacity->capacity = $capacity->capacity - $currentGeneration['current_generation'];
                } else if ($currentGeneration['current_generation'] > (int)$capacity->capacity) {
                    $capacity->capacity = 0;
                }
                if ($currentGeneration['current_generation'] == null) {
                    $currentGeneration['current_generation'] = 0;
                }
                return ['status' => true,'data' => [$currentGeneration['current_generation'], $capacity->capacity], 'value' => round($currentGeneration['current_generation'])];
            }
            else
            {
                return ['status' => false];
            }
        }
        return ['status' => false];
    }
}
