@extends('layouts.admin.master')
@section('title', 'Dashboard')
@section('content')
    <style>
        .solar_energy_tilizatio_vt {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -74px;
            z-index: 9;
            margin-bottom: 10px;
        }
        #map {
            min-height: 345px !important;
        }

        .solar_energy_tilizatio_vt ul {
            margin: 0;
            padding: 0;
        }

        .solar_energy_tilizatio_vt ul li {
            list-style: none;
            float: left;
            padding: 0 10px;
        }

        .card_newvt {
            position: relative;
        }
        .gr_vt .overtotal_vt {
            top: 109px;
        }

        .card_newvt .h6 {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -186px;
            z-index: 9;
            margin-bottom: 10px;
            font-size: 14px;
            color: #6E6E6E;
            padding: 0 10px;
        }

        .one_specific_vt {
            text-align: center;
            font-size: 14px;
            color: #6E6E6E;
            margin-top: -11px;
            z-index: 99;
            display: flex;
            justify-content: center;

        }

        .one_specific_vt .one-import-color {
            background-color: #fac858;
            min-width: 10px;
            max-width: 10px;
            height: 10px;
            border-radius: 2px;
            float: left;
            margin-top: 2px;
            margin-right: 8px;
        }

        .card {
            min-height: 460px;
        }

        .hi_vt_m_vt {
            min-height: 553px;
        }

        .kwh_khp_vt {
            color: #000;
            text-align: center;
            font-weight: bold;
        }

        .tabgrtow_vt {
            width: 100%;
            position: relative;
            margin-top: 63px;
            height: 298px;
            border: none;
        } .home-companise_dash-vt .select2-container--default .select2-search--inline .select2-search__field {
              min-width: 105px;
          }

        .card-box_vt {
            border-radius: .25rem;
            position: relative;
            background-color: #fff;
            padding: 1.5rem 1.5rem 0rem 1.5rem;
            box-shadow: none !important;
        }

        .card.energygeneration_vt{
            min-height: 460px;
        }
        .total_peak_hours_vt {
            width: 147px;
            text-align: center;
            margin-top: -50px;
            z-index: 999;
            position: absolute;
            left: 50%;
            transform: translateX(-58%);
            top: 55%;
        }
        .plantsLocations_vt{
            min-height: 460px;
        }
        .mapCard .card {
            min-height: 461px !important;
        }
        div#datatable_hybrid2_filter {
            display: none;
        }
        .btn_left_max_power_vt{
            left: 38%;
        }
        .btn_right_max_power_vt{
            right: 38%;
        }
        .btn_left_energy_source_vt{
            left: 38%;
        }
        .btn_right_energy_source_vt{
            right: 38%;
        }
        .btn_left_outage_served_vt{
            left: 38%;
        }
        .btn_right_outage_served_vt{
            right: 38%;
        }
    </style>
    <img id="mapMarkerIcon" src="{{ asset('assets/images/map_marker.svg')}}" alt="setting" style="display: none;">
    <div class="content">
        <div class="col-lg-12 inverter_battery_vt mt-3">
            {{--        php--}}
            <?php
            $plant_ids = \App\Http\Models\PlantUser::where('user_id', Request::user()->id)->pluck('plant_id');
            $userPlants = \App\Http\Models\Plant::whereIn('id', $plant_ids)->pluck('system_type');
            $arrayData = count(array_unique(json_decode(json_encode($userPlants),true)));
            ?>
            @if($arrayData != 1)
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    @if(Auth::user()->roles == 1)
                        <li class="nav-item">
                            {{--                <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : ''}}" id="Grid-tab" data-toggle="tab" href="{{ route('admin.dashboard') }}" role="tab" aria-controls="Grid" aria-selected="false">On Grid</a>--}}
                            <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : ''}}" id="Grid-tab" href="{{ route('admin.dashboard') }}">On Grid</a>
                        </li>
                    @endif
                    @if(Auth::user()->roles == 5 || Auth::user()->roles == 6)
                        <li class="nav-item">
                            {{--                <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : ''}}" id="Grid-tab" data-toggle="tab" href="{{ route('admin.dashboard') }}" role="tab" aria-controls="Grid" aria-selected="false">On Grid</a>--}}
                            <a class="nav-link {{ Request::is('admin/user-dashboard') ? 'active' : ''}}" id="Grid-tab" href="{{ route('user.dashboard') }}">On Grid</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        {{--                <a class="nav-link {{ Request::is('admin/Plants') ? 'active' : ''}}" id="Hybrid-tab" data-toggle="tab" href="{{ route('admin.plants') }}" role="tab" aria-controls="Hybrid" aria-selected="true">Hybrid</a>--}}
                        <a class="nav-link {{ Request::is('admin/Plants') ? 'active' : ''}}" id="Hybrid-tab" href="{{ route('admin.plants') }}">Hybrid</a>
                    </li>
                </ul>
            @endif
            <!-- Tab panes -->
            {{--        <div class="tab-content pt-0">--}}
            {{--            <div class="tab-pane active" id="Hybrid" role="tabpanel" aria-labelledby="Hybrid-tab">--}}
            {{--                123--}}
            {{--            </div>--}}


            {{--            <div class="tab-pane" id="Grid" role="tabpanel" aria-labelledby="Grid-tab">--}}
            {{--                123456789--}}
            {{--            </div>--}}
            {{--        </div>--}}
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
                        <form id="filtersForm" class="home-companise_dash-vt" action="{{route('admin.plants')}}"
                              method="GET">
                            <?php
                            $filter = Session::get('filter');
                            $capacity_chunk = Session::get('capacity_chunk');
                            $plant_counter = 1;
                            ?>
                            @if(Auth::user()->roles == 1 || Auth::user()->roles == 2)
                                <div class="form-group">
                                    <select class="form-control companyFilterMultiSelect" name="company[]" id="company"
                                            multiple>
                                        @if(isset($filter_data['company_array']) && $filter_data['company_array'])
                                            @foreach($filter_data['company_array'] as $company_data)
                                                <option
                                                    value="{{ $company_data->id }}" <?php echo isset($filter['company']) && in_array($company_data->id, $filter['company']) ? 'selected' : '' ?>>{{ $company_data->company_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif
                            <div class="form-group" style="min-width: 90px;">
                                <select class="form-control plantFilterMultiSelect" name="plant_name[]" id="plant_name"
                                        multiple>
                                    @if(isset($filter_data['plants']) && $filter_data['plants'])
                                        @foreach($filter_data['plants'] as $key => $plant)
                                            <option
                                                value="{{ $plant->id }}" <?php echo isset($filter['plant_name']) && in_array($plant->id, $filter['plant_name']) ? 'selected' : '' ?>>{{ $plant->plant_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="plant_status" id="plant_status">
                                    <option
                                        value="all" {{ app('request')->input('plant_status') == 'all' ? 'selected' : ''}}>
                                        Plant Status
                                    </option>
                                    <option
                                        value="Y" {{app('request')->input('plant_status') == 'Y' ? 'selected' : ''}}>
                                        Online
                                    </option>
                                    <option
                                        value="N" {{app('request')->input('plant_status') == 'N' ? 'selected' : ''}}>
                                        Offline
                                    </option>
                                    <option
                                        value="fault" {{app('request')->input('plant_status') == 'fault' ? 'selected' : ''}}>
                                        Fault
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="plant_type" id="plant_type">
                                    <option value="all">Plant Type</option>
                                    @if(isset($filter_data['plant_type']) && $filter_data['plant_type'])
                                        @foreach($filter_data['plant_type'] as $plant_type)
                                            <option
                                                value="{{ $plant_type->id }}" <?php echo isset($filter['plant_type']) && $filter['plant_type'] == $plant_type->id ? 'selected' : '' ?>>{{ $plant_type->type }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="system_type" id="system_type">
                                    <option value="all">System Type</option>
                                    @if(isset($filter_data['system_type']) && $filter_data['system_type'])
                                        @foreach($filter_data['system_type'] as $system_type)
                                            <option
                                                value="{{ $system_type->id }}" <?php echo isset($filter['system_type']) && $filter['system_type'] == $system_type->id ? 'selected' : '' ?>>{{ $system_type->type }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="province" id="province">
                                    <option value="all">Province</option>
                                    @if(isset($filter_data['province_array']) && $filter_data['province_array'])
                                        @foreach($filter_data['province_array'] as $province_data)
                                            <option
                                                value="{{ $province_data->province }}" <?php echo isset($filter['province']) && $filter['province'] == $province_data->province ? 'selected' : '' ?>>{{ $province_data->province }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="city" id="city">
                                    <option value="all">City</option>
                                    @if(isset($filter_data['city_array']) && $filter_data['city_array'])
                                        @foreach($filter_data['city_array'] as $city_data)
                                            <option
                                                value="{{ $city_data->city }}" <?php echo isset($filter['city']) && $filter['city'] == $city_data->city ? 'selected' : '' ?>>{{ $city_data->city }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <!-- <div class="form-group">
                            <select class="form-control select2-multiple" name="plants[]" id="plants" data-toggle="select2" multiple="" data-placeholder="Choose Plants">
                                @if(isset($filter_data['plants']) && $filter_data['plants'])
                                @foreach($filter_data['plants'] as $plant)
                                    <option value="{{ $plant->id }}" <?php echo isset($filter['plants']) && in_array($plant->id, $filter['plants']) ? 'selected' : '' ?>>{{ $plant->plant_name }}</option>
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
        <div class="container-fluid">
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
                            <a href="#" class="close" data-dismiss="alert"
                               aria-label="close">&times;</a> {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">

                <!-- Operating Status -->
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

                <!-- Energy Generation -->
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Energy Generation</h2>
                        </div>
                        <div class="day_month_year_vt" id="energy_gen_day_month_year_vt_day">
                            <button><i id="energy_genGraphPreviousDay" class="fa fa-caret-left"></i></button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-energy_gen mt10">
                                <input type="text" autocomplete="off" name="energy_genGraphDay" id="energy_genGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="energy_genGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="energy_gen_day_month_year_vt_month">
                            <button><i id="energy_genGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-energy_gen mt10">
                                    <input type="text" autocomplete="off" name="energy_genGraphMonth"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="energy_genGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="energy_gen_day_month_year_vt_year">
                            <button><i id="energy_genGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-energy_gen mt10">
                                    <input type="text" autocomplete="off" name="energy_genGraphYear"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
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

                <!-- Maximum power Achieved -->
                <div class="col-lg-6 mb-3">
                    <div class="card card_newvt hi_vt_m_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Maximum power Achieved</h2>
                        </div>
                        <div class="day_month_year_vt" id="max_power_day_month_year_vt_day">
                            <button class="btn_left_vt btn_left_max_power_vt"><i id="maxPowerGraphPreviousDay" class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-max-power mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="maxPowerGraphDay" id="maxPowerGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button class="btn_right_vt btn_right_max_power_vt"><i id="maxPowerGraphForwardDay" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="max_power_day_month_year_vt_month" style="display: none;">
                            <button class="btn_left_vt btn_left_max_power_vt"><i id="maxPowerGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-max-power mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="maxPowerGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_max_power_vt"><i id="maxPowerGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="max_power_day_month_year_vt_year" style="display: none;">
                            <button class="btn_left_vt btn_left_max_power_vt"><i id="maxPowerGraphPreviousYear" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-max-power mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="maxPowerGraphYear" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt btn_right_max_power_vt"><i id="maxPowerGraphForwardYear" class="fa fa-caret-right"></i>
                            </button>
                        </div>

                        <div class="day_my_btn_vt" id="max_power_day_my_btn_vt12">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        {{--                        <div class="day_my_btn_vt" id="maximum-power-achieved-graph">--}}
                        {{--                            <button class="day_bt_vt active" id="day">day</button>--}}
                        {{--                            <button class="month_bt_vt" id="month">month</button>--}}
                        {{--                            <button class="month_bt_vt" id="year">Year</button>--}}
                        {{--                        </div>--}}
                        <div class="card-box" dir="ltr">
                            <div id="maximum-power-achieved" class="text-center"></div>
                            <br>
                        </div>
                        <h6 class="h6" id="max-power">Maximum Power Achieved: 0 kW</h6>
                    </div>
                </div>

                <!-- Specific yield Kwh/Kwp -->
                <div class="col-lg-6 mb-3">
                    <div class="card card_newvt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Specific Yield Kwh/Kwp</h2>
                        </div>
                        <div class="day_month_year_vt" id="specific_yield_day_month_year_vt_month">
                            <button><i id="specific-yieldGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-specific-yield mt10">
                                    <input type="text" autocomplete="off" name="specific-yieldGraphMonth"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="specific-yieldGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="specific_yield_day_month_year_vt_year">
                            <button><i id="specific-yieldGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-specific-yield mt10">
                                    <input type="text" autocomplete="off" name="specific-yieldGraphYear"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="specific_yieldForwardYear" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_my_btn_vt" id="specific-yield-data">
                            <button class="month_bt_vt active" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        <div class="card-box_vt" dir="ltr">
                            <div class="kwh_khp_vt">Kwh/Kwp</div>
                            <br>
                            <div class="spinner-border text-success specificYieldGraphSpinner plantGraphSpinner"
                                 role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div id="specific-yield-bar-graph"></div>
                            <br>
                        </div>
                        <h5 class="one_specific_vt"><samp class="one-import-color"></samp> <span
                                id="specific-yield-value">Specific Yield: 0 Kwh/Kwp </span></h5>
                    </div>
                </div>

                <!-- Actual Generation / Expected Generation -->
                <div class="col-lg-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Actual Generation / Expected Generation</h2>
                        </div>
                        <div class="day_month_year_vt" id="expGen_day_month_year_vt_day">
                            <button><i id="expGenGraphPreviousDay" class="fa fa-caret-left"></i></button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-expGen mt10">
                                <input type="text" autocomplete="off" name="expGenGraphDay" id="expGenGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="expGenGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="expGen_day_month_year_vt_month">
                            <button><i id="expGenGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-expGen mt10">
                                    <input type="text" autocomplete="off" name="expGenGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="expGenGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="expGen_day_month_year_vt_year">
                            <button><i id="expGenGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-expGen mt10">
                                    <input type="text" autocomplete="off" name="expGenGraphYear" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
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

                <!-- Solar Energy Utilization -->
                <div class="col-lg-6 mb-3">
                    <div class="card card_newvt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Solar Energy Utilization</h2>
                        </div>
                        <div class="day_month_year_vt" id="solar_energy_utilization_day_month_year_vt_day">
                            <button><i id="solarEnergyUtilizationGraphPreviousDay" class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-solar-energy-utilization-day mt10">
                                <input type="text" autocomplete="off" name="solarEnergyUtilizationGraphDay"
                                       id="expGenGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="solarEnergyUtilizationGraphForwardDay" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="solar_energy_utilization_month_year_vt_month">
                            <button><i id="solarEnergyUtilizationGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-solar-energy-utilization-month mt10">
                                    <input type="text" autocomplete="off" name="solarEnergyUtilizationGraphMonth"
                                           placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="solarEnergyUtilizationGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="solar_energy_utilization_month_year_vt_year">
                            <button><i id="solarEnergyUtilizationGraphPreviousYear" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-solar-energy-utilization-year mt10">
                                    <input type="text" autocomplete="off" name="solarEnergyUtilizationGraphYear"
                                           placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="solarEnergyUtilizationGraphForwardYear" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_my_btn_vt" id="solar-energy-utilization-data">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                        <div class="card-box" dir="ltr">
                            <div id="solar-energy-utilization-graph-detail"></div>
                            <br>
                        </div>
                        <div class="solar_energy_tilizatio_vt">
                            <ul>
                                <li><samp class="battery-color"></samp> Battery Charge : <strong
                                        class="batteryChargeValue">0 kWh</strong></li>
                                <li><samp class="grid-import-color"></samp> Grid Export : <strong
                                        class="gridExportValue">0 kWh</strong></li>
                                <li><samp class="load-color"></samp> Load : <strong
                                        class="loadValue">0 kWh</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Energy Sources -->
                <div class="col-lg-6 mb-3">
                    <div class=" card card_newvt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Energy Sources</h2>
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
{{--                        <div class="day_my_btn_vt" id="energy_source_day_my_btn_vt">--}}
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
                            <div id="energy-source-graph-container"></div>
                            <div class="over_total_vt">total Energy<br>Consumption <span
                                    id="energySourcesGenerationValue"></span></div>
                        </div>
                        {{--                        <div class="card-box" dir="ltr">--}}
                        {{--                            <div class="kwh_khp_vt">Kwh/Kwp</div>--}}
                        {{--                            <br>--}}
                        {{--                            <div id="simple-pie" class="ct-chart ct-golden-section simple-pie-chart-chartist"></div>--}}
                        {{--                            <br>--}}
                        {{--                        </div>--}}
                    </div>
                </div>

                <!-- Maximum power Achieved -->
                <div class="col-lg-6 mb-3">
                    <div class="card  card_newvt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Outages Served</h2>
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
{{--                        <div class="day_my_btn_vt" id="outages-served-dashboard">--}}
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

                <!-- Consumption in peak hours (Battery/Grid) -->
                <div class="col-lg-6 mb-3">
                    <div class="card card_newvt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Consumption in peak hours (Battery/Grid)</h2>
                        </div>
                        <div class="day_month_year_vt" id="consumption_peak_day_month_year_vt_day">
                            <button><i class="fa fa-caret-left" id="consumption_peakGraphPreviousDay"></i></button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-consumption-peak mt10">
                                <input type="text" autocomplete="off" name="consumption_peakGraphDay"
                                       placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="consumption_peakGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="consumption_peak_day_month_year_vt_month">
                            <button><i id="consumption_peakGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-consumption-peak mt10">
                                    <input type="text" autocomplete="off" name="consumption_peakGraphMonth"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="consumption_peakGraphForwardMonth" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="consumption_peak_day_month_year_vt_year">
                            <button><i id="consumption_peakGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-consumption-peak mt10">
                                    <input type="text" autocomplete="off" name="consumption_peakGraphYear"
                                           placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="consumption_peakForwardYear" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_my_btn_vt" id="consumption-peak-hours">
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
                            <div class="total_peak_hours_vt">Total Peak hours Consumption <span
                                    id="total-peak-hours-consumption"></span></div>
                        </div>
                        <div class="history_gr_vt mt-0 mb-1" style="margin-top: -26px !important;">
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="col-lg-6 mb-3">
                    <div class="card alerts_card_vt hum_card_alar">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Alerts</h2>
                        </div>
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

                <!-- Cost Saving -->
                <div class="col-lg-6 mb-3">
                    <div class="card history_gr_area_vt one">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Cost Saving</h2>
                        </div>
                        <div class="card-box_vt">
                            <div class="day_my_btn_vt mb-3" id="cost-saving-graph-data"
                                 style="margin-top: -15px !important;">
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
                            <div class="gr_vt">
                                <div id="cost-saving-hours"></div>
                                <div class="overtotal_vt">Total Savings <span id="totalCostData"></span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Environmental Benefits -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Environmental Benefits</h2>
                        </div>
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
                                        <div class="col-md-12 mb-3 mb-md-0"><img
                                                src="{{ asset('assets/images/tree_planting.png')}}" alt=""></div>
                                        <div class="col-md-12 tree-vt" id="envPlantingDiv">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row tree-planting-vt">
                                        <div class="col-md-12 tree-vt">
                                            <h6>Accumulative CO<sub>2</sub> Emission <br> Reduction</h6>
                                        </div>
                                        <div class="col-md-12 mb-3 mb-md-0"><img
                                                src="{{ asset('assets/images/chimney.png')}}" alt=""></div>
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

                <!-- Plant Capacity -->
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Plant Capacity</h2>
                        </div>
                        <br>
                        <div class="card-box" dir="ltr">
                            <div class="plants_vt">Plant(s)</div>
                            <div>
                                <div id="bar-chart-plant" style="width: 100%; height: 300px;" data-colors="#0F75BC"
                                     data-capacity_1="{{ $capacity_1 ? $capacity_1 : 0 }}"
                                     data-capacity_2="{{ $capacity_2 ? $capacity_2 : 0 }}"
                                     data-capacity_3="{{ $capacity_3 ? $capacity_3 : 0 }}"
                                     data-capacity_4="{{ $capacity_4 ? $capacity_4 : 0 }}"
                                     data-capacity_5="{{ $capacity_5 ? $capacity_5 : 0 }}"></div>
                            </div>
                        </div>
                        <div class="online-fault-vt">
                            <p><samp class="color08_one_vt"></samp> Total Plants: <span> {{$total_plant}}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Cost Saving -->
                <div class="col-lg-12 mb-3" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Cost Saving3w34533333333333</h2>
                        </div>
                        <div class="card-box">
                            <div class="row w-100">
                                <div class="col-lg-12">
                                    <div class="en_gener_vt" dir="ltr">
                                        <div class="day_my_btn_vt mb-3" id="cost-saving-data"
                                             style="margin-top: -59px !important;">
                                            <button class="day_bt_vt active" id="day">day</button>
                                            <button class="month_bt_vt" id="month">month</button>
                                            <button class="month_bt_vt" id="year">Year</button>
                                        </div>
                                        <div
                                            class="spinner-border text-success costSavingGraphSpinner plantGraphSpinner"
                                            role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <div class="costSavingGraphError plantGraphError" style="display: none;">
                                            <span>Some Error Occured</span>
                                        </div>
                                        <div class="grtow_vt">
                                            <div id="cost-saving-hours1111"></div>
                                            <div class="overtotal_vt">Total Savings <span id="totalCostData"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cost Saving -->
                <div class="col-lg-6 mb-3">
                    <div class="card weather_card_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Weather</h2>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable_1" class="table table-borderless mb-0">
                                <thead>
                                <tr>
                                    <th style="text-align: left !important;width: 98px;padding-left: 15px;">City
                                    </th>
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
                                            <th scope="row"
                                                style="text-align: left !important;padding-left: 15px;">{{ $city_wise->city }}</th>
                                            <td>{{ $city_wise->sunrise }}</td>
                                            <td>{{ $city_wise->sunset }}</td>
                                            <td>{{ $city_wise->temperature}} &#8451;</td>
                                            <td title="{{ $city_wise->condition}}"
                                                style="padding: 8px 0px;transform: translateY(-5px);text-align:center;">
                                                <img
                                                    src="http://openweathermap.org/img/w/{{ $city_wise->icon }}.png"
                                                    alt="Current" width="40"></td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Cost Saving -->
                <div class="col-lg-6 mb-3 mapCard">
                    <div class="card plantsLocations_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Plants Locations</h2>
                        </div>
                        <div class="card-body map_body_vt">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>

                <!-- Ticket List -->
                <div class="col-lg-12 mb-3">
                    <div class="card card_body_padding_vt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Ticket List</h2>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable_7"
                                   class="display table table-borderless table-centered table-nowrap"
                                   style="width:100%">
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
                                    <tr class="clickable"
                                        href="{{route('admin.view.edit.ticket', ['type' => 'hybrid','id' => $item->id])}}"
                                        style="cursor: pointer;">
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
            <!-- end row -->
            <div class="row">
                <div class="col-12">
                    <div class="card hum_tum_vt pla_body_padd_vt pb-0 mb-4">
                        <div class="card-body mb-0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-header">
                                        <h3 class="All-graph-heading-vt">Plants</h3>
                                        <div class="dataTables_length_vt bs-select" id="dtBasicExample_length">
                                            <label>Show
                                                <select name="dtBasicExample_length" aria-controls="dtBasicExample"
                                                        class="custom-select custom-select-sm form-control form-control-sm">
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
                                <table id="datatable_hybrid2"
                                       class="display table table-borderless table-centered table-nowrap"
                                       style="width:100%">
                                    <thead class="thead-light vt_head_td">
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Status</th>
                                        <th>Plant Name</th>
                                        <th>Capacity</th>
                                        <th>Current Generation </th>
                                        <th>Daily Generation</th>
                                        <th>Battery SOC</th>
                                        <th>Battery Cycles</th>
                                        <th>Last Alarm</th>
                                        <th>Last Updated at</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($plants_dashboard as $key => $plant)
                                        <tr>
                                            <td class="one_setting_vt">
                                                <p>{{$plant_counter++}}</p>
                                            </td>
                                            <td class="che_vt">
                                                @if($plant->is_online == 'Y')
                                                    <img src="{{ asset('assets/images/icon_plant_check_vt.svg')}}"
                                                         alt="check" title="Online">
                                                @elseif($plant->is_online == 'P_Y')
                                                    <img src="{{ asset('assets/images/icon_plant_alert_vt.png')}}"
                                                         alt="check" title="Partially Online">
                                                @else
                                                    <img src="{{ asset('assets/images/icon_plant_vt.svg')}}"
                                                         alt="check"
                                                         title="Offline">
                                                @endif
                                            </td>
                                            <td class="one_btn_vt">
                                                <a href="{{ url('admin/hybrid/user-plant-detail/'.$plant->id)}}"
                                                   title="Plant Detail">{{ $plant->plant_name }}</a>
                                            </td>
                                            <td>
                                                {{ $plant->capacity.' kW' }}
                                            </td>
                                            <td>
                                                {{ $plant->latest_processed_current_variables != null ? $plant->latest_processed_current_variables->current_generation.' kW': '---' }}

                                            </td>
                                            <td>
                                                {{ $plant->expected_generation.' kWh' }}
                                            </td>
                                            <?php $batterySoc = \App\Http\Models\StationBattery::where('plant_id',$plant->id)->latest()->first();
                                            $batterySocData = '-----';
                                            if($batterySoc)
                                            {
                                                $batterySocData = $batterySoc->battery_capacity;
                                            }
                                            ?>
                                            <td>
                                                {{$batterySocData}}
                                            </td>
                                            <td>
                                                ---------
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-gl/dist/echarts-gl.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-stat/dist/ecStat.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/npm/echarts/dist/extension/dataTool.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/china.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/world.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/extension/bmap.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var filterss_arr = {};
            var plants_array = <?php echo $filter_data['plants']; ?>;
            var plant_axis_grid = 4;
            var labels = {};
            var plant_name = {};
            var capacity_chunk = {!!$capacity_chunk!!};
            var designedCapacity = {!! $designedCapacity !!};
            var month_name_arr = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            //companyPlantFilter(plants_array);

            $('.clickable').click(function () {
                window.location.href = $(this).attr('href');
            });

            $('.companyFilterMultiSelect').select2({

                "placeholder": "Select Companies"
            });

            $('.plantFilterMultiSelect').select2({

                "placeholder": "Select Plants"
            });

            $('#company').change(function () {

                companyPlantFilter(plants_array);
            });

            $('#clearFilters').on('click', function (e) {

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
                        formatter: function (params) {
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
            $('input[name="solarEnergyUtilizationGraphDay"]').val(currDate.todayDate);
            $('input[name="consumption_peakGraphDay"]').val(currDate.todayDate);
            $('input[name="energySourceDay"]').val(currDate.todayDate);
            $('input[name="energy_genGraphMonth"]').val(currDate.todayMonth);
            $('input[name="solarEnergyUtilizationGraphMonth"]').val(currDate.todayMonth);
            $('input[name="consumption_peakGraphMonth"]').val(currDate.todayMonth);
            $('input[name="energy_genGraphYear"]').val(currDate.todayYear);
            $('input[name="solarEnergyUtilizationGraphYear"]').val(currDate.todayYear);
            $('input[name="consumption_peakGraphYear"]').val(currDate.todayYear);
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
            $('input[name="maxPowerGraphDay"]').val(currDate.todayDate);
            $('input[name="maxPowerGraphMonth"]').val(currDate.todayMonth);
            $('input[name="maxPowerGraphYear"]').val(currDate.todayYear);
            $('input[name="energySourceGraphDay"]').val(currDate.todayDate);
            $('input[name="energySourceGraphMonth"]').val(currDate.todayMonth);
            $('input[name="energySourceGraphYear"]').val(currDate.todayYear);
            $('input[name="specific-yieldGraphMonth"]').val(currDate.todayMonth);
            $('input[name="specific-yieldGraphYear"]').val(currDate.todayYear);
            $('input[name="outageServedGraphDay"]').val(currDate.todayDate);
            $('input[name="outageServedGraphMonth"]').val(currDate.todayMonth);
            $('input[name="outageServedGraphYear"]').val(currDate.todayYear);

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
            var energy_date = $('input[name="alertGraphDay"]').val();
            var energy_time = 'day';
            var specific_yield = 'month';
            var history_date = $('input[name="historyGraphDay"]').val();
            var history_time = 'day';
            var specific_yield_month = $('input[name="alertGraphMonth"]').val();
            var solarEnergyUtilizationDate = $('input[name="solarEnergyUtilizationGraphDay"]').val();
            var solarEnergyUtilizationTime = 'day';
            solarEnergySourceGraphAjaxData(energy_date, energy_time, filterss_arr, plant_name);
            OutagesGraphAjax(energy_date, energy_time, filterss_arr, plant_name);
            costSavingDataAjax(energy_date, energy_time, filterss_arr, plant_name);
            consumptionGraphAjax(energy_date, energy_time, filterss_arr, plant_name);
            specificYieldGraphAjax(specific_yield_month, specific_yield, filterss_arr, plant_name);
            // maximumPowerAchievedGraph(30);
            solarEnergyUtilizationAjax(solarEnergyUtilizationDate, solarEnergyUtilizationTime, filterss_arr, plant_name);
            maximumPowerAchievedGraphAjax(solarEnergyUtilizationDate, solarEnergyUtilizationTime, filterss_arr, plant_name, designedCapacity);
            $('#dashboardSpinner').hide();

            changeSavingDayMonthYear(saving_date, saving_time);
            // savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            changeEnergyGenerationDayMonthYear(energy_gen_date, energy_gen_time);
            changeConsumptionPeakDayMonthYear(energy_gen_date, energy_gen_time);
            // energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            changeENVDayMonthYear(env_date, env_time);
            // envGraphAjax(env_date, env_time, filterss_arr, plant_name);
            changeExpGenDayMonthYear(expGen_date, expGen_time);
            // expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            changeAlertDayMonthYear(alert_date, alert_time);
            changeSpecificYieldMonthYear(specific_yield_month, specific_yield);
            changeSolarEnergyUtilizationDayMonthYear(solarEnergyUtilizationDate, solarEnergyUtilizationTime);
            // alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);

            $('.J-yearMonthDayPicker-single-max-power').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeMaxPowerDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-max-power').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeMaxPowerDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-max-power').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeMaxPowerDayMonthYear(this.$input.eq(0).val(), 'year');
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
            $('.J-yearMonthDayPicker-single-energy_gen').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeEnergyGenerationDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });
            $('.J-yearMonthDayPicker-single-solar-energy-utilization-day').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeSolarEnergyUtilizationDayMonthYear(this.$input.eq(0).val(), 'day');
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
            $('.J-yearMonthDayPicker-single-consumption-peak').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeConsumptionPeakDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });
            $('.J-yearMonthPicker-single-specific-yield').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    changeSpecificYieldMonthYear(this.$input.eq(0).val(), 'month');
                }
            });
            $('.J-yearMonthDayPicker-single-solar-energy-utilization-month').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    changeSolarEnergyUtilizationDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearMonthPicker-single-consumption-peak').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    changeConsumptionPeakDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });
            $('.J-yearMonthPicker-single-energy_gen').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeEnergyGenerationDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-specific-yield').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeSpecificYieldMonthYear(this.$input.eq(0).val(), 'year');
                }
            });
            $('.J-yearMonthDayPicker-single-solar-energy-utilization-year').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeSolarEnergyUtilizationDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });
            $('.J-yearPicker-single-consumption-peak').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeConsumptionPeakDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });
            $('.J-yearPicker-single-energy_gen').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeEnergyGenerationDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-env').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeENVDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-env').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeENVDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-env').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeENVDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-saving').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeSavingDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-saving').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeSavingDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-saving').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeSavingDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-expGen').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-expGen').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-expGen').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeExpGenDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-alert').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeAlertDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-alert').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeAlertDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-alert').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeAlertDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('#maxPowerGraphPreviousDay').on('click', function () {

                show_date = $("input[name='maxPowerGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                history_date = formatDate(datess);
                $('input[name="maxPowerGraphDay"]').val('');
                $('input[name="maxPowerGraphDay"]').val(history_date);
                console.log($("input[name='maxPowerGraphDay']").val());
                history_time = 'day';
                maximumPowerAchievedGraphAjax(history_date, history_time, filterss_arr, plant_name, designedCapacity);
            });

            $('#maxPowerGraphForwardDay').on('click', function () {

                show_date = $("input[name='maxPowerGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                history_date = formatDate(datess);
                $('input[name="maxPowerGraphDay"]').val('');
                $('input[name="maxPowerGraphDay"]').val(history_date);
                console.log($("input[name='maxPowerGraphDay']").val());
                history_time = 'day';
                maximumPowerAchievedGraphAjax(history_date, history_time, filterss_arr, plant_name, designedCapacity);
            });

            $('#maxPowerGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='maxPowerGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="maxPowerGraphMonth"]').val('');
                $('input[name="maxPowerGraphMonth"]').val(history_date);
                console.log($("input[name='maxPowerGraphMonth']").val());
                history_time = 'month';
                maximumPowerAchievedGraphAjax(history_date, history_time, filterss_arr, plant_name, designedCapacity);
            });

            $('#maxPowerGraphForwardMonth').on('click', function () {

                show_date = $("input[name='maxPowerGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="maxPowerGraphMonth"]').val('');
                $('input[name="maxPowerGraphMonth"]').val(history_date);
                console.log($("input[name='maxPowerGraphMonth']").val());
                history_time = 'month';
                maximumPowerAchievedGraphAjax(history_date, history_time, filterss_arr, plant_name, designedCapacity);
            });

            $('#maxPowerGraphPreviousYear').on('click', function () {

                show_date = $("input[name='maxPowerGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="maxPowerGraphYear"]').val('');
                $('input[name="maxPowerGraphYear"]').val(history_date);
                console.log($("input[name='maxPowerGraphYear']").val());
                history_time = 'year';
                maximumPowerAchievedGraphAjax(history_date, history_time, filterss_arr, plant_name, designedCapacity);
            });

            $('#maxPowerGraphForwardYear').on('click', function () {

                show_date = $("input[name='maxPowerGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="maxPowerGraphYear"]').val('');
                $('input[name="maxPowerGraphYear"]').val(history_date);
                console.log($("input[name='maxPowerGraphYear']").val());
                history_time = 'year';
                maximumPowerAchievedGraphAjax(history_date, history_time, filterss_arr, plant_name, designedCapacity);
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
                solarEnergySourceGraphAjaxData(history_date, history_time, filterss_arr, plant_name);
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
                solarEnergySourceGraphAjaxData(history_date, history_time, filterss_arr, plant_name);
            });

            $('#energySourceGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='energySourceGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="energySourceGraphMonth"]').val('');
                $('input[name="energySourceGraphMonth"]').val(history_date);
                console.log($("input[name='energySourceGraphMonth']").val());
                history_time = 'month';
                solarEnergySourceGraphAjaxData(history_date, history_time, filterss_arr, plant_name);
            });

            $('#energySourceGraphForwardMonth').on('click', function () {

                show_date = $("input[name='energySourceGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="energySourceGraphMonth"]').val('');
                $('input[name="energySourceGraphMonth"]').val(history_date);
                console.log($("input[name='energySourceGraphMonth']").val());
                history_time = 'month';
                solarEnergySourceGraphAjaxData(history_date, history_time, filterss_arr, plant_name);
            });

            $('#energySourceGraphPreviousYear').on('click', function () {

                show_date = $("input[name='energySourceGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="energySourceGraphYear"]').val('');
                $('input[name="energySourceGraphYear"]').val(history_date);
                console.log($("input[name='energySourceGraphYear']").val());
                history_time = 'year';
                solarEnergySourceGraphAjaxData(history_date, history_time, filterss_arr, plant_name);
            });

            $('#energySourceGraphForwardYear').on('click', function () {

                show_date = $("input[name='energySourceGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="energySourceGraphYear"]').val('');
                $('input[name="energySourceGraphYear"]').val(history_date);
                console.log($("input[name='energySourceGraphYear']").val());
                history_time = 'year';
                solarEnergySourceGraphAjaxData(history_date, history_time, filterss_arr, plant_name);
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
                OutagesGraphAjax(history_date, history_time, filterss_arr, plant_name);
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
                OutagesGraphAjax(history_date, history_time, filterss_arr, plant_name);
            });

            $('#outageServedGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='outageServedGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="outageServedGraphMonth"]').val('');
                $('input[name="outageServedGraphMonth"]').val(history_date);
                console.log($("input[name='outageServedGraphMonth']").val());
                history_time = 'month';
                OutagesGraphAjax(history_date, history_time, filterss_arr, plant_name);
            });

            $('#outageServedGraphForwardMonth').on('click', function () {

                show_date = $("input[name='outageServedGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="outageServedGraphMonth"]').val('');
                $('input[name="outageServedGraphMonth"]').val(history_date);
                console.log($("input[name='outageServedGraphMonth']").val());
                history_time = 'month';
                OutagesGraphAjax(history_date, history_time, filterss_arr, plant_name);
            });

            $('#outageServedGraphPreviousYear').on('click', function () {

                show_date = $("input[name='outageServedGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="outageServedGraphYear"]').val('');
                $('input[name="outageServedGraphYear"]').val(history_date);
                console.log($("input[name='outageServedGraphYear']").val());
                history_time = 'year';
                OutagesGraphAjax(history_date, history_time,  filterss_arr, plant_name);
            });

            $('#outageServedGraphForwardYear').on('click', function () {

                show_date = $("input[name='outageServedGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="outageServedGraphYear"]').val('');
                $('input[name="outageServedGraphYear"]').val(history_date);
                console.log($("input[name='outageServedGraphYear']").val());
                history_time = 'year';
                OutagesGraphAjax(history_date, history_time,  filterss_arr, plant_name);
            });

            $('#energy_genGraphPreviousDay').on('click', function () {

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
            $('#consumption_peakGraphPreviousDay').on('click', function () {

                show_date = $("input[name='consumption_peakGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                energy_gen_date = formatDate(datess);
                $('input[name="consumption_peakGraphDay"]').val('');
                $('input[name="consumption_peakGraphDay"]').val(energy_gen_date);
                console.log($("input[name='consumption_peakGraphDay']").val());
                energy_gen_time = 'day';
                consumptionGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#consumption_peakGraphForwardDay').on('click', function () {

                show_date = $("input[name='consumption_peakGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                energy_gen_date = formatDate(datess);
                $('input[name="consumption_peakGraphDay"]').val('');
                $('input[name="consumption_peakGraphDay"]').val(energy_gen_date);
                console.log($("input[name='consumption_peakGraphDay']").val());
                energy_gen_time = 'day';
                consumptionGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#energy_genGraphForwardDay').on('click', function () {

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

            $('#energy_genGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='energy_genGraphMonth']").val();
                energy_gen_date = formatPreviousMonth(show_date);
                $('input[name="energy_genGraphMonth"]').val('');
                $('input[name="energy_genGraphMonth"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphMonth']").val());
                energy_gen_time = 'month';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#consumption_peakGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='consumption_peakGraphMonth']").val();
                energy_gen_date = formatPreviousMonth(show_date);
                $('input[name="consumption_peakGraphMonth"]').val('');
                $('input[name="consumption_peakGraphMonth"]').val(energy_gen_date);
                console.log($("input[name='consumption_peakGraphMonth']").val());
                energy_gen_time = 'month';
                consumptionGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#specific-yieldGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='specific-yieldGraphMonth']").val();
                energy_gen_date = formatPreviousMonth(show_date);
                $('input[name="specific-yieldGraphMonth"]').val('');
                $('input[name="specific-yieldGraphMonth"]').val(energy_gen_date);
                energy_gen_time = 'month';
                specificYieldGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#specific-yieldGraphForwardMonth').on('click', function () {

                show_date = $("input[name='specific-yieldGraphMonth']").val();
                energy_gen_date = formatForwardMonth(show_date);
                $('input[name="specific-yieldGraphMonth"]').val('');
                $('input[name="specific-yieldGraphMonth"]').val(energy_gen_date);
                energy_gen_time = 'month';
                specificYieldGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphForwardMonth').on('click', function () {

                show_date = $("input[name='energy_genGraphMonth']").val();
                energy_gen_date = formatForwardMonth(show_date);
                $('input[name="energy_genGraphMonth"]').val('');
                $('input[name="energy_genGraphMonth"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphMonth']").val());
                energy_gen_time = 'month';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#consumption_peakGraphForwardMonth').on('click', function () {

                show_date = $("input[name='consumption_peakGraphMonth']").val();
                energy_gen_date = formatForwardMonth(show_date);
                $('input[name="consumption_peakGraphMonth"]').val('');
                $('input[name="consumption_peakGraphMonth"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphMonth']").val());
                energy_gen_time = 'month';
                consumptionGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphPreviousYear').on('click', function () {

                show_date = $("input[name='energy_genGraphYear']").val();
                energy_gen_date = formatPreviousYear(show_date);
                $('input[name="energy_genGraphYear"]').val('');
                $('input[name="energy_genGraphYear"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphYear']").val());
                energy_gen_time = 'year';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#specific-yieldGraphPreviousYear').on('click', function () {

                show_date = $("input[name='specific-yieldGraphYear']").val();
                energy_gen_date = formatPreviousYear(show_date);
                $('input[name="specific-yieldGraphYear"]').val('');
                $('input[name="specific-yieldGraphYear"]').val(energy_gen_date);
                console.log($("input[name='specific-yieldGraphYear']").val());
                energy_gen_time = 'year';
                specificYieldGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#specific_yieldForwardYear').on('click', function () {

                show_date = $("input[name='specific-yieldGraphYear']").val();
                energy_gen_date = formatForwardYear(show_date);
                $('input[name="specific-yieldGraphYear"]').val('');
                $('input[name="specific-yieldGraphYear"]').val(energy_gen_date);
                console.log($("input[name='specific-yieldGraphYear']").val());
                energy_gen_time = 'year';
                specificYieldGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#consumption_peakGraphPreviousYear').on('click', function () {

                show_date = $("input[name='consumption_peakGraphYear']").val();
                energy_gen_date = formatPreviousYear(show_date);
                $('input[name="consumption_peakGraphYear"]').val('');
                $('input[name="consumption_peakGraphYear"]').val(energy_gen_date);
                console.log($("input[name='consumption_peakGraphYear']").val());
                energy_gen_time = 'year';
                consumptionGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });

            $('#energy_genGraphForwardYear').on('click', function () {

                show_date = $("input[name='energy_genGraphYear']").val();
                energy_gen_date = formatForwardYear(show_date);
                $('input[name="energy_genGraphYear"]').val('');
                $('input[name="energy_genGraphYear"]').val(energy_gen_date);
                console.log($("input[name='energy_genGraphYear']").val());
                energy_gen_time = 'year';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#consumption_peakForwardYear').on('click', function () {

                show_date = $("input[name='consumption_peakGraphYear']").val();
                energy_gen_date = formatForwardYear(show_date);
                $('input[name="consumption_peakGraphYear"]').val('');
                $('input[name="consumption_peakGraphYear"]').val(energy_gen_date);
                console.log($("input[name='consumption_peakGraphYear']").val());
                energy_gen_time = 'year';
                consumptionGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
            });
            $('#consumption_peakGraphForwardYear').on('click', function () {

                show_date = $("input[name='consumption_peakGraphYear']").val();
                energy_gen_date = formatForwardYear(show_date);
                $('input[name="consumption_peakGraphYear"]').val('');
                $('input[name="consumption_peakGraphYear"]').val(energy_gen_date);
                console.log($("input[name='consumption_peakGraphYear']").val());
                energy_gen_time = 'year';
                energy_genGraphAjax(energy_gen_date, energy_gen_time, filterss_arr, plant_name);
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
                envGraphAjax(env_date, env_time, filterss_arr, plant_name);
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
                envGraphAjax(env_date, env_time, filterss_arr, plant_name);
            });

            $('#envGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatPreviousMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(env_date, env_time, filterss_arr, plant_name);
            });

            $('#envGraphForwardMonth').on('click', function () {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatForwardMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(env_date, env_time, filterss_arr, plant_name);
            });

            $('#envGraphPreviousYear').on('click', function () {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatPreviousYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(env_date, env_time, filterss_arr, plant_name);
            });

            $('#envGraphForwardYear').on('click', function () {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatForwardYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(env_date, env_time, filterss_arr, plant_name);
            });

            $('#savingGraphPreviousDay').on('click', function () {

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

            $('#savingGraphForwardDay').on('click', function () {

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

            $('#savingGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='savingGraphMonth']").val();
                saving_date = formatPreviousMonth(show_date);
                $('input[name="savingGraphMonth"]').val('');
                $('input[name="savingGraphMonth"]').val(saving_date);
                console.log($("input[name='savingGraphMonth']").val());
                saving_time = 'month';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphForwardMonth').on('click', function () {

                show_date = $("input[name='savingGraphMonth']").val();
                saving_date = formatForwardMonth(show_date);
                $('input[name="savingGraphMonth"]').val('');
                $('input[name="savingGraphMonth"]').val(saving_date);
                console.log($("input[name='savingGraphMonth']").val());
                saving_time = 'month';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphPreviousYear').on('click', function () {

                show_date = $("input[name='savingGraphYear']").val();
                saving_date = formatPreviousYear(show_date);
                $('input[name="savingGraphYear"]').val('');
                $('input[name="savingGraphYear"]').val(saving_date);
                console.log($("input[name='savingGraphYear']").val());
                saving_time = 'year';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#savingGraphForwardYear').on('click', function () {

                show_date = $("input[name='savingGraphYear']").val();
                saving_date = formatForwardYear(show_date);
                $('input[name="savingGraphYear"]').val('');
                $('input[name="savingGraphYear"]').val(saving_date);
                console.log($("input[name='savingGraphYear']").val());
                saving_time = 'year';
                savingGraphAjax(saving_date, saving_time, filterss_arr, plant_name);
            });

            $('#expGenGraphPreviousDay').on('click', function () {

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
            $('#solarEnergyUtilizationGraphPreviousDay').on('click', function () {

                show_date = $("input[name='solarEnergyUtilizationGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                expGen_date = formatDate(datess);
                $('input[name="solarEnergyUtilizationGraphDay"]').val('');
                $('input[name="solarEnergyUtilizationGraphDay"]').val(expGen_date);
                console.log($("input[name='solarEnergyUtilizationGraphDay']").val());
                expGen_time = 'day';
                solarEnergyUtilizationAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphForwardDay').on('click', function () {

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
            $('#solarEnergyUtilizationGraphForwardDay').on('click', function () {

                show_date = $("input[name='solarEnergyUtilizationGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                expGen_date = formatDate(datess);
                $('input[name="solarEnergyUtilizationGraphDay"]').val('');
                $('input[name="solarEnergyUtilizationGraphDay"]').val(expGen_date);
                console.log($("input[name='solarEnergyUtilizationGraphDay']").val());
                expGen_time = 'day';
                solarEnergyUtilizationAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='expGenGraphMonth']").val();
                expGen_date = formatPreviousMonth(show_date);
                $('input[name="expGenGraphMonth"]').val('');
                $('input[name="expGenGraphMonth"]').val(expGen_date);
                console.log($("input[name='expGenGraphMonth']").val());
                expGen_time = 'month';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });
            $('#solarEnergyUtilizationGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='solarEnergyUtilizationGraphMonth']").val();
                expGen_date = formatPreviousMonth(show_date);
                $('input[name="solarEnergyUtilizationGraphMonth"]').val('');
                $('input[name="solarEnergyUtilizationGraphMonth"]').val(expGen_date);
                console.log($("input[name='solarEnergyUtilizationGraphMonth']").val());
                expGen_time = 'month';
                solarEnergyUtilizationAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });

            $('#expGenGraphForwardMonth').on('click', function () {

                show_date = $("input[name='expGenGraphMonth']").val();
                expGen_date = formatForwardMonth(show_date);
                $('input[name="expGenGraphMonth"]').val('');
                $('input[name="expGenGraphMonth"]').val(expGen_date);
                console.log($("input[name='expGenGraphMonth']").val());
                expGen_time = 'month';
                expGenGraphAjax(expGen_date, expGen_time, filterss_arr, plant_name);
            });
            $('#solarEnergyUtilizationGraphForwardMonth').on('click', function () {

                show_date = $("input[name='solarEnergyUtilizationGraphMonth']").val();
                expGen_date = formatForwardMonth(show_date);
                $('input[name="solarEnergyUtilizationGraphMonth"]').val('');
                $('input[name="solarEnergyUtilizationGraphMonth"]').val(expGen_date);
                console.log($("input[name='solarEnergyUtilizationGraphMonth']").val());
                expGen_time = 'month';
                solarEnergyUtilizationAjax(expGen_date, expGen_time, filterss_arr, plant_name);
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
            $('#solarEnergyUtilizationGraphPreviousYear').on('click', function () {

                show_date = $("input[name='solarEnergyUtilizationGraphYear']").val();
                expGen_date = formatPreviousYear(show_date);
                $('input[name="solarEnergyUtilizationGraphYear"]').val('');
                $('input[name="solarEnergyUtilizationGraphYear"]').val(expGen_date);
                console.log($("input[name='solarEnergyUtilizationGraphYear']").val());
                expGen_time = 'year';
                solarEnergyUtilizationAjax(expGen_date, expGen_time, filterss_arr, plant_name);
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
            $('#solarEnergyUtilizationGraphForwardYear').on('click', function () {

                show_date = $("input[name='solarEnergyUtilizationGraphYear']").val();
                expGen_date = formatForwardYear(show_date);
                $('input[name="solarEnergyUtilizationGraphYear"]').val('');
                $('input[name="solarEnergyUtilizationGraphYear"]').val(expGen_date);
                console.log($("input[name='solarEnergyUtilizationGraphYear']").val());
                expGen_time = 'year';
                solarEnergyUtilizationAjax(expGen_date, expGen_time, filterss_arr, plant_name);
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
                alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
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
                alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
            });

            $('#alertGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatPreviousMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
            });

            $('#alertGraphForwardMonth').on('click', function () {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatForwardMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
            });

            $('#alertGraphPreviousYear').on('click', function () {

                show_date = $("input[name='alertGraphYear']").val();
                alert_date = formatPreviousYear(show_date);
                $('input[name="alertGraphYear"]').val('');
                $('input[name="alertGraphYear"]').val(alert_date);
                console.log($("input[name='alertGraphYear']").val());
                alert_time = 'year';
                alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
            });

            $('#alertGraphForwardYear').on('click', function () {

                show_date = $("input[name='alertGraphYear']").val();
                alert_date = formatForwardYear(show_date);
                $('input[name="alertGraphYear"]').val('');
                $('input[name="alertGraphYear"]').val(alert_date);
                console.log($("input[name='alertGraphYear']").val());
                alert_time = 'year';
                alertGraphAjax(alert_date, alert_time, filterss_arr, plant_name);
            });

            $("#energy_gen_day_my_btn_vt button").click(function () {

                $('#energy_gen_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeEnergyGenerationDayMonthYear(energy_gen_date, energy_gen_time);

            });
            // $("#maximum-power-achieved-graph button").click(function () {
            //
            //     $('#maximum-power-achieved-graph').children().removeClass("active");
            //     $(this).addClass("active");
            //
            //     changeMaximumPowerAchievedDayMonthYear(energy_gen_date, energy_gen_time);
            //
            // });
            $("#energy_source_day_my_btn_vt button").click(function () {

                $('#energy_source_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeSolarEnergyDayMonthYear(energy_date, energy_time);

            });
            $("#outages-served-dashboard button").click(function () {

                $('#outages-served-dashboard').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                changeOutagesDayMonthYear(energy_date, filterss_arr, plant_name);

            });
            $("#consumption-peak-hours button").click(function () {

                $('#consumption-peak-hours').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                changeConsumptionPeakDayMonthYear(energy_date, filterss_arr, plant_name);

            });
            $("#cost-saving-graph-data button").click(function () {

                $('#cost-saving-graph-data').children().removeClass("active");
                $(this).addClass("active");
                // console.log(history_date)
                // console.log($(this))
                changeCostSavingDayMonthYear(energy_date, filterss_arr, plant_name);

            });

            // function changeSolarDataEnergyDayMonthYear(date) {
            //     var d_m_y = '';
            //
            //     $('#history_day_my_btn_vt_31').children('button').each(function () {
            //         if ($(this).hasClass('active')) {
            //             d_m_y = $(this).attr('id');
            //         }
            //     });
            //
            //     solarEnergyGraphAjaxData(date, d_m_y);
            // }

            $("#env_day_my_btn_vt button").click(function () {

                $('#env_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeENVDayMonthYear(env_date, env_time);

            });

            $("#saving_day_my_btn_vt button").click(function () {

                $('#saving_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeSavingDayMonthYear(saving_date, saving_time);

            });

            $("#expGen_day_my_btn_vt button").click(function () {

                $('#expGen_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeExpGenDayMonthYear(expGen_date, expGen_time);

            });

            $("#alert_day_my_btn_vt button").click(function () {

                $('#alert_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeAlertDayMonthYear(alert_date, alert_time);

            });
            $("#solar-energy-utilization-data button").click(function () {

                $('#solar-energy-utilization-data').children().removeClass("active");
                $(this).addClass("active");
                changeSolarEnergyUtilizationDayMonthYear(solarEnergyUtilizationDate, solarEnergyUtilizationTime);

            });
            $("#specific-yield-data button").click(function () {

                $('#specific-yield-data').children().removeClass("active");
                $(this).addClass("active");
                changeSpecificYieldMonthYear(specific_yield_month, specific_yield);

            });

            function companyPlantFilter(plants_array) {

                $('#plant_name').empty();

                if (plants_array.length > 0) {

                    var com_id = $('#company').val();

                    for (var i = 0; i < com_id.length; i++) {

                        for (var j = 0; j < plants_array.length; j++) {

                            if (com_id[i] == plants_array[j].company_id) {

                                $('#plant_name').append('<option value=' + plants_array[j].id + '>' + plants_array[j].plant_name + '</option>')
                            }
                        }
                    }
                }
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

                solarEnergySourceGraphAjaxData(date, d_m_y, filterss_arr, plant_name);
            }

            $("#energy_source_day_my_btn_vt12 button").click(function () {

                $('#energy_source_day_my_btn_vt12').children().removeClass("active");
                $(this).addClass("active");

                changeEnergySourceDayMonthYear(history_date, history_time);

            });
            function changeEnergyGenerationDayMonthYear(date, time) {

                var d_m_y = '';

                $('#energy_gen_day_my_btn_vt').children('button').each(function () {
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

            // function changeMaximumPowerAchievedDayMonthYear(date, time) {
            //
            //     var d_m_y = '';
            //
            //     $('#maximum-power-achieved-graph').children('button').each(function () {
            //         if ($(this).hasClass('active')) {
            //             d_m_y = $(this).attr('id');
            //         }
            //     });
            //     var currDate = getCurrentDate();
            //
            //     if (d_m_y == 'day') {
            //         date = currDate.todayDate;
            //         time = 'day';
            //     } else if (d_m_y == 'month') {
            //         date = currDate.todayMonth;
            //         time = 'month';
            //     } else if (d_m_y == 'year') {
            //         date = currDate.todayYear;
            //         time = 'year';
            //     }
            //     console.log([date, time]);
            //     maximumPowerAchievedGraphAjax(date, time, filterss_arr, plant_name, designedCapacity);
            // }
            $("#max_power_day_my_btn_vt12 button").click(function () {

                $('#max_power_day_my_btn_vt12').children().removeClass("active");
                $(this).addClass("active");

                changeMaxPowerDayMonthYear(history_date, history_time);

            });
            function changeMaxPowerDayMonthYear(date, time) {

                var d_m_y = '';

                $('#max_power_day_my_btn_vt12').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });

                if (d_m_y == 'day') {
                    $('#max_power_day_month_year_vt_year').hide();
                    $('#max_power_day_month_year_vt_month').hide();
                    $('#max_power_day_month_year_vt_day').show();
                    date = $('input[name="maxPowerGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    $('#max_power_day_month_year_vt_year').hide();
                    $('#max_power_day_month_year_vt_day').hide();
                    $('#max_power_day_month_year_vt_month').show();
                    date = $('input[name="maxPowerGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#max_power_day_month_year_vt_day').hide();
                    $('#max_power_day_month_year_vt_month').hide();
                    $('#max_power_day_month_year_vt_year').show();
                    date = $('input[name="maxPowerGraphYear"]').val();
                    time = 'year';
                }

                maximumPowerAchievedGraphAjax(date, d_m_y, filterss_arr, plant_name, designedCapacity);
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

                OutagesGraphAjax(date, d_m_y, filterss_arr, plant_name);
            }
            function changeConsumptionPeakDayMonthYear(date, time) {

                var d_m_y = '';

                $('#consumption-peak-hours').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });
                if (d_m_y == 'day') {
                    $('#consumption_peak_day_month_year_vt_year').hide();
                    $('#consumption_peak_day_month_year_vt_month').hide();
                    $('#consumption_peak_day_month_year_vt_day').show();
                    date = $('input[name="consumption_peakGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    console.log('month');
                    $('#consumption_peak_day_month_year_vt_year').hide();
                    document.getElementById('consumption_peak_day_month_year_vt_day').style.display = 'none';
                    $('#consumption_peak_day_month_year_vt_month').show();
                    date = $('input[name="consumption_peakGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    console.log('year');
                    $('#consumption_peak_day_month_year_vt_day').hide();
                    $('#consumption_peak_day_month_year_vt_month').hide();
                    $('#consumption_peak_day_month_year_vt_year').show();
                    date = $('input[name="consumption_peakGraphYear"]').val();
                    time = 'year';
                }

                consumptionGraphAjax(date, time, filterss_arr, plant_name);
            }

            function changeSpecificYieldMonthYear(date, time) {

                var d_m_y = '';

                $('#specific-yield-data').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });
                if (d_m_y == 'month') {
                    $('#specific_yield_day_month_year_vt_month').show();
                    $('#specific_yield_day_month_year_vt_year').hide();

                    date = $('input[name="specific-yieldGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#specific_yield_day_month_year_vt_month').hide();
                    $('#specific_yield_day_month_year_vt_year').show();
                    date = $('input[name="specific-yieldGraphYear"]').val();
                    time = 'year';
                }

                specificYieldGraphAjax(date, time, filterss_arr, plant_name);
            }

            function changeSolarEnergyUtilizationDayMonthYear(date, time) {

                var d_m_y = '';

                $('#solar-energy-utilization-data').children('button').each(function () {
                    if ($(this).hasClass('active')) {
                        d_m_y = $(this).attr('id');
                    }
                });
                if (d_m_y == 'day') {
                    $('#solar_energy_utilization_day_month_year_vt_day').show();
                    $('#solar_energy_utilization_month_year_vt_month').hide();
                    $('#solar_energy_utilization_month_year_vt_year').hide();

                    date = $('input[name="solarEnergyUtilizationGraphDay"]').val();
                    time = 'day';
                } else if (d_m_y == 'month') {
                    $('#solar_energy_utilization_day_month_year_vt_day').hide();
                    $('#solar_energy_utilization_month_year_vt_month').show();
                    $('#solar_energy_utilization_month_year_vt_year').hide();

                    date = $('input[name="solarEnergyUtilizationGraphMonth"]').val();
                    time = 'month';
                } else if (d_m_y == 'year') {
                    $('#solar_energy_utilization_day_month_year_vt_day').hide();
                    $('#solar_energy_utilization_month_year_vt_month').hide();
                    $('#solar_energy_utilization_month_year_vt_year').show();
                    date = $('input[name="solarEnergyUtilizationGraphYear"]').val();
                    time = 'year';
                }
                console.log(time);
                solarEnergyUtilizationAjax(date, time, filterss_arr, plant_name);
            }

            // function changeSolarEnergyDayMonthYear(date, time) {
            //
            //     var d_m_y = '';
            //     console.log(date);
            //
            //     $('#energy_source_day_my_btn_vt').children('button').each(function () {
            //         if ($(this).hasClass('active')) {
            //             d_m_y = $(this).attr('id');
            //         }
            //     });
            //
            //     solarEnergyGraphAjaxData(date, d_m_y, filterss_arr, plant_name);
            //
            //     // solarEnergyGraphAjaxData(date, time, filterss_arr, plant_name);
            // }

            function changeENVDayMonthYear(date, time) {

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

                envGraphAjax(date, time, filterss_arr, plant_name);
            }

            function changeSavingDayMonthYear(date, time) {

                var d_m_y = '';

                $('#saving_day_my_btn_vt').children('button').each(function () {
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

                $('#expGen_day_my_btn_vt').children('button').each(function () {
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
                        'type': 'hybrid'
                    },

                    dataType: 'json',
                    success: function (data) {

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
                    error: function (data) {
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
                        'type': 'hybrid'
                    },

                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        $('#chartContainerDiv div').remove();
                        $('#chartDetailDiv').empty();

                        $('#chartContainerDiv').append('<div class="kWh_eng_vt">kWh</div>');
                        $('#chartContainerDiv').append('<div id="energyContainer" style="height: 230px; width: 100%;"></div>');
                        if (time == 'day') {

                            $('#chartDetailDiv').append('<p><samp></samp>Today Energy Generation :<span> ' + data.total_today + '</span></p><p><samp class="samp_vt"></samp>Yesterday Energy Generation: <span class="one">' + data.total_yesterday + '</span></p><p><samp class="samp_vt"></samp>Total Energy Generation: <span class="one">' + data.plant_total_generation + '</span></p>');
                        } else {

                            $('#chartDetailDiv').append('<p><samp></samp>This ' + time[0].toUpperCase() + time.slice(1) + ' Generated Energy(Online) <br><span> ' + data.total_today + '</span></p><p><samp class="samp_vt"></samp>Last ' + time[0].toUpperCase() + time.slice(1) + ' Generated Energy(Online) <br> <span class="one">' + data.total_yesterday + '</span></p><p><samp class="samp_vt"></samp>Total Generated Energy(Online) <br> <span class="one">' + data.plant_total_generation + '</span></p>');
                        }
                        $('#energyGenSpinner').hide();

                        dashboardEnergyGraph(time, data.today_time, data.plant_energy_graph, data.today_date, data.yesterday_date);
                    },
                    error: function (data) {
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
                    success: function (data) {
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
                    error: function (data) {
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
                        'type': 'hybrid'
                    },
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        $('#envPlantingDiv h2').remove();
                        $('#envReductionDiv h3').remove();
                        $('#envGenerationDiv').empty();

                        $('#envSpinner').hide();
                        $('#envPlantingDiv').append("<h2>" + (data[1] * 0.00131).toFixed(2) + "<samp>tree(s)</samp></h2>");
                        $('#envReductionDiv').append("<h3>" + (data[1] * 0.000646155).toFixed(2) + "<samp>T</samp></h3>");
                        $('#envGenerationDiv').append('<p><samp class="color07_one_vt"></samp> Total Generation: <span>' + data[0] + '</span></p>');
                    },
                    error: function (data) {
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
                        'type':'hybrid'
                    },

                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        $('#chartContainerGenDiv div').remove();
                        $('#chartDetailGenDiv').empty();

                        $('#chartContainerGenDiv').append('<div class="kWh_eng_vt_gen">kWh</div>');
                        $('#chartContainerGenDiv').append('<div id="chartContainerGen" style="height: 245px; width: 100%;"></div>');
                        $('#chartDetailGenDiv').append('<p><samp class="color01_one_vt"></samp> Actual: <span> ' + data.total_today + '</span></p><p><samp class="color02_one_vt"></samp> Expected: <span> ' + data.total_yesterday + '</span></p><p><samp class="color02_one_vt"></samp> Total: <span> ' + data.plant_total_generation + '</span></p>');
                        $('#expGenSpinner').hide();

                        plantGenGraph(time, data.today_time, data.plant_energy_graph, data.today_date, data.yesterday_date);
                    },
                    error: function (data) {
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
                        formatter: function (params) {

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
                        formatter: function (params) {

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
                        formatter: function (params) {

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
        });

        function changeOutagesDayMonthYear(date, filterss_arr, plant_name) {
            var d_m_y = '';

            $('#outages-served-dashboard').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            OutagesGraphAjax(date, d_m_y, filterss_arr, plant_name);
        }

        function changeCostSavingDayMonthYear(date, filterss_arr, plant_name) {
            var d_m_y = '';

            $('#cost-saving-graph-data').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            costSavingDataAjax(date, d_m_y, filterss_arr, plant_name);
        }

        function changeConsumptionPeakHoursDayMonthYear(date, filterss_arr, plant_name) {
            var d_m_y = '';

            $('#consumption-peak-hours').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });
            consumptionGraphAjax(date, d_m_y, filterss_arr, plant_name);
        }

        function consumptionGraphAjax(date, time, filter, plant_name) {
            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);

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
                url: "{{ route('admin.dashboard.consumption.peak.hours') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'filter': filters,
                    'plant_name': plantName
                },
                dataType: 'json',
                success: function (data) {
                    $('.consumptionPeakGraphSpinner').hide();
                    $('#containerpek').append('<div id="plantsConsumptionPeakHoursChart" style="height: 320px; width: 100%;"></div>');
                    $('#total-peak-hours-consumption').html(data.consumption + ' ' + 'kWh');

                    plantHistoryPeakHoursGraph(data);
                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function specificYieldGraphAjax(date, time, filter, plant_name) {
            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);
            $('#specific-yield-bar-graph').html('');
            $('.specificYieldGraphSpinner').show();

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.dashboard.specific.yield.graph') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'filter': filters,
                    'plant_name': plantName
                },
                dataType: 'json',
                success: function (data) {
                    $('.specificYieldGraphSpinner').hide();
                    $('#specific-yield-bar-graph').html('');
                    $('#specific-yield-bar-graph').append('<div id="plants-specific-yield-graph" style="height: 320px; width: 100%;"></div>');
                    $('#specific-yield-value').html('Specific Yield: ' + data.specific_yield_data + '  ' + 'Kwh/Kwp')

                    specificYieldGraph(data);
                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function maximumPowerAchievedGraphAjax(date, time, filter, plant_name, designedCapacity) {
            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);
            $('#maximum-power-achieved').html('');
            // $('.specificYieldGraphSpinner').show();

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.dashboard.maximum.power.achieved.graph') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'filter': filters,
                    'plant_name': plantName
                },
                dataType: 'json',
                success: function (data) {
                    $('#maximum-power-achieved').html('');
                    $('#maximum-power-achieved').append('<div id="maximum-power-achieved-graph-data" style="height: 400px; width: 100%;"></div>');
                    $('#max-power').html('Maximum Power Achieved: ' + data.data + ' ' + 'kW')
                    designedCapacity = data.designedCapacity;

                    maximumPowerAchievedGraph(data, designedCapacity);
                    // specificYieldGraph(data);
                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function solarEnergyUtilizationAjax(date, time, filter, plant_name) {
            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);
            $('#solar-energy-utilization-graph-detail').html('');
            // $('.specificYieldGraphSpinner').show();

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.dashboard.solar.energy.graph') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'filter': filters,
                    'plant_name': plantName
                },
                dataType: 'json',
                success: function (data) {
                    // $('.specificYieldGraphSpinner').hide();
                    // $('#specific-yield-bar-graph').html('');
                    $('#solar-energy-utilization-graph-detail').html('');
                    $('#solar-energy-utilization-graph-detail').append('<div id="plantsSolarEnergyUtilizationGraph" style="height: 320px; width: 100%;"></div>');
                    $('.batteryChargeValue').html(data.batteryChargeValue + ' ' + 'kWh');
                    $('.gridExportValue').html(data.gridExportValue + ' ' + 'kWh');
                    $('.loadValue').html(data.loadValue + ' ' + 'kWh');

                    solarEnergyUtilizationGraph(data);
                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function plantHistoryPeakHoursGraph(data) {
            // console.log(data);
            let dataArray = data.logData;
            let totalValue = data.total_value;
            let dom = document.getElementById("plantsConsumptionPeakHoursChart");
            let myChart = echarts.init(dom);
            let app = {};
            option = {
                tooltip: {
                    trigger: 'item',
                    position: ['40%', '20%'],
                    intersect: false,
                    formatter: '{b}'
                },
                legend: {
                    orient: 'horizontal',
                    // left: 30,
                    top: 200
                    // bottom:10
                },
                series: [
                    {
                        startAngle: 180,
                        endAngle: 360,
                        type: 'pie',
                        radius: ['90%', '65%'],
                        // center:['40%','60%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center'
                        },
                        labelLine: {
                            show: false
                        },
                        color: totalValue != 0 ? ['#5470c6', '#91cc75'] : ['#e0e0e0'],
                        data: totalValue != 0 ? dataArray : [
                            {
                                value: 1,
                                name: 'Battery Discharge 0 kWh',
                                tooltip: {
                                    show: false
                                }
                            },
                            {
                                value: 1,
                                name: 'Grid Import 0 kWh',
                                tooltip: {
                                    show: false
                                }
                            },
                            {
                                value: 2,
                                name: null,
                                itemStyle: {
                                    opacity: 0
                                },
                                tooltip: {
                                    show: false
                                }
                            }
                        ]
                    }
                ],
            }
            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
            console.log(myChart)
        }

        function specificYieldGraph(plantsHistoryGraphData) {
            // console.log(data);
            // let dataArray = data.logData;
            // let totalValue = data.total_value;
            var data = plantsHistoryGraphData.specific_history_graph;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var timeType = plantsHistoryGraphData.time_type;
            var legendArray = plantsHistoryGraphData.legend_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            let dom = document.getElementById("plants-specific-yield-graph");
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
                        for (let i = 0; i < p.length; i++) {
                            output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#f2b610;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value}  kWh/kWp</span>`;
                            if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                output += '<br/>'
                            }

                        }

                        return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + '-' + tooltipDate + '</span><br/><br/>' + output;
                    }
                },
                // legend: {
                //         data: legendArray,
                //         /*selected: {
                //     'Cost Saving': false,
                // },*/
                //         //bottom: '-15px',
                //     },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '17%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: true,
                    data: time,
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

        function maximumPowerAchievedGraph(plantsHistoryGraphData, designedCapacity) {
            var data = plantsHistoryGraphData.data;
            let dom = document.getElementById("maximum-power-achieved-graph-data");
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {
                series: [
                    {
                        type: 'gauge',
                        startAngle: 180,
                        endAngle: 0,
                        min: 0,
                        max: designedCapacity,
                        axisTick: {show: false},
                        splitLine: {show: false},
                        axisLine: {
                            lineStyle: {
                                show: false,
                                width: 70,
                                color: [
                                    [0.2, '#e82929'],
                                    [0.4, '#ff4500'],
                                    [0.6, '#FFA500'],
                                    [0.8, '#91cc75'],
                                    [1, '#427c27'],
                                ]
                            }
                        },
                        pointer: {
                            length: '100%',
                            width: 16,
                            icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
                            offsetCenter: [0, '5%'],
                            itemStyle: {
                                color: '#808080'
                            }
                        },
                        axisLabel: {
                            distance: 55,
                            fontSize: 20,
                            formatter: function (value) {
                                if (value === 0) {
                                    return '0';
                                } else if (value === designedCapacity) {
                                    return designedCapacity;
                                }
                                return '';
                            }
                        },
                        detail: {
                            formatter: function (value) {
                                return '{value|' + value.toFixed(0) + '}';
                            },
                            rich: {
                                value: {
                                    fontSize: 20,
                                    color: '#000000',
                                    padding: [-80, 0, 0, 0]
                                },
                            }
                        },
                        data: [
                            {
                                value: data
                            }
                        ]
                    }
                ],

            };
            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }


        function OutagesGraphAjax(date, time, filter, plant_name) {
            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);

            $('.outagesGraphSpinner').html('');
            $('.outagesSourcesGraphError').html('');
            $('#outages_hours').html('');


            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.dashboard.outages_grid_voltages') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'plant_name': plantName,
                    'filter': filters
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

        function costSavingDataAjax(date, time, filter, plant_name) {
            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);

            $('.costSavingGraphSpinner').show();
            $('#cost-saving-hours').empty();
            $('.costSavingGraphError').hide();
            $('#totalCostData').html('');

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.dashboard.cost_savings') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time': time,
                    'plant_name': plantName,
                    'filter': filters
                },
                dataType: 'json',
                success: function (data) {
                    $('#cost-saving-hours').empty();
                    $('.costSavingGraphSpinner').hide();
                    $('#cost-saving-hours').append('<div id="plant-saving-data-chart" style="height: 250px; width: 100%"></div>');
                    $('#totalCostData').html(data.totalSaving + ' ' + 'PKR');

                    costSavingDetailsGraph(data);
                },
                error: function (data) {

                    $('.costSavingGraphSpinner').hide();
                    $('.costSavingGraphError').show();
                }
            });
        }

        function costSavingDetailsGraph(data) {
            // console.log(data);
            let dataArray = data.logData;
            let dom = document.getElementById("plant-saving-data-chart");
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
                    top: '230px',
                    textStyle: {
                        fontSize: '11',
                    },
                    bottom: '1px'
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

        function solarEnergySourceGraphAjaxData(date, time, filter, plant_name) {
            filters = JSON.stringify(filter);
            plantName = JSON.stringify(plant_name);
            $('.energySourcesGraphSpinner').show();
            // $('#energyGenerationValue').html('');
            // $('#batteryChargingValue').html('');
            $('#energy-source-graph-container').html('');


            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();
            $.ajax({
                url: "{{ route('admin.dashboard.solar.energy.sources') }}",
                method: "GET",
                data: {
                    'filter': filters,
                    'plant_name': plantName,
                    'date': date,
                    'time': time,
                },
                dataType: 'json',
                success: function (data) {
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
                    $('.energySourcesGraphSpinner').hide();
                    $('#energy-source-graph-container').append('<div id="plantsEnergySourcesChart" style="height: 320px; width: 100%;"></div>');
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

        function solarEnergyUtilizationGraph(plantsHistoryGraphData) {
            // console.log(data);
            // let dataArray = data.logData;
            // let totalValue = data.total_value;
            var data = plantsHistoryGraphData.solar_energy_utilization_graph;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            let legendArray = plantsHistoryGraphData.legend_array;
            let dom = document.getElementById("plantsSolarEnergyUtilizationGraph");
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
                        for (let i = 0; i < p.length; i++) {
                            if (p[i].seriesName == 'Battery Charge') {
                                output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#5470c6;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                            } else if (p[i].seriesName == 'Grid Export') {
                                output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.PNG')}}"><span style="color:#fac858;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                            } else if (p[i].seriesName == 'Load') {
                                output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#91cc75;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                            }
                            if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                output += '<br/>'
                            }

                        }

                        return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + '-' + tooltipDate + '</span><br/><br/>' + output;
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
                    boundaryGap: true,
                    data: time,
                    axisLine: {
                        lineStyle: {
                            color: '#666666',
                        },
                        onZero: false,
                    },
                },
                // dataZoom: {
                //     type: "slider"
                // },
                yAxis: axisData,
                series: data
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

                google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
                    return function () {
                        infowindow.setContent(contentString[i]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            ;
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB12hWno8_DIMqw7xCV1QeqYn6I8FiIxVw&callback=initMap">
    </script>
@endsection
