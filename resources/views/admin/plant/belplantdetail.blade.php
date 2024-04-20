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
    </style>
    <?php

    $ac_output_total_power = 0;
    $str_limit = 2;

    ?>

    <div class="container-fluid px-xl-5 mt-3">
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
                        <h4 class="header_title_vt">All Inverters</h4>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>AC Output Total Power</th>
                                    <th>Daily Generation (Active)</th>
                                    <th>Monthly Generation</th>
                                    <th>Annual Generation</th>
                                    <th>Total Generation</th>
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
                                    <th>Work State</th>
                                    <th>Total DC Input Power</th>
                                    <th>Generation Yesterday</th>
                                    <th>Generation of Last Month</th>
                                    <th>Generation of Last Year</th>
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

                                            <?php
                                            $keys=0;
                                            ?>
                                            @foreach($inverters_array as $key => $inverter)

                                                @if($inverter->dv_inverter_serial_no != null || $inverter->dv_inverter_serial_no != '')
                                                    <?php
                                                    $keys=$keys+1;
                                                    ?>
                                                    <div class="carousel-item {{ $keys == 1 ? 'active' : '' }}" id="carousel_item_{{$inverter->dv_inverter_serial_no}}" data-serial_no="{{$inverter->dv_inverter_serial_no}}">


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
                                                                        <!-- <th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String1 </th> -->
                                                                        <!-- <th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String2 </th> -->
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

                                                                    <img src="{{ asset('assets/images/davice.jpg')}}" alt="" class="py-3">

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

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

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
            // alert(serial_no, date, time);
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
@endsection
