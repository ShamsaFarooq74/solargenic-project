@extends('layouts.admin.master')
@section('title', 'All Inverters')
@section('content')
<div class="container-fluid px-xl-5 ">

    <!-- inverter page start -->
    <section class="py-3">
        <div class="row">
            <div class="col-xl-12">
                <div class="home-companies-area-vt mb-3">
                    <form class="home-companise-vt" action="">
                        <div class="form-group">
                            <div class="drop-search-mt">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        All
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="check-area-mt">
                                            <label class="check mr-3">Region
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Plant Type
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Capacity
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">City
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Province
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="drop-search-mt">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Region
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="check-area-mt">
                                            <label class="check mr-3">Region 1
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Region 2
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Region 3
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="drop-search-mt">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Plant Type
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="check-area-mt">
                                            <label class="check mr-3">Plant Type 1
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Plant Type 2
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Plant Type 3
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="drop-search-mt">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Capacity
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="check-area-mt">
                                            <label class="check mr-3">Capacity 1
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Capacity 2
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Capacity 3
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="drop-search-mt">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        City
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="check-area-mt">
                                            <label class="check mr-3">City 1
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">City 2
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">City 3
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="drop-search-mt">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Province
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="check-area-mt">
                                            <label class="check mr-3">Province 1
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Province 2
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="check">Province 3
                                                <input type="radio" checked="checked" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="btn-companies-vt">
                        <button class="btn-clear-vt" type="button">Clear</button>
                        <a href="{{ url('admin/build-plant')}}">
                            <button type="button" class="btn-add-vt">
                                Build Plant
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="card p-0">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Operating Status</h2>
                        <h6 class="updated-head-vt">Updated: 2020-03-26 11:58 <i class="fas fa-spinner fa-spin"></i></h6>
                    </div>
                    <div class="card-box">
                        <div id="donut-chart" style="height: 210px;" data-colors="#2996CE,#FF9768,#FF6A6A" dir="ltr" data-online="6" data-offline="3" data-fault="2"></div>
                    </div>
                    <div class="online-fault-vt">
                        <p>Offline: <span> 6</span></p>
                        <p>Offline: <span> 3</span></p>
                        <p>Fault: <span> 2</span></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Generation Overview</h2>
                        <h6 class="updated-head-vt">Updated: 2020-03-26 11:58 <i class="fas fa-spinner fa-spin"></i></h6>
                    </div>
                    <div class="card-box">
                        <div class="text-center" dir="ltr">
                            <!-- <input data-plugin="knob" data-width="194" data-height="194" data-bgColor="#D23CF6" data-fgColor="#FFC054" data-displayInput=false value="55" /> -->
                            <div id="donut-chart2" style="height: 186px;" data-colors="#D23CF6,#FFC054" dir="ltr" data-capacity="160" data-generation="200"></div>
                        </div>
                    </div>
                    <div class="generation-overview-vt">
                        <p>Designed Capacity: <span> 200kWh</span></p>
                        <p>Current Generation: <span class="one"> 160kWh</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="All-graph-heading-vt">Plants List</h3>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-striped" id="products-datatable">
                                <thead>
                                    <tr>
                                        <th>Device Status</th>
                                        <th>Serial Number</th>
                                        <th>Current Generation</th>
                                        <th>Daily Generation</th>
                                        <th>Inverter Status</th>
                                        <th>Communication Mode</th>
                                        <th>Last Alerts</th>
                                        <th>Last Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            113452244
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            Wifi
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                    </tr>
                                    <tr class="text-center">
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            113452244
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            Wifi
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                    </tr> 
                                    <tr class="text-center">
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            113452244
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            Wifi
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                    </tr>
                                    <tr class="text-center">
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            113452244
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            1.1kWh
                                        </td>
                                        <td>
                                            <i class="fas fa-check-circle"></i>
                                        </td>
                                        <td>
                                            Wifi
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                        <td>
                                            12:15 2020-03-26
                                        </td>
                                    </tr>                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- inverter page end -->
</div>
@endsection