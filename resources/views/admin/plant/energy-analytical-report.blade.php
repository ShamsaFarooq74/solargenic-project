@extends('layouts.admin.master')
@section('title', 'Analytic Report')
@section('content')

<style>
    .energy_analytical_vt .table th {
    text-align: left !important;
    border: none;
    font-weight: bold;
    border-right: 1px solid #ccc !important;
}
.img_analytical_vt{
    width: 100%;
    float: left;
    margin: 15px 0;
    padding: 0 15px;
}
.img_analytical_vt img{
    margin-right: 50px;
    float: left;
}
.green_vt{
    font-size: 16px;
    color: green;
}
.yellow_vt{
    font-size: 16px;
    color: #C38E00;
}
.brown_vt{
    font-size: 16px;
    color: #f7b84b;
}
h4{
    font-size: 16px;
}
h3{
    font-size: 16px;
}
.h4_vt{
    background: #032262;
    padding: 0 15px;
    color: #fff;
    line-height: 40px;
    font-family: "Roboto-Regular ,sans-serif";
}
.h5_vt{
    background: #C38E00;
    padding: 0 15px;
    color: #fff;
    line-height: 40px;
    font-family: "Roboto-Regular ,sans-serif";
}
.card {
    overflow: hidden;
    box-shadow: none !important;
}
.energy_analy_report_vt .head_real_vt{
    float: left;
    width: auto;
}
.energy_analy_report_vt a{
    float: right;
    width: auto;
    color: #222;
    font-size: 14px;
}
</style>

<div class="col-md-12 mt-3 pb-3">
    <div class="card">
        <div class="card-header energy_analy_report_vt">
            <h2 class="head_real_vt">Energy Analytical Report</h2>
            <button id="generate_pdf_btn">Download PDF</button>
        </div>

        <div class="energy_analytical_vt" id="product_sheet">
            <div class="img_analytical_vt">
                <img src="http://192.168.1.250/bel-hybrid/assets/images/analytical_1.png" alt="" width="160">
                <img src="http://192.168.1.250/bel-hybrid/assets/images/bel_logo.png" alt="">
            </div>
            <table class="table">
                <tbody>
                    <tr>
                        <th scope="row">Customer Name:</th>
                        <td>Mr. Raheel Ashraf</td>
                    </tr>
                    <tr>
                        <th scope="row">Location:</th>
                        <td>{{$PlantDetail->location}}</td>
                    </tr>
                    <tr>
                        <th scope="row">System Size:</th>
                        <td>{{$PlantDetail->capacity}}</td>
                    </tr>
                    <tr>
                        <th scope="row">Report Date Timeline:</th>
                        <td>{{$ReportStartTime ." - ". $ReportEndTime}} </td>
                    </tr>
                </tbody>
            </table>
            <div class="graph_analy_vt">
                <div class="col-lg-4">
                    <div class=" card card_newvt">
                        <div class="gr_vt">
                            <div id="energy-source-graph-container"></div>
                            <div class="over_total_vt">total Energy<br>Consumption <span
                                    id="energySourcesGenerationValue"></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card graph_analytical_area_vt">
                <div class="card-header">
                    <h2 class="All-graph-heading-vt">Report Calculations</h2>
                </div>
                <div class="img_analytical_vt">
                    <img src="http://192.168.1.250/bel-hybrid/assets/images/analytical_2.png" alt="" width="50">
                </div>
                <div class="row px-3">
                    <div class="col-md-6">
                        <div class="text_analytical_area_vt">
                            <h4>Total Consumption</h4>
                            <p class="green_vt">{{$TotalProcessedData->plant_total_consumption}}</p>
                            <h4>Solar Utilized</h4>
                            <p class="yellow_vt">625 Units Utilized Savings earned through solar energy consumed by your local load in premises </p>
                            <h4>Grid Import</h4>
                            <p class="brown_vt">1100 Units Imported </p>
                            <h4>Battery Discharging</h4>
                            <p class="green_vt">Solar Units Consumed x 16.33 + GST + NJ Surcharge + FC Surcharge </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Solar Consumption Savings:</h4>
                        <p class="green_vt">Savings earned through solar energy </p>
                        <p class="brown_vt">is calculated as following: </p>
                    </div>
                </div>
            </div>
            <div class="graph_analy_vt">
                <div class="col-lg-4">
                    <div class=" card card_newvt">
                        <div class="gr_vt">
                            <div id="energy-source-graph-container1"></div>
                            <div class="over_total_vt">total Energy<br>Consumption <span
                                    id="energySourcesGenerationValue1"></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card graph_analytical_area_vt">
                <div class="card-header">
                    <h2 class="All-graph-heading-vt">Peak Hours Consumption</h2>
                </div>
                <div class="img_analytical_vt">
                    <img src="http://192.168.1.250/bel-hybrid/assets/images/analytical_3.png" alt="" width="50">
                </div>
                <div class="row px-3">
                    <div class="col-md-6">
                        <div class="text_analytical_area_vt">
                            <h3>(625 x 16.33) + 1849 + 67 + 286 = Rs. 12,000</h3>
                            <h4> Solar Generation</h4>
                            <p class="green_vt">1525 Units Generated</p>
                            <h4>Solar Consumed</h4>
                            <p class="brown_vt">625 Units Consumed </p>
                            <h4>Solar Export to Grid</h4>
                            <p class="green_vt">700 Units Exported </p>
                            <h4>Battery Charging</h4>
                            <p class="brown_vt">200 Units Charged batteries</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Solar Export Savings:</h4>
                        <p class="green_vt">Savings earned through exporting solar units </p>
                        <h4>is calculated as following: </h4>
                        <p class="brown_vt">Solar Units Exported x 16.33 700 x 16.33 = Rs. 2,000</p>
                    </div>
                </div>
            </div>
            <div class="graph_analy_vt">
                <div class="col-lg-4">
                    <div class=" card card_newvt">
                        <div class="gr_vt">
                            <div id="energy-source-graph-container2"></div>
                            <div class="over_total_vt">total Energy<br>Consumption <span
                                    id="energySourcesGenerationValue2"></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card graph_analytical_area_vt">
                <div class="card-header">
                    <h2 class="All-graph-heading-vt">Total Cost Saving</h2>
                </div>
                <div class="img_analytical_vt">
                    <img src="http://192.168.1.250/bel-hybrid/assets/images/analytical_4.png" alt="" width="50">
                </div>
                <div class="row px-3">
                    <div class="col-md-6">
                        <div class="text_analytical_area_vt">
                            <h4> 630 Units Consumed</h4>
                            <h4>Grid Import</h4>
                            <p class="green_vt">battery in Peak Hours is calculated as </p>
                            <p class="brown_vt">400 Units Consumed</p>
                            <h4>Battery Discharging</h4>
                            <p class="green_vt">230 Units Discharged </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Peak Hours Savings:</h4>
                        <p class="green_vt">Savings earned through discharging </p>
                        <h4> Battery Discharging </h4>
                        <p class="brown_vt"> Units in Peak Hours x 22.65</p>
                    </div>
                </div>
            </div>
            <div class="graph_analy_vt">
                <div class="col-lg-4">
                    <div class=" card card_newvt">
                        <div class="gr_vt">
                            <div id="energy-source-graph-container3"></div>
                            <div class="over_total_vt">total Energy<br>Consumption <span
                                    id="energySourcesGenerationValue3"></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card graph_analytical_area_vt">
                <div class="card-header">
                    <h2 class="All-graph-heading-vt">REPORT CALCULATIONS</h2>
                </div>
                <div class="img_analytical_vt">
                    <img src="http://192.168.1.250/bel-hybrid/assets/images/analytical_5.png" alt="" width="50">
                </div>
                <table class="table">
                    <div class="h4_vt">IESCO Tariff Rates</div>
                    <tbody>
                        <tr>
                            <th scope="row">High Tariff Rate per Kwh/Unit:</th>
                            <td>22.65 per Kwh/unit</td>
                        </tr>
                        <tr>
                            <th scope="row">Low Tariff Rate per Kwh/Unit:</th>
                            <td>16.33 peer Kwh/Unit</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row px-3">
                    <div class="col-md-12">
                        <div class="text_analytical_area_vt">
                            <h3>18,000 Rupees </h3>
                            <h4> Solar Consumption Savings</h4>
                            <p class="green_vt">13,000 Rupees Saved</p>
                            <h4>Solar Export Savings</h4>
                            <p class="brown_vt">2,000 Rupees Saved </p>
                        </div>
                    </div>
                </div>
                <table class="table">
                    <div class="h5_vt">Government Taxes / Surcharges Rate</div>
                    <tbody>
                    <tr>
                            <th scope="row">General Sales Tax:</th>
                            <td>17%</td>
                        </tr>
                        <tr>
                            <th scope="row">Nj Surchange:</th>
                            <td>0.10 Rs. per Kwh/Unit</td>
                        </tr>
                        <tr>
                            <th scope="row">FC Surchange:</th>
                            <td>0.10 Rs. per Kwh/Unit</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="graph_analy_vt">
                <div class="col-lg-12">
                    <div class="card card_newvt">
                        <div class="card-header">
                            <h2 class="All-graph-heading-vt">Specific Yield Kwh/Kwp</h2>
                        </div>
                        <div class="card-box_vt" dir="ltr">
                            <div class="kwh_khp_vt">Kwh/Kwp</div>
                            <br>
                            <div class="spinner-border text-success specificYieldGraphSpinner plantGraphSpinner"
                                 role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div id="specific-yield-bar-graph"></div>
                            <br>
                        </div>
                        <h5 class="one_specific_vt"><samp class="one-import-color"></samp> <span
                                id="specific-yield-value">Specific Yield: 0 Kwh/Kwp </span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-gl/dist/echarts-gl.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-stat/dist/ecStat.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/extension/dataTool.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/china.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/world.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/extension/bmap.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var filterss_arr = {};
        var plant_name = {};
        solarEnergyGraphAjaxData('2021-11-11', 'day', filterss_arr, plant_name);
        solarEnergy2GraphAjaxData('2021-11-11', 'day', filterss_arr, plant_name);
        solarEnergy3GraphAjaxData('2021-11-11', 'day', filterss_arr, plant_name);
        solarEnergy1GraphAjaxData('2021-11-11', 'day', filterss_arr, plant_name);
        specificYieldGraphAjax('2021-11-11', 'month', filterss_arr, plant_name);
    });

    $ = jQuery;

    $( "#generate_pdf_btn" ).click(function() {
        make_product_sheet();
    });


    function make_product_sheet() {

        console.log("#generate_pdf_btn clicked");
        var pdf = new jsPDF('p', 'pt', 'a4');
        pdf.addHTML(document.getElementById("product_sheet"), function() {
console.log("okkkyyy")
            ps_filename = "generated-product-sheet";
            pdf.save(ps_filename+'.pdf');
        });
    }

    function solarEnergyGraphAjaxData(date, time, filter, plant_name) {
        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);
        $('.energySourcesGraphSpinner').show();
        $('#energy-source-graph-container').html('');


        var plantID = $('#plantID').val();
        var plantMeterType = $('#plantMeterType').val();
        $.ajax({
            url: "{{ route('admin.dashboard.solar.energy.sources') }}",
            method: "GET",
            data: {
                'filter': filters,
                'plant_name': plantName,
                'date': date,
                'time': time,
            },
            dataType: 'json',
            success: function (data) {
                $('.energySourcesGraphSpinner').hide();
                $('#energy-source-graph-container').append('<div id="plantsEnergySourcesChart" style="height: 320px; width: 100%;"></div>');
                $('#energySourcesGenerationValue').html(data.generation + ' ' + 'kWh');

                energySourcesGraph(data);
            },
            error: function (data) {
            }
        });
    }
    function energySourcesGraph(data) {
        // console.log(data);
        let dataArray = data.logData;
        var dom = document.getElementById("plantsEnergySourcesChart");
        var myChart = echarts.init(dom);
        var app = {};
        option = {
            tooltip: {
                trigger: 'item',
                formatter: '{b}'
            },
            legend: {
                textStyle: {
                    fontSize: '11',
                },
                bottom: '1px'
            },
            series: [
                {
                    type: 'pie',
                    radius: ['43%', '53%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    labelLine: {
                        show: false
                    },
                    data: dataArray
                }
            ]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }
    }
    function solarEnergy1GraphAjaxData(date, time, filter, plant_name) {
        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);
        $('.energySourcesGraphSpinner1').show();
        $('#energy-source-graph-container1').html('');
        var plantID = $('#plantID').val();
        var plantMeterType = $('#plantMeterType').val();
        $.ajax({
            url: "{{ route('admin.dashboard.solar.energy.sources') }}",
            method: "GET",
            data: {
                'filter': filters,
                'plant_name': plantName,
                'date': date,
                'time': time,
            },
            dataType: 'json',
            success: function (data) {
                $('.energySourcesGraphSpinner1').hide();
                $('#energy-source-graph-container1').append('<div id="plantsEnergySourcesChart1" style="height: 320px; width: 100%;"></div>');
                $('#energySourcesGenerationValue1').html(data.generation + ' ' + 'kWh');

                energySourcesGraph1(data);
            },
            error: function (data) {
            }
        });
    }
    function energySourcesGraph1(data) {
        // console.log(data);
        let dataArray = data.logData;
        var dom = document.getElementById("plantsEnergySourcesChart1");
        var myChart = echarts.init(dom);
        var app = {};
        option = {
            tooltip: {
                trigger: 'item',
                formatter: '{b}'
            },
            legend: {
                textStyle: {
                    fontSize: '11',
                },
                bottom: '1px'
            },
            series: [
                {
                    type: 'pie',
                    radius: ['43%', '53%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    labelLine: {
                        show: false
                    },
                    data: dataArray
                }
            ]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }
    }

    function solarEnergy2GraphAjaxData(date, time, filter, plant_name) {
        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);
        $('.energySourcesGraphSpinner').show();
        $('#energy-source-graph-container2').html('');
        var plantID = $('#plantID').val();
        var plantMeterType = $('#plantMeterType').val();
        $.ajax({
            url: "{{ route('admin.dashboard.solar.energy.sources') }}",
            method: "GET",
            data: {
                'filter': filters,
                'plant_name': plantName,
                'date': date,
                'time': time,
            },
            dataType: 'json',
            success: function (data) {
                $('.energySourcesGraphSpinner').hide();
                $('#energy-source-graph-container2').append('<div id="plantsEnergySourcesChart2" style="height: 320px; width: 100%;"></div>');
                $('#energySourcesGenerationValue2').html(data.generation + ' ' + 'kWh');

                energySourcesGraph2(data);
            },
            error: function (data) {
            }
        });
    }
    function energySourcesGraph2(data) {
        // console.log(data);
        let dataArray = data.logData;
        var dom = document.getElementById("plantsEnergySourcesChart2");
        var myChart = echarts.init(dom);
        var app = {};
        option = {
            tooltip: {
                trigger: 'item',
                formatter: '{b}'
            },
            legend: {
                textStyle: {
                    fontSize: '11',
                },
                bottom: '1px'
            },
            series: [
                {
                    type: 'pie',
                    radius: ['43%', '53%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    labelLine: {
                        show: false
                    },
                    data: dataArray
                }
            ]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }
    }

    function solarEnergy3GraphAjaxData(date, time, filter, plant_name) {
        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);
        $('.energySourcesGraphSpinner').show();
        $('#energy-source-graph-container3').html('');
        var plantID = $('#plantID').val();
        var plantMeterType = $('#plantMeterType').val();
        $.ajax({
            url: "{{ route('admin.dashboard.solar.energy.sources') }}",
            method: "GET",
            data: {
                'filter': filters,
                'plant_name': plantName,
                'date': date,
                'time': time,
            },
            dataType: 'json',
            success: function (data) {
                $('.energySourcesGraphSpinner').hide();
                $('#energy-source-graph-container3').append('<div id="plantsEnergySourcesChart3" style="height: 320px; width: 100%;"></div>');
                $('#energySourcesGenerationValue3').html(data.generation + ' ' + 'kWh');

                energySourcesGraph3(data);
            },
            error: function (data) {
            }
        });
    }
    function energySourcesGraph3(data) {
        // console.log(data);
        let dataArray = data.logData;
        var dom = document.getElementById("plantsEnergySourcesChart3");
        var myChart = echarts.init(dom);
        var app = {};
        option = {
            tooltip: {
                trigger: 'item',
                formatter: '{b}'
            },
            legend: {
                textStyle: {
                    fontSize: '11',
                },
                bottom: '1px'
            },
            series: [
                {
                    type: 'pie',
                    radius: ['43%', '53%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    labelLine: {
                        show: false
                    },
                    data: dataArray
                }
            ]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }
    }




    function specificYieldGraphAjax(date, time, filter, plant_name) {
        filters = JSON.stringify(filter);
        plantName = JSON.stringify(plant_name);
        $('#specific-yield-bar-graph').html('');
        $('.specificYieldGraphSpinner').show();

        var plantID = $('#plantID').val();
        var plantMeterType = $('#plantMeterType').val();
        $.ajax({
            url: "{{ route('admin.dashboard.specific.yield.graph') }}",
            method: "GET",
            data: {
                'date': date,
                'time': time,
                'filter': filters,
                'plant_name': plantName
            },
            dataType: 'json',
            success: function (data) {
                $('.specificYieldGraphSpinner').hide();
                $('#specific-yield-bar-graph').html('');
                $('#specific-yield-bar-graph').append('<div id="plants-specific-yield-graph" style="height: 320px; width: 100%;"></div>');
                $('#specific-yield-value').html('Specific Yield: ' + data.specific_yield_data + '  ' + 'Kwh/Kwp')

                specificYieldGraph(data);
            },
            error: function (data) {

                // $('.historyGraphSpinner').hide();
                // $('.historyGraphError').show();
            }
        });
    }
    function specificYieldGraph(plantsHistoryGraphData) {
        // console.log(data);
        // let dataArray = data.logData;
        // let totalValue = data.total_value;
        var data = plantsHistoryGraphData.specific_history_graph;
        var axisData = plantsHistoryGraphData.y_axis_array;
        var time = plantsHistoryGraphData.time_array;
        var timeType = plantsHistoryGraphData.time_type;
        var legendArray = plantsHistoryGraphData.legend_array;
        var tooltipDate = plantsHistoryGraphData.tooltip_date;
        let dom = document.getElementById("plants-specific-yield-graph");
        var myChart = echarts.init(dom);
        var app = {};

        var option;

        option = {

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
                        output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#f2b610;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value}  kWh/kWp</span>`;
                        if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                            output += '<br/>'
                        }

                    }

                    return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + '-' + tooltipDate + '</span><br/><br/>' + output;
                }
            },
            // legend: {
            //         data: legendArray,
            //         /*selected: {
            //     'Cost Saving': false,
            // },*/
            //         //bottom: '-15px',
            //     },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '17%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: true,
                data: time,
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
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }
    }
</script>
@endsection
