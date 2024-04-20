<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
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
use App\Http\Models\PlantType;
use App\Http\Models\PlantSite;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\SystemType;
use App\Http\Models\Setting;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\TotalProcessedPlantDetail;

class BelDeleteDataController extends Controller
{
    public function deletePreviousData($date, $ID)
    {
        $queryDate = date('Y-m-d', strtotime($date));
        $deleteDataResponse1 = ProcessedPlantDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse2 = InverterSerialNo::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse3 = Inverter::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse4 = DailyInverterDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse5 = MonthlyInverterDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse6 = YearlyInverterDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse13 = InverterDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse7 = GenerationLog::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse8 = DailyProcessedPlantDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse9 = MonthlyProcessedPlantDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse10 = YearlyProcessedPlantDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse11 = ProcessedCurrentVariable::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $deleteDataResponse12 = TotalProcessedPlantDetail::where('plant_id', $ID)->whereDate('created_at', '<', $queryDate)->delete();
        $meterSerialNo = Plant::where('id', $ID)->first();
        if ($meterSerialNo) {
            $deleteDataResponse14 = MicrotechEnergyGenerationLog::where('meter_serial_no', $meterSerialNo->meter_serial_no)->whereDate('created_at', '<=', $queryDate)->delete();
            $deleteDataResponse15 = MicrotechPowerGenerationLog::where('meter_serial_no', $meterSerialNo->meter_serial_no)->whereDate('created_at', '<=', $queryDate)->delete();
        }
    }

    public function deleteNextData($date, $ID)
    {
        $queryDate = date('Y-m-d', strtotime($date));
        $deleteDataResponse1 = ProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse2 = InverterSerialNo::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse3 = Inverter::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse4 = DailyInverterDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse5 = MonthlyInverterDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse6 = YearlyInverterDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse13 = InverterDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse7 = GenerationLog::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse8 = DailyProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse9 = MonthlyProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse10 = YearlyProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse11 = ProcessedCurrentVariable::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse12 = TotalProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $meterSerialNo = Plant::where('id', $ID)->first();
        if ($meterSerialNo) {
            $deleteDataResponse14 = MicrotechEnergyGenerationLog::where('meter_serial_no', $meterSerialNo->meter_serial_no)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
            $deleteDataResponse15 = MicrotechPowerGenerationLog::where('meter_serial_no', $meterSerialNo->meter_serial_no)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        }
    }
}
