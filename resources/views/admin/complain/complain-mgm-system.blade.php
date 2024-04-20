@extends('layouts.admin.master')
@section('title', 'Ticket Dashboard')
@section('content')

    <style>
        .tickets_drop_vt {
            width: 110px;
            float: right;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #504E4E;
            line-height: 24px !important;
            font-weight: 300 !important;
            font-size: 12px;
            border: none !important;
        }

        .select2-container .select2-selection--single {
            border: none !important;
            height: calc(1.5em + .9rem + 2px);
            background-color: #fff;
            box-shadow: 0 1px 4px 0 rgb(0 0 0 / 0%) !important;
            outline: 0;
        }

        .dataTables_filter {
            display: none !important;
        }

        select {
            background-position-y: 6px !important;
        }

        .day_month_year_vt {
            min-height: 50px;
        }

        element.style {
        }

        .c-datepicker-single-editor .c-datepicker-data-input {
            text-align: left;
        }

        .c-datepicker-data-input {
            margin: 12px 0 0 0;
        }

        table.dataTable.stripe tbody tr.odd,
        table.dataTable.display tbody tr.odd {
            background-color: #ffffff;
        }

        .table .thead-light th {
            padding: 0 0 0 10px !important;
            text-align: left !important;
        }

        .card_hi_vt {
            height: 510px;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 0 !important;
            margin-right: 15px !important;
            margin-bottom: 15px !important;
            background-color: #fff;
            border: 1px solid #dee2e6;
            position: absolute !important;
            bottom: 0 !important;
            right: 0 !important;
        }

        .All-graph-heading-vt {
            text-transform: inherit !important;
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
            margin-bottom: 10px;
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
        table.dataTable tbody th, table.dataTable tbody td{
            text-align: left !important;
        }
        /* .align_vt td{
            text-align: left !important;
        } */
        .graph_btn_link_vt{
            justify-content: center;
            margin-top: 15px;
        }

        @media screen and (max-width:992px) {
            .table .thead-light th{
                text-align: left !important;
                padding-left: 10px !important;
            }
        }
        .priorityContainer_vtt{
            height: 320px;
        }
        @media screen and (max-width:1150px){
            .priorityContainer_vtt{
                height: 280px;
            }
        }
    </style>

    <div class="bred_area_vt">
        <div class="row">
            <div class="col-xl-12">
                <div class="home-companies-area-vt">
                    <form id="filtersForm" class="home-companise_dash-vt"
                          action="{{route('admin.complain.mgm.system')}}" method="GET">
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
                        <div class="btn-companiescl-vt" id="searchButtonDiv">
                            <button type="submit" class="btn_se_cle_vt" id="searchFilters">
                                <img src="{{ asset('assets/images/search_01.svg')}}" alt="search">
                            </button>
                            <button type="button" class="btn_se_cle_vt" id="clearFilters12">
                                <img src="{{ asset('assets/images/cle_02.svg')}}" alt="clear">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="container-fluid px-xl-3">
        <div class="row">
            <div class="col-lg-6 mb-3 mt-3">
                <div class="card" style="height: 510px;">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Ticket Priority</h2>
                    </div>
                    <div class="day_month_year_vt" id="priority_day_month_year_vt_day">
                        <button><i id="priorityGraphPreviousDay" class="fa fa-caret-left"></i></button>
                        <div
                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-priority mt10">
                            <input type="text" autocomplete="off" name="priorityGraphDay" id="priorityGraphDay"
                                   placeholder="Select" class="c-datepicker-data-input" value="">
                        </div>
                        <button><i id="priorityGraphForwardDay" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="priority_day_month_year_vt_month">
                        <button><i id="priorityGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-priority mt10">
                                <input type="text" autocomplete="off" name="priorityGraphMonth" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="priorityGraphForwardMonth" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="priority_day_month_year_vt_year">
                        <button><i id="priorityGraphPreviousYear" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-priority mt10">
                                <input type="text" autocomplete="off" name="priorityGraphYear" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
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
            <div class="col-lg-6 mt-3">
                <div class="card card_hi_vt">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Tickets by Category</h2>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable_5" class="display table table-borderless table-centered table-nowrap"
                               style="width:100%">
                            <thead class="thead-light vt_head_td">
                            <tr class="width_vvtt">
                                <th>Technician</th>
                                <th>Pending Task</th>
                                <th>Category</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($ticket_category)
                                @foreach($ticket_category as $ticket_cat)
                                    <tr>
                                        <td>{{ $ticket_cat->name }}</td>
                                        <td>{{ $ticket_cat->ticket_count }}</td>
                                        <td>{{ $ticket_cat->category_name}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Tickets Status</h2>
                    </div>
                    <div class="day_month_year_vt" id="status_day_month_year_vt_day">
                        <button><i id="statusGraphPreviousDay" class="fa fa-caret-left"></i></button>
                        <div
                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-status mt10">
                            <input type="text" autocomplete="off" name="statusGraphDay" id="statusGraphDay"
                                   placeholder="Select" class="c-datepicker-data-input" value="">
                        </div>
                        <button><i id="statusGraphForwardDay" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="status_day_month_year_vt_month">
                        <button><i id="statusGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-status mt10">
                                <input type="text" autocomplete="off" name="statusGraphMonth" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="statusGraphForwardMonth" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="status_day_month_year_vt_year">
                        <button><i id="statusGraphPreviousYear" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-status mt10">
                                <input type="text" autocomplete="off" name="statusGraphYear" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="statusGraphForwardYear" class="fa fa-caret-right"></i></button>
                    </div>

                    <div class="day_my_btn_vt" id="status_day_my_btn_vt">
                        <button class="day_bt_vt active" id="day">day</button>
                        <button class="month_bt_vt" id="month">month</button>
                        <button class="month_bt_vt" id="year">Year</button>
                    </div>
                    <div class="card-box" dir="ltr" id="statusGraphDiv">
                        <div id="statusContainer"></div>
                        <br>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 md-3">
                <div class="card card_hi_vt">
                    <!-- <div class="card-header" style="padding: 1rem 1rem 0rem 1rem !important; ">
                        <h2 class="All-graph-heading-vt">Tickets</h2>
                        <div class="tickets_drop_vt">
                        <select class="form-control" id="plant_id" name="plant_id" required="" data-toggle="select2">
                            <option value="">Select Plant</option>
                        </select>
                    </div>
                    </div> -->
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Tickets by Company</h2>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable_6" class="display table table-borderless table-centered table-nowrap"
                               style="width:100%">
                            <thead class="thead-light vt_head_td">
                            <tr class="width_vvtt">
                                <th>Technician</th>
                                <th>Pending Task</th>
                                <th>Company</th>
                            </tr>
                            </thead>
                            <tbody class="align_vt">
                            @if($ticket_company)
                                @foreach($ticket_company as $ticket_com)
                                    <tr>
                                        <td>{{ $ticket_com->name }}</td>
                                        <td>{{ $ticket_com->ticket_count }}</td>
                                        <td>{{ $ticket_com->company_name}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Tickets by Plants</h2>
                    </div>
                    <div class="day_month_year_vt" id="plantticket_day_month_year_vt_day">
                        <button><i id="plantticketGraphPreviousDay" class="fa fa-caret-left"></i></button>
                        <div
                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-status mt10">
                            <input type="text" autocomplete="off" name="plantTicketGraphDay" id="plantTicketGraphDay"
                                   placeholder="Select" class="c-datepicker-data-input" value="">
                        </div>
                        <button><i id="plantticketGraphForwardDay" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="plantticket_day_month_year_vt_month">
                        <button><i id="plantticketGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-status mt10">
                                <input type="text" autocomplete="off" name="plantTicketGraphMonth" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="plantticketGraphForwardMonth" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="plantticket_day_month_year_vt_year">
                        <button><i id="plantticketGraphPreviousYear" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-status mt10">
                                <input type="text" autocomplete="off" name="plantTicketGraphYear" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="plantticketGraphForwardYear" class="fa fa-caret-right"></i></button>
                    </div>

                    <div class="day_my_btn_vt" id="plantticket_day_my_btn_vt">
                        <button class="day_bt_vt active" id="day">day</button>
                        <button class="month_bt_vt" id="month">month</button>
                        <button class="month_bt_vt" id="year">Year</button>

                    </div>
                    <div class="row graph_btn_link_vt">
                    <button id="max-status" class="col-1 maximagechange"><img class="maximagechange" id="max124" src="{{ asset('assets/images/maxactive.png')}}" alt="" height="30" title="Max Tickets "></button>
                    <button id="min-status" class="col-1 ml-2 "><img id="min124" class="minimagechange" src="{{ asset('assets/images/min.png')}}" alt="" height="30" title="Min Tickets "></button>
                    </div>
                    <input type="hidden" id="hidden-data">
                    <div class="card-box" dir="ltr" id="plantticketsGraphDiv">
                        <div id="ticketsbyplant" style="height : 200px "></div>
                        <br>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Tickets</h2>
                    </div>
                    <div class="day_month_year_vt" id="approach_day_month_year_vt_day">
                        <button><i id="approachGraphPreviousDay" class="fa fa-caret-left"></i></button>
                        <div
                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-approach mt10">
                            <input type="text" autocomplete="off" name="approachGraphDay" id="approachGraphDay"
                                   placeholder="Select" class="c-datepicker-data-input" value="">
                        </div>
                        <button><i id="approachGraphForwardDay" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="approach_day_month_year_vt_month">
                        <button><i id="approachGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-approach mt10">
                                <input type="text" autocomplete="off" name="approachGraphMonth" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="approachGraphForwardMonth" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="approach_day_month_year_vt_year">
                        <button><i id="approachGraphPreviousYear" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-approach mt10">
                                <input type="text" autocomplete="off" name="approachGraphYear" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="approachGraphForwardYear" class="fa fa-caret-right"></i></button>
                    </div>

                    <div class="day_my_btn_vt" id="approach_day_my_btn_vt">
                        <button class="day_bt_vt active" id="day">day</button>
                        <button class="month_bt_vt" id="month">month</button>
                        <button class="month_bt_vt" id="year">Year</button>
                    </div>
                    <div class="card-box" dir="ltr">
                        <div class="card_box_vt_sp" id="approachSpinner" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <br>
                        <div class="mt-3 mb-3 chartjs-chart" id="approachChartDiv">
                            <div id="approachChart" style="height: 210px;"></div>
                        </div>
                        <div class="online-fault-vt mb-0" id="approachChartDetailDiv">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Ticket Receiving Medium</h2>
                    </div>
                    {{-- <div class="day_month_year_vt" id="medium_day_month_year_vt_day">
                        <button><i id="mediumGraphPreviousDay" class="fa fa-caret-left"></i></button>
                        <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-medium mt10">
                            <input type="text" autocomplete="off" name="mediumGraphDay" id="mediumGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                        </div>
                        <button><i id="mediumGraphForwardDay" class="fa fa-caret-right"></i></button>
                    </div> --}}
                    <div class="day_month_year_vt" id="medium_day_month_year_vt_month">
                        <button><i id="mediumGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-medium mt10">
                                <input type="text" autocomplete="off" name="mediumGraphMonth" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="mediumGraphForwardMonth" class="fa fa-caret-right"></i></button>
                    </div>
                    <div class="day_month_year_vt" id="medium_day_month_year_vt_year">
                        <button><i id="mediumGraphPreviousYear" class="fa fa-caret-left"></i></button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-medium mt10">
                                <input type="text" autocomplete="off" name="mediumGraphYear" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button><i id="mediumGraphForwardYear" class="fa fa-caret-right"></i></button>
                    </div>

                    <div class="day_my_btn_vt" id="medium_day_my_btn_vt">
                        {{-- <button class="day_bt_vt" id="day">day</button> --}}
                        <button class="month_bt_vt active" id="month">month</button>
                        <button class="month_bt_vt" id="year">Year</button>
                    </div>
                    <div class="card-box" dir="ltr" id="mediumGraphDiv">
                        <div id="mediumContainer"></div>
                        <br>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--  Alerts Center Modal-->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.0.0/dist/echarts.min.js"></script>
    <script type="text/javascript">
        $('.minimagechange').on({
            'click': function(){
                // console.log("dfh");
                $('#min124').attr('src','{{ asset('assets/images/minactive.png')}}');
                $('#max124').attr('src','{{ asset('assets/images/max.png')}}');
            }
        });
        $('.maximagechange').on({
            'click': function(){
                // console.log("dfh");
                $('#min124').attr('src','{{ asset('assets/images/min.png')}}');
                $('#max124').attr('src','{{ asset('assets/images/maxactive.png')}}');
            }
        });
        window.onload = function () {

            var plant_name = {};
            var filterss_arr = {};
            var plants_array = <?php echo $filter_data['plants']; ?>;
            var ticketStatus = "max";


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

            $('#clearFilters12').on('click', function (e) {

                $('#plant_type').prop('selectedIndex', 0);
                $('#company').prop('selectedIndex', 0);
                $('#province').prop('selectedIndex', 0);
                $('#city').prop('selectedIndex', 0);

                $(".companyFilterMultiSelect").val("");
                $(".companyFilterMultiSelect").trigger("change");

                $(".plantFilterMultiSelect").val("");
                $(".plantFilterMultiSelect").trigger("change");

                $('#filtersForm').trigger('submit');

            });
            if (($('#plant_name').val()).length != 0) {

                plant_name = $('#plant_name').val();
            }

            if ($('#plant_type').val() != 'all') {

                filterss_arr['plant_type'] = $('#plant_type').val();
            }

            if ($('#province').val() != 'all') {

                filterss_arr['province'] = $('#province').val();
            }

            if ($('#city').val() != 'all') {

                filterss_arr['city'] = $('#city').val();
            }


            var plant_axis_grid = 4;
            $(document).ready(function () {

                var ticket_count = <?php echo $ticket_count; ?>;

                var currDate = getCurrentDate();

                $('input[name="priorityGraphDay"]').val(currDate.todayDate);
                $('input[name="priorityGraphMonth"]').val(currDate.todayMonth);
                $('input[name="priorityGraphYear"]').val(currDate.todayYear);
                $('input[name="statusGraphDay"]').val(currDate.todayDate);
                $('input[name="statusGraphMonth"]').val(currDate.todayMonth);
                $('input[name="statusGraphYear"]').val(currDate.todayYear);
                $('input[name="approachGraphDay"]').val(currDate.todayDate);
                $('input[name="approachGraphMonth"]').val(currDate.todayMonth);
                $('input[name="approachGraphYear"]').val(currDate.todayYear);
                //$('input[name="mediumGraphDay"]').val(currDate.todayDate);
                $('input[name="mediumGraphMonth"]').val(currDate.todayMonth);
                $('input[name="mediumGraphYear"]').val(currDate.todayYear);
                $('input[name="plantTicketGraphDay"]').val(currDate.todayDate);
                $('input[name="plantTicketGraphMonth"]').val(currDate.todayMonth);
                $('input[name="plantTicketGraphYear"]').val(currDate.todayYear);


                var priority_date = $('input[name="priorityGraphDay"]').val();
                var priority_time = 'day';
                var status_date = $('input[name="statusGraphDay"]').val();
                var status_time = 'day';
                var approach_date = $('input[name="approachGraphDay"]').val();
                var approach_time = 'day';
                var medium_date = $('input[name="mediumGraphMonth"]').val();
                var medium_time = 'month';
                var plantticket_date = $('input[name="plantTicketGraphDay"]').val();
                var plantticket_date = 'day';




                changeStatusDayMonthYear(status_date, status_time, filterss_arr, plant_name);
                statusGraphAjax(status_date, status_time, filterss_arr, plant_name);
                changeApproachDayMonthYear(approach_date, approach_time, filterss_arr, plant_name);
                approachGraphAjax(approach_date, approach_time, filterss_arr, plant_name);
                changePriorityDayMonthYear(priority_date, priority_time, filterss_arr, plant_name);
                priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
                changeMediumDayMonthYear(medium_date, medium_time, filterss_arr, plant_name);
                mediumGraphAjax(medium_date, medium_time, filterss_arr, plant_name);
                changePlantTicketDayMonthYear(status_date, status_time, filterss_arr, plant_name);
                PlantTicketGraphAjax(status_date, status_time, filterss_arr, plant_name, ticketStatus);


                $('.J-yearMonthDayPicker-single-priority').datePicker({
                    format: 'YYYY-MM-DD',
                    language: 'en',
                    hide: function (type) {
                        changePriorityDayMonthYear(this.$input.eq(0).val(), 'day');
                    }
                });

                $('.J-yearMonthPicker-single-priority').datePicker({
                    format: 'MM-YYYY',
                    language: 'en',
                    hide: function (type) {
                        console.log(this.$input.eq(0).val());
                        changePriorityDayMonthYear(this.$input.eq(0).val(), 'month');
                    }
                });

                $('.J-yearPicker-single-priority').datePicker({
                    format: 'YYYY',
                    language: 'en',
                    hide: function (type) {
                        changePriorityDayMonthYear(this.$input.eq(0).val(), 'year');
                    }
                });

                $('.J-yearMonthDayPicker-single-status').datePicker({
                    format: 'YYYY-MM-DD',
                    language: 'en',
                    hide: function (type) {
                        changeStatusDayMonthYear(this.$input.eq(0).val(), 'day');
                    }
                });

                $('.J-yearMonthPicker-single-status').datePicker({
                    format: 'MM-YYYY',
                    language: 'en',
                    hide: function (type) {
                        console.log(this.$input.eq(0).val());
                        changeStatusDayMonthYear(this.$input.eq(0).val(), 'month');
                    }
                });

                $('.J-yearPicker-single-status').datePicker({
                    format: 'YYYY',
                    language: 'en',
                    hide: function (type) {
                        changeStatusDayMonthYear(this.$input.eq(0).val(), 'year');
                    }
                });

                $('.J-yearMonthDayPicker-single-status').datePicker({
                    format: 'YYYY-MM-DD',
                    language: 'en',
                    hide: function (type) {
                        changePlantTicketDayMonthYear(this.$input.eq(0).val(), 'day');
                    }
                });

                $('.J-yearMonthPicker-single-status').datePicker({
                    format: 'MM-YYYY',
                    language: 'en',
                    hide: function (type) {
                        console.log(this.$input.eq(0).val());
                        changePlantTicketDayMonthYear(this.$input.eq(0).val(), 'month');
                    }
                });

                $('.J-yearPicker-single-status').datePicker({
                    format: 'YYYY',
                    language: 'en',
                    hide: function (type) {
                        changePlantTicketDayMonthYear(this.$input.eq(0).val(), 'year');
                    }
                });


                $('.J-yearMonthDayPicker-single-approach').datePicker({
                    format: 'YYYY-MM-DD',
                    language: 'en',
                    hide: function (type) {
                        changeApproachDayMonthYear(this.$input.eq(0).val(), 'day');
                    }
                });

                $('.J-yearMonthPicker-single-approach').datePicker({
                    format: 'MM-YYYY',
                    language: 'en',
                    hide: function (type) {
                        console.log(this.$input.eq(0).val());
                        changeApproachDayMonthYear(this.$input.eq(0).val(), 'month');
                    }
                });

                $('.J-yearPicker-single-approach').datePicker({
                    format: 'YYYY',
                    language: 'en',
                    hide: function (type) {
                        changeApproachDayMonthYear(this.$input.eq(0).val(), 'year');
                    }
                });

                /*$('.J-yearMonthDayPicker-single-medium').datePicker({
                    format: 'YYYY-MM-DD',
                    language: 'en',
                    hide: function(type) {
                        changeMediumDayMonthYear(this.$input.eq(0).val(), 'day');
                    }
                });*/

                $('.J-yearMonthPicker-single-medium').datePicker({
                    format: 'MM-YYYY',
                    language: 'en',
                    hide: function (type) {
                        console.log(this.$input.eq(0).val());
                        changeMediumDayMonthYear(this.$input.eq(0).val(), 'month');
                    }
                });

                $('.J-yearPicker-single-medium').datePicker({
                    format: 'YYYY',
                    language: 'en',
                    hide: function (type) {
                        changeMediumDayMonthYear(this.$input.eq(0).val(), 'year');
                    }
                });

                $('#priorityGraphPreviousDay').on('click', function () {

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

                $('#priorityGraphForwardDay').on('click', function () {

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

                $('#priorityGraphPreviousMonth').on('click', function () {

                    show_date = $("input[name='priorityGraphMonth']").val();
                    priority_date = formatPreviousMonth(show_date);
                    $('input[name="priorityGraphMonth"]').val('');
                    $('input[name="priorityGraphMonth"]').val(priority_date);
                    console.log($("input[name='priorityGraphMonth']").val());
                    priority_time = 'month';
                    priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
                });

                $('#priorityGraphForwardMonth').on('click', function () {

                    show_date = $("input[name='priorityGraphMonth']").val();
                    priority_date = formatForwardMonth(show_date);
                    $('input[name="priorityGraphMonth"]').val('');
                    $('input[name="priorityGraphMonth"]').val(priority_date);
                    console.log($("input[name='priorityGraphMonth']").val());
                    priority_time = 'month';
                    priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
                });

                $('#priorityGraphPreviousYear').on('click', function () {

                    show_date = $("input[name='priorityGraphYear']").val();
                    priority_date = formatPreviousYear(show_date);
                    $('input[name="priorityGraphYear"]').val('');
                    $('input[name="priorityGraphYear"]').val(priority_date);
                    console.log($("input[name='priorityGraphYear']").val());
                    priority_time = 'year';
                    priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
                });

                $('#priorityGraphForwardYear').on('click', function () {

                    show_date = $("input[name='priorityGraphYear']").val();
                    priority_date = formatForwardYear(show_date);
                    $('input[name="priorityGraphYear"]').val('');
                    $('input[name="priorityGraphYear"]').val(priority_date);
                    console.log($("input[name='priorityGraphYear']").val());
                    priority_time = 'year';
                    priorityGraphAjax(priority_date, priority_time, filterss_arr, plant_name);
                });

                $('#plantticketGraphPreviousDay').on('click', function () {
                    var dataArray = [];
                    show_date = $("input[name='plantTicketGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() - 1);
                    status_date = formatDate(datess);
                    $('input[name="plantTicketGraphDay"]').val('');
                    $('input[name="plantTicketGraphDay"]').val(status_date);
                    console.log($("input[name='plantTicketGraphDay']").val());
                    status_time = 'day';
                    dataArray.push(status_date,status_time)
                    document.getElementById('hidden-data').setAttribute('value',dataArray);
                    let inputData = document.getElementById('hidden-data');
                    PlantTicketGraphAjax(status_date, status_time, filterss_arr, plant_name, ticketStatus);
                    dataArray = [];
                });

                $('#plantticketGraphForwardDay').on('click', function () {
                    var dataArray = [];
                    show_date = $("input[name='plantTicketGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() + 1);
                    status_date = formatDate(datess);
                    $('input[name="plantTicketGraphDay"]').val('');
                    $('input[name="plantTicketGraphDay"]').val(status_date);
                    console.log($("input[name='plantTicketGraphDay']").val());
                    status_time = 'day';
                    dataArray.push(status_date,status_time)
                    document.getElementById('hidden-data').setAttribute('value',dataArray);
                    let inputData = document.getElementById('hidden-data');
                    PlantTicketGraphAjax(status_date, status_time, filterss_arr, plant_name, ticketStatus);
                    dataArray = [];
                });

                $('#plantticketGraphPreviousMonth').on('click', function () {
                    var dataArray = [];
                    show_date = $("input[name='plantTicketGraphMonth']").val();
                    status_date = formatPreviousMonth(show_date);
                    $('input[name="plantTicketGraphMonth"]').val('');
                    $('input[name="plantTicketGraphMonth"]').val(status_date);
                    console.log($("input[name='plantTicketGraphMonth']").val());
                    status_time = 'month';
                    dataArray.push(status_date,status_time)
                    document.getElementById('hidden-data').setAttribute('value',dataArray);
                    let inputData = document.getElementById('hidden-data');
                    PlantTicketGraphAjax(status_date, status_time, filterss_arr, plant_name, ticketStatus);
                    dataArray = [];
                });

                $('#plantticketGraphForwardMonth').on('click', function () {

                    var dataArray = [];
                    show_date = $("input[name='plantTicketGraphMonth']").val();
                    status_date = formatForwardMonth(show_date);
                    $('input[name="plantTicketGraphMonth"]').val('');
                    $('input[name="plantTicketGraphMonth"]').val(status_date);
                    console.log($("input[name='plantTicketGraphMonth']").val());
                    status_time = 'month';
                    dataArray.push(status_date,status_time)
                    document.getElementById('hidden-data').setAttribute('value',dataArray);
                    let inputData = document.getElementById('hidden-data');
                    PlantTicketGraphAjax(status_date, status_time, filterss_arr, plant_name, ticketStatus);
                    dataArray = [];
                });

                $('#plantticketGraphPreviousYear').on('click', function () {
                    var dataArray = [];
                    show_date = $("input[name='plantTicketGraphYear']").val();
                    status_date = formatPreviousYear(show_date);
                    $('input[name="plantTicketGraphYear"]').val('');
                    $('input[name="plantTicketGraphYear"]').val(status_date);
                    console.log($("input[name='plantTicketGraphYear']").val());
                    status_time = 'year';
                    dataArray.push(status_date,status_time)
                    document.getElementById('hidden-data').setAttribute('value',dataArray);
                    let inputData = document.getElementById('hidden-data');
                    PlantTicketGraphAjax(status_date, status_time, filterss_arr, plant_name, ticketStatus);
                    dataArray = [];
                });

                $('#plantticketGraphForwardYear').on('click', function () {
                    var dataArray = [];
                    show_date = $("input[name='plantTicketGraphYear']").val();
                    status_date = formatForwardYear(show_date);
                    $('input[name="plantTicketGraphYear"]').val('');
                    $('input[name="plantTicketGraphYear"]').val(status_date);
                    console.log($("input[name='plantTicketGraphYear']").val());
                    status_time = 'year';
                    dataArray.push(status_date,status_time)
                    document.getElementById('hidden-data').setAttribute('value',dataArray);
                    let inputData = document.getElementById('hidden-data');
                    PlantTicketGraphAjax(status_date, status_time, filterss_arr, plant_name, ticketStatus);
                    dataArray = [];
                });

                $('#statusGraphPreviousDay').on('click', function () {

                    show_date = $("input[name='statusGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() - 1);
                    status_date = formatDate(datess);
                    $('input[name="statusGraphDay"]').val('');
                    $('input[name="statusGraphDay"]').val(status_date);
                    console.log($("input[name='statusGraphDay']").val());
                    status_time = 'day';
                    statusGraphAjax(status_date, status_time, filterss_arr, plant_name);
                });

                $('#statusGraphForwardDay').on('click', function () {

                    show_date = $("input[name='statusGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() + 1);
                    status_date = formatDate(datess);
                    $('input[name="statusGraphDay"]').val('');
                    $('input[name="statusGraphDay"]').val(status_date);
                    console.log($("input[name='statusGraphDay']").val());
                    status_time = 'day';
                    statusGraphAjax(status_date, status_time, filterss_arr, plant_name);
                });

                $('#statusGraphPreviousMonth').on('click', function () {

                    show_date = $("input[name='statusGraphMonth']").val();
                    status_date = formatPreviousMonth(show_date);
                    $('input[name="statusGraphMonth"]').val('');
                    $('input[name="statusGraphMonth"]').val(status_date);
                    console.log($("input[name='statusGraphMonth']").val());
                    status_time = 'month';
                    statusGraphAjax(status_date, status_time, filterss_arr, plant_name);
                });

                $('#statusGraphForwardMonth').on('click', function () {

                    show_date = $("input[name='statusGraphMonth']").val();
                    status_date = formatForwardMonth(show_date);
                    $('input[name="statusGraphMonth"]').val('');
                    $('input[name="statusGraphMonth"]').val(status_date);
                    console.log($("input[name='statusGraphMonth']").val());
                    status_time = 'month';
                    statusGraphAjax(status_date, status_time, filterss_arr, plant_name);
                });

                $('#statusGraphPreviousYear').on('click', function () {

                    show_date = $("input[name='statusGraphYear']").val();
                    status_date = formatPreviousYear(show_date);
                    $('input[name="statusGraphYear"]').val('');
                    $('input[name="statusGraphYear"]').val(status_date);
                    console.log($("input[name='statusGraphYear']").val());
                    status_time = 'year';
                    statusGraphAjax(status_date, status_time, filterss_arr, plant_name);
                });

                $('#statusGraphForwardYear').on('click', function () {

                    show_date = $("input[name='statusGraphYear']").val();
                    status_date = formatForwardYear(show_date);
                    $('input[name="statusGraphYear"]').val('');
                    $('input[name="statusGraphYear"]').val(status_date);
                    console.log($("input[name='statusGraphYear']").val());
                    status_time = 'year';
                    statusGraphAjax(status_date, status_time, filterss_arr, plant_name);
                });


                $('#approachGraphPreviousDay').on('click', function () {

                    show_date = $("input[name='approachGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() - 1);
                    approach_date = formatDate(datess);
                    $('input[name="approachGraphDay"]').val('');
                    $('input[name="approachGraphDay"]').val(approach_date);
                    console.log($("input[name='approachGraphDay']").val());
                    approach_time = 'day';
                    approachGraphAjax(approach_date, approach_time, filterss_arr, plant_name);
                });

                $('#approachGraphForwardDay').on('click', function () {

                    show_date = $("input[name='approachGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() + 1);
                    approach_date = formatDate(datess);
                    $('input[name="approachGraphDay"]').val('');
                    $('input[name="approachGraphDay"]').val(approach_date);
                    console.log($("input[name='approachGraphDay']").val());
                    approach_time = 'day';
                    approachGraphAjax(approach_date, approach_time, filterss_arr, plant_name);
                });

                $('#approachGraphPreviousMonth').on('click', function () {

                    show_date = $("input[name='approachGraphMonth']").val();
                    approach_date = formatPreviousMonth(show_date);
                    $('input[name="approachGraphMonth"]').val('');
                    $('input[name="approachGraphMonth"]').val(approach_date);
                    console.log($("input[name='approachGraphMonth']").val());
                    approach_time = 'month';
                    approachGraphAjax(approach_date, approach_time, filterss_arr, plant_name);
                });

                $('#approachGraphForwardMonth').on('click', function () {

                    show_date = $("input[name='approachGraphMonth']").val();
                    approach_date = formatForwardMonth(show_date);
                    $('input[name="approachGraphMonth"]').val('');
                    $('input[name="approachGraphMonth"]').val(approach_date);
                    console.log($("input[name='approachGraphMonth']").val());
                    approach_time = 'month';
                    approachGraphAjax(approach_date, approach_time, filterss_arr, plant_name);
                });

                $('#approachGraphPreviousYear').on('click', function () {

                    show_date = $("input[name='approachGraphYear']").val();
                    approach_date = formatPreviousYear(show_date);
                    $('input[name="approachGraphYear"]').val('');
                    $('input[name="approachGraphYear"]').val(approach_date);
                    console.log($("input[name='approachGraphYear']").val());
                    approach_time = 'year';
                    approachGraphAjax(approach_date, approach_time, filterss_arr, plant_name);
                });

                $('#approachGraphForwardYear').on('click', function () {

                    show_date = $("input[name='approachGraphYear']").val();
                    approach_date = formatForwardYear(show_date);
                    $('input[name="approachGraphYear"]').val('');
                    $('input[name="approachGraphYear"]').val(approach_date);
                    console.log($("input[name='approachGraphYear']").val());
                    approach_time = 'year';
                    approachGraphAjax(approach_date, approach_time, filterss_arr, plant_name);
                });

                /*$('#mediumGraphPreviousDay').on('click', function() {

                    show_date = $("input[name='mediumGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() - 1);
                    medium_date = formatDate(datess);
                    $('input[name="mediumGraphDay"]').val('');
                    $('input[name="mediumGraphDay"]').val(medium_date);
                    console.log($("input[name='mediumGraphDay']").val());
                    medium_time = 'day';
                    mediumGraphAjax(medium_date, medium_time);
                });

                $('#mediumGraphForwardDay').on('click', function() {

                    show_date = $("input[name='mediumGraphDay']").val();
                    var datess = new Date(show_date);
                    console.log(datess);
                    datess.setDate(datess.getDate() + 1);
                    medium_date = formatDate(datess);
                    $('input[name="mediumGraphDay"]').val('');
                    $('input[name="mediumGraphDay"]').val(medium_date);
                    console.log($("input[name='mediumGraphDay']").val());
                    medium_time = 'day';
                    mediumGraphAjax(medium_date, medium_time);
                });*/

                $('#mediumGraphPreviousMonth').on('click', function () {

                    show_date = $("input[name='mediumGraphMonth']").val();
                    medium_date = formatPreviousMonth(show_date);
                    $('input[name="mediumGraphMonth"]').val('');
                    $('input[name="mediumGraphMonth"]').val(medium_date);
                    console.log($("input[name='mediumGraphMonth']").val());
                    medium_time = 'month';
                    mediumGraphAjax(medium_date, medium_time, filterss_arr, plant_name);
                });

                $('#mediumGraphForwardMonth').on('click', function () {

                    show_date = $("input[name='mediumGraphMonth']").val();
                    medium_date = formatForwardMonth(show_date);
                    $('input[name="mediumGraphMonth"]').val('');
                    $('input[name="mediumGraphMonth"]').val(medium_date);
                    console.log($("input[name='mediumGraphMonth']").val());
                    medium_time = 'month';
                    mediumGraphAjax(medium_date, medium_time, filterss_arr, plant_name);
                });

                $('#mediumGraphPreviousYear').on('click', function () {

                    show_date = $("input[name='mediumGraphYear']").val();
                    medium_date = formatPreviousYear(show_date);
                    $('input[name="mediumGraphYear"]').val('');
                    $('input[name="mediumGraphYear"]').val(medium_date);
                    console.log($("input[name='mediumGraphYear']").val());
                    medium_time = 'year';
                    mediumGraphAjax(medium_date, medium_time, filterss_arr, plant_name);
                });

                $('#mediumGraphForwardYear').on('click', function () {

                    show_date = $("input[name='mediumGraphYear']").val();
                    medium_date = formatForwardYear(show_date);
                    $('input[name="mediumGraphYear"]').val('');
                    $('input[name="mediumGraphYear"]').val(medium_date);
                    console.log($("input[name='mediumGraphYear']").val());
                    medium_time = 'year';
                    mediumGraphAjax(medium_date, medium_time, filterss_arr, plant_name);
                });

                $("#priority_day_my_btn_vt button").click(function () {

                    $('#priority_day_my_btn_vt').children().removeClass("active");
                    $(this).addClass("active");

                    changePriorityDayMonthYear(priority_date, priority_time, filterss_arr, plant_name);

                });

                $("#status_day_my_btn_vt button").click(function () {

                    $('#status_day_my_btn_vt').children().removeClass("active");
                    $(this).addClass("active");

                    changeStatusDayMonthYear(status_date, status_time, filterss_arr, plant_name);

                });

                $("#plantticket_day_my_btn_vt button").click(function () {

                    $('#plantticket_day_my_btn_vt').children().removeClass("active");
                    $(this).addClass("active");

                    changePlantTicketDayMonthYear(status_date, status_time, filterss_arr, plant_name,ticketStatus);

                });
                $("#min-status").click(function () {

                    ticketStatus="min";
                    let graphData = document.getElementById('hidden-data').getAttribute('value');
                    let ajaxData =  graphData.split(',');
                    PlantTicketGraphAjax(ajaxData[0], ajaxData[1], filterss_arr, plant_name, ticketStatus);

                });
                $("#max-status").click(function () {
                    ticketStatus="max";
                    let graphData = document.getElementById('hidden-data').getAttribute('value');
                    let ajaxData =  graphData.split(',');
                    PlantTicketGraphAjax(ajaxData[0], ajaxData[1], filterss_arr, plant_name, ticketStatus);

                });
                $("#approach_day_my_btn_vt button").click(function () {

                    $('#approach_day_my_btn_vt').children().removeClass("active");
                    $(this).addClass("active");

                    changeApproachDayMonthYear(approach_date, approach_time, filterss_arr, plant_name);

                });

                $("#medium_day_my_btn_vt button").click(function () {

                    $('#medium_day_my_btn_vt').children().removeClass("active");
                    $(this).addClass("active");

                    changeMediumDayMonthYear(medium_date, medium_time, filterss_arr, plant_name);

                });

                function changePriorityDayMonthYear(date, time) {

                    var d_m_y = '';

                    $('#priority_day_my_btn_vt').children('button').each(function () {
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

                function changeStatusDayMonthYear(date, time) {

                    var d_m_y = '';

                    $('#status_day_my_btn_vt').children('button').each(function () {
                        if ($(this).hasClass('active')) {
                            d_m_y = $(this).attr('id');
                        }
                    });

                    if (d_m_y == 'day') {
                        $('#status_day_month_year_vt_year').hide();
                        $('#status_day_month_year_vt_month').hide();
                        $('#status_day_month_year_vt_day').show();
                        date = $('input[name="statusGraphDay"]').val();
                        time = 'day';
                    } else if (d_m_y == 'month') {
                        $('#status_day_month_year_vt_year').hide();
                        $('#status_day_month_year_vt_day').hide();
                        $('#status_day_month_year_vt_month').show();
                        date = $('input[name="statusGraphMonth"]').val();
                        time = 'month';
                    } else if (d_m_y == 'year') {
                        $('#status_day_month_year_vt_day').hide();
                        $('#status_day_month_year_vt_month').hide();
                        $('#status_day_month_year_vt_year').show();
                        date = $('input[name="statusGraphYear"]').val();
                        time = 'year';
                    }

                    statusGraphAjax(date, time, filterss_arr, plant_name);
                }

                function changePlantTicketDayMonthYear(date, time) {

                    var d_m_y = '';
                    var dataArray = [];

                    $('#plantticket_day_my_btn_vt').children('button').each(function () {
                        if ($(this).hasClass('active')) {
                            d_m_y = $(this).attr('id');
                        }
                    });

                    if (d_m_y == 'day') {
                        $('#plantticket_day_month_year_vt_year').hide();
                        $('#plantticket_day_month_year_vt_month').hide();
                        $('#plantticket_day_month_year_vt_day').show();
                        date = $('input[name="plantTicketGraphDay"]').val();
                        time = 'day';
                    } else if (d_m_y == 'month') {
                        $('#plantticket_day_month_year_vt_year').hide();
                        $('#plantticket_day_month_year_vt_day').hide();
                        $('#plantticket_day_month_year_vt_month').show();
                        date = $('input[name="plantTicketGraphMonth"]').val();
                        time = 'month';
                    } else if (d_m_y == 'year') {
                        $('#plantticket_day_month_year_vt_day').hide();
                        $('#plantticket_day_month_year_vt_month').hide();
                        $('#plantticket_day_month_year_vt_year').show();
                        date = $('input[name="plantTicketGraphYear"]').val();
                        time = 'year';
                    }
                    dataArray.push(date,time)
                    document.getElementById('hidden-data').setAttribute('value',dataArray);
                    let inputData = document.getElementById('hidden-data');
                    PlantTicketGraphAjax(date, time, filterss_arr, plant_name, ticketStatus);
                    dataArray = [];
                }

                function changeApproachDayMonthYear(date, time) {

                    var d_m_y = '';

                    $('#approach_day_my_btn_vt').children('button').each(function () {
                        if ($(this).hasClass('active')) {
                            d_m_y = $(this).attr('id');
                        }
                    });

                    if (d_m_y == 'day') {
                        $('#approach_day_month_year_vt_year').hide();
                        $('#approach_day_month_year_vt_month').hide();
                        $('#approach_day_month_year_vt_day').show();
                        date = $('input[name="approachGraphDay"]').val();
                        time = 'day';
                    } else if (d_m_y == 'month') {
                        $('#approach_day_month_year_vt_year').hide();
                        $('#approach_day_month_year_vt_day').hide();
                        $('#approach_day_month_year_vt_month').show();
                        date = $('input[name="approachGraphMonth"]').val();
                        time = 'month';
                    } else if (d_m_y == 'year') {
                        $('#approach_day_month_year_vt_day').hide();
                        $('#approach_day_month_year_vt_month').hide();
                        $('#approach_day_month_year_vt_year').show();
                        date = $('input[name="approachGraphYear"]').val();
                        time = 'year';
                    }

                    approachGraphAjax(date, time, filterss_arr, plant_name);
                }

                function changeMediumDayMonthYear(date, time) {

                    var d_m_y = '';

                    $('#medium_day_my_btn_vt').children('button').each(function () {
                        if ($(this).hasClass('active')) {
                            d_m_y = $(this).attr('id');
                        }
                    });

                    /*if (d_m_y == 'day') {
                        $('#medium_day_month_year_vt_year').hide();
                        $('#medium_day_month_year_vt_month').hide();
                        $('#medium_day_month_year_vt_day').show();
                        date = $('input[name="mediumGraphDay"]').val();
                        time = 'day';
                    } else*/
                    if (d_m_y == 'month') {
                        $('#medium_day_month_year_vt_year').hide();
                        $('#medium_day_month_year_vt_day').hide();
                        $('#medium_day_month_year_vt_month').show();
                        date = $('input[name="mediumGraphMonth"]').val();
                        time = 'month';
                    } else if (d_m_y == 'year') {
                        $('#medium_day_month_year_vt_day').hide();
                        $('#medium_day_month_year_vt_month').hide();
                        $('#medium_day_month_year_vt_year').show();
                        date = $('input[name="mediumGraphYear"]').val();
                        time = 'year';
                    }

                    mediumGraphAjax(date, time, filterss_arr, plant_name);
                }

                function statusGraphAjax(date, time, filter, plant_name) {

                    filters = JSON.stringify(filter);
                    plantName = JSON.stringify(plant_name);
                    $('#statusGraphDiv').empty();

                    $.ajax({
                        url: "{{ route('admin.ticket.status.graph') }}",
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

                            $('#statusGraphDiv').empty();

                            $('#statusGraphDiv').append('<div id="statusContainer" style="height:320px;"></div>');
                            ticketStatusGraph(data.ticket_status_graph, data.legend_array);
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }

                function PlantTicketGraphAjax(date, time, filter, plant_name, ticketStatus) {

                    filters = JSON.stringify(filter);
                    plantName = JSON.stringify(plant_name);
                    $('#plantticketsGraphDiv').empty();

                    $.ajax({
                        url: "{{ route('admin.plant.ticket.graph') }}",
                        method: "GET",
                        data: {
                            'date': date,
                            'time': time,
                            'filter': filters,
                            'plant_name': plantName,
                            'status': ticketStatus,

                        },
                        dataType: 'json',
                        success: function (data) {
                            console.log(status);

                            $('#plantticketsGraphDiv').empty();

                            $('#plantticketsGraphDiv').append('<div id="ticketsbyplant" style="height:320px;"></div>');
                            PlantticketGraph(data.ticket_plants_graph, data.legend_array);
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }

                function approachGraphAjax(date, time, filter, plant_name) {

                    filters = JSON.stringify(filter);
                    plantName = JSON.stringify(plant_name);

                    $('#approachChartDiv div').remove();
                    $('#approachChartDetailDiv').empty();
                    $('#approachSpinner').show();

                    $.ajax({
                        url: "{{ route('admin.ticket.approach.graph') }}",
                        method: "GET",
                        data: {
                            'date': date,
                            'time': time, 'filter': filters,
                            'plant_name': plantName
                        },

                        dataType: 'json',
                        success: function (data) {
                            console.log(data);

                            if (time == 'day') {

                                $('#approachChartDiv div').remove();
                                $('#approachChartDetailDiv').empty();

                                $('#approachChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_alert_day_vt"><span></span></div>');
                                $('#approachChartDiv').append('<div class="mt-3 mb-3 chartjs-chart" id="approachChart" style="height: 200px; width: 100%;" total_log="' + data.ticket_total + '" approach_log="' + data.ticket_approach + '" past_log="' + data.ticket_past + '" today_time="' + data.today_time + '"></div>');
                                $('#approachChartDetailDiv').append('<p><samp class="color05_one_vt"></samp>  Past Due Date/time: <span> ' + data.tot_approach + '</span></p><p><samp class="color04_one_vt"></samp> Total Tickets: <span> ' + data.tot_ticket + '</span></p><p><samp class="color03_one_vt"></samp> Approaching Due Date/Time: <span> ' + data.tot_past + '</span></p>');
                                $('#approachSpinner').hide();

                                plantApproachGraph(date, data.max_total);
                            } else if (time == 'month' || time == 'year') {

                                $('#approachChartDiv div').remove();
                                $('#approachChartDetailDiv').empty();

                                $('#approachChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_alert_month_vt"><span></span></div>');
                                $('#approachChartDiv').append('<div class="mt-3 mb-3 chartjs-chart" id="approachChart" style="height: 200px; width: 100%;"></div>');
                                $('#approachChartDetailDiv').append('<p><samp class="color05_one_vt"></samp> Past Due Date/time: <span> ' + data.tot_approach + '</span></p> <p><samp class="color04_one_vt"></samp> Total Tickets: <span> ' + data.tot_ticket + '</span></p><p><samp class="color03_one_vt"></samp> Approaching Due Date/Time: <span> ' + data.tot_past + '</span></p>');
                                $('#approachSpinner').hide();

                                approach_month_gen(time, date, data.total_log_data, data.approach_log_data, data.past_log_data);
                            }
                        },
                        error: function (data) {
                            console.log(data);
                            alert('Some Error Occured!');
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
                            'filter': filters,
                            'plant_name': plantName,
                            'request_from': 'ticketdashboard'
                        },
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);

                            $('#priorityGraphDiv').empty();

                            $('#priorityGraphDiv').append('<div class="priorityContainer_vtt" id="priorityContainer"></div>');

                            ticketPriorityGraph(data.ticket_priority_graph, data.legend_array);
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }

                function mediumGraphAjax(date, time, filter, plant_name) {
                    filters = JSON.stringify(filter);
                    plantName = JSON.stringify(plant_name);
                    $.ajax({
                        url: "{{ route('admin.ticket.medium.graph') }}",
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

                            $('#mediumGraphDiv').empty();

                            $('#mediumGraphDiv').append('<div id="mediumContainer" style="height:320px;"></div>');

                            ticketMediumGraph(data.ticket_medium_graph, data.month_array, data.legend_array);
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }
            });

            function ticketPriorityGraph(data, legend_array) {

                var dom = document.getElementById("priorityContainer");
                var myChart = echarts.init(dom);
                var app = {};

                option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{a} <br/>{b} : {c} ({d}%)'
                    },
                    legend: {
                        data: legend_array,
                        bottom: '3px',
                    },
                    series: [{
                        name: 'Ticket Priority',
                        type: 'pie',
                        radius: '50%',
                        label: false,
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

            function ticketStatusGraph(data, legend_array) {

                var dom = document.getElementById("statusContainer");
                var myChart = echarts.init(dom);
                var app = {};

                option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{a} <br/>{b} : {c} ({d}%)'
                    },
                    legend: {
                        data: legend_array,
                        bottom: '3px',
                    },
                    series: [{
                        name: 'Ticket Status',
                        type: 'pie',
                        radius: '50%',
                        label: false,
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

            function plantApproachGraph(date, max_total) {

                var today_time = $('#approachChart').attr('today_time').split(',');
                var total_log = $('#approachChart').attr('total_log').split(',');
                var approach_log = $('#approachChart').attr('approach_log').split(',');
                var past_log = $('#approachChart').attr('past_log').split(',');

                var totalss = [];
                var approachss = [];
                var pastss = [];

                max_total = Math.ceil((max_total / plant_axis_grid));

                var number_format = format_output(max_total);

                max_total = Math.round(max_total / Math.pow(10, number_format)) * Math.pow(10, number_format);
                var dateArr = date.split('-');

                for (var i = 0; i < total_log.length; i++) {
                    totalss[i] = {
                        label: today_time[i],
                        y: parseInt(total_log[i])
                    };
                }

                for (var i = 0; i < approach_log.length; i++) {
                    approachss[i] = {
                        label: today_time[i],
                        y: parseInt(approach_log[i])
                    };
                }

                for (var i = 0; i < past_log.length; i++) {
                    pastss[i] = {
                        label: today_time[i],
                        y: parseInt(past_log[i])
                    };
                }

                var options = {
                    axisX: {
                        interval: 1,
                    },
                    axisY: {
                        interval: max_total,
                        margin: 30,
                        gridThickness: 0.15
                    },
                    data: [
                        {
                            toolTipContent: dateArr[2] + "-" + dateArr[1] + "-" + dateArr[0] + " {label}<br/> Past Due Date/time: {y}",
                            markerType: "none",
                            type: "column",
                            color: "#0F75BC",
                            dataPoints: approachss,
                            barMaxWidth: 2
                        },
                        {
                            toolTipContent: dateArr[2] + "-" + dateArr[1] + "-" + dateArr[0] + " {label}<br/> Total Tickets: {y}",
                            markerType: "none",
                            type: "column",
                            color: "#68AD86",
                            dataPoints: totalss,
                            barMaxWidth: 2
                        },
                        {
                            toolTipContent: dateArr[2] + "-" + dateArr[1] + "-" + dateArr[0] + " {label}<br/> Approaching Due Date/Time: {y}",
                            markerType: "none",
                            type: "column",
                            color: "#0F75BC",
                            dataPoints: pastss,
                            barMaxWidth: 2
                        }
                    ]
                };
                $("#approachChart").CanvasJSChart(options);
            }

            function approach_month_gen(time, date, totalss, approachss, pastss) {

                var max_total = Math.max.apply(Math, totalss.map(function (o) {
                    return o.y;
                }));

                max_total = Math.ceil((max_total / plant_axis_grid));

                var number_format = format_output(max_total);

                max_total = Math.round(max_total / Math.pow(10, number_format)) * Math.pow(10, number_format);

                if (time == 'year') {

                    var options = {

                        axisX: {
                            interval: 1,
                        },

                        axisY: {
                            interval: max_total,
                            margin: 30,
                            gridThickness: 0.15
                        },

                        data: [{
                            toolTipContent: "{tooltip} " + date + "<br/> Past Due Date/time: {y}",
                            markerType: "none",
                            type: "column",
                            color: "#FF9768",
                            dataPoints: approachss,
                            barMaxWidth: 6
                        },
                            {
                                toolTipContent: "{tooltip} " + date + "<br/>Total Tickets: {y}",
                                markerType: "none",
                                type: "column",
                                color: "#68AD86",
                                dataPoints: totalss,
                                barMaxWidth: 6
                            },
                            {
                                toolTipContent: "{tooltip} " + date + "<br/>Approaching Due Date/Time: {y}",
                                markerType: "none",
                                type: "column",
                                color: "#0F75BC",
                                dataPoints: pastss,
                                barMaxWidth: 6
                            }

                        ]
                    };
                } else if (time == 'month') {

                    var dateArr = date.split('-');

                    var options = {

                        axisX: {
                            interval: 2,
                        },

                        axisY: {
                            interval: max_total,
                            margin: 30,
                            gridThickness: 0.15
                        },

                        data: [
                            {
                                toolTipContent: "{x}-" + dateArr[1] + "-" + dateArr[0] + "<br/> Past Due Date/time: {y}",
                                markerType: "none",
                                type: "column",
                                color: "#FF9768",
                                dataPoints: approachss,
                                barMaxWidth: 6
                            },

                            {
                                toolTipContent: "{x}-" + dateArr[1] + "-" + dateArr[0] + "<br/> Total Tickets: {y}",
                                markerType: "none",
                                type: "column",
                                color: "#68AD86",
                                dataPoints: totalss,
                                barMaxWidth: 6
                            },
                            {
                                toolTipContent: "{x}-" + dateArr[1] + "-" + dateArr[0] + "<br/> Approaching Due Date/Time: {y}",
                                markerType: "none",
                                type: "column",
                                color: "#0F75BC",
                                dataPoints: pastss,
                                barMaxWidth: 6
                            },

                        ]
                    };
                }

                var chart = new CanvasJS.Chart("approachChart", options);
                chart.render();
            }

            function format_output(num) {
                return parseInt(Math.log(num) / Math.log(10));
            }

            function ticketMediumGraph(data, month_array, legend_array) {

                var dom = document.getElementById("mediumContainer");
                var myChart = echarts.init(dom);
                var app = {};

                option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: legend_array,
                        // bottom: '-15px',
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
                        data: month_array
                    },
                    yAxis: {
                        type: 'value',
                        minInterval: 1
                    },
                    series: data
                };

                if (option && typeof option === 'object') {
                    myChart.setOption(option);
                }
            }

            function ticketCurrentApproachGraph(ticket_count, curr_approach_count) {

                var dom = document.getElementById("currentApproachContainer");
                var myChart = echarts.init(dom);
                var app = {};

                var intvl = 0;

                if (ticket_count < 10) {

                    intvl = ticket_count;
                } else {

                    intvl = 10;
                }

                option = {
                    series: [{
                        type: 'gauge',
                        startAngle: 180,
                        endAngle: 0,
                        min: 0,
                        max: ticket_count,
                        splitNumber: intvl,
                        itemStyle: {
                            color: '#0081D1',
                        },
                        progress: {
                            show: true,
                            width: 10
                        },
                        pointer: {
                            icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
                            length: '50%',
                            width: 5,
                            itemStyle: {
                                color: '#FE533F',
                            },
                        },
                        axisTick: {
                            splitNumber: 5,
                            lineStyle: {
                                width: 1.5,
                                color: '#999'
                            }
                        },
                        splitLine: {
                            length: 16,
                            lineStyle: {
                                width: 2,
                                color: '#999'
                            }
                        },
                        axisLabel: {
                            distance: 25,
                            color: '#999',
                            fontSize: 13,
                            formatter: function (data) {
                                var v = data;
                                return parseInt(v);
                            }
                        },
                        detail: {
                            show: false,
                        },
                        title: {
                            show: false
                        },
                        data: [{
                            value: curr_approach_count
                        }]
                    }]
                };

                if (option && typeof option === 'object') {
                    myChart.setOption(option);
                }
            }

            function ticketPastApproachGraph(ticket_count, past_approach_count) {

                var dom = document.getElementById("pastApproachContainer");
                var myChart = echarts.init(dom);
                var app = {};

                var intvl = 0;

                if (ticket_count < 10) {

                    intvl = ticket_count;
                } else {

                    intvl = 10;
                }

                option = {
                    series: [{
                        type: 'gauge',
                        startAngle: 180,
                        endAngle: 0,
                        min: 0,
                        max: ticket_count,
                        splitNumber: intvl,
                        itemStyle: {
                            color: '#0081D1',
                        },
                        progress: {
                            show: true,
                            width: 10
                        },
                        pointer: {
                            icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
                            length: '50%',
                            width: 5,
                            itemStyle: {
                                color: '#FE533F',
                            },
                        },
                        axisTick: {
                            splitNumber: 5,
                            lineStyle: {
                                width: 1.5,
                                color: '#999'
                            }
                        },
                        splitLine: {
                            length: 16,
                            lineStyle: {
                                width: 2,
                                color: '#999'
                            }
                        },
                        axisLabel: {
                            distance: 25,
                            color: '#999',
                            fontSize: 13,
                            formatter: function (data) {
                                var v = data;
                                return parseInt(v);
                            }
                        },
                        detail: {
                            show: false,
                        },
                        title: {
                            show: false
                        },
                        data: [{
                            value: past_approach_count
                        }]
                    }]
                };

                if (option && typeof option === 'object') {
                    myChart.setOption(option);
                }
            }
            function PlantticketGraph(data, legend_array) {
                console.log(legend_array);
                var dom = document.getElementById("ticketsbyplant");
                var myChart = echarts.init(dom);
                var app = {};

                option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{a} <br/>{b} : {c} ({d}%)'
                    },
                    legend: {
                        data: legend_array,
                        bottom: '3px',
                    },
                    series: [{
                        name: 'Tickets by Plants',
                        type: 'pie',
                        radius: '50%',
                        label: {
                            position: 'inner',
                            fontSize: 14,
                            formatter: '{c}',
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

        }


    </script>

@endsection
