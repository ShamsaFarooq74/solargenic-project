@extends('layouts.admin.master')

@section('title', 'User Plant Detail')

@section('content')

    <style type="text/css">
        .gr_text_vt {
            position: absolute;
            top: 132px !important;
            left: 49% !important;
            transform: translateX(-50%);
            width: 115px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 999 !important;
        }

        .card_exp_graph .en_gener_vt {
            position: relative;
            height: 340px !important;
            margin-left: 0;
        }

        .en_gener_vt .ch_year_vt_plant{
            width: 75px !important;
            height: 15px;
            bottom: -17px;
            background: #ffffff;
            left: 4px;
            z-index: 99;
            padding: 0px 0px 0px 83px;
            position: absolute;
            display: flex;
        }
        .card_vt{
            margin-bottom: 0;
            min-height: 350px;
            box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            background: #fff;
        }
        .head_right_info_vt{
            width: 100%;
            float: left;
            box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            background: #ffffff;
            padding: 0px;
            text-align: center;
            position: relative;
            padding: 0 0 47px 0 !important;
        }
        div#alertChartDiv {
            margin-top: 0px;
        }
        .card-stat-vt.alerts_gra_vt{
            min-height: 300px;
        }
        .card-stat-vt.alerts_gra_vt .spinner-border {
            position: relative !important;
            z-index: 999 !important;
            top: 60px !important;
            left: 0 !important;
        }
        .card-stat-vt.alerts_gra_vt span.noRecord {
            margin-top: 52px;
            width: 100%;
            float: left;
        }
        .kWh_eng_vt {
            top: -3px !important;
            left:30px !important;
        }
        .padding_line_vt {
            padding-top: 27px !important;
            padding-bottom: 19px !important;
        }
        .Inverterlist_vt{
            min-height: 302px;
        }
        .bel_history_gr_vt {
            width: 100%;
            float: left;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 60px;
            margin-bottom: 20px;
        }
        .bel_history_gr_vt ul {
            margin: 0;
            padding: 0;
        }

        .bel_history_gr_vt ul li {
            list-style: none;
            float: left;
            width: auto;
            padding: 0 5px;
            font-size: 12px;
            color: #bbb8b8;
        }

        .bel_history_gr_vt ul li strong {
            font-size: 12px;
            font-weight: 300;
            color: #636363;
        }
        .single_one_vt .img_text_vt {
            margin-top: -5px !important;
        }
        .single-dashboard-vt {
            background: #063c6e !important;
        }

        .plant_consumption_off {
            margin-top: 2px !important;
        }

    </style>

    <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">

    <div class="container-fluid px-xl-5 mt-3">

        <section class="">
            <input type="hidden" id="plantID" value="{{ $plant->id }}">
            <div class="row">
                <div class="col-lg-12 mb-1">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">

                <div class="col-lg-4 mb-3 mb-lg-0 web_card_vt">
                    <div class="card-header">

                        <h2 class="head_real_vt">Real Time</h2>
                    </div>
                    <div class="plant-area-vt">
                        <p>Consumption</p>

                        <h3 class="one">{{ isset($current['consumption']) && !empty($current['consumption']) ? $current['consumption'] : 0 }}</h3>

                        <p>Generation</p>

                        <h3 class="two">{{ isset($current['generation']) && !empty($current['generation']) ? $current['generation'] : 0 }}</h3>

                        @if($plant->system_type != 'All on Grid')

                            <p>Grid</p>

                            <h3 class="three">{{isset($current['grid_type']) && isset($current['grid']) && $current['grid'] != 0 && $current['grid_type'] == '-ve' ? '- ' : '' }}{{isset($current['grid']) && !empty($current['grid']) ? $current['grid'] : 0 }}</h3>

                        @endif

                    </div>

                </div>

                <div class="col-lg-8 mb-3 mb-lg-0  order-first order-md-12">

                    @if($plant->system_type != 'All on Grid')

                        <div class="single-dashboard-vt">

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

                                    <img src="{{ asset('assets/images/sensor.png')}}" alt="sensor" class="img" width="45">

                                    <div class="single-area-tow-vt">

                                        <h4>Consumption</h4>

                                        <span>{{ isset($current['consumption']) && !empty($current['consumption']) ? $current['consumption'] : 0 }}</span>

                                        @if(isset($current['consumption']) && $current['consumption'] == 0)

                                            <div class="size_consumption_off active-animatioon"></div>

                                        @else

                                            <div class="size_consumption active-animatioon"></div>

                                        @endif

                                    </div>

                                    <img src="{{ asset('assets/images/home.png')}}" alt="home" width="45">

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

                                <div class="img_con_vt"><img src="{{ asset('assets/images/sensor.png')}}" alt="tower" width="45"></div>

                                <div class="single_one_vt">

                                    <div class="img_text_vt">

                                        <h4>Consumption</h4>

                                        @if($plant->system_type != 'All on Grid')

                                            <span>{{ isset($current['consumption']) && !empty($current['consumption']) ? $current['consumption'] : 0 }}</span>

                                        @else

                                            <span>{{ isset($current['generation']) && !empty($current['generation']) ? $current['generation'] : 0 }}</span>

                                        @endif


                                        @if(isset($current['generation']) && $current['generation'] == 0)

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

                <div class="col-lg-4 mb-3 mb-lg-0 web_card_vt">
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
                        <h3 class="three"> {{$total['generation']}}</h3>
                    </div>

                </div>
                <div class="col-lg-4 mb-3 mb-lg-0 web_card_vt">
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
                        <h3 class="three"> {{$total['consumption']}}</h3>
                    </div>

                </div>

                @if($plant->system_type != 'All on Grid')
                    <div class="col-lg-4 mb-3 mb-lg-0 web_card_vt">
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
                            <h3 class="three"> {{$total['netGridSign'].''.$total['netGrid']}}</h3>
                        </div>

                    </div>
                @else
                    <div class="col-lg-4 mb-3 mb-lg-0 web_card_vt">
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
                            <h3 class="three"> {{$total['revenue']}}</h3>
                        </div>

                    </div>

                @endif



            </div>

            <div class="row mb-3 mobile_card_vt">
                <div class="">
                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                            <div class="col-lg-12 mb-3 mb-lg-0">
                    <div class="card-header">

                        <h2 class="head_real_vt">Real Time</h2>
                    </div>
                    <div class="plant-area-vt">
                        <p>Consumption</p>

                        <h3 class="one">{{ isset($current['consumption']) && !empty($current['consumption']) ? $current['consumption'] : 0 }}</h3>

                        <p>Generation</p>

                        <h3 class="two">{{ isset($current['generation']) && !empty($current['generation']) ? $current['generation'] : 0 }}</h3>

                        @if($plant->system_type != 'All on Grid')

                            <p>Grid</p>

                            <h3 class="three">{{isset($current['grid_type']) && isset($current['grid']) && $current['grid'] != 0 && $current['grid_type'] == '-ve' ? '- ' : '' }}{{isset($current['grid']) && !empty($current['grid']) ? $current['grid'] : 0 }}</h3>

                        @endif

                    </div>

                </div>
                            </div>
                            <div class="carousel-item">
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
                        <h3 class="three"> {{$total['generation']}}</h3>
                    </div>

                </div>
                            </div>
                            <div class="carousel-item">
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
                        <h3 class="three"> {{$total['consumption']}}</h3>
                    </div>

                </div>
                            </div>
                            <div class="carousel-item">
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
                            <h3 class="three"> {{$total['netGridSign'].''.$total['netGrid']}}</h3>
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
                            <h3 class="three"> {{$total['revenue']}}</h3>
                        </div>

                    </div>

                @endif
                            </div>
                            <div class="carousel-item">
                            @if($plant->system_type != 'All on Grid')
                        <div class="card_vt p-0 mb-3">

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
                                        <h3 class="three"> {{$total['boughtEnergy']}}</h3>
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
                                        <h3 class="three"> {{$total['sellEnergy']}}</h3>
                                    </div>
                                </div>

                            </div>

                        </div>
                    @endif
                            </div>
                            <div class="carousel-item">
                            @if($plant->system_type != 'All on Grid')
                        <div class="p-0  mb-3">

                            <div class="card-header">

                                <h2 class="All-graph-heading-vt">Cost Savings</h2>

                            </div>

                            <div class="plant-area-vt  padding_line_vt">
                                <p>Daily Saving</p>
                                <h3 class="one"> {{$daily['revenue']}}</h3>
                                <p>Monthly Saving </p>
                                <h3 class="two"> {{$monthly['revenue']}}</h3>
                                <p>Yearly Saving </p>
                                <h3 class="three"> {{$yearly['revenue']}}</h3>
                                <p>Total Saving </p>
                                <h3 class="three"> {{$total['revenue']}}</h3>
                            </div>


                        </div>
                    @endif
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-8 mb-3">
                    @if($plant->system_type != 'All on Grid')
                        <div class="card_vt p-0 mb-3 web_card_vt">

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
                                        <h3 class="three"> {{$total['boughtEnergy']}}</h3>
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
                                        <h3 class="three"> {{$total['sellEnergy']}}</h3>
                                    </div>
                                </div>

                            </div>

                        </div>
                    @endif

                    <div class="card mb-3 history_gr_area_vt" style="padding-bottom: 9px;">

                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">History</h2>
                            <div class="btn-companies-vt">
                                {{--                                <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">--}}
                                {{--                                    Export CSV--}}
                                {{--                                </button>--}}
                                <button type="button"  class="btn-add-vt"
                                        data-href="{{route('export.plant.data', ['plantID'=>$plant->id, 'Date'=>'2021-07-08'])}}"
                                        id="export-inverter-graph"
                                        class="ml-3 btn_add_vt btn-success btn-sm"
                                        onclick="exportTasks(event.target);">Export CSV
                                </button>
                            </div>
                        </div>


                        <div class="row history_vt">

                            <div class="col-xl-12">

                                <div class="day_month_year_vt" id="history_day_month_year_vt_day">
                                    <button><i id="historyGraphPreviousDay" class="fa fa-caret-left"></i></button>
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-history mt10">
                                        <input type="text" autocomplete="off" name="historyGraphDay" id="historyGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                    <button><i id="historyGraphForwardDay" class="fa fa-caret-right"></i></button>
                                </div>
                                <div class="day_month_year_vt" id="history_day_month_year_vt_month">
                                    <button><i id="historyGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                    <div class="mt40">
                                        <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-history mt10">
                                            <input type="text" autocomplete="off" name="historyGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                        </div>
                                    </div>
                                    <button><i id="historyGraphForwardMonth" class="fa fa-caret-right"></i></button>
                                </div>
                                <div class="day_month_year_vt" id="history_day_month_year_vt_year">
                                    <button><i id="historyGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                    <div class="mt40">
                                        <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-history mt10">
                                            <input type="text" autocomplete="off" name="historyGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                        </div>
                                    </div>
                                    <button><i id="historyGraphForwardYear" class="fa fa-caret-right"></i></button>
                                </div>
                                <div class="day_my_btn_vt" id="history_day_my_btn_vt">
                                    <button class="day_bt_vt active" id="day">day</button>
                                    <button class="month_bt_vt" id="month">month</button>
                                    <button class="month_bt_vt" id="year">Year</button>
                                </div>

                                    {{--                                <div class="five_text_vt">--}}
                                    {{--                                    <ul id="graphNavLink">--}}
                                    {{--                                        <li class="active"><a type="button">Generation</a></li>--}}
                                    {{--                                        <li class=""><a type="button">Consumption</a></li>--}}
                                    {{--                                        @if($plant->system_type != 'All on Grid')--}}
                                    {{--                                            <li class=""><a type="button">Buy</a></li>--}}
                                    {{--                                            <li class=""><a type="button">Sell</a></li>--}}
                                    {{--                                        @endif--}}
                                    {{--                                        <li class=""><a type="button">Cost Saving</a></li>--}}
                                    {{--                                    </ul>--}}
                                    {{--                                </div>--}}

                            </div>

                        </div>

                        <div class="card-box" dir="ltr" id="plantGraph">
                            <div class="card_box_vt_sp" id="energyGenSpinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div class="historyGraphError plantGraphError" style="display: none;">
                                <span>No Data Found</span>
                            </div>
                            <div class="energy_gener_vt">
                                <div class="ch_one_vt" id="chartContainerDiv">
                                    <div class="kWh_eng_vt"></div>
                                    <div class="ch_tr_vt"><span></span></div>
                                    <div id="chartContainer" style="height: 200px; width: 100%;"></div>
                                </div>
                            </div>
                            <div class="bel_history_gr_vt">
                                <ul>
                                    <li><samp class="color1_vt"></samp> Generation : <strong
                                            class="belGenerationTotalValue"></strong></li>
                                    @if($plant->system_type_id != 1)
                                        <li><samp class="color3_vt"></samp> Consumption : <strong
                                                class="belConsumptionTotalValue"></strong></li>
                                        <li><samp class="color6_vt"></samp> Cost Saving : <strong
                                                class="belSavingTotalValue"></strong>
                                        </li>
                                        <li><samp class="color4_vt"></samp> Buy : <strong class="belBuyTotalValue"></strong>
                                        </li>
                                        <li><samp class="color5_vt"></samp> Sell : <strong class="belSellTotalValue"></strong>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <div class="generation-overviewvt" id="chartDetailDiv">
                                <p><samp></samp><br><span></span></p>
                            </div>

                        </div>

                    </div>
                    <div class="card mb-3 history_gr_area_vt card_exp_graph" style="padding-bottom: 9px;">

                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Expected Generation</h2>
                        </div>


                        <div class="row history_vt">

                            <div class="col-xl-12">

                                <div class="day_month_year_vt" id="expGen_day_month_year_vt_year">
                                    <button><i id="expGenGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                    <div class="mt40">
                                        <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-expGen mt10">
                                            <input type="text" autocomplete="off" name="expGenGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                        </div>
                                    </div>
                                    <button><i id="expGenGraphForwardYear" class="fa fa-caret-right"></i></button>
                                </div>

                                <div class="day_my_btn_vt" id="expGen_day_my_btn_vt">
                                    <button class="month_bt_vt active" id="year">Year</button>
                                </div>
                                <div class="card-box" dir="ltr">
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

                    <div class="card_height_auto_vt mb-3 tabel_area_user_vt">

                        <div class="card-body" style="margin-bottom: -2px;">

                            <div class="row">

                                <div class="col-sm-12">

                                    <h3 class="All-graph-heading-vt">All Tickets <i class="fas fa-angle-right"></i> {{$plant->plant_name}}</h3>

                                </div>

                            </div>

                            <div class="table-responsive">
                                <table id="datatable_7" class="display table table-borderless table-centered table-nowrap" style="width:100%">
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
                                    <tbody>
                                    @foreach ($tickets as $Key => $item)
                                        <tr class="clickable" href="{{route('admin.view.edit.ticket', ['type' => 'bel','id' => $item->id])}}" style="cursor: pointer;">
                                            <th scope="row">{{$item->id}}</th>
                                            <td>{{$item->title}}</td>
                                            <td>{{$item->source_name}}</td>
                                            <td>{{$item->priority_name}}</td>
                                            <td>{{$item->status_name}}</td>
                                            <td>{{date('h:i A, d-m-Y', strtotime($item->closed_time))}}</td>
                                            <td>{{$item->agents}}</td>
                                            <td>{{date('h:i A, d-m-Y', strtotime($item->created_at))}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>

                    {{-- <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Ticket Priority</h2>
                        </div>
                        <div class="day_month_year_vt" id="priority_day_month_year_vt_day">
                            <button><i id="priorityGraphPreviousDay" class="fa fa-caret-left"></i></button>
                            <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-priority mt10">
                                <input type="text" autocomplete="off" name="priorityGraphDay" id="priorityGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="priorityGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="priority_day_month_year_vt_month">
                            <button><i id="priorityGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-priority mt10">
                                    <input type="text" autocomplete="off" name="priorityGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="priorityGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="priority_day_month_year_vt_year">
                            <button><i id="priorityGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-priority mt10">
                                    <input type="text" autocomplete="off" name="priorityGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="priorityGraphForwardYear" class="fa fa-caret-right"></i></button>
                        </div>

                        <div class="day_my_btn_vt" id="priority_day_my_btn_vt">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        <div class="card-box" dir="ltr" id="priorityGraphDiv">
                            <div id="priorityContainer"></div>
                            <br>
                        </div>
                    </div> --}}

                    <div class="card Inverterlist_vt">

                        <div class="">

                            <div class="card-header">
                                <h2 class="All-graph-heading-vt">Inverter List</h2>
                            </div>



                            <div class="table-responsive" style="margin-top: 15px;">

                                <table class="tablesaw table mb-0 tablesaw-stack" id="products-datatable">

                                    <thead>

                                    <tr>
                                        <th>Status</th>
                                        <th>Site ID</th>

                                        <th>Serial Number</th>

                                        <!-- <th>Current Generation</th> -->

                                        <th>Daily Generation</th>

                                        <!-- <th>Inverter Status</th> -->

                                        <th>Last Alerts</th>

                                        <th>Last Update</th>

                                    </tr>

                                    </thead>

                                    <tbody class="btn_a_vt">

                                    @if(count($daily_inverter_details) > 0)

                                        @foreach($daily_inverter_details as $key => $inverter)

                                            <tr class="text-center">
                                                <td>
                                                    @if($inverter[0]->site_status == 'Y')
                                                        <img src="{{ asset('assets/images/icon_plant_check_vt.svg')}}" alt="check" title="Online">
                                                    @else
                                                        <img src="{{ asset('assets/images/icon_plant_vt.svg')}}" alt="check" title="Offline">
                                                    @endif
                                                </td>
                                                <td>{{$key}}</td>

                                                <td>

                                                    @foreach ($inverter as $inv)
                                                        <?php
                                                        $serialNo = '00000000';
                                                        if($inv->serial_no)
                                                        {
                                                            $serialNo = $inv->serial_no;
                                                        }?>

                                                        @if(Auth::user()->roles != 1)
                                                        <a href="{{ route('admin.plant.inverter.detail', ['type' => 'bel','plantId' => $plant->id,'id' =>  $serialNo])}}">{{ $inv->serial_no != null ? $inv->serial_no : '00000000'}}</a><br>
                                                        @else
                                                        <a href="{{ route('admin.plant.site.details', ['plantId' => $plant->id,'siteId' =>  $inv->site_id])}}">{{ $inv->serial_no != null ? $inv->serial_no : '00000000'}}</a><br>
                                                        @endif
                                                    @endforeach
                                                </td>

                                            <!-- <td>

                                                    {{-- {{ $inverter->serial_no ? $inverter->serial_no : 0 }} kWh --}}

                                                </td> -->

                                                {{-- <td>{{ $plant->daily_inverter_detail ? $plant->daily_inverter_detail->last()->daily_generation : 0 }} kWh</td> --}}
                                                <td>
                                                    @foreach ($inverter as $inv)
                                                        {{ $inv->daily_generation ? $inv->daily_generation : 0 }} kWh<br>
                                                    @endforeach
                                                </td>
                                                <!-- <td>

                                                            <i class="fas fa-check-circle"></i>

                                                        </td> -->

                                                <td>
                                                    @foreach ($inverter as $inv)
                                                        @if($inv->last_alert != '-----')
                                                            {{ date('d',strtotime($inv->last_alert)).'-'.substr(date('F', mktime(0, 0, 0, (int)date('m',strtotime($inv->last_alert)), 10)), 0, 3).' '.date('h:i A',strtotime($inv->last_alert)) }}<br>
                                                        @else
                                                            {{$inv->last_alert}}<br>
                                                        @endif
                                                    @endforeach
                                                </td>

                                                <td>
                                                    @foreach ($inverter as $inv)
                                                        {{ date('d',strtotime($plant->updated_at)).'-'.substr(date('F', mktime(0, 0, 0, (int)date('m',strtotime($plant->updated_at)), 10)), 0, 3).' '.date('h:i A',strtotime($plant->updated_at)) }}<br>
                                                    @endforeach
                                                </td>

                                            </tr>

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

                <div class="col-lg-4 mb-3">
                    @if($plant->system_type != 'All on Grid')
                        <div class="p-0  mb-3 web_card_vt">

                            <div class="card-header">

                                <h2 class="All-graph-heading-vt">Cost Savings</h2>

                            </div>

                            <div class="plant-area-vt  padding_line_vt" style="min-height: 345px;">
                                <p>Daily Saving</p>
                                <h3 class="one"> {{$daily['revenue']}}</h3>
                                <p>Monthly Saving </p>
                                <h3 class="two"> {{$monthly['revenue']}}</h3>
                                <p>Yearly Saving </p>
                                <h3 class="three"> {{$yearly['revenue']}}</h3>
                                <p>Total Saving </p>
                                <h3 class="three"> {{$total['revenue']}}</h3>
                            </div>


                        </div>
                    @endif


                    <div class="card-stat-vt  mb-3" style="padding-bottom: 45px;">
                        <div class="card-header">

                            <h2 class="All-graph-heading-vt">Plant Info</h2>
                            @if(auth()->user()->roles == 1 || auth()->user()->roles == 2 || auth()->user()->roles == 3 || auth()->user()->roles == 4)
                                <a class="eidt-profil-vt" href="{{ url('admin/edit-plant/bel/'.$plant->id)}}" style="padding-top: 5px; padding-bottom: 5px;">Edit Plant </a>
                            @endif
                        </div>

                        <div class="stat-area-hed-vt" style="background: none;">

                            @if(isset($plant->company) && $plant->company && isset($plant->company->logo) && $plant->company->logo && $plant->company->logo != null)

                                <img style="position: absolute; z-index: 5;" src="{{ $plant->company && $plant->company->logo ? asset('company_logo/'.$plant->company->logo) : asset('company_logo/com_avatar.png')}}" alt="Company Logo" width="50">

                            @endif

                            <img style="    background-size: cover;
                        position: absolute;
                        z-index: 1;
                        width: 100%;
                        height: 140px;" src="{{ $plant->plant_pic ? asset('plant_photo/'.$plant->plant_pic) : asset('plant_photo/plant_avatar.png')}}" alt="Plant Picture" width="50">

                        </div>

                        @if(auth()->user()->roles == 1 || auth()->user()->roles == 2 || auth()->user()->roles == 3 || auth()->user()->roles == 4)
                            <h2 class="stat-head-vt"><a href="{{route('admin.edit.plant', ['type' => 'bel','id' => $plant->id])}}">{{$plant->plant_name }}</a></h2>
                        @else
                            <h2 class="stat-head-vt"><a>{{$plant->plant_name }}</a></h2>
                        @endif

                        <p>Plant Type<span>{{ $plant->plant_type }}</span></p>

                        <p>Designed Capacity<span>{{ $plant->capacity }} kW</span></p>

                        <p>Daily Expected Generation<span>{{ $plant->expected_generation }} kWh</span></p>

                        <p>Contact<span>{{ $plant->phone }}</span></p>

                        <p>Benchmark Price<span> {{ $plant->currency }} {{ $plant->benchmark_price }}/unit</span></p>

                        <p>Company<span>{{ $plant->company['company_name'] }}</span></p>


                    </div>





                    <div class="card-stat-vt p-0  mb-3" style="min-height: 225px;">


                        <div class="head_right_vt">
                            <h2>Current Status</h2>
                        <!-- <a href="{{ url('admin/user-plant-detail/'.$plant->id)}}" name="refresh" type="button" class="btn-clear-ref-vt">

                            <img src="{{ asset('assets/images/refresh.png')}}" alt="Current" width="20">

                        </a> -->
                        </div>
                        <!-- <h2 class="stat-head-vt">Current Status</h2> -->



                        @if($plant->is_online == 'Y')
                            <div class="off_img_vt">
                                <img src="{{ asset('assets/images/on_off.png')}}" alt="Current" width="50">
                            </div>
                            <h2 class="stat-head-vt">Working Properly</h2>
                        @elseif($plant->is_online == 'P_Y')
                            <div class="off_img_vt">
                                <img src="{{ asset('assets/images/on_off_orange.svg')}}" alt="Current" width="50">
                            </div>
                            <h2 class="stat-head-vt">Partial Online</h2>
                        @else
                            <div class="off_img_vt">
                                <img src="{{ asset('assets/images/on_off_blue.png')}}" alt="Current" width="50">
                            </div>
                            <h2 class="stat-head-vt">Offline</h2>

                        @endif

                    </div>

                    <div class="card-stat-vt card_wather_vt p-0  mb-3" style="min-height: 210px;">

                        <div class="head_right_vt">
                            <h2>Current Local Weather</h2>

                        <!-- <a href="{{ url('admin/user-plant-detail/'.$plant->id)}}" name="refresh" type="button" class="btn-clear-ref-vt">

                            <img src="{{ asset('assets/images/refresh.png')}}" alt="Current" width="20">

                        </a> -->

                        </div>
                        <div class="_locat_vt"><i class="fas fa-map-marker-alt"></i>{{$plant->city}}</div>
                        <div class="wather_ar_vt">

                            @if($weather != null)

                                @if($weather->icon != null)

                                    <div class="wather_img"><img src="http://openweathermap.org/img/w/{{ $weather->icon }}.png" alt="Current" width="80"></div>

                                @endif

                            @endif
                            <div class="font-span">{{ $weather ? $weather->temperature : '--' }}<sup>&#8451;</sup><span>{{ $weather ? $weather->condition : '--' }}</span></div>
                            <!-- <span class="font-span"> &#8451</span> -->
                        </div>
                        <p>Sunrise<span>{{ $weather ? $weather->sunrise : '--' }}</span></p>

                        <p>Sunset<span>{{ $weather ? $weather->sunset : '--' }}</span></p>


                    </div>

                    <div class="card-stat-vt p-0  mb-3">

                        <div class="head_right_vt">
                            <h2>Environmental Benefits</h2>
                        </div>

                        <div class="clander_left_vt">
                            <div class="day_month_year_vt" id="env_day_month_year_vt_day">
                                <button><i id="envGraphPreviousDay" class="fa fa-caret-left"></i></button>
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-env mt10">
                                    <input type="text" autocomplete="off" name="envGraphDay" id="envGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                                <button><i id="envGraphForwardDay" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="env_day_month_year_vt_month">
                                <button><i id="envGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-env mt10">
                                        <input type="text" autocomplete="off" name="envGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="envGraphForwardMonth" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="env_day_month_year_vt_year">
                                <button><i id="envGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-env mt10">
                                        <input type="text" autocomplete="off" name="envGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
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

                                    <div class="mb-3 mb-md-0"><img src="{{ asset('assets/images/tree_planting.png')}}" alt="" width="50"></div>

                                    <div class="tree-vt" id="envPlantingDiv">

                                        <h6>Accumulative Trees <br>Planting</h6>

                                        <h2><samp></samp></h2>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-6  pr-0 border_left_tree_vt">

                                <div class="row tree-planting-vt">

                                    <div class="col-md-12 mb-3 mb-md-0"><img src="{{ asset('assets/images/chimney.png')}}" alt="" width="50"></div>

                                    <div class="col-md-12 tree-vt" id="envReductionDiv">

                                        <h6>Accumulative CO<sub>2</sub> <br>Emission Reduction</h6>

                                        <h3><samp></samp></h3>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="online-fault-vt" id="envGenerationDiv">
                            <p><samp class="color07_one_vt"></samp> Total Generation: <span> 15 kWh</span></p>
                        </div>
                    </div>


                    <div class="card-stat-vt alerts_gra_vt p-0  mb-3">
                        <div class="head_right_vt">
                            <h2>Alerts</h2>
                        </div>

                        <div class="clander_left_vt">
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_day">
                                <button><i id="alertGraphPreviousDay" class="fa fa-caret-left"></i></button>
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-alert mt10">
                                    <input type="text" autocomplete="off" name="alertGraphDay" id="alertGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                                <button><i id="alertGraphForwardDay" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_month">
                                <button><i id="alertGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-alert mt10">
                                        <input type="text" autocomplete="off" name="alertGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="alertGraphForwardMonth" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_year">
                                <button><i id="alertGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-alert mt10">
                                        <input type="text" autocomplete="off" name="alertGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
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
                            <span class="noRecord" style="display: none;"> NO ALERTS to SHOW </span>
                        <div id="alertChartDiv">
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

        </section>

        <!-- Single plant dashboard end -->

    </div>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.0.0/dist/echarts.min.js"></script>
    <script type="text/javascript">
        var plant_axis_grid = 4;
        var filterss_arr = {};
        var plant_name = [{!! $plant->id !!}];
        var comm_fail = {!! $current['comm_fail'] !!};
        var month_name_arr = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        $(document).ready(function() {

            if(comm_fail == 1) {

                $('.comm_fail').html('Power Outage or Communication Failure');
            }

            $('.clickable').click(function() {
                window.location.href = $(this).attr('href');
            });

            $('.clickable').click(function() {
                window.location.href = $(this).attr('href');
            });

            var currDate = getCurrentDate();

            // $('input[name="priorityGraphDay"]').val(currDate.todayDate);
            // $('input[name="priorityGraphMonth"]').val(currDate.todayMonth);
            // $('input[name="priorityGraphYear"]').val(currDate.todayYear);
            $('input[name="historyGraphDay"]').val(currDate.todayDate);
            $('input[name="historyGraphMonth"]').val(currDate.todayMonth);
            $('input[name="historyGraphYear"]').val(currDate.todayYear);
            $('input[name="expGenGraphYear"]').val(currDate.todayYear);
            $('input[name="envGraphDay"]').val(currDate.todayDate);
            $('input[name="envGraphMonth"]').val(currDate.todayMonth);
            $('input[name="envGraphYear"]').val(currDate.todayYear);
            $('input[name="alertGraphDay"]').val(currDate.todayDate);
            $('input[name="alertGraphMonth"]').val(currDate.todayMonth);
            $('input[name="alertGraphYear"]').val(currDate.todayYear);

            // var priority_date = $('input[name="priorityGraphDay"]').val();
            // var priority_time = 'day';
            var types = 'generation';
            var history_date = $('input[name="historyGraphDay"]').val();
            var history_time = 'day';
            var expGen_date = $('input[name="expGenGraphYear"]').val();
            var expGen_time = 'year';
            var env_date = $('input[name="envGraphDay"]').val();
            var env_time = 'day';
            var alert_date = $('input[name="alertGraphDay"]').val();
            var alert_time = 'day';
            var id = {!!$plant->id!!};

            // changePriorityDayMonthYear(id, priority_date, priority_time);
            // priorityGraphAjax(id, priority_date, priority_time);
            changeHistoryDayMonthYear(types, id, history_date, history_time);
            changeExpGenDayMonthYear(expGen_date, expGen_time);
            changeENVDayMonthYear();
            changeAlertDayMonthYear();
            historyGraphAjax(types, id, history_date, history_time);
            expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            envGraphAjax(id, env_date, env_time);
            alertGraphAjax(id, alert_date, alert_time);

            $('#graphNavLink li a').click(function() {

                $(this).parent().toggleClass('active').siblings().removeClass('active');

                if ($(this).html() == 'Generation') {

                    types = 'generation';
                } else if ($(this).html() == 'Consumption') {

                    types = 'consumption';
                } else if ($(this).html() == 'Buy') {

                    types = 'buy';
                } else if ($(this).html() == 'Sell') {

                    types = 'sell';
                } else if ($(this).html() == 'Cost Saving') {

                    types = 'saving';
                }
                changeHistoryDayMonthYear(types, id, history_date, history_time);

            });

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
                hide: function(type) {
                    changeHistoryDayMonthYear(types, id, this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-history').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changeHistoryDayMonthYear(types, id, this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-history').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changeHistoryDayMonthYear(types, id, this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearPicker-single-expGen').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-env').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function(type) {
                    changeENVDayMonthYear(id, this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-env').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changeENVDayMonthYear(id, this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-env').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changeENVDayMonthYear(id, this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-alert').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function(type) {
                    changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-alert').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-alert').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
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

            $('#historyGraphPreviousDay').on('click', function() {

                show_date = $("input[name='historyGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                history_date = formatDate(datess);
                $('input[name="historyGraphDay"]').val('');
                $('input[name="historyGraphDay"]').val(history_date);
                console.log($("input[name='historyGraphDay']").val());
                history_time = 'day';
                historyGraphAjax(types, id, history_date, history_time);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphForwardDay').on('click', function() {

                show_date = $("input[name='historyGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                history_date = formatDate(datess);
                $('input[name="historyGraphDay"]').val('');
                $('input[name="historyGraphDay"]').val(history_date);
                console.log($("input[name='historyGraphDay']").val());
                history_time = 'day';
                historyGraphAjax(types, id, history_date, history_time);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='historyGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="historyGraphMonth"]').val('');
                $('input[name="historyGraphMonth"]').val(history_date);
                console.log($("input[name='historyGraphMonth']").val());
                history_time = 'month';
                historyGraphAjax(types, id, history_date, history_time);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphForwardMonth').on('click', function() {

                show_date = $("input[name='historyGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="historyGraphMonth"]').val('');
                $('input[name="historyGraphMonth"]').val(history_date);
                console.log($("input[name='historyGraphMonth']").val());
                history_time = 'month';
                historyGraphAjax(types, id, history_date, history_time);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphPreviousYear').on('click', function() {

                show_date = $("input[name='historyGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="historyGraphYear"]').val('');
                $('input[name="historyGraphYear"]').val(history_date);
                console.log($("input[name='historyGraphYear']").val());
                history_time = 'year';
                historyGraphAjax(types, id, history_date, history_time);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#historyGraphForwardYear').on('click', function() {

                show_date = $("input[name='historyGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="historyGraphYear"]').val('');
                $('input[name="historyGraphYear"]').val(history_date);
                console.log($("input[name='historyGraphYear']").val());
                history_time = 'year';
                historyGraphAjax(types, id, history_date, history_time);
                exportCsvDataValues(history_date, history_time, types);
            });

            $('#expGenGraphPreviousYear').on('click', function() {

                show_date = $("input[name='expGenGraphYear']").val();
                expGen_date = formatPreviousYear(show_date);
                $('input[name="expGenGraphYear"]').val('');
                $('input[name="expGenGraphYear"]').val(expGen_date);
                console.log($("input[name='expGenGraphYear']").val());
                expGen_time = 'year';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphForwardYear').on('click', function() {

                show_date = $("input[name='expGenGraphYear']").val();
                expGen_date = formatForwardYear(show_date);
                $('input[name="expGenGraphYear"]').val('');
                $('input[name="expGenGraphYear"]').val(expGen_date);
                console.log($("input[name='expGenGraphYear']").val());
                expGen_time = 'year';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#envGraphPreviousDay').on('click', function() {

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

            $('#envGraphForwardDay').on('click', function() {

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

            $('#envGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatPreviousMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphForwardMonth').on('click', function() {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatForwardMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphPreviousYear').on('click', function() {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatPreviousYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(id, env_date, env_time);
            });

            $('#envGraphForwardYear').on('click', function() {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatForwardYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(id, env_date, env_time);
            });

            $('#alertGraphPreviousDay').on('click', function() {

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

            $('#alertGraphForwardDay').on('click', function() {

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

            $('#alertGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatPreviousMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphForwardMonth').on('click', function() {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatForwardMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphPreviousYear').on('click', function() {

                show_date = $("input[name='alertGraphYear']").val();
                alert_date = formatPreviousYear(show_date);
                $('input[name="alertGraphYear"]').val('');
                $('input[name="alertGraphYear"]').val(alert_date);
                console.log($("input[name='alertGraphYear']").val());
                alert_time = 'year';
                alertGraphAjax(id, alert_date, alert_time);
            });

            $('#alertGraphForwardYear').on('click', function() {

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

            $("#history_day_my_btn_vt button").click(function() {

                $('#history_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeHistoryDayMonthYear(types, id, history_date, history_time);

            });

            $("#env_day_my_btn_vt button").click(function() {

                $('#env_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeENVDayMonthYear(id, env_date, env_time);

            });

            $("#alert_day_my_btn_vt button").click(function() {

                $('#alert_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeAlertDayMonthYear(id, alert_date, alert_time);

            });

        });

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

        function changeHistoryDayMonthYear(types, id, date, time) {

            var d_m_y = '';

            $('#history_day_my_btn_vt').children('button').each(function () {
                if($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#history_day_month_year_vt_year').hide();
                $('#history_day_month_year_vt_month').hide();
                $('#history_day_month_year_vt_day').show();
                date = $('input[name="historyGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#history_day_month_year_vt_year').hide();
                $('#history_day_month_year_vt_day').hide();
                $('#history_day_month_year_vt_month').show();
                date = $('input[name="historyGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#history_day_month_year_vt_day').hide();
                $('#history_day_month_year_vt_month').hide();
                $('#history_day_month_year_vt_year').show();
                date = $('input[name="historyGraphYear"]').val();
                time = 'year';
            }

            historyGraphAjax(types, id, date, time);
            exportCsvDataValues(date, time, types);
        }

        function changeExpGenDayMonthYear(date, time) {

            var d_m_y = '';

            $('#expGen_day_my_btn_vt').children('button').each(function () {
                if($(this).hasClass('active')) {
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
                if($(this).hasClass('active')) {
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
                if($(this).hasClass('active')) {
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

        function historyGraphAjax(types, id, date, time) {

            $('#chartContainerDiv div').remove();
            $('#chartDetailDiv').empty();
            $('#energyGenSpinner').show();
            $('.belGenerationTotalValue').html('');
            $('.belConsumptionTotalValue').html('');
            $('.belBuyTotalValue').html('');
            $('.belSellTotalValue').html('');
            $('.belSavingTotalValue').html('');

            $.ajax({
                url: "{{ route('admin.plant.history.graph') }}",
                method: "GET",
                data: {
                    'type': types,
                    'id': id,
                    'date': date,
                    'time' : time,
                    'graphType': 'bel'
                },
                dataType: 'json',
                success: function(data) {

                    console.log(data);
                    var unit = '';
                    var typess = ''

                    // if(types == 'generation') {
                    //     typess = 'Generation';
                    // }
                    // else if(types == 'consumption') {
                    //     typess = 'Consumption';
                    // }
                    // else if(types == 'buy') {
                    //     typess = 'Buy Energy';
                    // }
                    // else if(types == 'sell') {
                    //     typess = 'Sell Energy';
                    // }

                    $('#chartContainerDiv div').remove();
                    $('#chartDetailDiv').empty();

                    var timely = '';

                    // if(time == 'day') {
                    //
                    //     timely = 'Daily';
                    // }
                    // else if(time == 'month') {
                    //
                    //     timely = 'Monthly';
                    //
                    //     if(types == 'buy') {
                    //         typess = 'Buy';
                    //     }
                    //     else if(types == 'sell') {
                    //         typess = 'Sell';
                    //     }
                    // }
                    // else if(time == 'year') {
                    //
                    //     timely = 'Yearly';
                    //
                    //     if(types == 'buy') {
                    //         typess = 'Buy';
                    //     }
                    //     else if(types == 'sell') {
                    //         typess = 'Sell';
                    //     }
                    // }
                    //
                    // if(types == 'saving') {
                    //
                    //     typess = 'Cost Saving'
                    //     unit = 'PKR';
                    // }
                    // else {
                    //
                    //     unit = 'kWh';
                    //     if(time == 'day') {
                    //         unit = 'kW';
                    //     }
                    // }

                    // $('#chartContainerDiv').append('<div class="kWh_eng_vt">'+unit+'</div>');
                    $('.belGenerationTotalValue').html(data.total_generation);
                    $('.belConsumptionTotalValue').html(data.total_consumption);
                    $('.belBuyTotalValue').html(data.total_buy);
                    $('.belSellTotalValue').html(data.total_sell);
                    $('.belSavingTotalValue').html(data.total_saving);

                    $('#chartContainerDiv').append('<div id="chartContainer" style="height: 260px; width: 100%; margin-top:-30px !important;"></div>');
                    // $('#chartDetailDiv').append('<div class="online-fault-vt"><p><samp class="color_one_vt"></samp>'+timely +' '+ typess + ' : <span> ' + data.total_today+ '</span></p></div>');
                    $('#energyGenSpinner').hide();

                    plantGraph(data);

                },
                error: function(data) {
                    console.log(data);
                    // alert('Some Error Occured!');
                }
            });
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
                    'time' : time,
                    'filter' : filters,
                    'plant_name' : plantName
                },

                dataType: 'json',
                success: function(data) {
                    console.log(data);

                    $('#chartContainerGenDiv').empty();
                    $('.percentageActual').html('');
                    $('.totalExpected').html('');

                    $('#chartContainerGenDiv').append('<div id="chartContainerGen" style="height: 320px; width: 100%;"></div>');
                    $('.percentageActual').html(data.percentage+'%');
                    $('.totalExpected').html(data.expected_value);
                    $('#expGenSpinner').hide();

                    plantGenGraph(data.exp_ac_graph, data.legend_array, date, data.percentage);
                },
                error: function(data) {
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
                    'time' : time
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);

                    $('#envPlantingDiv h2').remove();
                    $('#envReductionDiv h3').remove();
                    $('#envGenerationDiv').empty();
                    $('#envSpinner').hide();

                    $('#envPlantingDiv').append("<h2>"+(data[1] * 0.00131).toFixed(2)+"<samp>tree(s)</samp></h2>");
                    $('#envReductionDiv').append("<h3>"+(data[1] * 0.000646155).toFixed(2)+"<samp>T</samp></h3>");
                    $('#envGenerationDiv').append('<p><samp class="color07_one_vt"></samp> Total Generation: <span>'+data[0]+' </span></p>');
                },
                error: function(data) {

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
                    'time' : time,
                    'plant_id' : id,
                    'from_url' : 'plant'
                },

                dataType: 'json',
                success: function(data) {
                    console.log(data);

                    if(data.total_fault != 0 || data.total_alarm != 0 || data.total_rtu != 0) {
                        $('.noRecord').hide();

                        $('#alertChartDiv div').remove();
                        $('#alertChartDetailDiv').empty();

                        $('#alertChartDiv').append('<div id="alertChart" style="height: 200px; width: 100%;" fault_log="' + data.alert_fault + '" alarm_log="' + data.alert_alarm + '" rtu_log="' + data.alert_rtu + '" today_time="' + data.today_time + '"></div>');
                        $('#alertChartDetailDiv').append('<p><samp class="color03_one_vt"></samp> Fault: <span> '+data.total_fault+'</span></p><p><samp class="color04_one_vt"></samp> Alarm: <span> '+data.total_alarm+'</span></p><p><samp class="color05_one_vt"></samp> RTU: <span> '+data.total_rtu+'</span></p>');
                        $('#alertSpinner').hide();

                        plantAlertGraph(time, data.today_time, data.plant_alert_graph, data.today_date);
                    }

                    else {

                        $('.noRecord').show();
                        $('#alertSpinner').hide();
                    }
                },
                error: function(data) {
                    // console.log(data);
                    // alert('Some Error Occured!');
                }
            });
        }

        function plantGraph(plantsHistoryGraphData) {
            var data = plantsHistoryGraphData.plant_history_graph;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var timeType = plantsHistoryGraphData.time_type;
            var legendArray = plantsHistoryGraphData.legend_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            // var timeData = plantsHistoryGraphData.time_data_array;
            // alert([data,axisData,time,timeType,legendArray,tooltipDate])
            var dom = document.getElementById("chartContainer");
            var myChart = echarts.init(dom);
            console.log(data);
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
                                    // console.log(typeof(p[i].value) )
                                    // console.log(p[i].value )
                                    if ((p[i].value === 0) || (p[i].value === null) || (p[i].value === undefined)) {
                                        batteryChargeValue = '-- W'
                                    } else {
                                        batteryChargeValue = energyFormatter(p[i].value) + 'W';
                                    }
                                    // console.log(batteryChargeValue)
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.PNG')}}" width="22.5px"><span style="color:#f2b610;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${batteryChargeValue}</span>`;
                                } else if (p[i].seriesName === 'Battery Discharge') {
                                    console.log('battery discharge')
                                    let batteryDischargeValue = p[i].value;
                                    if ((p[i].value === 0) || (p[i].value === null) || (p[i].value === undefined)) {

                                        batteryDischargeValue = '-- W'
                                    } else {
                                        batteryDischargeValue = energyFormatter(p[i].value) + 'W';
                                    }
                                    console.log(batteryDischargeValue)
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
                // series: [
                //     {
                //         data: [820, 932, 901, 934, 1290, 1330, 1320],
                //         type: 'line',
                //         smooth: true
                //     }
                // ]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantGenGraph(data, legend_array, date, percent) {

            var dom = document.getElementById("chartContainerGen");
            var myChart = echarts.init(dom);
            var app = {};
            console.log('percent'+percent);
            if(parseFloat(percent) < 100) {

                option = {
                    tooltip: {
                        trigger: 'item',
                        textStyle:{
                            color:'#000000',
                            fontFamily:'Roboto',
                            fontWeight:'bold',
                            fontSize:'13'
                        },
                        formatter:function(params,year) {
                            return 'Year '+date+'<br>'+params.name;
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
                        label:{
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
                        color:['#68AD86', '#0F75BC']
                    }]
                };

            }

            else {

                option = {
                    tooltip: {
                        trigger: 'item',
                        textStyle:{
                            color:'#000000',
                            fontFamily:'Roboto',
                            fontWeight:'bold',
                            fontSize:'13'
                        },
                        formatter:function(params,year) {
                            return 'Year '+date+'<br>'+params.name;
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
                        label:{
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
                        color:['#FF9768']
                    }]
                };

            }


            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function act_exp_gen(generation, time, types, date) {

            var max_gen = Math.max.apply(Math, generation.map(function(o) { return o.y; }));

            max_gen = Math.ceil((max_gen / plant_axis_grid));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10,number_format)) * Math.pow(10,number_format);

            var unit = 'kWh';

            if(time == 'month') {
                time = 'Monthly';
            }
            else if(time == 'year') {
                time = 'Yearly';
            }

            if(types == 'saving') {
                unit = 'PKR';
            }
            else if(types == 'buy') {
                types = 'Buy Energy';
            }
            else if(types == 'sell') {
                types = 'Sell Energy';
            }

            types = types[0].toUpperCase() + types.slice(1);

            if(time == 'Yearly') {

                var options = {

                    axisX: {
                        interval: 1,
                    },

                    axisY: {
                        interval: max_gen,
                        margin:50,
                        gridThickness: 0.15,
                    },
                    toolTip: {
                        color:'#333333',
                        fontFamily:'Roboto',
                        fontWeight:'600',
                        fontSize:'13'
                    },
                    data: [{
                        toolTipContent: "{tooltip} "+date+"<br/>"+types+": {y} "+unit,
                        markerType: "none",
                        type: "column",
                        color:"#063c6e",
                        dataPoints: generation
                    }
                    ]
                };
            }

            else if(time == 'Monthly') {

                var options = {

                    axisX: {
                        interval: 2,
                    },

                    axisY: {
                        interval: max_gen,
                        margin:50,
                        gridThickness: 0.15,
                    },
                    toolTip: {
                        color:'#333333',
                        fontFamily:'Roboto',
                        fontWeight:'600',
                        fontSize:'13'
                    },
                    data: [{
                        toolTipContent: date+"-{x}<br/>"+types+": {y} "+unit,
                        markerType: "none",
                        type: "column",
                        color:"#063c6e",
                        dataPoints: generation
                    }
                    ]
                };
            }

            var chart = new CanvasJS.Chart("chartContainer", options);
            chart.render();
        }

        function month_exp_gen(curr_generation, pre_generation, time, date) {

            var curr_max = Math.max.apply(Math, curr_generation.map(function(o) { return o.y; }));
            var pre_max = Math.max.apply(Math, pre_generation.map(function(o) { return o.y; }));

            if(curr_max >= pre_max) {

                var max_gen = curr_max;
            }
            else {

                var max_gen = pre_max;
            }

            max_gen = Math.ceil((max_gen / plant_axis_grid));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10,number_format)) * Math.pow(10,number_format);

            if(time == 'year') {

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
                        color:'#333333',
                        fontFamily:'Roboto',
                        fontWeight:'600',
                        fontSize:'13'
                    },
                    data: [{
                        toolTipContent: "{tooltip} "+date+"<br/>Actual Generation: {y} kWh",
                        markerType: "none",
                        type: "line",
                        color: "#68AD86",
                        dataPoints: curr_generation
                    },
                        {
                            toolTipContent: "{tooltip} "+date+"<br/>Expected Generation: {y} kWh",
                            markerType: "none",
                            type: "line",
                            color: "#0F75BC",
                            dataPoints: pre_generation
                        }
                    ]
                };
            }

            else if(time == 'month') {

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
                        color:'#333333',
                        fontFamily:'Roboto',
                        fontWeight:'600',
                        fontSize:'13'
                    },
                    data: [{
                        toolTipContent: "{x}-"+dateArr[1]+"-"+dateArr[0]+"<br/> Actual Generation: {y} kWh",
                        markerType: "none",
                        type: "column",
                        color: "#68AD86",
                        dataPoints: curr_generation
                    },
                        {
                            toolTipContent: "{x}-"+dateArr[1]+"-"+dateArr[0]+"<br/> Expected Generation: {y} kWh",
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
                    textStyle:{
                        fontFamily:'roboto',
                        fontStyle:'bold',
                        fontSize:12,
                        color:'#504E4E'
                    },
                    formatter:function(params){

                        if(time_type == 'day') {

                            return today_date+' '+params.name+'<br>'+params.seriesName+': '+params.data;
                        }
                        else if(time_type == 'month') {

                            return params.name+'-'+today_date+'<br>'+params.seriesName+': '+params.data;
                        }
                        else {

                            return getMonthName(params.name)+' '+today_date+'<br>'+params.seriesName+': '+params.data;
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
                    axisTick:{
                        interval: (time_type == 'month') ? 1 : 0
                    },
                    axisLabel:{
                        interval: (time_type == 'month') ? 1 : 0
                    },
                    axisLine:{
                        lineStyle:{
                            color:'#666666',
                        }
                    },
                },
                yAxis: {
                    type: 'value',
                    minInterval: 1,
                    splitNumber:4,
                    splitLine: {
                        lineStyle: {
                            color: '#f4f4f4',
                        }
                    },
                    axisLine:{
                        show:true,
                        lineStyle:{
                            color:'#666666',
                        }
                    },
                    axisTick:{
                        show:true,
                        alignWithLabel:true
                    },
                },
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function costFormatter(num) {

            if(Math.abs(num) > 999 && Math.abs(num) <= 999999) {
                return Math.sign(num)*((Math.abs(num)/1000).toFixed(2)) + ' K';
            }
            else if(Math.abs(num) > 999999 && Math.abs(num) <= 9999999999){
                return Math.sign(num)*((Math.abs(num)/1000000).toFixed(2)) + ' M';
            }
            else {
                return Math.sign(num)*Math.abs(num)
            }

        }

        function energyFormatter(num) {

            if(Math.abs(num) > Math.pow(10, 3) && Math.abs(num) <= Math.pow(10, 6)) {
                return Math.sign(num)*((Math.abs(num)/Math.pow(10, 3)).toFixed(2)) + ' M';
            }
            else if(Math.abs(num) > Math.pow(10, 6) && Math.abs(num) <= Math.pow(10, 9)){
                return Math.sign(num)*((Math.abs(num)/Math.pow(10, 6)).toFixed(2)) + ' G';
            }
            else if(Math.abs(num) > Math.pow(10, 9) && Math.abs(num) <= Math.pow(10, 12)){
                return Math.sign(num)*((Math.abs(num)/Math.pow(10, 9)).toFixed(2)) + ' T';
            }
            else {
                return Math.sign(num)*Math.abs(num) + ' k';
            }

        }

        function getMonthName(name) {

            for(var i = 0; i < month_name_arr.length; i++) {

                if(name == month_name_arr[i].slice(0,3)) {

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
        function exportCsvDataValues(date, time,types) {
            console.log([date, time,types]);
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
