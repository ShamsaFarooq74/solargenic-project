<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\GenerationLog;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\Notification;
use App\Http\Models\NotificationEmail;
use App\Http\Models\NotificationSMS;
use App\Http\Models\PlantDetail;
use App\Http\Models\PlantMPPT;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Ticket;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\User;
use App\Http\Models\Company;
use App\Http\Models\Plant;
use App\Http\Models\PlantSite;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\PlantUser;
use App\Http\Models\Inverter;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use View;


class DeletePlantDataController extends Controller
{
    public function DeletePlantData(Request $request)
    {
        $plant=Plant::where('id',$request->id)->delete();
        PlantSite::where('plant_id',$request->id)->delete();
        PlantUser::where('plant_id',$request->id)->delete();
        PlantDetail::where('plant_id',$request->id)->delete();
        PlantMPPT::where('plant_id',$request->id)->delete();
        InverterSerialNo::where('plant_id',$request->id)->delete();
        Inverter::where('plant_id',$request->id)->delete();
        InverterDetail::where('plant_id',$request->id)->delete();
        GenerationLog::where('plant_id',$request->id)->delete();
        ProcessedCurrentVariable::where('plant_id',$request->id)->delete();
        ProcessedPlantDetail::where('plant_id',$request->id)->delete();
        ExpectedGenerationLog::where('plant_id',$request->id)->delete();
        DailyInverterDetail::where('plant_id',$request->id)->delete();
        DailyProcessedPlantDetail::where('plant_id',$request->id)->delete();
        DailyInverterDetail::where('plant_id',$request->id)->delete();
        FaultAlarmLog::where('plant_id',$request->id)->delete();
        MonthlyInverterDetail::where('plant_id',$request->id)->delete();
        MonthlyProcessedPlantDetail::where('plant_id',$request->id)->delete();
        YearlyInverterDetail::where('plant_id',$request->id)->delete();
        YearlyProcessedPlantDetail::where('plant_id',$request->id)->delete();
        TotalProcessedPlantDetail::where('plant_id',$request->id)->delete();
        Ticket::where('plant_id',$request->id)->delete();
        Notification::where('plant_id',$request->id)->delete();
        return("Data against plant # ID ".$request->id." deleted successfully");
    }
}
