@extends('layouts.admin.master')

@section('title', 'User Dashboard')

@section('content')

<style type="text/css">

    .ch_a_day_vt {
        height: 100px !important;
        bottom: 17px !important;
    }

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
    .radio-toolbar {
    margin: 10px;
    }

    .radio-toolbar input[type="radio"] {
    opacity: 0;
    position: fixed;
    width: 0;
    }

    .radio-toolbar label {
        display: inline-block;
        background-color: #063c6e;
        padding: 8px 16px;
        font-family: sans-serif, Arial;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        color: #fff;
        cursor: pointer;
    }

    .radio-toolbar label:hover {
    background-color: #063c6e;
    }

    .radio-toolbar input[type="radio"]:focus + label {
        border: 2px dashed #444 !important;
    }

    .radio-toolbar input[type="radio"]:checked + label {
        background-color: #bfb !important;
        border-color: #4c4 !important;
        color: #f1556c !important;
    }

    .en_gener_vt .ch_year_vt_plant {
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

        .home-companise_dash-vt {
            float: left;
            width: 100% !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__choice {
            background-color: #063c6e;
            border: none;
            color: #fff !important;
            border-radius: 3px;
            padding: 0 7px;
            margin-top: 6px;
        }

        .home-companise_dash-vt .select2-container .select2-selection--multiple {
            min-height: 34px;
            height: auto !important;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .card {
            margin-bottom: 0;
            min-height: 350px !important;
        }
        .card-stat-vt {
        min-height: 422px !important;
        }
        .card-stat-vt.hig_vt{
            min-height: 500px !important;
        }
        table.dataTable {
            border-collapse: collapse !important;
            margin-bottom: 6px !important;
        }

        .tree-planting-vt {
            width: 110%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding-bottom: 32px;
        }
        .content-page .container-fluid {
        border: 1px solid #ece8e8;
        border-radius: 10px;
        padding: 9px 15px;
        margin-bottom: 15px;
    }
    hr{
        display: none !important;
    }

    .satisfied_vt {
        background-color: #fff !important;
        color: #f1556c !important;
        border: 1px solid #f1556c !important;
    }

    .satisfied_vt:hover {
    background-color: #063c6e;
    }
    .modal-footer .btn-primary{
        background-color: #063c6e !important;
        color: #fff !important;
        border: 1px solid #063c6e !important;
    }
    .modal-footer .btn-secondary{
        background-color: #f1556c !important;
        color: #fff !important;
        border: 1px solid #f1556c !important;
    }
    div#alertChartDiv {
    margin-top: 00px;
}
.card-stat-vt.alerts_gra_vt{
    min-height: 300px !important;
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
        margin-top: -10px !important;
    }
    </style>

@php
    $system_type_all_plants = 'Self-Consumption';
@endphp

<link href = "{{ asset('assets/css/jquery-ui.css')}}" rel = "stylesheet">
<!-- Start Content-->
<div class="bred_area_vt">

    <div class="col-lg-12 inverter_battery_vt mt-3">
        <?php
        $plant_ids = \App\Http\Models\PlantUser::where('user_id', Request::user()->id)->pluck('plant_id');
        $userPlants = \App\Http\Models\Plant::whereIn('id', $plant_ids)->pluck('system_type');
        $arrayData = count(array_unique(json_decode(json_encode($userPlants),true)));
        ?>
        @if($arrayData != 1)
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    {{--                <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : ''}}" id="Grid-tab" data-toggle="tab" href="{{ route('admin.dashboard') }}" role="tab" aria-controls="Grid" aria-selected="false">On Grid</a>--}}
                    <a class="nav-link {{ Request::is('admin/user-dashboard') ? 'active' : ''}}" id="Grid-tab" href="{{ route('user.dashboard') }}">On Grid</a>
                </li>
                <li class="nav-item">
                    {{--                <a class="nav-link {{ Request::is('admin/Plants') ? 'active' : ''}}" id="Hybrid-tab" data-toggle="tab" href="{{ route('admin.plants') }}" role="tab" aria-controls="Hybrid" aria-selected="true">Hybrid</a>--}}
                    <a class="nav-link {{ Request::is('admin/Plants') ? 'active' : ''}}" id="Hybrid-tab" href="{{ route('admin.plants') }}">Hybrid</a>
                </li>

            </ul>
        @endif
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="home-companies-area-vt">
                <form id="filtersForm" class="home-companise_dash-vt" action="{{route('user.dashboard')}}" method="GET">
                    <?php
                        $filter = Session::get('filter');
                    ?>
                    <div class="form-group" style="min-width: 90px;">
                        <select class="form-control select2-multiple" name="plant_name[]" id="plant_name" data-toggle="select2" multiple>
                            @if(isset($filter_data['plants']) && $filter_data['plants'])
                            @foreach($filter_data['plants'] as $key => $plant2)
                            <option value="{{ $plant2->id }}" <?php echo isset($filter['plant_name']) && in_array($plant2->id, $filter['plant_name'])  ? 'selected' : '' ?>>{{ $plant2->plant_name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="plant_status" id="plant_status">
                            <option value="all" {{ app('request')->input('plant_status') == 'all' ? 'selected' : ''}}>Plant Status</option>
                            <option value="Y" {{app('request')->input('plant_status') == 'Y' ? 'selected' : ''}}>Online</option>
                            <option value="N" {{app('request')->input('plant_status') == 'N' ? 'selected' : ''}}>Offline</option>
                            <option value="fault" {{app('request')->input('plant_status') == 'fault' ? 'selected' : ''}}>Fault</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="plant_type" id="plant_type">
                            <option value="all">Plant Type</option>
                            @if(isset($filter_data['plant_type']) && $filter_data['plant_type'])
                            @foreach($filter_data['plant_type'] as $plant_type)
                            <option value="{{ $plant_type->id }}" <?php echo isset($filter['plant_type']) && $filter['plant_type'] == $plant_type->id  ? 'selected' : '' ?>>{{ $plant_type->type }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="system_type" id="system_type">
                            <option value="all">System Type</option>
                            @if(isset($filter_data['system_type']) && $filter_data['system_type'])
                            @foreach($filter_data['system_type'] as $system_type)
                            <option value="{{ $system_type->id }}" <?php echo isset($filter['system_type']) &&  $filter['system_type'] ==  $system_type->id ? 'selected' : '' ?>>{{ $system_type->type }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="capacity" id="capacity">
                            <option value="all">Capacity</option>
                            @if(isset($filter_data['capacity_array']) && $filter_data['capacity_array'])
                            @foreach($filter_data['capacity_array'] as $capacity_data)
                            <option value="{{ $capacity_data->capacity }}" <?php echo isset($filter['capacity']) && $filter['capacity'] == $capacity_data->capacity  ? 'selected' : '' ?>>{{ $capacity_data->capacity }} kW</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="province" id="province">
                            <option value="all">Province</option>
                            @if(isset($filter_data['province_array']) && $filter_data['province_array'])
                            @foreach($filter_data['province_array'] as $province_data)
                            <option value="{{ $province_data->province }}" <?php echo isset($filter['province']) && $filter['province'] == $province_data->province  ? 'selected' : '' ?>>{{ $province_data->province }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="city" id="city">
                            <option value="all">City</option>
                            @if(isset($filter_data['city_array']) && $filter_data['city_array'])
                            @foreach($filter_data['city_array'] as $city_data)
                            <option value="{{ $city_data->city }}" <?php echo isset($filter['city']) && $filter['city'] == $city_data->city  ? 'selected' : '' ?>>{{ $city_data->city }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <select class="form-control select2-multiple" name="plants[]" id="plants" data-toggle="select2" multiple="" data-placeholder="Choose Plants">
                            @if(isset($filter_data['plants']) && $filter_data['plants'])
                            @foreach($filter_data['plants'] as $plant1)
                            <option value="{{ $plant1->id }}" <?php echo isset($filter['plants']) &&  in_array($plant1->id, $filter['plants']) ? 'selected' : '' ?>>{{ $plant1->plant_name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div> -->

                    <div class="btn-companiescl-vt" id="searchButtonDiv">
                        <button type="submit" class="btn_se_cle_vt" id="searchFilters">
                            <img src="{{ asset('assets/images/search_01.svg')}}" alt="search">
                        </button>
                        <button type="button" class="btn_se_cle_vt" id="clearFilters">
                            <img src="{{ asset('assets/images/cle_02.svg')}}" alt="clear">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-xl-5 ">

    <section class="">

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

            <div class="col-lg-4 mb-3 mb-lg-0">
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

                    <h3 class="three">{{isset($current['grid']) && !empty($current['grid']) ? $current['grid'] : 0 }}</h3>

                    @endif

                </div>

            </div>

            <div class="col-lg-8 mb-3 mb-lg-0">

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
                                <span class="comm_fail" style="font-size: 9px; margin-left:2px;">Power Outage or Communication Failure</span>

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

                                @if($current['generation'] == 0)

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

                <div class="plant-area-vt">
                    <p>Daily Generation</p>
                    <h3 class="one"> {{$daily['generation']}}</h3>
                    <p>Monthly Generation </p>
                    <h3 class="two"> {{$monthly['generation']}}</h3>
                    <p>Yearly Generation </p>
                    <h3 class="three"> {{$yearly['generation']}}</h3>
                </div>

            </div>
            <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="card-header">

                    <h2 class="head_real_vt">Consumption</h2>
                </div>
                <div class="plant-area-vt">
                    <p>Daily Consumption</p>
                    <h3 class="one"> {{$daily['consumption']}}</h3>
                    <p>Monthly Consumption </p>
                    <h3 class="two"> {{$monthly['consumption']}}</h3>
                    <p>Yearly Consumption </p>
                    <h3 class="three"> {{$yearly['consumption']}}</h3>
                </div>

            </div>
            @if($system_type_all_plants != 'All on Grid')
            <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="card-header">

                    <h2 class="head_real_vt">Net Grid Units</h2>
                </div>
                <div class="plant-area-vt">
                    <p>Daily Grid</p>
                    <h3 class="one"> {{$daily['netGridSign'].''.$daily['netGrid']}}</h3>
                    <p>Monthly Grid </p>
                    <h3 class="two"> {{$monthly['netGridSign'].''.$monthly['netGrid']}}</h3>
                    <p>Yearly Grid </p>
                    <h3 class="three"> {{$yearly['netGridSign'].''.$yearly['netGrid']}}</h3>
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
                    <p>Monthly Revenue </p>
                    <h3 class="two"> {{$monthly['revenue']}}</h3>
                    <p>Yearly Revenue </p>
                    <h3 class="three"> {{$yearly['revenue']}}</h3>
                </div>

            </div>

            @endif

        </div>

        <div class="row">

            <div class="col-lg-8 mb-3">
                @if($plant->system_type != 'All on Grid')
                <div class="card p-0 mb-3">

                    <div class="card-header">

                        <h2 class="All-graph-heading-vt">Energy Buy & Sell</h2>

                    </div>

                    <div class="mt-3">

                        <div class="daily-energy-vt">

                            <div class="energy_spac_vt">
                                <p>Daily Bought</p>
                                <h3 class="one"> {{$daily['boughtEnergy']}}</h3>
                                <p>Monthly Energy Bought </p>
                                <h3 class="two"> {{$monthly['boughtEnergy']}}</h3>
                                <p>Yearly Energy Bought </p>
                                <h3 class="three"> {{$yearly['boughtEnergy']}}</h3>
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
                            </div>
                        </div>

                    </div>

                </div>
                @endif

                <div class="card mb-3 history_gr_area_vt">
                    <div class="card weather_card_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Weather</h2>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable_1" class="table table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th style="text-align: left !important;width: 98px;padding-left: 15px;">City</th>
                                        <th>Sunrise</th>
                                        <th>Sunset</th>
                                        <th>Temp (C<sup>o</sup>)</th>
                                        <th>Condition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($weather)
                                    @foreach($weather as $city_wise)
                                    <tr>
                                        <th scope="row" style="text-align: left !important;padding-left: 15px;">{{ $city_wise->city }}</th>
                                        <td>{{ $city_wise->sunrise }}</td>
                                        <td>{{ $city_wise->sunset }}</td>
                                        <td>{{ $city_wise->temperature}} &#8451;</td>
                                        <td  title="{{ $city_wise->condition}}" style="padding: 8px 0px;transform: translateY(-5px);text-align:center;"><img src="http://openweathermap.org/img/w/{{ $city_wise->icon }}.png" alt="Current" width="40"></td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                    <div class="card mb-3 history_gr_area_vt" style="padding-bottom: 9px;">

                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">History</h2>
                            <div class="btn-companies-vt">
{{--                                <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">--}}
{{--                                    Export CSV--}}
{{--                                </button>--}}
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
                                <div class="mt-4 en_gener_vt">
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

                                <h3 class="All-graph-heading-vt">All Tickets</h3>
                                <a type="button" href="{{route('admin.ticket.add')}}" class="btn-add-vt float-right mt-1 mr-1" style="line-height: 36px;">Add Ticket</a>

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
                                        <tr>
                                            <th scope="row">{{$item->id}}</th>
                                            <td>{{$item->title}}</td>
                                            <td>{{$item->source_name}}</td>
                                            <td>{{$item->priority_name}}</td>
                                            <td>{{$item->status_name}}</td>
                                            <td>{{date('h:i A, d-m-Y', strtotime($item->closed_time))}}</td>
                                            <td>{{$item->agents}}</td>
                                            <td>{{date('h:i A, d-m-Y ', strtotime($item->created_at))}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

                <div class="card">
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
                </div>

            </div>

            <div class="col-lg-4 mb-3">
                {{-- @if($plant->system_type != 'All on Grid') --}}
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
                    </div>


                </div>
                {{-- @endif --}}

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
                    <span class="noRecord" style="display: none;">
                        NO ALERTS to SHOW
                    </span>
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

    <section>
        <!-- Modal -->
        <div class="modal fade" id="ticketFeedback" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ticket Feedback</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
                </div>
                <form action="{{route('admin.ticket.feedback.update')}}" method="POST">
                    @csrf
                <div class="modal-body">
                    @foreach ($tickets_feedback as $key => $item)
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <span>Your ticket # <b>{{$item->id}}</b> with title <b>{{$item->title}}</b> has been marked for closure. Please acknowledge if your issue has been resolved otherwise press unresolved so our team can further look into the issue.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="radio-toolbar">
                                        <input type="radio" id="open-{{$item->id}}" name="{{$item->id}}" value="N">
                                        <label class="satisfied_vt" for="open-{{$item->id}}">Unresolved</label>

                                        <input type="radio" id="close-{{$item->id}}" name="{{$item->id}}" value="Y">
                                        <label for="close-{{$item->id}}">Resolved</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>

            </div>
            </div>
        </div>
    </section>

    <!-- Single plant dashboard end -->

</div>
<script type="text/javascript" src="{{ asset('assets/js/canvasjs.min.js')}}"></script>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js')}}" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src = "{{ asset('assets/js/jquery-1.10.2.js')}}"></script>
<script src = "{{ asset('assets/js/jquery-ui.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/echarts.min.js')}}"></script>
<script type="text/javascript">

    var plant_axis_grid = 4;
    var plant_name = [{!! $plant->id !!}];
    var filterss_arr = {};
    var plant_name = {};
    var tickets_feedback_count = {!! $tickets_feedback !!};
    var month_name_arr = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    $(document).ready(function() {

        if(tickets_feedback_count.length > 0) {

            $('#ticketFeedback').modal('show');
        }

        $('#clearFilters').on('click',function(e) {

            $('#plant_status').prop('selectedIndex',0);
            $('#plant_type').prop('selectedIndex',0);
            $('#system_type').prop('selectedIndex',0);
            $('#capacity').prop('selectedIndex',0);
            $('#province').prop('selectedIndex',0);
            $('#city').prop('selectedIndex',0);

            $(".select2-multiple").val("");
            $(".select2-multiple").trigger("change");

            $('#filtersForm').trigger('submit');

        });

        if(($('#plant_name').val()).length != 0) {

            plant_name = $('#plant_name').val();
        }

        if($('#plant_status').val() != 'all') {

            if($('#plant_status').val() == 'fault') {

                filterss_arr['alarmLevel'] = '1';
            }

            else {

                filterss_arr['is_online'] = $('#plant_status').val();
            }

        }

        if($('#plant_type').val() != 'all') {

            filterss_arr['plant_type'] = $('#plant_type').val();
        }

        if($('#system_type').val() != 'all') {

            filterss_arr['system_type'] = $('#system_type').val();
            }

        if($('#capacity').val() != 'all') {

            filterss_arr['capacity'] = $('#capacity').val();
        }

        if($('#province').val() != 'all') {

            filterss_arr['province'] = $('#province').val();
        }

        if($('#city').val() != 'all') {

            filterss_arr['city'] = $('#city').val();
        }

        var currDate = getCurrentDate();

        $('input[name="priorityGraphDay"]').val(currDate.todayDate);
        $('input[name="priorityGraphMonth"]').val(currDate.todayMonth);
        $('input[name="priorityGraphYear"]').val(currDate.todayYear);
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

        var priority_date = $('input[name="priorityGraphDay"]').val();
        var priority_time = 'day';
        var types = 'generation';
        var history_date = $('input[name="historyGraphDay"]').val();
        var history_time = 'day';
        var expGen_date = $('input[name="expGenGraphYear"]').val();
        var expGen_time = 'year';
        var env_date = $('input[name="envGraphDay"]').val();
        var env_time = 'day';
        var alert_date = $('input[name="alertGraphDay"]').val();
        var alert_time = 'day';
        var types = 'generation';
        var id = {!!$plant->id!!};

        changePriorityDayMonthYear(priority_date, priority_time);
        changeHistoryDayMonthYear(types, id, history_date, history_time);
        changeExpGenDayMonthYear(expGen_date, expGen_time);
        changeENVDayMonthYear();
        changeAlertDayMonthYear();
        priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
        historyGraphAjax(types, filterss_arr, plant_name, id, history_date, history_time);
        expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
        envGraphAjax(env_date, env_time, filterss_arr, plant_name);
        alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);

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

        $('.J-yearMonthDayPicker-single-priority').datePicker({
            format: 'YYYY-MM-DD',
            language: 'en',
            hide: function(type) {
                changePriorityDayMonthYear(this.$input.eq(0).val(), 'day');
            }
        });

        $('.J-yearMonthPicker-single-priority').datePicker({
            format: 'MM-YYYY',
            language: 'en',
            hide: function(type) {
                console.log(this.$input.eq(0).val());
                changePriorityDayMonthYear(this.$input.eq(0).val(), 'month');
            }
        });

        $('.J-yearPicker-single-priority').datePicker({
            format: 'YYYY',
            language: 'en',
            hide: function(type) {
                changePriorityDayMonthYear(this.$input.eq(0).val(), 'year');
            }
        });

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
                changeENVDayMonthYear(this.$input.eq(0).val(), 'day');
            }
        });

        $('.J-yearMonthPicker-single-env').datePicker({
            format: 'MM-YYYY',
            language: 'en',
            hide: function(type) {
                console.log(this.$input.eq(0).val());
                changeENVDayMonthYear(this.$input.eq(0).val(), 'month');
            }
        });

        $('.J-yearPicker-single-env').datePicker({
            format: 'YYYY',
            language: 'en',
            hide: function(type) {
                changeENVDayMonthYear(this.$input.eq(0).val(), 'year');
            }
        });

        $('.J-yearMonthDayPicker-single-alert').datePicker({
            format: 'YYYY-MM-DD',
            language: 'en',
            hide: function(type) {
                changeAlertDayMonthYear(this.$input.eq(0).val(), 'day');
            }
        });

        $('.J-yearMonthPicker-single-alert').datePicker({
            format: 'MM-YYYY',
            language: 'en',
            hide: function(type) {
                console.log(this.$input.eq(0).val());
                changeAlertDayMonthYear(this.$input.eq(0).val(), 'month');
            }
        });

        $('.J-yearPicker-single-alert').datePicker({
            format: 'YYYY',
            language: 'en',
            hide: function(type) {
                changeAlertDayMonthYear(this.$input.eq(0).val(), 'year');
            }
        });

        $('#priorityGraphPreviousDay').on('click', function() {

            show_date = $("input[name='priorityGraphDay']").val();
            var datess = new Date(show_date);
            console.log(datess);
            datess.setDate(datess.getDate() - 1);
            priority_date = formatDate(datess);
            $('input[name="priorityGraphDay"]').val('');
            $('input[name="priorityGraphDay"]').val(priority_date);
            console.log($("input[name='priorityGraphDay']").val());
            priority_time = 'day';
            priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
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
            priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
        });

        $('#priorityGraphPreviousMonth').on('click', function() {

            show_date = $("input[name='priorityGraphMonth']").val();
            priority_date = formatPreviousMonth(show_date);
            $('input[name="priorityGraphMonth"]').val('');
            $('input[name="priorityGraphMonth"]').val(priority_date);
            console.log($("input[name='priorityGraphMonth']").val());
            priority_time = 'month';
            priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
        });

        $('#priorityGraphForwardMonth').on('click', function() {

            show_date = $("input[name='priorityGraphMonth']").val();
            priority_date = formatForwardMonth(show_date);
            $('input[name="priorityGraphMonth"]').val('');
            $('input[name="priorityGraphMonth"]').val(priority_date);
            console.log($("input[name='priorityGraphMonth']").val());
            priority_time = 'month';
            priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
        });

        $('#priorityGraphPreviousYear').on('click', function() {

            show_date = $("input[name='priorityGraphYear']").val();
            priority_date = formatPreviousYear(show_date);
            $('input[name="priorityGraphYear"]').val('');
            $('input[name="priorityGraphYear"]').val(priority_date);
            console.log($("input[name='priorityGraphYear']").val());
            priority_time = 'year';
            priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
        });

        $('#priorityGraphForwardYear').on('click', function() {

            show_date = $("input[name='priorityGraphYear']").val();
            priority_date = formatForwardYear(show_date);
            $('input[name="priorityGraphYear"]').val('');
            $('input[name="priorityGraphYear"]').val(priority_date);
            console.log($("input[name='priorityGraphYear']").val());
            priority_time = 'year';
            priorityGraphAjax(types, id, history_date, history_time);
        });

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
            historyGraphAjax(types, filterss_arr, plant_name, id, history_date, history_time);
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
            historyGraphAjax(types, id, filterss_arr, plant_name, history_date, history_time);
        });

        $('#historyGraphPreviousMonth').on('click', function() {

            show_date = $("input[name='historyGraphMonth']").val();
            history_date = formatPreviousMonth(show_date);
            $('input[name="historyGraphMonth"]').val('');
            $('input[name="historyGraphMonth"]').val(history_date);
            console.log($("input[name='historyGraphMonth']").val());
            history_time = 'month';
            historyGraphAjax(types, filterss_arr, plant_name, id, history_date, history_time);
        });

        $('#historyGraphForwardMonth').on('click', function() {

            show_date = $("input[name='historyGraphMonth']").val();
            history_date = formatForwardMonth(show_date);
            $('input[name="historyGraphMonth"]').val('');
            $('input[name="historyGraphMonth"]').val(history_date);
            console.log($("input[name='historyGraphMonth']").val());
            history_time = 'month';
            historyGraphAjax(types, filterss_arr, plant_name, id, history_date, history_time);
        });

        $('#historyGraphPreviousYear').on('click', function() {

            show_date = $("input[name='historyGraphYear']").val();
            history_date = formatPreviousYear(show_date);
            $('input[name="historyGraphYear"]').val('');
            $('input[name="historyGraphYear"]').val(history_date);
            console.log($("input[name='historyGraphYear']").val());
            history_time = 'year';
            historyGraphAjax(types, filterss_arr, plant_name, id, history_date, history_time);
        });

        $('#historyGraphForwardYear').on('click', function() {

            show_date = $("input[name='historyGraphYear']").val();
            history_date = formatForwardYear(show_date);
            $('input[name="historyGraphYear"]').val('');
            $('input[name="historyGraphYear"]').val(history_date);
            console.log($("input[name='historyGraphYear']").val());
            history_time = 'year';
            historyGraphAjax(types, filterss_arr, plant_name, id, history_date, history_time);
        });

        $('#expGenGraphPreviousYear').on('click', function() {

            show_date = $("input[name='expGenGraphYear']").val();
            expGen_date = formatPreviousYear(show_date);
            $('input[name="expGenGraphYear"]').val('');
            $('input[name="expGenGraphYear"]').val(expGen_date);
            console.log($("input[name='expGenGraphYear']").val());
            expGen_time = 'year';
            expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name, filterss_arr, plant_name);
        });

        $('#expGenGraphForwardYear').on('click', function() {

            show_date = $("input[name='expGenGraphYear']").val();
            expGen_date = formatForwardYear(show_date);
            $('input[name="expGenGraphYear"]').val('');
            $('input[name="expGenGraphYear"]').val(expGen_date);
            console.log($("input[name='expGenGraphYear']").val());
            expGen_time = 'year';
            expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name, filterss_arr, plant_name);
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
            envGraphAjax(env_date, env_time, filterss_arr, plant_name);
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
            envGraphAjax(env_date, env_time, filterss_arr, plant_name);
        });

        $('#envGraphPreviousMonth').on('click', function() {

            show_date = $("input[name='envGraphMonth']").val();
            env_date = formatPreviousMonth(show_date);
            $('input[name="envGraphMonth"]').val('');
            $('input[name="envGraphMonth"]').val(env_date);
            console.log($("input[name='envGraphMonth']").val());
            env_time = 'month';
            envGraphAjax(env_date, env_time, filterss_arr, plant_name);
        });

        $('#envGraphForwardMonth').on('click', function() {

            show_date = $("input[name='envGraphMonth']").val();
            env_date = formatForwardMonth(show_date);
            $('input[name="envGraphMonth"]').val('');
            $('input[name="envGraphMonth"]').val(env_date);
            console.log($("input[name='envGraphMonth']").val());
            env_time = 'month';
            envGraphAjax(env_date, env_time, filterss_arr, plant_name);
        });

        $('#envGraphPreviousYear').on('click', function() {

            show_date = $("input[name='envGraphYear']").val();
            env_date = formatPreviousYear(show_date);
            $('input[name="envGraphYear"]').val('');
            $('input[name="envGraphYear"]').val(env_date);
            console.log($("input[name='envGraphYear']").val());
            env_time = 'year';
            envGraphAjax(env_date, env_time, filterss_arr, plant_name);
        });

        $('#envGraphForwardYear').on('click', function() {

            show_date = $("input[name='envGraphYear']").val();
            env_date = formatForwardYear(show_date);
            $('input[name="envGraphYear"]').val('');
            $('input[name="envGraphYear"]').val(env_date);
            console.log($("input[name='envGraphYear']").val());
            env_time = 'year';
            envGraphAjax(env_date, env_time, filterss_arr, plant_name);
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
            alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
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
            alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
        });

        $('#alertGraphPreviousMonth').on('click', function() {

            show_date = $("input[name='alertGraphMonth']").val();
            alert_date = formatPreviousMonth(show_date);
            $('input[name="alertGraphMonth"]').val('');
            $('input[name="alertGraphMonth"]').val(alert_date);
            console.log($("input[name='alertGraphMonth']").val());
            alert_time = 'month';
            alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
        });

        $('#alertGraphForwardMonth').on('click', function() {

            show_date = $("input[name='alertGraphMonth']").val();
            alert_date = formatForwardMonth(show_date);
            $('input[name="alertGraphMonth"]').val('');
            $('input[name="alertGraphMonth"]').val(alert_date);
            console.log($("input[name='alertGraphMonth']").val());
            alert_time = 'month';
            alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
        });

        $('#alertGraphPreviousYear').on('click', function() {

            show_date = $("input[name='alertGraphYear']").val();
            alert_date = formatPreviousYear(show_date);
            $('input[name="alertGraphYear"]').val('');
            $('input[name="alertGraphYear"]').val(alert_date);
            console.log($("input[name='alertGraphYear']").val());
            alert_time = 'year';
            alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
        });

        $('#alertGraphForwardYear').on('click', function() {

            show_date = $("input[name='alertGraphYear']").val();
            alert_date = formatForwardYear(show_date);
            $('input[name="alertGraphYear"]').val('');
            $('input[name="alertGraphYear"]').val(alert_date);
            console.log($("input[name='alertGraphYear']").val());
            alert_time = 'year';
            alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
        });

        $("#priority_day_my_btn_vt button").click(function() {

            $('#priority_day_my_btn_vt').children().removeClass("active");
            $(this).addClass("active");

            changePriorityDayMonthYear(priority_date, priority_time);

        });


        $("#history_day_my_btn_vt button").click(function() {

            $('#history_day_my_btn_vt').children().removeClass("active");
            $(this).addClass("active");

            changeHistoryDayMonthYear(types, id, history_date, history_time);

        });

        $("#env_day_my_btn_vt button").click(function() {

            $('#env_day_my_btn_vt').children().removeClass("active");
            $(this).addClass("active");

            changeENVDayMonthYear(env_date, env_time);

        });

        $("#alert_day_my_btn_vt button").click(function() {

            $('#alert_day_my_btn_vt').children().removeClass("active");
            $(this).addClass("active");

            changeAlertDayMonthYear(alert_date, alert_time);

        });

    });

    function changePriorityDayMonthYear(date, time) {

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

        priorityGraphAjax(date, time, filterss_arr, plant_name);
    }

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

        historyGraphAjax(types, filterss_arr, plant_name, id, date, time);
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

        expGenGraphAjax(date, time, filterss_arr, plant_name, filterss_arr, plant_name);
    }

    function changeENVDayMonthYear(date, time) {

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

        envGraphAjax(date, time, filterss_arr, plant_name);
    }

    function changeAlertDayMonthYear(date, time) {

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

        alertGraphAjax(date, time, filterss_arr, plant_name);
    }

    function historyGraphAjax(types, filters_arr, plant_name, id, date, time) {

        filters = JSON.stringify(filters_arr);
        plantName = JSON.stringify(plant_name);

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
                'request_from': 'user_dashboard',
                'date': date,
                'filter' : filters,
                'plant_name' : plantName,
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

        $('#chartContainerGenDiv').empty();
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
                $('.percentageActual').empty();
                $('.totalExpected').empty();

                $('#chartContainerGenDiv').append('<div id="chartContainerGen" style="height: 320px; width: 100%;"></div>');
                $('.percentageActual').html(data.percentage+'%');
                $('.totalExpected').html(data.expected_value);
                $('#expGenSpinner').hide();

                plantGenGraph(data.exp_ac_graph, data.legend_array, date, data.percentage);
            },
            error: function(data) {
                console.log(data);
                //alert('Some Error Occured!');
            }
        });
    }

    function envGraphAjax(date, time, filter, plant_name) {

        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);

        $('#envPlantingDiv h2').remove();
        $('#envReductionDiv h3').remove();
        $('#envGenerationDiv').empty();
        $('#envSpinner').show();

        $.ajax({
            url: "{{ route('admin.dashboard.env.graph') }}",
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

                $('#envPlantingDiv h2').remove();
                $('#envReductionDiv h3').remove();
                $('#envGenerationDiv').empty();
                $('#envSpinner').hide();

                $('#envPlantingDiv').append("<h2>"+(data[1] * 0.00131).toFixed(2)+"<samp>tree(s)</samp></h2>");
                $('#envReductionDiv').append("<h3>"+(data[1] * 0.000646155).toFixed(2)+"<samp>T</samp></h3>");
                $('#envGenerationDiv').append('<p><samp class="color07_one_vt"></samp> Total Generation: <span>'+data[0]+' </span></p>');
            },
            error: function(data) {
                console.log(data);
                //alert('Some Error Occured!');
            }
        });
    }

    function priorityGraphAjax(date, time, filter, plant_name) {

        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);

        $('#priorityGraphDiv').empty();

        $.ajax({
            url: "{{ route('admin.ticket.priority.graph') }}",
            method: "GET",
            data: {
                'date': date,
                'time': time,
                'request_from': 'user_dashboard',
                'filter' : filters,
                'plant_name' : plantName
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
    }

    function alertGraphAjax(date, time, filter, plant_name) {

        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);

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
                'filter' : filters,
                'plant_name' : plantName,
                'from_url' : 'dashboard'
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);

                if(data.total_fault != 0 || data.total_alarm != 0 || data.total_rtu != 0) {
                    $('.noRecord').hide();

                    $('#alertChartDiv div').remove();
                    $('#alertChartDetailDiv').empty();

                    $('#alertChartDiv').append('<div id="alertChart" style="height: 200px; width: 100%;"></div>');
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
                console.log(data);
                //alert('Some Error Occured!');
            }
        });
    }

    function ticketPriorityGraph(data, legend_array) {

        var dom = document.getElementById("priorityContainer");
        var myChart = echarts.init(dom);
        var app = {};

        option = {
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c} ({d}%)',
                textStyle:{
                        color:'#000000',
                        fontFamily:'Roboto',
                        fontWeight:'bold',
                        fontSize:'13'
                    },
            },
            legend: {
                data: legend_array,
                bottom: '3px',
            },
            series: [{
                name: 'Ticket Priority',
                type: 'pie',
                radius: '50%',
                label:{
                    show: true,
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
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }
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
                formatter:function(params,year) {
                    return 'Year '+date+'<br>'+params.name;
                },
                textStyle:{
                        color:'#000000',
                        fontFamily:'Roboto',
                        fontWeight:'bold',
                        fontSize:'13'
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
                formatter:function(params,year) {
                    return 'Year '+date+'<br>'+params.name;
                },
                textStyle:{
                        color:'#000000',
                        fontFamily:'Roboto',
                        fontWeight:'bold',
                        fontSize:'13'
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
                    type: "column",
                    color: "#68AD86",
                    dataPoints: curr_generation
                    },
                    {
                    toolTipContent: "{tooltip} "+date+"<br/>Expected Generation: {y} kWh",
                    markerType: "none",
                    type: "column",
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

    function updateTicketFeedback(date, time, filter, plant_name) {

        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);

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
                'filter' : filters,
                'plant_name' : plantName,
                'from_url' : 'dashboard'
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);

                if(data.total_fault != 0 || data.total_alarm != 0 || data.total_rtu != 0) {
                    $('.noRecord').hide();

                    if(time == 'day') {

                        $('#alertChartDiv div').remove();
                        $('#alertChartDetailDiv').empty();

                        $('#alertChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_a_day_vt"><span>'+data.today_time.split(',')[0]+'</span></div>');
                        $('#alertChartDiv').append('<div id="alertChart" style="height: 200px; width: 100%;" fault_log="' + data.alert_fault + '" alarm_log="' + data.alert_alarm + '" rtu_log="' + data.alert_rtu + '" today_time="' + data.today_time + '"></div>');
                        $('#alertChartDetailDiv').append('<p><samp class="color03_one_vt"></samp> Fault: <span> '+data.total_fault+'</span></p><p><samp class="color04_one_vt"></samp> Alarm: <span> '+data.total_alarm+'</span></p><p><samp class="color05_one_vt"></samp> RTU: <span> '+data.total_rtu+'</span></p>');
                        $('#alertSpinner').hide();

                        plantAlertGraph(date, data.max_fault, data.max_alarm, data.max_rtu);
                    }

                    else if(time == 'month' || time == 'year') {

                        $('#alertChartDiv div').remove();
                        $('#alertChartDetailDiv').empty();

                        if(time == 'month') {
                            $('#alertChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_a_month_vt"><span>2</span></div>');
                        }
                        else if(time == 'year') {
                            $('#alertChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_a_year_vt"><span>Jan</span></div>');
                        }

                        $('#alertChartDiv').append('<div id="alertChart" style="height: 200px; width: 100%;"></div>');
                        $('#alertChartDetailDiv').append('<p><samp class="color03_one_vt"></samp> Fault: <span> '+data.total_fault+'</span></p><p><samp class="color04_one_vt"></samp> Alarm: <span> '+data.total_alarm+'</span></p><p><samp class="color05_one_vt"></samp> RTU: <span> '+data.total_rtu+'</span></p>');
                        $('#alertSpinner').hide();

                        alert_month_gen(time, date, data.fault_log_data, data.alarm_log_data, data.rtu_log_data);
                    }
                }

                else {

                    $('.noRecord').show();
                    $('#alertSpinner').hide();
                }
            },
            error: function(data) {
                console.log(data);
                //alert('Some Error Occured!');
            }
        });
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
</script>

<script type="text/javascript" src="{{ asset('assets/js/loader.js')}}"></script>

<script src="{{ asset('assets/js/datepicker.all.js')}}"></script>
<script src="{{ asset('assets/js/datepicker.en.js')}}"></script>
@endsection
