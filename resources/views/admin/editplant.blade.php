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
<div class="container-fluid px-xl-5">
    <section class="py-2">
        <div class="row">
            <div class="col-12 mb-1">
                <div class="report-head-vt">
                    <h4>Edit Plant</h4>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    @include('alert')
                    <form class="parsley-examples" method="post" action="{{ url('admin/update-plant/'.$plant->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="img_log_them_vt">
                                    <img src="{{ $plant->plant_pic ? asset('plant_photo/'.$plant->plant_pic) : asset('assets/images/tree_planting.png')}}" alt="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="img_company_them_vt">
                                    <img src="{{ $plant->company ? asset('company_logo/'.$plant->company->logo) : asset('assets/images/tree_planting.png')}}" alt="">
                                </div>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Site ID<span class="text-danger">*</span></label>
                                    <select name="siteId" class="form-control site_Id_data" onchange="site_Id_data(this);" required="" readonly>
                                        <option value="{{ $plant->siteId }}" selected="">{{ $plant->siteId }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Plant Name<span class="text-danger">*</span></label>
                                    <input type="text" name="plant_name" class="form-control" required placeholder="Type Plant Name" value="{{ $plant->plant_name ? $plant->plant_name : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Plant Type<span class="text-danger">*</span></label>
                                    <select class="form-control" name="plant_type" id="plant_type" required>
                                        <option value="">Select</option>
                                        <option value="Residential Rooftop" {{ $plant->plant_type == "Residential Rooftop" ? 'selected' : '' }}>Residential Rooftop</option>
                                        <option value="Commercial Rooftop" {{ $plant->plant_type == "Commercial Rooftop" ? 'selected' : '' }}>Commercial Rooftop</option>
                                        <option value="Industrial Rooftop" {{ $plant->plant_type == "Industrial Rooftop" ? 'selected' : '' }}>Industrial Rooftop</option>
                                        <option value="Ground Mounted" {{ $plant->plant_type == "Ground Mounted" ? 'selected' : '' }}>Ground Mounted</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>System Type</label>
                                    <select class="form-control" name="system_type" id="system_type">
                                        <option value="">Select</option>
                                        <option value="All on Grid" {{ $plant->system_type == "All on Grid" ? 'selected' : '' }}>All on Grid</option>
                                        <option value="Self-consumption" {{ $plant->system_type == "Self-consumption" ? 'selected' : '' }}>Self-consumption</option>
                                        <option value="Off-grid" {{ $plant->system_type == "Off-grid" ? 'selected' : '' }}>Off-grid</option>
                                        <option value="Storage System" {{ $plant->system_type == "Storage System" ? 'selected' : '' }}>Storage System</option>
                                        <option value="Utility-scale All Power on Grid" {{ $plant->system_type == "Utility-scale All Power on Grid" ? 'selected' : '' }}>Utility-scale All Power on Grid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Designed Capacity<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="capacity" required placeholder="Enter Capacity" value="{{ $plant->capacity ? $plant->capacity : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Time Zone<span class="text-danger">*</span></label>
                                    <select class="form-control" name="timezone" id="system_type" required>
                                        <option value="">Select</option>
                                        <option value="Asia/Karachi" {{ $plant->timezone == "Asia/Karachi" ? 'selected' : '' }}>Asia/Karachi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" class="form-control" name="phone" placeholder="Enter Contact Number" value="{{ $plant->phone ? $plant->phone : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Benchmark Price</label>
                                    <input type="text" class="form-control" name="benchmark_price" placeholder="PKR/KWh" value="{{ $plant->benchmark_price ? $plant->benchmark_price : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company<span class="text-danger">*</span></label>
                                    <select class="form-control" name="company_id" id="system_type" required>
                                        <option value="">Select</option>
                                        @if($companies)
                                        @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ $company->id == $plant->company_id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Azimuth</label>
                                    <input type="text" class="form-control" name="azimuth" placeholder="Azimuth" value="{{ $plant->azimuth ? $plant->azimuth : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Angle</label>
                                    <input type="text" class="form-control" name="angle" placeholder="Angle" value="{{ $plant->angle ? $plant->angle : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Latitude<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="loc_lat" id="loc_lat" placeholder="Latitude" required="" value="{{ $plant->loc_lat ? $plant->loc_lat : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Longitude<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="loc_long" id="loc_long" placeholder="Longitude" required="" value="{{ $plant->loc_long ? $plant->loc_long : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="location" id="location" placeholder="Address" value="{{ $plant->location ? $plant->location : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Daily Expected Generation</label>
                                    <input type="text" class="form-control" name="expected_generation" placeholder="kwp" value="{{ $plant->expected_generation ? $plant->expected_generation : '' }}">
                                </div>
                            </div>
                            <input type="hidden" name="is_online" id="isOnline" value="{{ $plant->is_online ? $plant->is_online : '' }}">
                            <input type="hidden" name="alarmLevel" id="alarmLevel" value="{{ $plant->alarmLevel ? $plant->alarmLevel : 0 }}">
                            <input type="hidden" name="city" id="city" value="{{ $plant->city ? $plant->city : '' }}">
                            <input type="hidden" name="province" id="province" value="{{ $plant->province ? $plant->province : '' }}">
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
                        <div class="form-group mb-0">
                            <div>
                                <button type="submit" class="btn-create-vt">
                                    Update Plant
                                </button>
                                <button type="reset" class="btn-close-vt">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: 30.3753,
                lng: 69.3451
            },
            mapTypeControl: false,
            zoom: 7
        });

        var input = document.getElementById('pac-input');

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        // Specify just the place data fields that you need.
        autocomplete.setFields(
            ['place_id', 'geometry', 'name', 'formatted_address']);

        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);

        var marker = new google.maps.Marker({
            map: map
        });

        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });

        autocomplete.addListener('place_changed', function() {
            infowindow.close();

            var place = autocomplete.getPlace();

            if (!place.geometry) {
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
                map.setZoom(13)
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(7);
            }

            // Set the position of the marker using the place ID and location.
            marker.setPlace({
                placeId: place.place_id,
                location: place.geometry.location
            });

            marker.setVisible(true);
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB12hWno8_DIMqw7xCV1QeqYn6I8FiIxVw&libraries=places&callback=initMap" async defer></script>
@endsection