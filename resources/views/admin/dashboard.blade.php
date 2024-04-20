@extends('layouts.admin.master')
@section('title', 'Dashboard')
@section('content')
    <style>
        table.dataTable.no-footer {
            border-bottom: none;
        }

        .kWh_eng_vt {
            top: 26px !important;
            left: 27px !important;
        }

        .home-companies-area-vt{
            margin-bottom: 10px !important;
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
            /* height: auto !important; */
            overflow: hidden;
            /* margin-bottom: 15px; */
        }
        .home-companise_dash-vt .select2-container--default .select2-search--inline .select2-search__field {
            min-width: 105px;
        }

        span.noRecord {
            width: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 110px;
        }

        .card.alerts_card_vt.hum_card_alar {
            min-height: 455px !important;
        }

        .pla_body_padd_vt .select2-container .select2-selection--multiple {
            border: none !important;
            background-color: #ffffff !important;
            box-shadow: 0 0px 0px 0 rgba(0, 0, 0, .1) !important;
        }

        .pla_body_padd_vt .select2-container--default.select2-container--focus .select2-selection--multiple {
            border: none !important;
            outline: 0 !important;
            background: #ffffff !important;
        }
        .card.energyg_vt{
            min-height: 440px;
        }
        .environmentalbenefits_vt{
            min-height: 414px;
        }

        /* @media only screen and (max-width: 1212px) and (min-width: 992px)  {
            .form-group{
                width: 10% !important;
            }
        } */

    </style>
    <img id="mapMarkerIcon" src="{{ asset('assets/images/map_marker.svg')}}" alt="setting" style="display: none;">
    <div class="content">
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
                        <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : ''}}" id="Grid-tab" href="{{ route('admin.dashboard') }}">On Grid</a>
                    </li>
                    <li class="nav-item">
                        {{--                <a class="nav-link {{ Request::is('admin/Plants') ? 'active' : ''}}" id="Hybrid-tab" data-toggle="tab" href="{{ route('admin.plants') }}" role="tab" aria-controls="Hybrid" aria-selected="true">Hybrid</a>--}}
                        <a class="nav-link {{ Request::is('admin/Plants') ? 'active' : ''}}" id="Hybrid-tab" href="{{ route('admin.plants') }}">Hybrid</a>
                    </li>
                </ul>
            @endif
        </div>
        <div class="card_box_vt_sp mainLoader" id="dashboardSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Start Content-->
        <div class="bred_area_vt">
            <div class="row">
                <div class="col-xl-12">
                    <div class="home-companies-area-vt">
                        <form id="filtersForm" class="home-companise_dash-vt" action="{{route('admin.dashboard')}}" method="GET">
                            <?php
                            $filter = Session::get('filter');
                            $capacity_chunk = Session::get('capacity_chunk');
                            $plant_counter = 1;
                            ?>
                            @if(Auth::user()->roles == 1 || Auth::user()->roles == 2)
                                <div class="form-group" >
                                    <select class="form-control companyFilterMultiSelect" name="company[]" id="company" multiple>
                                        @if(isset($filter_data['company_array']) && $filter_data['company_array'])
                                            @foreach($filter_data['company_array'] as $company_data)
                                                <option value="{{ $company_data->id }}" <?php echo isset($filter['company']) && in_array($company_data->id, $filter['company'])  ? 'selected' : '' ?>>{{ $company_data->company_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif
                            <div class="form-group" style="min-width: 90px;">
                                <select class="form-control plantFilterMultiSelect" name="plant_name[]" id="plant_name" multiple>
                                    @if(isset($filter_data['plants']) && $filter_data['plants'])
                                        @foreach($filter_data['plants'] as $key => $plant)
                                            <option value="{{ $plant->id }}" <?php echo isset($filter['plant_name']) && in_array($plant->id, $filter['plant_name'])  ? 'selected' : '' ?>>{{ $plant->plant_name }}</option>
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
                            @foreach($filter_data['plants'] as $plant)
                                <option value="{{ $plant->id }}" <?php echo isset($filter['plants']) &&  in_array($plant->id, $filter['plants']) ? 'selected' : '' ?>>{{ $plant->plant_name }}</option>
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
        <div class="container-fluid medi_class_vt">
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
            <!-- start page title -->
            <!-- end page title -->
            <!-- end row-->
            <div class="row">

                <div class="col-lg-6 mb-3">
                    <div class="card mb-0">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Operating Status</h2>
                        </div>
                        <br>
                        <div class="card-box donut_chart_card_box" dir="ltr">
                            <div class="gr_text_vt">
                                @if(($online+$offline+$alarmLevel) > 0)
                                    <p>{{round(($online/($online+$offline+$alarmLevel)) * 100), 2}}%</p>
                                @else
                                    <p>0%</p>
                                @endif
                                <h6>{{count($plants_dashboard)}}</h6>
                            </div>
                            <div id="donut-chart" style="height: 295px;"></div>
                        </div>
                        <div class="online_fault_vt">
                            <p><samp class="color_one_vt"></samp> Online: <span> {{ $online }}</span></p>
                            <p><samp class="color_tow_vt"></samp> Offline: <span> {{ $offline }}</span></p>
                            <p><samp class="color_three_vt"></samp> Fault: <span> {{ $alarmLevel }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card energyg_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Energy Generation</h2>
                        </div>
                        <div class="day_month_year_vt" id="energy_gen_day_month_year_vt_day">
                            <button><i id="energy_genGraphPreviousDay" class="fa fa-caret-left"></i></button>
                            <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-energy_gen mt10">
                                <input type="text" autocomplete="off" name="energy_genGraphDay" id="energy_genGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="energy_genGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="energy_gen_day_month_year_vt_month">
                            <button><i id="energy_genGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-energy_gen mt10">
                                    <input type="text" autocomplete="off" name="energy_genGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="energy_genGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="energy_gen_day_month_year_vt_year">
                            <button><i id="energy_genGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-energy_gen mt10">
                                    <input type="text" autocomplete="off" name="energy_genGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="energy_genGraphForwardYear" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_my_btn_vt" id="energy_gen_day_my_btn_vt">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>

                        <div class="card-box">
                            <div class="card_box_vt_sp" id="energyGenSpinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div class="energy_gener_vt">
                                <div class="ch_one_vt" id="chartContainerDiv" style="margin-top: -20px;">
                                    <div class="kWh_eng_vt"></div>
                                    <div class="ch_tr_vt"><span></span></div>
                                    <div id="chartContainer" style="height: 200px; width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="generation-overview-vt" id="chartDetailDiv">

                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Actual Generation / Expected Generation</h2>
                        </div>
                        <div class="day_month_year_vt" id="expGen_day_month_year_vt_day">
                            <button><i id="expGenGraphPreviousDay" class="fa fa-caret-left"></i></button>
                            <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-expGen mt10">
                                <input type="text" autocomplete="off" name="expGenGraphDay" id="expGenGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="expGenGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="expGen_day_month_year_vt_month">
                            <button><i id="expGenGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-expGen mt10">
                                    <input type="text" autocomplete="off" name="expGenGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="expGenGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
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
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        <div class="card-box" dir="ltr">
                            <div class="card_box_vt_sp" id="expGenSpinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div class="mt-4 en_gener_vt">
                                <div class="ch_one_vt" id="chartContainerGenDiv" style="margin-top: -55px;">
                                    <div class="kWh_eng_vt_gen"></div>
                                    <div class="ch_tr_vt"><span></span></div>
                                    <div id="chartContainerGen" style="height: 200px; width: 100%;"></div>
                                </div>
                            </div>
                            <div class="online-fault-vt" id="chartDetailGenDiv" style="margin-top: 55px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card alerts_card_vt hum_card_alar">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Alerts</h2>
                        </div>
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
                        <div class="card-box" dir="ltr">
                            <div class="card_box_vt_sp" id="alertSpinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <span class="noRecord" style="display: none;" style="margin-top:20px;">
                            NO ALERTS to SHOW
                        </span>
                            <br>
                            <div class="chartjs-chart" id="alertChartDiv" style="margin-top: -44px;">
                                <div id="alertChart" style="height: 210px;"></div>
                            </div>
                            <div class="online-fault-vt mb-0" id="alertChartDetailDiv" style="margin-top: 14px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card history_gr_area_vt one">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Cost Saving</h2>
                        </div>
                        <div class="day_month_year_vt" id="saving_day_month_year_vt_day">
                            <button><i id="savingGraphPreviousDay" class="fa fa-caret-left"></i></button>
                            <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-saving mt10">
                                <input type="text" autocomplete="off" name="savingGraphDay" id="savingGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="savingGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="saving_day_month_year_vt_month">
                            <button><i id="savingGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-saving mt10">
                                    <input type="text" autocomplete="off" name="savingGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="savingGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="saving_day_month_year_vt_year">
                            <button><i id="savingGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-saving mt10">
                                    <input type="text" autocomplete="off" name="savingGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="savingGraphForwardYear" class="fa fa-caret-right"></i></button>
                        </div>

                        <div class="day_my_btn_vt" id="saving_day_my_btn_vt">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        <div class="card-box">
                            <div class="card_box_vt_sp" id="savingSpinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div class="chartjs-chart ch_one_vt" id="savingChartDiv" style="margin-top: -21px;">
                            </div>
                            <div class="online-fault-vt mb-0" id="savingChartDetailDiv" style="margin-top:97px !important;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card environmentalbenefits_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Environmental Benefits</h2>
                        </div>
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
                        <div class="card-body">
                            <div class="card_box_vt_sp" id="envSpinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row tree-planting-vt border_tree_vt">
                                        <div class="col-md-12 tree-vt">
                                            <h6>Accumulative Trees <br>Planting</h6>
                                        </div>
                                        <div class="col-md-12 mb-3 mb-md-0"><img src="{{ asset('assets/images/tree_planting.png')}}" alt=""></div>
                                        <div class="col-md-12 tree-vt" id="envPlantingDiv">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row tree-planting-vt">
                                        <div class="col-md-12 tree-vt">
                                            <h6>Accumulative CO<sub>2</sub> Emission <br> Reduction</h6>
                                        </div>
                                        <div class="col-md-12 mb-3 mb-md-0"><img src="{{ asset('assets/images/chimney.png')}}" alt=""></div>
                                        <div class="col-md-12 tree-vt" id="envReductionDiv">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="online-fault-vt" id="envGenerationDiv">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Plant Capacity</h2>
                        </div>
                        <br>
                        <div class="card-box" style="padding-top: 0;" dir="ltr">
                            <r>
                                <div class="plants_vt">Plant(s)</div>
                                <div>
                                    <div id="bar-chart-plant" style="width: 100%; height: 290px;" data-colors="#0F75BC" data-capacity_1="{{ $capacity_1 ? $capacity_1 : 0 }}" data-capacity_2="{{ $capacity_2 ? $capacity_2 : 0 }}" data-capacity_3="{{ $capacity_3 ? $capacity_3 : 0 }}" data-capacity_4="{{ $capacity_4 ? $capacity_4 : 0 }}" data-capacity_5="{{ $capacity_5 ? $capacity_5 : 0 }}"></div>
                                </div>
                        </div>
                        <div class="online-fault-vt">
                            <p><samp class="color08_one_vt"></samp> Total Plants: <span> {{$total_plant}}</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Cost Saving</h2>
                        </div>
                        <div class="card-box">
                            <div class="row w-100">
                                <div class="col-lg-12">
                                    <div class="en_gener_vt" dir="ltr">
                                        <div class="ch_tr_vt"></div>
                                        <div class="kWh_revenue_vt">PKR</div>
                                        <div id="chart2" style="height: 200px;" data-colors="#31B4F8"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-6 mb-3">
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
                                            <td title="{{ $city_wise->condition}}" style="padding: 8px 0px;transform: translateY(-5px);text-align:center;"><img src="http://openweathermap.org/img/w/{{ $city_wise->icon }}.png" alt="Current" width="40"></td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3 mapCard">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Plants Locations</h2>
                        </div>
                        <div class="card-body map_body_vt">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            <!-- <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h2 class="All-graph-heading-vt">All Tickets</h2>
                </div>
                <div class="day_month_year_vt">
                    <button><i class="fa fa-caret-left"></i></button>
                    <input type="text" id="humanfd-datepicker" class="form-control" placeholder="Select">

                    <button><i class="fa fa-caret-right"></i></button>
                </div>
                <div class="day_my_btn_vt">
                    <button class="day_bt_vt active">day</button>
                    <button class="months_bt_vt">month</button>
                    <button class="month_bt_vt">Year</button>
                </div>
                <div class="card-box" dir="ltr">
                    <div class="Yearly_text_vt">Yearly Tickets :63</div>
                    <br>
                    <div id="sparkline3" data-colors="#4A81D4,#F7B84B,#1ABC9C" class="text-center" data-on_grid="{{ $on_grid }}" data-off_grid="{{ $off_grid }}" data-hybrid="{{ $hybrid }}"></div>
                    <br>
                </div>
                <div class="online-fault-vt">
                    <p><samp class="colorble1_one_vt"></samp> Low: <span> 89</span></p>
                    <p><samp class="colorble2_tow_vt"></samp> Medium: <span> 89</span></p>
                    <p><samp class="colorble3_three_vt"></samp> High: <span> 89</span></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card ticket_class_unique_vt">
                <div class="card-header">
                    <h2 class="All-graph-heading-vt">Tickets Status</h2>
                </div>
                <div class="day_month_year_vt">
                    <button><i class="fa fa-caret-left"></i></button>
                    <input type="text" id="humanfd-datepicker" class="form-control" placeholder="Select">
                    <button><i class="fa fa-caret-right"></i></button>
                </div>
                <div class="day_my_btn_vt">
                    <button class="day_bt_vt active">day</button>
                    <button class="months_bt_vt">month</button>
                    <button class="month_bt_vt">Year</button>
                </div>
                <div class="card-box" dir="ltr">
                    <div class="Yearly_text_vt">Yearly Tickets :63</div>
                    <br>
                    <div id="simple-pie" class="ct-chart ct-golden-section simple-pie-chart-chartist"></div>
                    <br>
                </div>
                <div class="online-fault-vt">
                    <p><samp class="colorble_one1_vt"></samp> Cancel: <span> 6</span></p>
                    <p><samp class="colorble_tow2_vt"></samp> Assigned: <span> 7</span></p>
                    <p><samp class="colorble_three3_vt"></samp> On Hold: <span> 9</span></p>
                    <p><samp class="colorble_four4_vt"></samp> In progress:<span> 10</span></p>
                    <p><samp class="colorble_four5_vt"></samp> Open:<span> 34</span></p>
                </div>
            </div>
        </div> -->
                <div class="col-lg-12 mb-3">
                    <div class="card card_body_padding_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Ticket List</h2>
                        </div>
                        <div class="card-body">
                            <div class="card_height_auto_vt tabel_area_user_vt">

                                <div class="card-body" style="margin-bottom: -2px;">

                                    <div class="table-responsive">
                                        <table id="datatable_7" class="display table table-borderless table-centered table-nowrap" style="width:100%">
                                            <thead>
                                            <tr>
                                                <th>ID #</th>
                                                <th>Site Name</th>
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
                                                    <td>{{$item->plant_name}}</td>
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
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="row">
                <div class="col-12">
                    <div class="card hum_tum_vt pla_body_padd_vt pb-0 mb-4">
                        <div class="card-body mb-0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-header">
                                        <h3 class="All-graph-heading-vt">Plants</h3>
                                        <div class="dataTables_length_vt bs-select" id="dtBasicExample_length"><label>Show <select name="dtBasicExample_length" aria-controls="dtBasicExample" class="custom-select custom-select-sm form-control form-control-sm">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="datatable_2" class="display table table-borderless table-centered table-nowrap" style="width:100%">
                                    <thead class="thead-light vt_head_td">
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Edit</th>
                                        <th>Status</th>
                                        <th>Name</th>
                                        <th>Capacity</th>
                                        <th>Plant Type</th>
                                        <th>Daily Expected Generation</th>
                                        <th>Current Generation</th>
                                        <th>Daily Generation</th>
                                        <th>Last Alarm</th>
                                        <th>Updated at</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($plants_dashboard as $key => $plant)
                                        <tr>
                                            <td class="one_setting_vt">
                                                <p>{{$plant_counter++}}</p>
                                            </td>
                                            <td class="one_setting_vt">
                                                <a href="{{route('admin.edit.plant', ['type' => 'bel','id'=>$plant->id])}}"><img src="{{ asset('assets/images/icon_setting.svg')}}" alt="setting"></a>
                                            </td>
                                            <td class="che_vt">
                                                @if($plant->is_online == 'Y')
                                                    <img src="{{ asset('assets/images/icon_plant_check_vt.svg')}}" alt="check" title="Online">
                                                @elseif($plant->is_online == 'P_Y')
                                                    <img src="{{ asset('assets/images/icon_plant_alert_vt.png')}}" alt="check" title="Partially Online">
                                                @else
                                                    <img src="{{ asset('assets/images/icon_plant_vt.svg')}}" alt="check" title="Offline">
                                                @endif
                                            </td>
                                            <td class="one_btn_vt">
                                                <a href="{{ url('admin/bel/user-plant-detail/'.$plant->id)}}" title="Plant Detail">{{ $plant->plant_name }}</a>
                                            </td>
                                            <td>
                                                {{ $plant->capacity.' kW' }}
                                            </td>
                                            <td>
                                                {{ $plant->plant_type }}
                                            </td>
                                            <td>
                                                {{ $plant->expected_generation.' kWh' }}
                                            </td>
                                            <td>
                                                {{ $plant->latest_processed_current_variables != null ? $plant->latest_processed_current_variables->current_generation.' kW': '---' }}
                                            </td>
                                            <td>
                                                {{ $plant->latest_daily_processed_plant_detail != null ? $plant->latest_daily_processed_plant_detail->dailyGeneration.' kWh': '---' }}
                                            </td>
                                            <td>
                                                {{ $plant->latest_fault_alarm_log != null ? date('h:i A d-m-Y', strtotime($plant->latest_fault_alarm_log->created_at)): '---' }}
                                            </td>
                                            <td>
                                                {{ $plant ? date('h:i A d-m-Y ', strtotime($plant->updated_at)) : date('h:i A d-m-Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->
            <!-- end row -->
        </div> <!-- container -->
    </div> <!-- content -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-gl/dist/echarts-gl.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-stat/dist/ecStat.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/extension/dataTool.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/china.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/world.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/extension/bmap.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

    <script type="text/javascript">
        window.onload = function() {

            var filterss_arr = {};
            var plants_array = <?php echo $filter_data['plants']; ?>;
            var plant_axis_grid = 4;
            var labels = {};
            var plant_name = {};
            var capacity_chunk = {!!$capacity_chunk!!};
            var month_name_arr = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            //companyPlantFilter(plants_array);

            $('.clickable').click(function() {
                window.location.href = $(this).attr('href');
            });

            $('.companyFilterMultiSelect').select2({

                "placeholder": "Select Companies"
            });

            $('.plantFilterMultiSelect').select2({

                "placeholder": "Select Plants"
            });

            $('#company').change(function() {

                companyPlantFilter(plants_array);
            });

            $('#clearFilters').on('click', function(e) {

                $('#plant_status').prop('selectedIndex', 0);
                $('#plant_type').prop('selectedIndex', 0);
                $('#system_type').prop('selectedIndex', 0);
                $('#company').prop('selectedIndex', 0);
                $('#province').prop('selectedIndex', 0);
                $('#city').prop('selectedIndex', 0);

                $(".companyFilterMultiSelect").val("");
                $(".companyFilterMultiSelect").trigger("change");

                $(".plantFilterMultiSelect").val("");
                $(".plantFilterMultiSelect").trigger("change");

                $('#filtersForm').trigger('submit');

            });

            var data1 = <?php echo $plants_donut_graph; ?>;
            if ($('#donut-chart').length > 0) {
                var eChart_1 = echarts.init(document.getElementById('donut-chart'));
                var option = {
                    series: [{
                        type: 'pie',
                        label: {
                            show: false,
                            position: 'center'
                        },
                        labelLine: {
                            show: false
                        },
                        radius: ['84%', '75%'],
                        color: ['#1F78B4', '#FF9768', '#E11818'],
                        data: data1
                    }]
                };
                eChart_1.setOption(option);
            }

            if ($('#bar-chart-plant').length > 0) {
                var eChart_5 = echarts.init(document.getElementById('bar-chart-plant'));
                option = {
                    tooltip: {
                        padding: 6,
                        borderWidth: 1,
                        borderColor: '#0F75BC',
                        textStyle: {
                            color: '#000'
                        },
                        backgroundColor: '#fff',
                        formatter: function(params) {
                            var data = params.data;
                            var name = params.name;
                            return 'Range: ' + name + '<br>Plants: ' + data;
                        }
                    },
                    xAxis: {
                        axisLabel: {
                            interval: 0,
                        },
                        data: [0 + "-" + String(capacity_chunk) + "kW", String(capacity_chunk + 1) + "-" + String(capacity_chunk * 2) + "kW", String((capacity_chunk * 2) + 1) + "-" + String(capacity_chunk * 3) + "kW", String((capacity_chunk * 3) + 1) + "-" + String(capacity_chunk * 4) + "kW", String((capacity_chunk * 4) + 1) + "-" + String(capacity_chunk * 5) + "kW"]
                    },
                    yAxis: {
                        type: 'value',
                        minInterval: 1,
                        splitLine: {
                            lineStyle: {
                                width: 2.35,
                                color: '#f4f4f4'
                            },
                        },
                    },
                    grid: {
                        height: '198px',
                    },
                    series: [{
                        data: [$("#bar-chart-plant").attr("data-capacity_1"), $("#bar-chart-plant").attr("data-capacity_2"), $("#bar-chart-plant").attr("data-capacity_3"), $("#bar-chart-plant").attr("data-capacity_4"), $("#bar-chart-plant").attr("data-capacity_5")],
                        type: 'bar',
                        color: '#0F75BC',
                        barMaxWidth: 8
                    }]
                };
                eChart_5.setOption(option);
            }

            if (($('#plant_name').val()).length != 0) {

                plant_name = $('#plant_name').val();
            }

            if ($('#plant_status').val() != 'all') {

                if ($('#plant_status').val() == 'fault') {

                    filterss_arr['alarmLevel'] = '1';
                } else {

                    filterss_arr['is_online'] = $('#plant_status').val();
                }

            }

            if ($('#plant_type').val() != 'all') {

                filterss_arr['plant_type'] = $('#plant_type').val();
            }

            if ($('#system_type').val() != 'all') {

                filterss_arr['system_type'] = $('#system_type').val();
            }

            if ($('#province').val() != 'all') {

                filterss_arr['province'] = $('#province').val();
            }

            if ($('#city').val() != 'all') {

                filterss_arr['city'] = $('#city').val();
            }

            var currDate = getCurrentDate();

            $('input[name="energy_genGraphDay"]').val(currDate.todayDate);
            $('input[name="energy_genGraphMonth"]').val(currDate.todayMonth);
            $('input[name="energy_genGraphYear"]').val(currDate.todayYear);
            $('input[name="envGraphDay"]').val(currDate.todayDate);
            $('input[name="envGraphMonth"]').val(currDate.todayMonth);
            $('input[name="envGraphYear"]').val(currDate.todayYear);
            $('input[name="savingGraphDay"]').val(currDate.todayDate);
            $('input[name="savingGraphMonth"]').val(currDate.todayMonth);
            $('input[name="savingGraphYear"]').val(currDate.todayYear);
            $('input[name="alertGraphDay"]').val(currDate.todayDate);
            $('input[name="alertGraphMonth"]').val(currDate.todayMonth);
            $('input[name="alertGraphYear"]').val(currDate.todayYear);
            $('input[name="expGenGraphDay"]').val(currDate.todayDate);
            $('input[name="expGenGraphMonth"]').val(currDate.todayMonth);
            $('input[name="expGenGraphYear"]').val(currDate.todayYear);

            var energy_gen_date = $('input[name="energy_genGraphDay"]').val();
            var energy_gen_time = 'day';
            var env_date = $('input[name="envGraphDay"]').val();
            var env_time = 'day';
            var saving_date = $('input[name="savingGraphDay"]').val();
            var saving_time = 'day';
            var expGen_date = $('input[name="expGenGraphDay"]').val();
            var expGen_time = 'day';
            var alert_date = $('input[name="alertGraphDay"]').val();
            var alert_time = 'day';

            $('#dashboardSpinner').hide();

            changeSavingDayMonthYear(saving_date, saving_time);
            // savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            changeEnergyGenerationDayMonthYear(energy_gen_date, energy_gen_time);
            // energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            changeENVDayMonthYear(env_date, env_time);
            // envGraphAjax(env_date, env_time, filterss_arr, plant_name);
            changeExpGenDayMonthYear(expGen_date, expGen_time);
            // expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            changeAlertDayMonthYear(alert_date, alert_time);
            // alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);

            $('.J-yearMonthDayPicker-single-energy_gen').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function(type) {
                    changeEnergyGenerationDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-energy_gen').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changeEnergyGenerationDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-energy_gen').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changeEnergyGenerationDayMonthYear(this.$input.eq(0).val(), 'year');
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

            $('.J-yearMonthDayPicker-single-saving').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function(type) {
                    changeSavingDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-saving').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changeSavingDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-saving').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changeSavingDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-expGen').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function(type) {
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-expGen').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-expGen').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'year');
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

            $('#energy_genGraphPreviousDay').on('click', function() {

                show_date = $("input[name='energy_genGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                energy_gen_date = formatDate(datess);
                $('input[name="energy_genGraphDay"]').val('');
                $('input[name="energy_genGraphDay"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphDay']").val());
                energy_gen_time = 'day';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphForwardDay').on('click', function() {

                show_date = $("input[name='energy_genGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                energy_gen_date = formatDate(datess);
                $('input[name="energy_genGraphDay"]').val('');
                $('input[name="energy_genGraphDay"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphDay']").val());
                energy_gen_time = 'day';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='energy_genGraphMonth']").val();
                energy_gen_date = formatPreviousMonth(show_date);
                $('input[name="energy_genGraphMonth"]').val('');
                $('input[name="energy_genGraphMonth"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphMonth']").val());
                energy_gen_time = 'month';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphForwardMonth').on('click', function() {

                show_date = $("input[name='energy_genGraphMonth']").val();
                energy_gen_date = formatForwardMonth(show_date);
                $('input[name="energy_genGraphMonth"]').val('');
                $('input[name="energy_genGraphMonth"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphMonth']").val());
                energy_gen_time = 'month';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphPreviousYear').on('click', function() {

                show_date = $("input[name='energy_genGraphYear']").val();
                energy_gen_date = formatPreviousYear(show_date);
                $('input[name="energy_genGraphYear"]').val('');
                $('input[name="energy_genGraphYear"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphYear']").val());
                energy_gen_time = 'year';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphForwardYear').on('click', function() {

                show_date = $("input[name='energy_genGraphYear']").val();
                energy_gen_date = formatForwardYear(show_date);
                $('input[name="energy_genGraphYear"]').val('');
                $('input[name="energy_genGraphYear"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphYear']").val());
                energy_gen_time = 'year';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
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

            $('#savingGraphPreviousDay').on('click', function() {

                show_date = $("input[name='savingGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                saving_date = formatDate(datess);
                $('input[name="savingGraphDay"]').val('');
                $('input[name="savingGraphDay"]').val(saving_date);
                console.log($("input[name='savingGraphDay']").val());
                saving_time = 'day';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphForwardDay').on('click', function() {

                show_date = $("input[name='savingGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                saving_date = formatDate(datess);
                $('input[name="savingGraphDay"]').val('');
                $('input[name="savingGraphDay"]').val(saving_date);
                console.log($("input[name='savingGraphDay']").val());
                saving_time = 'day';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='savingGraphMonth']").val();
                saving_date = formatPreviousMonth(show_date);
                $('input[name="savingGraphMonth"]').val('');
                $('input[name="savingGraphMonth"]').val(saving_date);
                console.log($("input[name='savingGraphMonth']").val());
                saving_time = 'month';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphForwardMonth').on('click', function() {

                show_date = $("input[name='savingGraphMonth']").val();
                saving_date = formatForwardMonth(show_date);
                $('input[name="savingGraphMonth"]').val('');
                $('input[name="savingGraphMonth"]').val(saving_date);
                console.log($("input[name='savingGraphMonth']").val());
                saving_time = 'month';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphPreviousYear').on('click', function() {

                show_date = $("input[name='savingGraphYear']").val();
                saving_date = formatPreviousYear(show_date);
                $('input[name="savingGraphYear"]').val('');
                $('input[name="savingGraphYear"]').val(saving_date);
                console.log($("input[name='savingGraphYear']").val());
                saving_time = 'year';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphForwardYear').on('click', function() {

                show_date = $("input[name='savingGraphYear']").val();
                saving_date = formatForwardYear(show_date);
                $('input[name="savingGraphYear"]').val('');
                $('input[name="savingGraphYear"]').val(saving_date);
                console.log($("input[name='savingGraphYear']").val());
                saving_time = 'year';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#expGenGraphPreviousDay').on('click', function() {

                show_date = $("input[name='expGenGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                expGen_date = formatDate(datess);
                $('input[name="expGenGraphDay"]').val('');
                $('input[name="expGenGraphDay"]').val(expGen_date);
                console.log($("input[name='expGenGraphDay']").val());
                expGen_time = 'day';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphForwardDay').on('click', function() {

                show_date = $("input[name='expGenGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                expGen_date = formatDate(datess);
                $('input[name="expGenGraphDay"]').val('');
                $('input[name="expGenGraphDay"]').val(expGen_date);
                console.log($("input[name='expGenGraphDay']").val());
                expGen_time = 'day';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='expGenGraphMonth']").val();
                expGen_date = formatPreviousMonth(show_date);
                $('input[name="expGenGraphMonth"]').val('');
                $('input[name="expGenGraphMonth"]').val(expGen_date);
                console.log($("input[name='expGenGraphMonth']").val());
                expGen_time = 'month';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphForwardMonth').on('click', function() {

                show_date = $("input[name='expGenGraphMonth']").val();
                expGen_date = formatForwardMonth(show_date);
                $('input[name="expGenGraphMonth"]').val('');
                $('input[name="expGenGraphMonth"]').val(expGen_date);
                console.log($("input[name='expGenGraphMonth']").val());
                expGen_time = 'month';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
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

            $("#energy_gen_day_my_btn_vt button").click(function() {

                $('#energy_gen_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeEnergyGenerationDayMonthYear(energy_gen_date, energy_gen_time);

            });

            $("#env_day_my_btn_vt button").click(function() {

                $('#env_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeENVDayMonthYear(env_date, env_time);

            });

            $("#saving_day_my_btn_vt button").click(function() {

                $('#saving_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeSavingDayMonthYear(saving_date, saving_time);

            });

            $("#expGen_day_my_btn_vt button").click(function() {

                $('#expGen_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeExpGenDayMonthYear(expGen_date, expGen_time);

            });

            $("#alert_day_my_btn_vt button").click(function() {

                $('#alert_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeAlertDayMonthYear(alert_date, alert_time);

            });

            function companyPlantFilter(plants_array) {

                $('#plant_name').empty();

                if (plants_array.length > 0) {

                    var com_id = $('#company').val();

                    for(var i = 0; i < com_id.length; i++) {

                        for (var j = 0; j < plants_array.length; j++) {

                            if (com_id[i] == plants_array[j].company_id) {

                                $('#plant_name').append('<option value=' + plants_array[j].id + '>' + plants_array[j].plant_name + '</option>')
                            }
                        }
                    }
                }
            }

            function changeEnergyGenerationDayMonthYear(date, time) {

                var d_m_y = '';

                $('#energy_gen_day_my_btn_vt').children('button').each(function() {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                if (d_m_y == 'day') {
                    $('#energy_gen_day_month_year_vt_year').hide();
                    $('#energy_gen_day_month_year_vt_month').hide();
                    $('#energy_gen_day_month_year_vt_day').show();
                    date = $('input[name="energy_genGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    $('#energy_gen_day_month_year_vt_year').hide();
                    $('#energy_gen_day_month_year_vt_day').hide();
                    $('#energy_gen_day_month_year_vt_month').show();
                    date = $('input[name="energy_genGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#energy_gen_day_month_year_vt_day').hide();
                    $('#energy_gen_day_month_year_vt_month').hide();
                    $('#energy_gen_day_month_year_vt_year').show();
                    date = $('input[name="energy_genGraphYear"]').val();
                    time = 'year';
                }

                energy_genGraphAjax(date, time, filterss_arr, plant_name);
            }

            function changeENVDayMonthYear(date, time) {

                var d_m_y = '';

                $('#env_day_my_btn_vt').children('button').each(function() {
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

                envGraphAjax(date, time, filterss_arr, plant_name);
            }

            function changeSavingDayMonthYear(date, time) {

                var d_m_y = '';

                $('#saving_day_my_btn_vt').children('button').each(function() {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                if (d_m_y == 'day') {
                    $('#saving_day_month_year_vt_year').hide();
                    $('#saving_day_month_year_vt_month').hide();
                    $('#saving_day_month_year_vt_day').show();
                    date = $('input[name="savingGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    $('#saving_day_month_year_vt_year').hide();
                    $('#saving_day_month_year_vt_day').hide();
                    $('#saving_day_month_year_vt_month').show();
                    date = $('input[name="savingGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#saving_day_month_year_vt_day').hide();
                    $('#saving_day_month_year_vt_month').hide();
                    $('#saving_day_month_year_vt_year').show();
                    date = $('input[name="savingGraphYear"]').val();
                    time = 'year';
                }

                savingGraphAjax(date, time, filterss_arr, plant_name);
            }

            function changeExpGenDayMonthYear(date, time) {

                var d_m_y = '';

                $('#expGen_day_my_btn_vt').children('button').each(function() {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                if (d_m_y == 'day') {
                    $('#expGen_day_month_year_vt_year').hide();
                    $('#expGen_day_month_year_vt_month').hide();
                    $('#expGen_day_month_year_vt_day').show();
                    date = $('input[name="expGenGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    $('#expGen_day_month_year_vt_year').hide();
                    $('#expGen_day_month_year_vt_day').hide();
                    $('#expGen_day_month_year_vt_month').show();
                    date = $('input[name="expGenGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#expGen_day_month_year_vt_day').hide();
                    $('#expGen_day_month_year_vt_month').hide();
                    $('#expGen_day_month_year_vt_year').show();
                    date = $('input[name="expGenGraphYear"]').val();
                    time = 'year';
                }

                expGenGraphAjax(date, time, filterss_arr, plant_name);
            }

            function changeAlertDayMonthYear(date, time) {

                var d_m_y = '';

                $('#alert_day_my_btn_vt').children('button').each(function() {
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

                alertGraphAjax(date, time, filterss_arr, plant_name);
            }

            function alertGraphAjax(date, time, filter, plant_name) {

                filters = JSON.stringify(filter);
                plantName = JSON.stringify(plant_name);

                $('#alertChartDiv div').remove();
                $('#alertChartDetailDiv').empty();
                $('#alertSpinner').show();

                $.ajax({
                    url: "{{ route('admin.dashboard.alert.graph') }}",
                    method: "GET",
                    data: {
                        'date': date,
                        'time': time,
                        'filter': filters,
                        'plant_name': plantName,
                        'from_url': 'dashboard',
                        'type': 'bel'
                    },

                    dataType: 'json',
                    success: function(data) {

                        if (data.total_fault != 0 || data.total_alarm != 0 || data.total_rtu != 0) {

                            $('.noRecord').hide();
                            $('#alertChartDiv div').remove();
                            $('#alertChartDetailDiv').empty();

                            $('#alertChartDiv').append('<div id="alertChart" style="height: 275px; width: 100%;"></div>');
                            $('#alertChartDetailDiv').append('<p><samp class="color03_one_vt"></samp> Fault: <span> ' + data.total_fault + '</span></p><p><samp class="color04_one_vt"></samp> Alarm: <span> ' + data.total_alarm + '</span></p><p><samp class="color05_one_vt"></samp> RTU: <span> ' + data.total_rtu + '</span></p>');
                            $('#alertSpinner').hide();

                            plantAlertGraph(time, data.today_time, data.plant_alert_graph, data.today_date);
                        } else {

                            $('.noRecord').show();
                            $('#alertSpinner').hide();
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }

            function energy_genGraphAjax(date, time, filter, plant_name) {

                console.log(plant_name);
                filters = JSON.stringify(filter);
                plantName = JSON.stringify(plant_name);

                $('#chartContainerDiv div').remove();
                $('#chartDetailDiv').empty();
                $('#energyGenSpinner').show();

                $.ajax({
                    url: "{{ route('admin.dashboard.energy.graph') }}",
                    method: "GET",
                    data: {
                        'date': date,
                        'time': time,
                        'filter': filters,
                        'plant_name': plantName,
                        'type': 'bel'
                    },

                    dataType: 'json',
                    success: function(data) {
                        console.log(data);

                        $('#chartContainerDiv div').remove();
                        $('#chartDetailDiv').empty();

                        $('#chartContainerDiv').append('<div class="kWh_eng_vt">kWh</div>');
                        $('#chartContainerDiv').append('<div id="energyContainer" style="height: 230px; width: 100%;"></div>');
                        if (time == 'day') {

                            $('#chartDetailDiv').append('<p><samp></samp>Today :<span> ' + data.total_today + '</span></p><p><samp class="samp_vt"></samp>Yesterday : <span class="one">' + data.total_yesterday + '</span></p><p><samp class="samp_vt"></samp>Total : <span class="one">' + data.plant_total_generation + '</span></p>');
                        } else {

                            $('#chartDetailDiv').append('<p><samp></samp>This ' + time[0].toUpperCase() + time.slice(1) + ' Generated Energy(Online) <br><span> ' + data.total_today + '</span></p><p><samp class="samp_vt"></samp>Last ' + time[0].toUpperCase() + time.slice(1) + ' Generated Energy(Online) <br> <span class="one">' + data.total_yesterday + '</span></p><p><samp class="samp_vt"></samp>Total Generated Energy(Online) <br> <span class="one">' + data.plant_total_generation + '</span></p>');
                        }
                        $('#energyGenSpinner').hide();

                        dashboardEnergyGraph(time, data.today_time, data.plant_energy_graph, data.today_date, data.yesterday_date);
                    },
                    error: function(data) {
                        console.log(data);
                        //alert('Some Error Occured!');
                    }
                });
            }

            function savingGraphAjax(date, time, filter, plant_name) {

                filters = JSON.stringify(filter);
                plantName = JSON.stringify(plant_name);

                $('#savingChartDiv div').remove();
                $('#savingChartDetailDiv').empty();
                $('#savingSpinner').show();

                $.ajax({
                    url: "{{ route('admin.dashboard.saving.graph') }}",
                    method: "GET",
                    data: {
                        'date': date,
                        'time': time,
                        'filter': filters,
                        'plant_name': plantName
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);

                        $('#savingChartDiv div').remove();
                        $('#savingChartDetailDiv').empty();

                        $('#savingChartDiv').append('<div class="kWh_eng_vt_sav">PKR</div>');
                        $('#savingChartDiv').append('<div id="chartSaving" style="height: 265px; width: 100%;"></div>');

                        var timely = '';

                        if (time == 'day') {

                            timely = 'Daily';
                        } else if (time == 'month') {

                            timely = 'Monthly';
                        } else if (time == 'year') {

                            timely = 'Yearly';
                        }


                        $('#savingChartDetailDiv').append('<p><samp class="color06_one_vt"></samp>' + timely + ' Cost Saving:<span> PKR ' + costFormatter(data.total_today) + ' </span></p><p><samp class="color06_one_vt"></samp>Total Cost Saving:<span> PKR ' + data.plant_total_saving + ' </span></p>');
                        $('#savingSpinner').hide();

                        plantGraphSaving(time, data.today_time, data.plant_saving_graph, data.today_date);
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
                        'time': time,
                        'filter': filters,
                        'plant_name': plantName,
                        'type': 'bel'
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);

                        $('#envPlantingDiv h2').remove();
                        $('#envReductionDiv h3').remove();
                        $('#envGenerationDiv').empty();

                        $('#envSpinner').hide();
                        $('#envPlantingDiv').append("<h2>" + (data[1] * 0.00131).toFixed(2) + "<samp>tree(s)</samp></h2>");
                        $('#envReductionDiv').append("<h3>" + (data[1] * 0.000646155).toFixed(2) + "<samp>T</samp></h3>");
                        $('#envGenerationDiv').append('<p><samp class="color07_one_vt"></samp> Total Generation: <span>' + data[0] + '</span></p>');
                    },
                    error: function(data) {
                        console.log(data);
                        //alert('Some Error Occured!');
                    }
                });
            }

            function expGenGraphAjax(date, time, filter, plant_name) {

                filters = JSON.stringify(filter);
                plantName = JSON.stringify(plant_name);

                $('#chartContainerGenDiv div').remove();
                $('#chartDetailGenDiv').empty();
                $('#expGenSpinner').show();

                $.ajax({
                    url: "{{ route('admin.main.dashboard.expected.generation.graph') }}",
                    method: "GET",
                    data: {
                        'date': date,
                        'time': time,
                        'filter': filters,
                        'plant_name': plantName,
                        'type':'bel'
                    },

                    dataType: 'json',
                    success: function(data) {
                        console.log(data);

                        $('#chartContainerGenDiv div').remove();
                        $('#chartDetailGenDiv').empty();

                        $('#chartContainerGenDiv').append('<div class="kWh_eng_vt_gen">kWh</div>');
                        $('#chartContainerGenDiv').append('<div id="chartContainerGen" style="height: 245px; width: 100%;"></div>');
                        $('#chartDetailGenDiv').append('<p><samp class="color01_one_vt"></samp> Actual: <span> ' + data.total_today + '</span></p><p><samp class="color02_one_vt"></samp> Expected: <span> ' + data.total_yesterday + '</span></p><p><samp class="color02_one_vt"></samp> Total: <span> ' + data.plant_total_generation + '</span></p>');
                        $('#expGenSpinner').hide();

                        plantGenGraph(time, data.today_time, data.plant_energy_graph, data.today_date, data.yesterday_date);
                    },
                    error: function(data) {
                        console.log(data);
                        //alert('Some Error Occured!');
                    }
                });
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
                        formatter: function(params) {

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

            function plantGenGraph(time_type, time, data, today_date, yesterday_date) {

                var dom = document.getElementById("chartContainerGen");
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
                        formatter: function(params) {

                            if (time_type == 'day') {

                                if (params[0].data == null) {

                                    return yesterday_date + ' ' + params[1].name + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>';
                                } else {

                                    return today_date + ' ' + params[0].name + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>' + params[0].seriesName + ': ' + energyFormatter(params[0].data) + 'Wh';
                                }

                            } else if (time_type == 'month') {

                                return params.name + '-' + today_date + '<br>' + params.seriesName + ': ' + energyFormatter(params.data) + 'Wh<br>';
                            } else {

                                return getMonthName(params.name) + ' ' + today_date + '<br>' + params.seriesName + ': ' + energyFormatter(params.data) + 'Wh<br>';
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
                            interval: (time_type == 'day') ? parseInt((time.length) / 10) : 0
                        },
                        axisLabel: {
                            interval: (time_type == 'day') ? parseInt((time.length) / 10) : 0
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

            function plantGraphSaving(time_type, time, data, today_date) {

                var dom = document.getElementById("chartSaving");
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
                        formatter: function(params) {

                            if (time_type == 'day') {
                                return today_date + ' ' + params[0].name + '<br>PKR ' + costFormatter(params[0].data);
                            } else if (time_type == 'month') {

                                return params.name + '-' + today_date + '<br>PKR ' + costFormatter(params.data);
                            } else {

                                return getMonthName(params.name) + ' ' + today_date + '<br>PKR ' + costFormatter(params.data);
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

            function dashboardEnergyGraph(time_type, time, data, today_date, yesterday_date) {

                var dom = document.getElementById("energyContainer");
                var myChart = echarts.init(dom);
                var app = {};

                var option;

                option = {
                    tooltip: {
                        trigger: 'axis',
                        textStyle: {
                            fontFamily: 'roboto',
                            fontStyle: 'bold',
                            fontSize: 12,
                            color: '#504E4E'
                        },
                        formatter: function(params) {

                            if (time_type == 'day') {

                                if (params[0].data == null) {

                                    return yesterday_date + ' ' + params[1].name + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>';
                                } else {

                                    return today_date + ' ' + params[0].name + '<br>' + params[0].seriesName + ': ' + energyFormatter(params[0].data) + 'Wh<br>' + yesterday_date + ' ' + params[1].name + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>';
                                }

                            } else if (time_type == 'month') {

                                if (params[0].data == null) {

                                    return params[1].name + '-' + yesterday_date + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>';
                                } else {

                                    return params[0].name + '-' + today_date + '<br>' + params[0].seriesName + ': ' + energyFormatter(params[0].data) + 'Wh<br>' + params[1].name + '-' + yesterday_date + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>';
                                }

                            } else {

                                if (params[0].data == null) {

                                    return yesterday_date + ' ' + params[1].name + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>';
                                } else {

                                    return getMonthName(params[0].name) + ' ' + today_date + '<br>' + params[0].seriesName + ': ' + energyFormatter(params[0].data) + 'Wh<br>' + getMonthName(params[1].name) + ' ' + yesterday_date + '<br>' + params[1].seriesName + ': ' + energyFormatter(params[1].data) + 'Wh<br>';
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
                        boundaryGap: false,
                        data: time,
                        axisTick: {
                            interval: (time_type == 'day') ? parseInt((time.length) / 7) : (time_type == 'month') ? 1 : 0
                        },
                        axisLabel: {
                            interval: (time_type == 'day') ? parseInt((time.length) / 7) : (time_type == 'month') ? 1 : 0
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

            function format_output(num) {
                return parseInt(Math.log(num) / Math.log(10));
            }

            function getMonthName(name) {

                for (var i = 0; i < month_name_arr.length; i++) {

                    if (name == month_name_arr[i].slice(0, 3)) {

                        return month_name_arr[i];
                    }

                }
            }

            function costFormatter(num) {

                if (Math.abs(num) > 999 && Math.abs(num) <= 999999) {
                    return Math.sign(num) * ((Math.abs(num) / 1000).toFixed(2)) + 'K';
                } else if (Math.abs(num) > 999999 && Math.abs(num) <= 9999999999) {
                    return Math.sign(num) * ((Math.abs(num) / 1000000).toFixed(2)) + 'M';
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

            function donut_chart_css() {

                $('.donut_chart_card_box').css({
                    'display': 'flex',
                    'justify-content': 'center',
                    'align-items': 'center'
                })
            }

            setTimeout(donut_chart_css, 2000);
        }
    </script>
    <script>
        var map;

        function initMap() {
            map = new google.maps.Map(
                document.getElementById('map'), {
                    center: new google.maps.LatLng(30.3753, 69.3451),
                    zoom: 5
                });

            var iconBase =
                'https://developers.google.com/maps/documentation/javascript/examples/full/images/';

            var icons = {
                parking: {
                    icon: iconBase + 'marker.png'
                },
                library: {
                    icon: iconBase + 'library_maps.png'
                },
                info: {
                    icon: iconBase + 'info-i_maps.png'
                }
            };

            const infowindow = new google.maps.InfoWindow({
                content: "Location",
            });

            var features = [
                    <?php foreach ($plants_dashboard as $key => $plant) { ?> {
                    position: new google.maps.LatLng(<?php echo $plant->loc_lat; ?>, <?php echo $plant->loc_long; ?>),

                    <?php
                        $yearly_Generation = $plant->yearly_processed_plant_detail ? $plant->yearly_processed_plant_detail->where('plant_id', $plant->id)->orderBy('created_at', 'DESC')->first() : 0;
                        ?>

                    name: "<?php echo $plant->plant_name; ?>",
                    annual: "<?php echo $plant->yearly_processed_detail; ?>",
                    expected: "<?php echo $plant->yearly_expected_generation; ?>",
                },
                <?php } ?>
            ];

            var contentString = [];

            // Create markers.
            for (var i = 0; i < features.length; i++) {
                var marker = new google.maps.Marker({
                    position: features[i].position,
                    map: map,
                    icon: document.getElementById('mapMarkerIcon').src
                });

                contentString[i] = "<div>Plant Name: " + features[i].name + "</div>" +
                    "<br>" +
                    "<div>Annual Generation: " + features[i].annual + "</div>" +
                    "<br>" +
                    "<div>Expected Generation: " + features[i].expected + "</div>" +
                    "<br>";

                google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
                    return function() {
                        infowindow.setContent(contentString[i]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            };
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB12hWno8_DIMqw7xCV1QeqYn6I8FiIxVw&callback=initMap">
    </script>
@endsection
