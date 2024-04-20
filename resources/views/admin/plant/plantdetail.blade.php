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

        .card-widgets > a {
            color: inherit;
            font-size: 24px;
            display: inline-block;
            line-height: 1;
            font-weight: bold;
        }

        .btn_int_clear_vt {
            display: flex;
            justify-content: end;
            align-items: center;
            float: left;
            width: 100%;
            margin: 10px 0;
        }

        .card-widgets > a {
            font-size: 16px !important;
            display: inline-block;
            line-height: 1;
            font-weight: bold;
            position: absolute;
            z-index: 99;
            color: #222;
            top: 13px !important;
            right: 25px;
        }

        .card-widgets > a.collapsed i:before {
            content: "\f077" !important;
        }


        h5.card-title.mb-0 {
            font-size: 16px;
            background: rgb(211 211 211 / 65%);
            line-height: 40px;
            color: #222;
            padding: 0 27px;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .tabel_inv_vt .table thead th {
            color: #063C6E !important;
            text-align: left !important;
            padding-left: 0 !important;
            width: 25%;
            text-transform: capitalize !important;
            padding-bottom: 0 !important;
        }

        .tabel_inv_vt .table td, .table th {
            text-align: left !important;
            border-bottom: none !important;
        }

        .tabel_inv_vt tbody td,
        tbody th {
            text-align: left !important;
            border: none !important;
            padding-left: 0 !important;
            padding-top: 0 !important;
        }

        .card {
            border-radius: 15px !important;
            overflow: hidden !important;
        }

        .card-widgets > a {
            font-size: 24px;
            display: inline-block;
            line-height: 1;
            font-weight: bold;
            position: absolute;
            z-index: 99;
            color: #000;
            top: 10px;
            right: 25px;
        }

        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.5rem 1.5rem 0rem 1.5rem !important;
        }

        .to_bar_graph_vt {
            width: 100%;
            float: left;
        }

        .border_one_vt {
            box-shadow: none !important;
        }

        .left_multiselect_vt {
            width: 300px;
            float: left;
            position: relative;
        }

        .btn_add_vt {
            background: none !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px;
            width: auto;
            height: 37px;
            color: #000 !important;
            padding: 0 20px;
            box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            float: right;
        }

        #checkone_vt {
            display: none;
            border: 1px #dadada solid;
            min-width: 400px;
            padding: 10px;
            position: absolute;
            left: 0;
            top: 36px;
            z-index: 9999999;
            background: #fff;
        }

        #checkone_vt label {
            display: block;
            margin-bottom: 5px !important;
            font-size: 12px !important;
            width: 50%;
            float: left;
        }

        #checkone_vt label input {
            margin-right: 4px;
            transform: translateY(3px);
        }

        #checkone_vt label:hover {
            background-color: #1e90ff;
        }

        #check_battery_monthly_vt {
            display: none;
            border: 1px #dadada solid;
            min-width: 400px;
            padding: 10px;
            position: absolute;
            left: 0;
            top: 36px;
            z-index: 9999999;
            background: #fff;
        }

        #check_battery_monthly_vt label {
            display: block;
            margin-bottom: 5px !important;
            font-size: 12px !important;
            width: 50%;
            float: left;
        }

        #check_battery_monthly_vt label input {
            margin-right: 4px;
            transform: translateY(3px);
        }

        #check_battery_monthly_vt label:hover {
            background-color: #1e90ff;
        }

        #checkpv_vt {
            display: none;
            border: 1px #dadada solid;
            min-width: 400px;
            padding: 10px;
            position: absolute;
            left: 0;
            top: 36px;
            z-index: 9999999;
            background: #fff;
            height: 245px;
            overflow-y: auto;
        }

        #checkpv_vt label {
            display: block;
            margin-bottom: 5px !important;
            font-size: 12px !important;
            width: 50%;
            float: left;
        }

        #checkpv_vt label input {
            margin-right: 4px;
            transform: translateY(3px);
        }

        #checkpv_vt label:hover {
            background-color: #1e90ff;
        }

        #check_monthly_vt {
            display: none;
            border: 1px #dadada solid;
            min-width: 400px;
            padding: 10px;
            position: absolute;
            left: 0;
            top: 36px;
            z-index: 9999999;
            background: #fff;
            height: 245px;
            overflow-y: auto;
        }

        #check_monthly_vt label {
            display: block;
            margin-bottom: 5px !important;
            font-size: 12px !important;
            width: 50%;
            float: left;
        }

        #check_monthly_vt label input {
            margin-right: 4px;
            transform: translateY(3px);
        }

        #check_monthly_vt label:hover {
            background-color: #1e90ff;
        }

        .multiselect_vt {
            width: 200px;
            background: #fff;
        }

        .selectBox_vt {
            position: relative;
            width: 180px;
            float: left;
        }

        .selectBox_vt select {
            width: 100%;
            padding: 7px;
            font-weight: 300;
        }


        .overSelect_vt {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }

        .drop_bt_area_vt button {
            background: #fff;
            border: none;
            border-radius: 7px;
            margin-left: 10px;
            font-size: 14px;
            color: #a7a6a6;
            line-height: 32px;
            position: absolute;
            right: 0;
            top: 0;
        }

        .left_month_date_vt {
            width: 28vw;
            float: right;
            position: relative;
            margin-top: 13px;
        }

        .left_month_date_vt .day_month_calender_vt {
            position: relative;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0;
            z-index: 99;
            float: left;
            width: 100%;
        }

        .left_month_date_vt .left_month_date_vt .day_month_year_vt {
            background: #e6e6e6;
            width: 170px;
            display: flex;
            justify-content: center;
            max-height: 36px;
            float: left;
            padding: 0 10px;
        }

        .left_month_date_vt .day_month_year_vt {
            background: #e6e6e6;
            width: 170px;
            display: flex;
            justify-content: center;
            max-height: 30px;
            float: left;
            padding: 0 10px;
        }

        .left_month_date_vt .day_my_btn_vt {
            width: 300px;
            justify-content: center;
            display: flex;
            background: none;
            border: none;
            margin-top: 0;
        }

        .left_month_date_vt .day_my_btn_vt .month_btn_vt {
            background: none;
            width: 100px;
            justify-content: center;
            border: 1px solid #3955c5;
            border-radius: 100px;
            line-height: 25px;
            margin: 0 3px;
            color: #3955c5;
            text-transform: capitalize;
        }

        .left_month_date_vt .day_my_btn_vt .month_btn_vt:hover {
            background: #3955c5;
            color: #ffffff;
        }

        .left_month_date_vt .day_my_btn_vt .month_btn_vt.active {
            background: #3955c5;
            color: #ffffff;
        }

        .clear_vt_btn {
            background: none !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px;
            width: auto;
            height: 34px;
            color: #000 !important;
            padding: 0 38px;
            box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            float: left;
        }
    </style>
    <?php

    $ac_output_total_power = 0;
    $str_limit = 2;

    ?>

    <div class="container-fluid px-xl-5" style="padding-bottom: 0rem;">
        <section class="py-2">
            <input type="hidden" id="plantID" value="{{ $plantId }}">
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
            <div class="row">
                <div class="col-lg-12 inverter_battery_vt">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                               aria-controls="home" aria-selected="true">Inverter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                               aria-controls="profile" aria-selected="false">Battery</a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content pt-0">
                        <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            @if($grid_type == "Three-phase")
                                <div class="tabel_inv_vt">
                                    <h4 class="header_title_vt">Inverter Summary</h4>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase2" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Basic Information</h5>

                                            <div id="cardCollpase2" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Serial number</th>
                                                            <th>Inverter Type</th>
                                                            <th>Rated power</th>
                                                            <th>Output Power Level</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterVersionData['serial_number']}}</th>
                                                            <td>{{$gridData['grid_type']}}</td>
                                                            <td>{{$inverterVersionData['rated_power']}}</td>
                                                            <td>0</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase3" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Version Information</h5>

                                            <div id="cardCollpase3" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Protocol version</th>
                                                            <th>MAIN</th>
                                                            <th>HMI</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterVersionData['protocol_version']}}</th>
                                                            <td>{{$inverterVersionData['control_software_version']}}</td>
                                                            <td>{{$inverterVersionData['HMI']}}</td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    {{--                                <div class="card">--}}
                                    {{--                                    <div class="card-body">--}}
                                    {{--                                        <div class="card-widgets">--}}
                                    {{--                                            <a data-toggle="collapse" href="#cardCollpase4" role="button"--}}
                                    {{--                                               aria-expanded="false" aria-controls="cardCollpase1"><i--}}
                                    {{--                                                    class="fa fa-chevron-down"></i></a>--}}
                                    {{--                                        </div>--}}
                                    {{--                                        <h5 class="card-title mb-0">State</h5>--}}

                                    {{--                                        <div id="cardCollpase4" class="collapse pt-3 show">--}}
                                    {{--                                            <div class="table-responsive">--}}
                                    {{--                                                <table class="table mb-0">--}}
                                    {{--                                                    <thead>--}}
                                    {{--                                                    <tr>--}}
                                    {{--                                                        <th>Inverter status</th>--}}
                                    {{--                                                        <th>PV charging control status</th>--}}
                                    {{--                                                        <th>Relay status generator side</th>--}}
                                    {{--                                                        <th></th>--}}
                                    {{--                                                    </tr>--}}
                                    {{--                                                    </thead>--}}
                                    {{--                                                    <tbody>--}}
                                    {{--                                                    <tr>--}}
                                    {{--                                                        <th scope="row">--}}
                                    {{--                                                            @if($inverterVersionData['inverter_status'] == 'Y')--}}
                                    {{--                                                                <img--}}
                                    {{--                                                                    src="{{ asset('assets/images/icon_plant_check_vt.svg')}}"--}}
                                    {{--                                                                    alt="check"--}}
                                    {{--                                                                    title="Online">--}}
                                    {{--                                                            @else--}}
                                    {{--                                                                <img src="{{ asset('assets/images/icon_plant_vt.svg')}}"--}}
                                    {{--                                                                     alt="check"--}}
                                    {{--                                                                     title="Offline">--}}
                                    {{--                                                            @endif--}}
                                    {{--                                                        </th>--}}
                                    {{--                                                        <td>2344567</td>--}}
                                    {{--                                                        <td>4.3kWh</td>--}}
                                    {{--                                                        <td></td>--}}
                                    {{--                                                    </tr>--}}
                                    {{--                                                    </tbody>--}}
                                    {{--                                                </table>--}}
                                    {{--                                            </div>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                    {{--                                </div> <!-- end card-->--}}
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase5" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Power Grid</h5>

                                            <div id="cardCollpase5" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Grid Status</th>
                                                            <th>Grid Frequency</th>
                                                            <th>Grid VoltageL1</th>
                                                            <th>Total Grid Power</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>

                                                            <td>{{$grid_type}}</td>
                                                            <td>{{ isset($gridData['grid_frequency']) ? $gridData['grid_frequency'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_voltage_l1']) ? $gridData['grid_voltage_l1'] : 0}}</td>
                                                            <td>{{isset($gridData['total_grid_power']) ? $gridData['total_grid_power'] : 0}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>

                                                            <th>Grid voltage L2</th>
                                                            <th>Grid Voltage L3</th>
                                                            <th>Total External CT Power</th>
                                                            <th>Grid Current L1</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{isset($gridData['grid_voltage_l2']) ? $gridData['grid_voltage_l2'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_voltage_l3']) ? $gridData['grid_voltage_l3'] : 0}}</td>
                                                            <td>{{isset($gridData['total_Ct_power']) ? $gridData['total_Ct_power'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_current_l1']) ? $gridData['grid_current_l1'] : 0}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>

                                                            <th>Grid Current L2</th>
                                                            <th>Grid Current L3</th>
                                                            <th>Grid Power L1</th>
                                                            <th>Grid Power L2</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>

                                                            <td>{{isset($gridData['grid_current_l2']) ? $gridData['grid_current_l2'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_current_l3']) ? $gridData['grid_current_l3'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_power_ld1']) ?  $gridData['grid_power_ld1'] : 0}}</td>
                                                            <td>{{ isset($gridData['grid_power_ld2']) ? $gridData['grid_power_ld2'] : 0}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>

                                                            <th>Grid Power L3</th>
                                                            <th>External CT1 Power</th>
                                                            <th>External CT2 Power</th>
                                                            <th>External CT3 Power</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{isset($gridData['grid_power_ld3']) ? $gridData['grid_power_ld3'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_external_ct1']) ? $gridData['grid_external_ct1'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_external_ct2']) ? $gridData['grid_external_ct2'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_external_ct3']) ? $gridData['grid_external_ct3'] : 0}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>

                                                            <th>Daily Energy Buy</th>
                                                            <th>Daily energy sell</th>
                                                            <th>Total Energy Buy</th>
                                                            <th>Total Energy Sell</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{$gridData['daily_energy_purchased']}}</td>
                                                            <td>{{$gridData['daily_grid_feed_in']}}</td>
                                                            <td>{{$gridData['total_energy_purchased']}}</td>
                                                            <td>{{$gridData['total_grid_feed_in']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase6" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Electricity Consumption</h5>

                                            <div id="cardCollpase6" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Load Voltage L1</th>
                                                            <th>Load Voltage L2</th>
                                                            <th>Load Voltage L3</th>
                                                            <th>Total Consumption Apparent Power</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{isset($inverterData['loadvoltagel1']) ? $inverterData['loadvoltagel1'] : 0 }}</th>
                                                            <td>{{$inverterData['loadvoltagel2']}}</td>
                                                            <td>{{$inverterData['loadvoltagel3']}}</td>
                                                            <td>{{$inverterData['ConsumpApparentPower']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Load Frequency</th>
                                                            <th>Total consumption power</th>
                                                            <th>Daily consumption</th>
                                                            <th>Total Consumption</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterData['loadfrequency']}}</th>
                                                            <td>{{$inverterData['totalConsumptionPower']}}</td>
                                                            <td>{{$inverterData['dailyConsumptionEnergy']}}</td>
                                                            <td>{{$inverterData['totalConsumption']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                {{--                                            <div class="table-responsive">--}}
                                                {{--                                                <table class="table mb-0">--}}
                                                {{--                                                    <thead>--}}
                                                {{--                                                    <tr>--}}
                                                {{--                                                        <th>Consumption frequency</th>--}}
                                                {{--                                                        <th>Consumption active power R</th>--}}
                                                {{--                                                        <th>Total consumption energy</th>--}}
                                                {{--                                                        <th></th>--}}
                                                {{--                                                    </tr>--}}
                                                {{--                                                    </thead>--}}
                                                {{--                                                    <tbody>--}}
                                                {{--                                                    <tr>--}}
                                                {{--                                                        <th scope="row">{{$inverterData['consumptionFrequency']}}</th>--}}
                                                {{--                                                        <td>{{$inverterData['consumptionActivePowerR']}}</td>--}}
                                                {{--                                                        <td>{{$inverterData['totalConsumptionEnergy']}}</td>--}}
                                                {{--                                                        <td></td>--}}
                                                {{--                                                    </tr>--}}
                                                {{--                                                    </tbody>--}}
                                                {{--                                                </table>--}}
                                                {{--                                            </div>--}}
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase7" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Electricity Generation </h5>

                                            <div id="cardCollpase7" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Total Solar Power</th>
                                                            <th>Inverter Output L1</th>
                                                            <th>Inverter Output L2</th>
                                                            <th>Inverter Output L3</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterData['totalDCInputPower']}}</th>
                                                            <td>{{$inverterData['outputPowerl1']}}</td>
                                                            <td>{{$inverterData['outputPowerl2']}}</td>
                                                            <td>{{$inverterData['outputPowerl3']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Total Inverter Output Power</th>
                                                            <th>Commulative Power (Active)</th>
                                                            <th>Daily Production (Active)</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterData['TotaloutputPower']}}</th>
                                                            <td>{{$inverterData['totalProduction']}}</td>
                                                            <td>{{$inverterData['dailyProduction']}}</td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase8" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Temperature </h5>

                                            <div id="cardCollpase8" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Temperature- Battery</th>
                                                            <th>AC Temperature</th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterData['BatteryTemperature']}}</th>
                                                            <td>{{$inverterData['inverterTemperature']}}</td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->

                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase9" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Inverter Details ({{$inverterNo}}) </h5>

                                            <div id="cardCollpase9" class="collapse pt-3 show">
                                                <div class="row border_one_vt">
                                                    <div class="col-lg-4 for_table_vt">


                                                        <table class="table table-bordered mb-0 tablet">
                                                            <thead class="thead-light">
                                                            <tr>

                                                                <th> DC</th>
                                                                <th> Voltage</th>
                                                                <th> Current</th>
                                                                <th> Power</th>
                                                                <!-- <th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String1 </th> -->
                                                                <!-- <th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String2 </th> -->
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @if(count($inverterRMPPTData) > 0)
                                                                @foreach($inverterRMPPTData as $key => $inverterMppt)
                                                                    <tr class="vt_this">

                                                                        <td>PV{{$key + 1}}</td>

                                                                        <td>{{isset($inverterMppt->mppt_voltage) ? $inverterMppt->mppt_voltage : 0.00}}
                                                                            V
                                                                        </td>

                                                                        <td>{{isset($inverterMppt->mppt_current) ? $inverterMppt->mppt_current : 0.00}}
                                                                            A
                                                                        </td>

                                                                        <td>{{isset($inverterMppt->mppt_power) ? $inverterMppt->mppt_power : 0.00}}</td>

                                                                    </tr>
                                                                @endforeach
                                                            @else

                                                                <tr class="">

                                                                    <td>PV1</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>

                                                                <tr class="">

                                                                    <td>PV2</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>
                                                                <tr class="">

                                                                    <td>PV3</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>
                                                                <tr class="">

                                                                    <td>PV4</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>
                                                            @endif
                                                            </tbody>
                                                        </table>


                                                    </div>
                                                    <div class="col-lg-4  text-center">

                                                        <div class="img_center_vt"></div>
                                                        <img
                                                            src="{{ asset('assets/images/davice.jpg')}}" alt=""
                                                            class="py-3">
                                                        <table class="table card-text">


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

                                                                <td>{{$inverterData['ac_voltage_r']}}</td>

                                                                <td>{{$inverterData['ac_current_r']}}</td>

                                                                <td>{{$inverterData['ac_frequency_r']}}</td>

                                                            </tr>

                                                            <tr class="">

                                                                <td>S</td>

                                                                <td>{{$inverterData['ac_voltage_s']}}</td>

                                                                <td>{{$inverterData['ac_current_s']}}</td>

                                                                <td>{{$inverterData['ac_frequency_s']}}</td>

                                                            </tr>

                                                            <tr class="">

                                                                <td>T</td>

                                                                <td>{{$inverterData['ac_voltage_s']}}</td>

                                                                <td>{{$inverterData['ac_current_s']}}</td>

                                                                <td>{{$inverterData['ac_frequency_s']}}</td>

                                                            </tr>

                                                            </tbody>

                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase10" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Graphical Data</h5>

                                            <div id="cardCollpase10" class="collapse pt-3 show">
                                                <div class="to_bar_graph_vt">
                                                    <div class="left_multiselect_vt">
                                                        <div class="multiselect_VT">
                                                            <div id="checkpv_vt">
                                                                <label for="one"><input class="inverterDetailData"
                                                                                        name="inverterDetail[]"
                                                                                        value="dc-voltage-pv1"
                                                                                        type="checkbox"
                                                                                        id="one" checked
                                                                                        onchange="handleChange(this);"/>DC
                                                                    Voltage
                                                                    PV1</label>
                                                                <label for="inverterOne"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-voltage-pv2"
                                                                                                type="checkbox"
                                                                                                id="inverterOne"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Voltage
                                                                    PV2</label>
                                                                <label for="inverterTwo"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-voltage-pv3"
                                                                                                type="checkbox"
                                                                                                id="inverterTwo"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Voltage
                                                                    PV3</label>
                                                                <label for="inverterThree"><input class="inverterDetailData"
                                                                                                  name="inverterDetail[]"
                                                                                                  value="dc-voltage-pv4"
                                                                                                  type="checkbox"
                                                                                                  id="inverterThree"
                                                                                                  onchange="handleChange(this);"/>DC
                                                                    Voltage PV4</label>
                                                                <label for="inverterFour"><input class="inverterDetailData"
                                                                                                 name="inverterDetail[]"
                                                                                                 value="dc-current-pv1"
                                                                                                 type="checkbox"
                                                                                                 id="inverterFour" checked
                                                                                                 onchange="handleChange(this);"/>DC
                                                                    Current
                                                                    PV1</label>
                                                                <label for="inverterFive"><input class="inverterDetailData"
                                                                                                 name="inverterDetail[]"
                                                                                                 value="dc-current-pv2"
                                                                                                 type="checkbox"
                                                                                                 id="inverterFive"
                                                                                                 onchange="handleChange(this);"/>DC
                                                                    Current PV2</label>
                                                                <label for="inverterSix"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-current-pv3"
                                                                                                type="checkbox"
                                                                                                id="inverterSix"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Current
                                                                    PV3</label>
                                                                <label for="inverterSeven"><input class="inverterDetailData"
                                                                                                  name="inverterDetail[]"
                                                                                                  value="dc-current-pv4"
                                                                                                  type="checkbox"
                                                                                                  id="inverterSeven"
                                                                                                  onchange="handleChange(this);"/>DC
                                                                    Current PV4</label>
                                                                <label for="inverterEight"><input class="inverterDetailData"
                                                                                                  name="inverterDetail[]"
                                                                                                  value="dc-power-pv1"
                                                                                                  type="checkbox"
                                                                                                  id="inverterEight"
                                                                                                  onchange="handleChange(this);"/>DC
                                                                    Power
                                                                    PV1</label>
                                                                <label for="inverterNine"><input class="inverterDetailData"
                                                                                                 name="inverterDetail[]"
                                                                                                 value="dc-power-pv2"
                                                                                                 type="checkbox"
                                                                                                 id="one"
                                                                                                 onchange="handleChange(this);"/>DC
                                                                    Power
                                                                    PV2</label>
                                                                <label for="inverterTen"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-power-pv3"
                                                                                                type="checkbox"
                                                                                                id="inverterTen"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Power
                                                                    PV3</label>
                                                                <label for="inverterEleven"><input
                                                                        class="inverterDetailData"
                                                                        name="inverterDetail[]"
                                                                        value="dc-power-pv4" type="checkbox"
                                                                        id="inverterEleven" onchange="handleChange(this);"/>DC
                                                                    Power PV4</label>
                                                                <label for="inverterTwelve"><input
                                                                        class="inverterDetailData"
                                                                        name="inverterDetail[]"
                                                                        value="daily-grid-feed-in"
                                                                        type="checkbox" id="inverterTwelve"
                                                                        onchange="handleChange(this);"/>Daily grid
                                                                    feed in</label>
                                                                <label for="inverter13"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="ac-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter13"
                                                                                               onchange="handleChange(this);"/>AC
                                                                    Voltage
                                                                </label>
                                                                <label for="inverter14"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="ac-current"
                                                                                               type="checkbox"
                                                                                               id="inverter14"
                                                                                               onchange="handleChange(this);"/>AC
                                                                    Current
                                                                </label>
                                                                <label for="inverter15"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="ac-frequency"
                                                                                               type="checkbox"
                                                                                               id="inverter15"
                                                                                               onchange="handleChange(this);"/>AC
                                                                    Frequency </label>
                                                                <label for="inverter16"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="daily-energy-purchased"
                                                                                               type="checkbox"
                                                                                               id="inverter16"
                                                                                               onchange="handleChange(this);"/>Daily
                                                                    energy purchased</label>
                                                                <label for="inverter17"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="inverter-output-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter17"
                                                                                               onchange="handleChange(this);"/>Inverter
                                                                    output voltage</label>
                                                                <label for="inverter18"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="meter-active-power"
                                                                                               type="checkbox"
                                                                                               id="inverter18"
                                                                                               onchange="handleChange(this);"/>Meter
                                                                    active power</label>
                                                                <label for="inverter19"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="daily-production"
                                                                                               type="checkbox"
                                                                                               id="inverter19"
                                                                                               onchange="handleChange(this);"/>Daily
                                                                    production</label>
                                                                <label for="inverter20"><input class="inverterDetailData"
                                                                                               type="checkbox"
                                                                                               name="inverterDetail[]"
                                                                                               value="meter-total-active-power"
                                                                                               id="inverter20"
                                                                                               onchange="handleChange(this);"/>Meter
                                                                    total
                                                                    active
                                                                    power</label>
                                                                <label for="inverter21"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="total-grid-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter21"
                                                                                               onchange="handleChange(this);"/>Total
                                                                    grid voltage</label>
                                                                <label for="inverter22"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="grid-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter22"
                                                                                               onchange="handleChange(this);"/>Grid
                                                                    voltage </label>
                                                                @if($grid_type == "Three-phase")
                                                                    <label for="inverter35"><input class="inverterDetailData"
                                                                                                   name="inverterDetail[]"
                                                                                                   value="grid-voltage-l1"
                                                                                                   type="checkbox"
                                                                                                   id="inverter35"
                                                                                                   onchange="handleChange(this);"/>Grid
                                                                        voltage L1 </label>
                                                                    <label for="inverter36"><input class="inverterDetailData"
                                                                                                   name="inverterDetail[]"
                                                                                                   value="grid-voltage-l2"
                                                                                                   type="checkbox"
                                                                                                   id="inverter36"
                                                                                                   onchange="handleChange(this);"/>Grid
                                                                        voltage L2 </label>
                                                                    <label for="inverter37"><input class="inverterDetailData"
                                                                                                   name="inverterDetail[]"
                                                                                                   value="grid-voltage-l3"
                                                                                                   type="checkbox"
                                                                                                   id="inverter37"
                                                                                                   onchange="handleChange(this);"/>Grid
                                                                        voltage L3 </label>
                                                                @endif
                                                                <label for="inverter23"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="consumption-frequency"
                                                                                               type="checkbox"
                                                                                               id="inverter23"
                                                                                               onchange="handleChange(this);"/>Consumption
                                                                    frequency </label>
                                                                <label for="inverter24"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="consumption-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter24"
                                                                                               onchange="handleChange(this);"/>Consumption
                                                                    Voltage </label>
                                                                <label for="inverter25"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="grid-current"
                                                                                               type="checkbox"
                                                                                               id="inverter25"
                                                                                               onchange="handleChange(this);"/>Grid
                                                                    current </label>
                                                                <label for="inverter26"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="total-consumption-power"
                                                                                               type="checkbox"
                                                                                               id="inverter26"
                                                                                               onchange="handleChange(this);"/>Total
                                                                    consumption power </label>
                                                                <label for="inverter27"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="grid-frequency"
                                                                                               type="checkbox"
                                                                                               id="inverter27"
                                                                                               onchange="handleChange(this);"/>Grid
                                                                    frequency </label>
                                                                <label for="inverter28"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="consumption-active-power"
                                                                                               type="checkbox"
                                                                                               id="inverter28"
                                                                                               onchange="handleChange(this);"/>Consumption
                                                                    active power </label>
                                                                <label for="inverter29"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="phase-grid-power"
                                                                                               type="checkbox"
                                                                                               id="inverter29"
                                                                                               onchange="handleChange(this);"/>Phase
                                                                    grid power </label>
                                                                <label for="inverter30"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="daily-consumption"
                                                                                               type="checkbox"
                                                                                               id="inverter30"
                                                                                               onchange="handleChange(this);"/>Daily
                                                                    consumption </label>
                                                                <label for="inverter31"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="total-grid-power"
                                                                                               type="checkbox"
                                                                                               id="inverter31"
                                                                                               onchange="handleChange(this);"/>Total
                                                                    grid power </label>
                                                                <label for="inverter32">
                                                                    <input type="checkbox" class="inverterDetailData"
                                                                           id="inverter32" name="inverterDetail[]"
                                                                           value="meter-ac-current"
                                                                           onchange="handleChange(this);"/>Meter AC
                                                                    Current</label>
                                                                <label for="inverter33">
                                                                    <input type="checkbox" class="inverterDetailData"
                                                                           id="inverter33" name="inverterDetail[]"
                                                                           value="meter-ac-power"
                                                                           onchange="handleChange(this);"/>Meter AC
                                                                    Power</label>
                                                                <label for="inverter34">
                                                                    <input type="checkbox" class="inverterDetailData"
                                                                           id="inverter34" name="inverterDetail[]"
                                                                           value="ac-temperature"
                                                                           onchange="handleChange(this);"/>AC
                                                                    Temperature</label>
                                                                {{--                                                            <label for="three18"><input class="inverterDetailData"--}}
                                                                {{--                                                                                        name="inverterDetail[]"--}}
                                                                {{--                                                                                        value="ac-temperature"--}}
                                                                {{--                                                                                        type="checkbox" id="three18"/>AC--}}
                                                                {{--                                                                temperature </label>--}}
                                                                <div class="btn_int_clear_vt">
                                                                    <button class="clear_vt_btn"
                                                                            onclick="clearInverterData()">
                                                                        Clear
                                                                    </button>
                                                                    <button class="btn_add_vt ml-2 mr-2" type="submit"
                                                                            id="searchInverterCheckBox">Search
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            {{--                                                        </div>--}}
                                                        </div>

                                                        <div class="multiselect_VT">

                                                            <div id="check_monthly_vt">
                                                                <label for="one"><input class="inverterDetailData"
                                                                                        name="inverterMonthlyDetail[]"
                                                                                        value="solar-production"
                                                                                        type="checkbox"
                                                                                        id="one" checked
                                                                                        onchange="handleChange(this);"/>
                                                                    Solar Production</label>
                                                                <label for="inverterOne"><input class="inverterDetailData"
                                                                                                name="inverterMonthlyDetail[]"
                                                                                                value="energy-purchased"
                                                                                                type="checkbox"
                                                                                                id="inverterOne"
                                                                                                onchange="handleChange(this);"/>Energy
                                                                    Purchased</label>
                                                                <label for="inverterTwo"><input class="inverterDetailData"
                                                                                                name="inverterMonthlyDetail[]"
                                                                                                value="grid-feed-in"
                                                                                                type="checkbox"
                                                                                                id="inverterTwo"
                                                                                                onchange="handleChange(this);"/>Grid
                                                                    Feed In</label>
                                                                <label for="inverterThree"><input class="inverterDetailData"
                                                                                                  name="inverterMonthlyDetail[]"
                                                                                                  value="consumption-energy"
                                                                                                  type="checkbox"
                                                                                                  id="inverterThree"
                                                                                                  onchange="handleChange(this);"/>Consumption
                                                                    Energy</label>
                                                                <div class="btn_int_clear_vt">
                                                                    <button class="clear_vt_btn"
                                                                            onclick="clearInverterData()">
                                                                        Clear
                                                                    </button>
                                                                    <button class="btn_add_vt ml-2 mr-2" type="button"
                                                                            onclick="inverterMonthlySearchData()">Search
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn_add_vt col-6"
                                                                    id="inverter-select-parameter">Select Parameter
                                                            </button>

                                                            <button type="button"
                                                                    data-href="{{route('export.inverter.hybrid.graph', ['plantID'=>$plantId, 'Date'=>'2021-07-08'])}}"
                                                                    id="export-inverter-graph"
                                                                    class="ml-3 btn_add_vt btn-success btn-sm"
                                                                    onclick="exportTasks(event.target);">Export CSV
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="left_month_date_vt">
                                                        <div class="day_month_calender_vt">
                                                            <div class="day_month_year_vt"
                                                                 id="inverter_day_month_year_vt_day" style="">
                                                                <button><i id="inverterGraphPreviousDay"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div
                                                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-inverter mt10">
                                                                    <input type="text" autocomplete="off"
                                                                           name="inverterGraphDay" id="inverterGraphDay"
                                                                           placeholder="Select"
                                                                           class="c-datepicker-data-input" value="">
                                                                </div>
                                                                <button><i id="inverterGraphForwardDay"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="inverter_day_month_year_vt_month"
                                                                 style="display: none;">
                                                                <button><i id="inverterGraphPreviousMonth"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-inverter mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="inverterGraphMonth"
                                                                               placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="inverterGraphForwardMonth"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="inverter_day_month_year_vt_year"
                                                                 style="display: none;">
                                                                <button><i id="inverterGraphPreviousYear"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-inverter mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="inverterGraphYear" placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="inverterGraphForwardYear"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_my_btn_vt" id="inverter_day_my_btn_vt">
                                                                <button class="day_bt_vt active" id="day">day</button>
                                                                <button class="month_btn_vt" id="month">month</button>
                                                                <button class="month_btn_vt" id="year">Year</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{--                                            <canvas id="container_inverter_graph_data_vt"--}}
                                                {{--                                                    style="width:100%; height: 300px;"></canvas>--}}
                                                <div class="history-card-box" dir="ltr"
                                                     id="container_inverter_graph_data_vt">
                                                    <div id="inverterContainer"></div>
                                                    <br>
                                                </div>

                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <!-- end card-box -->
                                </div> <!-- end col -->
                            @else
                                <div class="tabel_inv_vt">
                                    <h4 class="header_title_vt">Inverter Summary</h4>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase2" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Basic Information</h5>

                                            <div id="cardCollpase2" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Serial number</th>
                                                            @if($meterType  == 'Solis-Cloud')
                                                            <th>Product Model</th>
                                                            @else
                                                            <th>General settings</th>
                                                            @endif
                                                            <th>Inverter Type</th>
                                                            <th>Rated power</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{isset($inverterVersionData['serial_number']) ? $inverterVersionData['serial_number'] : 0}}</th>
                                                            <td>{{isset($inverterVersionData['general_settings']) ? $inverterVersionData['general_settings'] : 0}}</td>
                                                            @if($meterType  == 'Solis-Cloud')
                                                                <td>{{ isset($grid_type) ? $grid_type : 0 }}</td>
                                                            @else
                                                                <td>{{ isset($gridData['grid_type']) ? $gridData['grid_type'] : 0 }}</td>
                                                            @endif
                                                            <td>{{isset($inverterVersionData['rated_power']) ? $inverterVersionData['rated_power'] : 0}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase3" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Version Information</h5>

                                            <div id="cardCollpase3" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Lithium Battery Version Number</th>
                                                              @if($meterType  == 'Solis-Cloud')
                                                              <th>Software Version</th>
                                                              <th>Grid Standard</th>
                                                              @else
                                                              <th>Protocol version</th>
                                                              <th>HMI</th>
                                                              <th>MAIN_1</th>
                                                              @endif
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            @if($meterType != 'Solis-Cloud')<th scope="row">{{isset($inverterVersionData['protocol_version']) ? $inverterVersionData['protocol_version'] : 0}}</th>@endif
                                                            <td>{{isset($inverterVersionData['lithium_battery_version']) ? $inverterVersionData['lithium_battery_version'] : 0}}</td>
                                                            <td>{{isset( $inverterVersionData['HMI']) ? $inverterVersionData['HMI'] : 0}}</td>
                                                            <td>{{isset($inverterVersionData['main_1']) ? $inverterVersionData['main_1'] : 0}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if($meterType != 'Solis-Cloud')
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>MAIN_2</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{isset($inverterVersionData['main_2']) ? $inverterVersionData['main_2'] : 0}}</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase4" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">State</h5>

                                            <div id="cardCollpase4" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Inverter status</th>
                                                            <th>PV charging control status</th>
                                                            <th>Relay status generator side</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">
                                                                @if($inverterVersionData['inverter_status'] == 'Y')
                                                                    <img
                                                                        src="{{ asset('assets/images/icon_plant_check_vt.svg')}}"
                                                                        alt="check"
                                                                        title="Online">
                                                                @else
                                                                    <img src="{{ asset('assets/images/icon_plant_vt.svg')}}"
                                                                         alt="check"
                                                                         title="Offline">
                                                                @endif
                                                            </th>
                                                            <td>2344567</td>
                                                            <td>4.3kWh</td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase5" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Power Grid</h5>

                                            <div id="cardCollpase5" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Grid Frequency</th>
                                                            <th>Grid Type</th>
                                                            <th>Grid Voltage L/N</th>
                                                            <th>Total Grid Power</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>

                                                            <td>{{isset($gridData['grid_frequency']) ? $gridData['grid_frequency'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_type']) ? $gridData['grid_type'] : 0}}</td>
                                                            <td>{{isset($gridData['grid_voltage_ln']) ? $gridData['grid_voltage_ln'] : 0}}</td>
                                                            <td>{{isset($gridData['total_grid_power']) ? $gridData['total_grid_power'] : 0}} </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>

                                                            <th>Grid Current L/N</th>
                                                            <th>External CT Current L/N</th>
                                                            <th>External CT Power L/N</th>
                                                            <th>Cumulative Grid Feed-in</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{isset($gridData['grid_current_ln']) ? $gridData['grid_current_ln'] : 0 }}</td>
                                                            <td>{{isset($gridData['external_ct_current_ln']) ? $gridData['external_ct_current_ln'] : 0}}</td>
                                                            <td>{{ isset($gridData['external_ct_power_ln']) ? $gridData['external_ct_power_ln'] :0}}</td>
                                                            <th scope="row">{{isset($gridData['total_grid_feed_in']) ? $gridData['total_grid_feed_in'] : 0}}</th>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>

                                                            <th>Cumulative Energy Purchased</th>
                                                            <th>Daily Grid Feed-in</th>
                                                            <th>Daily Energy Purchased</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>

                                                            <td>{{isset($gridData['total_energy_purchased']) ? $gridData['total_energy_purchased'] : 0}}</td>
                                                            <td>{{isset($gridData['daily_grid_feed_in']) ? $gridData['daily_grid_feed_in']  : 0}}</td>
                                                            <td>{{isset($gridData['daily_energy_purchased']) ? $gridData['daily_energy_purchased'] : 0}}</td>
                                                            <th scope="row"></th>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
{{--                                                <div class="table-responsive">--}}
{{--                                                    <table class="table mb-0">--}}
{{--                                                        <thead>--}}
{{--                                                        <tr>--}}

{{--                                                            <th>Total grid power</th>--}}
{{--                                                            <th>Meter total active power</th>--}}
{{--                                                            <th></th>--}}
{{--                                                            <th></th>--}}
{{--                                                        </tr>--}}
{{--                                                        </thead>--}}
{{--                                                        <tbody>--}}
{{--                                                        <tr>--}}
{{--                                                            <td>{{isset($gridData['total_grid_power']) ? $gridData['total_grid_power'] : 0}}</td>--}}
{{--                                                            <td>{{isset($gridData['meter_total_active_power']) ? $gridData['meter_total_active_power'] : 0}}</td>--}}
{{--                                                            <td></td>--}}
{{--                                                            <td></td>--}}
{{--                                                        </tr>--}}
{{--                                                        </tbody>--}}
{{--                                                    </table>--}}
{{--                                                </div>--}}
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase6" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Electricity Consumption</h5>

                                            <div id="cardCollpase6" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Load Voltage L/N</th>
                                                            <th>Total consumption power</th>
                                                            <th>Daily consumption energy</th>
                                                            <th>Cumulative Consumption</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{isset($inverterData['loadvoltageln']) ? $inverterData['loadvoltageln']  : 0}}</th>
                                                            <td>{{$inverterData['totalConsumptionPower']}}</td>
                                                            <td>{{$inverterData['dailyConsumptionEnergy']}}</td>
                                                            <td>{{$inverterData['totalConsumptionEnergy']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
{{--                                                <div class="table-responsive">--}}
{{--                                                    <table class="table mb-0">--}}
{{--                                                        <thead>--}}
{{--                                                        <tr>--}}
{{--                                                            <th>Consumption frequency</th>--}}
{{--                                                            <th>Consumption active power R</th>--}}
{{--                                                            <th>Total consumption energy</th>--}}
{{--                                                            <th></th>--}}
{{--                                                        </tr>--}}
{{--                                                        </thead>--}}
{{--                                                        <tbody>--}}
{{--                                                        <tr>--}}
{{--                                                            <th scope="row">{{$inverterData['consumptionFrequency']}}</th>--}}
{{--                                                            <td>{{$inverterData['consumptionActivePowerR']}}</td>--}}
{{--                                                            <td>{{$inverterData['totalConsumptionEnergy']}}</td>--}}
{{--                                                            <td></td>--}}
{{--                                                        </tr>--}}
{{--                                                        </tbody>--}}
{{--                                                    </table>--}}
{{--                                                </div>--}}
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase7" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Electricity Generation </h5>

                                            <div id="cardCollpase7" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Total DC input power</th>
                                                            <th>Inverter Output Power L/N</th>
                                                            <th>Total production</th>
                                                            <th>Daily production</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterData['totalDCInputPower']}}</th>
                                                            <td>{{isset($inverterData['inverteroutputpowerln']) ? $inverterData['inverteroutputpowerln'] : 0}}</td>
                                                            <td>{{$inverterData['totalProduction']}}</td>
                                                            <td>{{$inverterData['dailyProduction']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Generator Input as Load Output Enable</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{isset($inverterData['GeneInputLoadEnable']) ? $inverterData['GeneInputLoadEnable'] : 0}}</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    @if($meterType  != 'Solis-Cloud')
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase8" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Temperature </h5>
                                            <div id="cardCollpase8" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Battery Temperature</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['BatteryTemperature']}}</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                      
                                        </div>
                                    </div>
                                     @endif
                                     <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase9" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Inverter Details ({{$inverterNo}}) </h5>

                                            <div id="cardCollpase9" class="collapse pt-3 show">
                                                <div class="row border_one_vt">
                                                    <div class="col-lg-4 for_table_vt">


                                                        <table class="table table-bordered mb-0 tablet">
                                                            <thead class="thead-light">
                                                            <tr>

                                                                <th> DC</th>
                                                                <th> Voltage</th>
                                                                <th> Current</th>
                                                                <th> Power</th>
                                                                <!-- <th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String1 </th> -->
                                                                <!-- <th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String2 </th> -->
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @if(count($inverterRMPPTData) > 0)
                                                                @foreach($inverterRMPPTData as $key => $inverterMppt)
                                                                    <tr class="vt_this">

                                                                        <td>PV{{$key + 1}}</td>

                                                                        <td>{{isset($inverterMppt->mppt_voltage) ? $inverterMppt->mppt_voltage : 0.00}}
                                                                            V
                                                                        </td>

                                                                        <td>{{isset($inverterMppt->mppt_current) ? $inverterMppt->mppt_current : 0.00}}
                                                                            A
                                                                        </td>

                                                                        <td>{{isset($inverterMppt->mppt_power) ? $inverterMppt->mppt_power : 0.00}}</td>

                                                                    </tr>
                                                                @endforeach
                                                            @else

                                                                <tr class="">

                                                                    <td>PV1</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>

                                                                <tr class="">

                                                                    <td>PV2</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>
                                                                <tr class="">

                                                                    <td>PV3</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>
                                                                <tr class="">

                                                                    <td>PV4</td>

                                                                    <td>0 V</td>

                                                                    <td>0 A</td>

                                                                    <td>0W</td>

                                                                </tr>
                                                            @endif
                                                            </tbody>
                                                        </table>


                                                    </div>
                                                    <div class="col-lg-4  text-center">

                                                        <div class="img_center_vt"></div>
                                                        <img
                                                            src="{{ asset('assets/images/davice.jpg')}}" alt=""
                                                            class="py-3">
                                                        <table class="table card-text">


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

                                                                <td>{{$inverterData['ac_voltage_r']}}</td>

                                                                <td>{{$inverterData['ac_current_r']}}</td>

                                                                <td>{{$inverterData['ac_frequency_r']}}</td>

                                                            </tr>

                                                            <tr class="">

                                                                <td>S</td>

                                                                <td>{{$inverterData['ac_voltage_s']}}</td>

                                                                <td>{{$inverterData['ac_current_s']}}</td>

                                                                <td>{{$inverterData['ac_frequency_s']}}</td>

                                                            </tr>


                                                            </tbody>

                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase10" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Graphical Data</h5>

                                            <div id="cardCollpase10" class="collapse pt-3 show">
                                                <div class="to_bar_graph_vt">
                                                    <div class="left_multiselect_vt">
                                                        <div class="multiselect_VT">
                                                            {{--                                                        <div class="selectBox_vt" onclick="showCheckbox()">--}}
                                                            {{--                                                            <select>--}}
                                                            {{--                                                                <option>Select Parameter</option>--}}
                                                            {{--                                                            </select>--}}
                                                            {{--                                                            <div class="overSelect_vt"></div>--}}
                                                            {{--                                                        </div>--}}
                                                            {{--                                                        <div id="inverter-select-parameter">--}}
                                                            <div id="checkpv_vt">
                                                                <label for="one"><input class="inverterDetailData"
                                                                                        name="inverterDetail[]"
                                                                                        value="dc-voltage-pv1"
                                                                                        type="checkbox"
                                                                                        id="one" checked
                                                                                        onchange="handleChange(this);"/>DC
                                                                    Voltage
                                                                    PV1</label>
                                                                <label for="inverterOne"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-voltage-pv2"
                                                                                                type="checkbox"
                                                                                                id="inverterOne"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Voltage
                                                                    PV2</label>
                                                                <label for="inverterTwo"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-voltage-pv3"
                                                                                                type="checkbox"
                                                                                                id="inverterTwo"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Voltage
                                                                    PV3</label>
                                                                <label for="inverterThree"><input class="inverterDetailData"
                                                                                                  name="inverterDetail[]"
                                                                                                  value="dc-voltage-pv4"
                                                                                                  type="checkbox"
                                                                                                  id="inverterThree"
                                                                                                  onchange="handleChange(this);"/>DC
                                                                    Voltage PV4</label>
                                                                <label for="inverterFour"><input class="inverterDetailData"
                                                                                                 name="inverterDetail[]"
                                                                                                 value="dc-current-pv1"
                                                                                                 type="checkbox"
                                                                                                 id="inverterFour" checked
                                                                                                 onchange="handleChange(this);"/>DC
                                                                    Current
                                                                    PV1</label>
                                                                <label for="inverterFive"><input class="inverterDetailData"
                                                                                                 name="inverterDetail[]"
                                                                                                 value="dc-current-pv2"
                                                                                                 type="checkbox"
                                                                                                 id="inverterFive"
                                                                                                 onchange="handleChange(this);"/>DC
                                                                    Current PV2</label>
                                                                <label for="inverterSix"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-current-pv3"
                                                                                                type="checkbox"
                                                                                                id="inverterSix"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Current
                                                                    PV3</label>
                                                                <label for="inverterSeven"><input class="inverterDetailData"
                                                                                                  name="inverterDetail[]"
                                                                                                  value="dc-current-pv4"
                                                                                                  type="checkbox"
                                                                                                  id="inverterSeven"
                                                                                                  onchange="handleChange(this);"/>DC
                                                                    Current PV4</label>
                                                                <label for="inverterEight"><input class="inverterDetailData"
                                                                                                  name="inverterDetail[]"
                                                                                                  value="dc-power-pv1"
                                                                                                  type="checkbox"
                                                                                                  id="inverterEight"
                                                                                                  onchange="handleChange(this);"/>DC
                                                                    Power
                                                                    PV1</label>
                                                                <label for="inverterNine"><input class="inverterDetailData"
                                                                                                 name="inverterDetail[]"
                                                                                                 value="dc-power-pv2"
                                                                                                 type="checkbox"
                                                                                                 id="one"
                                                                                                 onchange="handleChange(this);"/>DC
                                                                    Power
                                                                    PV2</label>
                                                                <label for="inverterTen"><input class="inverterDetailData"
                                                                                                name="inverterDetail[]"
                                                                                                value="dc-power-pv3"
                                                                                                type="checkbox"
                                                                                                id="inverterTen"
                                                                                                onchange="handleChange(this);"/>DC
                                                                    Power
                                                                    PV3</label>
                                                                <label for="inverterEleven"><input
                                                                        class="inverterDetailData"
                                                                        name="inverterDetail[]"
                                                                        value="dc-power-pv4" type="checkbox"
                                                                        id="inverterEleven" onchange="handleChange(this);"/>DC
                                                                    Power PV4</label>
                                                                <label for="inverterTwelve"><input
                                                                        class="inverterDetailData"
                                                                        name="inverterDetail[]"
                                                                        value="daily-grid-feed-in"
                                                                        type="checkbox" id="inverterTwelve"
                                                                        onchange="handleChange(this);"/>Daily grid
                                                                    feed in</label>
                                                                <label for="inverter13"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="ac-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter13"
                                                                                               onchange="handleChange(this);"/>AC
                                                                    Voltage
                                                                </label>
                                                                <label for="inverter14"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="ac-current"
                                                                                               type="checkbox"
                                                                                               id="inverter14"
                                                                                               onchange="handleChange(this);"/>AC
                                                                    Current
                                                                </label>
                                                                <label for="inverter15"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="ac-frequency"
                                                                                               type="checkbox"
                                                                                               id="inverter15"
                                                                                               onchange="handleChange(this);"/>AC
                                                                    Frequency </label>
                                                                <label for="inverter16"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="daily-energy-purchased"
                                                                                               type="checkbox"
                                                                                               id="inverter16"
                                                                                               onchange="handleChange(this);"/>Daily
                                                                    energy purchased</label>
                                                                <label for="inverter17"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="inverter-output-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter17"
                                                                                               onchange="handleChange(this);"/>Inverter
                                                                    output voltage</label>
                                                                <label for="inverter18"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="meter-active-power"
                                                                                               type="checkbox"
                                                                                               id="inverter18"
                                                                                               onchange="handleChange(this);"/>Meter
                                                                    active power</label>
                                                                <label for="inverter19"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="daily-production"
                                                                                               type="checkbox"
                                                                                               id="inverter19"
                                                                                               onchange="handleChange(this);"/>Daily
                                                                    production</label>
                                                                <label for="inverter20"><input class="inverterDetailData"
                                                                                               type="checkbox"
                                                                                               name="inverterDetail[]"
                                                                                               value="meter-total-active-power"
                                                                                               id="inverter20"
                                                                                               onchange="handleChange(this);"/>Meter
                                                                    total
                                                                    active
                                                                    power</label>
                                                                <label for="inverter21"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="total-grid-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter21"
                                                                                               onchange="handleChange(this);"/>Total
                                                                    grid voltage</label>
                                                                <label for="inverter22"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="grid-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter22"
                                                                                               onchange="handleChange(this);"/>Grid
                                                                    voltage </label>
                                                                <label for="inverter23"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="consumption-frequency"
                                                                                               type="checkbox"
                                                                                               id="inverter23"
                                                                                               onchange="handleChange(this);"/>Consumption
                                                                    frequency </label>
                                                                <label for="inverter24"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="consumption-voltage"
                                                                                               type="checkbox"
                                                                                               id="inverter24"
                                                                                               onchange="handleChange(this);"/>Consumption
                                                                    Voltage </label>
                                                                <label for="inverter25"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="grid-current"
                                                                                               type="checkbox"
                                                                                               id="inverter25"
                                                                                               onchange="handleChange(this);"/>Grid
                                                                    current </label>
                                                                <label for="inverter26"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="total-consumption-power"
                                                                                               type="checkbox"
                                                                                               id="inverter26"
                                                                                               onchange="handleChange(this);"/>Total
                                                                    consumption power </label>
                                                                <label for="inverter27"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="grid-frequency"
                                                                                               type="checkbox"
                                                                                               id="inverter27"
                                                                                               onchange="handleChange(this);"/>Grid
                                                                    frequency </label>
                                                                <label for="inverter28"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="consumption-active-power"
                                                                                               type="checkbox"
                                                                                               id="inverter28"
                                                                                               onchange="handleChange(this);"/>Consumption
                                                                    active power </label>
                                                                <label for="inverter29"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="phase-grid-power"
                                                                                               type="checkbox"
                                                                                               id="inverter29"
                                                                                               onchange="handleChange(this);"/>Phase
                                                                    grid power </label>
                                                                <label for="inverter30"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="daily-consumption"
                                                                                               type="checkbox"
                                                                                               id="inverter30"
                                                                                               onchange="handleChange(this);"/>Daily
                                                                    consumption </label>
                                                                <label for="inverter31"><input class="inverterDetailData"
                                                                                               name="inverterDetail[]"
                                                                                               value="total-grid-power"
                                                                                               type="checkbox"
                                                                                               id="inverter31"
                                                                                               onchange="handleChange(this);"/>Total
                                                                    grid power </label>
                                                                <label for="inverter32">
                                                                    <input type="checkbox" class="inverterDetailData"
                                                                           id="inverter32" name="inverterDetail[]"
                                                                           value="meter-ac-current"
                                                                           onchange="handleChange(this);"/>Meter AC
                                                                    Current</label>
                                                                <label for="inverter33">
                                                                    <input type="checkbox" class="inverterDetailData"
                                                                           id="inverter33" name="inverterDetail[]"
                                                                           value="meter-ac-power"
                                                                           onchange="handleChange(this);"/>Meter AC
                                                                    Power</label>
                                                                <label for="inverter34">
                                                                    <input type="checkbox" class="inverterDetailData"
                                                                           id="inverter34" name="inverterDetail[]"
                                                                           value="ac-temperature"
                                                                           onchange="handleChange(this);"/>AC
                                                                    Temperature</label>
                                                                {{--                                                            <label for="three18"><input class="inverterDetailData"--}}
                                                                {{--                                                                                        name="inverterDetail[]"--}}
                                                                {{--                                                                                        value="ac-temperature"--}}
                                                                {{--                                                                                        type="checkbox" id="three18"/>AC--}}
                                                                {{--                                                                temperature </label>--}}
                                                                <div class="btn_int_clear_vt">
                                                                    <button class="clear_vt_btn"
                                                                            onclick="clearInverterData()">
                                                                        Clear
                                                                    </button>
                                                                    <button class="btn_add_vt ml-2 mr-2" type="submit"
                                                                            id="searchInverterCheckBox">Search
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            {{--                                                        </div>--}}
                                                        </div>

                                                        <div class="multiselect_VT">

                                                            <div id="check_monthly_vt">
                                                                <label for="one"><input class="inverterDetailData"
                                                                                        name="inverterMonthlyDetail[]"
                                                                                        value="solar-production"
                                                                                        type="checkbox"
                                                                                        id="one" checked
                                                                                        onchange="handleChange(this);"/>
                                                                    Solar Production</label>
                                                                <label for="inverterOne"><input class="inverterDetailData"
                                                                                                name="inverterMonthlyDetail[]"
                                                                                                value="energy-purchased"
                                                                                                type="checkbox"
                                                                                                id="inverterOne"
                                                                                                onchange="handleChange(this);"/>Energy
                                                                    Purchased</label>
                                                                <label for="inverterTwo"><input class="inverterDetailData"
                                                                                                name="inverterMonthlyDetail[]"
                                                                                                value="grid-feed-in"
                                                                                                type="checkbox"
                                                                                                id="inverterTwo"
                                                                                                onchange="handleChange(this);"/>Grid
                                                                    Feed In</label>
                                                                <label for="inverterThree"><input class="inverterDetailData"
                                                                                                  name="inverterMonthlyDetail[]"
                                                                                                  value="consumption-energy"
                                                                                                  type="checkbox"
                                                                                                  id="inverterThree"
                                                                                                  onchange="handleChange(this);"/>Consumption
                                                                    Energy</label>
                                                                <div class="btn_int_clear_vt">
                                                                    <button class="clear_vt_btn"
                                                                            onclick="clearInverterData()">
                                                                        Clear
                                                                    </button>
                                                                    <button class="btn_add_vt ml-2 mr-2" type="button"
                                                                            onclick="inverterMonthlySearchData()">Search
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn_add_vt col-6"
                                                                    id="inverter-select-parameter">Select Parameter
                                                            </button>

                                                            <button type="button"
                                                                    data-href="{{route('export.inverter.hybrid.graph', ['plantID'=>$plantId, 'Date'=>'2021-07-08'])}}"
                                                                    id="export-inverter-graph"
                                                                    class="ml-3 btn_add_vt btn-success btn-sm"
                                                                    onclick="exportTasks(event.target);">Export CSV
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="left_month_date_vt">
                                                        <div class="day_month_calender_vt">
                                                            <div class="day_month_year_vt"
                                                                 id="inverter_day_month_year_vt_day" style="">
                                                                <button><i id="inverterGraphPreviousDay"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div
                                                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-inverter mt10">
                                                                    <input type="text" autocomplete="off"
                                                                           name="inverterGraphDay" id="inverterGraphDay"
                                                                           placeholder="Select"
                                                                           class="c-datepicker-data-input" value="">
                                                                </div>
                                                                <button><i id="inverterGraphForwardDay"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="inverter_day_month_year_vt_month"
                                                                 style="display: none;">
                                                                <button><i id="inverterGraphPreviousMonth"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-inverter mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="inverterGraphMonth"
                                                                               placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="inverterGraphForwardMonth"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="inverter_day_month_year_vt_year"
                                                                 style="display: none;">
                                                                <button><i id="inverterGraphPreviousYear"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-inverter mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="inverterGraphYear" placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="inverterGraphForwardYear"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_my_btn_vt" id="inverter_day_my_btn_vt">
                                                                <button class="day_bt_vt active" id="day">day</button>
                                                                <button class="month_btn_vt" id="month">month</button>
                                                                <button class="month_btn_vt" id="year">Year</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{--                                            <canvas id="container_inverter_graph_data_vt"--}}
                                                {{--                                                    style="width:100%; height: 300px;"></canvas>--}}
                                                <div class="history-card-box" dir="ltr"
                                                     id="container_inverter_graph_data_vt">
                                                    <div id="inverterContainer"></div>
                                                    <br>
                                                </div>

                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <!-- end card-box -->
                                </div> <!-- end col -->
                            @endif

                        </div>

                        @if($grid_type == "Three-phase")
                            <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="tabel_inv_vt">
                                    <h4 class="header_title_vt">Battery summary</h4>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase21" role="button"
                                                   aria-expanded="false"
                                                   aria-controls="cardCollpase1"><i class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Battery Information</h5>

                                            <div id="cardCollpase21" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Battery status</th>
                                                            <th>Battery voltage</th>
                                                            <th>Battery current</th>
                                                            <th>Battery power</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['battery_status']}}</th>
                                                            <td>{{$batteryData['battery_voltage']}}</td>
                                                            <td>{{$batteryData['battery_current']}}</td>
                                                            <td>{{$batteryData['battery_power']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Soc</th>
                                                            <th>Total charging energy</th>
                                                            <th>Daily charging energy</th>
                                                            <th>Total discharging energy</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{$batteryData['soc']}}</td>
                                                            <th scope="row">{{$batteryData['total_charging_energy']}}</th>
                                                            <td>{{$batteryData['daily_charging_energy']}}</td>
                                                            <td>{{$batteryData['total_discharging_energy']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Daily discharging energy</th>
                                                            <th>Battery charging type</th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['daily_discharging_energy']}}</th>
                                                            <td>{{$batteryData['battery_type']}}</td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase22" role="button"
                                                   aria-expanded="false"
                                                   aria-controls="cardCollpase1"><i class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">BMS Information</h5>

                                            <div id="cardCollpase22" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>BMS Charge Voltage</th>
                                                            <th>BMS Discharge Voltage</th>
                                                            <th>BMS Charge Current limit</th>
                                                            <th>BMS Discharge Current limit</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['bms_voltage']}}</th>
                                                            <td>{{$batteryData['bms_discharge_voltage']}}</td>
                                                            <td>{{$batteryData['bms_current_limiting_charging']}}</td>
                                                            <td>{{$batteryData['bms_battery_current_limiting_discharging']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>BMS Soc</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['battery_bms_soc']}}</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase8" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Temperature </h5>

                                            <div id="cardCollpase8" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Temperature- Battery</th>
                                                            <th>DC Temperature</th>
                                                            <th>AC Temperature</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$inverterData['BatteryTemperature']}}</th>
                                                            <td>{{$inverterData['DCTemperature']}}</td>
                                                            <td>{{$inverterData['inverterTemperature']}}</td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase10" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Graphical Data</h5>

                                            <div id="cardCollpase10" class="collapse pt-3 show">
                                                <div class="to_bar_graph_vt">
                                                    <div class="left_multiselect_vt">
                                                        {{--                                                    <div class="multiselect_VT">--}}
                                                        {{--                                                        <div class="selectBox_vt" onclick="showCheckone()">--}}
                                                        {{--                                                        <div class="selectBox_vt" id="inverter-graph-data">--}}

                                                        {{--                                                            <div id="inverter-graph-data" class="overSelect_vt"></div>--}}
                                                        {{--                                                        </div>--}}
                                                        {{--                                                    </div>--}}
                                                        <div id="checkone_vt">
                                                            {{--                                                        <div id="inverter-battery-data">--}}
                                                            <label for="battery1">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery1"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-voltage" checked
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                voltage</label>
                                                            <label for="battery2">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery2"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="daily-charging-energy"
                                                                       onchange="handleBatteryChange(this);"/>Daily
                                                                charging energy</label>
                                                            <label for="battery3">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery3"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-current" checked
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                current </label>
                                                            <label for="battery4">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery4"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="daily-discharging-energy"
                                                                       onchange="handleBatteryChange(this);"/>Daily
                                                                discharging energy</label>
                                                            <label for="battery5">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery5"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-power"
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                power</label>
                                                            <label for="battery6">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery6"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-charging-voltage"
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                charging voltage</label>
                                                            <label for="battery7">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery7"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="soc"
                                                                       onchange="handleBatteryChange(this);"/>Soc</label>
                                                            <label for="battery8">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery8"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="bms-temperature"
                                                                       onchange="handleBatteryChange(this);"/>BMS
                                                                temperature</label>
                                                            <label for="battery9">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery10"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="bms-voltage"
                                                                       onchange="handleBatteryChange(this);"/>BMS
                                                                voltage</label>
                                                            <label for="battery11">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery11"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-temperature"
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                temperature</label>
                                                            <label for="battery12">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery12"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="bms-current"
                                                                       onchange="handleBatteryChange(this);"/>BMS
                                                                current</label>

                                                            {{--                                                            <button class="clear_vt_btn" onclick="clearBatteryData()">Clear</button>--}}
                                                            {{--                                                            <button class="btn_add_vt ml-2 mr-2" type="submit"--}}
                                                            {{--                                                                    id="searchBatteryCheckBox">Search--}}
                                                            {{--                                                            </button>--}}
                                                            <div class="btn_int_clear_vt">
                                                                <button class="clear_vt_btn"
                                                                        onclick="clearBatteryData()">
                                                                    Clear
                                                                </button>
                                                                <button class="btn_add_vt ml-2 mr-2" type="submit"
                                                                        id="searchBatteryCheckBox">Search
                                                                </button>
                                                            </div>
                                                            {{--                                                        </div>--}}

                                                        </div>
                                                        <div id="check_battery_monthly_vt">
                                                            {{--                                                        <div id="inverter-battery-data">--}}
                                                            <label for="battery1">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery1"
                                                                       name="inverterBatteryMonthlyCheckBoxArray[]"
                                                                       value="battery-charge" checked
                                                                       onchange="handleMonthlyBatteryChange(this);"/>Charging
                                                                Energy</label>
                                                            <label for="battery2">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery2"
                                                                       name="inverterBatteryMonthlyCheckBoxArray[]"
                                                                       value="battery-discharge"
                                                                       onchange="handleMonthlyBatteryChange(this);"/>Discharging
                                                                Energy</label>


                                                            {{--                                                            <button class="clear_vt_btn" onclick="clearBatteryData()">Clear</button>--}}
                                                            {{--                                                            <button class="btn_add_vt ml-2 mr-2" type="submit"--}}
                                                            {{--                                                                    id="searchBatteryCheckBox">Search--}}
                                                            {{--                                                            </button>--}}
                                                            <div class="btn_int_clear_vt">
                                                                <button class="clear_vt_btn"
                                                                        onclick="clearBatteryData()">
                                                                    Clear
                                                                </button>
                                                                <button class="btn_add_vt ml-2 mr-2" type="submit"
                                                                        id="searchMonthlyBatteryCheckBox">Search
                                                                </button>
                                                            </div>
                                                            {{--                                                        </div>--}}

                                                        </div>
                                                        {{--                                                    </div>--}}
                                                        <div class="d-flex">
                                                            <button class="btn_add_vt" id="inverter-graph-data"
                                                                    onclick="showCheckbox()">Select Parameter
                                                            </button>

                                                            <button type="button"
                                                                    data-href="{{route('export.battery.hybrid.graph', ['plantID'=>$plantId, 'Date'=>'2021-07'])}}"
                                                                    id="export-battery-graph"
                                                                    class="ml-3 btn_add_vt btn-success btn-sm"
                                                                    onclick="exportBattery(event.target);">Export CSV
                                                            </button>
                                                        </div>
                                                        {{--                                                    <button class="btn_add_vt" id="inverter-graph-data"--}}
                                                        {{--                                                            onclick="showCheckbox()">--}}
                                                        {{--                                                        Select Parameter--}}
                                                        {{--                                                    </button>--}}
                                                        {{--                                                    <button type="button" class="btn_add_vt" data-toggle="modal"--}}
                                                        {{--                                                            data-target="#exampleModalScrollable">Export CSV--}}
                                                        {{--                                                    </button>--}}

                                                    </div>
                                                    <div class="left_month_date_vt">
                                                        <div class="day_month_calender_vt">
                                                            <div class="day_month_year_vt"
                                                                 id="battery_day_month_year_vt_day" style="">
                                                                <button><i id="batteryGraphPreviousDay"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div
                                                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-battery mt10">
                                                                    <input type="text" autocomplete="off"
                                                                           name="batteryGraphDay"
                                                                           id="batteryGraphDay"
                                                                           placeholder="Select"
                                                                           class="c-datepicker-data-input" value="">
                                                                </div>
                                                                <button><i id="batteryGraphForwardDay"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="battery_monthly_data"
                                                                 style="display: none;">
                                                                <button><i id="batteryGraphPreviousMonth"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-battery mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="batteryGraphMonth"
                                                                               placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="batteryGraphForwardMonth"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="battery_day_month_year_vt_year"
                                                                 style="display: none;">
                                                                <button><i id="batteryGraphPreviousYear"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-battery mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="batteryGraphYear" placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="batteryGraphForwardYear"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_my_btn_vt" id="battery_day_my_btn_vt">
                                                                <button class="day_bt_vt active" id="day">day</button>
                                                                <button class="month_btn_vt" id="month">month</button>
                                                                <button class="month_btn_vt" id="year">Year</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="battery-card-box" dir="ltr"
                                                     id="container_battery_graph_data_vt">
                                                    <div id="batteryContainer"></div>
                                                    <br>
                                                </div>

                                            </div>
                                        </div>
                                    </div> <!-- end card-->-
                                </div>
                            </div>
                        @else
                            <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="tabel_inv_vt">
                                    <h4 class="header_title_vt">Battery summary</h4>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase21" role="button"
                                                   aria-expanded="false"
                                                   aria-controls="cardCollpase1"><i class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Battery Information</h5>

                                            <div id="cardCollpase21" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Battery status</th>
                                                            <th>Battery voltage</th>
                                                            <th>Battery current</th>
                                                            <th>Battery power</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['battery_status']}}</th>
                                                            <td>{{$batteryData['battery_voltage']}}</td>
                                                            <td>{{$batteryData['battery_current']}}</td>
                                                            <td>{{$batteryData['battery_power']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Soc</th>
                                                            <th>Total charging energy</th>
                                                            <th>Daily charging energy</th>
                                                            <th>Total discharging energy</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{$batteryData['soc']}}</td>
                                                            <th scope="row">{{$batteryData['total_charging_energy']}}</th>
                                                            <td>{{$batteryData['daily_charging_energy']}}</td>
                                                            <td>{{$batteryData['total_discharging_energy']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Daily discharging energy</th>
                                                            <th>Battery type</th>
                                                            <th>Battery charging voltage</th>
                                                            <th>Battery Cycles</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['daily_discharging_energy']}}</th>
                                                            <td>{{$batteryData['battery_type']}}</td>
                                                            <td>{{$batteryData['battery_charging_voltage']}}</td>
                                                            <td>---</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase22" role="button"
                                                   aria-expanded="false"
                                                   aria-controls="cardCollpase1"><i class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">BMS Information</h5>

                                            <div id="cardCollpase22" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>BMS Voltage</th>
                                                            <th>BMS current</th>
                                                            <th>BMS battery current limiting charging</th>
                                                            <th>BMS temperature</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['bms_voltage']}}</th>
                                                            <td>{{$batteryData['bms_current']}}</td>
                                                            <td>{{$batteryData['bms_current_limiting_charging']}}</td>
                                                            <td>{{$batteryData['bms_temperature']}}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>BMS battery current limiting discharging</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['bms_battery_current_limiting_discharging']}}</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-->
                                    @if($meterType  != 'Solis-Cloud')
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase23" role="button"
                                                   aria-expanded="false"
                                                   aria-controls="cardCollpase1"><i class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Temperature</h5>

                                            <div id="cardCollpase23" class="collapse pt-3 show">
                                                <div class="table-responsive">
                                                    <table class="table mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Battery temperature</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">{{$batteryData['bms_temperature']}}</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif <!-- end card-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-widgets">
                                                <a data-toggle="collapse" href="#cardCollpase10" role="button"
                                                   aria-expanded="false" aria-controls="cardCollpase1"><i
                                                        class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <h5 class="card-title mb-0">Graphical Data</h5>

                                            <div id="cardCollpase10" class="collapse pt-3 show">
                                                <div class="to_bar_graph_vt">
                                                    <div class="left_multiselect_vt">
                                                        {{--                                                    <div class="multiselect_VT">--}}
                                                        {{--                                                        <div class="selectBox_vt" onclick="showCheckone()">--}}
                                                        {{--                                                        <div class="selectBox_vt" id="inverter-graph-data">--}}

                                                        {{--                                                            <div id="inverter-graph-data" class="overSelect_vt"></div>--}}
                                                        {{--                                                        </div>--}}
                                                        {{--                                                    </div>--}}
                                                        <div id="checkone_vt">
                                                            {{--                                                        <div id="inverter-battery-data">--}}
                                                            <label for="battery1">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery1"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-voltage" checked
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                voltage</label>
                                                            <label for="battery2">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery2"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="daily-charging-energy"
                                                                       onchange="handleBatteryChange(this);"/>Daily
                                                                charging energy</label>
                                                            <label for="battery3">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery3"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-current" checked
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                current </label>
                                                            <label for="battery4">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery4"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="daily-discharging-energy"
                                                                       onchange="handleBatteryChange(this);"/>Daily
                                                                discharging energy</label>
                                                            <label for="battery5">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery5"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-power"
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                power</label>
                                                            <label for="battery6">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery6"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-charging-voltage"
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                charging voltage</label>
                                                            <label for="battery7">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery7"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="soc"
                                                                       onchange="handleBatteryChange(this);"/>Soc</label>
                                                            <label for="battery8">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery8"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="bms-temperature"
                                                                       onchange="handleBatteryChange(this);"/>BMS
                                                                temperature</label>
                                                            <label for="battery9">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery10"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="bms-voltage"
                                                                       onchange="handleBatteryChange(this);"/>BMS
                                                                voltage</label>
                                                            <label for="battery11">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery11"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="battery-temperature"
                                                                       onchange="handleBatteryChange(this);"/>Battery
                                                                temperature</label>
                                                            <label for="battery12">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery12"
                                                                       name="inverterBatteryCheckBoxArray[]"
                                                                       value="bms-current"
                                                                       onchange="handleBatteryChange(this);"/>BMS
                                                                current</label>

                                                            {{--                                                            <button class="clear_vt_btn" onclick="clearBatteryData()">Clear</button>--}}
                                                            {{--                                                            <button class="btn_add_vt ml-2 mr-2" type="submit"--}}
                                                            {{--                                                                    id="searchBatteryCheckBox">Search--}}
                                                            {{--                                                            </button>--}}
                                                            <div class="btn_int_clear_vt">
                                                                <button class="clear_vt_btn"
                                                                        onclick="clearBatteryData()">
                                                                    Clear
                                                                </button>
                                                                <button class="btn_add_vt ml-2 mr-2" type="submit"
                                                                        id="searchBatteryCheckBox">Search
                                                                </button>
                                                            </div>
                                                            {{--                                                        </div>--}}

                                                        </div>
                                                        <div id="check_battery_monthly_vt">
                                                            {{--                                                        <div id="inverter-battery-data">--}}
                                                            <label for="battery1">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery1"
                                                                       name="inverterBatteryMonthlyCheckBoxArray[]"
                                                                       value="battery-charge" checked
                                                                       onchange="handleMonthlyBatteryChange(this);"/>Charging
                                                                Energy</label>
                                                            <label for="battery2">
                                                                <input type="checkbox" class="inverterBatteryCheckBox"
                                                                       id="battery2"
                                                                       name="inverterBatteryMonthlyCheckBoxArray[]"
                                                                       value="battery-discharge"
                                                                       onchange="handleMonthlyBatteryChange(this);"/>Discharging
                                                                Energy</label>


                                                            {{--                                                            <button class="clear_vt_btn" onclick="clearBatteryData()">Clear</button>--}}
                                                            {{--                                                            <button class="btn_add_vt ml-2 mr-2" type="submit"--}}
                                                            {{--                                                                    id="searchBatteryCheckBox">Search--}}
                                                            {{--                                                            </button>--}}
                                                            <div class="btn_int_clear_vt">
                                                                <button class="clear_vt_btn"
                                                                        onclick="clearBatteryData()">
                                                                    Clear
                                                                </button>
                                                                <button class="btn_add_vt ml-2 mr-2" type="submit"
                                                                        id="searchMonthlyBatteryCheckBox">Search
                                                                </button>
                                                            </div>
                                                            {{--                                                        </div>--}}

                                                        </div>
                                                        {{--                                                    </div>--}}
                                                        <div class="d-flex">
                                                            <button class="btn_add_vt" id="inverter-graph-data"
                                                                    onclick="showCheckbox()">Select Parameter
                                                            </button>

                                                            <button type="button"
                                                                    data-href="{{route('export.battery.hybrid.graph', ['plantID'=>$plantId, 'Date'=>'2021-07'])}}"
                                                                    id="export-battery-graph"
                                                                    class="ml-3 btn_add_vt btn-success btn-sm"
                                                                    onclick="exportBattery(event.target);">Export CSV
                                                            </button>
                                                        </div>
                                                        {{--                                                    <button class="btn_add_vt" id="inverter-graph-data"--}}
                                                        {{--                                                            onclick="showCheckbox()">--}}
                                                        {{--                                                        Select Parameter--}}
                                                        {{--                                                    </button>--}}
                                                        {{--                                                    <button type="button" class="btn_add_vt" data-toggle="modal"--}}
                                                        {{--                                                            data-target="#exampleModalScrollable">Export CSV--}}
                                                        {{--                                                    </button>--}}

                                                    </div>
                                                    <div class="left_month_date_vt">
                                                        <div class="day_month_calender_vt">
                                                            <div class="day_month_year_vt"
                                                                 id="battery_day_month_year_vt_day" style="">
                                                                <button><i id="batteryGraphPreviousDay"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div
                                                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-battery mt10">
                                                                    <input type="text" autocomplete="off"
                                                                           name="batteryGraphDay"
                                                                           id="batteryGraphDay"
                                                                           placeholder="Select"
                                                                           class="c-datepicker-data-input" value="">
                                                                </div>
                                                                <button><i id="batteryGraphForwardDay"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="battery_monthly_data"
                                                                 style="display: none;">
                                                                <button><i id="batteryGraphPreviousMonth"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-battery mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="batteryGraphMonth"
                                                                               placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="batteryGraphForwardMonth"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_month_year_vt"
                                                                 id="battery_day_month_year_vt_year"
                                                                 style="display: none;">
                                                                <button><i id="batteryGraphPreviousYear"
                                                                           class="fa fa-caret-left"></i></button>
                                                                <div class="mt40">
                                                                    <div
                                                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-battery mt10">
                                                                        <input type="text" autocomplete="off"
                                                                               name="batteryGraphYear" placeholder="Select"
                                                                               class="c-datepicker-data-input" value="">
                                                                    </div>
                                                                </div>
                                                                <button><i id="batteryGraphForwardYear"
                                                                           class="fa fa-caret-right"></i></button>
                                                            </div>
                                                            <div class="day_my_btn_vt" id="battery_day_my_btn_vt">
                                                                <button class="day_bt_vt active" id="day">day</button>
                                                                <button class="month_btn_vt" id="month">month</button>
                                                                <button class="month_btn_vt" id="year">Year</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="battery-card-box" dir="ltr"
                                                     id="container_battery_graph_data_vt">
                                                    <div id="batteryContainer"></div>
                                                    <br>
                                                </div>

                                            </div>
                                        </div>
                                    </div> <!-- end card-->-
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{--    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>--}}
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.0.0/dist/echarts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"
            integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>--}}

    <script>
        var expanded = false;
        var inverterExpand = false;
        var monthlyExpanded = false;

        function showCheckbox() {
            var checkbox = document.getElementById("checkpv_vt");
            var checkboxMonthlyData = document.getElementById("check_monthly_vt");
            if (!expanded) {
                checkbox.style.display = "block";
                expanded = true;
                checkboxMonthlyData.style.display = "none";
            } else {
                checkbox.style.display = "none";
                expanded = false;
                checkboxMonthlyData.style.display = "none";
            }
        }

        function inverterCheckbox() {
            var checkbox = document.getElementById("checkpv_vt");
            // var checkboxMonthlyData = document.getElementById("check_monthly_vt");
            if (!inverterExpand) {
                checkbox.style.display = "block";
                inverterExpand = true;
            } else {
                checkbox.style.display = "none";
                inverterExpand = false;
            }
        }

        function showMonthlyCheckbox() {
            var checkboxMonthlyData = document.getElementById("check_monthly_vt");
            var checkbox = document.getElementById("checkpv_vt");
            if (!monthlyExpanded) {
                checkboxMonthlyData.style.display = "block";
                checkbox.style.display = "none";
                monthlyExpanded = true;
            } else {
                checkboxMonthlyData.style.display = "none";
                checkbox.style.display = "none";
                monthlyExpanded = false;
            }
        }
    </script>
    <script>
        var expanded = false;

        function showBatteryCheckbox() {
            var checkbox = document.getElementById("checkone_vt");
            if (!expanded) {
                checkbox.style.display = "block";
                expanded = true;
            } else {
                checkbox.style.display = "none";
                expanded = false;
            }
        }
    </script>
    <script>
        var expanded = false;

        function showCheckone() {
            var checkbox = document.getElementById("checkone_vt");
            if (!expanded) {
                checkbox.style.display = "block";
                expanded = true;
            } else {
                checkbox.style.display = "none";
                expanded = false;
            }
        }
    </script>

    <script type="text/javascript">
        // var inverterUnitArray = new Array();
        // inverterUnitArray = ['dc-voltage-pv1', 'dc-power-pv1', 'dc-current-pv1', 'dc-voltage-pv2', 'dc-power-pv2', 'dc-current-pv2', 'dc-voltage-pv3', 'dc-power-pv3', 'dc-current-pv3', 'dc-voltage-pv4', 'dc-power-pv4', 'dc-current-pv4'];
        function unique(array) {
            return array.filter(function (el, index, arr) {
                return index == arr.indexOf(el);
            });
        }

        function handleChange(checkbox) {
            var inverterUnitArray = new Array();
            var batteryUnitArray = new Array();
            var inverterUnit = '';
            var batteryUnit = '';
            var batteryUnitData = new Array();
            var inverterUnitData = new Array();
            if (checkbox.checked == true) {
                inverterUnitCheckboxArray = $("input[name='inverterDetail[]']:checked").map(function () {

                    if ($(this).val() == 'dc-voltage-pv1') {
                        inverterUnit = 'V';
                    } else if ($(this).val() == 'dc-voltage-pv2') {
                        inverterUnit = 'V';
                    } else if ($(this).val() == 'dc-voltage-pv3') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-voltage-pv4') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv1') {
                        inverterUnit = 'A';
                    } else if ($(this).val() == 'dc-current-pv2') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv3') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv4') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv1') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)

                    } else if ($(this).val() == 'dc-power-pv2') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv3') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv4') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-grid-feed-in') {
                        inverterUnit = 'kWh';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-energy-purchased') {
                        inverterUnit = 'kWh';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'inverter-output-voltage') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-production') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-total-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-grid-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-voltage') {
                        inverterUnit = 'V';
                    } else if ($(this).val() == 'grid-voltage-l1') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-voltage-l2') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-voltage-l3') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    }  else if ($(this).val() == 'consumption-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'consumption-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-consumption-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'consumption-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'phase-grid-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-consumption') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-grid-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-ac-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-ac-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-temperature') {
                        inverterUnit = 'T';
                        $(this).attr('checked', true)
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
                // document.getElementById("submit").removeAttribute("disabled");
            } else {
                for (let i = 0; i < inverterUnitCheckboxArray.length; i++) {
                    if (inverterUnitCheckboxArray[i] === checkbox.value) {
                        inverterUnitCheckboxArray.splice(i, 1)
                    }
                }
            }
        }

        function handleBatteryChange(checkbox) {
            var batteryUnitArray = new Array();
            var batteryUnit = '';
            if (checkbox.checked == true) {
                batteryUnitCheckboxArray = $("input[name='inverterBatteryCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'battery-current') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-power') {
                        batteryUnit = 'W';
                    } else if ($(this).val() == 'daily-charging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'daily-discharging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-charging-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'soc') {
                        batteryUnit = '%';
                    } else if ($(this).val() == 'bms-temperature') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-temperature') {
                        batteryUnit = 'kW';
                    } else if ($(this).val() == 'bms-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'bms-current') {
                        batteryUnit = 'A';
                    }
                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
                // document.getElementById("submit").removeAttribute("disabled");
            }
        }

        function handleMonthlyBatteryChange(checkbox) {
            var batteryUnitArray = new Array();
            var batteryUnit = '';
            if (checkbox.checked == true) {
                batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-charge') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-discharge') {
                        batteryUnit = 'kWh';
                    }

                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
                // document.getElementById("submit").removeAttribute("disabled");
            }
        }

        var uncheckedEventesData = [];

        function clearInverterData() {
            let inverterData = document.getElementsByClassName('inverterDetailData');
            inverterUnitCheckboxArray = new Array();
            $('.inverterDetailData').prop('checked', false);
            $(".inverterDetailData").removeAttr('checked');
        }

        function clearBatteryData() {
            let inverterData = document.getElementsByClassName('inverterBatteryCheckBox');
            batteryUnitCheckboxArray = new Array();
            $('.inverterBatteryCheckBox').prop('checked', false);
            $(".inverterBatteryCheckBox").removeAttr('checked');
        }

        $(document).ready(function () {
            let test = document.getElementById("inverter-graph-data");
            let batteryData = document.getElementById("inverter-battery-data");
            let inverterHoverEvent = document.getElementById('inverter-select-parameter');
            let checkBoxData = document.getElementById('checkpv_vt');
            $('#inverter-select-parameter').hover(function () {
                // inverterCheckbox();
                // $(this).fadeIn(500);
                document.getElementById("checkpv_vt").style.display = 'block';
            }, function () {
                document.getElementById("checkpv_vt").style.display = 'none';
            });
            $('#checkpv_vt').hover(function () {
                // inverterCheckbox();
                // $(this).fadeIn(500);
                document.getElementById("checkpv_vt").style.display = 'block';
            }, function () {
                document.getElementById("checkpv_vt").style.display = 'none';
            });
            // test.addEventListener("onfocus", function (event) {
            //     showCheckone(), false
            // });
            $('#inverter-graph-data').hover(function () {
                // inverterCheckbox();
                // $(this).fadeIn(500);
                document.getElementById("checkone_vt").style.display = 'block';
            }, function () {
                document.getElementById("checkone_vt").style.display = 'none';
            });
            $('#checkone_vt').hover(function () {
                // inverterCheckbox();
                // $(this).fadeIn(500);
                document.getElementById("checkone_vt").style.display = 'block';
            }, function () {
                document.getElementById("checkone_vt").style.display = 'none';
            });
            // inverterHoverEvent.addEventListener("onfocus", function (event) {
            //     inverterCheckbox(), false
            // });
            // checkBoxData.addEventListener("onfocus", function (event) {
            //     showCheckbox(), false
            // });
            // inverterHoverEvent.addEventListener("mouseenter", function (event) {
            //     showCheckbox(), false
            // });
            //
            // checkBoxData.addEventListener("mouseleave", function (event) {
            //     document.getElementById("checkpv_vt").style.display = "none", false
            // });
            // test.addEventListener("mouseleave", function (event) {
            //     document.getElementById("checkone_vt").style.display = "none", false
            // });
            var inverterUnitArray = new Array();
            var batteryUnitArray = new Array();
            var inverterUnit = '';
            var batteryUnit = '';
            var batteryUnitData = new Array();
            var inverterUnitData = new Array();
            var serial_no = $('.carousel-indicators li').data('slide-to');

            var totalItems = $('.carousel-item').length;
            var currentIndex = $('div.active').index();

            var currDate = getCurrentDate();

            $('input[name="inverterGraphDay"]').val(currDate.todayDate);
            $('input[name="inverterGraphMonth"]').val(currDate.todayMonth);
            $('input[name="inverterGraphYear"]').val(currDate.todayYear);
            $('input[name="batteryGraphDay"]').val(currDate.todayDate);
            $('input[name="batteryGraphMonth"]').val(currDate.todayMonth);
            $('input[name="batteryGraphYear"]').val(currDate.todayYear);

            var inverter_date = $('input[name="inverterGraphDay"]').val();
            var battery_date = $('input[name="batteryGraphDay"]').val();
            var inverter_time = 'day';
            var battery_time = 'day';
            // var inverter_date = $('input[name="inverterGraphDay"]').val();
            // alert(inverter_date)
            // var inverter_time = 'day';
            var weatherUnitArray = new Array();
            var weatherUnit = '';
            // inverterUnitArray = ['dc-voltage-pv1', 'dc-power-pv1', 'dc-current-pv1', 'dc-voltage-pv2', 'dc-power-pv2', 'dc-current-pv2', 'dc-voltage-pv3', 'dc-power-pv3', 'dc-current-pv3', 'dc-voltage-pv4', 'dc-power-pv4', 'dc-current-pv4'];

            inverterUnitCheckboxArray = $("input[name='inverterDetail[]']:checked").map(function () {

                if ($(this).val() == 'dc-voltage-pv1') {
                    inverterUnit = 'V';
                } else if ($(this).val() == 'dc-voltage-pv2') {
                    inverterUnit = 'V';
                } else if ($(this).val() == 'dc-voltage-pv3') {
                    inverterUnit = 'V';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'dc-voltage-pv4') {
                    inverterUnit = 'V';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'dc-current-pv1') {
                    inverterUnit = 'A';
                } else if ($(this).val() == 'dc-current-pv2') {
                    inverterUnit = 'A';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'dc-current-pv3') {
                    inverterUnit = 'A';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'dc-current-pv4') {
                    inverterUnit = 'A';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'dc-power-pv1') {
                    inverterUnit = 'kW';
                    $(this).attr('checked', true)

                } else if ($(this).val() == 'dc-power-pv2') {
                    inverterUnit = 'kW';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'dc-power-pv3') {
                    inverterUnit = 'kW';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'dc-power-pv4') {
                    inverterUnit = 'kW';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'daily-grid-feed-in') {
                    inverterUnit = 'kWh';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'ac-voltage') {
                    inverterUnit = 'V';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'ac-current') {
                    inverterUnit = 'A';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'ac-frequency') {
                    inverterUnit = 'Hz';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'daily-energy-purchased') {
                    inverterUnit = 'kWh';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'inverter-output-voltage') {
                    inverterUnit = 'A';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'meter-active-power') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'daily-production') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'meter-total-active-power') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'total-grid-voltage') {
                    inverterUnit = 'V';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'grid-voltage') {
                    inverterUnit = 'V';
                } else if ($(this).val() == 'consumption-frequency') {
                    inverterUnit = 'Hz';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'consumption-voltage') {
                    inverterUnit = 'V';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'grid-current') {
                    inverterUnit = 'A';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'total-consumption-power') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'grid-frequency') {
                    inverterUnit = 'Hz';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'consumption-active-power') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'phase-grid-power') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'daily-consumption') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'total-grid-power') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'meter-ac-current') {
                    inverterUnit = 'A';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'meter-ac-power') {
                    inverterUnit = 'W';
                    $(this).attr('checked', true)
                } else if ($(this).val() == 'ac-temperature') {
                    inverterUnit = 'T';
                    $(this).attr('checked', true)
                }
                if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                    inverterUnitArray.push(inverterUnit);
                }

                if (inverterUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }

            }).get();
            batteryUnitCheckboxArray = $("input[name='inverterBatteryCheckBoxArray[]']:checked").map(function () {

                if ($(this).val() == 'battery-voltage') {
                    batteryUnit = 'V';
                } else if ($(this).val() == 'battery-current') {
                    batteryUnit = 'A';
                } else if ($(this).val() == 'battery-power') {
                    batteryUnit = 'W';
                } else if ($(this).val() == 'daily-charging-energy') {
                    batteryUnit = 'kWh';
                } else if ($(this).val() == 'daily-discharging-energy') {
                    batteryUnit = 'kWh';
                } else if ($(this).val() == 'battery-charging-voltage') {
                    batteryUnit = 'V';
                } else if ($(this).val() == 'soc') {
                    batteryUnit = '%';
                } else if ($(this).val() == 'bms-temperature') {
                    batteryUnit = 'A';
                } else if ($(this).val() == 'battery-temperature') {
                    batteryUnit = 'kW';
                } else if ($(this).val() == 'bms-voltage') {
                    batteryUnit = 'V';
                } else if ($(this).val() == 'bms-current') {
                    batteryUnit = 'A';
                }
                if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                    batteryUnitArray.push(batteryUnit);
                }

                if (batteryUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }
            }).get();

            $('.inverterBatteryCheckBox').change(function () {

                var batteryUnitArray = new Array();
                var batteryUnit = '';
                var batteryUnitData = new Array();

                batteryUnitCheckboxArray = $("input[name='inverterBatteryCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'battery-current') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-power') {
                        batteryUnit = 'W';
                    } else if ($(this).val() == 'daily-charging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'daily-discharging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-charging-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'soc') {
                        batteryUnit = '%';
                    } else if ($(this).val() == 'bms-temperature') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-temperature') {
                        batteryUnit = 'kW';
                    } else if ($(this).val() == 'bms-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'bms-current') {
                        batteryUnit = 'A';
                    }
                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
            });
            changeBatteryDayMonthYear(battery_date, battery_time, batteryUnitCheckboxArray);
            exportBatteryCsvDataValues(battery_date, battery_time, batteryUnitCheckboxArray);
            $('.inverterDetailData').change(function () {

                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitData = new Array();

                inverterUnitCheckboxArray = $("input[name='inverterDetail[]']:checked").map(function () {

                    if ($(this).val() == 'dc-voltage-pv1') {
                        inverterUnit = 'V';

                    } else if ($(this).val() == 'dc-voltage-pv2') {
                        inverterUnit = 'V';
                        // $(this).attr('checked',true)
                    } else if ($(this).val() == 'dc-voltage-pv3') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-voltage-pv4') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv1') {
                        inverterUnit = 'A';
                    } else if ($(this).val() == 'dc-current-pv2') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv3') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv4') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv1') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)

                    } else if ($(this).val() == 'dc-power-pv2') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv3') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv4') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-grid-feed-in') {
                        inverterUnit = 'kWh';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-energy-purchased') {
                        inverterUnit = 'kWh';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'inverter-output-voltage') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-production') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-total-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-grid-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-voltage') {
                        inverterUnit = 'V';
                    } else if ($(this).val() == 'consumption-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'consumption-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-consumption-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'consumption-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'phase-grid-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-consumption') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-grid-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-ac-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-ac-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-temperature') {
                        inverterUnit = 'T';
                        $(this).attr('checked', true)
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
            });
            changeInverterDayMonthYear(inverter_date, inverter_time, inverterUnitCheckboxArray);
            exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);

            // changeInverterDayMonthYear(serial_no, inverter_date, inverter_time);
            // append_graph(serial_no, inverter_date, inverter_time);

            $('.J-yearMonthDayPicker-single-inverter').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    // changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'day');
                    inverterGraphAjax(this.$input.eq(0).val(), 'day', inverterUnitCheckboxArray)
                    exportCsvDataValues(this.$input.eq(0).val(), 'day', inverterUnitCheckboxArray)
                }
            });

            $('.J-yearMonthPicker-single-inverter').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    // changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'month');
                    inverterGraphAjax(this.$input.eq(0).val(), 'month', inverterUnitCheckboxArray)
                    exportCsvDataValues(this.$input.eq(0).val(), 'day', inverterUnitCheckboxArray)
                }
            });

            $('.J-yearPicker-single-inverter').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    // changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'year');
                    inverterGraphAjax(this.$input.eq(0).val(), 'year', inverterUnitCheckboxArray)
                    exportCsvDataValues(this.$input.eq(0).val(), 'day', inverterUnitCheckboxArray)
                }
            });
            $('.J-yearMonthDayPicker-single-battery').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    // changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'day');
                    batteryGraphAjax(this.$input.eq(0).val(), 'day', batteryUnitCheckboxArray)
                    exportBatteryCsvDataValues(this.$input.eq(0).val(), 'day', batteryUnitCheckboxArray)
                }
            });

            $('.J-yearMonthPicker-single-battery').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    // changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'month');
                    batteryGraphAjax(this.$input.eq(0).val(), 'day', batteryUnitCheckboxArray)
                    exportBatteryCsvDataValues(this.$input.eq(0).val(), 'day', batteryUnitCheckboxArray)
                }
            });

            $('.J-yearPicker-single-battery').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    // changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'year');
                    batteryGraphAjax(this.$input.eq(0).val(), 'day', batteryUnitCheckboxArray)
                    exportBatteryCsvDataValues(this.$input.eq(0).val(), 'day', batteryUnitCheckboxArray)
                }
            });

            $('#inverterGraphPreviousDay').on('click', function () {

                show_date = $("input[name='inverterGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                inverter_date = formatDate(datess);
                $('input[name="inverterGraphDay"]').val('');
                $('input[name="inverterGraphDay"]').val(inverter_date);
                console.log($("input[name='inverterGraphDay']").val());
                inverter_time = 'day';
                inverterGraphAjax(inverter_date, inverter_time, inverterUnitCheckboxArray)
                exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphForwardDay').on('click', function () {

                show_date = $("input[name='inverterGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                inverter_date = formatDate(datess);
                $('input[name="inverterGraphDay"]').val('');
                $('input[name="inverterGraphDay"]').val(inverter_date);
                console.log($("input[name='inverterGraphDay']").val());
                inverter_time = 'day';
                inverterGraphAjax(inverter_date, inverter_time, inverterUnitCheckboxArray)
                exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='inverterGraphMonth']").val();
                inverter_date = formatPreviousMonth(show_date);
                $('input[name="inverterGraphMonth"]').val('');
                $('input[name="inverterGraphMonth"]').val(inverter_date);
                console.log($("input[name='inverterGraphMonth']").val());
                inverter_time = 'month';
                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitCheckboxArray = new Array();

                inverterUnitCheckboxArray = $("input[name='inverterMonthlyDetail[]']:checked").map(function () {

                    if ($(this).val() == 'solar-production') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'consumption-energy') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'energy-purchased') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'grid-feed-in') {
                        inverterUnit = 'V';
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
                inverterGraphAjax(inverter_date, inverter_time, inverterUnitCheckboxArray)
                exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphForwardMonth').on('click', function () {

                show_date = $("input[name='inverterGraphMonth']").val();
                inverter_date = formatForwardMonth(show_date);
                $('input[name="inverterGraphMonth"]').val('');
                $('input[name="inverterGraphMonth"]').val(inverter_date);
                console.log($("input[name='inverterGraphMonth']").val());
                inverter_time = 'month';
                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitCheckboxArray = new Array();

                inverterUnitCheckboxArray = $("input[name='inverterMonthlyDetail[]']:checked").map(function () {

                    if ($(this).val() == 'solar-production') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'consumption-energy') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'energy-purchased') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'grid-feed-in') {
                        inverterUnit = 'V';
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
                inverterGraphAjax(inverter_date, inverter_time, inverterUnitCheckboxArray)
                exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphPreviousYear').on('click', function () {

                show_date = $("input[name='inverterGraphYear']").val();
                inverter_date = formatPreviousYear(show_date);
                $('input[name="inverterGraphYear"]').val('');
                $('input[name="inverterGraphYear"]').val(inverter_date);
                console.log($("input[name='inverterGraphYear']").val());
                inverter_time = 'year';
                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitCheckboxArray = new Array();

                inverterUnitCheckboxArray = $("input[name='inverterMonthlyDetail[]']:checked").map(function () {

                    if ($(this).val() == 'solar-production') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'consumption-energy') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'energy-purchased') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'grid-feed-in') {
                        inverterUnit = 'V';
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
                inverterGraphAjax(inverter_date, inverter_time, inverterUnitCheckboxArray)
                exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#inverterGraphForwardYear').on('click', function () {

                show_date = $("input[name='inverterGraphYear']").val();
                inverter_date = formatForwardYear(show_date);
                $('input[name="inverterGraphYear"]').val('');
                $('input[name="inverterGraphYear"]').val(inverter_date);
                console.log($("input[name='inverterGraphYear']").val());
                inverter_time = 'year';
                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitCheckboxArray = new Array();

                inverterUnitCheckboxArray = $("input[name='inverterMonthlyDetail[]']:checked").map(function () {

                    if ($(this).val() == 'solar-production') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'consumption-energy') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'energy-purchased') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'grid-feed-in') {
                        inverterUnit = 'V';
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
                inverterGraphAjax(inverter_date, inverter_time, inverterUnitCheckboxArray)
                exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);
            });
            $('#batteryGraphPreviousDay').on('click', function () {

                show_date = $("input[name='batteryGraphDay']").val();
                var datess = new Date(show_date);
                datess.setDate(datess.getDate() - 1);
                battery_date = formatDate(datess);
                $('input[name="batteryGraphDay"]').val('');
                $('input[name="batteryGraphDay"]').val(battery_date);
                console.log($("input[name='batteryGraphDay']").val());
                battery_time = 'day';
                batteryGraphAjax(battery_date, battery_time, batteryUnitCheckboxArray)
                exportBatteryCsvDataValues(battery_date, battery_time, batteryUnitCheckboxArray)
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#batteryGraphForwardDay').on('click', function () {

                show_date = $("input[name='batteryGraphDay']").val();
                var datess = new Date(show_date);
                datess.setDate(datess.getDate() + 1);
                battery_date = formatDate(datess);
                $('input[name="batteryGraphDay"]').val('');
                $('input[name="batteryGraphDay"]').val(battery_date);
                console.log($("input[name='batteryGraphDay']").val());
                battery_time = 'day';
                var batteryUnitCheckboxArray = new Array();
                var batteryUnitArray = new Array();
                var batteryUnit = '';
                batteryUnitCheckboxArray = $("input[name='inverterBatteryCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'battery-current') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-power') {
                        batteryUnit = 'W';
                    } else if ($(this).val() == 'daily-charging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'daily-discharging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-charging-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'soc') {
                        batteryUnit = '%';
                    } else if ($(this).val() == 'bms-temperature') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-temperature') {
                        batteryUnit = 'kW';
                    } else if ($(this).val() == 'bms-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'bms-current') {
                        batteryUnit = 'A';
                    }
                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
                batteryGraphAjax(battery_date, battery_time, batteryUnitCheckboxArray)
                exportBatteryCsvDataValues(battery_date, battery_time, batteryUnitCheckboxArray)
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#batteryGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='batteryGraphMonth']").val();
                battery_date = formatPreviousMonth(show_date);
                $('input[name="batteryGraphMonth"]').val('');
                $('input[name="batteryGraphMonth"]').val(battery_date);
                battery_time = 'month';
                var batteryUnitCheckboxArray = new Array();
                var batteryUnitArray = new Array();
                var batteryUnit = '';
                batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-charge') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-discharge') {
                        batteryUnit = 'kWh';
                    }

                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
                batteryGraphAjax(battery_date, battery_time, batteryUnitCheckboxArray)
                exportBatteryCsvDataValues(battery_date, battery_time, batteryUnitCheckboxArray)
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#batteryGraphForwardMonth').on('click', function () {
                battery_time = 'month';
                var batteryUnitCheckboxArray = new Array();
                var batteryUnitArray = new Array();
                var batteryUnit = '';
                batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-charge') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-discharge') {
                        batteryUnit = 'kWh';
                    }

                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();

                show_date = $("input[name='batteryGraphMonth']").val();
                battery_date = formatForwardMonth(show_date);
                $('input[name="batteryGraphMonth"]').val('');
                $('input[name="batteryGraphMonth"]').val(battery_date);
                battery_time = 'month';
                batteryGraphAjax(battery_date, battery_time, batteryUnitCheckboxArray)
                exportBatteryCsvDataValues(battery_date, battery_time, batteryUnitCheckboxArray)
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#batteryGraphPreviousYear').on('click', function () {
                show_date = $("input[name='batteryGraphYear']").val();
                battery_date = formatPreviousYear(show_date);
                $('input[name="batteryGraphYear"]').val('');
                $('input[name="batteryGraphYear"]').val(battery_date);
                battery_time = 'year';
                var batteryUnitCheckboxArray = new Array();
                var batteryUnitArray = new Array();
                var batteryUnit = '';
                batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-charge') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-discharge') {
                        batteryUnit = 'kWh';
                    }

                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
                batteryGraphAjax(battery_date, battery_time, batteryUnitCheckboxArray)
                exportBatteryCsvDataValues(battery_date, battery_time, batteryUnitCheckboxArray)
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $('#batteryGraphForwardYear').on('click', function () {

                show_date = $("input[name='batteryGraphYear']").val();
                battery_date = formatForwardYear(show_date);
                $('input[name="batteryGraphYear"]').val('');
                $('input[name="batteryGraphYear"]').val(battery_date);
                console.log($("input[name='batteryGraphYear']").val());
                battery_time = 'year';
                battery_time = 'month';
                var batteryUnitCheckboxArray = new Array();
                var batteryUnitArray = new Array();
                var batteryUnit = '';
                batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-charge') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-discharge') {
                        batteryUnit = 'kWh';
                    }

                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
                batteryGraphAjax(battery_date, battery_time, batteryUnitCheckboxArray)
                exportBatteryCsvDataValues(battery_date, battery_time, batteryUnitCheckboxArray)
                // append_graph(serial_no, inverter_date, inverter_time);
            });

            $("#inverter_day_my_btn_vt button").click(function () {
                $('#inverter-select-parameter').hover(function () {
                    document.getElementById("checkpv_vt").style.display = 'block';
                    document.getElementById("check_monthly_vt").style.display = 'none';
                }, function () {
                    document.getElementById("checkpv_vt").style.display = 'none';
                });
                $('#checkpv_vt').hover(function () {
                    document.getElementById("checkpv_vt").style.display = 'block';
                    document.getElementById("check_monhtly_vt").style.display = 'none';
                }, function () {
                    document.getElementById("checkpv_vt").style.display = 'none';
                });
                $('#inverter_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");
                let monthlyData = $(this).addClass("active");
                changeInverterDayMonthYear(inverter_date, inverter_time, inverterUnitCheckboxArray);
                // exportCsvDataValues(inverter_date, inverter_time, inverterUnitCheckboxArray);


            });
            $("#battery_day_my_btn_vt button").click(function () {
                $('#inverter-graph-data').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("checkone_vt").style.display = 'block';
                }, function () {
                    document.getElementById("checkone_vt").style.display = 'none';
                });
                $('#checkone_vt').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("checkone_vt").style.display = 'block';
                }, function () {
                    document.getElementById("checkone_vt").style.display = 'none';
                });

                $('#battery_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeBatteryDayMonthYear(battery_date, battery_time, batteryUnitCheckboxArray);

            });

            // next click event --
            $('.right').on('click', function (e) {
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
            $('.left').on('click', function (e) {
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
        });

        function changeInverterDayMonthYear(date, time, inverterUnitCheckboxArray) {

            var d_m_y = '';

            $('#inverter_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#inverter_day_month_year_vt_year').hide();
                $('#inverter_day_month_year_vt_month').hide();
                $('#inverter_day_month_year_vt_day').show();
                date = $('input[name="inverterGraphDay"]').val();
                time = 'day';
                $('#inverter-select-parameter').hover(function () {
                    document.getElementById("checkpv_vt").style.display = 'block';
                    // document.getElementById("check_monhtly_vt").style.display = 'none';
                }, function () {
                    document.getElementById("checkpv_vt").style.display = 'none';
                });
                $('#checkpv_vt').hover(function () {
                    document.getElementById("checkpv_vt").style.display = 'block';
                    // document.getElementById("check_monhtly_vt").style.display = 'none';
                }, function () {
                    document.getElementById("checkpv_vt").style.display = 'none';
                });
                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitCheckboxArray = new Array();
                inverterUnitCheckboxArray = $("input[name='inverterDetail[]']:checked").map(function () {

                    if ($(this).val() == 'dc-voltage-pv1') {
                        inverterUnit = 'V';

                    } else if ($(this).val() == 'dc-voltage-pv2') {
                        inverterUnit = 'V';
                        // $(this).attr('checked',true)
                    } else if ($(this).val() == 'dc-voltage-pv3') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-voltage-pv4') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv1') {
                        inverterUnit = 'A';
                    } else if ($(this).val() == 'dc-current-pv2') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv3') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-current-pv4') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv1') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)

                    } else if ($(this).val() == 'dc-power-pv2') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv3') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'dc-power-pv4') {
                        inverterUnit = 'kW';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-grid-feed-in') {
                        inverterUnit = 'kWh';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-energy-purchased') {
                        inverterUnit = 'kWh';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'inverter-output-voltage') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-production') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-total-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-grid-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-voltage') {
                        inverterUnit = 'V';
                    } else if ($(this).val() == 'consumption-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'consumption-voltage') {
                        inverterUnit = 'V';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-consumption-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'grid-frequency') {
                        inverterUnit = 'Hz';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'consumption-active-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'phase-grid-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'daily-consumption') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'total-grid-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-ac-current') {
                        inverterUnit = 'A';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'meter-ac-power') {
                        inverterUnit = 'W';
                        $(this).attr('checked', true)
                    } else if ($(this).val() == 'ac-temperature') {
                        inverterUnit = 'T';
                        $(this).attr('checked', true)
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
                // document.getElementById('daily-inverter-data').style.display = 'inline';
                // document.getElementById('monthly-inverter-data').style.display = 'none';

            } else if (d_m_y == 'month') {
                $('#inverter_day_month_year_vt_year').hide();
                $('#inverter_day_month_year_vt_day').hide();
                $('#inverter_day_month_year_vt_month').show();
                date = $('input[name="inverterGraphMonth"]').val();
                time = 'month';
                // document.getElementById('daily-inverter-data').style.display = 'none';
                // document.getElementById('monthly-inverter-data').style.display = 'inline';
                $('#inverter-select-parameter').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_monthly_vt").style.display = 'block';
                }, function () {
                    document.getElementById("check_monthly_vt").style.display = 'none';
                });
                $('#check_monthly_vt').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_monthly_vt").style.display = 'block';
                }, function () {
                    document.getElementById("check_monthly_vt").style.display = 'none';
                });
                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitCheckboxArray = new Array();

                inverterUnitCheckboxArray = $("input[name='inverterMonthlyDetail[]']:checked").map(function () {

                    if ($(this).val() == 'solar-production') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'consumption-energy') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'energy-purchased') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'grid-feed-in') {
                        inverterUnit = 'V';
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
            } else if (d_m_y == 'year') {
                $('#inverter_day_month_year_vt_day').hide();
                $('#inverter_day_month_year_vt_month').hide();
                $('#inverter_day_month_year_vt_year').show();
                date = $('input[name="inverterGraphYear"]').val();
                time = 'year';
                $('#inverter-select-parameter').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_monthly_vt").style.display = 'block';
                }, function () {
                    document.getElementById("check_monthly_vt").style.display = 'none';
                });
                $('#check_monthly_vt').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_monthly_vt").style.display = 'block';
                }, function () {
                    document.getElementById("check_monthly_vt").style.display = 'none';
                });
                var inverterUnitArray = new Array();
                var inverterUnit = '';
                var inverterUnitCheckboxArray = new Array();
                inverterUnitCheckboxArray = $("input[name='inverterMonthlyDetail[]']:checked").map(function () {

                    if ($(this).val() == 'solar-production') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'consumption-energy') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'energy-purchased') {
                        inverterUnit = 'kWh';
                    } else if ($(this).val() == 'grid-feed-in') {
                        inverterUnit = 'V';
                    }
                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
            }

            inverterGraphAjax(date, time, inverterUnitCheckboxArray);
            exportCsvDataValues(date, time, inverterUnitCheckboxArray);
        }

        function changeBatteryDayMonthYear(date, time, batteryUnitCheckboxArray) {

            var d_m_y = '';

            $('#battery_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                var batteryUnitArray = new Array();
                var batteryUnit = new Array();
                var batteryUnitCheckboxArray = new Array();

                $('#inverter-graph-data').hover(function () {
                    document.getElementById("checkone_vt").style.display = 'block';
                    document.getElementById("check_battery_monthly_vt").style.display = 'none';
                }, function () {
                    document.getElementById("checkone_vt").style.display = 'none';
                });
                $('#checkone_vt').hover(function () {
                    document.getElementById("checkone_vt").style.display = 'block';
                    document.getElementById("check_battery_monthly_vt").style.display = 'none';
                }, function () {
                    document.getElementById("checkone_vt").style.display = 'none';
                });
                batteryUnitCheckboxArray = $("input[name='inverterBatteryCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'battery-current') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-power') {
                        batteryUnit = 'W';
                    } else if ($(this).val() == 'daily-charging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'daily-discharging-energy') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-charging-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'soc') {
                        batteryUnit = '%';
                    } else if ($(this).val() == 'bms-temperature') {
                        batteryUnit = 'A';
                    } else if ($(this).val() == 'battery-temperature') {
                        batteryUnit = 'kW';
                    } else if ($(this).val() == 'bms-voltage') {
                        batteryUnit = 'V';
                    } else if ($(this).val() == 'bms-current') {
                        batteryUnit = 'A';
                    }
                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
                $('#battery_day_month_year_vt_year').hide();
                $('#battery_monthly_data').hide();
                $('#battery_day_month_year_vt_day').show();
                date = $('input[name="batteryGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                var batteryUnitArray = new Array();
                var batteryUnit = new Array();
                var batteryUnitCheckboxArray = new Array();
                $('#battery_day_month_year_vt_year').hide();
                $('#battery_day_month_year_vt_day').hide();
                $('#battery_monthly_data').show();
                date = $('input[name="batteryGraphMonth"]').val();
                time = 'month';
                $('#inverter-graph-data').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_battery_monthly_vt").style.display = 'block';
                    document.getElementById("checkone_vt").style.display = 'none';
                }, function () {
                    document.getElementById("check_battery_monthly_vt").style.display = 'none';
                });
                $('#check_battery_monthly_vt').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_battery_monthly_vt").style.display = 'block';
                    document.getElementById("checkone_vt").style.display = 'none';
                }, function () {
                    document.getElementById("check_battery_monthly_vt").style.display = 'none';
                });
                batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-charge') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-discharge') {
                        batteryUnit = 'kWh';
                    }
                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
            } else if (d_m_y == 'year') {
                var batteryUnitArray = new Array();
                var batteryUnit = new Array();
                var batteryUnitCheckboxArray = new Array();
                $('#battery_day_month_year_vt_day').hide();
                $('#battery_monthly_data').hide();
                $('#battery_day_month_year_vt_year').show();
                date = $('input[name="batteryGraphYear"]').val();
                time = 'year';
                $('#inverter-graph-data').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_battery_monthly_vt").style.display = 'block';
                    document.getElementById("checkone_vt").style.display = 'none';
                }, function () {
                    document.getElementById("check_battery_monthly_vt").style.display = 'none';
                });
                $('#check_battery_monthly_vt').hover(function () {
                    // inverterCheckbox();
                    // $(this).fadeIn(500);
                    document.getElementById("check_battery_monthly_vt").style.display = 'block';
                    document.getElementById("checkone_vt").style.display = 'none';
                }, function () {
                    document.getElementById("check_battery_monthly_vt").style.display = 'none';
                });
                batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                    if ($(this).val() == 'battery-charge') {
                        batteryUnit = 'kWh';
                    } else if ($(this).val() == 'battery-discharge') {
                        batteryUnit = 'kWh';
                    }
                    if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                        batteryUnitArray.push(batteryUnit);
                    }

                    if (batteryUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }
                }).get();
            }

            batteryGraphAjax(date, time, batteryUnitCheckboxArray);
            exportBatteryCsvDataValues(date, time, batteryUnitCheckboxArray);
        }

        function inverterGraphAjax(date, time, inverterUnitArray) {

            $('#container_inverter_graph_data_vt').empty();
            var inverterNo = {!! json_encode($inverterNo) !!};
            var plantID = $('#plantID').val();
            console.log(inverterUnitArray);
            $.ajax({
                url: "{{ route('admin.graph.plant.inverter.ajax') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                    'historyCheckBoxArray': JSON.stringify(inverterUnitArray),
                    'inverterSerialNo': inverterNo
                },
                dataType: 'json',
                success: function (data) {

                    $('#container_inverter_graph_data_vt').append('<div id="plant-inverter-chart" style="height:300px;width:100%;margin-top: 50px"></div>');

                    plantInverterGraphDetails(data);
                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function plantInverterGraphDetails(plantsHistoryGraphData) {

            var data = plantsHistoryGraphData.plant_inverter_graph_data;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var timeType = plantsHistoryGraphData.time_type;
            var legendArray = plantsHistoryGraphData.legend_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            var dom = document.getElementById("plant-inverter-chart");
            console.log(dom);
            var myChart = echarts.init(dom);
            var app = {};
            // alert(legendArray)
            // var option = {
            //     xAxis: {
            //         type: 'category',
            //         boundaryGap: false,
            //         data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
            //     },
            //     yAxis: {
            //         type: 'value'
            //     },
            //     series: [
            //         {
            //             data: [820, 932, 901, 934, 1290, 1330, 1320],
            //             type: 'line',
            //             areaStyle: {}
            //         }
            //     ]
            // };


            var option = {

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
                        // console.log(p);
                        for (let i = 0; i < p.length; i++) {
                            if (timeType == 'day') {
                                if (p[i].seriesName == 'DC Voltage PV1') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'DC Voltage PV2') {
                                    console.log(p[i].seriesName)
                                    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'DC Voltage PV3') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/dc-voltage-pv3.PNG')}}" width="22.5px"><span style="color:#fce7e7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'DC Voltage PV4') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/dc-voltage-pv4.PNG')}}" width="22.5px"><span style="color:#b9dfef;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'DC Power PV1') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_history.png')}}"><span style="color:#E38595;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'DC Power PV2') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8FC34D;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'DC Power PV3') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/dc-power-pv3.png')}}" width="22.5px"><span style="color:#a2d5e9;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'DC Power PV4') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/dc-power-pv4.png')}}" width="22.5px" width="22.5px"><span style="color:#9a60b4;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'DC Current PV1') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8fc34d;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'DC Current PV2') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.png')}}" width="22.5px"><span style="color:#facc65;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'DC Current PV3') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/dc-current-pv3.png')}}" width="22.5px"><span style="color:#84c5a7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'DC Current PV4') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}" width="22.5px"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'Phase Grid Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/phase-grid-power.png')}}" width="22.5px"><span style="color:#ed3333;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'Grid Voltage') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid-voltage.png')}}" width="22.5px"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'Grid Voltage L1') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_voltage_l1.png')}}" width="22.5px"><span style="color:#2F7EE4;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'Grid Voltage L2') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_voltage_l2.png')}}" width="22.5px"><span style="color:#DE3B52;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'Grid Voltage L3') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_voltage_l3.png')}}" width="22.5px"><span style="color:#9E5ED9;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'Grid Frequency') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid-frequency.PNG')}}" width="22.5px"><span style="color:#8df758;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} Hz</span>`;
                                }
                                if (p[i].seriesName == 'Total Grid Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}" width="22.5px"><span style="color:#d858f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'Grid Current') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-grid-feed-in.png')}}" width="22.5px"><span style="color:#7958f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'Daily Grid Feed In') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-grid-feed-in.png')}}" width="22.5px"><span style="color:#7958f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Daily Energy Purchased') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-energy-purchased.png')}}" width="22.5px"><span style="color:#58a5f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'AC Current') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/ac-current.png')}}" width="22.5px"><span style="color:#9e5ed9;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'AC Voltage') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/ac-voltage.png')}}" width="22.5px"><span style="color:#f658f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;

                                }
                                if (p[i].seriesName == 'AC Frequency') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/ac-frequency.png')}}" width="22.5px"><span style="color:#048fff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} Hz</span>`;
                                }
                                if (p[i].seriesName == 'Consumption Frequnency') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/consumption-frequency.png')}}" width="22.5px"><span style="color:#ecf758;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} Hz</span>`;
                                }
                                if (p[i].seriesName == 'Inverter Output Voltage') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/inverter-output-voltage.png')}}" width="22.5px"><span style="color:#d97b5e;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'Daily Production') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-generation.png')}}" width="22.5px"><span style="color:#58f7cc;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Daily Consumption') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-consumption.PNG')}}" width="22.5px"><span style="color:#f76558;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Meter Total Active Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/meter-total-active-power.png')}}" width="22.5px"><span style="color:#ed33cc6e;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'Meter Active Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/meter-active-power.png')}}" width="22.5px"><span style="color:#af33ed6e;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'Consumption Active Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/consumption-active-power.PNG')}}" width="22.5px"><span style="color:#5b04ff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'Total Consumption Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/total-consumption-power.PNG')}}" width="22.5px"><span style="color:#8b0808;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'Meter AC Current') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/meter-ac-current.PNG')}}" width="22.5px"><span style="color:#144a8399;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'Meter AC Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/meter-ac-power.PNG')}}" width="22.5px"><span style="color:#621f5699;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'AC Temperature') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/ac-temperature.PNG')}}" width="22.5px"><span style="color:#83421499;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} <sup>o</sup>C</span>`;
                                }

                                if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                    output += '<br/>'
                                }

                            }
                            if (timeType == 'month' || timeType == 'year') {
                                if (p[i].seriesName == 'Solar Production') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/dc-power-pv3.png')}}" width="22.5px"><span style="color:#a2d5e9;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Energy Purchased') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-energy-purchased.png')}}" width="22.5px"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Grid Feed In') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid-voltage.png')}}" width="22.5px"><span style="color:#58a5f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Consumption Energy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-grid-feed-in.png')}}" width="22.5px"><span style="color:#7958f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                    output += '<br/>'
                                }

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
                // }

            }
            // alert(option)
            // alert(typeof option)

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
            // console.log(document.getElementById('container_inverter_graph_data_vt'))
        }

        function exportCsvDataValues(date, time, batteryArray = ["dc-voltage-pv1", "dc-current-pv1"]) {
            console.log([date, time]);
            let exportDataRef = document.getElementById("export-inverter-graph").getAttribute('data-href');
            let splitData = exportDataRef.split('?');
            var plantID = $('#plantID').val();
            var inverterSerialNo = {!! json_encode($inverterNo) !!};
            let url = splitData[0] + '?plantID=' + plantID + '&Date=' + date + '&time=' + time + '&inverterSerialNo=' + inverterSerialNo + '&inverterArray=' + JSON.stringify(batteryArray);
            document.getElementById("export-inverter-graph").removeAttribute('data-href');
            document.getElementById("export-inverter-graph").setAttribute('data-href', url);
        }
        function exportBatteryCsvDataValues(date, time, batteryArray = ["battery-voltage", "battery-current"]) {
            let exportDataRef = document.getElementById("export-battery-graph").getAttribute('data-href');
            let splitData = exportDataRef.split('?');
            var plantID = $('#plantID').val();
            var inverterSerialNo = {!! json_encode($inverterNo) !!};
            let url = splitData[0] + '?plantID=' + plantID + '&Date=' + date + '&time=' + time + '&inverterSerialNo=' + inverterSerialNo + '&inverterArray=' + JSON.stringify(batteryArray);

            document.getElementById("export-battery-graph").removeAttribute('data-href');
            document.getElementById("export-battery-graph").setAttribute('data-href', url);
            console.log(document.getElementById("export-battery-graph").getAttribute('data-href'))
        }

        function batteryGraphAjax(date, time, batteryUnitArray) {
            // alert('hellllllllllllll')
            // terUnitArray = ['generation'];
            $('#container_battery_graph_data_vt').empty();
            // console.log(document.querySelector('#container_battery_graph_data_vt'));
            var inverterNo = {!! json_encode($inverterNo) !!};
            // alert(inverterNo)
            var plantID = $('#plantID').val();
            {{--// var plantMeterType = $('#plantMeterType').val();--}}
            $.ajax({
                url: "{{ route('admin.graph.plant.battery.ajax') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                    'historyCheckBoxArray': JSON.stringify(batteryUnitArray),
                    'inverterSerialNo': inverterNo
                },
                dataType: 'json',
                success: function (data) {

                    // Timedata = data['time_array'];
                    // timetype = time;
                    // console.log(Timedata);
                    //
                    // var generationdata=data.plant_inverter_graph;
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
                    //
                    // $('.historyGraphSpinner').hide();
                    //
                    $('#container_battery_graph_data_vt').append('<div id="plant-battery-chart-detail" style="height:300px;width:1400px;margin-top: 50px"></div>');
                    plantBatteryGraphDetails(data);
                    // $('.generationTotalValue').html(data.total_generation);
                    // $('.consumptionTotalValue').html(data.total_consumption);
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


                },
                error: function (data) {

                    // $('.historyGraphSpinner').hide();
                    // $('.historyGraphError').show();
                }
            });
        }

        function plantBatteryGraphDetails(plantsHistoryGraphData) {

            var data = plantsHistoryGraphData.plant_battery_graph_data;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var timeType = plantsHistoryGraphData.time_type;
            var legendArray = plantsHistoryGraphData.legend_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            var domData = document.querySelector("#plant-battery-chart-detail");
            var batteryChart = echarts.init(domData);
            var app = {};
            console.log(time);
            var option = {

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
                            if (timeType == 'day') {
                                if (p[i].seriesName == 'Battery Voltage') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-voltage.png')}}" width="22.5px"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'Battery Current') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/total-consumption-power.PNG')}}" width="22.5px"><span style="color:#8b0808;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'Battery Power') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.PNG')}}" width="22.5px"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                                }
                                if (p[i].seriesName == 'Daily Charging Energy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/daily-energy-purchased.PNG')}}" width="22.5px"><span style="color:#58a5f7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Daily Discharging Energy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/ac-current.PNG')}}" width="22.5px"><span style="color:#9e5ed9;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'BMS Current') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}" width="22.5px"><span style="color:#8fc34d;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} A</span>`;
                                }
                                if (p[i].seriesName == 'BMS Voltage') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-charge-image.png')}}" width="22.5px"><span style="color:#facc65;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'Battery Charging Voltage') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/consumption-active-power.PNG')}}" width="22.5px"><span style="color:#5b04ff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} V</span>`;
                                }
                                if (p[i].seriesName == 'SOC') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/soc-image-color.PNG')}}" width="22.5px"><span style="color:#605bf4;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} %</span>`;
                                }
                                if (p[i].seriesName == 'Battery Temperature') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}" width="22.5px"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} <sup>o</sup>C</span>`;
                                }
                                if (p[i].seriesName == 'BMS Temperature') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/dc-voltage-pv3.PNG')}}" width="22.5px"><span style="color:#fce7e7;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} <sup>o</sup>C</span>`;
                                }
                                if (i != p.length - 1) {
                                    output += '<br/>'
                                }

                            }
                            if (timeType == 'month' || timeType == 'year') {
                                if (p[i].seriesName == 'Battery Charge Energy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid-voltage.png')}}" width="22.5px"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (p[i].seriesName == 'Battery Discharge Energy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/battery-voltage.png')}}" width="22.5px"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh</span>`;
                                }
                                if (i != p.length - 1) {
                                    output += '<br/>'
                                }
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
                // }

            }
            // alert(option)
            // alert(typeof option)

            if (option && typeof option === 'object') {
                batteryChart.setOption(option);
            }
            // console.log(document.getElementById('container_inverter_graph_data_vt'))
        }

        $('#searchInverterCheckBox').click(function () {

            showCheckbox();

            var inverterTimeValue = $('#inverter_day_my_btn_vt').find('button.active').attr('id');
            inverterGraphAjax($('input[name="inverterGraphDay"]').val(), inverterTimeValue, inverterUnitCheckboxArray);
            exportCsvDataValues($('input[name="inverterGraphDay"]').val(), inverterTimeValue, inverterUnitCheckboxArray);
        });
        $('#searchBatteryCheckBox').click(function () {

            showBatteryCheckbox();
            var batteryTimeValue = $('#battery_day_my_btn_vt').find('button.active').attr('id');
            batteryGraphAjax($('input[name="batteryGraphDay"]').val(), batteryTimeValue, batteryUnitCheckboxArray);
            exportBatteryCsvDataValues($('input[name="batteryGraphDay"]').val(), batteryTimeValue, batteryUnitCheckboxArray);
        });
        $('#searchMonthlyBatteryCheckBox').click(function () {
            var batteryUnitCheckboxArray = new Array();
            var batteryUnitArray = new Array();
            var batteryUnit = new Array();
            batteryUnitCheckboxArray = $("input[name='inverterBatteryMonthlyCheckBoxArray[]']:checked").map(function () {

                if ($(this).val() == 'battery-charge') {
                    batteryUnit = 'kWh';
                } else if ($(this).val() == 'battery-discharge') {
                    batteryUnit = 'kWh';
                }
                if (batteryUnitArray.indexOf(batteryUnit) === -1) {

                    batteryUnitArray.push(batteryUnit);
                }

                if (batteryUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }
            }).get();
            var batteryTimeValue = $('#battery_day_my_btn_vt').find('button.active').attr('id');
            batteryGraphAjax($('input[name="batteryGraphDay"]').val(), batteryTimeValue, batteryUnitCheckboxArray);
            exportBatteryCsvDataValues($('input[name="batteryGraphDay"]').val(), batteryTimeValue, batteryUnitCheckboxArray);

        });

        function inverterMonthlySearchData() {
            var inverterUnitArray = new Array();
            var inverterUnit = '';
            var inverterTimeValue = $('#inverter_day_my_btn_vt').find('button.active').attr('id');
            inverterUnitCheckboxArray = $("input[name='inverterMonthlyDetail[]']:checked").map(function () {

                if ($(this).val() == 'solar-production') {
                    inverterUnit = 'kWh';
                } else if ($(this).val() == 'consumption-energy') {
                    inverterUnit = 'kWh';
                } else if ($(this).val() == 'energy-purchased') {
                    inverterUnit = 'kWh';
                } else if ($(this).val() == 'grid-feed-in') {
                    inverterUnit = 'V';
                }
                if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                    inverterUnitArray.push(inverterUnit);
                }

                if (inverterUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }

            }).get();
            inverterGraphAjax($('input[name="inverterGraphMonth"]').val(), inverterTimeValue, inverterUnitCheckboxArray);
            exportCsvDataValues($('input[name="inverterGraphMonth"]').val(), inverterTimeValue, inverterUnitCheckboxArray);
        }

        function graph(serial_no, max_generation) {

            var today_log = $('#' + serial_no).attr('data-today_log').split(',');
            var today_time = $('#' + serial_no).attr('data-today_log_time').split(',');
            var today = [];
            var intervalCount = 3;

            var max_gen = max_generation;

            max_gen = Math.ceil((max_gen / 5));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10, number_format)) * Math.pow(10, number_format);

            if (today_time.length > 13) {

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

            var max_gen = Math.max.apply(Math, curr_generation.map(function (o) {
                return o.y;
            }));

            max_gen = Math.ceil((max_gen / 5));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10, number_format)) * Math.pow(10, number_format);

            if (time == 'year') {

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
                        toolTipContent: "{tooltip} " + date + "<br/>Monthly Generation: {y} kWh",
                        markerType: "none",
                        type: "column",
                        color: "#0F75BC",
                        dataPoints: curr_generation
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
                        margin: 50,
                        gridThickness: 0.15,
                    },

                    data: [{
                        toolTipContent: "{x}-" + dateArr[1] + "-" + dateArr[0] + "<br/> Daily Generation: {y} kWh",
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

            {{--		@if($inverters_array)--}}

            {{--		@foreach($inverters_array as $key => $inverter)--}}
            {{--		// alert('hy');--}}

            {{--		$("#demo-foo-row-toggler_{{$inverter->dv_inverter_serial_no}}").footable(), $("#demo-foo-accordion").footable().on("footable_row_expanded", function(o) {--}}
            {{--			$("#demo-foo-accordion tbody tr.footable-detail-show").not(o.row).each(function() {--}}
            {{--				$("#demo-foo-accordion").data("footable").toggleDetail(this)--}}
            {{--			})--}}
            {{--		}), $("#demo-foo-pagination").footable(), $("#demo-show-entries").change(function(o) {--}}
            {{--			o.preventDefault();--}}
            {{--			var t = $(this).val();--}}
            {{--			$("#demo-foo-pagination").data("page-size", t), $("#demo-foo-pagination").trigger("footable_initialized")--}}
            {{--		});--}}
            {{--		var t = $("#demo-foo-filtering");--}}
            {{--		t.footable().on("footable_filtering", function(o) {--}}
            {{--			var t = $("#demo-foo-filter-status").find(":selected").val();--}}
            {{--			o.filter += o.filter && 0 < o.filter.length ? " " + t : t, o.clear = !o.filter--}}
            {{--		}), $("#demo-foo-filter-status").change(function(o) {--}}
            {{--			o.preventDefault(), t.trigger("footable_filter", {--}}
            {{--				filter: $(this).val()--}}
            {{--			})--}}
            {{--		}), $("#demo-foo-search").on("input", function(o) {--}}
            {{--			o.preventDefault(), t.trigger("footable_filter", {--}}
            {{--				filter: $(this).val()--}}
            {{--			})--}}
            {{--		});--}}
            {{--		var e = $("#demo-foo-addrow");--}}
            {{--		e.footable().on("click", ".demo-delete-row", function() {--}}
            {{--			var o = e.data("footable"),--}}
            {{--				t = $(this).parents("tr:first");--}}
            {{--			o.removeRow(t)--}}
            {{--		}), $("#demo-input-search2").on("input", function(o) {--}}
            {{--			o.preventDefault(), e.trigger("footable_filter", {--}}
            {{--				filter: $(this).val()--}}
            {{--			})--}}
            {{--		}), $("#demo-btn-addrow").click(function() {--}}
            {{--			e.data("footable").appendRow('<tr><td style="text-align: center;"><button class="demo-delete-row btn btn-danger btn-xs btn-icon"><i class="fa fa-times"></i></button></td><td>Adam</td><td>Doe</td><td>Traffic Court Referee</td><td>22 Jun 1972</td><td><span class="badge label-table badge-success   ">Active</span></td></tr>')--}}
            {{--		})--}}

            {{--		@endforeach--}}
            {{--		@endif--}}

        }

    </script>

    {{--    <script>--}}
    {{--        var xValues = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150];--}}
    {{--        var yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];--}}

    {{--        new Chart("container_inverter_graph_vt", {--}}
    {{--            type: "line",--}}
    {{--            data: {--}}
    {{--                labels: xValues,--}}
    {{--                datasets: [{--}}
    {{--                    fill: false,--}}
    {{--                    lineTension: 0,--}}
    {{--                    backgroundColor: "rgba(0,0,255,1.0)",--}}
    {{--                    borderColor: "rgba(0,0,255,0.1)",--}}
    {{--                    data: yValues--}}
    {{--                }]--}}
    {{--            },--}}
    {{--            options: {--}}
    {{--                legend: {display: false},--}}
    {{--                scales: {--}}
    {{--                    yAxes: [{ticks: {min: 6, max: 16}}],--}}
    {{--                }--}}
    {{--            }--}}
    {{--        });--}}
    {{--    </script>--}}
    <script>
        function exportTasks(_this) {
            let exportDataRef = document.getElementById("export-inverter-graph").getAttribute('data-href');
            let _url = exportDataRef;
            window.location.href = _url;
        }
        function exportBattery(_this) {
            let exportDataRef = document.getElementById("export-battery-graph").getAttribute('data-href');
            let _url = exportDataRef;
            window.location.href = _url;
        }
    </script>
    <script>
        $(document).ready(function () {
            $('.add_mar_vt').on('click', function () {
                $('.vt_this').toggleClass("highlight");
                // console.log('hello');
            });
        });

    </script>

    {{--    <script src="{{ asset('assets/js/jquery-3.5.1.min.js')}}"--}}
    {{--            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>--}}


@endsection
