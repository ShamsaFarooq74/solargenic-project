@extends('layouts.admin.master')
@section('title', 'Dashboard')
@section('content')

<div class="content">
    <row>
        <div class="col-lg-12 pt-3">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 right-img-wrapper">
                    <div class="soloar-img-holder">
                        <img class="soloar-img" src="{{ asset('assets/images/solar-img.png')}}" alt="solar">
                        <div class="solar-btn-vt">
                            <div class="solar-btn_vt">
                                <img src="{{ asset('assets/images/on-off.svg')}}" alt="on/off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 widgits-wrapper_vt">
                            <div class="widgit-holder_vt">
                                <img src="{{ asset('assets/images/control1.png')}}" alt="on/off">
                                <h1 class="dashboard-heading-title">Control Mode</h1>
                                <p class="dashboard-para-title">Enabled</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 widgits-wrapper_vt">
                            <div class="widgit-holder_vt ">
                                <img src="{{ asset('assets/images/site-status.png')}}" alt="on/off">
                                <h1 class="dashboard-heading-title">Site Status</h1>
                                <p class="dashboard-para-title">Enabled</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 widgits-wrapper_vt">
                            <div class="widgit-holder_vt">
                                <img src="{{ asset('assets/images/fault-code.png')}}" alt="on/off">
                                <h1 class="dashboard-heading-title">Fault Code</h1>
                                <p class="dashboard-para-title">Standby</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 widgits-wrapper_vt">
                            <div class="widgit-holder_vt">
                                <img src="{{ asset('assets/images/meter-type.png')}}" alt="on/off">
                                <h1 class="dashboard-heading-title">Meter Type</h1>
                                <p class="dashboard-para-title">Klemson</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 pl-0 padding-vt">
                    <div class="weather-details d-card_vt">
                        <div class="degree-content_vt">
                            <h1>19°</h1>
                            <img src="{{ asset('assets/images/Moon-cloud.png')}}" alt="on/off">
                        </div>
                        <div class="temprature-widgit">
                            <span>H:24°</span>
                            <span>L:18°</span>
                        </div>
                        <div class="weather-area">
                            <h2>Modal Town, Lahore</h2>
                            <p>Mid Rain</p>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="weather-widgit-holder_vt">
                                <div class="weather-widgit_vt">
                                    <div class="d-flex">
                                        <div class="days-holder_vt padding-vt">
                                            <div class="days-widgit_vt d-card_vt">
                                                <h1>Sunday</h1>
                                                <p>12/29</p>
                                                <img src="{{ asset('assets/images/Moon-wind1.png')}}" alt="Moon">
                                            </div>
                                        </div>
                                        <div class="days-holder_vt padding-vt">
                                            <div class="days-widgit_vt d-card_vt">
                                                <h1>Monday</h1>
                                                <p>12/29</p>
                                                <img src="{{ asset('assets/images/Moon-cloud1.png')}}" alt="Moon">
                                            </div>
                                        </div>
                                        <div class="days-holder_vt padding-vt">
                                            <div class="days-widgit_vt d-card_vt">
                                                <h1>Tuesday</h1>
                                                <p>12/29</p>
                                                <img src="{{ asset('assets/images/sunny-W.png')}}" alt="Moon">
                                            </div>
                                        </div>
                                        <div class="days-holder_vt padding-vt">
                                            <div class="days-widgit_vt d-card_vt">
                                                <h1>Wednesday</h1>
                                                <p>12/29</p>
                                                <img src="{{ asset('assets/images/Moon-wind1.png')}}" alt="Moon">
                                            </div>
                                        </div>
                                        <div class="days-holder_vt padding-vt">
                                            <div class="days-widgit_vt d-card_vt">
                                                <h1>Sunday</h1>
                                                <p>12/29</p>
                                                <img src="{{ asset('assets/images/Sun-cloud1.png')}}" alt="Moon">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 right-card-p mb-1">
                    <div class="d-card_vt p-3 xl-lg-screen">
                        <div class="row pb-3">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="device-status">
                                    <div class="device-status-content pt-1">
                                        <div>
                                            <img src="{{ asset('assets/images/device-status.png')}}" alt="status">
                                        </div>
                                        <div class="status-working">
                                            <p class="dashboard-para-title">Current Status</p>
                                            <h1 class="dashboard-heading-title">Working Properly</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="plant-details">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1 class="dashboard-heading-title">Plant Name</h1>
                                        <p class="dashboard-para-title">BH Liberty</p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <h1 class="dashboard-heading-title">Plant Type</h1>
                                        <p class="dashboard-para-title">Commercial Rooftop</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-3">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="device-status">
                                    <div class="device-status-content pt-1">
                                        <div>
                                            <img src="{{ asset('assets/images/capacity.png')}}" alt="status">
                                        </div>
                                        <div class="status-working">
                                            <p class="dashboard-para-title">0.17 (tons)</p>
                                            <h1 class="dashboard-heading-title">Accumulative CO2</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="plant-details">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1 class="dashboard-heading-title">Designed Capacity</h1>
                                        <p class="dashboard-para-title">114.6 kW</p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <h1 class="dashboard-heading-title">Contact</h1>
                                        <p class="dashboard-para-title">03212433900</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-3">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="device-status">
                                    <div class="device-status-content pt-1">
                                        <div>
                                            <img src="{{ asset('assets/images/Benchmark.png')}}" alt="status">
                                        </div>
                                        <div class="status-working">
                                            <p class="dashboard-para-title">0.55 tree(s)</p>
                                            <h1 class="dashboard-heading-title">Working Properly</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="plant-details">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1 class="dashboard-heading-title">Benchmark Price</h1>
                                        <p class="dashboard-para-title">PKR 28.9/unit</p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <h1 class="dashboard-heading-title">Company</h1>
                                        <p class="dashboard-para-title">Beaconhouse School System</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-lg-12 pl-0 padding-vt">
                    <div class="plant-table-data">
                        <div class="row">
                            <div class="col-lg-4 col-md-4">
                                <div class="plant-widgit">
                                    <h1 class="plant-widgit-title">Grid</h1>
                                    <div class="plant-status-data">
                                        <p>Total Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>L1 Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>L2 Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>L3 Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                </div>
                                <div class="plant-widgit pt-3">
                                    <h1 class="plant-widgit-title">Load Power</h1>
                                    <div class="plant-status-data">
                                        <p>Total Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>L1 Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>L2 Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>L3 Grid Power</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="new-single-dashboard-vt">
                                    <div class="new-single-dashb_vt">
                                        <div class="new-single-dashboard-row-vt">
                                            <img src="https://pushapp.bel-energise.com/assets/images/tower.png" alt="tower" width="45">
                                            <div class="new-single-area-vt">
                                                <h4>Grid</h4>
                                                <span style="position:absolute; bottom:-113px;">1.8 kW</span>
                                                <div class="new-size_power active-animatioon"></div>
                                            </div>
                                        </div>
                                    <div class="new-single-dashboard-row-vt">
                                        <img src="https://pushapp.bel-energise.com/assets/images/power.png" alt="tower" width="45">
                                        <div class="new-single-area-vt">
                                            <h4>Generation</h4>
                                            <span style="position:relative; bottom:-78px; left: 57px;">0 kW</span>
                                            <div class="new-size_generation_off active-animatioon"></div>
                                        </div>
                                    </div>
                                    <div class="new-single-dashboard-tow-vt" style="width: auto;text-align: center;float: none;">
                                        <img src="https://pushapp.bel-energise.com/assets/images/sensor.png" alt="sensor" class="img" width="45"                           style="float: none;">
                                    </div>
                                    <div class="new-single-dashboard-tow-vt" style="float: none; clear:both; margin: 0 auto;">
                                        <div class="new-single-area-tow-vt">
                                            <h4>Consumption</h4>
                                            <span>1.8 kW</span>
                                        <div class="new-size_consumption active-animatioon"></div>
                                    </div>
                                        <img src="https://pushapp.bel-energise.com/assets/images/home.png" alt="home"                       width="45">
                                    </div>
                                    <div class="new-single-dashboard-tow-vt">
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="plant-widgit">
                                    <h1 class="plant-widgit-title">Inverter Power</h1>
                                    <div class="plant-status-data">
                                        <p>Daily Inverter Energy</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>Total Inverter Energy</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>Grid Frequency</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                </div>
                                <div class="plant-widgit pt-3">
                                    <h1 class="plant-widgit-title">Power Factor</h1>
                                    <div class="plant-status-data">
                                        <p>Power Factor L1</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>Power Factor L2</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                    <div class="plant-status-data">
                                        <p>Power Factor L3</p>
                                        <h1>105 kWh</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-lg-12 pl-0 padding-vt">
                    <div class="graph-content_vt">
                        <div class="day_my_btn_vt chart-btn_vt" id="alert_day_my_btn_vt">
                            <button class="day_bt_vt new-btn active" id="day">day</button>
                            <button class="month_bt_vt new-month_vt" id="month">month</button>
                            <button class="month_bt_vt new-month_vt" id="year">Year</button>
                        </div>
                        <div id="graph_vt_chart"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 px-0">
                    <div class="table-data-content">
                        <div class="card card_body_padding_vt dash-table_vt" style="min-height:350px;">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-8">
                                    <div class="p-2">
                                        <h2 class="All-graph-heading-vt" style="padding-top:10px;">Alerts</h2>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="search-btn-div p-2">
                                        <input class="form-control" type="text" placeholder="Search Alert">
                                        <button>
                                            <img src="{{ asset('assets/images/alert-btn.png')}}" alt="alert">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="datatable_7"
                                    class="display table table-borderless table-centered table-nowrap"
                                    style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>ALERT DETAILS</th>
                                        <th>DESCRIPTION</th>
                                        <th>STATUS</th>
                                        <th>EVENT TIME</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>System Fault</td>
                                            <td>A system fault is an error or malfunction that occurs.</td>
                                            <td class="red">Active</td>
                                            <td>07-03-2023, 03:45 PM</td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>System Fault</td>
                                            <td>A system fault is an error or malfunction that occurs.</td>
                                            <td class="red">Active</td>
                                            <td>07-03-2023, 03:45 PM</td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>System Fault</td>
                                            <td>A system fault is an error or malfunction that occurs.</td>
                                            <td class="green">Closed</td>
                                            <td>07-03-2023, 03:45 PM</td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>System Fault</td>
                                            <td>A system fault is an error or malfunction that occurs.</td>
                                            <td class="green">Closed</td>
                                            <td>07-03-2023, 03:45 PM</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </row>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.0.0/dist/echarts.min.js"></script>

<script>
        // based ready dom, initialize echarts instance 
		var myChart = echarts.init(document.getElementById('graph_vt_chart'), 'dark-fresh-cut');
		// Themes: azul, bee-inspired, blue, caravan, carp, cool, dark, dark-blue, dark-bold, dark-digerati, dark-fresh-cut, dark-mushroom, default, eduardo, forest, fresh-cut, fruit, gray, green, helianthus, infographic, inspired, jazz, london, macarons, macarons2, mint, red, red-velvet, roma, royal, sakura, shine, tech-blue, vintage 

        // Specify configurations and data graphs 
        var option = {
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data:['Kale','Lettuce','Celery','Cucumber','Parsley']
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
        data: ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name:'Kale',
            type:'line',
            stack: 'Total Amount',
            data:[120, 132, 101, 134, 90, 230, 210]
        },
        {
            name:'Lettuce',
            type:'line',
            stack: 'Total Amount',
            data:[220, 182, 191, 234, 290, 330, 310]
        },
        {
            name:'Celery',
            type:'line',
            stack: 'Total Amount',
            data:[150, 232, 201, 154, 190, 330, 410]
        },
        {
            name:'Cucumber',
            type:'line',
            stack: 'Total Amount',
            data:[320, 332, 301, 334, 390, 330, 320]
        },
        {
            name:'Parsley',
            type:'line',
            stack: 'Total Amount',
            data:[820, 932, 901, 934, 1290, 1330, 1320]
        }
    ]
};

		// Use just the specified configurations and data charts. 
		myChart.setOption(option);
</script>

@endsection