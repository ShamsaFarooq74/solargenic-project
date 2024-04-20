@extends('layouts.admin.master')
@section('title', 'All Plants')
@section('content')

    @php

        $plant_counter = 1;

    @endphp
    <style>
        th.dt-center, td.dt-center {
            text-align: center !important;
        }
        div#datatable_plant_filter {
            width: 30%;
            float: left;
            padding-left: 20px;
            top: 14px;
            position: absolute;
            left: 0;
        }
        .button_new_ad_area a{
            position: fixed;
            bottom: 90px;
            right: 50px;
            font-size: 24px;
            color: #fff !important;
            width: 50px;
            height: 50px;
            background: #063c6e;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 100px;
        }
        div#datatable_plant_filter input{
            margin-left: 10px;
            border: 1px solid #ccc !important;
        }
        .dataTables_wrapper .dataTables_length select {
            min-width: 50px !important;
            border: 1px solid #ccc;
        }
    </style>

    <div class="content">

        <div class="row">
            <div class="col-lg-12 mb-1 mt-3">
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
            <div class="col-12">
                <div class="card hum_tum_vt pla_body_padd_vt pb-2 mb-4">
                    <div class="card-body mb-2">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-header border-0 mt-3" style="box-shadow: none !important;">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="{{ (Auth::user()->roles != 1) ? 'datatable_plant' : 'datatable_2' }}"
                                   class="display table table-borderless table-centered table-nowrap"
                                   style="width:100%">
                                <thead class="thead-light vt_head_td">
                                <tr>
                                    <th>Sr. #</th>
                                    @if(Auth::user()->roles != 5 && Auth::user()->roles != 6)
                                        <th>Edit</th>
                                    @endif
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th>Capacity</th>
                                    <th>Plant Type</th>
                                    <th>Daily Expected Generation</th>
                                    <th>Current Generation</th>
                                    <th>Daily Generation</th>
                                    <th>Battery SOC</th>
                                    <th>Last Alarm</th>
                                    <th>Updated at</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($plants) > 0)
                                    @foreach($plants as $key => $plant)
                                        <tr>
                                            <td class="one_setting_vt">
                                                <p>{{$plant_counter++}}</p>
                                            </td>
<!--                                            --><?php //print_r(Auth::user()->roles)?>
                                            @if(Auth::user()->roles != 5 && Auth::user()->roles != 6)
                                                <td class="one_setting_vt">
                                                    <a href="{{route('admin.edit.plant', ['type' => $plant->type,'id'=> $plant->id])}}"><img
                                                            src="{{ asset('assets/images/icon_setting.svg')}}"
                                                            alt="setting"></a>
                                                </td>
                                            @endif
                                            <td class="che_vt">
                                                @if($plant->is_online == 'Y')
                                                    <img src="{{ asset('assets/images/icon_plant_check_vt.svg')}}"
                                                         alt="check" title="Online">
                                                @elseif($plant->is_online == 'P_Y')
                                                    <img src="{{ asset('assets/images/icon_plant_alert_vt.png')}}"
                                                         alt="check" title="Partially Online">
                                                @else
                                                    <img src="{{ asset('assets/images/icon_plant_vt.svg')}}" alt="check"
                                                         title="Offline">
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ url('admin/'.$plant->type.'/user-plant-detail/'.$plant->id)}}"
                                                   title="Plant Detail">{{ $plant->plant_name }}</a>
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
                                            <?php $batterySoc = \App\Http\Models\StationBattery::where('plant_id',$plant->id)->latest()->first();
                                            $batterySocData = '---';
                                            if($batterySoc)
                                            {
                                                $batterySocData = $batterySoc->battery_capacity;
                                            }
                                            ?>
                                            <td>
                                                @if($plant->system_type == "Storage System")
                                                    {{$batterySocData}}
                                                @else
                                                   ---
                                                @endif
                                            </td>
                                            <td>
                                                {{ $plant->latest_fault_alarm_log != null ? date('h:i A d-m-Y', strtotime($plant->latest_fault_alarm_log->created_at)): '---' }}
                                            </td>
                                            <td>
                                                {{ $plant ? date('h:i A d-m-Y ', strtotime($plant->updated_at)) : date('h:i A d-m-Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
{{--    @dd($type);--}}
{{--    @if($type)--}}
    <div class="button_new_ad_area"><a href="{{route('admin.build.plant', ['type' => $type])}}" ><i class="fa fa-plus"></i></a></div>
@endsection
