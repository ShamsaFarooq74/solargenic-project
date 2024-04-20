@extends('layouts.admin.master')
@section('title', 'Build Plants')
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

        div#battery-serial-number-data {
            width: 100%;
            padding: 0 15px;
        }
        .controls:focus {
            border-color: #4d90fe;
        }

        .check_emi {
            width: 100%;
            float: left;
            padding-top: 10px;
            font-size: 12px;
            font-weight: 300;
            color: #9C9C9C;
        }

        .check_emi input {
            transform: translateY(2px);
        }

        .inverter_list_vt label {
            border: 1px solid rgb(204 204 204 / 40%);
            margin-right: 5px;
            padding: 5px;
            /* width: 33.3%; */
            /* float: left; */
            margin-bottom: 15px;
        }

        .inverter_list_vt label input {
            width: 100% !important;
        }
    </style>
    <img id="mapMarkerIcon" src="{{ asset('assets/images/map_marker.svg')}}" alt="setting" style="display: none;">
    <div class="bred_area_vt">
        <div class="row">
            <div class="col-xl-12">
                <div class="home-companies-area-vt">
                    <div class="btn-companies-vt">
                        <a href="{{ route('admin.build.plant', ['type' => $type])}}">
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
                            <h4>Build Plant</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 build_plant_table_vt">
                    <div class="card-box">
                        @include('alert')
                        <form class="parsley-examples" id="buildPlantForm" method="post"
                              action="{{ url('admin/store-plant') }}" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Inverter Type<span class="text-danger">*</span></label>
                                        <select name="plant_meter_type" id="plant_meter_type" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($plantMeterType as $key => $item)
                                                <option value="{{ $item->id }}">{{ $item->meter_type_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 sunGrowCredentialDiv" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>App Key<span class="text-danger">*</span></label>
                                                <input type="text" id="app_key" name="app_key"
                                                       value="{{$sunGrowCredentials['sunGrowAppKey']}}"
                                                       class="form-control"
                                                       placeholder="Type App Key" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>User Account<span class="text-danger">*</span></label>
                                                <input type="text" id="user_account" name="user_account"
                                                       value="{{$sunGrowCredentials['sunGrowUserAccount']}}"
                                                       class="form-control" placeholder="Type User Account" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>User Password<span class="text-danger">*</span></label>
                                                <input type="text" id="user_password" name="user_password"
                                                       value="{{$sunGrowCredentials['sunGrowPassword']}}"
                                                       class="form-control"
                                                       placeholder="Type User Password" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 huaweiCredentialDiv" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Username<span class="text-danger">*</span></label>
                                                <input type="text" id="username" name="username" class="form-control"
                                                       placeholder="Type Username" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>System Code<span class="text-danger">*</span></label>
                                                <input type="text" id="system_code" name="system_code"
                                                       class="form-control" placeholder="Type System Code" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 solisCredentialDiv" style="display: none;"  >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>App ID<span class="text-danger">*</span></label>
                                                <input type="text" id="app_id" name="app_id" class="form-control"
                                                  value="{{$SolisCrediential->appID}}"     placeholder="Type App ID" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Org ID<span class="text-danger">*</span></label>
                                                <input type="text" id="org_id" name="org_id" class="form-control"
                                                       value="{{$SolisCrediential->OrgId}}" placeholder="Type Org ID" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>App Secret<span class="text-danger">*</span></label>
                                                <input type="text" id="app_secret" name="app_secret"
                                                       value="{{$SolisCrediential->appKey}}"  class="form-control" placeholder="Type App Secret" required/>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Organization ID<span class="text-danger">*</span></label>
                                                <input type="text" id="org_id" name="org_id" class="form-control" placeholder="Type Organization ID" required/>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Username<span class="text-danger">*</span></label>
                                                <input type="text" id="solis_username" name="solis_username"
                                                       value="{{$SolisCrediential->userAccount}}"  class="form-control" placeholder="Type Username" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Password<span class="text-danger">*</span></label>
                                                <input type="text" id="solis_password" name="solis_password"
                                                       value="{{$SolisCrediential->userPassword}}"  class="form-control" placeholder="Type Password" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 solicCloudCredentialDiv" style="display: none;"  >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>App ID<span class="text-danger">*</span></label>
                                                <input type="text" id="api_key" name="api_key" class="form-control"
                                                  value="{{$SolicCloudCredentials['solicCloudApiKey']}}"  placeholder="Api Key Required" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>App Secret<span class="text-danger">*</span></label>
                                                <input type="text" id="secret_key" name="secret_key" class="form-control"
                                                       value="{{$SolicCloudCredentials['solicCloudSecretKey']}}" placeholder="Secret Key" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Base Url<span class="text-danger">*</span></label>
                                                <input type="text" id="base_url" name="base_url"
                                                       value="{{$SolicCloudCredentials['solicCloudBaseUrl']}}"  class="form-control" placeholder="Base Url" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 getSiteIDButtonDiv" style="display: none;">
                                    <div class="form-group">
                                        <button class="btn btn-primary w-100 mt-3 getSiteIDButton">Get Site List
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 siteIDListDiv" style="display: none;">
                                    <div class="form-group">
                                        <label>Site ID<span class="text-danger">*</span></label>
                                        <select class="form-control" id="site_id" name="site_id" required>
                                            <option value="">Select Site ID</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6 siteIDListDiv" style="display: none;">
                                    <div class="form-group">
                                        <label>Site ID<span class="text-danger">*</span></label>
                                        <select class="form-control select2-multiple" id="siteId" name="siteId[]" data-toggle="select2" multiple="multiple" required>
                                            <option value="">Select Site ID</option>
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Plant Name<span class="text-danger">*</span></label>
                                        <input type="text" id="plant_name" name="plant_name" class="form-control"
                                               placeholder="Type Plant Name" required/>
                                    </div>
                                </div>
                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>API Key LED Integration<span class="text-danger">*</span></label>
                                        <input type="text" id="led_api_key" name="led_api_key" class="form-control"
                                               placeholder="Enter Thinkspeak key" required/>
                                    </div>
                                </div>
                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Plant Build Date</label>
                                        <input type="date" id="plant_build_date" name="plant_build_date"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Plant Net Meter Date</label>
                                        <input type="date" id="plant_net_meter_date" name="plant_net_meter_date"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Grid Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="grid_type" id="grid_type" required>
                                            <option value="">Select</option>
                                            <option value="Single-phase">Single-phase</option>
                                            <option value="Three-phase">Three-phase</option>
                                            <option value="Three-phase-string">Three-phase-string</option>

                                        </select>
                                    </div>
                                </div>
{{--                                <div class="col-md-3 generalPlantFields" style="display: none;">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label>EMI Present</label>--}}
{{--                                        <label class="check_emi">--}}
{{--                                            <input type="checkbox" name="plant_has_emi" value="Y">--}}
{{--                                            <span class="checkmark"></span>--}}
{{--                                            Checked--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-md-6 generalPlantFields inverterListDiv inverter_list_vt"
                                     style="display: none;">

                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>System Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="system_type" id="system_type" required>
                                            <option value="">Select</option>
                                            @if($system_types)
                                                @foreach($system_types as $system_type)
                                                    <option
                                                        value="{{ $system_type->id }}">{{ $system_type->type }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Plant Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="plant_type" id="plant_type" required>
                                            <option value="">Select</option>
                                            @if($plant_types)
                                                @foreach($plant_types as $plant_type)
                                                    <option
                                                        value="{{ $plant_type->id }}">{{ $plant_type->type }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="spinner-border text-success buildPlantSpinner" role="status"
                                     style="display: none;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Designed Capacity<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="capacity" id="capacity"
                                               placeholder="kW" required/>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Installed Capacity<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="installed_capacity"
                                               id="installed_capacity" placeholder="kWp" required/>
                                    </div>
                                </div>
{{--                                <div class="col-md-6" id="meterTypeClass" style="display: none;">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label>Energy Meter Type<span class="text-danger">*</span></label>--}}
{{--                                        <select class="form-control" name="meter_type" id="meterType">--}}
{{--                                            <option value="Saltec">Saltec</option>--}}
{{--                                            <option value="Microtech">Microtech</option>--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-md-2 meterTypeFields" style="display: none;">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Meter ID</label>
                                            <input type="text" id="meter_serial_no" class="form-control"
                                                   name="meter_serial_no" placeholder="1545853"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 meterTypeFields" style="display: none;">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Ratio Factor</label>
                                            <input type="text" class="form-control" name="ratio_factor"
                                                   min="0.00000000000001" value="1" placeholder="1"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Contact Number<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="phone"
                                               placeholder="Contact Number" id="phone"/>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Time Zone<span class="text-danger">*</span></label>
                                        <select class="form-control" name="timezone" id="timezone" required>
                                            <option value="">Select</option>
                                            <option value="Asia/Karachi">Asia/Karachi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Company<span class="text-danger">*</span></label>
                                        <select class="form-control" name="company_id" id="company_id" required>
                                            <option value="">Select</option>
                                            @if($companies)
                                                @foreach($companies as $company)
                                                    <option
                                                        value="{{ $company->id }}">{{ $company->company_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Benchmark Price<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="benchmark_price"
                                               name="benchmark_price" placeholder="PKR/KWh" required/>
                                    </div>
                                </div>
                                <div class="col-md-6 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Daily Expected Generation<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="expected_generation"
                                               name="expected_generation" placeholder="kWh/kWp" required>
                                    </div>
                                </div>

                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Azimuth</label>
                                        <input type="text" class="form-control" id="azimuth" name=" azimuth"
                                            placeholder="Azimuth">
                                    </div>
                                </div>
                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Angle</label>
                                        <input type="text" class="form-control" id="angle" name="angle" placeholder="Angle">
                                    </div>
                                </div>
                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Latitude<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control loc_lat1" name="loc_lat" id="loc_lat"
                                               placeholder="Latitude" required>
                                    </div>
                                </div>
                                <div class="col-md-3 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Longitude<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control loc_long1" name="loc_long" id="loc_long"
                                               placeholder="Longitude" required>
                                    </div>
                                </div>
                                 <div class="generalPlantSolicCloudMain row">
                                    <div class="col-md-4 generalPlantFields" style="display: none;">
                                        <div class="form-group">
                                            <label>Battery Ah<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="" name="battery_ah"
                                                placeholder="Battery Ah" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 generalPlantFields" style="display: none;">
                                        <div class="form-group">
                                            <label>Battery DOD<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="" name="battery_dod"
                                                placeholder="Battery DOD" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 generalPlantFields" style="display: none;">
                                        <div class="form-group">
                                            <label>Battery Voltage<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="" name="battery_voltage"
                                                placeholder="Battery Voltage" required>
                                        </div>
                                    </div>
                                </div>
                                     <div class="col-md-4 generalPlantFields" style="display: none;">
                                         <div class="form-group">
                                             <label>Peak Tariff Rate</label>
                                             <input type="text" class="form-control" value="" name="peak_terriff_rate"
                                                 placeholder="Peak Tariff Rate">
                                         </div>
                                     </div>
                                    <div class="col-md-4 generalPlantFields" style="display: none;">
                                        <div class="form-group">
                                            <label>Peak Start Time</label>
                                            <input type="time" class="form-control" value="" name="peak_start_time"
                                                placeholder="Peak Start Time">
                                        </div>
                                    </div>
                                    <div class="col-md-4 generalPlantFields" style="display: none;">
                                        <div class="form-group">
                                            <label>Peak End Time</label>
                                            <input type="time" class="form-control" value="" name="peak_end_time"
                                                placeholder="Peak End Time">
                                        </div>
                                    </div>
                                     <div class="generalPlantSolicCloudMain row">
                                    <div class="col-md-12 generalPlantFields" style="display: none;">
                                        <div class="form-group">
                                            <label for="number-of-batteries-data">Number of Batteries</label>
                                            <select class="form-control" id="number-of-batteries-data" name="battery_number">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row generalPlantFields"  style="display: none;" id="battery-serial-number-data">
                                        <div class="col-md-12">
                                            <div class="form-group" >
                                                <label>Battery Serial Number</label>
                                                <input type="text" class="form-control" value="" name="battery_serial_no[]"
                                                    placeholder="Battery Serial Number">
                                            </div>

                                        </div>
                                    </div>  
                                </div>
                                <div class="col-md-12 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Address<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="" name="location" id="location"
                                               placeholder="Enter" required>
                                    </div>
                                </div>
                                <input type="hidden" name="isOnline" id="isOnline">
                                <input type="hidden" name="alarmLevel" id="alarmLevel">
                                <input type="hidden" name="city" id="city">
                                <input type="hidden" name="province" id="province">
                                <div class="col-md-12 generalPlantFields" style="display: none;">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <div style="display: none">
                                            <input id="pac-input" class="controls" type="text"
                                                   placeholder="Type address here"
                                                   value="43 Gurumangat Rd, Block N Gulberg III, Lahore, Punjab, Pakistan">
                                        </div>
                                        <div class="card-body map_body_vt">
                                            <div id="map"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="build_plant_btn_vt">
                                <div class="form-group mb-0">
                                    <div>
                                        <button type="submit" class="btn-create-vt generalPlantFields"
                                                style="display: none;" id="buildPlantBtn">
                                            Build Plant
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
     @php
     $ExpectedDefault = App\Http\Models\Setting::where('perimeter', 'expect_generation_1Kw')->value('value');
     @endphp
     <input type="hidden" value="{{ $ExpectedDefault }}" name="ExpectedDefaultvalue" id="ExpectedDefaultvalue" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function () {
            // let batteryData = $('#number-of-batteries-data').find(":selected").text();
            $('#number-of-batteries-data').change(function () {
                let selectedFiledData = $(this).val();
                document.getElementById('battery-serial-number-data').innerHTML = '';
                for (let k = 0; k < selectedFiledData; k++) {

                    let innerHtml = `<div class="col-md-4">
                                        <div class="form-group" >
                                            <label>Battery Serial Number</label>
                                            <input type="text" class="form-control" value="" name="battery_serial_no[]"
                                                   placeholder="Battery Serial Number">
                                        </div>

                                    </div>`;
                    $('#battery-serial-number-data').append(innerHtml);
                }

            });

        });

        var siteDataObject;
        var ExpectedDefault = $('#ExpectedDefaultvalue').val();
        var plantVendorType = $('#plant_meter_type');
        var siteID = $('#site_id');
        var inverterListDiv = $('.inverterListDiv');
        var plantName = $('#plant_name');
        var led_api_key = $('#led_api_key');
        var plantBuildDate = $('#plant_build_date');
        var plantNetMeterDate = $('#plant_net_meter_date');
        var capacity = $('#capacity');
        var installedCapacity = $('#installed_capacity');
        var timeZone = $('#timezone');
        var azimuth = $('#azimuth');
        var angle = $('#angle');
        var phone = $('#phone');
        var companyID = $('#company_id');
        var benchmarkPrice = $('#benchmark_price');
        var expectedGeneration = $('#expected_generation');
        var systemType = $('#system_type');
        var gridType = $('#grid_type');
        var plantType = $('#plant_type');
        var latitude = $('#loc_lat');
        var longitude = $('#loc_long');
        var address = $('#location');
        // var energyMeterType = $('#meterTypeClass');
        var generalPlantFields = $('.generalPlantFields');
        var generalPlantSolicCloudMain = $('.generalPlantSolicCloudMain');
        $('.solisCredentialDiv').hide();
        $('.getSiteIDButtonDiv').hide();
         
        if (systemType.val() == 4) {
            generalPlantSolicCloudMain.show();
          }else{
            generalPlantSolicCloudMain.show();
        }

       
        //System Type on Change
        // systemType.change(function () {
        //     alert(systemType);
        //     console.log(systemType);
        //     if(plantVendorType.val() == 4){
        //         generalPlantSolicCloudMain.show();
        //     }else{
        //         generalPlantSolicCloudMain.hide();
        //     }
        // });
        plantVendorType.change(function () {

            if ($(this).val() == '') {

                $('.solisCredentialDiv').hide();
                $('.sunGrowCredentialDiv').hide();
                $('.huaweiCredentialDiv').hide();
                $('.getSiteIDButtonDiv').hide();
                $('.solicCloudCredentialDiv').hide();
            } else {

                $('.solisCredentialDiv').hide();
                $('.sunGrowCredentialDiv').hide();
                $('.huaweiCredentialDiv').hide();
                $('.solicCloudCredentialDiv').hide();

                if ($(this).val() == 3) {

                    $('.huaweiCredentialDiv').show();
                } else if ($(this).val() == 4) {

                    $('.sunGrowCredentialDiv').show();
                } else if ($(this).val() == 5) {

                    $('.solisCredentialDiv').show();
                }
                //  else if($(this).val() == 6){
                //       $('.solicCloudCredentialDiv').show();
                // }

                $('.getSiteIDButtonDiv').show();
            }
        });

        $('.getSiteIDButton').click(function (e) {

                e.preventDefault();
                var credentialsArray = {};

                if (plantVendorType.val() == 3) {

                    if ($.trim($('#username').val()) != '') {

                        if ($.trim($('#system_code').val()) != '') {

                            credentialsArray['vendor'] = 'Huawei';
                            credentialsArray['username'] = $.trim($('#username').val());
                            credentialsArray['system_code'] = $.trim($('#system_code').val());

                            getSiteListAjax(credentialsArray);
                        } else {

                            alert('Please Enter System Code!');
                        }
                    } else {

                        alert('Please Enter Username!');
                    }
                } else if (plantVendorType.val() == 4) {
                    
                    if ($.trim($('#app_key').val()) != '') {

                        if ($.trim($('#user_account').val()) != '') {

                            if ($.trim($('#user_password').val()) != '') {

                                credentialsArray['vendor'] = 'SunGrow';
                                credentialsArray['appkey'] = $.trim($('#app_key').val());
                                credentialsArray['user_account'] = $.trim($('#user_account').val());
                                credentialsArray['user_password'] = $.trim($('#user_password').val());

                                getSiteListAjax(credentialsArray);
                            } else {

                                alert('Please Enter User Password!');
                            }
                        } else {

                            alert('Please Enter User Account!');
                        }
                    } else {

                        alert('Please Enter App Key!');
                    }
                } else if (plantVendorType.val() == 5) {

                    if ($.trim($('#app_id').val()) != '') {
                        if ($.trim($('#org_id').val()) != '') {

                            if ($.trim($('#app_secret').val()) != '') {

                                // if($.trim($('#org_id').val()) != '') {

                                if ($.trim($('#solis_username').val()) != '') {

                                    if ($.trim($('#solis_password').val()) != '') {

                                        credentialsArray['vendor'] = 'Solis';
                                        credentialsArray['app_id'] = $.trim($('#app_id').val());
                                        credentialsArray['app_secret'] = $.trim($('#app_secret').val());
                                        // credentialsArray['org_id'] = $.trim($('#org_id').val());
                                        credentialsArray['username'] = $.trim($('#solis_username').val());
                                        credentialsArray['password'] = $.trim($('#solis_password').val());
                                        credentialsArray['org_id'] = $.trim($('#org_id').val());

                                        getSiteListAjax(credentialsArray);
                                    } else {

                                        alert('Please Enter Password!');
                                    }
                                } else {

                                    alert('Please Enter Username!');
                                }
                                /*}
                                else {

                                    alert('Please Enter Organization ID!');
                                }*/
                            } else {

                                alert('Please Enter App Secret Key!');
                            }
                        } else {
                            alert('Please Enter Org ID!');
                        }
                    } else {

                        alert('Please Enter App ID!');
                    }
                }
                 else if (plantVendorType.val() == 6) {

                    if ($.trim($('#api_key').val()) != '') {

                        if ($.trim($('#secret_key').val()) != '') {

                            if ($.trim($('#base_url').val()) != '') {

                                credentialsArray['vendor'] = 'SolicCloud';
                                credentialsArray['apikey'] = $.trim($('#api_key').val());
                                credentialsArray['secretkey'] = $.trim($('#secret_key').val());
                                credentialsArray['baseurl'] = $.trim($('#base_url').val());

                                getSiteListAjax(credentialsArray);
                            } else {

                                alert('Please Enter User Base Url!');
                            }
                        } else {

                            alert('Please Enter  Secret Key!');
                        }
                    } else {

                        alert('Please Enter Base Url!');
                    }
                }
            }
        )
        ;

        siteID.change(function () {

            showPlantDataFromAPI();

            if ($.trim(siteID.val()) != '') {

                var credentialsArray = {};

                if (plantVendorType.val() == 3) {

                    credentialsArray['vendor'] = 'Huawei';
                    credentialsArray['username'] = $.trim($('#username').val());
                    credentialsArray['system_code'] = $.trim($('#system_code').val());
                    credentialsArray['site_id'] = siteID.val();
                } else if (plantVendorType.val() == 4) {

                    credentialsArray['vendor'] = 'SunGrow';
                    credentialsArray['appkey'] = $.trim($('#app_key').val());
                    credentialsArray['user_account'] = $.trim($('#user_account').val());
                    credentialsArray['user_password'] = $.trim($('#user_password').val());
                    credentialsArray['site_id'] = siteID.val();
                } else if (plantVendorType.val() == 5) {
                    credentialsArray['vendor'] = 'Solis';
                    credentialsArray['app_id'] = $.trim($('#app_id').val());
                    credentialsArray['app_secret'] = $.trim($('#app_secret').val());
                    credentialsArray['org_id'] = $.trim($('#org_id').val());
                    credentialsArray['username'] = $.trim($('#solis_username').val());
                    credentialsArray['password'] = $.trim($('#solis_password').val());
                    credentialsArray['site_id'] = siteID.val();
                }
                else if (plantVendorType.val() == 6) {
                credentialsArray['vendor'] = 'SolicCloud';
                credentialsArray['apikey'] = $.trim($('#app_key').val());
                credentialsArray['secretkey'] = $.trim($('#secret_key').val());
                credentialsArray['baseurl'] = $.trim($('#base_url').val());
                credentialsArray['site_id'] = siteID.val();
                }

                getSiteInverterListAjax(credentialsArray);
            }
        });
        
        systemType.change(function () {
        
            if (systemType.val() == 4) {
            
                generalPlantSolicCloudMain.show();
                // $('#meterType option').remove();
        
                // if (plantVendorType.val() == 3 || plantVendorType.val() == 4 || plantVendorType.val() == 5) {
        
                //     $('#meterType').append('<option value="present">Present</option><option value="not_present">Not Present</option>');
                // } else {
        
                //     $('#meterType').append('<option name="grid_meter" value="saltec">Saltec</option><option value="microtech">Microtech</option>');
                // }
        
                // energyMeterType.show();
            } else {
                generalPlantSolicCloudMain.hide();
                // energyMeterType.hide();
            }
        });

        function getSiteListAjax(credentialsArray) {

            var dataString = JSON.stringify(credentialsArray);
            var plantMeterType = plantVendorType.val();

            $.ajax({

                url: "{{ route('admin.get.site.ids') }}",
                method: "POST",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'data': dataString
                },
                dataType: 'json',
                beforeSend: function () {

                    siteID.empty();
                    $('.siteIDListDiv').show();
                    siteID.attr('disabled', 'disabled');
                    $(".buildPlantSpinner").show();
                },
                success: function (data) {
                    console.log(data);

                    siteDataObject = data;

                    siteID.append('<option value="">Select Site ID</option>');

                    if (plantMeterType == 1) {

                        $.each(data, function (index, value) {

                            siteID.append('<option value="' + value.siteId + '">' + value.siteName + '</option>');
                        });
                    } else if (plantMeterType == 3) {

                        if (data.hasOwnProperty('errorStatus') && data.errorStatus == 1) {

                            alert('Invalid Credentials!');
                        } else {

                            if (data.hasOwnProperty('cookieError') && data.cookieError == 1) {

                                $('.getSiteIDButton').trigger('click');
                            } else {

                                $.each(data, function (index, value) {

                                    siteID.append('<option value="' + value.stationCode + '">' + value.stationName + '</option>');
                                });
                            }
                        }
                    } else if (plantMeterType == 4) {

                        $.each(data, function (index, value) {

                            siteID.append('<option value="' + value.ps_id + '">' + value.ps_name + '</option>');
                        });
                    } else if (plantMeterType == 5) {

                        $.each(data, function (index, value) {

                            siteID.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else if (plantMeterType == 6) {

                        $.each(data, function (index, value) {
                            siteID.append('<option value="' + value.id + '">' + value.stationName + '</option>');
                        });
                    }

                    siteID.removeAttr('disabled');
                    $(".buildPlantSpinner").hide();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        function getSiteInverterListAjax(credentialsArray) {
        
            var dataString = JSON.stringify(credentialsArray);
            var plantMeterType = plantVendorType.val();
     
            $.ajax({

                url: "{{ route('admin.get.site.inverters') }}",
                method: "POST",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'data': dataString
                },
                dataType: 'json',
                beforeSend: function () {

                    inverterListDiv.empty();
                },
                success: function (data) {
                    console.log(data);
                 
                    if (plantMeterType == 3) {

                        if (data.hasOwnProperty('errorStatus') && data.errorStatus == 1) {

                            alert('Invalid Credentials!');
                        } else {

                            if (data.hasOwnProperty('cookieError') && data.cookieError == 1) {

                                getSiteInverterListAjax();
                            } else {

                                var lat = '';
                                var long = '';

                                $.each(data, function (index, value) {

                                    if (value.devTypeId == 1) {

                                        inverterListDiv.append('<label>' + value.devName + '<input type="text" class="form-control inverterDCPower" name="inverter_' + value.id + '" placeholder="Installed DC Power" required></label>');
                                    }

                                    if (value.latitude != null && value.latitude != '') {

                                        lat = value.latitude;
                                    }
                                    if (value.longitude != null && value.longitude != '') {

                                        long = value.longitude;
                                    }

                                    latitude.val('');
                                    longitude.val('');

                                    latitude.val(lat);
                                    longitude.val(long);
                                    getAddressUsingLatLong(lat, long);
                                    initMap();
                                });
                            }
                        }
                    } else if (plantMeterType == 4) {

                        $.each(data, function (index, value) {

                            if (value.device_type == 1) {

                                inverterListDiv.append('<label>' + value.sn + '<input type="text" class="form-control inverterDCPower" name="inverter_' + value.ps_key + '" placeholder="Installed DC Power" required></label><br>');
                            }
                        });
                    } else if (plantMeterType == 5) {

                        $.each(data, function (index, value) {

                            if (value.deviceType == 'INVERTER') {

                                inverterListDiv.append('<label>' + value.deviceId + '<input type="text" class="form-control inverterDCPower" name="inverter_' + value.deviceSn + '" placeholder="Installed DC Power" required></label><br>');
                            }
                        });
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        function showPlantDataFromAPI() {

            if ($.trim(siteID.val()) != '') {

                generalPlantFields.show();

                plantName.val('');
                plantBuildDate.val('');
                plantNetMeterDate.val('');
                capacity.val('');
                installedCapacity.val('');
                latitude.val('');
                longitude.val('');
                address.val('');

                plantName.val($("#site_id option:selected").text());
                 
                if (plantVendorType.val() == 3) {

                    $.each(siteDataObject, function (index, value) {

                        if (value.stationCode == $("#site_id option:selected").val()) {

                            installedCapacity.val((value.capacity) * 1000);
                        }
                    });
                } else if (plantVendorType.val() == 4) {

                    $.each(siteDataObject, function (index, value) {

                        if (value.ps_id == $("#site_id option:selected").val()) {

                            if (value.ps_type == 4 || value.ps_type == 5) {

                                $('#plant_type').val("1");
                            } else if (value.ps_type == 3 || value.ps_type == 7) {

                                $('#plant_type').val("2");
                            }

                            var now = new Date(value.install_date);

                            var day = ("0" + now.getDate()).slice(-2);
                            var month = ("0" + (now.getMonth() + 1)).slice(-2);
                            var today = now.getFullYear() + "-" + (month) + "-" + (day);

                            plantBuildDate.val(today);
                            capacity.val(value.design_capacity);
                            installedCapacity.val(value.total_capcity.value);
                            latitude.val(value.latitude);
                            longitude.val(value.longitude);
                            address.val(value.location);
                            getAddressUsingLatLong(value.latitude, value.longitude);
                            initMap();
                        }
                    });
                } else if (plantVendorType.val() == 5) {
                   
                    $.each(siteDataObject, function (index, value) {

                        if (value.id == $("#site_id option:selected").val()) {

                            if (value.type == "HOUSE_ROOF") {

                                $('#plant_type').val("1");
                            } else if (value.type == 'COMMERCIAL_ROOF') {

                                $('#plant_type').val("2");
                            }

                            now = new Date(1000 * (value.createdDate));

                            var day = ("0" + now.getDate()).slice(-2);
                            var month = ("0" + (now.getMonth() + 1)).slice(-2);
                            var today = now.getFullYear() + "-" + (month) + "-" + (day);

                            plantBuildDate.val(today);
                            capacity.val(value.installedCapacity);
                            installedCapacity.val(value.installedCapacity);
                            latitude.val(value.locationLat);
                            longitude.val(value.locationLng);
                            getAddressUsingLatLong(value.locationLat, value.locationLng);
                            initMap();
                        }
                    });
                }
                 else if (plantVendorType.val() == 6) {
                     
                    $.each(siteDataObject, function (index, value) {
                            console.log(value.capacityStr);
                        if (value.id == $("#site_id option:selected").val()) {
                            var fisGenerateTimeStamp   = value.fisGenerateTime;
                            var fisGeneratedate = new Date(fisGenerateTimeStamp);
                            var fisGenerateformate = moment(fisGeneratedate).format('YYYY-MM-DD');
                            var timestamp = value.createDate;
                            var date = new Date(timestamp);
                            var formattedDate = moment(date).format('YYYY-MM-DD');

                            var system_Type;
                            if (value.stationTypeNew === 4) {
                            system_Type = 2;
                            } else if (value.stationTypeNew === 1) {
                            system_Type = 4;
                            } else if (value.stationTypeNew === 0) {
                            system_Type = 1;
                            }
                            $('#plant_type').val("1");
                            systemType.val(system_Type);
                            expectedGeneration.val(value.capacity * ExpectedDefault);
                            plantBuildDate.val(formattedDate);
                            plantNetMeterDate.val(fisGenerateformate);
                            capacity.val(value.capacity);
                            installedCapacity.val(value.capacity);
                            var timeZoneString = value.timeZoneName;
                            var timeZoneName = timeZoneString.split(" ").pop();
                            timeZone.val(timeZoneName);
                            azimuth.val(value.azimuth);
                            angle.val(value.dip);
                            benchmarkPrice.val(value.price);
                            var location = value.countyStr + '.' + value.cityStr + '.' + value.regionStr + '.' +
                            value.countryStr;
                            address.val(location);
                            if (value.locationLat && value.locationLng) {
                            latitude.val(value.locationLat);
                            longitude.val(value.locationLng);
                            getAddressUsingLatLong(value.locationLat, value.locationLng);
                            initMap();
                            }else{
                             $('#province').val(value.countryStr);
                             $('#city').val(value.cityStr);
                            }
                        }
                    });
                }
            } else {

                generalPlantFields.hide();
                // energyMeterType.hide();
            }

            $('#buildPlantForm').on('submit', function (event) {

                // $(this).find(':input[type=submit]').attr('disabled', 'disabled');
                event.preventDefault();

                /*var formdata = new FormData(this);
                formdata.append('mppt_str', JSON.stringify(mppt_arr));*/

                if ($.trim(siteID.val()) != '') {

                    if ($.trim(plantName.val()) != '') {

                        var flag = false;

                        $('.inverterDCPower').filter(function () {

                            if ($(this).val() == '') {

                                flag = true;
                            }
                        });

                        if (!(flag)) {

                            if ($.trim(systemType.val()) != '') {

                                if ($.trim(plantType.val()) != '') {

                                    if ($.trim(capacity.val()) != '') {

                                        if ($.trim(installedCapacity.val()) != '') {

                                            if ($.trim(phone.val()) != '') {

                                                if ($.trim(timeZone.val()) != '') {

                                                    if ($.trim(companyID.val()) != '') {

                                                        if ($.trim(benchmarkPrice.val()) != '') {

                                                            if ($.trim(expectedGeneration.val()) != '') {

                                                                if ($.trim(latitude.val()) != '') {

                                                                    if ($.trim(longitude.val()) != '') {

                                                                        if ($.trim(address.val()) != '') {

                                                                            $('#buildPlantForm')[0].submit();
                                                                        } else {

                                                                            alert('Please enter address!');
                                                                            return false;
                                                                        }
                                                                    } else {

                                                                        alert('Please enter longitude!');
                                                                        return false;
                                                                    }
                                                                } else {

                                                                    alert('Please enter latitude!');
                                                                    return false;
                                                                }
                                                            } else {

                                                                alert('Please enter daily expected generation!');
                                                                return false;
                                                            }
                                                        } else {

                                                            alert('Please enter benchmark price!');
                                                            return false;
                                                        }
                                                    } else {

                                                        alert('Please select company!');
                                                        return false;
                                                    }
                                                } else {

                                                    alert('Please select time zone!');
                                                    return false;
                                                }
                                            } else {

                                                alert('Please enter contact number!');
                                                return false;
                                            }
                                        } else {

                                            alert('Please enter plant installed capacity!');
                                            return false;
                                        }
                                    } else {

                                        alert('Please enter plant capacity!');
                                        return false;
                                    }
                                } else {

                                    alert('Please select plant type!');
                                    return false;
                                }
                            } else {

                                alert('Please select system type!');
                                return false;
                            }
                        } else {

                            alert('Please enter inverter DC Power!');
                            return false;
                        }
                    } else {

                        alert('Please enter plant name!');
                        return false;
                    }
                } else {

                    alert('Please select site ID!');
                    return false;
                }

                /*$.ajax({
                    url:"{{ route('admin.store.plant') }}",
                method:"POST",
                data:formdata,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success:function(data)
                {
                    if(data.error_status == 1){
                        alert(data.message);
                        $('#buildPlantBtn').removeAttr('disabled');
                    }
                    else {

                        alert(data.message);
                        location.href = "{{ URL('admin/user-plant-detail') }}" +"/" + data.plant_id;
                    }

                },
                error:function(data)
                {
                    console.log(data);
                    alert('Some error occured');
                    $('#buildPlantBtn').removeAttr('disabled');
                }
            });*/
            });

            /*$('#capacity').val('');
            $('#phone').val('');
            $('#loc_lat').val('');
            $('#loc_long').val('');
            $('#location').val('');

            var plantMeterType = $('#plant_meter_type').val();
            var siteID = $('#site_id').val();

            if(!($.isEmptyObject(siteDataObject))) {

                if(siteID.length == 1) {

                    if(plantMeterType == 'Saltec') {

                        $.each(siteDataObject, function( index, value ) {

                            if(value.siteId == siteID[0]) {

                                $('#loc_lat').val(value.lat);
                                $('#loc_long').val(value.long);
                                initMap();
                            }
                        });
                    }
                    else if(plantMeterType == 'Huawei') {

                        $.each(siteDataObject, function( index, value ) {

                            if(value.stationCode == siteID[0]) {

                                $('#capacity').val(value.capacity);
                                $('#phone').val(value.linkmanPho);
                                $('#location').val(value.stationAddr);
                            }
                        });
                    }
                    else if(plantMeterType == 'SunGrow') {

                        $.each(siteDataObject, function( index, value ) {

                            if(value.ps_id == siteID[0]) {

                                if(value.ps_type == 4 || value.ps_type == 5) {

                                    $('#plant_type').val("1");
                                }
                                else if(value.ps_type == 3 || value.ps_type == 7) {

                                    $('#plant_type').val("2");
                                }

                                $('#capacity').val(value.design_capacity);
                                $('#loc_lat').val(value.latitude);
                                $('#loc_long').val(value.longitude);
                                $('#location').val(value.location);
                                initMap();
                            }
                        });
                    }
                }
            }*/
        }

        function getAddressUsingLatLong(lat, long) {

            if (lat && long) {

                $.ajax({

                    url: "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + long + "&key=AIzaSyB12hWno8_DIMqw7xCV1QeqYn6I8FiIxVw",

                    success: function (res) {

                        $('#location').val(res.results[1].formatted_address);
                        $('#pac-input').val(res.results[1].formatted_address);

                        var address = res.results[0].address_components;

                        for (var i = 0; i <= address.length; i++) {

                            if (address[i].types[0] === "administrative_area_level_2") {

                                var city = address[i].long_name;
                                $('#city').val(city);
                            } else if (address[i].types[0] === "administrative_area_level_3") {

                                var city = address[i].long_name;
                                $('#city').val(city);
                            } else if (address[i].types[0] === "locality") {

                                var city = address[i].long_name;
                                $('#city').val(city);
                            }
                            if (address[i].types[0] === "administrative_area_level_1") {

                                var province = address[i].long_name;
                                $('#province').val(province);
                            }
                        }
                    }
                });
            }
        }

    </script>
    {{-- <script>

        var siteDataObject;

        $(document).ready(function() {

            $('.loc_lat1').keyup(function() {

                var lat = $('.loc_lat1').val();

                var long = $('.loc_long1').val();

                if (long != '' && lat.length > 5) {

                    console.log(lat);

                    console.log(long);

                    initMap();

                }

            });

            $('.loc_long1').keyup(function() {

                var lat = $('.loc_lat1').val();

                var long = $('.loc_long1').val();

                if (lat != '' && long.length > 5) {

                    initMap();

                }

            });

            /*$('#siteId').change(function () {

                var site_id_arr = $('#siteId').val();
                var lat_arr;
                var long_arr;

                if(site_id_arr.length > 0) {

                    $.ajax({
                        url:"{{ route('admin.build.plant.getLatLong') }}",
                        method: "GET",
                        data: {
                            'site_id_arr': site_id_arr
                        },
                        dataType: 'json',
                        success:function(data)
                        {
                            $('#loc_lat').val(data[0][0]);
                            $('#loc_long').val(data[1][0]);

                            var lat = $('#loc_lat').val();

                            var long = $('#loc_long').val();

                            if (lat != '' && long != '' && lat != null && long != null && lat.length > 5 && long.length > 5) {

                                console.log(lat);

                                console.log(long);

                                get_data_agaist_lat_log(lat, long)

                            }
                            else {

                                $('#location').val('');
                            }

                        },
                        error:function(data)
                        {
                            console.log(data);
                            alert('Some error occurred!');
                        }
                    });
                }
            });*/

            $('#plant_meter_type').on('change', function() {

                var plantMeterType = $('#plant_meter_type').val();

                $.ajax({
                    url:"{{ route('admin.get.site.ids') }}",
                    method: "POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'plantMeterType': plantMeterType
                    },
                    dataType: 'json',
                    beforeSend: function(){

                        $('#siteId').empty();
                        $('#siteId').attr('disabled', 'disabled');
                        $(".buildPlantSpinner").show();
                    },
                    success:function(data)
                    {
                         
                        siteDataObject = data;

                        if(plantMeterType == 'Saltec') {
                            $.each(data, function( index, value ) {

                                $("#siteId").append('<option value="'+value.siteId+'">'+value.siteName+'</option>');
                            });
                        }
                        else if(plantMeterType == 'Huawei') {

                            $.each(data, function( index, value ) {

                                $("#siteId").append('<option value="'+value.stationCode+'">'+value.stationName+'</option>');
                            });
                        }
                        else if(plantMeterType == 'SunGrow') {
                              
                            $.each(data, function( index, value ) {

                                $("#siteId").append('<option value="'+value.ps_id+'">'+value.ps_name+'</option>');
                            });
                        }

                        $('#siteId').removeAttr('disabled');
                        $(".buildPlantSpinner").hide();
                    },
                    error:function(data)
                    {
                        console.log(data);
                    }
                });

                if(plantMeterType == 'Saltec' || plantMeterType == 'Microtech') {

                    $('#meterTypeClass').show();
                    $('#meterTypeClass').attr('required', 'required');
                }
                else {

                    $('#meterTypeClass').hide();
                    $('#meterTypeClass').removeAttr('required');
                }
            });

            $('#siteId').on('change', function() {

                $('#capacity').val('');
                $('#phone').val('');
                $('#loc_lat').val('');
                $('#loc_long').val('');
                $('#location').val('');

                var plantMeterType = $('#plant_meter_type').val();
                
                var siteID = $('#siteId').val();

                if(!($.isEmptyObject(siteDataObject))) {

                    if(siteID.length == 1) {

                        if(plantMeterType == 'Saltec') {

                            $.each(siteDataObject, function( index, value ) {

                                if(value.siteId == siteID[0]) {

                                    $('#loc_lat').val(value.lat);
                                    $('#loc_long').val(value.long);
                                    initMap();
                                }
                            });
                        }
                        else if(plantMeterType == 'Huawei') {

                            $.each(siteDataObject, function( index, value ) {

                                if(value.stationCode == siteID[0]) {

                                    $('#capacity').val(value.capacity);
                                    $('#phone').val(value.linkmanPho);
                                    $('#location').val(value.stationAddr);
                                }
                            });
                        }
                        else if(plantMeterType == 'SunGrow') {

                            $.each(siteDataObject, function( index, value ) {

                                if(value.ps_id == siteID[0]) {

                                    if(value.ps_type == 4 || value.ps_type == 5) {

                                        $('#plant_type').val("1");
                                    }
                                    else if(value.ps_type == 3 || value.ps_type == 7) {

                                        $('#plant_type').val("2");
                                    }

                                    $('#capacity').val(value.design_capacity);
                                    $('#loc_lat').val(value.latitude);
                                    $('#loc_long').val(value.longitude);
                                    $('#location').val(value.location);
                                    initMap();
                                }
                            });
                        }
                    }
                }
            });

                var mppt_arr = {};

                $('#buildPlantForm').on('submit', function(event){
                    $(this).find(':input[type=submit]').attr('disabled', 'disabled');
                    event.preventDefault();
                    var formdata = new FormData(this);
                    formdata.append('mppt_str', JSON.stringify(mppt_arr));
                    $.ajax({
                        url:"{{ route('admin.store.plant') }}",
                        method:"POST",
                        data:formdata,
                        dataType:'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success:function(data)
                        {
                            if(data.error_status == 1){
                                alert(data.message);
                                $('#buildPlantBtn').removeAttr('disabled');
                            }
                            else {

                                alert(data.message);
                                location.href = "{{ URL('admin/user-plant-detail') }}" +"/" + data.plant_id;
                            }

                        },
                        error:function(data)
                        {
                            console.log(data);
                            alert('Some error occured');
                            $('#buildPlantBtn').removeAttr('disabled');
                        }
                    });
                });


            $('.meterTypeFields').hide();

            $('#meterType').on('change', function() {

                var metr_type = $('#meterType').val();
                console.log(metr_type);

                if(metr_type == 'Microtech') {

                    $('#meterTypeClass').removeClass('col-md-6');
                    $('#meterTypeClass').toggleClass('col-md-2');
                    $('.meterTypeFields').show();
                    $('#meter_serial_no').attr('required', 'required');
                }
                else {

                    if($('#meterTypeClass').hasClass('col-md-2')) {

                        $('#meterTypeClass').removeClass('col-md-2');
                        $('#meterTypeClass').toggleClass('col-md-6');
                    }

                    $('#meter_serial_no').removeAttr('required');

                    $('.meterTypeFields').hide();
                }
            });

            $('#pac-input').change(function() {

                initMap();
            });

            $('#saveMPPTString').on('click', function() {

                mpptID = $('#changeMPPT').val();

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
        });
       </script>--}}
    <script>
        var map;

        function initMap() {
            var map_lat = $('.loc_lat1').val();
            var map_long = $('.loc_long1').val();

            if (map_lat == '') {

                map_lat = 30.3753;
            }
            if (map_long == '') {

                map_long = 69.3451;
            }

            map = new google.maps.Map(
                document.getElementById('map'), {
                    center: new google.maps.LatLng(map_lat, map_long),
                    zoom: 8
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
            }
            ;
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB12hWno8_DIMqw7xCV1QeqYn6I8FiIxVw&callback=initMap">
    </script>
@endsection
