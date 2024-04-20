@extends('layouts.admin.master')
@section('title', 'Cron Job Detail')
@section('content')
    <style>

        .btn_action_vt {
            color: #fff;
            background-color: #8DBE3F;
            border-color: #8DBE3F;
            border-radius: 5px;
        }

        .btn_action_vt:hover {
            color: #8DBE3F;
            background-color: #fff;
        }

        .table tr {
            cursor: pointer;
        }

        .table tbody tr:hover {
            background: #8DBE3F;
            color: #fff;
        }

        .table {
            background-color: #fff !important;
        }

        .hedding h1 {
            color: #fff;
            font-size: 25px;
        }

        .main-section {
            margin-top: 120px;
        }

        .hiddenRow {
            padding: 0 !important;
            background-color: #ebeef8;
        }

        .accordian-body span {
            color: #a2a2a2 !important;
        }

        /* .accordian-body p {
            margin: 0;
            float: left;
            width: 16%;
            padding-bottom: 12px;
        } */
        .user_email_vt {
            width: 100%;
            float: left;
            border-bottom: 1px solid #eaebef;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .user_email_vt span {
            width: 40px;
            float: left;
            font-size: 12px;
            font-weight: 300;
        }

        .user_email_vt p {
            width: auto;
            float: left;
            color: #a2a2a2 !important;
            font-size: 12px;
            font-weight: 300;
            margin: 0;
        }

        .user_email_vt p:hover {
            color: #a2a2a2 !important;
        }

        .accordian-body .card {
            border-radius: 4px !important;
            box-shadow: none !important;
        }

        .accordian-body .collapse.show {
            background: #f6f7fc !important;
        }

        .accordian-body .card-header {
            box-shadow: none !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__rendered {
            margin: 0;
            height: auto !important;
            border-radius: 5px;
        }

        .table-hover tr {
            cursor: default !important;
            background: #fff !important;
            color: #1C1B1B;
        }

        .table-hover thead tr:hover {
            background: #fff !important;
            color: #1C1B1B;

        }

        .table-hover tbody tr {
            background: #fff !important;
            color: #9C9C9C !important;

        }

        .table-hover tbody tr:hover {
            background: #fff !important;
            color: #9C9C9C !important;

        }

        .fa-exclamation {
            border: 3px solid #E11818;
            width: 70px;
            height: 70px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #E11818;
            font-size: 27px;
            margin: 0 auto;
        }

        .table .thead-light th{
            text-align: left !important;
        }
        .btn_warning {
            color: #fff;
            background-color: #8DBE3F;
            border-color: #8DBE3F;
            min-width: 101px;
        }

        .btn-success {
            color: #fff;
            background-color: #E11818;
            border-color: #E11818;
        }

        .btn_warning:hover {
            color: #fff;
            background-color: #8DBE3F;
            border-color: #8DBE3F;
        }

        .btn-success:hover {
            color: #fff;
            background-color: #E11818;
            border-color: #E11818;
        }

        .btn_foot_vt {
            width: 215px;
            margin: 0 auto;
        }

        .modal-body.p-3 h3 {
            text-align: center;
            margin: 25px 0;
        }

        .modal-header .close {
            color: #fff;
        }

        .select2-container .select2-selection--multiple {
            min-height: 36px;
            box-shadow: none !important;
            background: #f9f9f9 !important;
            border: none !important;
            border-radius: .2rem !important;
        }

        .select2-container .select2-search--inline .select2-search__field {
            background: #f9f9f9 !important;
        }

        .select2-result.select2-result-unselectable.select2-disabled {
            display: none !important;
        }
        span {
            display: inline-block !important;
        }

    </style>

    <div class="card-body mb-2">
        <div class="table-responsive">
            <table

                class="display table table-borderless table-centered table-nowrap" style="width:100%">
                <thead class="thead-light vt_head_td">
                <tr>
                    <th><span>Sr #</span></th>
                    <th><span>Inverter Type</span></th>
                    <th><span>Status</span></th>
                    <th><span>Start Time</span></th>
                    <th><span>End Time</span></th>
                    <th><span>Total Time</span></th>
                </tr>
                </thead>
                <tbody>
                @if(count($CronJobDetail) > 0)
                    @foreach($CronJobDetail as $key => $Detail)
                        <tr>
                            <td class="one_setting_vt">
                                <p><span>{{$CronJobDetail->firstItem() + $key}}</span></p>
                            </td>
                            <td class="one_setting_vt">
                               <span>{{$Detail->type}}</span>
                            </td>
                            <td>
                                <span>{{$Detail->status}}</span>
                            </td>
                            <td>
                                <span>{{$Detail->start_time}}</span>
                            </td>
                            <td>
                                <span>{{$Detail->end_time}}</span>

                            </td>
                            <td>
                                @if($Detail->end_time == "0000-00-00 00:00:00")
                                    <span>N/A</span>
                                @else
                                    <?php
                                    $totalTime = strtotime($Detail->end_time) - strtotime($Detail->start_time);
                                    ?>
                                    <span>{{Date("i", $totalTime) }} mints</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>

            </table>


        </div>
        <div class="col-md-2" style=" float: right; padding: 15px 0px 0 0px" >
            {{ $CronJobDetail->links() }}
        </div>
    </div>
@endsection
