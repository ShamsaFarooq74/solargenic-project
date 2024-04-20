@extends('layouts.admin.master')

@section('title', 'User Plant Detail')

@section('content')
    <style>
        .selectBox {
            position: relative;
        }

        .btn-add-vt {
            border-radius: 4px;
            width: 110px;
            height: 37px;
            color: #707070;
            font-size: 13px;
            background: #fff;
            position: absolute;
            top: 6px;
            right: 15px;
            border: 1px solid #707070;
        }

        .btn-add-vt:hover {
            background: #003366;
            color: #fff;
        }

        .selectBox select {
            width: 100%;
            height: 35px;
            border-radius: 2px;
            font-size: 12px;
            color: #a7a6a6;
            border-color: #ccc !important;
            font-family: "Sofia-Pro-Regular-Az ,sans-serif";
        }

        .c-datepicker-date-editor {
            position: relative !important;
        }

        .multiselect span {
            width: 150px;
            float: left;
            position: absolute;
            left: 8px;
            top: 8px;
        }
        .btn_left_solar_vt{
            left: 35%;
        }
        .btn_right_solar_vt{
            right: 35%;
        }
        .btn_left_energy_source_vt{
            left: 38%;
        }
        .btn_right_energy_source_vt{
            right: 38%;
        }
        .btn_left_outage_served_vt{
            left: 35%;
        }
        .btn_right_outage_served_vt{
            right: 35%;
        }
        .text-center{
            text-align:center !important;
        }
        .home_imgtext_vtt {
        width: 190px !important;
        float: right;
        margin-top: -88px !important;
        margin-right: 65px;
        text-align: center;
        }
        .mx-padding{
           padding-bottom :35px !important;
        }
        .mx-height{
            height:322px !important;
            margin-top: 73px !important;
        }.mx-padding-bottom{
            padding-bottom :25px !important;
        }
        .bottom-padding{
            padding-bottom: 48px !important;
        }
    
        .single-dash {
        width: 100%;
        float: left;
        min-height: 391px ;
        background: #063c6e;
        padding: 83px 6vw ;
        position: relative;
        overflow-x: auto;
        margin-bottom: 10px;
        }
    
       .row.wrap-none {
       flex-wrap: nowrap !important;
       }
       .margin-top-left{
        margin-left:-43px;
        font-size:12px;
       }
       .margins-top-left{
            margin-left: -30px;
            font-size: 12px;
       }
       .margin-left{
        margin-left:50px;
        font-size:12px;
       }
      .margins-left{
            margin-left: 122px;
            font-size: 12px;
      }
    </style>



    <link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <div class="container-fluid px-xl-5 mt-3">

        <section>
            <input type="hidden" id="plantID" value="{{ $plant->id }}">
            <div class="row">
                <div class="col-lg-12 mb-1">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert"
                               aria-label="close">&times;</a> {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="row ">
                
                <div class="col-lg-4 mb-3 mb-lg-3">
                    <div class="card-header">

                        <h2 class="head_real_vt">Real Time</h2>
                    </div>
                    <div class="plant-area-vt py-2">
                        @if($plant->meter_type == 'Solis-Cloud')
                        <p>Backup Consumption </p>

                        <h3 class="one" id="real-time-backup-load">
                            {{ isset($current['backupLoad']) && !empty($current['backupLoad']) ? $current['backupLoad'] : 0 }}
                        </h3>
                        <p>Grid Consumption </p>
                     
                        <h3 class="one" id="real-time-consumption">
                            {{ isset($current['gridLoad']) && !empty($current['gridLoad']) ? $current['gridLoad'] : 0 }}
                        </h3>
                        @else
                        <p>Consumption </p>

                        <h3 class="one" id="real-time-consumption">
                            {{ isset($current['consumption']) && !empty($current['consumption']) ? $current['consumption'] : 0 }}
                        </h3>
                       @endif
                        <p>Generation</p>

                        <h3 class="two" id="real-time-generation">
                            {{ isset($current['generation']) && !empty($current['generation']) ? $current['generation'] : 0 }}
                        </h3>

                        @if($plant->system_type != 'All on Grid')

                            <p>Grid</p>

                            <h3 class="three" id="real-time-grid">
                                {{isset($current['grid_type']) && isset($current['grid']) && $current['grid'] != 0 && $current['grid_type'] == '-ve' ? '- ' : '' }}{{isset($current['grid']) && !empty($current['grid']) ? $current['grid'] : 0 }}
                            </h3>

                        @endif

                        <p>Battery</p>
                        <h3 class="three2"
                            id="real-time-battery-data">{{isset($current['battery_power_data']) ? $current['battery_power_data'] : 0}}</h3>

                    </div>

                </div>

                <div class="col-lg-8 mb-3 mb-lg-0  order-first order-md-12" id="real-time-energy-flow-graph">

                    @if($plant->system_type != 'All on Grid')

                        <div
                            class="{{ $plant->meter_type == 'Solis-Cloud' ? 'single-dash' :'single-dashboard-vt' }}">

                            <div class="single-dashb_vt">

                                <div class="single-dashboard-row-vt">

                                    <img src="{{ asset('assets/images/tower.png') }}" alt="tower" width="45">

                                    <div class="single-area-vt">

                                        <h4 style="padding-left: 15px;">Grid</h4>

                                        <span>{{isset($current['grid']) && !empty($current['grid']) ? $current['grid'] : 0 }}</span>

                                        @if(isset($current['grid']) && $current['grid'] != 0)

                                            @if($current['grid_type'] == '+ve')

                                                <div class="size_power active-animatioon"></div>

                                            @elseif($current['grid_type'] == '-ve')

                                                <div class="size_power1 active-animatioon"></div>

                                            @endif

                                        @else

                                            <div class="size_power_off active-animatioon"></div>

                                            <span class="comm_fail" style="font-size: 9px; margin-left:2px;"></span>

                                        @endif

                                    </div>

                                </div>

                                <div class="single-dashboard-tow-vt">

                                    <img src="{{ asset('assets/images/sensor.png')}}" alt="sensor" class="img"
                                         width="45">

                                </div>

                                <div class="single-dashboard-row-vt">

                                    <img src="{{ asset('assets/images/power.png')}}" alt="tower" width="45">

                                    <div class="single-area-vt">

                                        <h4>Generation</h4>

                                        <span>{{ isset($current['generation']) && !empty($current['generation']) ? $current['generation'] : 0 }}</span>

                                        @if(isset($current['generation']) && $current['generation'] == 0)

                                            <div class="size_generation_off active-animatioon"></div>

                                        @else

                                            <div class="size_generation active-animatioon"></div>

                                        @endif

                                    </div>

                                </div>

                                <div class="home_powerarea_vt">
                                @if($plant->meter_type == 'Solis-Cloud')
                                <div class="home_power_area_vt">
                                         <div class="home_imgtext_vt home_imgtext_vtt">
                                             <span></span>
                                             <div class="row wrap-none">
                                                 <div class="col-lg-6">
                                                     <h4>Grid Consumption</h4>
                                                     <img src="{{ asset('assets/images/grid-com.png') }}"
                                                         alt="tower" width="45">
                                                 </div>
                                                 <div class="col-lg-6">
                                                     <h4>Backup Consumption</h4>
                                                     <img src="{{ asset('assets/images/home.png') }}"
                                                         alt="tower" width="42" style="margin-left:10px">
                                                 </div>
                                             </div>
                                         </div>
                                           
                                         <div style="margin-top:51px">
                                           @if(isset($current['gridLoad']) && $current['gridLoad'] == 0)
                                            <div class="sizes_generations_offs active-animatioon">
                                                <span class="text-light margins-top-left">{{ $current['gridLoad'] }}</span>
                                            </div>
                                              <!-- <div class="resize_generation_off_div active-animatioon"></div>   -->
                                             @else
                                              <div class="sizes_generations active-animatioon">
                                                  <span class="text-light margin-top-left">{{ $current['gridLoad'] }}</span>
                                              </div>
                                               <div class="resizes_generations_off_div active-animatioon"></div>
                                              @endif
                                              @if(isset($current['backupLoad']) && $current['backupLoad']== 0)
                                            <div class="resize_generation_off active-animatioon">
                                                 <span class="text-light margins-left">{{ $current['backupLoad'] }}</span>
                                             </div>
                                             @else
                                              <div class="resizes_generations_off active-animatioon">
                                                  <span
                                                      class="text-light margin-left">{{ $current['backupLoad'] }}</span>
                                              </div>
                                               <div class="resizes_generations_off_div active-animatioon"></div>
                                              @endif
                                         </div>
                                      

                                            <!-- <div class="size_generation active-animatioon"></div> -->

                                      
                                    </div>
                                @else
                                    <div class="home_power_area_vt">

                                        <div class="home_imgtext_vt">
                                            <span>{{ isset($current['consumption']) && !empty($current['consumption']) ? $current['consumption'] : 0 }}</span>
                                            <h4>Consumption</h4>
                                            <img src="{{ asset('assets/images/home.png')}}" alt="tower" width="45">
                                        </div>
                                        @if(isset($current['consumption']) && $current['consumption'] == 0)

                                            <div class="size_generation_off active-animatioon"></div>

                                        @else

                                            <div class="size_generation active-animatioon"></div>

                                        @endif
                                    </div>
                                @endif
                                </div>
                                @if($plant->system_type_id == 4)
                                    <div class="single-battery-row-vt">
                                        <div class="single-battery-vt">
                                            <div class="batter_icon_text">
                                                <img src="{{ asset('assets/images/battery.png')}}" alt="tower"
                                                     width="35">
                                                <h4>battery</h4>
                                                <span>{{isset($current['battery_power_data']) ? $current['battery_power_data'] : 0 }}</span>
                                            </div>
                                            {{--                                            <div class="battery_power active-animatioon"></div>--}}
                                            @if(isset($current['battery_power']) && $current['battery_power'] == 0)
                                                <div class="battery_power_off active-animatioon"></div>
                                            @else
                                                @if($current['battery_type'] == '+ve')
                                                    <div class="battery_power1 active-animatioon"></div>
                                                @elseif($current['battery_type'] == '-ve')
                                                    <div class="battery_power active-animatioon"></div>
                                                @else
                                                    <div class="battery_power_off active-animatioon"></div>
                                                @endif

                                            @endif
                                        </div>

                                    </div>
                                @endif

                            </div>

                        </div>

                    @else

                        <div class="single_dashboard_vt">

                            <div class="plant_single_dashb_vt">

                                <div class="single_one_vt">

                                    <img src="{{ asset('assets/images/power.png')}}" alt="tower" width="45">

                                    <div class="img_text_vt">

                                        <h4>Generation</h4>

                                        <span>{{ isset($current['generation']) && !empty($current['generation']) ? $current['generation'] : 0 }}</span>

                                        @if(isset($current['generation']) && $current['generation'] == 0)

                                            <div class="plant_consumption_off active-animatioon"></div>

                                        @else

                                            <div class="plant_consumption active-animatioon"></div>

                                        @endif

                                    </div>

                                </div>
                                <div class="img_con_vt"><img src="{{ asset('assets/images/sensor.png')}}" alt="tower"
                                                             width="45"></div>

                                <div class="single_one_vt">

                                    <div class="img_text_vt">

                                        <h4>Consumption</h4>

                                        @if($plant->system_type != 'All on Grid')

                                            <span>{{ isset($current['consumption']) && !empty($current['consumption']) ? $current['consumption'] : 0 }}</span>

                                        @else

                                            <span>{{ isset($current['generation']) && !empty($current['generation']) ? $current['generation'] : 0 }}</span>

                                        @endif


                                        @if($plant->generation_log && $plant->generation_log->last()->current_generation == null
                                        || $plant->generation_log->last()->current_generation == 0)
                                            <div class="plant_consumption_off active-animatioon"></div>

                                        @else

                                            <div class="plant_consumption active-animatioon"></div>

                                        @endif

                                    </div>

                                    <img src="{{ asset('assets/images/home.png')}}" alt="tower" width="45">

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

            <div class="row mb-3">
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="card-header">
                        <h2 class="head_real_vt">Generation</h2>
                    </div>

                    <div class="plant-area-vt py-3">
                        <p>Daily Generation</p>
                        <h3 class="one"> {{$daily['generation']}}</h3>
                        <p>Monthly Generation </p>
                        <h3 class="two"> {{$monthly['generation']}}</h3>
                        <p>Yearly Generation </p>
                        <h3 class="three"> {{$yearly['generation']}}</h3>
                        <p>Total Generation </p>
                        <h3 class="three2"> {{$total['generation']}}</h3>
                    </div>

                </div>
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="card-header">

                        <h2 class="head_real_vt">Consumption</h2>
                    </div>
                    <div class="plant-area-vt py-3">
                        <p>Daily Consumption</p>
                        <h3 class="one"> {{$daily['consumption']}}</h3>
                        <p>Monthly Consumption </p>
                        <h3 class="two"> {{$monthly['consumption']}}</h3>
                        <p>Yearly Consumption </p>
                        <h3 class="three"> {{$yearly['consumption']}}</h3>
                        <p>Total Consumption </p>
                        <h3 class="three2"> {{$total['consumption']}}</h3>
                    </div>

                </div>
                @if($plant->system_type != 'All on Grid')
                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <div class="card-header">

                            <h2 class="head_real_vt">Net Grid Units</h2>
                        </div>

                        <div class="plant-area-vt py-3">
                            <p>Daily Grid</p>
                            <h3 class="one"> {{$daily['netGridSign'].''.$daily['netGrid']}}</h3>
                            <p>Monthly Grid </p>
                            <h3 class="two"> {{$monthly['netGridSign'].''.$monthly['netGrid']}}</h3>
                            <p>Yearly Grid </p>
                            <h3 class="three"> {{$yearly['netGridSign'].''.$yearly['netGrid']}}</h3>
                            <p>Total Grid </p>
                            <h3 class="three2"> {{$total['netGridSign'].''.$total['netGrid']}}</h3>
                        </div>

                    </div>
                @else
                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <div class="card-header">
                            <h2 class="head_real_vt">Cost Savings</h2>
                        </div>
                        <div class="plant-area-vt ">
                            <p>Daily Saving</p>
                            <h3 class="one"> {{$daily['revenue']}}</h3>
                            <p>Monthly Saving </p>
                            <h3 class="two"> {{$monthly['revenue']}}</h3>
                            <p>Yearly Saving </p>
                            <h3 class="three"> {{$yearly['revenue']}}</h3>
                            <p>Total Saving </p>
                            <h3 class="three2"> {{$total['revenue']}}</h3>
                        </div>

                    </div>
                @endif
            </div>

            <div class="row mb-3">
                <div class="col-lg-8">
                    <div class="card_vt p-0 mb-3">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Battery Information</h2>
                        </div>
                        <div class="mt-0">

                            <div class="daily-energy-vt">
                                <div class="energy_spac_vt">
                                    <div class="battery_icon_vt" id="battery-detail-data">

                                        @if($current['battery_capacity'] == '100%')
                                            <div id="div2" class="fa fa-battery"></div>
                                            <span>{{$current['battery_capacity']}}</span>
                                        @elseif($current['battery_capacity'] <= '25%')
                                            @if($current['battery_type'] == '-ve')
                                                <div id="div25" class="fa"></div>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @else
                                                <i style="color:#ff0202;font-size: 52px"
                                                   class="fa fa-battery-quarter"></i>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @endif
                                        @elseif($current['battery_capacity'] <= '50%')
                                            @if($current['battery_type'] == '-ve')
                                                <div id="div50" class="fa"></div>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @else
                                                <i style="color:#ff8000;font-size: 52px"
                                                   class="fa fa-battery-half"></i>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @endif
                                        @elseif($current['battery_capacity'] <= '75%')
                                            @if($current['battery_type'] == '-ve')
                                                <div id="div75" class="fa"></div>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @else
                                                <i style="color:#8000ff;font-size: 52px"
                                                   class="fa fa-battery-three-quarters"></i>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @endif
                                        @else
                                            @if($current['battery_type'] == '-ve')
                                                <div id="div100" class="fa"></div>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @else
                                                <i style="color:#0080ff;font-size: 52px"
                                                   class="fa fa-battery-three-quarters"></i>
                                                <span>{{$current['battery_capacity']}}</span>
                                            @endif
                                        @endif

                                    </div>
                                    <p>Battery Remaining</p>
                                    <h3 class="one"> {{$battery_remaining}} (kWh)</h3>
                                    <p>Battery Backup - Current Load </p>
                                    @if($battery_backup_formula == 'No Load State')
                                        <h3 class="two">{{$battery_backup_formula}}</h3>
                                    @else
                                        <h3 class="two">{{$battery_backup_formula}}(Hrs)</h3>
                                    @endif
                                    <p>Battery Backup - Max Load</p>
                                    <h3 class="three"> {{$battery_backup_max_load}} (Hrs)</h3>
                                </div>
                            </div>

                            <div class="daily-energy-vt one_tow_three">
                                <div class="row w-100 text-center">
                                    <div class="col-md-6">
                                        <p>Daily Charging Energy</p>
                                        <h3 class="one"> {{$daily['dailyChargeEnergy']}}</h3>
                                        <p>Monthly Charge </p>
                                        <h3 class="two"> {{$monthly['monthlyChargeEnergy']}}</h3>
                                        <p>Yearly Charge </p>
                                        <h3 class="three"> {{$yearly['yearlyChargeEnergy']}}</h3>
                                        <p>Total Charge </p>
                                        <h3 class="three2"> {{$total['totalChargeEnergy']}}</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <p>Daily Discharge Energy</p>
                                        <h3 class="one"> {{$daily['dailyDischargeEnergy']}}</h3>
                                        <p>Monthly Discharge </p>
                                        <h3 class="two"> {{$monthly['monthlyDischargeEnergy']}}</h3>
                                        <p>Yearly Discharge </p>
                                        <h3 class="three"> {{$yearly['yearlyDischargeEnergy']}}</h3>
                                        <p>Total Discharge </p>
                                        <h3 class="three2"> {{$total['totalDischargeEnergy']}}</h3>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
           @if($plant->meter_type == 'Solis-Cloud')
                <div class="col-lg-4">
                    <div class="card_vt pb-3 mb-3">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Grid & Backup Consumption</h2>
                        </div>
                        <div class="daily-energy-vt one_tow_three " style="width:100% !important">
                            <div class="row w-100 text-center">
                                <div class="col-md-6">
                                    <p class="text-center">Daily Grid Load</p>
                                    <h3 class="one">{{$daily['dailyGridLoad']}}</h3>
                                    <p class="text-center">Monthly Grid Load </p>
                                    <h3 class="two"> {{ $monthly['gridLoad'] }}</h3>
                                    <p class="text-center">Yearly Grid Load </p>
                                    <h3 class="three">{{ $yearly['gridLoad'] }}</h3>
                                    <p class="text-center">Total Grid Load </p>
                                    <h3 class="three2">{{ $total['gridLoad'] }}</h3>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-center">Daily Backup Load</p>
                                    <h3 class="one">{{$daily['dailyBackupLoad']}}</h3>
                                    <p class="text-center">Monthly Backup Load </p>
                                    <h3 class="two">{{ $monthly['backupLoad'] }}
                                    </h3>
                                    <p class="text-center">Yearly Backup Load</p>
                                    <h3 class="three">{{ $yearly['backupLoad'] }}
                                    </h3>
                                    <p class="text-center">Total Backup Load</p>
                                    <h3 class="three2">{{ $total['backupLoad'] }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            @else
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="card-stat-vt hihht_vt p-0  mb-3">
                     

                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Consumption in Peak Hours (Battery/Grid)</h2>
                            <div class="tooltip_vt"><i class="fas fa-info"></i>
                                <span class="tooltiptext">
                                    <p class="m-0 p-0">This graph shows the contribution of grid and battery to power up your load in peak hours.</p>
                                </span>
                            </div>
                        </div>
                        <div class="day_my_btn_vt mb-3" id="history_13">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        <div class="spinner-border text-success consumptionPeakGraphSpinner plantGraphSpinner"
                             role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="consumptionPeakGraphError plantGraphError" style="display: none;">
                            <span>Some Error Occured</span>
                        </div>
                        <div class="grpaek_vt">
                            <div class="ch_one_vt" id="containerpek"></div>
                            <div class="over_totalpeakhours_vt">total Peak hours<br>Consumption <span
                                    id="total-peak-hours-consumption"></span></div>
                        </div>
                        <div class="history_gr_vt mt-0 mb-1" style="margin-top: -26px !important;">
                        </div>
                    </div>

                </div>
            @endif
            </div>

            <div class="row">
                <div class=" col-md-4">
                    <div class="card-stat-vt p-0  mb-3">
                        <div class="head_right_vt">
                            <h2 class="All-graph-heading-vt">Solar Energy Utilization</h2>
                            <div class="tooltip_vt"><i class="fas fa-info"></i>
                                <span class="tooltiptext">
                                    <p class="m-0 p-0">This graph shows the utilization of energy produced by solar panels in terms of battery charging, load and grid export.</p>
                                </span>
                            </div>
                        </div>
                        <div class="day_month_year_vt" id="solar_utiliza_day_month_year_vt_day">
                            <button class="btn_left_vt btn_left_solar_vt"><i id="solarUtilizationGraphPreviousDay" class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-solar mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="solarGraphDay" id="solarGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button class="btn_right_vt btn_right_solar_vt"><i id="solarUtilizationGraphForwardDay" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="solar_utiliza_day_month_year_vt_month" style="display: none;">
                            <button class="btn_left_vt btn_left_solar_vt"><i id="solarUtilizationGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-solar mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="solarGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_solar_vt"><i id="solarUtilizationGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="solar_utiliza_day_month_year_vt_year" style="display: none;">
                            <button class="btn_left_vt btn_left_solar_vt"><i id="solarUtilizationGraphPreviousYear" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-solar mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="solarGraphYear" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_solar_vt"><i id="solarUtilizationGraphForwardYear" class="fa fa-caret-right"></i>
                            </button>
                        </div>

                        <div class="day_my_btn_vt" id="solar_day_my_btn_vt12">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
{{--                        <div class="day_my_btn_vt mb-3" id="history_consumption_peak_13">--}}
{{--                            <button class="day_bt_vt active" id="day">day</button>--}}
{{--                            <button class="month_bt_vt" id="month">month</button>--}}
{{--                            <button class="month_bt_vt" id="year">Year</button>--}}
{{--                        </div>--}}
                        <div class="spinner-border text-success solarUtilizationGraphSpinner plantGraphSpinner"
                             role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="solarGraphUtilizationGraphError plantGraphError" style="display: none;">
                            <span>Some Error Occured</span>
                        </div>
                        <div class="gr_vt">
                            <div id="container"></div>
                            <div class="over_total_vt">total Energy<br>Generation <span
                                    id="energyGenerationValue"></span>
                            </div>
                        </div>
                    </div>
                </div>
            @if($plant->meter_type == 'Solis-Cloud')
              <div class="col-lg-4 mb-3 mb-lg-0">
                  <div class="card-stat-vt hihht_vt p-0  mb-3 mx-padding">
                      <div class="card-header">
                          <h2 class="All-graph-heading-vt" style="text-align: left;">Consumption in Peak
                              Hours
                              (Battery/Grid)</h2>
                          <div class="tooltip_vt"><i class="fas fa-info"></i>
                              <span class="tooltiptext">
                                  <p class="m-0 p-0">This graph shows the contribution of grid and battery to power up
                                      your load in peak hours.</p>
                              </span>
                          </div>
                      </div>
                      <div class="day_my_btn_vt mb-3" id="history_13">
                          <button class="day_bt_vt active" id="day">day</button>
                          <button class="month_bt_vt" id="month">month</button>
                          <button class="month_bt_vt" id="year">Year</button>
                      </div>
                      <div class="spinner-border text-success consumptionPeakGraphSpinner plantGraphSpinner"
                          role="status">
                          <span class="sr-only">Loading...</span>
                      </div>
                      <div class="consumptionPeakGraphError plantGraphError" style="display: none;">
                          <span>Some Error Occured</span>
                      </div>
                      <div class="grpaek_vt mx-height">
                          <div class="ch_one_vt" id="containerpek"></div>
                          <div class="over_totalpeakhours_vt">total Peak hours<br>Consumption <span
                                  id="total-peak-hours-consumption"></span></div>
                      </div>
                      <div class="history_gr_vt mt-0 mb-1" style="margin-top: -26px !important;">
                      </div>
                  </div>

              </div>
             @else
                <div class=" col-md-4">
                    <div class="card-stat-vt p-0  mb-3 ">
                        <div class="head_right_vt">
                            <h2 class="All-graph-heading-vt">Energy Sources</h2>
                            <div class="tooltip_vt"><i class="fas fa-info"></i>
                                <span class="tooltiptext">
                                                        <p class="m-0 p-0">This graph shows the contribution of each energy source to power your load for a specific time.</p>
                                                    </span>
                            </div>
                        </div>
                        <div class="day_month_year_vt" id="energy_source_day_month_year_vt_day">
                            <button class="btn_left_vt btn_left_energy_source_vt"><i id="energySourceGraphPreviousDay" class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-energy-source mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="energySourceGraphDay" id="energySourcerGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button class="btn_right_vt btn_right_energy_source_vt"><i id="energySourceGraphForwardDay" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="energy_source_day_month_year_vt_month" style="display: none;">
                            <button class="btn_left_vt btn_left_energy_source_vt"><i id="energySourceGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-energy-source mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="energySourceGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_energy_source_vt"><i id="energySourceGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="energy_source_day_month_year_vt_year" style="display: none;">
                            <button class="btn_left_vt btn_left_energy_source_vt"><i id="energySourceGraphPreviousYear" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-energy-source mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="energySourceGraphYear" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_energy_source_vt"><i id="energySourceGraphForwardYear" class="fa fa-caret-right"></i>
                            </button>
                        </div>

                        <div class="day_my_btn_vt" id="energy_source_day_my_btn_vt12">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
{{--                        <div class="day_my_btn_vt mb-3" id="history_day_my_btn_vt_31">--}}
{{--                            <button class="day_bt_vt active" id="day">day</button>--}}
{{--                            <button class="month_bt_vt" id="month">month</button>--}}
{{--                            <button class="month_bt_vt" id="year">Year</button>--}}
{{--                        </div>--}}
                        <div class="spinner-border text-success energySourcesGraphSpinner plantGraphSpinner"
                             role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="energySourcesGraphError plantGraphError" style="display: none;">
                            <span>Some Error Occured</span>
                        </div>
                        <div class="gr_vt">
                            <div id="container3"></div>
                            <div class="over_total_vt">total Energy<br>Consumption <span
                                    id="energySourcesGenerationValue"></span></div>
                        </div>

                    </div>
                </div>
           @endif

                <div class=" col-md-4">
                    <div class="card-stat-vt p-0  mb-3 mx-padding-bottom">
                        <div class="head_right_vt">
                            <h2 class="All-graph-heading-vt">Outages served</h2>
                        </div>
                        <div class="day_month_year_vt" id="outage_served_day_month_year_vt_day">
                            <button class="btn_left_vt btn_left_outage_served_vt"><i id="outageServedGraphPreviousDay" class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-outage-served mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="outageServedGraphDay" id="outageServedGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button class="btn_right_vt btn_right_outage_served_vt"><i id="outageServedGraphForwardDay" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="outage_served_day_month_year_vt_month" style="display: none;">
                            <button class="btn_left_vt btn_left_outage_served_vt"><i id="outageServedGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-outage-served mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="outageServedGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_outage_served_vt"><i id="outageServedGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="outage_served_day_month_year_vt_year" style="display: none;">
                            <button class="btn_left_vt btn_left_outage_served_vt"><i id="outageServedGraphPreviousYear" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-outage-served mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="outageServedGraphYear" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_outage_served_vt"><i id="outageServedGraphForwardYear" class="fa fa-caret-right"></i>
                            </button>
                        </div>

                        <div class="day_my_btn_vt" id="outage_served_day_my_btn_vt12">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
{{--                        <div class="day_my_btn_vt mb-3" id="history_day_my_btn_vt_outages_grid">--}}
{{--                            <button class="day_bt_vt active" id="day">day</button>--}}
{{--                            <button class="month_bt_vt" id="month">month</button>--}}
{{--                            <button class="month_bt_vt" id="year">Year</button>--}}
{{--                        </div>--}}
                        <div class="spinner-border text-success outagesGraphSpinner plantGraphSpinner" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="outagesSourcesGraphError plantGraphError" style="display: none;">
                            <span>Some Error Occured</span>
                        </div>
                        <div class="time_area_vt mt-3">
                            <span id="outages_hours"></span><h6>hrs</h6>
                        </div>
                        <div class="history_gr_vt mt-2">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    @if($plant->system_type != 'All on Grid')
                        <div class="card_vt p-0 mb-3 bottom-padding">

                            <div class="card-header">

                                <h2 class="All-graph-heading-vt">Energy Buy & Sell</h2>

                            </div>

                            <div class="mt-0">

                                <div class="daily-energy-vt">

                                    <div class="energy_spac_vt">
                                        <p>Daily Bought</p>
                                        <h3 class="one"> {{$daily['boughtEnergy']}}</h3>
                                        <p>Monthly Energy Bought </p>
                                        <h3 class="two"> {{$monthly['boughtEnergy']}}</h3>
                                        <p>Yearly Energy Bought </p>
                                        <h3 class="three"> {{$yearly['boughtEnergy']}}</h3>
                                        <p>Total Energy Bought </p>
                                        <h3 class="three2"> {{$total['boughtEnergy']}}</h3>
                                    </div>
                                </div>

                                <div class="daily-energy-vt">

                                    <div class="energy_spac_vt">
                                        <p>Daily Sell</p>
                                        <h3 class="one"> {{$daily['sellEnergy']}}</h3>
                                        <p>Monthly Energy Sell </p>
                                        <h3 class="two"> {{$monthly['sellEnergy']}}</h3>
                                        <p>Yearly Energy Sell</p>
                                        <h3 class="three"> {{$yearly['sellEnergy']}}</h3>
                                        <p>Total Energy Sell</p>
                                        <h3 class="three2"> {{$total['sellEnergy']}}</h3>
                                    </div>
                                </div>

                            </div>

                        </div>
                    @endif
                </div>
                <div class="col-lg-4">
                    <div class="card-stat-vt  mb-3" style="padding-bottom: 19px;">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Plant Info</h2>
                            <div class="on_off_area_vt">

                                <div class="off_img_vt">
                                    <img
                                        src="{{ $plant->faultLevel != 0 ? asset('assets/images/plant_detail_fault.png') : ($plant->is_online == 'P_Y' ? asset('assets/images/plant_detail_partial_online.png') :  ($plant->is_online == 'Y' ? asset('assets/images/plant_detail_online.png') : asset('assets/images/plant_detail_offline.png'))) }}"
                                        alt="Current"
                                        width="20">
                                </div>
                            </div>
                            @if(auth()->user()->roles == 1 || auth()->user()->roles == 2 || auth()->user()->roles == 3 ||
                            auth()->user()->roles == 4)
                                <a class="eidt-profil-vt" href="{{ url('admin/edit-plant/hybrid/'.$plant->id)}}"
                                   style="padding-top: 5px; padding-bottom: 5px;">Edit Plant </a>
                            @endif
                        </div>

                        <div class="stat-area-hed-vt" style="background: none;">

                            @if(isset($plant->company) && $plant->company && isset($plant->company->logo) &&
                            $plant->company->logo && $plant->company->logo != null)

                                <img style="position: absolute; z-index: 5;"
                                     src="{{ $plant->company && $plant->company->logo ? asset('company_logo/'.$plant->company->logo) : asset('company_logo/com_avatar.png')}}"
                                     alt="Company Logo" width="50">

                            @endif

                            <img style="    background-size: cover;
                        position: absolute;
                        z-index: 1;
                        width: 100%;
                        height: 100px;"
                                 src="{{ $plant->plant_pic ? asset('plant_photo/'.$plant->plant_pic) : asset('plant_photo/plant_avatar.png')}}"
                                 alt="Plant Picture" width="50">

                        </div>

                        @if(auth()->user()->roles == 1 || auth()->user()->roles == 2 || auth()->user()->roles == 3 ||auth()->user()->roles == 4)
                            <h2 class="stat-head-vt"><a
                                    href="{{route('admin.edit.plant', ['type' => 'hybrid','id' => $plant->id])}}">{{$plant->plant_name }}</a>
                            </h2>
                        @else
                            <h2 class="stat-head-vt"><a>{{$plant->plant_name }}</a></h2>
                        @endif

                        <p>Plant Type<span>{{ $plant->plant_type }}</span></p>

                        <p>Designed Capacity<span>{{ $plant->capacity }} kW</span></p>

                        <p>Daily Expected Generation<span>{{ $plant->expected_generation }} kWh</span></p>

                        <p>Contact<span>{{ $plant->phone }}</span></p>

                        <p>Benchmark Price<span> {{ $plant->currency }} {{ $plant->benchmark_price }}/unit</span></p>

                        <p>Company<span>{{ isset($plant->company['company_name']) ? $plant->company['company_name']:"" }}</span>
                        </p>


                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card_vt hhhhh_gvt p-0 mb-3">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Cost Savings</h2>
                        </div>
                        <div class="mt-0">
                            <div class="daily-energy-vt py-5 ">
                                <div class="day_my_btn_vt mb-3" id="cost-saving-data"
                                     style="margin-top: -59px !important;">
                                    <button class="day_bt_vt active" id="day">day</button>
                                    <button class="month_bt_vt" id="month">month</button>
                                    <button class="month_bt_vt" id="year">Year</button>
                                </div>
                                <div class="spinner-border text-success costSavingGraphSpinner plantGraphSpinner"
                                     role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div class="costSavingGraphError plantGraphError" style="display: none;">
                                    <span>Some Error Occured</span>
                                </div>
                                <div class="grtow_vt">
                                    <div id="containerpekhour"></div>
                                    <div class="overtotal_vt">Total Savings <span id="totalCostData"></span></div>
                                </div>
                            </div>

                            <div class="daily-energy-vt">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="energy_spac_vt pt-4">
                                            <p style= "height: 50px;">Peak Hours Savings</p>
                                            <p>Daily </p>
                                            <h3 class="one"> {{$daily['dailyPeakHoursSaving']}}</h3>
                                            <p>Monthly </p>
                                            <h3 class="two"> {{$monthly['monthlyPeakHoursSaving']}}</h3>
                                            <p>Yearly</p>
                                            <h3 class="three"> {{$yearly['yearlyPeakHoursSaving']}}</h3>
                                            <p>Total</p>
                                            <h3 class="three2"> {{$yearly['yearlyPeakHoursSaving']}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="energy_spac_vt pt-4">
                                            <p style= "height: 50px;">Generation Savings</p>
                                            <p>Daily </p>
                                            <h3 class="one"> {{$daily['dailyGenerationSaving']}}</h3>
                                            <p>Monthly </p>
                                            <h3 class="two"> {{$monthly['monthlyGenerationSaving']}}</h3>
                                            <p>Yearly</p>
                                            <h3 class="three"> {{$yearly['yearlyGenerationSaving']}}</h3>
                                            <p>Total</p>
                                            <h3 class="three2"> {{$yearly['yearlyGenerationSaving']}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="energy_spac_vt pt-4">
                                            <p style="height: 50px;"> Total Cost Savings</p>
                                            <p>Daily </p>
                                            <h3 class="one"> {{$daily['dailyTotalSaving']}}</h3>
                                            <p>Monthly </p>
                                            <h3 class="two"> {{$monthly['monthlyTotalSaving']}}</h3>
                                            <p>Yearly</p>
                                            <h3 class="three"> {{$yearly['yearlyTotalCostSaving']}}</h3>
                                            <p>Total</p>
                                            <h3 class="three2"> {{$yearly['yearlyTotalCostSaving']}}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-stat-vt p-0 mb-2">
                        <div class="head_right_vt">
                            <h2>Environmental Benefits</h2>
                        </div>
                        <div class="clander_left_vt">
                            <div class="day_month_year_vt" id="env_day_month_year_vt_day">
                                <button><i id="envGraphPreviousDay" class="fa fa-caret-left"></i></button>
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-env mt10">
                                    <input type="text" autocomplete="off" name="envGraphDay" id="envGraphDay"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                                <button><i id="envGraphForwardDay" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="env_day_month_year_vt_month">
                                <button><i id="envGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div
                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-env mt10">
                                        <input type="text" autocomplete="off" name="envGraphMonth" placeholder="Select"
                                               class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="envGraphForwardMonth" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="env_day_month_year_vt_year">
                                <button><i id="envGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div
                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-env mt10">
                                        <input type="text" autocomplete="off" name="envGraphYear" placeholder="Select"
                                               class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="envGraphForwardYear" class="fa fa-caret-right"></i></button>
                            </div>

                            <div class="day_my_btn_vt" id="env_day_my_btn_vt">
                                <button class="day_bt_vt active" id="day">day</button>
                                <button class="month_bt_vt" id="month">month</button>
                                <button class="month_bt_vt" id="year">Year</button>
                            </div>
                        </div>

                        <div class="row">

                            <div class="card_box_vt_sp" id="envSpinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>

                            <div class="col-lg-6 pr-0">

                                <div class="tree-planting-vt">

                                    <div class="mb-3 mb-md-0"><img src="{{ asset('assets/images/tree_planting.png')}}"
                                                                   alt="" width="50"></div>

                                    <div class="tree-vt" id="envPlantingDiv">

                                        <h6>Accumulative Trees <br>Planting</h6>

                                        <h2><samp></samp></h2>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-6 pr-0 border_left_tree_vt">

                                <div class="row tree-planting-vt">

                                    <div class="col-md-12 mb-3 mb-md-0"><img
                                            src="{{ asset('assets/images/chimney.png')}}"
                                            alt="" width="50"></div>

                                    <div class="col-md-12 tree-vt" id="envReductionDiv">

                                        <h6>Accumulative CO<sub>2</sub> <br>Emission Reduction</h6>

                                        <h3 ><samp></samp></h3>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="online-fault-vt" id="envGenerationDiv">
                            <p><samp class="color07_one_vt"></samp> Total Generation: <span> 15 kWh</span></p>
                        </div>
                    </div>
                    <div class="card-stat-vt card_wather_vt p-0  mb-3">
                        <div class="head_right_vt">
                            <h2>Current Local Weather</h2>
                        </div>
                        <div class="_locat_vt"><i class="fas fa-map-marker-alt"></i>{{$plant->city}}</div>
                        <div class="wather_ar_vt">

                            @if($weather != null)

                                @if($weather->icon != null)

                                    <div class="wather_img"><img
                                            src="http://openweathermap.org/img/w/{{ $weather->icon }}.png"
                                            alt="Current" width="80"></div>

                                @endif

                            @endif
                            <div class="font-span">
                                {{ $weather ? $weather->temperature : '--' }}
                                <sup>o</sup><span>{{ $weather ? $weather->condition : '--' }}</span>
                            </div>
                        </div>
                        <p>Sunrise<span>{{ $weather ? $weather->sunrise : '--' }}</span></p>

                        <p>Sunset<span>{{ $weather ? $weather->sunset : '--' }}</span></p>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">History</h2>
                            <button type="button" class="btn-add-vt"
                                    data-href="{{route('export.plant.data', ['plantID'=>$plant->id, 'Date'=>'2021-07-08'])}}"
                                    id="export-inverter-graph"
                                    class="ml-3 btn_add_vt btn-success btn-sm"
                                    onclick="exportTasks(event.target);">Export CSV
                            </button>
                        </div>
                        <!-- search and Dropdown -->
                        <div class="drop_search_history_vt">
                            <div class="multiselect">
                                <span>Select Parameter</span>
                                <div class="selectBox" onclick="showCheckbox();">
                                    <select>
                                    </select>
                                    <div class="overSelect"></div>
                                </div>
                                <div id="checkbox">
                                    <label for="one">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                               value="generation" checked/>Generation</label>
                                    <label for="id2">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="id2"
                                               value="consumption" checked/>Consumption</label>
                                    <label for="id3">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="id3"
                                               value="grid" checked/>Grid</label>
                                    <label for="id4">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="id4"
                                               value="buy" checked/>Buy</label>
                                    <label for="id5">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="id5"
                                               value="sell" checked/>Sell</label>
                                    <label for="id6">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="id6"
                                               value="saving"/>Cost Saving</label>
                                    @if($plant->plant_has_emi == 'Y')
                                        <label for="id7">
                                            <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                                   id="id7"
                                                   value="irradiance" checked/>Irradiance</label>
                                    @endif
                                    @if($plant->system_type_id == 4)
                                        <label for="id8" class="batteryPowerCheckBox">
                                            <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                                   id="id8"
                                                   value="battery-power" checked/>Battery Power</label>
                                        <label for="id9" class="batterySocCheckBox">
                                            <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                                   id="id9"
                                                   value="soc" checked/>Soc</label>
                                        <label for="id10" >
                                            <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                                   id="id10"
                                                   value="battery-charge" checked/>Battery Charge</label>
                                        <label for="id11">
                                            <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                                   id="id11"
                                                   value="battery-discharge" checked/>Battery Discharge</label>
                                    @endif
                                </div>
                            </div>
                            <button type="submit" id="searchHistoryCheckBox">Search</button>
                        </div>

                        <div>
                            <button id="btnConvert">Export</button>
                        </div>
                        <div class="day_month_year_vt" id="history_day_month_year_vt_day">
                            <button class="btn_left_vt"><i id="historyGraphPreviousDay" class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-history mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="historyGraphDay" id="historyGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button class="btn_right_vt"><i id="historyGraphForwardDay"
                                    class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="history_day_month_year_vt_month" style="display: none;">
                            <button class="btn_left_vt"><i id="historyGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-history mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="historyGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt"><i id="historyGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="history_day_month_year_vt_year" style="display: none;">
                            <button class="btn_left_vt"><i id="historyGraphPreviousYear" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-history mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="historyGraphYear" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt"><i id="historyGraphForwardYear" class="fa fa-caret-right"></i>
                            </button>
                        </div>

                        <div class="day_my_btn_vt" id="history_day_my_btn_vt12">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        <div class="spinner-border text-success historyGraphSpinner plantGraphSpinner" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="historyGraphError plantGraphError" style="display: none;">
                            <span>No Data Found</span>
                        </div>
                        <div class="history-card-box" dir="ltr" id="historyGraphDiv">
                            <div id="historyContainer"></div>
                            <br>
                        </div>
                        <div class="history_gr_vt">
                            <ul>
                                <li><samp class="color1_vt"></samp> Generation : <strong
                                        class="generationTotalValue"></strong></li>
                                @if($plant->system_type_id != 1)
                                    <li><samp class="color3_vt"></samp> Consumption : <strong
                                            class="consumptionTotalValue"></strong></li>
                                    <li><samp class="color2_vt"></samp> Grid : <strong class="gridTotalValue"></strong>
                                    </li>
                                    <li><samp class="color4_vt"></samp> Buy : <strong class="buyTotalValue"></strong>
                                    </li>
                                    <li><samp class="color5_vt"></samp> Sell : <strong class="sellTotalValue"></strong>
                                    </li>
                                @endif
                                @if($plant->system_type_id == 4)
                                    <li><samp class="color2_vt" style="color:#f2b610"></samp> Charge : <strong
                                            class="chargeTotalValue"></strong>
                                    </li>
                                    <li><samp class="color4_vt" style="color:#31bfbf"></samp> Discharge : <strong
                                            class="dischargeTotalValue"></strong>
                                    </li>
                                @endif
                                <li><samp class="color6_vt"></samp> Cost Saving : <strong
                                        class="savingTotalValue"></strong>
                                </li>
                                @if($plant->meter_type == 'Huawei' && $plant->plant_has_emi == 'Y')
                                    <li><samp class="color7_vt"></samp> Irradiance : <strong
                                            class="irradianceTotalValue"></strong></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <div class="card">
                        <div class="card-stat-vt card_wather_vt p-0  mb-3">
                            <div class="head_right_vt">
                                <h2>Current Local Weather</h2>
                            </div>
                        </div>

                        <div>
                            <button id="btnConvert">Export</button>
                        </div>
                        <div class="day_month_year_vt" id="weather_day_month_year_vt_day">
                            <button class="btn_left_weather_vt"><i id="weatherGraphPreviousDay"
                                                                   class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-weather mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="weatherGraphDay" id="weatherGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button class="btn_right_weather_vt"><i id="weatherGraphForwardDay"
                                                                    class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="weather_day_month_year_vt_month" style="display: none;">
                            <button class="btn_left_vt"><i id="weatherGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-weather mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="weatherGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt"><i id="weatherGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>

                        <div class="day_my_btn_vt" id="weather_day_my_btn_vt12">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                        </div>
                        <div class="spinner-border text-success weatherGraphSpinner plantGraphSpinner" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="weatherGraphError plantGraphError" style="display: none;">
                            <span>No Data Found</span>
                        </div>
                        <div class="history-card-box" dir="ltr" id="weatherGraphDiv">
                            <div id="weatherContainer"></div>
                            <br>
                        </div>
                        <div class="history_gr_vt">
                            <ul>
                                <li><samp class="color1_vt"></samp> Generation : <strong
                                        class="generationWeatherTotalValue"></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="card mb-3 history_gr_area_vt card_exp_graph" style="padding-bottom: 9px;">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Expected Generation</h2>
                        </div>
                        <div class="row history_vt">
                            <div class="col-xl-12">
                                <div class="day_month_year_vt" id="expGen_day_month_year_vt_year">
                                    <button><i id="expGenGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                    <div class="mt40">
                                        <div
                                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-expGen mt10">
                                            <input type="text" autocomplete="off" name="expGenGraphYear"
                                                   placeholder="Select" class="c-datepicker-data-input" value="">
                                        </div>
                                    </div>
                                    <button><i id="expGenGraphForwardYear" class="fa fa-caret-right"></i></button>
                                </div>

                                <div class="day_my_btn_vt" id="expGen_day_my_btn_vt">
                                    <button class="month_bt_vt active" id="year">Year</button>
                                </div>
                                <div class="card-box hig_hig_vt" dir="ltr">
                                    <div class="card_box_vt_sp" id="expGenSpinner" style="display: none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="en_gener_vt donut_chart_card">
                                        <div class="gr_text_vt">
                                            <p class="percentageActual" style="font-size: 26px;color:#68AD86;"></p>
                                            <h6 class="totalExpected" style="font-size: 19px;color:#968787;"></h6>
                                        </div>
                                        <div class="ch_one_vt" id="chartContainerGenDiv">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-8 mb-3">

                    <div class="card" style="margin-bottom: -2px;">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">tickets List</h2>
                        </div>
                        <div class="table-responsive" style="margin-top: 15px;">
                            <table class="tablesaw table mb-0 tablesaw-stack" id="products-datatable">
                                <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Title</th>
                                    <th>Source</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Due In</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                </tr>
                                </thead>
                                <tbody class="btn_a_vt pb-3">
                                @foreach ($tickets as $key => $ticket)
                                    <tr class="text-center">
                                        <td>{{$key +1}}</td>
                                        <td>{{$ticket->title}}</td>
                                        <td>{{$ticket->source_name}}</td>
                                        <td>{{$ticket->priority_name}}</td>
                                        <td>{{$ticket->status_name}}</td>
                                        <td>{{$ticket->closed_time}}</td>
                                        <td>{{$ticket->agents}}</td>
                                        <td>{{$ticket->created_at}}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card-stat-vt alerts_gra_vt p-0  mb-3">
                        <div class="head_right_vt">
                            <h2 class="All-graph-heading-vt">Alerts</h2>
                        </div>

                        <div class="clander_left_vt">
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_day">
                                <button><i id="alertGraphPreviousDay" class="fa fa-caret-left"></i></button>
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-alert mt10">
                                    <input type="text" autocomplete="off" name="alertGraphDay" id="alertGraphDay"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                                <button><i id="alertGraphForwardDay" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_month">
                                <button><i id="alertGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div
                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-alert mt10">
                                        <input type="text" autocomplete="off" name="alertGraphMonth"
                                               placeholder="Select"
                                               class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="alertGraphForwardMonth" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_year">
                                <button><i id="alertGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div
                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-alert mt10">
                                        <input type="text" autocomplete="off" name="alertGraphYear" placeholder="Select"
                                               class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="alertGraphForwardYear" class="fa fa-caret-right"></i></button>
                            </div>

                            <div class="day_my_btn_vt" id="alert_day_my_btn_vt">
                                <button class="day_bt_vt active" id="day">day</button>
                                <button class="month_bt_vt" id="month">month</button>
                                <button class="month_bt_vt" id="year">Year</button>
                            </div>
                        </div>
                        <div class="spinner-border text-primary" id="alertSpinner" role="status" style="display: none;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="noRecord" style="display: none;">
                        NO ALERTS to SHOW
                    </span>
                        <div id="alertChartDiv" class="mt-5">
                            <div id="alertChart" style="width: 320px; height: 201px;"></div>
                        </div>
                        <div class="online-fault-vt" id="alertChartDetailDiv">
                            <p><samp class="color03_one_vt"></samp> Fault: <span> </span></p>
                            <p><samp class="color04_one_vt"></samp> Alarm: <span> </span></p>
                            <p><samp class="color05_one_vt"></samp> RTU: <span> </span></p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="">
                            <div class="card-header">
                                <h2 class="All-graph-heading-vt">Device List</h2>
                            </div>
                            <div class="table-responsive" style="margin-top: 15px;">
                                <table class="tablesaw table mb-0 tablesaw-stack" id="products-datatable">
                                    <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Serial Number</th>
                                        <th>Temperature</th>
                                        <th>Battery Total Capacity</th>
                                        <th>Battery Remaining Capacity</th>
                                        <th>Daily Generation</th>
                                        <th>Last Alerts</th>
                                        <th>Last Update</th>
                                    </tr>
                                    </thead>
                                    <tbody class="btn_a_vt">

                                    @if(count($daily_inverter_details) > 0)

                                        @foreach($daily_inverter_details as $key => $inverter)

                                            <tr class="text-center">
                                                <td>
                                                    @if($inverter->site_status == 'Y')
                                                        <img src="{{ asset('assets/images/icon_plant_check_vt.svg')}}"
                                                             alt="check"
                                                             title="Online">
                                                    @else
                                                        <img src="{{ asset('assets/images/icon_plant_vt.svg')}}"
                                                             alt="check"
                                                             title="Offline">
                                                    @endif
                                                </td>
                                                <td>{{$inverter->type}}</td>

                                                <td>
                                                    <?php
                                                    $serialNo = 0;
                                                    if ($inverter->inverter_serial_number) {
                                                        $serialNo = $inverter->inverter_serial_number;
                                                    }?>

                                                    <a href="{{ route('admin.plant.inverter.detail', ['type' => 'hybrid','plantId' => $plant->id, 'id' => $serialNo])}}">
                                                        {{ $inverter->serial_no != null ? $inverter->serial_no : '00000000'}}
                                                    </a>
                                                    <br>

                                                </td>
                                                <td>{{ $inverter->temperature}} &#176C</td>
                                                <td>{{ $inverter->battery_capacity}}</td>
                                                <td>{{ $inverter->battery_remaining}}</td>
                                                <td>
                                                    @if($inverter->daily_generation != '--')
                                                        {{ $inverter->daily_generation ? $inverter->daily_generation : 0 }}
                                                        kWh
                                                    @else
                                                        {{$inverter->daily_generation}}
                                                    @endif
                                                    <br>
                                                </td>
                                                <td>
                                                    @if($inverter->last_alert != '-----')
                                                        {{ date('d',strtotime($inverter->last_alert)).'-'.substr(date('F', mktime(0, 0, 0, (int)date('m',strtotime($inverter->last_alert)), 10)), 0, 3).' '.date('h:i A',strtotime($inverter->last_alert)) }}
                                                        <br>
                                                    @else
                                                        {{$inverter->last_alert}}<br>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ date('d',strtotime($plant->updated_at)).'-'.substr(date('F', mktime(0, 0, 0, (int)date('m',strtotime($plant->updated_at)), 10)), 0, 3).' '.date('h:i A',strtotime($plant->updated_at)) }}
                                                    <br>
                                                </td>

                                        @endforeach

                                    @else

                                        <tr class="text-center">

                                            <td colspan="4">Inverter list not found.</td>

                                        </tr>

                                    @endif

                                    </tbody>

                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">

                </div>
            </div>
        </section>
    </div>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>


    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.0.0/dist/echarts.min.js"></script>
    <script type="text/javascript">
        var plant_axis_grid = 4;
        var filterss_arr = {};
        var plant_name = [{!! $plant->id !!}];
        var comm_fail = {!! $current['comm_fail'] !!};
        var month_name_arr = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        $(document).ready(function () {
            var batteryPowerData = {!! json_encode($current['battery_power']) !!};
            var batteryTypeData = {!! json_encode($current['battery_type']) !!};
            var batteryCapacityData = {!! json_encode($current['battery_capacity'])!!};

            function chargebattery() {
                let childData = document.getElementById('battery-detail-data').children[0].getAttribute('id');
                var a;
                a = document.getElementById(childData);
                // if(childData === 'div25')
                // {
                if (childData !== 'div2') {
                    if (a) {
                        a.innerHTML = "&#xf244;";
                    }
                }
                if (childData !== 'div2' && batteryPowerData !== 0 && batteryTypeData === '-ve') {

                    setTimeout(function () {
                        a.innerHTML = "&#xf243;";
                    }, 1000);
                    setTimeout(function () {
                        a.innerHTML = "&#xf242;";
                    }, 2000);
                    setTimeout(function () {
                        a.innerHTML = "&#xf241;";
                    }, 3000);
                    setTimeout(function () {
                        a.innerHTML = "&#xf240;";
                    }, 4000);
                } else {

                }

            }

            chargebattery();
            setInterval(chargebattery, 5000);
            var plantId = {!! $plant->id !!};
            var metertype = {!! json_encode($plant->meter_type) !!};
            var autoloadTime = {!! $autoloadTime !!};
            var systemType = {!! json_encode($plant->system_type) !!};
            var systemTypeId = {!! json_encode($plant->system_type_id) !!};
            var plantGenerationLog = {!! json_encode($plant->generation_log) !!};
            var autloadTimeCalculation = autoloadTime * 60 * 1000;
            setInterval(function () {
                $.ajax({
                    type: 'GET',
                    data: {
                        'plantId': plantId,
                    },
                    url: "{{ route('plant.real.time.data') }}",
                    success: function (response) {
                        if (response.status) {

                            document.getElementById('real-time-generation').innerText = response.data.generation;
                            document.getElementById('real-time-backup-load').innerText = response.data.backupLoad;
                            document.getElementById('real-time-consumption').innerText = response.data.consumption;
                            document.getElementById('real-time-battery-data').innerText = response.data.battery_power_data;
                            document.getElementById('real-time-grid').innerText = response.data.grid;
                            document.getElementById('real-time-energy-flow-graph').innerHTML = '';
                            let gridImport = '';
                            let animationGeneration = '';
                            let animationConsumption = '';
                            let batteryAnimationData = '';
                            let systemAnimationData = '';
                            let meterTypeAmimation = '';
                            let gridAnimationData = '';
                            let systemAnimationconsumption = '';
                            let systemAnimationbackup = '';
                            let systemTypeGenerationData = '';
                            let systemTypeGrid = '';
                            let plantGenerationData = '';
                            if (response.data.grid !== 0) {
                                if (response.data.grid_type === '+ve') {
                                    gridImport = `<div class="size_power active-animatioon"></div>`;
                                } else if (response.data.grid_type === '-ve') {
                                    gridImport = `<div class="size_power1 active-animatioon"></div>`;
                                }
                            } else {
                                gridImport = `<div class="size_power_off active-animatioon"></div>

                                            <span class="comm_fail" style="font-size: 9px; margin-left:2px;"></span>`;
                            }
                            if (response.data.current_generation_data === 0) {
                                animationGeneration = '<div class="size_generation_off active-animatioon"></div>';
                            } else {
                                animationGeneration = '<div class="size_generation active-animatioon"></div>';
                            }
                            if (response.data.current_consumption_data === 0) {
                                animationConsumption = '<div class="size_generation_off active-animatioon"></div>';
                            } else {
                                animationConsumption = '<div class="size_generation active-animatioon"></div>';
                            }

                            if (response.data.battery_power === 0 || response.data.battery_power == null) {
                                batteryAnimationData = '<div class="battery_power_off active-animatioon"></div>';
                            } else {
                                if (response.data.battery_type === '+ve') {
                                    batteryAnimationData = '<div class="battery_power1 active-animatioon"></div>';
                                } else if (response.data.battery_type === '-ve') {
                                    batteryAnimationData = '<div class="battery_power active-animatioon"></div>';
                                } else {
                                    batteryAnimationData = '<div class="battery_power_off active-animatioon"></div>';
                                }

                            }

                            if (systemTypeId === 4) {
                                systemAnimationData = ` 
                                <div class="single-battery-row-vt">
                                    <div class="single-battery-vt">
                                        <div class="batter_icon_text">
                                            <img src="{{ asset('assets/images/battery.png')}}" alt="tower"
                                                width="35">
                                                <h4>battery</h4>
                                                <span>${response.data.battery_power_data}</span>
                                        </div>
                                    ${batteryAnimationData}
                                    </div>`;
                            }
                            if (response.data.generation === 0) {
                                systemTypeGenerationData = `<div class="plant_consumption_off active-animatioon"></div>`;
                            } else {
                                systemTypeGenerationData = `<div class="plant_consumption active-animatioon"></div>`;
                            }
                            if (systemType !== 'All on Grid') {
                                systemTypeGrid = `<span>${response.data.consumption}</span>`;
                            } else {

                                systemTypeGrid = `<span>${response.data.generation}</span>`;
                            }
                            if (plantGenerationLog) {
                                plantGenerationData = `<div class="plant_consumption_off active-animatioon"></div>`;
                            } else {
                                plantGenerationData = `<div class="plant_consumption active-animatioon"></div>`;
                            }
                            if (systemType !== 'All on Grid') {
                                gridAnimationData += `<div class="single-dashboard-vt">
                                <div class="single-dashb_vt">

                                    <div class="single-dashboard-row-vt">

                                        <img src="{{ asset('assets/images/tower.png') }}" alt="tower" width="45">

                                        <div class="single-area-vt">

                                            <h4 style="padding-left: 15px;">Grid</h4>

                                            <span>${response.data.grid}</span>
                                        ${gridImport}
                                    </div>

                            </div>
                            </div>

                                </div>

                                <div class="single-dashboard-tow-vt">
                                    <img src="{{ asset('assets/images/sensor.png')}}" alt="sensor" class="img" width="45">
                                    </div>
                                    <div class="single-dashboard-row-vt">
                                        <img src="{{ asset('assets/images/power.png')}}" alt="tower" width="45">
                                        <div class="single-area-vt">
                                            <h4>Generation</h4>
                                            <span>${response.data.generation}</span>${animationGeneration}
                                        </div>
                                    </div>`
                                    if(metertype === "Solis-Cloud"){
                                        meterTypeAmimation = `<div class="home_power_area_vt">
                                            <div class="home_imgtext_vt home_imgtext_vtt">
                                                <span></span>
                                                <div class="row wrap-none">
                                                    <div class="col-lg-6">
                                                        <h4>Grid Consumption</h4>
                                                        <img src="{{ asset('assets/images/grid-com.png') }}"
                                                            alt="tower" width="45">
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <h4>Backup Consumption</h4>
                                                        <img src="{{ asset('assets/images/home.png') }}"
                                                            alt="tower" width="45" style="margin-left:10px">
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="margin-top:51px">`
                                                if(response.data.consumption === 0){
                                                systemAnimationconsumption = `<div
                                                    class="sizes_generations_offs active-animatioon">
                                                    <span
                                                        class="text-light margins-top-left">${response.data.consumption}</span>
                                                </div>
                                                <!-- <div class="resize_generation_off_div active-animatioon"></div>   -->`
                                                }else{
                                                systemAnimationconsumption = `<div
                                                    class="sizes_generations active-animatioon">
                                                    <span
                                                        class="text-light margin-top-left">${response.data.consumption}</span>
                                                </div>
                                                <div class="resizes_generations_off_div active-animatioon"></div>`
                                                }
                                                if(response.data.backupLoad === 0){
                                                systemAnimationbackup = `<div
                                                    class="resize_generation_off active-animatioon">
                                                    <span
                                                        class="text-light margins-left">${response.data.backupLoad}</span>
                                                </div>`
                                                }else{
                                                systemAnimationbackup = `<div
                                                    class="resizes_generations_off active-animatioon">
                                                    <span
                                                        class="text-light margin-left">${response.data.backupLoad}</span>
                                                </div>
                                                <div class="resizes_generations_off_div active-animatioon"></div>`
                                                }
                                                `
                                            </div>
                                            <!-- <div class="size_generation active-animatioon"></div> -->
                                        </div>`;
                                    }else{
                                          meterTypeAmimation = 
                                    `<div class="home_powerarea_vt">
                                        <div class="home_power_area_vt">
                                            <div class="home_imgtext_vt">
                                                <span>${response.data.consumption}</span>
                                                <h4>Consumption</h4>
                                                <img src="{{ asset('assets/images/home.png') }}" alt="tower" width="45">
                                            </div> ${animationConsumption}
                                            {{-- <div class="size_generation active-animatioon"></div> --}}
                                        </div>
                                    </div>`;
                                    }
                                `${systemAnimationData}</div>
                            </div>`
                            } else {
                                gridAnimationData += `
                                <div class="single_dashboard_vt">
                                    <div class="plant_single_dashb_vt">
                                        <div class="single_one_vt">
                                    <img src="{{ asset('assets/images/power.png')}}" alt="tower" width="45">
                                    <img src="{{ asset('assets/images/power.png')}}" alt="tower" width="45">

                                            <img src="{{ asset('assets/images/power.png')}}" alt="tower" width="45">

                                    <div class="img_text_vt">
                                    <div class="img_text_vt">

                                            <div class="img_text_vt">

                                                <h4>Generation</h4>
                                                <span>${response.data.generation}/span>${systemTypeGenerationData}
                                            </div>
                                        </div>
                                        <div class="img_con_vt"><img src="{{ asset('assets/images/sensor.png')}}" alt="tower"width="45"></div>
                                        <div class="single_one_vt">
                                            <div class="img_text_vt">
                                                <h4>Consumption</h4> ${systemTypeGrid}
                                                ${plantGenerationData}
                               </div>
                               </div>

                                            </div>

                               <img src="{{ asset('assets/images/home.png')}}" alt="tower" width="45">
                               <img src="{{ asset('assets/images/home.png')}}" alt="tower" width="45">

                                            <img src="{{ asset('assets/images/home.png')}}" alt="tower" width="45">

                                </div>
                                </div>

                                        </div>

                                    </div>
                                </div>`;
                            }
                            document.getElementById('real-time-energy-flow-graph').innerHTML = gridAnimationData +
                            meterTypeAmimation + systemAnimationconsumption + systemAnimationData +
                            systemAnimationbackup;
                            console.log(gridAnimationData +
                            meterTypeAmimation + systemAnimationconsumption + systemAnimationData +
                            systemAnimationbackup);
                        }
                    },
                });
            }, autloadTimeCalculation);

            if (comm_fail == 1) {

                $('.comm_fail').html('Power Outage or Communication Failure');
            }

            $('.clickable').click(function () {
                window.location.href = $(this).attr('href');
            });

            $('.clickable').click(function () {
                window.location.href = $(this).attr('href');
            });

            var currDate = getCurrentDate();

            $('input[name="historyGraphDay"]').val(currDate.todayDate);
            $('input[name="solarGraphDay"]').val(currDate.todayDate);
            $('input[name="energySourceGraphDay"]').val(currDate.todayDate);
            $('input[name="outageServedGraphDay"]').val(currDate.todayDate);
            $('input[name="historyGraphMonth"]').val(currDate.todayMonth);
            $('input[name="solarGraphMonth"]').val(currDate.todayMonth);
            $('input[name="energySourceGraphMonth"]').val(currDate.todayMonth);
            $('input[name="outageServedGraphMonth"]').val(currDate.todayMonth);
            $('input[name="historyGraphYear"]').val(currDate.todayYear);
            $('input[name="solarGraphYear"]').val(currDate.todayYear);
            $('input[name="energySourceGraphYear"]').val(currDate.todayYear);
            $('input[name="outageServedGraphYear"]').val(currDate.todayYear);
            $('input[name="expGenGraphYear"]').val(currDate.todayYear);
            $('input[name="envGraphDay"]').val(currDate.todayDate);
            $('input[name="envGraphMonth"]').val(currDate.todayMonth);
            $('input[name="envGraphYear"]').val(currDate.todayYear);
            $('input[name="weatherGraphDay"]').val(currDate.todayDate);
            $('input[name="weatherGraphMonth"]').val(currDate.todayMonth);
            $('input[name="weatherGraphYear"]').val(currDate.todayYear);
            $('input[name="alertGraphDay"]').val(currDate.todayDate);
            $('input[name="alertGraphMonth"]').val(currDate.todayMonth);
            $('input[name="alertGraphYear"]').val(currDate.todayYear);

            var types = 'generation';
            var history_date = $('input[name="historyGraphDay"]').val();
            var history_time = 'day';
            var solar_date = $('input[name="solarGraphDay"]').val();
            var solar_time = 'day';
            var expGen_date = $('input[name="expGenGraphYear"]').val();
            var expGen_time = 'year';
            var env_date = $('input[name="envGraphDay"]').val();
            var env_time = 'day';
            var weather_date = $('input[name="weatherGraphDay"]').val();
            var weather_time = 'day';
            var alert_date = $('input[name="alertGraphDay"]').val();
            var alert_time = 'day';
            var id = {!!$plant->id!!};
            changeExpGenDayMonthYear(expGen_date, expGen_time);
            changeENVDayMonthYear();
            changeAlertDayMonthYear();
            // historyGraphAjax( history_date, history_time,types);
            consumptionGraphAjax(history_date, history_time);
            solarEnergyGraphAjax(history_date, history_time);
            solarEnergySourceGraphAjaxData(history_date, history_time);
            costSavingDataAjax(history_date, history_time);
            // weatherGraphAjax(history_date, history_time)
            OutagesGraphAjax(history_date, history_time);
            expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            envGraphAjax(id, env_date, env_time);
            alertGraphAjax(id, alert_date, alert_time);
            var historyUnitArray = new Array();
            var historyUnit = '';
            var weatherUnitArray = new Array();
            var weatherUnit = '';

            historyCheckBoxArray = $("input[name='historyCheckBox[]']:checked").map(function () {

                if ($(this).val() == 'generation') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'consumption') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'grid') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'buy') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'sell') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'saving') {

                    historyUnit = 'PKR';
                } else if ($(this).val() == 'irradiance') {

                    historyUnit = 'W/m2';
                } else if ($(this).val() == 'battery-power') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'soc') {

                    historyUnit = '%';
                } else if ($(this).val() == 'battery-charge') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'battery-discharge') {

                    historyUnit = 'kW';
                }

                if (historyUnitArray.indexOf(historyUnit) === -1) {

                    historyUnitArray.push(historyUnit);
                }

                if (historyUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }

            }).get();

            $('.historyCheckBox').change(function () {

                var historyUnitArray = new Array();
                var historyUnit = '';

                historyCheckBoxArray = $("input[name='historyCheckBox[]']:checked").map(function () {

                    if ($(this).val() == 'generation') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'consumption') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'grid') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'buy') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'sell') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'saving') {

                        historyUnit = 'PKR';
                    } else if ($(this).val() == 'irradiance') {

                        historyUnit = 'W/m2';
                    } else if ($(this).val() == 'battery-power') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'soc') {

                        historyUnit = '%';
                    } else if ($(this).val() == 'battery-charge') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'battery-discharge') {

                        historyUnit = 'kW';
                    }
                    if (historyUnitArray.indexOf(historyUnit) === -1) {

                        historyUnitArray.push(historyUnit);
                    }

                    if (historyUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
            });
            weatherCheckBoxArray = $("input[name='weatherCheckBox[]']:checked").map(function () {

                if ($(this).val() == 'generation') {

                    weatherUnit = 'kW';
                } else if ($(this).val() == 'consumption') {

                    weatherUnit = 'kW';
                } else if ($(this).val() == 'grid') {

                    weatherUnit = 'kW';
                } else if ($(this).val() == 'buy') {

                    weatherUnit = 'kW';
                } else if ($(this).val() == 'sell') {

                    weatherUnit = 'kW';
                } else if ($(this).val() == 'saving') {

                    weatherUnit = 'PKR';
                } else if ($(this).val() == 'irradiance') {

                    weatherUnit = 'W/m2';
                } else if ($(this).val() == 'battery-power') {

                    weatherUnit = 'kW';
                } else if ($(this).val() == 'soc') {

                    weatherUnit = '%';
                } else if ($(this).val() == 'battery-charge') {

                    weatherUnit = 'kW';
                } else if ($(this).val() == 'battery-discharge') {

                    weatherUnit = 'kW';
                }

                if (weatherUnitArray.indexOf(weatherUnit) === -1) {

                    weatherUnitArray.push(weatherUnit);
                }

                if (weatherUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }

            }).get();

            $('.weatherCheckBox').change(function () {

                var weatherUnitArray = new Array();
                var weatherUnit = '';

                weatherCheckBoxArray = $("input[name='weatherCheckBox[]']:checked").map(function () {

                    if ($(this).val() == 'generation') {

                        weatherUnit = 'kW';
                    } else if ($(this).val() == 'consumption') {

                        weatherUnit = 'kW';
                    } else if ($(this).val() == 'grid') {

                        weatherUnit = 'kW';
                    } else if ($(this).val() == 'buy') {

                        weatherUnit = 'kW';
                    } else if ($(this).val() == 'sell') {

                        weatherUnit = 'kW';
                    } else if ($(this).val() == 'saving') {

                        weatherUnit = 'PKR';
                    } else if ($(this).val() == 'irradiance') {

                        weatherUnit = 'W/m2';
                    } else if ($(this).val() == 'battery-power') {

                        weatherUnit = 'kW';
                    } else if ($(this).val() == 'soc') {

                        weatherUnit = '%';
                    } else if ($(this).val() == 'battery-charge') {

                        weatherUnit = 'kW';
                    } else if ($(this).val() == 'battery-discharge') {

                        weatherUnit = 'kW';
                    }
                    if (weatherUnitArray.indexOf(weatherUnit) === -1) {

                        weatherUnitArray.push(weatherUnit);
                    }

                    if (weatherUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
            });
            changeHistoryDayMonthYear(history_date, history_time, historyCheckBoxArray);
            changeWeatherDayMonthYear(history_date, history_time, historyCheckBoxArray);
            // console.log(document.getElementById('battery-detail-data').children[0].getAttribute('id'))


            // $('#graphNavLink li a').click(function() {
            //
            //     $(this).parent().toggleClass('active').siblings().removeClass('active');
            //
            //     if ($(this).html() == 'Generation') {
            //
            //         types = 'generation';
            //     } else if ($(this).html() == 'Consumption') {
            //
            //         types = 'consumption';
            //     } else if ($(this).html() == 'Buy') {
            //
            //         types = 'buy';
            //     } else if ($(this).html() == 'Sell') {
            //
            //         types = 'sell';
            //     } else if ($(this).html() == 'Cost Saving') {
            //
            //         types = 'saving';
            //     }
            //     changeHistoryDayMonthYear(types, id, history_date, history_time);
            //
            // });

            /*$('.J-yearMonthDayPicker-single-priority').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function(type) {
                    changePriorityDayMonthYear(id, this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-priority').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changePriorityDayMonthYear(id, this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-priority').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changePriorityDayMonthYear(id, this.$input.eq(0).val(), 'year');
                }
            });*/

            $('.J-yearMonthDayPicker-single-history').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeHistoryDayMonthYear(this.$input.eq(0).val(), 'day', historyCheckBoxArray);
                }
            });

            $('.J-yearMonthPicker-single-history').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeHistoryDayMonthYear(this.$input.eq(0).val(), 'month', historyCheckBoxArray);
                }
            });

            $('.J-yearPicker-single-history').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeHistoryDayMonthYear(this.$input.eq(0).val(), 'year', historyCheckBoxArray);
                }
            });
            $('.J-yearMonthDayPicker-single-solar').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeSolarUtilizationDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-solar').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeSolarUtilizationDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-solar').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeSolarUtilizationDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });
            $('.J-yearMonthDayPicker-single-energy-source').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeEnergySourceDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-energy-source').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeEnergySourceDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-energy-source').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeEnergySourceDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });
            $('.J-yearMonthDayPicker-single-outage-served').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeOutageServedDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-outage-served').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeOutageServedDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-outage-served').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeOutageServedDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });
            $('.J-yearMonthDayPicker-single-weather').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeWeatherDayMonthYear(this.$input.eq(0).val(), 'day', historyCheckBoxArray);
                }
            });

            $('.J-yearMonthPicker-single-weather').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeWeatherDayMonthYear(this.$input.eq(0).val(), 'month', historyCheckBoxArray);
                }
            });

            $('.J-yearPicker-single-weather').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeWeatherDayMonthYear(this.$input.eq(0).val(), 'year', historyCheckBoxArray);
                }
            });

            $('.J-yearPicker-single-expGen').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-env').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeENVDayMonthYear(id, this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-env').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeENVDayMonthYear(id, this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-env').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeENVDayMonthYear(id, this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-alert').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-alert').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-alert').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'year');
                }
            });

            /*$('#priorityGraphPreviousDay').on('click', function() {

                show_date = $("input[name='priorityGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                priority_date = formatDate(datess);
                $('input[name="priorityGraphDay"]').val('');
                $('input[name="priorityGraphDay"]').val(priority_date);
                console.log($("input[name='priorityGraphDay']").val());
                priority_time = 'day';
                priorityGraphAjax(id, priority_date, priority_time);
            });

            $('#priorityGraphForwardDay').on('click', function() {

                show_date = $("input[name='priorityGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                priority_date = formatDate(datess);
                $('input[name="priorityGraphDay"]').val('');
                $('input[name="priorityGraphDay"]').val(priority_date);
                console.log($("input[name='priorityGraphDay']").val());
                priority_time = 'day';
                priorityGraphAjax(id, priority_date, priority_time);
            });

            $('#priorityGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='priorityGraphMonth']").val();
                priority_date = formatPreviousMonth(show_date);
                $('input[name="priorityGraphMonth"]').val('');
                $('input[name="priorityGraphMonth"]').val(priority_date);
                console.log($("input[name='priorityGraphMonth']").val());
                priority_time = 'month';
                priorityGraphAjax(id, priority_date, priority_time);
            });

            $('#priorityGraphForwardMonth').on('click', function() {

                show_date = $("input[name='priorityGraphMonth']").val();
                priority_date = formatForwardMonth(show_date);
                $('input[name="priorityGraphMonth"]').val('');
                $('input[name="priorityGraphMonth"]').val(priority_date);
                console.log($("input[name='priorityGraphMonth']").val());
                priority_time = 'month';
                priorityGraphAjax(id, priority_date, priority_time);
            });

            $('#priorityGraphPreviousYear').on('click', function() {

                show_date = $("input[name='priorityGraphYear']").val();
                priority_date = formatPreviousYear(show_date);
                $('input[name="priorityGraphYear"]').val('');
                $('input[name="priorityGraphYear"]').val(priority_date);
                console.log($("input[name='priorityGraphYear']").val());
                priority_time = 'year';
                priorityGraphAjax(id, priority_date, priority_time);
            });

            $('#priorityGraphForwardYear').on('click', function() {

                show_date = $("input[name='priorityGraphYear']").val();
                priority_date = formatForwardYear(show_date);
                $('input[name="priorityGraphYear"]').val('');
                $('input[name="priorityGraphYear"]').val(priority_date);
                console.log($("input[name='priorityGraphYear']").val());
                priority_time = 'year';
                priorityGraphAjax(id, priority_date, priority_time);
            });*/
            $('#searchHistoryCheckBox').click(function () {

                showCheckbox();

                var historyTimeValue = $('#history_day_my_btn_vt12').find('button.active').attr('id');

                historyGraphAjax($('input[name="historyGraphDay"]').val(), historyTimeValue, historyCheckBoxArray);
            });
            $('#searchWeatherCheckBox').click(function () {

                showWeatherCheckbox();

                var weatherTimeValue = $('#weather_day_my_btn_vt12').find('button.active').attr('id');
                weatherGraphAjax($('input[name="weatherGraphDay"]').val(), weatherTimeValue, weatherCheckBoxArray);
            });
            $('#historyGraphPreviousDay').on('click', function () {

                show_date = $("input[name='historyGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                history_date = formatDate(datess);
                $('input[name="historyGraphDay"]').val('');
                $('input[name="historyGraphDay"]').val(history_date);
                console.log($("input[name='historyGraphDay']").val());
                history_time = 'day';
                $('.batteryPowerCheckBox').show();
                $('.batterySocCheckBox').show();
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphForwardDay').on('click', function () {

                show_date = $("input[name='historyGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                history_date = formatDate(datess);
                $('input[name="historyGraphDay"]').val('');
                $('input[name="historyGraphDay"]').val(history_date);
                console.log($("input[name='historyGraphDay']").val());
                history_time = 'day';
                $('.batteryPowerCheckBox').show();
                $('.batterySocCheckBox').show();
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='historyGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="historyGraphMonth"]').val('');
                $('input[name="historyGraphMonth"]').val(history_date);
                console.log($("input[name='historyGraphMonth']").val());
                history_time = 'month';
                let forDeletion = ["battery-power", "soc"]
                historyCheckBoxArray = historyCheckBoxArray.filter(item => !forDeletion.includes(item))
                $('.batteryPowerCheckBox').hide();
                $('.batterySocCheckBox').hide();
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphForwardMonth').on('click', function () {

                show_date = $("input[name='historyGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="historyGraphMonth"]').val('');
                $('input[name="historyGraphMonth"]').val(history_date);
                console.log($("input[name='historyGraphMonth']").val());
                history_time = 'month';
                let forDeletion = ["battery-power", "soc"]
                historyCheckBoxArray = historyCheckBoxArray.filter(item => !forDeletion.includes(item))
                $('.batteryPowerCheckBox').hide();
                $('.batterySocCheckBox').hide();
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphPreviousYear').on('click', function () {

                show_date = $("input[name='historyGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="historyGraphYear"]').val('');
                $('input[name="historyGraphYear"]').val(history_date);
                console.log($("input[name='historyGraphYear']").val());
                history_time = 'year';
                let forDeletion = ["battery-power", "soc"]
                historyCheckBoxArray = historyCheckBoxArray.filter(item => !forDeletion.includes(item))
                $('.batteryPowerCheckBox').hide();
                $('.batterySocCheckBox').hide();
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphForwardYear').on('click', function () {

                show_date = $("input[name='historyGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="historyGraphYear"]').val('');
                $('input[name="historyGraphYear"]').val(history_date);
                console.log($("input[name='historyGraphYear']").val());
                history_time = 'year';
                let forDeletion = ["battery-power", "soc"]
                historyCheckBoxArray = historyCheckBoxArray.filter(item => !forDeletion.includes(item))
                $('.batteryPowerCheckBox').hide();
                $('.batterySocCheckBox').hide();
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
                exportCsvDataValues(history_date, history_time, types);
            });
            $('#searchHistoryCheckBox').click(function () {

                showCheckbox();

                var historyTimeValue = $('#history_day_my_btn_vt12').find('button.active').attr('id');

                historyGraphAjax($('input[name="solarGraphDay"]').val(), historyTimeValue, historyCheckBoxArray);
            });
            $('#solarUtilizationGraphPreviousDay').on('click', function () {

                show_date = $("input[name='solarGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                history_date = formatDate(datess);
                $('input[name="solarGraphDay"]').val('');
                $('input[name="solarGraphDay"]').val(history_date);
                console.log($("input[name='solarGraphDay']").val());
                history_time = 'day';
                solarEnergyGraphAjax(history_date, history_time);
            });

            $('#solarUtilizationGraphForwardDay').on('click', function () {

                show_date = $("input[name='solarGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                history_date = formatDate(datess);
                $('input[name="solarGraphDay"]').val('');
                $('input[name="solarGraphDay"]').val(history_date);
                console.log($("input[name='solarGraphDay']").val());
                history_time = 'day';
                solarEnergyGraphAjax(history_date, history_time);
            });

            $('#solarUtilizationGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='solarGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="solarGraphMonth"]').val('');
                $('input[name="solarGraphMonth"]').val(history_date);
                console.log($("input[name='solarGraphMonth']").val());
                history_time = 'month';
                solarEnergyGraphAjax(history_date, history_time);
            });

            $('#solarUtilizationGraphForwardMonth').on('click', function () {

                show_date = $("input[name='solarGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="solarGraphMonth"]').val('');
                $('input[name="solarGraphMonth"]').val(history_date);
                console.log($("input[name='solarGraphMonth']").val());
                history_time = 'month';
                solarEnergyGraphAjax(history_date, history_time);
            });

            $('#solarUtilizationGraphPreviousYear').on('click', function () {

                show_date = $("input[name='solarGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="solarGraphYear"]').val('');
                $('input[name="solarGraphYear"]').val(history_date);
                console.log($("input[name='solarGraphYear']").val());
                history_time = 'year';
                solarEnergyGraphAjax(history_date, history_time);
            });

            $('#solarUtilizationGraphForwardYear').on('click', function () {

                show_date = $("input[name='solarGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="solarGraphYear"]').val('');
                $('input[name="solarGraphYear"]').val(history_date);
                console.log($("input[name='solarGraphYear']").val());
                history_time = 'year';
                solarEnergyGraphAjax(history_date, history_time);
            });
            $('#energySourceGraphPreviousDay').on('click', function () {

                show_date = $("input[name='energySourceGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                history_date = formatDate(datess);
                $('input[name="energySourceGraphDay"]').val('');
                $('input[name="energySourceGraphDay"]').val(history_date);
                console.log($("input[name='energySourceGraphDay']").val());
                history_time = 'day';
                solarEnergySourceGraphAjaxData(history_date, history_time);
            });

            $('#energySourceGraphForwardDay').on('click', function () {

                show_date = $("input[name='energySourceGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                history_date = formatDate(datess);
                $('input[name="energySourceGraphDay"]').val('');
                $('input[name="energySourceGraphDay"]').val(history_date);
                console.log($("input[name='energySourceGraphDay']").val());
                history_time = 'day';
                solarEnergySourceGraphAjaxData(history_date, history_time);
            });

            $('#energySourceGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='energySourceGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="energySourceGraphMonth"]').val('');
                $('input[name="energySourceGraphMonth"]').val(history_date);
                console.log($("input[name='energySourceGraphMonth']").val());
                history_time = 'month';
                solarEnergySourceGraphAjaxData(history_date, history_time);
            });

            $('#energySourceGraphForwardMonth').on('click', function () {

                show_date = $("input[name='energySourceGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="energySourceGraphMonth"]').val('');
                $('input[name="energySourceGraphMonth"]').val(history_date);
                console.log($("input[name='energySourceGraphMonth']").val());
                history_time = 'month';
                solarEnergySourceGraphAjaxData(history_date, history_time);
            });

            $('#energySourceGraphPreviousYear').on('click', function () {

                show_date = $("input[name='energySourceGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="energySourceGraphYear"]').val('');
                $('input[name="energySourceGraphYear"]').val(history_date);
                console.log($("input[name='energySourceGraphYear']").val());
                history_time = 'year';
                solarEnergySourceGraphAjaxData(history_date, history_time);
            });

            $('#energySourceGraphForwardYear').on('click', function () {

                show_date = $("input[name='energySourceGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="energySourceGraphYear"]').val('');
                $('input[name="energySourceGraphYear"]').val(history_date);
                console.log($("input[name='energySourceGraphYear']").val());
                history_time = 'year';
                solarEnergySourceGraphAjaxData(history_date, history_time);
            });

            $('#outageServedGraphPreviousDay').on('click', function () {

                show_date = $("input[name='outageServedGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                history_date = formatDate(datess);
                $('input[name="outageServedGraphDay"]').val('');
                $('input[name="outageServedGraphDay"]').val(history_date);
                console.log($("input[name='outageServedGraphDay']").val());
                history_time = 'day';
                OutagesGraphAjax(history_date, history_time);
            });

            $('#outageServedGraphForwardDay').on('click', function () {

                show_date = $("input[name='outageServedGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                history_date = formatDate(datess);
                $('input[name="outageServedGraphDay"]').val('');
                $('input[name="outageServedGraphDay"]').val(history_date);
                console.log($("input[name='outageServedGraphDay']").val());
                history_time = 'day';
                OutagesGraphAjax(history_date, history_time);
            });

            $('#outageServedGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='outageServedGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="outageServedGraphMonth"]').val('');
                $('input[name="outageServedGraphMonth"]').val(history_date);
                console.log($("input[name='outageServedGraphMonth']").val());
                history_time = 'month';
                OutagesGraphAjax(history_date, history_time);
            });

            $('#outageServedGraphForwardMonth').on('click', function () {

                show_date = $("input[name='outageServedGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="outageServedGraphMonth"]').val('');
                $('input[name="outageServedGraphMonth"]').val(history_date);
                console.log($("input[name='outageServedGraphMonth']").val());
                history_time = 'month';
                OutagesGraphAjax(history_date, history_time);
            });

            $('#outageServedGraphPreviousYear').on('click', function () {

                show_date = $("input[name='outageServedGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="outageServedGraphYear"]').val('');
                $('input[name="outageServedGraphYear"]').val(history_date);
                console.log($("input[name='outageServedGraphYear']").val());
                history_time = 'year';
                OutagesGraphAjax(history_date, history_time);
            });

            $('#outageServedGraphForwardYear').on('click', function () {

                show_date = $("input[name='outageServedGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="outageServedGraphYear"]').val('');
                $('input[name="outageServedGraphYear"]').val(history_date);
                console.log($("input[name='outageServedGraphYear']").val());
                history_time = 'year';
                OutagesGraphAjax(history_date, history_time);
            });

            $('#expGenGraphPreviousYear').on('click', function () {

                show_date = $("input[name='expGenGraphYear']").val();
                expGen_date = formatPreviousYear(show_date);
                $('input[name="expGenGraphYear"]').val('');
                $('input[name="expGenGraphYear"]').val(expGen_date);
                console.log($("input[name='expGenGraphYear']").val());
                expGen_time = 'year';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphForwardYear').on('click', function () {

                show_date = $("input[name='expGenGraphYear']").val();
                expGen_date = formatForwardYear(show_date);
                $('input[name="expGenGraphYear"]').val('');
                $('input[name="expGenGraphYear"]').val(expGen_date);
                console.log($("input[name='expGenGraphYear']").val());
                expGen_time = 'year';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#envGraphPreviousDay').on('click', function () {

                show_date = $("input[name='envGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                env_date = formatDate(datess);
                $('input[name="envGraphDay"]').val('');
                $('input[name="envGraphDay"]').val(env_date);
                console.log($("input[name='envGraphDay']").val());
                env_time = 'day';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphForwardDay').on('click', function () {

                show_date = $("input[name='envGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                env_date = formatDate(datess);
                $('input[name="envGraphDay"]').val('');
                $('input[name="envGraphDay"]').val(env_date);
                console.log($("input[name='envGraphDay']").val());
                env_time = 'day';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatPreviousMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphForwardMonth').on('click', function () {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatForwardMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphPreviousYear').on('click', function () {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatPreviousYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphForwardYear').on('click', function () {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatForwardYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(id, env_date, env_time);
            });

            $('#weatherGraphPreviousDay').on('click', function () {

                show_date = $("input[name='weatherGraphDay']").val();
                var datess = new Date(show_date);
                datess.setDate(datess.getDate() - 1);
                env_date = formatDate(datess);
                $('input[name="weatherGraphDay"]').val('');
                $('input[name="weatherGraphDay"]').val(env_date);
                env_time = 'day';
                weatherGraphAjax(env_date, env_time, weatherCheckBoxArray);
            });

            $('#weatherGraphForwardDay').on('click', function () {

                show_date = $("input[name='weatherGraphDay']").val();
                var datess = new Date(show_date);
                datess.setDate(datess.getDate() + 1);
                env_date = formatDate(datess);
                $('input[name="weatherGraphDay"]').val('');
                $('input[name="weatherGraphDay"]').val(env_date);
                env_time = 'day';
                weatherGraphAjax(env_date, env_time, weatherCheckBoxArray);
            });

            $('#weatherGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='weatherGraphMonth']").val();
                env_date = formatPreviousMonth(show_date);
                $('input[name="weatherGraphMonth"]').val('');
                $('input[name="weatherGraphMonth"]').val(env_date);
                env_time = 'month';
                weatherGraphAjax(env_date, env_time, weatherCheckBoxArray);
            });

            $('#weatherGraphForwardMonth').on('click', function () {

                show_date = $("input[name='weatherGraphMonth']").val();
                env_date = formatForwardMonth(show_date);
                $('input[name="weatherGraphMonth"]').val('');
                $('input[name="weatherGraphMonth"]').val(env_date);
                env_time = 'month';
                weatherGraphAjax(env_date, env_time, weatherCheckBoxArray);
            });

            $('#weatherGraphPreviousYear').on('click', function () {

                show_date = $("input[name='weatherGraphYear']").val();
                env_date = formatPreviousYear(show_date);
                $('input[name="weatherGraphYear"]').val('');
                $('input[name="weatherGraphYear"]').val(env_date);
                env_time = 'year';
                weatherGraphAjax(env_date, env_time, weatherCheckBoxArray);
            });

            $('#weatherGraphForwardYear').on('click', function () {

                show_date = $("input[name='weatherGraphYear']").val();
                env_date = formatForwardYear(show_date);
                $('input[name="weatherGraphYear"]').val('');
                $('input[name="weatherGraphYear"]').val(env_date);
                env_time = 'year';
                weatherGraphAjax(env_date, env_time, weatherCheckBoxArray);
            });

            $('#alertGraphPreviousDay').on('click', function () {

                show_date = $("input[name='alertGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                alert_date = formatDate(datess);
                $('input[name="alertGraphDay"]').val('');
                $('input[name="alertGraphDay"]').val(alert_date);
                console.log($("input[name='alertGraphDay']").val());
                alert_time = 'day';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphForwardDay').on('click', function () {

                show_date = $("input[name='alertGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                alert_date = formatDate(datess);
                $('input[name="alertGraphDay"]').val('');
                $('input[name="alertGraphDay"]').val(alert_date);
                console.log($("input[name='alertGraphDay']").val());
                alert_time = 'day';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatPreviousMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphForwardMonth').on('click', function () {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatForwardMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphPreviousYear').on('click', function () {

                show_date = $("input[name='alertGraphYear']").val();
                alert_date = formatPreviousYear(show_date);
                $('input[name="alertGraphYear"]').val('');
                $('input[name="alertGraphYear"]').val(alert_date);
                console.log($("input[name='alertGraphYear']").val());
                alert_time = 'year';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphForwardYear').on('click', function () {

                show_date = $("input[name='alertGraphYear']").val();
                alert_date = formatForwardYear(show_date);
                $('input[name="alertGraphYear"]').val('');
                $('input[name="alertGraphYear"]').val(alert_date);
                console.log($("input[name='alertGraphYear']").val());
                alert_time = 'year';
                alertGraphAjax(id, alert_date, alert_time);
            });

            /*$("#priority_day_my_btn_vt button").click(function() {

                $('#priority_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changePriorityDayMonthYear(id, priority_date, priority_time);

            });*/
            $("#history_consumption_peak_13 button").click(function () {

                $('#history_consumption_peak_13').children().removeClass("active");
                $(this).addClass("active");

                changeSolarEnergyDayMonthYear(history_date);

            });

            function changeSolarEnergyDayMonthYear(date) {

                var d_m_y = '';

                $('#history_consumption_peak_13').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });
                solarEnergyGraphAjax(date, d_m_y);
            }
            function changeEnergySourceDayMonthYear(date, time) {

                var d_m_y = '';

                $('#energy_source_day_my_btn_vt12').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                if (d_m_y == 'day') {
                    $('#energy_source_day_month_year_vt_year').hide();
                    $('#energy_source_day_month_year_vt_month').hide();
                    $('#energy_source_day_month_year_vt_day').show();
                    date = $('input[name="energySourceGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    $('#energy_source_day_month_year_vt_year').hide();
                    $('#energy_source_day_month_year_vt_day').hide();
                    $('#energy_source_day_month_year_vt_month').show();
                    date = $('input[name="energySourceGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#energy_source_day_month_year_vt_day').hide();
                    $('#energy_source_day_month_year_vt_month').hide();
                    $('#energy_source_day_month_year_vt_year').show();
                    date = $('input[name="energySourceGraphYear"]').val();
                    time = 'year';
                }

                solarEnergySourceGraphAjaxData(date, d_m_y);
            }
            $("#outage_served_day_my_btn_vt12 button").click(function () {

                $('#outage_served_day_my_btn_vt12').children().removeClass("active");
                $(this).addClass("active");

                changeOutageServedDayMonthYear(history_date, history_time);

            });
            function changeOutageServedDayMonthYear(date, time) {

                var d_m_y = '';

                $('#outage_served_day_my_btn_vt12').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                if (d_m_y == 'day') {
                    $('#outage_served_day_month_year_vt_year').hide();
                    $('#outage_served_day_month_year_vt_month').hide();
                    $('#outage_served_day_month_year_vt_day').show();
                    date = $('input[name="outageServedGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    $('#outage_served_day_month_year_vt_year').hide();
                    $('#outage_served_day_month_year_vt_day').hide();
                    $('#outage_served_day_month_year_vt_month').show();
                    date = $('input[name="outageServedGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#outage_served_day_month_year_vt_day').hide();
                    $('#outage_served_day_month_year_vt_month').hide();
                    $('#outage_served_day_month_year_vt_year').show();
                    date = $('input[name="outageServedGraphYear"]').val();
                    time = 'year';
                }

                OutagesGraphAjax(date, d_m_y);
            }

            function solarEnergySourceGraphAjaxData(date, time) {

                $('.energySourcesGraphSpinner').show();
                $('#container3').empty();
                // $('.solarGraphUtilizationGraphError').hide();
                // $('#energyGenerationValue').html('');
                // $('#batteryChargingValue').html('');
                // $('#gridExportValue').html('');
                $('#energySourcesGenerationValue').html('');

                var plantID = $('#plantID').val();
                var plantMeterType = $('#plantMeterType').val();
                $.ajax({
                    url: "{{ route('admin.graph.solar.energy.sources') }}",
                    method: "GET",
                    data: {
                        'plantID': plantID,
                        'date': date,
                        'time': time,
                    },
                    dataType: 'json',
                    success: function (data) {
                        $('.energySourcesGraphSpinner').hide();
                        // console.log(data)

                        // Timedata = data['time_array'];
                        // timetype = time;
                        // console.log(data);
                        // console.log(data.plant_history_graph);
                        // console.log(Timedata);

                        //var generationdata=data.plant_history_graph;
                        // $.each(data.plant_history_graph, function (index, item) {
                        //     if ("Generation" == item.name) {
                        //         generationdata = item.data;
                        //         console.log(generationdata);
                        //     }
                        //     if ("Cost Saving" == item.name) {
                        //         costsaving = item.data;
                        //         console.log(costsaving);
                        //     }
                        // });

                        // $('.consumptionPeakGraphSpinner').hide();
                        //
                        $('#container3').append('<div id="plantsEnergySourcesChart" style="height: 320px; width: 100%;"></div>');
                        // let dischargeData = data.batteryDischarge + 'kWh';
                        $('#energySourcesGenerationValue').html(data.generation + ' ' + 'kWh');
                        // $('#batteryChargingValue').html(data.batteryChargingValue + ' ' + 'kWh');
                        // $('#gridExportValue').html(data.gridExportValue + ' ' + 'kWh');
                        // $('#solarLoadValue').html(data.solarLoadValue + ' ' + 'kWh');
                        // $('.gridTotalValue').html(data.total_grid);
                        // $('.chargeTotalValue').html(data.total_charge);
                        // $('.dischargeTotalValue').html(data.total_discharge);
                        // $('.buyTotalValue').html(data.total_buy);
                        // $('.sellTotalValue').html(data.total_sell);
                        // $('.savingTotalValue').html(data.total_saving);
                        // if (time == 'day') {
                        //
                        //     $('.irradianceTotalValue').html(data.total_irradiation + ' W/m<sup>2</sup>');
                        // } else if (time == 'month' || time == 'year') {
                        //
                        //     $('.irradianceTotalValue').html(data.total_irradiation + ' kWh/m<sup>2</sup>');
                        // }

                        energySourcesGraph(data);
                    },
                    error: function (data) {

                        // $('.historyGraphSpinner').hide();
                        // $('.historyGraphError').show();
                    }
                });
            }

            $("#history_13 button").click(function () {
                // alert('okkkkkkkkk');

                $('#history_13').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                changeConsumptionDayMonthYear(history_date);

            });

            function changeConsumptionDayMonthYear(date) {
                var d_m_y = '';

                $('#history_13').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                consumptionGraphAjax(date, d_m_y);
            }

            $("#weather_day_my_btn_vt button").click(function () {
                // alert('okkkkkkkkk');

                $('#weather_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                changeWeatherDayMonthYear(history_date, history_time, historyCheckBoxArray);

            });

            // function changeWeatherDayMonthYear(date) {
            //     var d_m_y = '';
            //
            //     $('#weather_day_my_btn_vt').children('button').each(function () {
            //         if ($(this).hasClass('active')) {
            //             d_m_y = $(this).attr('id');
            //         }
            //     });
            //
            //     weatherGraphAjax(date, d_m_y);
            // }

            $("#cost-saving-data button").click(function () {
                // alert('okkkkkkkkk');

                $('#cost-saving-data').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                costSavingData(history_date);

            });

            function costSavingData(date) {
                var d_m_y = '';

                $('#cost-saving-data').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });
                costSavingDataAjax(date, d_m_y);
            }

            $("#history_day_my_btn_vt_outages_grid button").click(function () {
                // alert('okkkkkkkkk');

                $('#history_day_my_btn_vt_outages_grid').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                changeOutagesDayMonthYear(history_date);

            });

            function changeOutagesDayMonthYear(date) {
                var d_m_y = '';

                $('#history_day_my_btn_vt_outages_grid').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                OutagesGraphAjax(date, d_m_y);
            }

            $("#history_day_my_btn_vt_31 button").click(function () {
                // alert('okkkkkkkkk');

                $('#history_day_my_btn_vt_31').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                changeSolarDataEnergyDayMonthYear(history_date);

            });

            function changeSolarDataEnergyDayMonthYear(date) {
                var d_m_y = '';

                $('#history_day_my_btn_vt_31').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                solarEnergyGraphAjaxData(date, d_m_y);
            }


            $("#history_day_my_btn_vt12 button").click(function () {

                $('#history_day_my_btn_vt12').children().removeClass("active");
                $(this).addClass("active");

                changeHistoryDayMonthYear(history_date, history_time, historyCheckBoxArray);

            });
            $("#solar_day_my_btn_vt12 button").click(function () {

                $('#solar_day_my_btn_vt12').children().removeClass("active");
                $(this).addClass("active");

                changeSolarUtilizationDayMonthYear(history_date, history_time);

            });
            $("#energy_source_day_my_btn_vt12 button").click(function () {

                $('#energy_source_day_my_btn_vt12').children().removeClass("active");
                $(this).addClass("active");

                changeEnergySourceDayMonthYear(history_date, history_time);

            });

            $("#weather_day_my_btn_vt12 button").click(function () {

                $('#weather_day_my_btn_vt12').children().removeClass("active");
                $(this).addClass("active");

                changeWeatherDayMonthYear(history_date, history_time, historyCheckBoxArray);

            });

            $("#env_day_my_btn_vt button").click(function () {

                $('#env_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeENVDayMonthYear(id, env_date, env_time);

            });

            $("#alert_day_my_btn_vt button").click(function () {

                $('#alert_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeAlertDayMonthYear(id, alert_date, alert_time);

            });

        });
        var expanded = false;

        function showCheckbox() {
            var checkbox = document.getElementById("checkbox");
            if (!expanded) {
                checkbox.style.display = "block";
                expanded = true;
            } else {
                checkbox.style.display = "none";
                expanded = false;
            }
        }

        var expandedWeather = false;

        function showWeatherCheckbox() {
            var checkbox = document.getElementById("checkboxWeather");
            if (!expandedWeather) {
                checkbox.style.display = "block";
                expandedWeather = true;
            } else {
                checkbox.style.display = "none";
                expandedWeather = false;
            }
        }

        /*function changePriorityDayMonthYear(id, date, time) {

        var d_m_y = '';

        $('#priority_day_my_btn_vt').children('button').each(function() {
        if ($(this).hasClass('active')) {
        d_m_y = $(this).attr('id');
    }
    });

        if (d_m_y == 'day') {
        $('#priority_day_month_year_vt_year').hide();
        $('#priority_day_month_year_vt_month').hide();
        $('#priority_day_month_year_vt_day').show();
        date = $('input[name="priorityGraphDay"]').val();
        time = 'day';
    } else if (d_m_y == 'month') {
        $('#priority_day_month_year_vt_year').hide();
        $('#priority_day_month_year_vt_day').hide();
        $('#priority_day_month_year_vt_month').show();
        date = $('input[name="priorityGraphMonth"]').val();
        time = 'month';
    } else if (d_m_y == 'year') {
        $('#priority_day_month_year_vt_day').hide();
        $('#priority_day_month_year_vt_month').hide();
        $('#priority_day_month_year_vt_year').show();
        date = $('input[name="priorityGraphYear"]').val();
        time = 'year';
    }

        priorityGraphAjax(id, date, time);
    }*/

        function changeHistoryDayMonthYear(date, time, types) {
            // alert(types)

            var d_m_y = '';

            $('#history_day_my_btn_vt12').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#history_day_month_year_vt_year').hide();
                $('#history_day_month_year_vt_month').hide();
                $('#history_day_month_year_vt_day').show();
                date = $('input[name="historyGraphDay"]').val();
                time = 'day';
                $('.batteryPowerCheckBox').show();
                $('.batterySocCheckBox').show();
            } else if (d_m_y == 'month') {
                $('#history_day_month_year_vt_year').hide();
                $('#history_day_month_year_vt_day').hide();
                $('#history_day_month_year_vt_month').show();
                date = $('input[name="historyGraphMonth"]').val();
                time = 'month';
                let forDeletion = ["battery-power", "soc"]
                $('.batteryPowerCheckBox').hide();
                $('.batterySocCheckBox').hide();
                types = types.filter(item => !forDeletion.includes(item))
            } else if (d_m_y == 'year') {
                $('#history_day_month_year_vt_day').hide();
                $('#history_day_month_year_vt_month').hide();
                $('#history_day_month_year_vt_year').show();
                date = $('input[name="historyGraphYear"]').val();
                time = 'year';
                let forDeletion = ["battery-power", "soc"]
                $('.batteryPowerCheckBox').hide();
                $('.batterySocCheckBox').hide();
                types = types.filter(item => !forDeletion.includes(item))
            }

            historyGraphAjax(date, time, types);
            exportCsvDataValues(date, time, types);
        }

        function changeSolarUtilizationDayMonthYear(date, time) {

            var d_m_y = '';

            $('#solar_day_my_btn_vt12').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#solar_utiliza_day_month_year_vt_year').hide();
                $('#solar_utiliza_day_month_year_vt_month').hide();
                $('#solar_utiliza_day_month_year_vt_day').show();
                date = $('input[name="solarGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#solar_utiliza_day_month_year_vt_year').hide();
                $('#solar_utiliza_day_month_year_vt_day').hide();
                $('#solar_utiliza_day_month_year_vt_month').show();
                date = $('input[name="solarGraphMonth"]').val();
                time = 'month';
                let forDeletion = ["battery-power", "soc"]
                $('.batteryPowerCheckBox').hide();
                $('.batterySocCheckBox').hide();
            } else if (d_m_y == 'year') {
                $('#solar_utiliza_day_month_year_vt_day').hide();
                $('#solar_utiliza_day_month_year_vt_month').hide();
                $('#solar_utiliza_day_month_year_vt_year').show();
                date = $('input[name="solarGraphYear"]').val();
                time = 'year';
            }

            solarEnergyGraphAjax(date, d_m_y);
        }

        function changeWeatherDayMonthYear(date, time, types) {
            // alert(types)

            var d_m_y = '';

            $('#weather_day_my_btn_vt12').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#weather_day_month_year_vt_year').hide();
                $('#weather_day_month_year_vt_month').hide();
                $('#weather_day_month_year_vt_day').show();
                date = $('input[name="weatherGraphDay"]').val();
                time = 'day';
                $('.batteryWeatherPowerCheckBox').show();
                $('.batteryWeatherSocCheckBox').show();
            } else if (d_m_y == 'month') {
                $('#weather_day_month_year_vt_year').hide();
                $('#weather_day_month_year_vt_day').hide();
                $('#weather_day_month_year_vt_month').show();
                date = $('input[name="weatherGraphMonth"]').val();
                time = 'month';
                let forDeletion = ["battery-power", "soc"]
                $('.batteryWeatherPowerCheckBox').hide();
                $('.batteryWeatherSocCheckBox').hide();
                types = types.filter(item => !forDeletion.includes(item))
            } else if (d_m_y == 'year') {
                $('#weather_day_month_year_vt_day').hide();
                $('#weather_day_month_year_vt_month').hide();
                $('#weather_day_month_year_vt_year').show();
                date = $('input[name="weatherGraphYear"]').val();
                time = 'year';
                let forDeletion = ["battery-power", "soc"]
                $('.batteryWeatherPowerCheckBox').hide();
                $('.batteryWeatherSocCheckBox').hide();
                types = types.filter(item => !forDeletion.includes(item))
            }

            weatherGraphAjax(date, time, types);
        }

        function changeExpGenDayMonthYear(date, time) {

            var d_m_y = '';

            $('#expGen_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'year') {
                $('#expGen_day_month_year_vt_day').hide();
                $('#expGen_day_month_year_vt_month').hide();
                $('#expGen_day_month_year_vt_year').show();
                date = $('input[name="expGenGraphYear"]').val();
                time = 'year';
            }

            expGenGraphAjax(date, time, filterss_arr, plant_name);
        }

        function changeENVDayMonthYear(id, date, time) {

            var d_m_y = '';

            $('#env_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#env_day_month_year_vt_year').hide();
                $('#env_day_month_year_vt_month').hide();
                $('#env_day_month_year_vt_day').show();
                date = $('input[name="envGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#env_day_month_year_vt_year').hide();
                $('#env_day_month_year_vt_day').hide();
                $('#env_day_month_year_vt_month').show();
                date = $('input[name="envGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#env_day_month_year_vt_day').hide();
                $('#env_day_month_year_vt_month').hide();
                $('#env_day_month_year_vt_year').show();
                date = $('input[name="envGraphYear"]').val();
                time = 'year';
            }

            envGraphAjax(id, date, time);
        }

        function changeAlertDayMonthYear(id, date, time) {

            var d_m_y = '';

            $('#alert_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#alert_day_month_year_vt_year').hide();
                $('#alert_day_month_year_vt_month').hide();
                $('#alert_day_month_year_vt_day').show();
                date = $('input[name="alertGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#alert_day_month_year_vt_year').hide();
                $('#alert_day_month_year_vt_day').hide();
                $('#alert_day_month_year_vt_month').show();
                date = $('input[name="alertGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#alert_day_month_year_vt_day').hide();
                $('#alert_day_month_year_vt_month').hide();
                $('#alert_day_month_year_vt_year').show();
                date = $('input[name="alertGraphYear"]').val();
                time = 'year';
            }

            alertGraphAjax(id, date, time);
        }

        /*function priorityGraphAjax(id, date, time) {

        $('#priorityGraphDiv').empty();

        $.ajax({
        url: "{{ route('admin.ticket.priority.graph') }}",
                                method: "GET",
                                data: {
                                'date': date,
                                'time': time,
                                'request_from': 'plant_detail',
                                'plant_id': id
                            },
                                dataType: 'json',
                                success: function(data) {
                                console.log(data);

                                $('#priorityGraphDiv').empty();

                                $('#priorityGraphDiv').append('<div id="priorityContainer" style="height:320px;"></div>');

                                ticketPriorityGraph(data.ticket_priority_graph, data.legend_array);
                            },
                                error: function(data) {
                                console.log(data);
                            }
                            });
                            }*/


        function historyGraphAjax(date, time, historyCheckBoxArray) {

            $('.historyGraphSpinner').show();
            $('#historyGraphDiv').empty();
            $('.historyGraphError').hide();
            $('.generationTotalValue').html('');
            $('.consumptionTotalValue').html('');
            $('.gridTotalValue').html('');
            $('.buyTotalValue').html('');
            $('.sellTotalValue').html('');
            $('.savingTotalValue').html('');
            $('.irradianceTotalValue').html('');
            $('.chargeTotalValue').html('');
            $('.dischargeTotalValue').html('');

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.graph.plant.history') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                    'plantMeterType': plantMeterType,
                    'historyCheckBoxArray': JSON.stringify(historyCheckBoxArray)
                },
                dataType: 'json',
                success: function (data) {

                    Timedata = data['time_array'];
                    timetype = time;
                    console.log(data);
                    console.log(data.plant_history_graph);
                    console.log(Timedata);

                    //var generationdata=data.plant_history_graph;
                    $.each(data.plant_history_graph, function (index, item) {
                        if ("Generation" == item.name) {
                            generationdata = item.data;
                            console.log(generationdata);
                        }
                        if ("Cost Saving" == item.name) {
                            costsaving = item.data;
                            console.log(costsaving);
                        }
                    });

                    $('.historyGraphSpinner').hide();

                    $('#historyGraphDiv').append('<div id="plantsHistoryChart" style="height:300px;width:100%;"></div>');
                    $('.generationTotalValue').html(data.total_generation);
                    $('.consumptionTotalValue').html(data.total_consumption);
                    $('.gridTotalValue').html(data.total_grid);
                    $('.chargeTotalValue').html(data.total_charge);
                    $('.dischargeTotalValue').html(data.total_discharge);
                    $('.buyTotalValue').html(data.total_buy);
                    $('.sellTotalValue').html(data.total_sell);
                    $('.savingTotalValue').html(data.total_saving);
                    if (time == 'day') {

                        $('.irradianceTotalValue').html(data.total_irradiation + ' W/m<sup>2</sup>');
                    } else if (time == 'month' || time == 'year') {

                        $('.irradianceTotalValue').html(data.total_irradiation + ' kWh/m<sup>2</sup>');
                    }

                    plantHistoryGraph(data);
                },
                error: function (data) {

                    $('.historyGraphSpinner').hide();
                    $('.historyGraphError').show();
                }
            });
        }

        function weatherGraphAjax(date, time, weatherCheckBoxArray) {
            console.log([date, time, weatherCheckBoxArray])
            $('.weatherGraphSpinner').show();
            $('#weatherGraphDiv').empty();
            $('.weatherGraphError').hide();
            $('.generationWeatherTotalValue').html('');
            $('.consumptionWeatherTotalValue').html('');
            $('.gridWeatherTotalValue').html('');
            $('.buyWeatherTotalValue').html('');
            $('.sellWeatherTotalValue').html('');
            $('.savingWeatherTotalValue').html('');
            $('.irradianceWeatherTotalValue').html('');
            $('.chargeWeatherTotalValue').html('');
            $('.dischargeWeatherTotalValue').html('');

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.graph.plant.weather') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                    'plantMeterType': plantMeterType,
                    'historyCheckBoxArray': JSON.stringify(weatherCheckBoxArray)
                },
                dataType: 'json',
                success: function (data) {
                    // alert('data');
                    Timedata = data['time_array'];
                    timetype = time;
                    console.log(data);
                    console.log(data.plant_history_graph);
                    console.log(Timedata);

                    //var generationdata=data.plant_history_graph;
                    $.each(data.plant_history_graph, function (index, item) {
                        if ("Generation" == item.name) {
                            generationdata = item.data;
                            console.log(generationdata);
                        }
                        if ("Cost Saving" == item.name) {
                            costsaving = item.data;
                            console.log(costsaving);
                        }
                    });

                    $('.weatherGraphSpinner').hide();

                    $('#weatherGraphDiv').append('<div id="plantsWeatherChart" style="height:300px;width:100%;"></div>');
                    $('.generationWeatherTotalValue').html(data.total_generation);
                    $('.consumptionWeatherTotalValue').html(data.total_consumption);
                    $('.gridWeatherTotalValue').html(data.total_grid);
                    $('.chargeWeatherTotalValue').html(data.total_charge);
                    $('.dischargeWeatherTotalValue').html(data.total_discharge);
                    $('.buyWeatherTotalValue').html(data.total_buy);
                    $('.sellWeatherTotalValue').html(data.total_sell);
                    $('.savingWeatherTotalValue').html(data.total_saving);
                    if (time == 'day') {

                        $('.irradianceWeatherTotalValue').html(data.total_irradiation + ' W/m<sup>2</sup>');
                    } else if (time == 'month' || time == 'year') {

                        $('.irradianceWeatherTotalValue').html(data.total_irradiation + ' kWh/m<sup>2</sup>');
                    }

                    plantWeatherGraph(data);
                },
                error: function (data) {

                    $('.weatherGraphSpinner').hide();
                    $('.weatherGraphError').show();
                }
            });
        }

        function consumptionGraphAjax(date, time) {

            $('.consumptionPeakGraphSpinner').show();
            $('#containerpek').empty();
            $('.consumptionPeakGraphError').hide();
            $('.generationTotalValue').html('');
            $('.consumptionTotalValue').html('');
            $('#battery-remaining').html('');
            $('#total-peak-hours-consumption').html('');
            $('#grid-import').html('');

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.graph.consumption.peak.hours') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                },
                dataType: 'json',
                success: function (data) {
                    $('.consumptionPeakGraphSpinner').hide();
                    // console.log(data)

                    // Timedata = data['time_array'];
                    // timetype = time;
                    // console.log(data);
                    // console.log(data.plant_history_graph);
                    // console.log(Timedata);

                    //var generationdata=data.plant_history_graph;
                    // $.each(data.plant_history_graph, function (index, item) {
                    //     if ("Generation" == item.name) {
                    //         generationdata = item.data;
                    //         console.log(generationdata);
                    //     }
                    //     if ("Cost Saving" == item.name) {
                    //         costsaving = item.data;
                    //         console.log(costsaving);
                    //     }
                    // });

                    // $('.consumptionPeakGraphSpinner').hide();
                    //
                    $('#containerpek').append('<div id="plantsConsumptionPeakHoursChart" style="height: 320px; width: 100%;"></div>');
                    // let dischargeData = data.batteryDischarge + 'kWh';
                    $('#battery-remaining').html(data.gridImport + ' ' + 'kWh');
                    $('#total-peak-hours-consumption').html(data.consumption + ' ' + 'kWh');
                    $('#grid-import').html(data.batteryDischarge + ' ' + 'kWh');
                    // $('.gridTotalValue').html(data.total_grid);
                    // $('.chargeTotalValue').html(data.total_charge);
                    // $('.dischargeTotalValue').html(data.total_discharge);
                    // $('.buyTotalValue').html(data.total_buy);
                    // $('.sellTotalValue').html(data.total_sell);
                    // $('.savingTotalValue').html(data.total_saving);
                    // if (time == 'day') {
                    //
                    //     $('.irradianceTotalValue').html(data.total_irradiation + ' W/m<sup>2</sup>');
                    // } else if (time == 'month' || time == 'year') {
                    //
                    //     $('.irradianceTotalValue').html(data.total_irradiation + ' kWh/m<sup>2</sup>');
                    // }

                    plantHistoryPeakHoursGraph(data);
                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function costSavingDataAjax(date, time) {

            $('.costSavingGraphSpinner').show();
            $('#containerpekhour').empty();
            $('.costSavingGraphError').hide();
            $('#totalCostData').html('');

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.graph.cost.savings') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                },
                dataType: 'json',
                success: function (data) {
                    $('.costSavingGraphSpinner').hide();
                    $('#containerpekhour').append('<div id="plantsSavingDataChart" style="height: 400px; width: 100%;padding-bottom: 200px"></div>');
                    $('#totalCostData').html(data.totalSaving + ' ' + 'PKR');

                    costSavingDetailsGraph(data);
                },
                error: function (data) {

                    $('.costSavingGraphSpinner').hide();
                    $('.costSavingGraphError').show();
                }
            });
        }

        {{--function weatherGraphAjax(date, time) {--}}
        {{--    weatherGraph();--}}
        {{--    --}}{{--$('.costSavingGraphSpinner').show();--}}
        {{--    --}}{{--$('#containerpekhour').empty();--}}
        {{--    --}}{{--$('.costSavingGraphError').hide();--}}
        {{--    --}}{{--$('#totalCostData').html('');--}}

        {{--    --}}{{--var plantID = $('#plantID').val();--}}
        {{--    --}}{{--var plantMeterType = $('#plantMeterType').val();--}}
        {{--    --}}{{--$.ajax({--}}
        {{--    --}}{{--    url: "{{ route('admin.graph.cost.savings') }}",--}}
        {{--    --}}{{--    method: "GET",--}}
        {{--    --}}{{--    data: {--}}
        {{--    --}}{{--        'plantID': plantID,--}}
        {{--    --}}{{--        'date': date,--}}
        {{--    --}}{{--        'time': time,--}}
        {{--    --}}{{--    },--}}
        {{--    --}}{{--    dataType: 'json',--}}
        {{--    --}}{{--    success: function (data) {--}}
        {{--    --}}{{--        $('.costSavingGraphSpinner').hide();--}}
        {{--    --}}{{--        $('#containerpekhour').append('<div id="plantsSavingDataChart" style="height: 400px; width: 100%;padding-bottom: 200px"></div>');--}}
        {{--    --}}{{--        $('#totalCostData').html(data.totalSaving + ' ' + 'PKR');--}}

        {{--    --}}{{--        costSavingDetailsGraph(data);--}}
        {{--    --}}{{--    },--}}
        {{--    --}}{{--    error: function (data) {--}}

        {{--    --}}{{--        $('.costSavingGraphSpinner').hide();--}}
        {{--    --}}{{--        $('.costSavingGraphError').show();--}}
        {{--    --}}{{--    }--}}
        {{--    --}}{{--});--}}
        {{--}--}}

        function OutagesGraphAjax(date, time) {

            $('.outagesGraphSpinner').html('');
            $('.outagesSourcesGraphError').html('');
            $('#outages_hours').html('');


            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.graph.outages_grid_voltages') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                },
                dataType: 'json',
                success: function (data) {
                    $('.outagesGraphSpinner').hide();
                    $('#outages_hours').html(data.outagesHours);
                },
                error: function (data) {
                    $('.outagesGraphSpinner').hide();
                    // $('.historyGraphSpinner').hide();
                    $('.outagesSourcesGraphError').show();
                }
            });
        }

        function solarEnergyGraphAjax(date, time) {

            $('.solarUtilizationGraphSpinner').show();
            $('#container').empty();
            $('#energyGenerationValue').html('');

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.graph.solar.utilization.energy') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                },
                dataType: 'json',
                success: function (data) {
                    $('.solarUtilizationGraphSpinner').hide();
                    // console.log(data)

                    // Timedata = data['time_array'];
                    // timetype = time;
                    // console.log(data);
                    // console.log(data.plant_history_graph);
                    // console.log(Timedata);

                    //var generationdata=data.plant_history_graph;
                    // $.each(data.plant_history_graph, function (index, item) {
                    //     if ("Generation" == item.name) {
                    //         generationdata = item.data;
                    //         console.log(generationdata);
                    //     }
                    //     if ("Cost Saving" == item.name) {
                    //         costsaving = item.data;
                    //         console.log(costsaving);
                    //     }
                    // });

                    // $('.consumptionPeakGraphSpinner').hide();
                    //
                    $('#container').append('<div id="plantsEnergyPeakHoursChart" style="height: 320px; width: 100%"></div>');
                    $('#energyGenerationValue').html(data.generation + ' ' + 'kWh');
                    // let dischargeData = data.batteryDischarge + 'kWh';
                    // $('#battery-remaining').html(data.gridImport + ' ' + 'kWh');
                    // $('#total-peak-hours-consumption').html(data.consumption + ' ' + 'kWh');
                    // $('#grid-import').html(data.batteryDischarge + ' ' + 'kWh');
                    // $('.gridTotalValue').html(data.total_grid);
                    // $('.chargeTotalValue').html(data.total_charge);
                    // $('.dischargeTotalValue').html(data.total_discharge);
                    // $('.buyTotalValue').html(data.total_buy);
                    // $('.sellTotalValue').html(data.total_sell);
                    // $('.savingTotalValue').html(data.total_saving);
                    // if (time == 'day') {
                    //
                    //     $('.irradianceTotalValue').html(data.total_irradiation + ' W/m<sup>2</sup>');
                    // } else if (time == 'month' || time == 'year') {
                    //
                    //     $('.irradianceTotalValue').html(data.total_irradiation + ' kWh/m<sup>2</sup>');
                    // }

                    solarUtilizationEnergyGraph(data);
                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function costSavingDetailsGraph(data) {
            // console.log(data);
            let dataArray = data.logData;
            let dom = document.getElementById("plantsSavingDataChart");
            let myChart = echarts.init(dom);
            let app = {};
            option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}'
                    //    params.name;
                    // },
                },
                legend: {
                    // top: '5%',
                    // data: dataArray,
                    // top : '175px',
                    bottom: '20px',
                    show: false
                },
                series: [
                    {
                        type: 'pie',
                        radius: ['63%', '80%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center',
                            // formatter : '{d}'
                        },
                        labelLine: {
                            show: false
                        },
                        data: dataArray
                    }
                ]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
            console.log(myChart)
        }

        function plantHistoryPeakHoursGraph(data) {
            // console.log(data);
            let legend_array = data.legendArray;
            let dataArray = data.logData;
            let dom = document.getElementById("plantsConsumptionPeakHoursChart");
            let myChart = echarts.init(dom);
            let app = {};
            option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}'
                    //    params.name;
                    // },
                },
                legend: {
                    // top: '5%',
                    // data: dataArray,
                    // top : '175px',
                    bottom: '20px'
                },
                series: [
                    {
                        type: 'pie',
                        radius: ['43%', '53%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center',
                            // formatter : '{d}'
                        },
                        labelLine: {
                            show: false
                        },
                        data: dataArray
                    }
                ]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
            console.log(myChart)
        }

        function weatherGraph() {
            var dom = document.getElementById("containerwather");
            var myChart = echarts.init(dom);
            var app = {};

            var option;


            var pathSymbols = {
                reindeer: 'path://M416 128c-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8C256.4 24.8 219.3 0 176 0 114.1 0 64 50.1 64 112c0 7.3.8 14.3 2.1 21.2C27.8 145.8 0 181.5 0 224c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm-32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-192 96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm128 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zM64 448c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32z',
                plane: 'path://M512 320h-1.6c-7.4-36.5-39.7-64-78.4-64-24.6 0-46.3 11.3-61 28.8-18.6-35.9-55.8-60.8-99-60.8-58.3 0-105.6 44.7-110.9 101.6C123.3 338.5 96 373.9 96 416c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm48-160h-17.6c-7.4-36.5-39.7-64-78.4-64s-71 27.5-78.4 64H368c-27.6 0-51.8 13.9-66.2 35.1 29.1 6.2 55.5 21.3 75.6 43.3 16.5-9.4 35.3-14.5 54.6-14.5 44.6 0 84.3 26.6 102.1 65.9 20.5 3.6 39.1 12.3 55 24.4 29.8-11.7 50.9-40.5 50.9-74.3 0-44.1-35.8-79.9-80-79.9zM132.3 303.2c2.4-10.3 6.1-19.9 10.5-29.1-6.6-4-13-8.5-18.7-14.2-37.4-37.4-37.4-98.3 0-135.8 37.4-37.4 98.3-37.4 135.8 0 12.2 12.2 19.9 26.9 24.1 42.4 6.1-7 12.9-13.3 20.6-18.6l26.3-78.4c3.4-10.2-6.3-19.8-16.5-16.4l-75.3 25.1-35.5-71c-4.8-9.6-18.5-9.6-23.3 0l-35.5 71-75.3-25.1C59.3 49.7 49.7 59.4 53 69.6l25.1 75.3-71 35.5c-9.6 4.8-9.6 18.5 0 23.3l71 35.5L53 314.5c-3.4 10.2 6.3 19.8 16.5 16.5l42.7-14.2c6.3-5.1 12.9-9.8 20.1-13.6zM128 192c0 23.4 12.8 43.8 31.7 54.9 23.1-29.5 57.2-49.2 96.2-53.5 0-.5.1-.9.1-1.4 0-35.3-28.7-64-64-64s-64 28.7-64 64z',
                rocket: 'path://M544 320c-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8-18.6-35.9-55.8-60.8-99-60.8-61.9 0-112 50.1-112 112 0 7.3.8 14.3 2.1 21.2-38.3 12.6-66.1 48.3-66.1 90.8 0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zM304 160c40.7 0 78.6 17.2 105.4 46.5 9.8-5.6 20.5-9.4 31.6-11.8 4.3-10.8 7-22.3 7-34.7 0-53-43-96-96-96-14.1 0-27.4 3.2-39.5 8.7C296.6 30.3 256 0 208 0 151.6 0 105.3 41.9 97.6 96.2c-.5 0-1-.2-1.6-.2-53 0-96 43-96 96s43 96 96 96h65.6c8.1-71.8 68.5-128 142.4-128z',
                train: 'path://M160.3 291.7c-3.6-5.3-9.9-8.1-16.4-6.8l-56 11.1L99 240c1.2-6.4-1.4-12.8-6.8-16.4l-47.4-31.8L92.2 160c5.4-3.6 8-10.1 6.8-16.4l-11.1-56 56 11.1c6.5 1.3 12.8-1.4 16.4-6.8L192 44.4l31.8 47.5c3.6 5.3 10 8.1 16.4 6.8L319.6 83c8.7-1.7 14.3-10.1 12.6-18.8-1.7-8.7-10.3-14.5-18.8-12.6l-68.9 13.6-39.2-58.5c-5.9-8.9-20.6-8.9-26.6 0l-39.1 58.5-69-13.7c-5.3-1.1-10.7.6-14.4 4.4-3.8 3.8-5.4 9.2-4.4 14.5l13.7 69-58.4 39.1c-4.4 3-7.1 7.9-7.1 13.3 0 5.3 2.7 10.3 7.1 13.3l58.4 39.1-13.7 69c-1 5.3.6 10.7 4.4 14.5 3.8 3.8 9 5.5 14.4 4.4l68.9-13.7 39.1 58.5c3.1 4.6 8.2 7.1 13.3 7.1 3.1 0 6.2-.9 8.9-2.7 7.3-4.9 9.3-14.9 4.4-22.2l-44.9-67.4zM192 140c26.4 0 48 20 51.1 45.6 4.8-3.6 9.8-6.9 15.1-9.9 1.5-8.4 3.9-16.5 6.8-24.3-14.3-25.7-41.5-43.4-73-43.4-46.2 0-83.7 37.6-83.7 83.8s37.5 83.8 83.7 83.8c.3 0 .6-.1.9-.1 1.1-11.4 3.7-22.4 7.7-32.8-2.8.5-5.6.9-8.5.9-28.5 0-51.7-23.2-51.7-51.7-.1-28.6 23.1-51.9 51.6-51.9zm336 164h-59.5l11-44.1c1.2-4.8.1-9.8-2.9-13.7-3-3.9-7.7-6.2-12.6-6.2h-80c-7.3 0-13.8 5-15.5 12.1l-32 128C334 390.3 341.7 400 352 400h61.6l-13.4 93.7c-1.6 11.1 7.7 18.2 15.8 18.2 8.5 0 12.7-6.2 13.6-7.6 3.4-5.3 111.9-175.8 111.9-175.8 6.8-10.7-1-24.5-13.5-24.5zm-85.7 121.2l5.6-38.9c.7-4.6-.7-9.2-3.8-12.7-3-3.5-7.5-5.5-12.1-5.5h-59.5l24-96h47l-11 44.1c-2.5 10.2 5.2 19.9 15.5 19.9h50.8c-24.1 37.9-42.6 66.9-56.5 89.1zm133-227.9C570 158.2 536.5 128 496 128c-8.6 0-17 1.4-25.2 4.3-19.7-23-48.2-36.3-78.8-36.3-56.5 0-102.7 45.3-104 101.6-37.8 13.3-64 49.3-64 90.4 0 47.5 34.8 86.8 80.1 94.4.1-3.4.5-6.7 1.3-10.1l5.3-21.3c-30.9-4.5-54.8-30.9-54.8-63.1 0-30.6 21.8-57 52-62.8l14.5-2.8-2-18c-.2-1.5-.4-2.9-.4-4.4 0-39.7 32.3-72 72-72 24.3 0 46.8 12.2 60.2 32.7l8.1 12.4 13-7.1c32.7-17.8 70.7 8.2 70.8 40.4l-.2 16.2 12.8 2.6c29.8 6 51.3 32.3 51.3 62.7 0 27-16.9 50-40.5 59.4-2.8 4.3-11.6 18.2-23.3 36.6 52.9-.1 95.8-43.1 95.8-96 0-41.1-26.6-77.4-64.7-90.5zM560 208.8z',
                ship: 'path://M48 352c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm416 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm-119 11.1c4.6-14.5 1.6-30.8-9.8-42.3-11.5-11.5-27.8-14.4-42.3-9.9-7-13.5-20.7-23-36.9-23s-29.9 9.5-36.9 23c-14.5-4.6-30.8-1.6-42.3 9.9-11.5 11.5-14.4 27.8-9.9 42.3-13.5 7-23 20.7-23 36.9s9.5 29.9 23 36.9c-4.6 14.5-1.6 30.8 9.9 42.3 8.2 8.2 18.9 12.3 29.7 12.3 4.3 0 8.5-1.1 12.6-2.5 7 13.5 20.7 23 36.9 23s29.9-9.5 36.9-23c4.1 1.3 8.3 2.5 12.6 2.5 10.8 0 21.5-4.1 29.7-12.3 11.5-11.5 14.4-27.8 9.8-42.3 13.5-7 23-20.7 23-36.9s-9.5-29.9-23-36.9zM512 224c0-53-43-96-96-96-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8C256.4 24.8 219.3 0 176 0 114.1 0 64 50.1 64 112c0 7.3.8 14.3 2.1 21.2C27.8 145.8 0 181.5 0 224c0 53 43 96 96 96h43.4c3.6-8 8.4-15.4 14.8-21.8 13.5-13.5 31.5-21.1 50.8-21.3 13.5-13.2 31.7-20.9 51-20.9s37.5 7.7 51 20.9c19.3.2 37.3 7.8 50.8 21.3 6.4 6.4 11.3 13.8 14.8 21.8H416c53 0 96-43 96-96z',
                car: 'path://M571.7 238.8c2.8-9.9 4.3-20.2 4.3-30.8 0-61.9-50.1-112-112-112-16.7 0-32.9 3.6-48 10.8-31.6-45-84.3-74.8-144-74.8-94.4 0-171.7 74.5-175.8 168.2C39.2 220.2 0 274.3 0 336c0 79.6 64.4 144 144 144h368c70.7 0 128-57.2 128-128 0-47-25.8-90.8-68.3-113.2zM512 448H144c-61.9 0-112-50.1-112-112 0-56.8 42.2-103.7 97-111-.7-5.6-1-11.3-1-17 0-79.5 64.5-144 144-144 60.3 0 111.9 37 133.4 89.6C420 137.9 440.8 128 464 128c44.2 0 80 35.8 80 80 0 18.5-6.3 35.6-16.9 49.2C573 264.4 608 304.1 608 352c0 53-43 96-96 96z',
                run: 'path://M574 313.47A191.54 191.54 0 0 1 436.9 384a110.41 110.41 0 0 0-53.5-52.7 94.83 94.83 0 0 0 .6-10.72c0-53-43.1-96.17-96-96.17a95.1 95.1 0 0 0-36.4 7.21 124.78 124.78 0 0 0-16.7-14.22 188.15 188.15 0 0 1-1.8-25.05C233.1 86.06 319.1 0 425 0a197.47 197.47 0 0 1 35.1 3.21c8.2 1.6 10.1 12.62 2.8 16.73a150.63 150.63 0 0 0-76.1 131c0 94.17 85.4 165.7 178.5 148a9 9 0 0 1 8.7 14.53z',
                walk: 'path://M512 320h-1.6c-7.4-36.5-39.7-64-78.4-64-24.6 0-46.3 11.3-61 28.8-18.6-35.9-55.8-60.8-99-60.8-58.3 0-105.6 44.7-110.9 101.6C123.3 338.5 96 373.9 96 416c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm48-160h-17.6c-7.4-36.5-39.7-64-78.4-64s-71 27.5-78.4 64H368c-27.6 0-51.8 13.9-66.2 35.1 29.1 6.2 55.5 21.3 75.6 43.3 16.5-9.4 35.3-14.5 54.6-14.5 44.6 0 84.3 26.6 102.1 65.9 20.5 3.6 39.1 12.3 55 24.4 29.8-11.7 50.9-40.5 50.9-74.3 0-44.1-35.8-79.9-80-79.9zM132.3 303.2c2.4-10.3 6.1-19.9 10.5-29.1-6.6-4-13-8.5-18.7-14.2-37.4-37.4-37.4-98.3 0-135.8 37.4-37.4 98.3-37.4 135.8 0 12.2 12.2 19.9 26.9 24.1 42.4 6.1-7 12.9-13.3 20.6-18.6l26.3-78.4c3.4-10.2-6.3-19.8-16.5-16.4l-75.3 25.1-35.5-71c-4.8-9.6-18.5-9.6-23.3 0l-35.5 71-75.3-25.1C59.3 49.7 49.7 59.4 53 69.6l25.1 75.3-71 35.5c-9.6 4.8-9.6 18.5 0 23.3l71 35.5L53 314.5c-3.4 10.2 6.3 19.8 16.5 16.5l42.7-14.2c6.3-5.1 12.9-9.8 20.1-13.6zM128 192c0 23.4 12.8 43.8 31.7 54.9 23.1-29.5 57.2-49.2 96.2-53.5 0-.5.1-.9.1-1.4 0-35.3-28.7-64-64-64s-64 28.7-64 64z',
                walk12: 'path://M543.7 304.3C539.8 259.4 502 224 456 224c-17.8 0-34.8 5.3-49.2 15.2-22.5-29.5-57.3-47.2-94.8-47.2-66.2 0-120 53.8-120 120v.4c-38.3 16-64 53.5-64 95.6 0 57.3 46.7 104 104 104h304c57.3 0 104-46.7 104-104 0-54.8-42.6-99.8-96.3-103.7zM536 480H232c-39.7 0-72-32.3-72-72 0-32.3 21.9-60.7 53.3-69.2l13.3-3.6-2-17.2c-.3-2-.6-4-.6-6 0-48.5 39.5-88 88-88 32.2 0 61.8 17.9 77.2 46.8l10.6 19.8 15.2-16.5c10.8-11.7 25.4-18.1 41-18.1 30.9 0 56 25.1 56 56 0 1.6-.3 3.1-.8 6.9l-2.5 20 23.5-2.4c1.2-.2 2.5-.4 3.8-.4 39.7 0 72 32.3 72 72S575.7 480 536 480zM92.6 323l12.5-63.2c1.2-6.3-1.4-12.8-6.8-16.4l-53.5-35.8 53.5-35.8c5.4-3.6 8-10.1 6.8-16.4L92.6 92.1l63.2 12.5c6.4 1.3 12.8-1.5 16.4-6.8L208 44.3l35.8 53.5c3.6 5.3 9.9 8.1 16.4 6.8l63.2-12.5-12.5 63.2c-.3 1.6-.1 3.2 0 4.8.4 0 .7-.1 1.1-.1 10.1 0 20 1.1 29.6 3 .2-.5.5-.9.6-1.5l17.2-86.7c1-5.2-.6-10.6-4.4-14.4s-9.2-5.5-14.4-4.4l-76.2 15.1-43.1-64.4c-6-8.9-20.6-8.9-26.6 0l-43.2 64.5-76.1-15.1c-5.3-1.1-10.7.6-14.4 4.4-3.8 3.8-5.4 9.2-4.4 14.4l15.1 76.2-64.6 43.1c-4.4 3-7.1 8-7.1 13.3s2.7 10.3 7.1 13.3L71.6 264l-15.1 76.2c-1 5.2.6 10.6 4.4 14.4 3 3 7.1 4.7 11.3 4.7 1 0 2.1-.1 3.1-.3l32.8-6.5c6.2-13.8 14.6-26.5 25.1-37.6L92.6 323zM208 149.7c18.5 0 34.8 8.9 45.4 22.4 10.2-4.3 20.8-7.6 31.9-9.6-15.6-26.6-44.2-44.8-77.3-44.8-49.5 0-89.8 40.3-89.8 89.8 0 33 18.1 61.7 44.8 77.3 2-11.1 5.3-21.7 9.6-31.9-13.6-10.6-22.4-26.9-22.4-45.4 0-31.8 25.9-57.8 57.8-57.8z'
            };

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'none'
                    },
                    formatter: function (params) {
                        return params[0].name + ': ' + params[0].value;
                    }
                },
                xAxis: {
                    data: ['00:00', '3:00', '6:00', '09:00', '12:00', '15:00', '18:00', '21:00', '24:00'],
                    axisTick: {
                        show: true
                    },
                    axisLine: {
                        show: true
                    },
                    axisLabel: {
                        color: '#ccc'
                    }
                },
                yAxis: {
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLine: {
                        show: true
                    },
                    axisLabel: {
                        show: false
                    }
                },
                color: ['#000'],
                series: [{
                    name: 'hill',
                    type: 'pictorialBar',
                    barCategoryGap: '-130%',
                    symbol: 'path://M0,10 L10,10 C5.5,10 5.5,5 5,0 C4.5,5 4.5,10 0,10 z',
                    itemStyle: {
                        opacity: 1
                    },
                    emphasis: {
                        itemStyle: {
                            opacity: 1
                        }
                    },
                    z: 10
                }, {
                    name: 'glyph',
                    type: 'pictorialBar',
                    barGap: '-100%',
                    symbolPosition: 'end',
                    symbolSize: 20,
                    symbolOffset: [0, '-100%'],
                    data: [{
                        value: 50,
                        symbol: pathSymbols.reindeer,
                    }, {
                        value: 50,
                        symbol: pathSymbols.rocket
                    }, {
                        value: 50,
                        symbol: pathSymbols.plane
                    }, {
                        value: 50,
                        symbol: pathSymbols.train
                    }, {
                        value: 50,
                        symbol: pathSymbols.ship
                    }, {
                        value: 50,
                        symbol: pathSymbols.car
                    }, {
                        value: 50,
                        symbol: pathSymbols.run
                    }, {
                        value: 50,
                        symbol: pathSymbols.walk
                    }, {
                        value: 50,
                        symbol: pathSymbols.walk12
                    }]
                }]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function solarUtilizationEnergyGraph(data) {
            // console.log(data);
            let dataArray = data.logData;
            var dom = document.getElementById("plantsEnergyPeakHoursChart");
            var myChart = echarts.init(dom);
            var app = {};
            option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}'
                },
                legend: {
                    // top: '89%',
                    // left: 'center'
                    textStyle: {
                        fontSize: '11',
                    },
                    bottom: '1px'
                    // left: 'center'
                    // data: dataArray,
                    // top : '160px',
                    // position:'relative',
                    // zlevel: '99px'
                    // bottom : '20px'
                },
                series: [
                    {
                        type: 'pie',
                        radius: ['43%', '53%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center'
                        },
                        labelLine: {
                            show: false
                        },
                        data: dataArray
                    }
                ]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function energySourcesGraph(data) {
            // console.log(data);
            let dataArray = data.logData;
            var dom = document.getElementById("plantsEnergySourcesChart");
            var myChart = echarts.init(dom);
            var app = {};
            option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}'
                },
                legend: {
                    // top: '89%',
                    // left: 'center'
                    textStyle: {
                        fontSize: '11',
                    },
                    bottom: '1px'
                    // left: 'center'
                    // data: dataArray,
                    // top : '160px',
                    // position:'relative',
                    // zlevel: '99px'
                    // bottom : '20px'
                },
                series: [
                    {
                        type: 'pie',
                        radius: ['43%', '53%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center'
                        },
                        labelLine: {
                            show: false
                        },
                        data: dataArray
                    }
                ]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantHistoryGraph(plantsHistoryGraphData) {
            // alert('okkkkkkkkkkkkkkkkkkkkkkk');
            var data = plantsHistoryGraphData.plant_history_graph;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var timeType = plantsHistoryGraphData.time_type;
            var legendArray = plantsHistoryGraphData.legend_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            var dom = document.getElementById("plantsHistoryChart");
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {

                tooltip: {
                    trigger: 'axis',
                    textStyle: {
                        //fontFamily: 'poppins, sans-serif',
                        fontStyle: 'bold',
                        fontSize: 12,
                        //color: '#504E4E',
                    },
                    formatter: function (p) {
                        let output = '';
                        console.log(p);
                        for (let i = 0; i < p.length; i++) {
                            if (timeType == 'day') {
                                if (p[i].seriesName == 'Cost Saving') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/saving_history.png')}}"><span style="color:#009FFD;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${costFormatter(p[i].value)} PKR</span>`;
                                } else if (p[i].seriesName == 'Generation') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Consumption') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/consumption_history.png')}}"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Grid') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_history.png')}}"><span style="color:#E38595;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Buy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8FC34D;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Sell') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Irradiance') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} W/m<sup>2</sup></span>`;
                                } else if (p[i].seriesName == 'SOC') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/soc-image-color.PNG')}}" width="22.5px"><span style="color:#605bf4;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value}%</span>`;
                                } else if (p[i].seriesName == 'Battery Power') {
                                    // alert(typeof(p[i].value))
                                    // let batteryData = p[i].value;
                                    //  let batterySign = Math.sign(p[i].value)
                                    // let batterySignValue = '';
                                    // if(batterySign === -1)
                                    // {
                                    //     batteryData * -1
                                    //     batterySignValue = '-'
                                    // }
                                    //  let batteryData = p[i].value;
                                    //  if( batterySign === -1)
                                    //  {
                                    //      batteryData = batteryData * -1
                                    //  }
                                    let batteryPowerValue = energyFormatter(p[i].value) + 'W'
                                    //  // alert(batteryPowerValue)
                                    //  if(batterySign === -1)
                                    //  {
                                    //      batteryPowerValue = batteryPowerValue * -1;
                                    //  }
                                    output += `<img src="{{ asset('assets/images/graph_icons/batter-power-image.PNG')}}" width="22.5px"><span style="color:#45c745;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${batteryPowerValue}</span>`;
                                } else if (p[i].seriesName === 'Battery Charge') {
                                    let batteryChargeValue = p[i].value;

                                    if ((p[i].value === 0) || (p[i].value === null) || (p[i].value === undefined)) {
                                        batteryChargeValue = '-- W'
                                    } else {
                                        batteryChargeValue = energyFormatter(p[i].value) + 'W';
                                    }

                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.PNG')}}" width="22.5px"><span style="color:#f2b610;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${batteryChargeValue}</span>`;
                                } else if (p[i].seriesName === 'Battery Discharge') {
                                    let batteryDischargeValue = p[i].value;
                                    if ((p[i].value === 0) || (p[i].value === null) || (p[i].value === undefined)) {

                                        batteryDischargeValue = '-- W'
                                    } else {
                                        batteryDischargeValue = energyFormatter(p[i].value) + 'W';
                                    }
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-discharge-color.PNG')}}" width="22.5px"><span style="color:#31bfbf;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${batteryDischargeValue}</span>`;
                                }
                            } else {
                                if (p[i].seriesName == 'Cost Saving') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/saving_history.png')}}"><span style="color:#009FFD;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${costFormatter(p[i].value)} PKR</span>`;
                                } else if (p[i].seriesName == 'Generation') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Consumption') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/consumption_history.png')}}"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Grid') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_history.png')}}"><span style="color:#E38595;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Buy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8FC34D;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Sell') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Irradiance') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh/m<sup>2</sup></span>`;
                                } else if (p[i].seriesName == 'Battery Charge') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.PNG')}}" width="22.5px"><span style="color:#f2b610;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Battery Discharge') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-discharge-color.PNG')}}" width="22.5px"><span style="color:#31bfbf;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                }
                            }
                            if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                output += '<br/>'
                            }

                        }

                        if (timeType == 'day') {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ', ' + tooltipDate + '</span><br/><br/>' + output;
                        } else if (timeType == 'month') {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + '-' + tooltipDate + '</span><br/><br/>' + output;
                        } else {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ' ' + tooltipDate + '</span><br/><br/>' + output;
                        }
                    }
                },
                legend: {
                    data: legendArray,
                    /*selected: {
                'Cost Saving': false,
            },*/
                    //bottom: '-15px',
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '17%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: (timeType == 'day') ? false : true,
                    data: time,
                    // axisTick: {
                    //     interval: (timeType == 'day') ? parseInt((time.length) / 6) : (timeType == 'month') ? 1 : 0
                    // },
                    // axisLabel: {
                    //     interval: (timeType == 'day') ? parseInt((time.length) / 6) : (timeType == 'month') ? 1 : 0
                    // },
                    // axisLabel: {
                    //     formatter: (function(value, index) {
                    //         let array = ['00', '02', '04', '06', '08', '10', '12', '14', '16', '18', '20', '22'];
                    //         let zeroValue = value.split(':');
                    //         if (array[index] !== zeroValue[0]) {
                    //             return array[index];
                    //         } else {
                    //             return array[index] + '00'
                    //         }
                    //     })
                    // },
                    axisLine: {
                        lineStyle: {
                            color: '#666666',
                        },
                        onZero: false,
                    },
                },
                dataZoom: {
                    type: "slider"
                },
                yAxis: axisData,
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantWeatherGraph(plantsHistoryGraphData) {
            // alert('okkkkkkkkkkkkkkkkkkkkkkk');
            var data = plantsHistoryGraphData.plant_history_graph;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var timeData = plantsHistoryGraphData.time_data_array;
            var timeDataArray = plantsHistoryGraphData.time_data_array;
            var timeType = plantsHistoryGraphData.time_type;
            var legendArray = plantsHistoryGraphData.legend_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            var toolTimeDetails = plantsHistoryGraphData.tooltip_date;
            // let timeData = time.split(':');
            var finalTimeArray = [];
            // console.log(timeData);
            // for (let k = 0; k < time.length; k++) {
            //     let timeDetails = time[k].split(':')
            //     finalTimeArray.push(timeDetails[0]+':00')
            // }
            // console.log(finalTimeArray)
            // let axisLabelData = parseInt((finalTimeArray.length) / 12);
            var dom = document.getElementById("plantsWeatherChart");
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {

                tooltip: {
                    trigger: 'axis',
                    textStyle: {
                        //fontFamily: 'poppins, sans-serif',
                        fontStyle: 'bold',
                        fontSize: 12,
                        //color: '#504E4E',
                    },
                    formatter: function (p, time_details) {

                        let output = '';
                        for (let i = 0; i < p.length; i++) {
                            if (timeType == 'day') {
                                {{--if (p[i].seriesName == 'Cost Saving') {--}}
                                    {{--    output += `<img src="{{ asset('assets/images/graph_icons/saving_history.png')}}"><span style="color:#009FFD;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${costFormatter(p[i].value)} PKR</span>`;--}}
                                    {{--} else--}}


                                if (p[i].seriesName == 'Generation') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                }
                                {{--    else if(p[i].seriesName == 'glyph')--}}
                                {{--{--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">Sunny</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">10</span>`;--}}
                                {{--}--}}
                                {{--    else if (p[i].seriesName == 'Consumption') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/consumption_history.png')}}"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Grid') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/grid_history.png')}}"><span style="color:#E38595;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Buy') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8FC34D;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Sell') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Irradiance') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} W/m<sup>2</sup></span>`;--}}
                                {{--} else if (p[i].seriesName == 'SOC') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/soc-image-color.PNG')}}" width="22.5px"><span style="color:#605bf4;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value}%</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Battery Power') {--}}
                                // alert(typeof(p[i].value))
                                // let batteryData = p[i].value;
                                //  let batterySign = Math.sign(p[i].value)
                                // let batterySignValue = '';
                                // if(batterySign === -1)
                                // {
                                //     batteryData * -1
                                //     batterySignValue = '-'
                                // }
                                //  let batteryData = p[i].value;
                                //  if( batterySign === -1)
                                //  {
                                //      batteryData = batteryData * -1
                                //  }
                                // let batteryPowerValue = energyFormatter(p[i].value) + 'W'
                                //  // alert(batteryPowerValue)
                                //  if(batterySign === -1)
                                //  {
                                //      batteryPowerValue = batteryPowerValue * -1;
                                //  }
                                {{--output += `<img src="{{ asset('assets/images/graph_icons/batter-power-image.PNG')}}" width="22.5px"><span style="color:#45c745;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${batteryPowerValue}</span>`;--}}
                                {{--} else if (p[i].seriesName === 'Battery Charge') {--}}
                                {{--    let batteryChargeValue = p[i].value;--}}
                                {{--    // console.log(typeof(p[i].value) )--}}
                                {{--    // console.log(p[i].value )--}}
                                {{--    if ((p[i].value === 0) || (p[i].value === null) || (p[i].value === undefined)) {--}}
                                {{--        batteryChargeValue = '-- W'--}}
                                {{--    } else {--}}
                                {{--        batteryChargeValue = energyFormatter(p[i].value) + 'W';--}}
                                {{--    }--}}
                                {{--    // console.log(batteryChargeValue)--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.PNG')}}" width="22.5px"><span style="color:#f2b610;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${batteryChargeValue}</span>`;--}}
                                {{--} else if (p[i].seriesName === 'Battery Discharge') {--}}
                                {{--    console.log('battery discharge')--}}
                                {{--    let batteryDischargeValue = p[i].value;--}}
                                {{--    if ((p[i].value === 0) || (p[i].value === null) || (p[i].value === undefined)) {--}}

                                {{--        batteryDischargeValue = '-- W'--}}
                                {{--    } else {--}}
                                {{--        batteryDischargeValue = energyFormatter(p[i].value) + 'W';--}}
                                {{--    }--}}
                                {{--    console.log(batteryDischargeValue)--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/battery-discharge-color.PNG')}}" width="22.5px"><span style="color:#31bfbf;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${batteryDischargeValue}</span>`;--}}
                                {{--}--}}
                            } else {
                                {{--if (p[i].seriesName == 'Cost Saving') {--}}
                                    {{--    output += `<img src="{{ asset('assets/images/graph_icons/saving_history.png')}}"><span style="color:#009FFD;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${costFormatter(p[i].value)} PKR</span>`;--}}
                                    {{--} else--}}
                                if (p[i].seriesName == 'Generation') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                }
                                {{--    else if (p[i].seriesName == 'Consumption') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/consumption_history.png')}}"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Grid') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/grid_history.png')}}"><span style="color:#E38595;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Buy') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8FC34D;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Sell') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Irradiance') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh/m<sup>2</sup></span>`;--}}
                                {{--} else if (p[i].seriesName == 'Battery Charge') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.PNG')}}" width="22.5px"><span style="color:#f2b610;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;--}}
                                {{--} else if (p[i].seriesName == 'Battery Discharge') {--}}
                                {{--    output += `<img src="{{ asset('assets/images/graph_icons/battery-discharge-color.PNG')}}" width="22.5px"><span style="color:#31bfbf;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;--}}
                                {{--}--}}
                            }
                            if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                output += '<br/>'
                            }

                        }

                        if (timeType == 'day') {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ', ' + tooltipDate + '</span><br/><br/>' + output;
                        } else if (timeType == 'month') {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + '-' + tooltipDate + '</span><br/><br/>' + output;
                        } else {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ' ' + tooltipDate + '</span><br/><br/>' + output;
                        }
                    }
                },
                legend: {
                    data: legendArray,
                    show: false
                    /*selected: {
                'Cost Saving': false,
            },*/
                    //bottom: '-15px',
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '17%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: (timeType == 'day') ? false : true,
                    data: time,
                    axisTick: {
                        interval: (timeType == 'day') ? parseInt((timeData.length) / 12) : (timeType == 'month') ? 0 : 0
                    },
                    axisLabel: {
                        interval: (timeType == 'day') ? parseInt((timeData.length) / 12) : (timeType == 'month') ? 0 : 0
                    },
                    // axisLabel: {
                    //     formatter: (function(value, index) {
                    //         let array = ['00', '02', '04', '06', '08', '10', '12', '14', '16', '18', '20', '22'];
                    //         let zeroValue = value.split(':');
                    //         if (array[index] !== zeroValue[0]) {
                    //             return array[index];
                    //         } else {
                    //             return array[index] + '00'
                    //         }
                    //     })
                    // },
                    axisLine: {
                        lineStyle: {
                            color: '#666666',
                        },
                        onZero: false,
                    },
                },
                dataZoom: {
                    type: "slider"
                },
                yAxis: axisData,
                series: data
                // series: [{
                //     name: 'glyph',
                //     type: 'pictorialBar',
                //     barGap: '-100%',
                //     symbolPosition: 'end',
                //     symbolSize: 20,
                //     symbolOffset: [0, '-120%'],
                //     data: [{
                //         value: 123,
                //         symbol: 'path://M416 128c-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8C256.4 24.8 219.3 0 176 0 114.1 0 64 50.1 64 112c0 7.3.8 14.3 2.1 21.2C27.8 145.8 0 181.5 0 224c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm-32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-192 96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm128 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zM64 448c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32z',
                //     }, {
                //         value: 60,
                //         symbol: 'path://M416 128c-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8C256.4 24.8 219.3 0 176 0 114.1 0 64 50.1 64 112c0 7.3.8 14.3 2.1 21.2C27.8 145.8 0 181.5 0 224c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm-32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-192 96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm128 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zM64 448c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32z',
                //
                //     }]
                // }]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }


        function expGenGraphAjax(date, time, filter, plant_name) {

            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);

            $('#chartContainerGenDiv div').remove();
            $('#chartDetailGenDiv').empty();
            $('#expGenSpinner').show();
            $('.percentageActual').html('');
            $('.totalExpected').html('');

            $.ajax({
                url: "{{ route('admin.dashboard.expected.generation.graph') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'filter': filters,
                    'plant_name': plantName
                },

                dataType: 'json',
                success: function (data) {

                    $('#chartContainerGenDiv').empty();
                    $('.percentageActual').html('');
                    $('.totalExpected').html('');

                    $('#chartContainerGenDiv').append('<div id="chartContainerGen" style="height: 320px; width: 100%;"></div>');
                    $('.percentageActual').html(data.percentage + '%');
                    $('.totalExpected').html(data.expected_value);
                    $('#expGenSpinner').hide();

                    plantGenGraph(data.exp_ac_graph, data.legend_array, date, data.percentage);
                },
                error: function (data) {
                    console.log(data);
                    // alert('Some Error Occured!');
                }
            });
        }

        function envGraphAjax(id, date, time) {

            $('#envPlantingDiv h2').remove();
            $('#envReductionDiv h3').remove();
            $('#envGenerationDiv').empty();
            $('#envSpinner').show();

            $.ajax({
                url: "{{ route('admin.plant.env.graph') }}",
                method: "GET",
                data: {
                    'id': id,
                    'date': date,
                    'time': time
                },
                dataType: 'json',
                success: function (data) {

                    $('#envPlantingDiv h2').remove();
                    $('#envReductionDiv h3').remove();
                    $('#envGenerationDiv').empty();
                    $('#envSpinner').hide();

                    $('#envPlantingDiv').append("<h2>" + (data[1] * 0.997/18.3).toFixed(2) + "<samp>tree(s)</samp></h2>");
                    $('#envReductionDiv').append("<h3>" + (data[1] * 0.000581).toFixed(2) + "<samp>T</samp></h3>");
                    $('#envGenerationDiv').append('<p><samp class="color07_one_vt"></samp> Total Generation: <span>' + data[0] + ' </span></p>');
                },
                error: function (data) {
                    console.log(data);
                    // alert('Some Error Occured!');
                }
            });
        }

        function alertGraphAjax(id, date, time) {

            $('#alertChartDiv div').remove();
            $('#alertChartDetailDiv').empty();
            $('#alertSpinner').show();
            $('.noRecord').hide();

            $.ajax({
                url: "{{ route('admin.dashboard.alert.graph') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'plant_id': id,
                    'from_url': 'plant'
                },

                dataType: 'json',
                success: function (data) {

                    if (data.total_fault != 0 || data.total_alarm != 0 || data.total_rtu != 0) {
                        $('.noRecord').hide();

                        $('#alertChartDiv div').remove();
                        $('#alertChartDetailDiv').empty();

                        $('#alertChartDiv').append('<div id="alertChart" style="height: 200px; width: 100%;" fault_log="' + data.alert_fault + '" alarm_log="' + data.alert_alarm + '" rtu_log="' + data.alert_rtu + '" today_time="' + data.today_time + '"></div>');
                        $('#alertChartDetailDiv').append('<p><samp class="color03_one_vt"></samp> Fault: <span> ' + data.total_fault + '</span></p><p><samp class="color04_one_vt"></samp> Alarm: <span> ' + data.total_alarm + '</span></p><p><samp class="color05_one_vt"></samp> RTU: <span> ' + data.total_rtu + '</span></p>');
                        $('#alertSpinner').hide();

                        plantAlertGraph(time, data.today_time, data.plant_alert_graph, data.today_date);
                    } else {

                        $('.noRecord').show();
                        $('#alertSpinner').hide();
                    }
                },
                error: function (data) {
                    console.log(data);
                    // alert('Some Error Occured!');
                }
            });
        }

        function plantGraph(time_type, time, data, today_date, types) {

            var dom = document.getElementById("chartContainer");
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {

                tooltip: {
                    trigger: (time_type == 'day') ? 'axis' : 'item',
                    textStyle: {
                        fontFamily: 'roboto',
                        fontStyle: 'bold',
                        fontSize: 12,
                        color: '#504E4E'
                    },
                    formatter: function (params) {

                        if (time_type == 'day') {

                            if (types != 'saving') {

                                return today_date + ' ' + params[0].name + '<br>' + params[0].seriesName + ': ' + energyFormatter(params[0].data) + 'W';
                            } else {

                                return today_date + ' ' + params[0].name + '<br>' + params[0].seriesName + ': PKR ' + costFormatter(params[0].data);
                            }

                        } else if (time_type == 'month') {

                            if (types != 'saving') {

                                return params.name + '-' + today_date + '<br>' + params.seriesName + ': ' + energyFormatter(params.data) + 'Wh';
                            } else {

                                return params.name + '-' + today_date + '<br>' + params.seriesName + ': PKR ' + costFormatter(params.data);
                            }
                        } else {

                            if (types != 'saving') {

                                return getMonthName(params.name) + ' ' + today_date + '<br>' + params.seriesName + ': ' + energyFormatter(params.data) + 'Wh';
                            } else {

                                return getMonthName(params.name) + ' ' + today_date + '<br>' + params.seriesName + ': PKR ' + costFormatter(params.data);
                            }

                        }

                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: (time_type == 'day') ? false : true,
                    data: time,
                    axisTick: {
                        interval: (time_type == 'day') ? parseInt((time.length) / 6) : (time_type == 'month') ? 1 : 0
                    },
                    axisLabel: {
                        interval: (time_type == 'day') ? parseInt((time.length) / 6) : (time_type == 'month') ? 1 : 0
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#666666',
                        }
                    },
                },
                yAxis: {
                    type: 'value',
                    splitNumber: 4,
                    minInterval: 1,
                    splitLine: {
                        lineStyle: {
                            color: '#f4f4f4',
                        }
                    },
                    axisLine: {
                        show: true,
                        lineStyle: {
                            color: '#666666',
                        }
                    },
                    axisTick: {
                        show: true,
                        alignWithLabel: true
                    },
                },
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantGenGraph(data, legend_array, date, percent) {

            var dom = document.getElementById("chartContainerGen");
            var myChart = echarts.init(dom);
            var app = {};

            if (parseFloat(percent) < 100) {

                option = {
                    tooltip: {
                        trigger: 'item',
                        textStyle: {
                            color: '#000000',
                            fontFamily: 'Roboto',
                            fontWeight: 'bold',
                            fontSize: '13'
                        },
                        formatter: function (params, year) {
                            return 'Year ' + date + '<br>' + params.name;
                        },
                        // formatter: '{a} <br/>{b}'
                    },
                    legend: {
                        data: legend_array,
                        bottom: '-4px',
                    },
                    series: [{
                        name: 'Ticket Status',
                        type: 'pie',
                        radius: ['53%', '67%'],
                        label: {
                            show: false,
                            position: 'inner',
                            formatter: "{d}%",
                        },
                        labelLine: false,
                        data: data,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        color: ['#68AD86', '#0F75BC']
                    }]
                };

            } else {

                option = {
                    tooltip: {
                        trigger: 'item',
                        textStyle: {
                            color: '#000000',
                            fontFamily: 'Roboto',
                            fontWeight: 'bold',
                            fontSize: '13'
                        },
                        formatter: function (params, year) {
                            return 'Year ' + date + '<br>' + params.name;
                        },
                        // formatter: '{a} <br/>{b}'
                    },
                    legend: {
                        data: legend_array,
                        bottom: '-8px',
                    },
                    series: [{
                        name: 'Ticket Status',
                        type: 'pie',
                        radius: ['53%', '67%'],
                        label: {
                            show: false,
                            position: 'inner',
                            formatter: "{d}%",
                        },
                        labelLine: false,
                        data: data,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        color: ['#FF9768']
                    }]
                };

            }


            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function act_exp_gen(generation, time, types, date) {

            var max_gen = Math.max.apply(Math, generation.map(function (o) {
                return o.y;
            }));

            max_gen = Math.ceil((max_gen / plant_axis_grid));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10, number_format)) * Math.pow(10, number_format);

            var unit = 'kWh';

            if (time == 'month') {
                time = 'Monthly';
            } else if (time == 'year') {
                time = 'Yearly';
            }

            if (types == 'saving') {
                unit = 'PKR';
            } else if (types == 'buy') {
                types = 'Buy Energy';
            } else if (types == 'sell') {
                types = 'Sell Energy';
            }

            types = types[0].toUpperCase() + types.slice(1);

            if (time == 'Yearly') {

                var options = {

                    axisX: {
                        interval: 1,
                    },

                    axisY: {
                        interval: max_gen,
                        margin: 50,
                        gridThickness: 0.15,
                    },
                    toolTip: {
                        color: '#333333',
                        fontFamily: 'Roboto',
                        fontWeight: '600',
                        fontSize: '13'
                    },
                    data: [{
                        toolTipContent: "{tooltip} " + date + "<br/>" + types + ": {y} " + unit,
                        markerType: "none",
                        type: "column",
                        color: "#063c6e",
                        dataPoints: generation
                    }
                    ]
                };
            } else if (time == 'Monthly') {

                var options = {

                    axisX: {
                        interval: 2,
                    },

                    axisY: {
                        interval: max_gen,
                        margin: 50,
                        gridThickness: 0.15,
                    },
                    toolTip: {
                        color: '#333333',
                        fontFamily: 'Roboto',
                        fontWeight: '600',
                        fontSize: '13'
                    },
                    data: [{
                        toolTipContent: date + "-{x}<br/>" + types + ": {y} " + unit,
                        markerType: "none",
                        type: "column",
                        color: "#063c6e",
                        dataPoints: generation
                    }
                    ]
                };
            }

            var chart = new CanvasJS.Chart("chartContainer", options);
            chart.render();
        }

        function month_exp_gen(curr_generation, pre_generation, time, date) {

            var curr_max = Math.max.apply(Math, curr_generation.map(function (o) {
                return o.y;
            }));
            var pre_max = Math.max.apply(Math, pre_generation.map(function (o) {
                return o.y;
            }));

            if (curr_max >= pre_max) {

                var max_gen = curr_max;
            } else {

                var max_gen = pre_max;
            }

            max_gen = Math.ceil((max_gen / plant_axis_grid));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10, number_format)) * Math.pow(10, number_format);

            if (time == 'year') {

                var options = {

                    axisX: {
                        interval: 1,
                    },

                    axisY: {
                        interval: max_gen,
                        margin: 45,
                        gridThickness: 0.15
                    },
                    toolTip: {
                        color: '#333333',
                        fontFamily: 'Roboto',
                        fontWeight: '600',
                        fontSize: '13'
                    },
                    data: [{
                        toolTipContent: "{tooltip} " + date + "<br/>Actual Generation: {y} kWh",
                        markerType: "none",
                        type: "line",
                        color: "#68AD86",
                        dataPoints: curr_generation
                    },
                        {
                            toolTipContent: "{tooltip} " + date + "<br/>Expected Generation: {y} kWh",
                            markerType: "none",
                            type: "line",
                            color: "#0F75BC",
                            dataPoints: pre_generation
                        }
                    ]
                };
            } else if (time == 'month') {

                var dateArr = date.split('-');

                var options = {

                    axisX: {
                        interval: 1,
                    },

                    axisY: {
                        interval: max_gen,
                        margin: 45,
                        gridThickness: 0.15
                    },
                    toolTip: {
                        color: '#333333',
                        fontFamily: 'Roboto',
                        fontWeight: '600',
                        fontSize: '13'
                    },
                    data: [{
                        toolTipContent: "{x}-" + dateArr[1] + "-" + dateArr[0] + "<br/> Actual Generation: {y} kWh",
                        markerType: "none",
                        type: "column",
                        color: "#68AD86",
                        dataPoints: curr_generation
                    },
                        {
                            toolTipContent: "{x}-" + dateArr[1] + "-" + dateArr[0] + "<br/> Expected Generation: {y} kWh",
                            markerType: "none",
                            type: "column",
                            color: "#0F75BC",
                            dataPoints: pre_generation
                        }
                    ]
                };
            }

            var chart = new CanvasJS.Chart("chartContainerGen", options);
            chart.render();
        }

        function plantAlertGraph(time_type, time, data, today_date) {

            var dom = document.getElementById("alertChart");
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {
                tooltip: {
                    trigger: 'item',
                    textStyle: {
                        fontFamily: 'roboto',
                        fontStyle: 'bold',
                        fontSize: 12,
                        color: '#504E4E'
                    },
                    formatter: function (params) {

                        if (time_type == 'day') {

                            return today_date + ' ' + params.name + '<br>' + params.seriesName + ': ' + params.data;
                        } else if (time_type == 'month') {

                            return params.name + '-' + today_date + '<br>' + params.seriesName + ': ' + params.data;
                        } else {

                            return getMonthName(params.name) + ' ' + today_date + '<br>' + params.seriesName + ': ' + params.data;
                        }

                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: (time_type == 'day') ? false : true,
                    data: time,
                    axisTick: {
                        interval: (time_type == 'month') ? 1 : 0
                    },
                    axisLabel: {
                        interval: (time_type == 'month') ? 1 : 0
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#666666',
                        }
                    },
                },
                yAxis: {
                    type: 'value',
                    minInterval: 1,
                    splitNumber: 4,
                    splitLine: {
                        lineStyle: {
                            color: '#f4f4f4',
                        }
                    },
                    axisLine: {
                        show: true,
                        lineStyle: {
                            color: '#666666',
                        }
                    },
                    axisTick: {
                        show: true,
                        alignWithLabel: true
                    },
                },
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function costFormatter(num) {

            if (Math.abs(num) > 999 && Math.abs(num) <= 999999) {
                return Math.sign(num) * ((Math.abs(num) / 1000).toFixed(2)) + ' K';
            } else if (Math.abs(num) > 999999 && Math.abs(num) <= 9999999999) {
                return Math.sign(num) * ((Math.abs(num) / 1000000).toFixed(2)) + ' M';
            } else {
                return Math.sign(num) * Math.abs(num)
            }

        }

        function energyFormatter(num) {
            if (Math.abs(num) > Math.pow(10, 3) && Math.abs(num) <= Math.pow(10, 6)) {
                return Math.sign(num) * ((Math.abs(num) / Math.pow(10, 3)).toFixed(2)) + ' M';
            } else if (Math.abs(num) > Math.pow(10, 6) && Math.abs(num) <= Math.pow(10, 9)) {
                return Math.sign(num) * ((Math.abs(num) / Math.pow(10, 6)).toFixed(2)) + ' G';
            } else if (Math.abs(num) > Math.pow(10, 9) && Math.abs(num) <= Math.pow(10, 12)) {
                return Math.sign(num) * ((Math.abs(num) / Math.pow(10, 9)).toFixed(2)) + ' T';
            } else {
                return Math.sign(num) * Math.abs(num) + ' k';
            }

        }

        function getMonthName(name) {

            for (var i = 0; i < month_name_arr.length; i++) {

                if (name == month_name_arr[i].slice(0, 3)) {

                    return month_name_arr[i];
                }

            }
        }

        // function ticketPriorityGraph(data, legend_array) {

        //     var dom = document.getElementById("priorityContainer");
        //     var myChart = echarts.init(dom);
        //     var app = {};

        //     option = {
        //         tooltip: {
        //             trigger: 'item',
        //             formatter: '{a} <br/>{b} : {c} ({d}%)'
        //         },
        //         legend: {
        //             data: legend_array,
        //             bottom: '3px',
        //         },
        //         series: [{
        //             name: 'Ticket Priority',
        //             type: 'pie',
        //             radius: '50%',
        //             label:{
        //                 show: true,
        //                 position: 'inner',
        //                 formatter: "{d}%",
        //             },
        //             labelLine: false,
        //             data: data,
        //             emphasis: {
        //                 itemStyle: {
        //                     shadowBlur: 10,
        //                     shadowOffsetX: 0,
        //                     shadowColor: 'rgba(0, 0, 0, 0.5)'
        //                 }
        //             }
        //         }]
        //     };

        //     if (option && typeof option === 'object') {
        //         myChart.setOption(option);
        //     }
        // }
        function exportCsvDataValues(date, time, types) {

            let exportDataRef = document.getElementById("export-inverter-graph").getAttribute('data-href');
            let splitData = exportDataRef.split('?');
            var plantID = $('#plantID').val();
            {{--var inverterSerialNo = {!! json_encode($inverterNo) !!};--}}
            // let url = splitData[0] + '?plantID=' + plantID + '&Date=' + date + '&time=' + time + '&inverterSerialNo=' + inverterSerialNo + '&inverterArray=' + JSON.stringify(batteryArray);
            let url = splitData[0] + '?plantID=' + plantID + '&Date=' + date + '&time=' + time;
            document.getElementById("export-inverter-graph").removeAttribute('data-href');
            document.getElementById("export-inverter-graph").setAttribute('data-href', url);
        }
    </script>
    <script>
        function exportTasks(_this) {
            let exportDataRef = document.getElementById("export-inverter-graph").getAttribute('data-href');
            let _url = exportDataRef;
            window.location.href = _url;
        }
    </script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="{{ asset('assets/js/datepicker.all.js')}}"></script>
    <script src="{{ asset('assets/js/datepicker.en.js')}}"></script>
@endsection
