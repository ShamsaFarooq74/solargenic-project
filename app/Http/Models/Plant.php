<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class Plant extends Model

{

    protected $fillable = [

        'company_id','siteId','plant_name','timezone','phone','currency','location','loc_lat','loc_long','city','province','capacity','benchmark_price','direction_angle','tilt_angle','avg_generation_price','national_fit','ratio_factor','building_cost','loan_proportion','plant_type','system_type','azimuth','expected_generation','daily_expected_saving','angle','plant_pic', 'plant_has_emi','is_online','alarmLevel','created_by','updated_by','updated_by_at','created_at','updated_at'

    ];

    protected $casts = [
        'company_id'=>'string',
        'siteId'=>'string',
        'serial_no'=>'string',
        'capacity'=>'string',
        'direction_angle'=>'string',
        'tilt_angle'=>'string',
        'avg_generation_price'=>'string',
        'national_fit'=>'string',
        'building_cost'=>'string',
        'loan_proportion'=>'string',
        'ratio_factor'=>'string',
        'azimuth'=>'string',
        'alarmLevel'=>'string',
        'expected_generation'=>'string'
    ];

    public function getDates() {
        return array();
    }

    public function plant_user()

    {

        return $this->hasMany(PlantUser::class,'plant_id');

    }

    public function inverters()

    {

        return $this->hasMany(Inverter::class,'plant_id');

    }

    public function inverter_serial_no()

    {

        return $this->hasMany(InverterSerialNo::class,'plant_id');

    }

    public function plant_sites()

    {

        return $this->hasMany(PlantSite::class,'plant_id');

    }
   public function station_battery_data()

    {

        return $this->hasMany(StationBatteryData::class,'plant_id');

    }

    public function plant_mppts()

    {

        return $this->hasMany(PlantMPPT::class,'plant_id');

    }


    public function daily_inverter_detail()

    {

        return $this->hasMany(DailyInverterDetail::class,'plant_id');

    }



    public function monthly_inverter_detail()

    {

        return $this->hasMany(MonthlyInverterDetail::class,'plant_id');

    }


    public function yearly_inverter_detail()

    {

        return $this->hasMany(YearlyInverterDetail::class,'plant_id');

    }



    public function logger()

    {

        return $this->hasOne(Loggers::class,'plant_id');

    }



    public function plant_details()

    {

        return $this->hasOne(PlantDetail::class,'plant_id');

    }



    public function inverter_details()

    {

        return $this->hasOne(InverterDetail::class,'plant_id');

    }



    public function daily_processed_plant_detail()

    {

        return $this->hasOne(DailyProcessedPlantDetail::class,'plant_id');

    }

    public function latest_daily_processed_plant_detail()

    {

        return $this->daily_processed_plant_detail()->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC');

    }

    public function processed_current_variables()

    {
        return $this->hasOne(ProcessedCurrentVariable::class,'plant_id');
    }

    public function latest_processed_current_variables()

    {
        return $this->processed_current_variables()->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC');
    }

    public function inverter_emi_details()
    {
        return $this->hasOne(InverterEMIDetail::class,'plant_id');
    }

    public function latest_inverter_emi_details()

    {
        return $this->inverter_emi_details()->whereDate('collect_time', date('Y-m-d'))->orderBY('collect_time', 'DESC');
    }

    public function inverterserialno()

    {

        return $this->hasMany(InverterSerialNo::class,'plant_id');

    }

    public function monthly_processed_plant_detail()

    {

        return $this->hasOne(MonthlyProcessedPlantDetail::class,'plant_id');

    }



    public function yearly_processed_plant_detail()

    {

        return $this->hasOne(YearlyProcessedPlantDetail::class,'plant_id');

    }

    public function latest_yearly_processed_plant_detail() {

        return $this->yearly_processed_plant_detail()->whereYear('created_at', date('Y'));
    }


    public function users()

    {

        return $this->hasMany(User::class);

    }



    public function notification()

    {

        return $this->hasOne(Notification::class);

    }



    public function generation_log()

    {

        return $this->hasMany(GenerationLog::class);

    }



    public function company()

    {

        return $this->belongsTo(Company::class, 'company_id');

    }



    public function expected_generation_log()

    {

        return $this->hasMany(ExpectedGenerationLog::class);

    }



    public function fault_alarm_log()

    {

        return $this->hasOne(FaultAlarmLog::class, 'plant_id');

    }

    public function latest_fault_alarm_log()

    {

        return $this->fault_alarm_log()->orderBy('created_at', 'DESC');

    }



    public function fault_and_alarm()

    {

        return $this->hasOne(FaultAndAlarm::class);

    }

}

