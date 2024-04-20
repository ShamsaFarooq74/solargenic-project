<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Plant;
use App\Http\Models\PlantSite;
use App\Http\Models\PlantMPPT;
use App\Http\Models\PlantDetail;
use App\Http\Models\User;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\DailyProcessedPlantEMIDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantEMIDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantEMIDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\TotalProcessedPlantEMIDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterEMIDetail;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\Inverter;
use App\Http\Models\InverterEnergyLog;
use App\Http\Models\GenerationLog;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\Company;
use App\Http\Models\UserCompany;
use App\Http\Models\PlantUser;
use App\Http\Models\Notification;
use App\Http\Models\SystemType;
use App\Http\Models\Ticket;
use App\Http\Models\TicketSource;
use App\Http\Models\Setting;
use App\Http\Models\TicketAgent;
use App\Http\Models\Employee;
use App\Http\Models\PlantType;
use App\Http\Models\PlantMeterType;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\SiteInverterDetail;
use App\Http\Models\Weather;
use Spatie\Permission\Models\Role;


class DeleteDataController extends Controller
{

    public function deleteData($date, $ID) {

        $queryDate = date('Y-m-d', strtotime($date));

        $deleteDataResponse1 = InverterDetail::where('plant_id', $ID)->whereBetween('collect_time', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse2 = InverterEMIDetail::where('plant_id', $ID)->whereBetween('collect_time', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse3 = InverterMPPTDetail::where('plant_id', $ID)->whereBetween('collect_time', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse4 = InverterEnergyLog::where('plant_id', $ID)->whereBetween('collect_time', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse5 = GenerationLog::where('plant_id', $ID)->whereBetween('collect_time', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse6 = ProcessedCurrentVariable::where('plant_id', $ID)->whereBetween('collect_time', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse7 = DailyInverterDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse8 = MonthlyInverterDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse9 = YearlyInverterDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse10 = DailyProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse11 = DailyProcessedPlantEMIDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse12 = MonthlyProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse13 = MonthlyProcessedPlantEMIDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse14 = YearlyProcessedPlantDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();
        $deleteDataResponse15 = YearlyProcessedPlantEMIDetail::where('plant_id', $ID)->whereBetween('created_at', [$queryDate, date("Y-m-d", strtotime("+1 day"))])->delete();

    }
}
