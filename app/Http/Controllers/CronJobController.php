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
use App\Http\Controllers\HardwareAPIData\HuaweiController;
use App\Http\Controllers\HardwareAPIData\SunGrowController;
use App\Http\Controllers\Api\PlantSiteDataController;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\GenerationLog;
use App\Http\Models\ProcessedCurrentVariable;

class CronJobController extends Controller
{
    public function index() {

        try {

            $globalGenerationLogMaxID = GenerationLog::max('cron_job_id');
            $globalProcessedLogMaxID = ProcessedCurrentVariable::max('processed_cron_job_id');
            $globalInverterDetailMaxID = InverterDetail::max('inverter_cron_job_id');

            // $saltecMicrotechController = new PlantSiteDataController();
            // $saltecMicrotechController->plant_site_data();

            $huaweiController = new HuaweiController();
            $huaweiController->index($globalGenerationLogMaxID, $globalProcessedLogMaxID, $globalInverterDetailMaxID);

            $sunGrowController = new SunGrowController();
            $sunGrowController->sunGrow($globalGenerationLogMaxID, $globalProcessedLogMaxID, $globalInverterDetailMaxID);
        }

        catch (Exception $e) {

            return $e->getMessage();
        }

    }
}
