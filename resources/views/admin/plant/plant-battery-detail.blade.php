@extends('layouts.admin.master')

@section('title', 'Inverter Details')

@section('content')
    <style>
        .test {
            top: 50%;
            bottom: 50%;
            left: 50%;
            /*right: 0;*/
            /*margin: auto;*/
        }
        .table td, .table th {
            text-align: center;
        }
    </style>
    <?php

    $ac_output_total_power = 0;
    $str_limit = 2;

    ?>
    <!-- <div class="bred_area_vt">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title">
					<ol class="breadcrumb m-0 p-0">
						<li class="breadcrumb-item"><a href="{{ url('admin/Plants') }}">Plants</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('admin/user-plant-detail/'.$plant->id) }}">{{$plant->plant_name}}</a>
                            @if($plant->is_online == 'Y')
        <i class="check_pla_vt fas fa-check-circle" title="Online"></i></li>
@elseif($plant->is_online == 'P_Y')
        <i class="exclamation_pla_vt fas fa-exclamation-circle" title="Partially Online"></i></li>
@else
        <i class="cross_pla_vt fas fa-times-circle" title="Offline"></i></li>
@endif
        </li>
        <li class="breadcrumb-item active">Inverters</li>
    </ol>
</div>
</div>
</div>
</div>
</div> -->
    <div class="container-fluid px-xl-5" style="padding-bottom: 40rem;">
        <section class="py-2">
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
                <div class="col-lg-12 tabel_inv_vt">
                    <div class="card-box">
                        <h4 class="header_title_vt">Batery Details</h4>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>Battery Total Power</th>
                                    <th>Deily Charging Engery</th>
                                    <th>Deily Discharging Engery</th>
                                    <th>Total Discharging</th>
                                    <th>Total charging</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($plant)
                                    @foreach($plant->inverters as $key => $inverter)

                                        @php $ac_output_total_power = (double)$plant->inverters[$key]->ac_output_power + $ac_output_total_power; @endphp

                                    @endforeach
                                @endif
                                <tr>
                                    @if ($ac_output_total_power)
                                        @if((int)$ac_output_total_power >= pow(10,6) && (int)$ac_output_total_power < pow(10,9))
                                            <td>{{(int)($ac_output_total_power) / pow(10,6)}}GW</td>
                                        @elseif((int)$ac_output_total_power >= pow(10,3) && (int)$ac_output_total_power < pow(10,6))
                                            <td>{{(int)($ac_output_total_power) / pow(10,3)}}MW</td>
                                        @else
                                            <td>{{$ac_output_total_power}}kW</td>
                                        @endif
                                    @else
                                        <td>0kW</td>
                                    @endif
                                    <td>{{$total_daily_generation}}</td>
                                    <td>{{$total_monthly_generation}}</td>
                                    <td>{{$total_yearly_generation}}</td>
                                    <td>{{$total_generation_sum}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>Serial Number </th>
                                    <th>Dattery State</th>
                                    <th>Battery Type</th>
                                    <th>Battery Cycles</th>
                                    <th>Temperature</th>
                                    <th>Total Capacty</th>
                                    <th>Maximum Voltages</th>
                                    <th>Minimum Voltages</th>
                                    <th>Warning Status</th>
                                    <th>Failure State</th>
                                    <th>Remaining Capacity</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($inverter_previous_data)
                                    @foreach($inverter_previous_data as $key => $inverter)
                                        <tr>
                                            <th scope="row">{{$inverter->dv_inverter_serial_no != null ? $inverter->dv_inverter_serial_no : '00000000'}}</th>
                                            <td title="{{$inverter->workstate}}">{{Str::words($inverter->workstate, $str_limit, '...')}}</td>
                                            @if ($inverter->dc_power)
                                                @if((int)$inverter->dc_power >= pow(10,6) && (int)$inverter->dc_power < pow(10,9))
                                                    <td>{{(int)($inverter->dc_power) / pow(10,6)}}MW</td>
                                                @elseif((int)$inverter->dc_power >= pow(10,3) && (int)$inverter->dc_power < pow(10,6))
                                                    <td>{{(int)($inverter->dc_power) / pow(10,3)}}kW</td>
                                                @else
                                                    <td>{{$inverter->dc_power}}W</td>
                                                @endif
                                            @else
                                                <td>0W</td>
                                            @endif

                                            <td>{{$inverter->daily_generation}}</td>
                                            <td>{{$inverter->monthly_generation}}</td>
                                            <td class="on_off_vt">{{$inverter->yearly_generation}}</td>
                                            <td>daily generation</td>
                                            <td> Maximum Voltages</td>
                                            <td> Minimum Voltages	</td>
                                            <td> Warning Status	</td>
                                            <td> Remaining Capacity</td>
                                        </tr>

                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card-box -->
                </div> <!-- end col -->
            </div>

        </section>

        @if($plant->inverters)

            <?php
            $inverters_array = $plant->inverters;
            ?>

            @if(!($inverters_array->isEmpty()))

                <section>

                    <div class="row">

                        <div class="col-lg-12">

                            <div class="ibox-content power-daciec-area-vt">

                                <div id="carouselExampleControls" class="carousel slide" data-interval="false">

                                    <ol class="carousel-indicators">

                                        @if($inverters_array)
                                            @php
                                                $i=-1;
                                            @endphp
                                            @foreach($inverters_array as $key => $inverter)
                                                @if($inverter->dv_inverter_serial_no != null || $inverter->dv_inverter_serial_no != '')
                                                    @php
                                                        $i=$i+1;
                                                    @endphp
                                                    <li data-target="#carouselExampleControls disabled" data-index="{{$i}}" data-slide-to="{{ $inverter->dv_inverter_serial_no }}" class=" carousel_{{$inverter->dv_inverter_serial_no}} {{ $key == 0 ? 'active' : '' }}"></li>
                                                @endif
                                            @endforeach

                                        @endif

                                    </ol>

                                    <a class="left carousel-control" href="#carouselExampleControls" data-slide="prev">
                                        <img src="{{ asset('assets/images/left.svg') }}" alt="">
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="right carousel-control" href="#carouselExampleControls" data-slide="next">
                                        <img src="{{ asset('assets/images/right.svg') }}" alt="">
                                        <span class="sr-only">Next</span>
                                    </a>
                                    <div class="carousel-inner">

                                        @if($inverters_array)

                                            @foreach($inverters_array as $key => $inverter)

                                                @if($inverter->dv_inverter_serial_no != null || $inverter->dv_inverter_serial_no != '')

                                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" id="carousel_item_{{$inverter->dv_inverter_serial_no}}" data-serial_no="{{$inverter->dv_inverter_serial_no}}">


                                                        <div class="row border_one_vt">
                                                            <div class="col-lg-12">
                                                                <h4 class="header_title_vt"> Inverter Details ({{$inverter->dv_inverter_serial_no}})</h4>
                                                            </div>
                                                            <div class="col-lg-4 for_table_vt">

                                                                {{--<table id="demo-foo-row-toggler_{{$inverter->dv_inverter_serial_no}}" class="table table-bordered toggle-circle mb-0  footable tablet breakpoint">--}}
                                                                <table  class="table table-bordered mb-0 tablet">
                                                                    <thead class="thead-light">
                                                                    <tr>
                                                                        {{-- <th id="toggler_{{$inverter->dv_inverter_serial_no}}" class="footable-sortable " data-toggle="true"> DC </th> --}}
                                                                        <th> DC </th>
                                                                        <th> Voltage </th>
                                                                        <th> Current </th>
                                                                        <th> Power </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @php
                                                                        $inv_power1 = $inverter->l_voltage1 * $inverter->l_current1;
                                                                        $inv_power2 = $inverter->l_voltage2 * $inverter->l_current2;
                                                                        $inv_power3 = $inverter->l_voltage3 * $inverter->l_current3;
                                                                    @endphp
                                                                    <tr class="vt_this">

                                                                        <td>PV1</td>

                                                                        <td>{{ $inverter->l_voltage1 ? $inverter->l_voltage1 : 0 }} V</td>

                                                                        <td>{{ $inverter->l_current1 ? $inverter->l_current1 : 0 }} A</td>

                                                                        @if($inv_power1)
                                                                            @if((int)$inv_power1 >= pow(10,6) && (int)$inv_power1 < pow(10,9))
                                                                                <td>{{(int)($inv_power1) / pow(10,6)}}MW</td>
                                                                            @elseif((int)$inv_power1 >= pow(10,3) && (int)$inv_power1 < pow(10,6))
                                                                                <td>{{(int)($inv_power1) / pow(10,3)}}kW</td>
                                                                            @else
                                                                                <td>{{$inv_power1}}W</td>
                                                                            @endif
                                                                        @else
                                                                            <td>0W</td>
                                                                        @endif

                                                                    </tr>

                                                                    <tr class="">

                                                                        <td>PV2</td>

                                                                        <td>{{ $inverter->l_voltage2 ? $inverter->l_voltage2 : 0 }} V</td>

                                                                        <td>{{ $inverter->l_current2 ? $inverter->l_current2 : 0 }} A</td>

                                                                        @if($inv_power2)
                                                                            @if((int)$inv_power2 >= pow(10,6) && (int)$inv_power2 < pow(10,9))
                                                                                <td>{{(int)($inv_power2) / pow(10,6)}}MW</td>
                                                                            @elseif((int)$inv_power2 >= pow(10,3) && (int)$inv_power2 < pow(10,6))
                                                                                <td>{{(int)($inv_power2) / pow(10,3)}}kW</td>
                                                                            @else
                                                                                <td>{{$inv_power2}}W</td>
                                                                            @endif
                                                                        @else
                                                                            <td>0W</td>
                                                                        @endif

                                                                    </tr>

                                                                    <tr class="">

                                                                        <td>PV3</td>

                                                                        <td>{{ $inverter->l_voltage3 ? $inverter->l_voltage3 : 0 }} V</td>

                                                                        <td>{{ $inverter->l_current3 ? $inverter->l_current3 : 0 }} A</td>

                                                                        @if($inv_power3)
                                                                            @if((int)$inv_power3 >= pow(10,6) && (int)$inv_power3 < pow(10,9))
                                                                                <td>{{(int)($inv_power3) / pow(10,6)}}MW</td>
                                                                            @elseif((int)$inv_power3 >= pow(10,3) && (int)$inv_power3 < pow(10,6))
                                                                                <td>{{(int)($inv_power3) / pow(10,3)}}kW</td>
                                                                            @else
                                                                                <td>{{$inv_power3}}W</td>
                                                                            @endif
                                                                        @else
                                                                            <td>0W</td>
                                                                        @endif

                                                                    </tr>
                                                                    </tbody>
                                                                </table>


                                                            </div>

                                                            <div class="col-lg-4  text-center">

                                                                <table class="table card-text">
                                                                    <div class="img_center_vt"></div>

                                                                    <img src="{{ asset('assets/images/batteryimg.jpg')}}" alt="" class="py-3">

                                                                </table>

                                                            </div>

                                                            <div class="col-lg-4">

                                                                <table class="table card-text">

                                                                    <thead class="thead-light">

                                                                    <tr>

                                                                        <th>Phase</th>

                                                                        <th>Voltage</th>

                                                                        <th>Current</th>

                                                                        <th>Frequency</th>

                                                                    </tr>

                                                                    </thead>

                                                                    <tbody>

                                                                    <tr class="vt_this">

                                                                        <td>R</td>

                                                                        <td>{{ $inverter->r_voltage1 }} V</td>

                                                                        <td>{{ $inverter->r_current1 }} A</td>

                                                                        <td>{{ $inverter->frequency }} Hz</td>

                                                                    </tr>

                                                                    <tr class="">

                                                                        <td>S</td>

                                                                        <td>{{ $inverter->r_voltage2 }} V</td>

                                                                        <td>{{ $inverter->r_current2 }} A</td>

                                                                        <td>{{ $inverter->frequency }} Hz</td>

                                                                    </tr>

                                                                    <tr class="">

                                                                        <td>T</td>

                                                                        <td>{{ $inverter->r_voltage3 }} V</td>

                                                                        <td>{{ $inverter->r_current3 }} A</td>

                                                                        <td>{{ $inverter->frequency }} Hz</td>

                                                                    </tr>

                                                                    </tbody>

                                                                </table>

                                                            </div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-lg-1"></div>
                                                            <div class="col-lg-10 table-details-vt mt-3">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <h2 class="All-graph-heading-vt">History</h2>
                                                                        <div class="btn-companies-vt">
                                                                            <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                                                Export CSV
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box">
                                                                        <div class="test spinner-border text-primary m-2" role="status" style="display:none;">
                                                                            <span class="sr-only">Loading...</span>
                                                                        </div>

                                                                        <div class="energy_gener_vt">

                                                                            <div class="ch_one_vt" id="graphDiv_{{$inverter->dv_inverter_serial_no}}">
                                                                                <div class="ch_tr_vt"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_{{$inverter->dv_inverter_serial_no}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-1"></div>

                                                    </div>
                                                @endif
                                            @endforeach

                                        @else

                                            <div>Generation not found.</div>

                                        @endif

                                    </div>
                                </div>

                            </div>

                        </div>

                        @if($inverters_array)

                            <div class="col-lg-12">
                                <div class="day_month_calender_vt">
                                    <div class="day_month_year_vt" id="inverter_day_month_year_vt_day">
                                        <button><i id="inverterGraphPreviousDay" class="fa fa-caret-left"></i></button>
                                        <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-inverter mt10">
                                            <input type="text" autocomplete="off" name="inverterGraphDay" id="inverterGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                                        </div>
                                        <button><i id="inverterGraphForwardDay" class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_month_year_vt" id="inverter_day_month_year_vt_month">
                                        <button><i id="inverterGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                        <div class="mt40">
                                            <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-inverter mt10">
                                                <input type="text" autocomplete="off" name="inverterGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                            </div>
                                        </div>
                                        <button><i id="inverterGraphForwardMonth" class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_month_year_vt" id="inverter_day_month_year_vt_year">
                                        <button><i id="inverterGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                        <div class="mt40">
                                            <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-inverter mt10">
                                                <input type="text" autocomplete="off" name="inverterGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                            </div>
                                        </div>
                                        <button><i id="inverterGraphForwardYear" class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_my_btn_vt" id="inverter_day_my_btn_vt">
                                        <button class="day_bt_vt active" id="day">day</button>
                                        <button class="month_bt_vt" id="month">month</button>
                                        <button class="month_bt_vt" id="year">Year</button>
                                    </div>
                                </div>
                            </div>

                        @endif

                    </div>

                </section>

            @endif

        @else

            <section>

                <div class="row">

                    <div class="col-lg-12 mb-4">

                        <p>Inverter Record not found at this moment.</p>

                    </div>

                </div>

            </section>




        @endif

        <section>
        <div class="row card-box border_one_vt">
            <div class="col-lg-12">
                <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
            </div>

            <div class="col-lg-4 batter_img_border  text-center">
                <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                </table>
            </div>

            <div class="col-lg-8">
                <table class="table card-text">
                    <thead class="thead-light">
                    <tr>
                        <th> Battery </th>
                        <th> Voltage </th>
                        <th> Current </th>
                        <th> Power </th>
                    </tr>

                    </thead>

                    <tbody>

                    <tr class="vt_this">

                        <td>Pack 1</td>

                        <td>226.4 V</td>

                        <td>75.1 A</td>

                        <td>50.4 Hz</td>

                    </tr>

                    <tr class="">

                        <td>Pack 2</td>

                        <td>229.6 V</td>

                        <td>75.5 A</td>

                        <td>50.4 Hz</td>

                    </tr>

                    <tr class="">

                        <td>Pack 3</td>

                        <td>228 V</td>

                        <td>75.5 A</td>

                        <td>50.4 Hz</td>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>
            <!-- <div class="row">
                <div class="col-lg-12">
                    <div class="ibox-content power-daciec-area-vt">
                        <div id="carouselExampleControls" class="carousel slide" data-interval="false">
                            <ol class="carousel-indicators">

                                <li data-target="#carouselExampleControls disabled" data-index="0" data-slide-to="A1910263689" class=" carousel_A1910263689 active"></li>
                                <li data-target="#carouselExampleControls disabled" data-index="1" data-slide-to="A1910263689" class=" carousel_A1910263689 "></li>
                                <li data-target="#carouselExampleControls disabled" data-index="2" data-slide-to="A1910263689" class=" carousel_A1910263689 "></li>
                                <li data-target="#carouselExampleControls disabled" data-index="3" data-slide-to="A1910263689" class=" carousel_A1910263689 "></li>
                                <li data-target="#carouselExampleControls disabled" data-index="4" data-slide-to="A1910263689" class=" carousel_A1910263689 "></li>
                                <li data-target="#carouselExampleControls disabled" data-index="5" data-slide-to="A1910263689" class=" carousel_A1910263689 "></li>
                                <li data-target="#carouselExampleControls disabled" data-index="6" data-slide-to="A1910263689" class=" carousel_A1910263689 "></li>


                            </ol>
                            <a class="left carousel-control" href="#carouselExampleControls" data-slide="prev">
                                <img src="https://app.bel-energise.com/assets/images/left.svg" alt="">
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#carouselExampleControls" data-slide="next">
                                <img src="https://app.bel-energise.com/assets/images/right.svg" alt="">
                                <span class="sr-only">Next</span>
                            </a>
                            <div class="carousel-inner">
                                <div class="carousel-item active" id="carousel_item_A1910263689" data-serial_no="A1910263689">


                                    <div class="row border_one_vt">
                                        <div class="col-lg-12">
                                            <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
                                        </div>

                                        <div class="col-lg-4 batter_img_border  text-center">
                                            <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                                            </table>
                                        </div>

                                        <div class="col-lg-8">

                                            <table class="table card-text">
                                                <thead class="thead-light">
                                                <tr>
													<th> Battery </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>
												</tr>

                                                </thead>

                                                <tbody>

                                                <tr class="vt_this">

                                                    <td>R</td>

                                                    <td>226.4 V</td>

                                                    <td>75.1 A</td>

                                                    <td>50.4 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>S</td>

                                                    <td>229.6 V</td>

                                                    <td>75.5 A</td>

                                                    <td>50.4 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>T</td>

                                                    <td>228 V</td>

                                                    <td>75.5 A</td>

                                                    <td>50.4 Hz</td>

                                                </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10 table-details-vt mt-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="All-graph-heading-vt">History</h2>
                                                    <div class="btn-companies-vt">
                                                        <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                            Export CSV
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-box">
                                                    <div class="test spinner-border text-primary m-2" role="status" style="display: none;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>

                                                    <div class="energy_gener_vt">

                                                        <div class="ch_one_vt" id="graphDiv_A1910263689"><div class="ch_tr_vt"><span></span></div><div id="A1910263689" style="height: 200px; width: 100%;" data-today_log="0.3,1.8,2.5,7.5,13.3,18.1,32,41.5,50.3,75.2,81,101.9,122.5,151.5,178.6,191.9,212.7,235.7,287.9,314.8,332.9,344,357,371.9,399.7,417.5,423.1,429.6,447.3,457.6,468.2,491.7,495.4,508.8,515.6,521.1" data-today_log_time="05:50,06:13,06:20,06:49,07:11,07:24,07:53,08:06,08:22,08:54,09:02,09:25,09:44,10:11,10:35,10:45,11:02,11:23,11:54,12:13,12:35,12:49,13:03,13:20,13:48,14:15,14:26,14:44,15:09,15:28,15:41,16:13,16:23,16:49,17:11,17:36"><div class="canvasjs-chart-container" style="position: relative; text-align: left; cursor: auto; direction: ltr;"><canvas class="canvasjs-chart-canvas" width="1217" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="1217" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none;"></canvas><div class="canvasjs-chart-toolbar" style="position: absolute; right: 1px; top: 1px; border: 1px solid transparent;"><button state="menu" type="button" title="More Options" style="background-color: white; color: black; border: none; user-select: none; padding: 5px 12px; cursor: pointer; float: left; width: 40px; height: 25px; outline: 0px; vertical-align: baseline; line-height: 0;"><img style="height:95%; pointer-events: none;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAeCAYAAABE4bxTAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADoSURBVFhH7dc9CsJAFATgRxIIBCwCqZKATX5sbawsY2MvWOtF9AB6AU8gguAJbD2AnZ2VXQT/Ko2TYGCL2OYtYQc+BuYA+1hCtnCVwMm27SGaXpDJIAiCvCkVR05hGOZNN3HkFMdx3nQRR06+76/R1IcFLJlNQEWlmWlBTwJtKLKHynehZqnjOGM0PYWRVXk61C37p7xlZ3Hk5HneCk1dmMH811xGoKLSzDiQwIBZB4ocoPJdqNkDt2yKlueWRVGUtzy3rPwo3sWRU3nLjuLI6OO67oZM00wMw3hrmpZx0XU9syxrR0T0BeMpb9dneSR2AAAAAElFTkSuQmCC" alt="More Options"></button><div tabindex="-1" style="position: absolute; z-index: 1; user-select: none; cursor: pointer; right: 0px; top: 25px; min-width: 120px; outline: 0px; font-size: 14px; font-family: Arial, Helvetica, sans-serif; padding: 5px 0px; text-align: left; line-height: 10px; background-color: white; box-shadow: rgb(136, 136, 136) 2px 2px 10px; display: none;"><div style="padding: 12px 8px; background-color: white; color: black;">Print</div><div style="padding: 12px 8px; background-color: white; color: black;">Save as JPEG</div><div style="padding: 12px 8px; background-color: white; color: black;">Save as PNG</div></div></div><div class="canvasjs-chart-tooltip" style="position: absolute; height: auto; box-shadow: rgba(0, 0, 0, 0.1) 1px 1px 2px 2px; z-index: 1000; pointer-events: none; display: none; border-radius: 5px;"><div style=" width: auto;height: auto;min-width: 50px;line-height: auto;margin: 0px 0px 0px 0px;padding: 5px;font-family: Calibri, Arial, Georgia, serif;font-weight: normal;font-style: italic;font-size: 14px;color: #000000;text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);text-align: left;border: 2px solid gray;background: rgba(255,255,255,.9);text-indent: 0px;white-space: nowrap;border-radius: 5px;-moz-user-select:none;-khtml-user-select: none;-webkit-user-select: none;-ms-user-select: none;user-select: none;} "> Sample Tooltip</div></div><a class="canvasjs-chart-credit" title="JavaScript Charts" style="outline: none; margin: 0px; position: absolute; right: auto; top: 186px; color: dimgrey; text-decoration: none; font-size: 11px; font-family: Calibri, &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Arial, sans-serif;" tabindex="-1" target="_blank" href="https://canvasjs.com/">CanvasJS.com</a></div></div></div>
                                                    </div>
                                                </div>
                                                <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_A1910263689"><p><samp></samp> Daily Generation: <span>521.1 kWh</span></p></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1"></div>

                                </div>
                                <div class="carousel-item " id="carousel_item_A1910263689" data-serial_no="A1910263689">


                                    <div class="row border_one_vt">
                                        <div class="col-lg-12">
                                            <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
                                        </div>

                                        <div class="col-lg-4 batter_img_border  text-center">
                                            <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                                            </table>
                                        </div>

                                        <div class="col-lg-8">

                                            <table class="table card-text">

                                                <thead class="thead-light">

                                                <tr>
                                                    <th> Battery </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>
                                                </tr>

                                                </thead>

                                                <tbody>

                                                <tr class="vt_this">

                                                    <td>R</td>

                                                    <td>224 V</td>

                                                    <td>4.3 A</td>

                                                    <td>50 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>S</td>

                                                    <td>219.9 V</td>

                                                    <td>4.3 A</td>

                                                    <td>50 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>T</td>

                                                    <td>209.6 V</td>

                                                    <td>4.3 A</td>

                                                    <td>50 Hz</td>

                                                </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10 table-details-vt mt-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="All-graph-heading-vt">History</h2>
                                                    <div class="btn-companies-vt">
                                                        <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                            Export CSV
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-box">
                                                    <div class="test spinner-border text-primary m-2" role="status" style="display: none;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>

                                                    <div class="energy_gener_vt">

                                                        <div class="ch_one_vt" id="graphDiv_A1910263689"><div class="ch_tr_vt"><span></span></div><div id="A1910263689" style="height: 200px; width: 100%;" data-today_log="0.3,1.8,2.5,7.5,13.3,18.1,32,41.5,50.3,75.2,81,101.9,122.5,151.5,178.6,191.9,212.7,235.7,287.9,314.8,332.9,344,357,371.9,399.7,417.5,423.1,429.6,447.3,457.6,468.2,491.7,495.4,508.8,515.6,521.1" data-today_log_time="05:50,06:13,06:20,06:49,07:11,07:24,07:53,08:06,08:22,08:54,09:02,09:25,09:44,10:11,10:35,10:45,11:02,11:23,11:54,12:13,12:35,12:49,13:03,13:20,13:48,14:15,14:26,14:44,15:09,15:28,15:41,16:13,16:23,16:49,17:11,17:36"><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div></div>
                                                    </div>
                                                </div>
                                                <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_A1910263689"><p><samp></samp> Daily Generation: <span>521.1 kWh</span></p></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1"></div>

                                </div>
                                <div class="carousel-item " id="carousel_item_A1910263689" data-serial_no="A1910263689">


                                    <div class="row border_one_vt">
                                        <div class="col-lg-12">
                                            <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
                                        </div>
                                        <div class="col-lg-4 batter_img_border text-center">
                                            <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                                            </table>
                                        </div>

                                        <div class="col-lg-8">

                                            <table class="table card-text">

                                                <thead class="thead-light">

                                                <tr>
                                                    <th> Battery </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>
                                                </tr>

                                                </thead>

                                                <tbody>

                                                <tr class="vt_this">

                                                    <td>R</td>

                                                    <td>228.7 V</td>

                                                    <td>4.2 A</td>

                                                    <td> Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>S</td>

                                                    <td>228 V</td>

                                                    <td>4.3 A</td>

                                                    <td> Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>T</td>

                                                    <td>231.2 V</td>

                                                    <td>4.2 A</td>

                                                    <td> Hz</td>

                                                </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10 table-details-vt mt-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="All-graph-heading-vt">History</h2>
                                                    <div class="btn-companies-vt">
                                                        <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                            Export CSV
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-box">
                                                    <div class="test spinner-border text-primary m-2" role="status" style="display: none;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>

                                                    <div class="energy_gener_vt">

                                                        <div class="ch_one_vt" id="graphDiv_A1910263689"><div class="ch_tr_vt"><span></span></div><div id="A1910263689" style="height: 200px; width: 100%;" data-today_log="0.3,1.8,2.5,7.5,13.3,18.1,32,41.5,50.3,75.2,81,101.9,122.5,151.5,178.6,191.9,212.7,235.7,287.9,314.8,332.9,344,357,371.9,399.7,417.5,423.1,429.6,447.3,457.6,468.2,491.7,495.4,508.8,515.6,521.1" data-today_log_time="05:50,06:13,06:20,06:49,07:11,07:24,07:53,08:06,08:22,08:54,09:02,09:25,09:44,10:11,10:35,10:45,11:02,11:23,11:54,12:13,12:35,12:49,13:03,13:20,13:48,14:15,14:26,14:44,15:09,15:28,15:41,16:13,16:23,16:49,17:11,17:36"><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div></div>
                                                    </div>
                                                </div>
                                                <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_A1910263689"><p><samp></samp> Daily Generation: <span>521.1 kWh</span></p></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1"></div>

                                </div>
                                <div class="carousel-item " id="carousel_item_A1910263689" data-serial_no="A1910263689">


                                    <div class="row border_one_vt">
                                        <div class="col-lg-12">
                                            <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
                                        </div>

                                        <div class="col-lg-4 batter_img_border text-center">
                                            <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                                            </table>
                                        </div>

                                        <div class="col-lg-8">

                                            <table class="table card-text">

                                                <thead class="thead-light">

                                                <tr>
                                                    <th> Battery </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>
                                                </tr>

                                                </thead>

                                                <tbody>

                                                <tr class="vt_this">

                                                    <td>R</td>

                                                    <td>238.4 V</td>

                                                    <td>13.4 A</td>

                                                    <td>50.4 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>S</td>

                                                    <td>236 V</td>

                                                    <td>13.5 A</td>

                                                    <td>50.4 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>T</td>

                                                    <td>238.4 V</td>

                                                    <td>13.4 A</td>

                                                    <td>50.4 Hz</td>

                                                </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10 table-details-vt mt-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="All-graph-heading-vt">History</h2>
                                                    <div class="btn-companies-vt">
                                                        <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                            Export CSV
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-box">
                                                    <div class="test spinner-border text-primary m-2" role="status" style="display: none;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>

                                                    <div class="energy_gener_vt">

                                                        <div class="ch_one_vt" id="graphDiv_A1910263689"><div class="ch_tr_vt"><span></span></div><div id="A1910263689" style="height: 200px; width: 100%;" data-today_log="0.3,1.8,2.5,7.5,13.3,18.1,32,41.5,50.3,75.2,81,101.9,122.5,151.5,178.6,191.9,212.7,235.7,287.9,314.8,332.9,344,357,371.9,399.7,417.5,423.1,429.6,447.3,457.6,468.2,491.7,495.4,508.8,515.6,521.1" data-today_log_time="05:50,06:13,06:20,06:49,07:11,07:24,07:53,08:06,08:22,08:54,09:02,09:25,09:44,10:11,10:35,10:45,11:02,11:23,11:54,12:13,12:35,12:49,13:03,13:20,13:48,14:15,14:26,14:44,15:09,15:28,15:41,16:13,16:23,16:49,17:11,17:36"><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div></div>
                                                    </div>
                                                </div>
                                                <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_A1910263689"><p><samp></samp> Daily Generation: <span>521.1 kWh</span></p></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1"></div>

                                </div>
                                <div class="carousel-item " id="carousel_item_A1910263689" data-serial_no="A1910263689">


                                    <div class="row border_one_vt">
                                        <div class="col-lg-12">
                                            <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
                                        </div>
                                        <div class="col-lg-4 batter_img_border text-center">
                                            <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                                            </table>
                                        </div>

                                        <div class="col-lg-8">

                                            <table class="table card-text">

                                                <thead class="thead-light">

                                                <tr>
                                                    <th> Battery </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>

                                                </tr>

                                                </thead>

                                                <tbody>

                                                <tr class="vt_this">

                                                    <td>R</td>

                                                    <td>237.6 V</td>

                                                    <td>0.3 A</td>

                                                    <td>50.1 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>S</td>

                                                    <td>236.8 V</td>

                                                    <td>0.3 A</td>

                                                    <td>50.1 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>T</td>

                                                    <td>239.2 V</td>

                                                    <td>0.3 A</td>

                                                    <td>50.1 Hz</td>

                                                </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10 table-details-vt mt-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="All-graph-heading-vt">History</h2>
                                                    <div class="btn-companies-vt">
                                                        <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                            Export CSV
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-box">
                                                    <div class="test spinner-border text-primary m-2" role="status" style="display: none;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>

                                                    <div class="energy_gener_vt">

                                                        <div class="ch_one_vt" id="graphDiv_A1910263689"><div class="ch_tr_vt"><span></span></div><div id="A1910263689" style="height: 200px; width: 100%;" data-today_log="0.3,1.8,2.5,7.5,13.3,18.1,32,41.5,50.3,75.2,81,101.9,122.5,151.5,178.6,191.9,212.7,235.7,287.9,314.8,332.9,344,357,371.9,399.7,417.5,423.1,429.6,447.3,457.6,468.2,491.7,495.4,508.8,515.6,521.1" data-today_log_time="05:50,06:13,06:20,06:49,07:11,07:24,07:53,08:06,08:22,08:54,09:02,09:25,09:44,10:11,10:35,10:45,11:02,11:23,11:54,12:13,12:35,12:49,13:03,13:20,13:48,14:15,14:26,14:44,15:09,15:28,15:41,16:13,16:23,16:49,17:11,17:36"><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div></div>
                                                    </div>
                                                </div>
                                                <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_A1910263689"><p><samp></samp> Daily Generation: <span>521.1 kWh</span></p></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1"></div>

                                </div>
                                <div class="carousel-item " id="carousel_item_A1910263689" data-serial_no="A1910263689">


                                    <div class="row border_one_vt">
                                        <div class="col-lg-12">
                                            <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
                                        </div>

                                        <div class="col-lg-4 batter_img_border text-center">
                                            <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                                            </table>
                                        </div>

                                        <div class="col-lg-8">

                                            <table class="table card-text">

                                                <thead class="thead-light">

                                                <tr>

                                                    <th> Battery </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>

                                                </tr>

                                                </thead>

                                                <tbody>

                                                <tr class="vt_this">

                                                    <td>R</td>

                                                    <td>221.1 V</td>

                                                    <td>18.8 A</td>

                                                    <td>49.9 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>S</td>

                                                    <td>221.7 V</td>

                                                    <td>18.9 A</td>

                                                    <td>49.9 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>T</td>

                                                    <td>221.4 V</td>

                                                    <td>18.8 A</td>

                                                    <td>49.9 Hz</td>

                                                </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10 table-details-vt mt-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="All-graph-heading-vt">History</h2>
                                                    <div class="btn-companies-vt">
                                                        <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                            Export CSV
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-box">
                                                    <div class="test spinner-border text-primary m-2" role="status" style="display: none;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>

                                                    <div class="energy_gener_vt">

                                                        <div class="ch_one_vt" id="graphDiv_A1910263689"><div class="ch_tr_vt"><span></span></div><div id="A1910263689" style="height: 200px; width: 100%;" data-today_log="0.3,1.8,2.5,7.5,13.3,18.1,32,41.5,50.3,75.2,81,101.9,122.5,151.5,178.6,191.9,212.7,235.7,287.9,314.8,332.9,344,357,371.9,399.7,417.5,423.1,429.6,447.3,457.6,468.2,491.7,495.4,508.8,515.6,521.1" data-today_log_time="05:50,06:13,06:20,06:49,07:11,07:24,07:53,08:06,08:22,08:54,09:02,09:25,09:44,10:11,10:35,10:45,11:02,11:23,11:54,12:13,12:35,12:49,13:03,13:20,13:48,14:15,14:26,14:44,15:09,15:28,15:41,16:13,16:23,16:49,17:11,17:36"><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div></div>
                                                    </div>
                                                </div>
                                                <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_A1910263689"><p><samp></samp> Daily Generation: <span>521.1 kWh</span></p></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1"></div>

                                </div>
                                <div class="carousel-item " id="carousel_item_A1910263689" data-serial_no="A1910263689">


                                    <div class="row border_one_vt">
                                        <div class="col-lg-12">
                                            <h4 class="header_title_vt"> Battery Details (A1910263689)</h4>
                                        </div>

                                        <div class="col-lg-4 batter_img_border text-center">
                                            <div class="img_center_vt"></div><img src="{{ asset('assets/images/battery123.png')}}" alt="" class="py-3"><table class="table card-text">
                                            </table>
                                        </div>

                                        <div class="col-lg-8">

                                            <table class="table card-text">

                                                <thead class="thead-light">

                                                <tr>

                                                    <th> Battery </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>

                                                </tr>

                                                </thead>

                                                <tbody>

                                                <tr class="vt_this">

                                                    <td>R</td>

                                                    <td>245.6 V</td>

                                                    <td>14.3 A</td>

                                                    <td>50.2 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>S</td>

                                                    <td>244 V</td>

                                                    <td>14.3 A</td>

                                                    <td>50.2 Hz</td>

                                                </tr>

                                                <tr class="">

                                                    <td>T</td>

                                                    <td>245.6 V</td>

                                                    <td>14.3 A</td>

                                                    <td>50.2 Hz</td>

                                                </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10 table-details-vt mt-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="All-graph-heading-vt">History</h2>
                                                    <div class="btn-companies-vt">
                                                        <button type="button" class="btn-add-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                                                            Export CSV
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-box">
                                                    <div class="test spinner-border text-primary m-2" role="status" style="display: none;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>

                                                    <div class="energy_gener_vt">

                                                        <div class="ch_one_vt" id="graphDiv_A1910263689"><div class="ch_tr_vt"><span></span></div><div id="A1910263689" style="height: 200px; width: 100%;" data-today_log="0.3,1.8,2.5,7.5,13.3,18.1,32,41.5,50.3,75.2,81,101.9,122.5,151.5,178.6,191.9,212.7,235.7,287.9,314.8,332.9,344,357,371.9,399.7,417.5,423.1,429.6,447.3,457.6,468.2,491.7,495.4,508.8,515.6,521.1" data-today_log_time="05:50,06:13,06:20,06:49,07:11,07:24,07:53,08:06,08:22,08:54,09:02,09:25,09:44,10:11,10:35,10:45,11:02,11:23,11:54,12:13,12:35,12:49,13:03,13:20,13:48,14:15,14:26,14:44,15:09,15:28,15:41,16:13,16:23,16:49,17:11,17:36"><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div></div>
                                                    </div>
                                                </div>
                                                <div class="generation-overview-vt" style="margin-top:5px;" id="generationID_A1910263689"><p><samp></samp> Daily Generation: <span>521.1 kWh</span></p></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12">
                    <div class="day_month_calender_vt">
                        <div class="day_month_year_vt" id="inverter_day_month_year_vt_day">
                            <button><i id="inverterGraphPreviousDay" class="fa fa-caret-left"></i></button>
                            <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-inverter mt10">
                                <input type="text" autocomplete="off" name="inverterGraphDay" id="inverterGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button><i id="inverterGraphForwardDay" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="inverter_day_month_year_vt_month" style="display: none;">
                            <button><i id="inverterGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-inverter mt10">
                                    <input type="text" autocomplete="off" name="inverterGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="inverterGraphForwardMonth" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="inverter_day_month_year_vt_year" style="display: none;">
                            <button><i id="inverterGraphPreviousYear" class="fa fa-caret-left"></i></button>
                            <div class="mt40">
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-inverter mt10">
                                    <input type="text" autocomplete="off" name="inverterGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button><i id="inverterGraphForwardYear" class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_my_btn_vt" id="inverter_day_my_btn_vt">
                            <button class="day_bt_vt active" id="day">day</button>
                            <button class="month_bt_vt" id="month">month</button>
                            <button class="month_bt_vt" id="year">Year</button>
                        </div>
                    </div>
                </div>


            </div> -->
        </section>


    </div>

    <script src="{{ asset('assets/js/jquery-3.5.1.min.js')}}" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script type="text/javascript">
        window.onload = function() {

            var serial_no = $('.carousel-indicators li').data('slide-to');

            var totalItems = $('.carousel-item').length;
            var currentIndex = $('div.active').index();

            var currDate = getCurrentDate();

            $('input[name="inverterGraphDay"]').val(currDate.todayDate);
            $('input[name="inverterGraphMonth"]').val(currDate.todayMonth);
            $('input[name="inverterGraphYear"]').val(currDate.todayYear);

            var inverter_date = $('input[name="inverterGraphDay"]').val();
            var inverter_time = 'day';

            changeInverterDayMonthYear(serial_no, inverter_date, inverter_time);
            append_graph(serial_no, inverter_date, inverter_time);

            $('.J-yearMonthDayPicker-single-inverter').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function(type) {
                    changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-inverter').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function(type) {
                    console.log(this.$input.eq(0).val());
                    changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-inverter').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function(type) {
                    changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'year');
                }
            });

            $('#inverterGraphPreviousDay').on('click', function() {

                show_date = $("input[name='inverterGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                inverter_date = formatDate(datess);
                $('input[name="inverterGraphDay"]').val('');
                $('input[name="inverterGraphDay"]').val(inverter_date);
                console.log($("input[name='inverterGraphDay']").val());
                inverter_time = 'day';
                append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphForwardDay').on('click', function() {

                show_date = $("input[name='inverterGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                inverter_date = formatDate(datess);
                $('input[name="inverterGraphDay"]').val('');
                $('input[name="inverterGraphDay"]').val(inverter_date);
                console.log($("input[name='inverterGraphDay']").val());
                inverter_time = 'day';
                append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphPreviousMonth').on('click', function() {

                show_date = $("input[name='inverterGraphMonth']").val();
                inverter_date = formatPreviousMonth(show_date);
                $('input[name="inverterGraphMonth"]').val('');
                $('input[name="inverterGraphMonth"]').val(inverter_date);
                console.log($("input[name='inverterGraphMonth']").val());
                inverter_time = 'month';
                append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphForwardMonth').on('click', function() {

                show_date = $("input[name='inverterGraphMonth']").val();
                inverter_date = formatForwardMonth(show_date);
                $('input[name="inverterGraphMonth"]').val('');
                $('input[name="inverterGraphMonth"]').val(inverter_date);
                console.log($("input[name='inverterGraphMonth']").val());
                inverter_time = 'month';
                append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphPreviousYear').on('click', function() {

                show_date = $("input[name='inverterGraphYear']").val();
                inverter_date = formatPreviousYear(show_date);
                $('input[name="inverterGraphYear"]').val('');
                $('input[name="inverterGraphYear"]').val(inverter_date);
                console.log($("input[name='inverterGraphYear']").val());
                inverter_time = 'year';
                append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphForwardYear').on('click', function() {

                show_date = $("input[name='inverterGraphYear']").val();
                inverter_date = formatForwardYear(show_date);
                $('input[name="inverterGraphYear"]').val('');
                $('input[name="inverterGraphYear"]').val(inverter_date);
                console.log($("input[name='inverterGraphYear']").val());
                inverter_time = 'year';
                append_graph(serial_no, inverter_date, inverter_time);
            });

            $("#inverter_day_my_btn_vt button").click(function() {

                $('#inverter_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeInverterDayMonthYear(serial_no, inverter_date, inverter_time);

            });

            // next click event --
            $('.right').on('click', function(e) {
                // getting total length and index values of list
                var totalItems = $('.carousel-item').length;
                var currentIndex = $('div.active').index();

                // increment current index value to 1 , nd if it reaches lats index then point to 0 index
                if (currentIndex == (totalItems - 1)) {
                    currentIndex = 0;
                } else {
                    currentIndex = currentIndex + 1;
                }

                // fetch serial no. and then append graph
                serial_no = $(".carousel-indicators > li[data-index='" + currentIndex + "']").attr('data-slide-to');

                changeInverterDayMonthYear(serial_no, inverter_date, inverter_time);

            });

            // previous click event --
            $('.left').on('click', function(e) {
                // getting total length and index values of list
                var totalItems = $('.carousel-item').length;
                var currentIndex = $('div.active').index();

                // decrement current index value to 1 , nd if it reaches first index then point to last index
                if (currentIndex == 0) {
                    currentIndex = totalItems - 1;
                } else {
                    currentIndex = currentIndex - 1;
                }

                // fetch serial no. and then append graph
                serial_no = $(".carousel-indicators > li[data-index='" + currentIndex + "']").attr('data-slide-to');

                changeInverterDayMonthYear(serial_no, inverter_date, inverter_time);
            });
        }

        function changeInverterDayMonthYear(serial_no, date, time) {

            var d_m_y = '';

            $('#inverter_day_my_btn_vt').children('button').each(function () {
                if($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#inverter_day_month_year_vt_year').hide();
                $('#inverter_day_month_year_vt_month').hide();
                $('#inverter_day_month_year_vt_day').show();
                date = $('input[name="inverterGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#inverter_day_month_year_vt_year').hide();
                $('#inverter_day_month_year_vt_day').hide();
                $('#inverter_day_month_year_vt_month').show();
                date = $('input[name="inverterGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#inverter_day_month_year_vt_day').hide();
                $('#inverter_day_month_year_vt_month').hide();
                $('#inverter_day_month_year_vt_year').show();
                date = $('input[name="inverterGraphYear"]').val();
                time = 'year';
            }

            append_graph(serial_no, date, time);
        }

        function append_graph(serial_no, date, time) {
            // console.log(serial_no);
            $graph_div = 'div#graphDiv_' + serial_no;
            $($graph_div).empty();
            $id_div = 'div#generationID_' + serial_no;
            $($id_div).empty();
            $('.test').show();
            $.ajax({
                type: 'get',
                url: "{{ URL('admin/plant-inverter-graphs') }}" + "/" + serial_no + "/" + time + "/" + date,
                success: function(res) {
                    console.log(res);

                    if(time == 'day') {

                        if (res["today_energy_generation"]) {
                            $($id_div).empty();
                            $($graph_div).empty();

                            $($id_div).append('<p><samp></samp> Daily Generation: <span>' + res["today_energy_gene"] + '</span></p>');
                            $($graph_div).append('<div class="ch_tr_vt"><span></span></div><div id="' + serial_no + '" style="height: 200px; width: 100%;" data-today_log=' + res["today_generation"] + ' data-today_log_time=' + res["today_time"] + '><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div>');
                            graph(serial_no, res["max_energy_generation"]);
                        }
                        else {
                            $('.test').hide();
                            $($graph_div).append('No record Found!');
                        }
                    }

                    else if(time == 'month' || time == 'year') {

                        if (res.today_log_data) {

                            $($graph_div).empty();
                            $($id_div).empty();

                            if(time == 'month') {

                                $($id_div).append('<p><samp></samp> Monthly Generation: <span>' + res.today_energy_gene + '</span></p>');
                                $($graph_div).append('<div class="ch_tr_vt"><span></span></div><div id="' + serial_no + '" style="height: 200px; width: 100%;" data-today_log=' + res["today_generation"] + ' data-today_log_time=' + res["today_time"] + '><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div>');
                            }

                            else if(time == 'year') {

                                $($id_div).append('<p><samp></samp> Yearly Generation: <span>' + res.today_energy_gene + '</span></p>');
                                $($graph_div).append('<div class="ch_tr_years_vt"><span></span></div><div id="' + serial_no + '" style="height: 200px; width: 100%;" data-today_log=' + res["today_generation"] + ' data-today_log_time=' + res["today_time"] + '><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas></div>');
                            }

                            graph_mon_year(serial_no, res.today_log_data, time, date);
                        }
                        else {
                            $('.test').hide();
                            $($graph_div).append('No record Found!');
                        }
                    }
                },
                error: function(res) {
                    console.log('Failed');
                    $('.test').hide();
                    $($graph_div).append('No record Found!');
                }
            });
        }

        function graph(serial_no, max_generation) {
            console.log("Hello" + serial_no);

            var today_log = $('#' + serial_no).attr('data-today_log').split(',');
            var today_time = $('#' + serial_no).attr('data-today_log_time').split(',');
            var today = [];
            var intervalCount = 3;

            var max_gen = max_generation;

            max_gen = Math.ceil((max_gen / 5));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10,number_format)) * Math.pow(10,number_format);

            if(today_time.length > 13) {

                var intervalVal = Math.ceil((today_time.length / 10));
                intervalCount += intervalVal + 3;
            }

            for (var i = 0; i < today_log.length; i++) {
                today[i] = {
                    label: today_time[i],
                    y: parseFloat(today_log[i])
                };
            }

            var date = '<?php echo date('d-m-Y') ?>';

            var options = {
                exportEnabled: true,
                animationEnabled: true,
                axisX: {
                    interval: intervalCount,
                },
                axisY: {
                    gridThickness: 0.15,
                    interval: max_gen,
                    margin: 40
                },
                data: [{
                    toolTipContent: date + " {label}<br/> Today Generation: {y} kWh",
                    markerType: "none",
                    type: "line",
                    dataPoints: today,
                }]
            };
            $('.test').hide();
            $('#' + serial_no).CanvasJSChart(options);

        }

        function graph_mon_year(serial_no, curr_generation, time, date) {

            var max_gen = Math.max.apply(Math, curr_generation.map(function(o) { return o.y; }));

            max_gen = Math.ceil((max_gen / 5));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10,number_format)) * Math.pow(10,number_format);

            if(time == 'year') {

                var options = {

                    axisX: {
                        interval: 1,
                    },

                    axisY: {
                        interval: max_gen,
                        margin: 50,
                        gridThickness: 0.15,
                    },

                    data: [{
                        toolTipContent: "{tooltip} "+date+"<br/>Monthly Generation: {y} kWh",
                        markerType: "none",
                        type: "column",
                        color: "#0F75BC",
                        dataPoints: curr_generation
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
                        margin: 50,
                        gridThickness: 0.15,
                    },

                    data: [{
                        toolTipContent: "{x}-"+dateArr[1]+"-"+dateArr[0]+"<br/> Daily Generation: {y} kWh",
                        markerType: "none",
                        type: "column",
                        color: "#0F75BC",
                        dataPoints: curr_generation
                    }
                    ]
                };
            }

            $('.test').hide();
            var chart = new CanvasJS.Chart(String(serial_no), options);
            chart.render();
        }

        /*$(window).on("load", function() {
            append_footable();
        });*/

        // append footable
        function append_footable() {

            @if($inverters_array)

            @foreach($inverters_array as $key => $inverter)
            // alert('hy');

            $("#demo-foo-row-toggler_{{$inverter->dv_inverter_serial_no}}").footable(), $("#demo-foo-accordion").footable().on("footable_row_expanded", function(o) {
                $("#demo-foo-accordion tbody tr.footable-detail-show").not(o.row).each(function() {
                    $("#demo-foo-accordion").data("footable").toggleDetail(this)
                })
            }), $("#demo-foo-pagination").footable(), $("#demo-show-entries").change(function(o) {
                o.preventDefault();
                var t = $(this).val();
                $("#demo-foo-pagination").data("page-size", t), $("#demo-foo-pagination").trigger("footable_initialized")
            });
            var t = $("#demo-foo-filtering");
            t.footable().on("footable_filtering", function(o) {
                var t = $("#demo-foo-filter-status").find(":selected").val();
                o.filter += o.filter && 0 < o.filter.length ? " " + t : t, o.clear = !o.filter
            }), $("#demo-foo-filter-status").change(function(o) {
                o.preventDefault(), t.trigger("footable_filter", {
                    filter: $(this).val()
                })
            }), $("#demo-foo-search").on("input", function(o) {
                o.preventDefault(), t.trigger("footable_filter", {
                    filter: $(this).val()
                })
            });
            var e = $("#demo-foo-addrow");
            e.footable().on("click", ".demo-delete-row", function() {
                var o = e.data("footable"),
                    t = $(this).parents("tr:first");
                o.removeRow(t)
            }), $("#demo-input-search2").on("input", function(o) {
                o.preventDefault(), e.trigger("footable_filter", {
                    filter: $(this).val()
                })
            }), $("#demo-btn-addrow").click(function() {
                e.data("footable").appendRow('<tr><td style="text-align: center;"><button class="demo-delete-row btn btn-danger btn-xs btn-icon"><i class="fa fa-times"></i></button></td><td>Adam</td><td>Doe</td><td>Traffic Court Referee</td><td>22 Jun 1972</td><td><span class="badge label-table badge-success   ">Active</span></td></tr>')
            })

            @endforeach
            @endif

        }

        // // when clicked on carousel list
        // $(document).on("click", ".carousel-indicators li", function() {
        // 	var serial_no = $(this).data('slide-to');
        // 	var totalItems = $('.carousel-item').length;
        // 	var currentIndex = $('div.active').index();
        //
        // 	$( this ).parent().find( 'li.active' ).removeClass( 'active' );
        // 	$( this ).addClass( 'active' );
        // 	// $('.carousel-indicators li').removeClass('active');
        // 	// $active_class='.carousel_'+serial_no;
        // 	// $($active_class).addClass('active');
        // 	// $('.carousel-item').removeClass('active');
        // 	// $active_class1='#carousel_item_'+serial_no;
        // 	// $($active_class1).addClass('active');
        // 	// //
        // 	// console.log($active_class1);
        // 	// console.log($active_class);
        // 	// console.log(serial_no);
        // 	// append_graph(serial_no);
        // 	//
        // 	// $('.carousel-indicators li').removeClass('active');
        // 	// $active_class='.carousel_'+serial_no;
        // 	// $($active_class).addClass('active');
        // 	// $('.carousel-item').removeClass('active');
        // 	// $active_class1='#carousel_item_'+serial_no;
        // 	// $($active_class1).addClass('active');
        //
        // });
    </script>

    <script>
        $(document).ready(function() {
            $('.add_mar_vt').on('click', function() {
                $('.vt_this').toggleClass("highlight");
                // console.log('hello');
            });
        });
    </script>
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js')}}" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
@endsection
