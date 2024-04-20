@extends('layouts.admin.master')
@section('title', 'Edit Plants')
@section('content')
    <style type="text/css">
        .controls {
            background-color: #fff;
            border-radius: 2px;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            height: 29px;
            margin-left: 17px;
            margin-top: 10px;
            outline: none;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 400px;
        }

        .controls:focus {
            border-color: #4d90fe;
        }
    </style>
    @php
        $total_mppts = $plant_details->plant_mppts && count($plant_details->plant_mppts) > 0? $plant_details->plant_mppts[0]->total_mppt : [];
        $mppt_object = $plant_details->plant_mppts ? $plant_details->plant_mppts : [];
    @endphp
    <img id="mapMarkerIcon" src="{{ asset('assets/images/map_marker.svg')}}" alt="setting" style="display: none;">
    <div class="bred_area_vt">
        <div class="row">
            <div class="col-xl-12">
                <div class="home-companies-area-vt">
                    <div class="btn-companies-vt">
                        <a href="{{ url('admin/edit-plant/'.$plant_details->id)}}">
                            <button name="refresh" type="button" class="btn-clear-ref-vt">
                                <img src="{{ asset('assets/images/refresh.png')}}" alt="refresh">
                            </button>
                        </a>
                    @if(Auth::user()->roles == 1 || Auth::user()->roles == 3)
                        <!-- <a href="{{ url('admin/build-plant')}}">
                            <button type="button" class="btn-add-vt">
                                Build Plant
                            </button>
                        </a> -->
                        @endif
                        <p>Updated at {{date('h:i A d-m-Y')}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid px-xl-5">
        <section>
            <div class="row">
                <div class="col-md-12">
                    <div class="card-header">
                        <div class="report-head-vt">
                            <h4>Edit Plant</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 build_plant_table_vt">
                    <div class="card-box">
                        @include('alert')
                        <form class="parsley-examples" id="editPlantForm" method="post" action="{{ url('admin/store-plant') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">

                                    <div class="img_log_them_vt">

                                        <img src="{{ $plant_details->plant_pic ? asset('plant_photo/'.$plant_details->plant_pic) : asset('plant_photo/plant_avatar.png')}}" alt="">

                                    </div>

                                </div>

                                <div class="col-md-6">

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Plant Picture</label>
                                        <div class="file-upload">
                                            <div class="file-select">
                                                <div class="file-select-button" id="fileName">Choose File</div>
                                                <div class="file-select-name" id="noFile">No file chosen...</div>
                                                <input type="file" name="plant_pic" id="chooseFile">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="{{$plant_details->id}}">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="siteId">Site ID<span class="text-danger">*</span></label>
                                        <select class="form-control select2-multiple" id="siteId" name="siteId[]" data-toggle="select2" multiple="multiple" required>
                                            @foreach($plant_sites as $p => $plant_site)
                                                <option value="{{ $plant_site->site_id }}" @foreach ($plant_sites_arr as $arr) {{ $arr == $plant_site->site_id ? 'selected' : '' }} @endforeach>{{ $plant_site->site_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Plant Name<span class="text-danger">*</span></label>
                                        <input type="text" name="plant_name" class="form-control" value="{{$plant_details->plant_name}}" placeholder="Type Plant Name" required/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>API Key LED Integration</label>
                                        <input type="text" class="form-control" name="led_api_key" value="{{$plant_details->api_key}}" placeholder="Enter Thingspeak key" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>System Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="system_type" id="system_type" required>
                                            <option value="">Select</option>
                                            @if($system_types)
                                                @foreach($system_types as $system_type)
                                                    <option value="{{ $system_type->id }}" {{ $plant_details->system_type == $system_type->id ? 'selected' : '' }}>{{ $system_type->type }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Plant Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="plant_type" id="plant_type" required>
                                            <option value="">Select</option>
                                            @if($plant_types)
                                                @foreach($plant_types as $plant_type)
                                                    <option value="{{ $plant_type->id }}" {{ $plant_details->plant_type == $plant_type->id ? 'selected' : '' }}>{{ $plant_type->type }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="mpptStringDiv">
                                        <label>Select MPPT String</label>
                                        <select class="form-control" name="total_mppt" id="changeMPPT">
                                            <option value="">Select MPPT</option>
                                            <option value="1"{{ $total_mppts == 1 ? 'selected' : '' }}>MPPT 1</option>
                                            <option value="2"{{ $total_mppts == 2 ? 'selected' : '' }}>MPPT 2</option>
                                            <option value="3"{{ $total_mppts == 3 ? 'selected' : '' }}>MPPT 3</option>
                                            <option value="4"{{ $total_mppts == 4 ? 'selected' : '' }}>MPPT 4</option>
                                            <option value="5"{{ $total_mppts == 5 ? 'selected' : '' }}>MPPT 5</option>
                                            <option value="6"{{ $total_mppts == 6 ? 'selected' : '' }}>MPPT 6</option>
                                            <option value="7"{{ $total_mppts == 7 ? 'selected' : '' }}>MPPT 7</option>
                                            <option value="8"{{ $total_mppts == 8 ? 'selected' : '' }}>MPPT 8</option>
                                            <option value="9"{{ $total_mppts == 9 ? 'selected' : '' }}>MPPT 9</option>
                                        </select>
                                        <div id="appendStringSelected">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Designed Capacity<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{$plant_details->capacity}}" name="capacity" placeholder="kW" required/>
                                    </div>
                                </div>
                                <div class="col-md-6" id="meterTypeClass">
                                    <div class="form-group">
                                        <label>Meter Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="meter_type" id="meterType" required>
                                            <option value="">Select</option>
                                            <option value="Saltec" {{ $plant_details->meter_type == 'Saltec' ? 'selected' : '' }}>Saltec</option>
                                            <option value="Microtech" {{ $plant_details->meter_type == 'Microtech' ? 'selected' : '' }}>Microtech</option>
                                            <option value="Saltec-Goodwe" {{ $plant_details->meter_type == 'Saltec-Goodwe' ? 'selected' : '' }}>Saltec + Goodwe</option>
                                            <option value="Microtech-Goodwe" {{ $plant_details->meter_type == 'Microtech-Goodwe' ? 'selected' : '' }}>Microtech + Goodwe</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 meterTypeFields">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Meter ID</label>
                                            <input type="text" class="form-control" id="meter_serial_no" value="{{$plant_details->meter_serial_no}}" name="meter_serial_no" placeholder="1545853" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 meterTypeFields">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Ratio Factor</label>
                                            <input type="text" class="form-control" value="{{$plant_details->ratio_factor}}" name="ratio_factor" min="0.00000000000001" value="1" placeholder="1" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Number</label>
                                        <input type="text" class="form-control" value="{{$plant_details->phone}}" name="phone" placeholder="Contact Number" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Time Zone<span class="text-danger">*</span></label>
                                        <select class="form-control" name="timezone" id="system_type" required>
                                            <option value="">Select</option>
                                            <option value="Asia/Karachi" {{ $plant_details->timezone == 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Company<span class="text-danger">*</span></label>
                                        <select class="form-control" name="company_id" id="system_type" required>
                                            <option value="">Select</option>
                                            @if($companies)
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" {{ $plant_details->company_id == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Benchmark Price<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{$plant_details->benchmark_price}}" name="benchmark_price" placeholder="PKR/KWh" required/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Daily Expected Generation<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{$plant_details->expected_generation}}" name="expected_generation" placeholder="kWh/kWp" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Azimuth</label>
                                        <input type="text" class="form-control" value="{{$plant_details->azimuth}}" name="azimuth" placeholder="Azimuth">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Angle</label>
                                        <input type="text" class="form-control" value="{{$plant_details->angle}}" name="angle" placeholder="Angle">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Latitude<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control loc_lat1" value="{{$plant_details->loc_lat}}" name="loc_lat" id="loc_lat" placeholder="Latitude" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Longitude<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control loc_long1" value="{{$plant_details->loc_long}}" name="loc_long" id="loc_long" placeholder="Longitude" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Address<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{$plant_details->location}}" name="location" id="location" placeholder="Address" required>
                                    </div>
                                </div>

                                <input type="hidden" name="isOnline" id="isOnline" value="{{$plant_details->isOnline}}">
                                <input type="hidden" name="alarmLevel" id="alarmLevel" value="{{$plant_details->alarmLevel}}">
                                <input type="hidden" name="city" id="city" value="{{$plant_details->city}}">
                                <input type="hidden" name="province" id="province" value="{{$plant_details->province}}">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <div style="display: none">
                                            <input id="pac-input" class="controls" type="text" placeholder="Type address here">
                                        </div>
                                        <div class="card-body map_body_vt">
                                            <div id="map"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($is_build == 0)
                                <div class="card hum_tum_vt pla_body_padd_vt">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card-header">
                                                    <h3 class="All-graph-heading-vt">Users List</h3>
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
                                            <table class="table table-borderless table-centered table-nowrap">
                                                <thead class="thead-light vt_head_td">
                                                <tr>
                                                    <td style="background: #e7e9eb;border-bottom: 1px solid #e7e9eb;">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="customCheck2">
                                                            <label class="custom-control-label" for="customCheck2">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <th>
                                                        <select class="form-control" data-toggle="select2">
                                                            <option>Name</option>
                                                            <optgroup>
                                                                <option value="AK">Name</option>
                                                                <option value="HI">Hawaii</option>
                                                                <option value="CA">California</option>
                                                                <option value="NV">Nevada</option>
                                                                <option value="OR">Oregon</option>
                                                                <option value="WA">Washington</option>
                                                            </optgroup>
                                                        </select>
                                                    </th>
                                                    <th>
                                                        <select class="form-control" data-toggle="select2">
                                                            <option>Email</option>
                                                            <optgroup label="Alaska">
                                                                <option value="AK">Name</option>
                                                                <option value="HI">Hawaii</option>
                                                                <option value="CA">California</option>
                                                                <option value="NV">Nevada</option>
                                                                <option value="OR">Oregon</option>
                                                                <option value="WA">Washington</option>
                                                            </optgroup>
                                                        </select>
                                                    </th>
                                                    <th>
                                                        <select class="form-control" data-toggle="select2">
                                                            <option>Role</option>
                                                            <optgroup label="Alaska">
                                                                <option value="AK">Capacity</option>
                                                                <option value="HI">Hawaii</option>
                                                                <option value="CA">California</option>
                                                                <option value="NV">Nevada</option>
                                                                <option value="OR">Oregon</option>
                                                                <option value="WA">Washington</option>
                                                            </optgroup>
                                                        </select>
                                                    </th>
                                                    <th>
                                                        <select class="form-control" data-toggle="select2">
                                                            <option>Company Name</option>
                                                            <optgroup label="Alaska">
                                                                <option value="AK">Expected Generation</option>
                                                                <option value="HI">Hawaii</option>
                                                                <option value="CA">California</option>
                                                                <option value="NV">Nevada</option>
                                                                <option value="OR">Oregon</option>
                                                                <option value="WA">Washington</option>
                                                            </optgroup>
                                                        </select>
                                                    </th>
                                                    <th>
                                                        <select class="form-control" data-toggle="select2">
                                                            <option>Plant Name</option>
                                                            <optgroup label="Alaska">
                                                                <option value="AK">Daily Expected</option>
                                                                <option value="HI">Hawaii</option>
                                                                <option value="CA">California</option>
                                                                <option value="NV">Nevada</option>
                                                                <option value="OR">Oregon</option>
                                                                <option value="WA">Washington</option>
                                                            </optgroup>
                                                        </select>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="btn_a_vt">
                                                <!-- @if($plants) -->
                                                <input type="hidden" id="key" value="{{ count($plants) }}">
                                                <!-- @foreach($plants as $key => $plant) -->
                                                <tr>
                                                    <td>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="customCheck2">
                                                            <label class="custom-control-label" for="customCheck2">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        Jerry Young
                                                    </td>
                                                    <td>
                                                        frank.hoffman@mail.com
                                                    </td>
                                                    <td>
                                                        ABC
                                                    </td>
                                                    <td>
                                                        Beacon
                                                    </td>
                                                    <td>
                                                        Beacon<br>
                                                        Beacon<br>
                                                        Beacon
                                                    </td>
                                                </tr>
                                                <!-- @endforeach -->
                                                <!-- @endif -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination justify-content-end mb-0">
                                                <li class="page-item disabled">
                                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                                </li>
                                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">Next</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            @endif
                            <div class="build_plant_btn_vt">
                                <div class="form-group mb-0">
                                    <div>
                                        <button type="submit" class="btn-create-vt" id="buildPlantBtn">
                                            Update Plant
                                        </button>
                                        <button type="reset" class="btn-close-vt">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
    </div>

    <div class="build_plan_model_vt modal fade" id="mpptModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    Select MPPT Strings
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-create-vt" id="saveMPPTString">Save</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script type="text/javascript" defer>

        $(document).ready(function() {

            $('.loc_lat1').on('change paste keyup', function() {

                var lat = $('.loc_lat1').val();

                var long = $('.loc_long1').val();

                if (long != '' && lat.length > 5) {

                    console.log(lat);

                    console.log(long);

                    initMap();

                }

            });

            $('.loc_long1').on('change paste keyup', function() {

                var lat = $('.loc_lat1').val();

                var long = $('.loc_long1').val();

                if (lat != '' && long.length > 5) {

                    initMap();

                }

            });

            {{--$('#siteId').change(function () {--}}

            {{--    var site_id_arr = $('#siteId').val();--}}
            {{--    var lat_arr;--}}
            {{--    var long_arr;--}}

            {{--    if(site_id_arr.length > 0) {--}}

            {{--        $.ajax({--}}
            {{--            url:"{{ route('admin.build.plant.getLatLong') }}",--}}
            {{--            method: "GET",--}}
            {{--            data: {--}}
            {{--                'site_id_arr': site_id_arr--}}
            {{--            },--}}
            {{--            dataType: 'json',--}}
            {{--            success:function(data)--}}
            {{--            {--}}
            {{--                $('#loc_lat').val(data[0][0]);--}}
            {{--                $('#loc_long').val(data[1][0]);--}}

            {{--                var lat = $('#loc_lat').val();--}}

            {{--                var long = $('#loc_long').val();--}}

            {{--                if (lat != '' && long != '' && lat != null && long != null && lat.length > 5 && long.length > 5) {--}}

            {{--                    console.log(lat);--}}

            {{--                    console.log(long);--}}

            {{--                    get_data_agaist_lat_log(lat, long)--}}

            {{--                }--}}
            {{--                else {--}}

            {{--                    $('#location').val('');--}}
            {{--                }--}}
            {{--            },--}}
            {{--            error:function(data)--}}
            {{--            {--}}
            {{--                console.log(data);--}}
            {{--                alert('Some error occurred!');--}}
            {{--            }--}}
            {{--        });--}}
            {{--    }--}}

            {{--});--}}

            var mppt_arr = {};
            var mppt_arr_temp = {!! $mppt_object !!};

            if(mppt_arr_temp.length > 0) {

                for(arr in mppt_arr_temp) {

                    $('#appendStringSelected').append(mppt_arr_temp[arr].string+'>'+mppt_arr_temp[arr].string_mppt+', ');
                    mppt_arr[mppt_arr_temp[arr].string] = mppt_arr_temp[arr].string_mppt;
                }
            }

            $('#editPlantForm').on('submit', function(event){
                $(this).find(':input[type=submit]').attr('disabled', 'disabled');
                event.preventDefault();
                var formdata = new FormData(this);
                formdata.append('mppt_str', JSON.stringify(mppt_arr));
                $.ajax({
                    url:"{{ route('admin.update.bel.plant') }}",
                    method:"POST",
                    data:formdata,
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success:function(data)
                    {
                        console.log(data,'kds');
                        if(data.error_status == 1){
                            // alert(data.message);
                            $('#buildPlantBtn').removeAttr('disabled');
                        }
                        else {
                                console.log('OK', data);
                            //  alert(data.message);
                            // location.href = "{{ URL('admin/bel/user-plant-detail') }}" +"/" + data.plant_id;
                        }
                    },
                    error:function(data)
                    {
                        //console.log(data);
                        alert('Some error occurred!');
                        $('#buildPlantBtn').removeAttr('disabled');
                    }
                });
            });


            $('.meterTypeFields').hide();

            $('#meterType').on('change', function() {

                check_meter_type();
            });

            $('#saveMPPTString').on('click', function() {

                mpptID = $('#changeMPPT').val();

                mppt_arr = {};

                for(var i = 1; i <= mpptID * 2; i++) {

                    str_check = $('#string'+i+'mppt').val();

                    if(str_check.search('Select') < 0) {

                        mppt_arr['String'+i] = $('#string'+i+'mppt').val();
                    }
                }

                $('#appendStringSelected').empty();

                for(arr in mppt_arr) {

                    $('#appendStringSelected').append(arr+'>'+mppt_arr[arr]+', ');
                }

            });

            function showMapLocation() {

                var lat = $('.loc_lat1').val();

                var long = $('.loc_long1').val();

                if (long != '' && lat.length > 5) {

                    initMap();

                }
            }

            setTimeout(check_meter_type, 1000);
            setTimeout(showMapLocation, 1000);
        });

        function check_meter_type() {

            var metr_type = $('#meterType').val();
            console.log(metr_type);

            if(metr_type == 'Microtech' || metr_type == 'Microtech-Goodwe') {

                $('#meterTypeClass').removeClass('col-md-6');
                $('#meterTypeClass').toggleClass('col-md-2');
                $('.meterTypeFields').css("display", "block");
                $('#meter_serial_no').attr('required', 'required');
                console.log('Microtech Done');
            }
            else {

                if($('#meterTypeClass').hasClass('col-md-2')) {

                    $('#meterTypeClass').removeClass('col-md-2');
                    $('#meterTypeClass').toggleClass('col-md-6');
                }

                $('#meter_serial_no').removeAttr('required');
                $('.meterTypeFields').css("display", "none");
            }
        }
    </script>
    <script>
        var map;

        function initMap() {
            var map_lat = $('.loc_lat1').val();
            var map_long = $('.loc_long1').val();

            if(map_lat == '') {

                map_lat = 30.3753;
            }
            if(map_long == '') {

                map_long = 69.3451;
            }

            map = new google.maps.Map(
                document.getElementById('map'), {
                    center: new google.maps.LatLng(map_lat, map_long),
                    zoom: 9
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
                {
                    position: new google.maps.LatLng(map_lat, map_long),
                }
            ];

            console.log(features);

            var contentString = [];

            // Create markers.
            for (var i = 0; i < features.length; i++) {
                var marker = new google.maps.Marker({
                    position: features[i].position,
                    map: map,
                    icon: document.getElementById('mapMarkerIcon').src
                });
            };
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB12hWno8_DIMqw7xCV1QeqYn6I8FiIxVw&callback=initMap">
    </script>
@endsection
