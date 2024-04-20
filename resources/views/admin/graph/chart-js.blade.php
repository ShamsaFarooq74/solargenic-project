<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="{{ asset('asset/styles.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css')}}" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>JS Bin</title>
    <script src="{{ asset('assets/js/Chart.min.js')}}"></script>
</head>
<body>
{{--Dhougnut Chart--}}
<div class="card">
    <h4 class="ml-3">Generation</h4>
    <div class="row">

        <div class="col-6">
            <canvas id="myChart"  style="width:200px; height:250px;">
            </canvas>
            <?php $data = json_decode($dataSet,true);
            $installedCapacity = $data['installed_capacity'];
            $totalPower = $data['total_power'];
            ?>
            <div style="position: absolute; white-space: nowrap; z-index: 9999999;left: 100px;bottom: 50px;font-size: 12px">
                <div>{{$installedCapacity}} kwh</div>
                (Installed capacity)
                <hr>
                <div>{{$totalPower}} kwh</div>
                (Total power)
            </div>
        </div>
        <div class="col-6">
            @for($i=0;$i<count($labels['label']);$i++)
            <span>{{$labels['label'][$i]}}</span>
            <h6>{{ $labels['values'][$i]['value'] }}</h6>
            @endfor
        </div>
    </div>
    </div>
<script>
    let plantLabels = JSON.parse(`<?php echo $plant; ?>`);
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: plantLabels.labels,
            datasets: [{
                label: '# of Plants',
                data: plantLabels.data,
                backgroundColor: [
                    'rgb(238,117,43)',
                    'rgb(74,119,23)',

                ],
                borderColor: [
                    'rgb(238,117,43)',
                    'rgb(74,119,23)',
                ],
                borderWidth: 0.5
            }]
        },
        options: {
            rotation: 1 * Math.PI,
            circumference: 1 * Math.PI,
            cutoutPercentage: 80,
            legend: {
                display: false,
            }
        }
    });
</script>

{{--Guage Chart--}}
<div class="card">
    <h4 class="ml-3">Area Chart</h4>
    <div class="row">
            <canvas id="areaChart"  style="width:200px; height:250px;">
            </canvas>
    </div>
</div>
<script>
    var ctx = document.getElementById("areaChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["24 KWH",	"50 KWH",	"30 KWH",	"40 KWH",	"90 kwh",	"30 kwh",'600 kwh','500 kwh','10 kwh','20 kwh'],
            datasets: [{
                data: [24,	50,	30,	40,	90,	30,	600,500,10, 20],
                fill: true,
                borderColor: '#CD853F',
                backgroundColor: '#CD853F',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false,
            },
            scales: {
                yAxes: [{
                    ticks: {
                        display: false,
                        gridLines: {
                            color: "rgba(0, 0, 0, 0)",
                        }
                    }
                }],
                xAxes: [{
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)",
                    }
                }],
            }
        }
    });
</script>{{--Speed0chart--}}
<div class="card">
    <h4 class="ml-3">Speed Chart</h4>
    <div class="row">
            <canvas id="oilChart" height="80">
            </canvas>
    </div>
</div>
<script>
    // var chartAbc = document.getElementById('needleChart'
    // new Chart('needleChart', {
    //     type: 'doughnut',
    //     plugins: [{
    //         afterDraw: chart => {
    //             var needleValue = chart.chart.config.data.datasets[0].needleValue;
    //             var dataTotal = chart.chart.config.data.datasets[0].data.reduce((a, b) => a + b, 0);
    //             var angle = Math.PI + (1 / dataTotal * needleValue * Math.PI);
    //             var ctx = chart.chart.ctx;
    //             var cw = chart.chart.canvas.offsetWidth;
    //             var ch = chart.chart.canvas.offsetHeight;
    //             var cx = cw / 2;
    //             var cy = ch - 6;
    //
    //             ctx.translate(cx, cy);
    //             ctx.rotate(angle);
    //             ctx.beginPath();
    //             ctx.moveTo(0, -3);
    //             ctx.lineTo(ch - 20, 0);
    //             ctx.lineTo(0, 3);
    //             ctx.fillStyle = 'rgb(0, 0, 0)';
    //             ctx.fill();
    //             ctx.rotate(-angle);
    //             ctx.translate(-cx, -cy);
    //             ctx.beginPath();
    //             ctx.arc(cx, cy, 5, 0, Math.PI * 2);
    //             ctx.fill();
    //         }
    //     }],
    //     data: {
    //         labels: [],
    //         datasets: [{
    //             data: [35, 35, 35],
    //             needleValue: 27,
    //             backgroundColor: [
    //                 'rgba(255, 99, 132, 0.2)',
    //                 'rgba(255, 206, 86, 0.2)',
    //                 'rgba(63, 191, 63, 0.2)'
    //             ]
    //         }]
    //     },
    //     options: {
    //         layout: {
    //             padding: {
    //                 bottom: 3
    //             }
    //         },
    //         rotation: -Math.PI,
    //         cutoutPercentage: 30,
    //         circumference: Math.PI,
    //         legend: {
    //             position: 'left'
    //         },
    //         animation: {
    //             animateRotate: false,
    //             animateScale: true
    //         }
    //     }
    // });
    // var oilCanvas = document.getElementById("oilChart");
    //
    // Chart.defaults.global.defaultFontFamily = "Lato";
    // Chart.defaults.global.defaultFontSize = 18;
    //
    // var oilData = {
    //     labels: [],
    //     datasets: [{
    //         data: [35, 35, 35],
    //         needleValue: 580,
    //         backgroundColor: [
    //             'rgba(255, 99, 132, 0.2)',
    //             'rgba(255, 206, 86, 0.2)',
    //             'rgba(63, 191, 63, 0.2)'
    //         ],
    //
    //         borderWidth: ""
    //     }]
    // };
    //
    // var chartOptions = {
    //     rotation: -Math.PI,
    //     cutoutPercentage: 30,
    //     circumference: Math.PI,
    //     legend: {
    //         position: 'left'
    //     },
    //     animation: {
    //         animateRotate: false,
    //         animateScale: true
    //     }
    // };
    //
    // function drawNeedle(radius, radianAngle) {
    //     console.log(canvas);
    //     var canvas = document.getElementById("oilChart");
    //
    //     var ctx = canvas.getContext('2d');
    //     var cw = canvas.offsetWidth;
    //     var ch = canvas.offsetHeight;
    //     var cx = cw / 2;
    //     var cy = ch - (ch / 4);
    //
    //     ctx.translate(cx, cy);
    //     ctx.rotate(radianAngle);
    //     ctx.beginPath();
    //     ctx.moveTo(0, -5);
    //     ctx.lineTo(radius, 0);
    //     ctx.lineTo(0, 5);
    //     ctx.fillStyle = 'rgba(0, 76, 0, 0.8)';
    //     ctx.fill();
    //     ctx.rotate(-radianAngle);
    //     ctx.translate(-cx, -cy);
    //     ctx.beginPath();
    //     ctx.arc(cx, cy, 7, 0, Math.PI * 2);
    //     ctx.fill();
    // }
    //
    // var pieChart = new Chart(oilCanvas, {
    //     type: 'doughnut',
    //     data: oilData,
    //     options: chartOptions
    // });
</script>
</body>
</html>
