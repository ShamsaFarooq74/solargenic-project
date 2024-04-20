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
<div class="card">
    <h4 class="ml-3">Area Chart</h4>
    <div class="row">
            <canvas id="areaChart"  style="width:200px; height:250px;"></canvas>
    </div>
</div>
<script>
    let plantLabels = JSON.parse(`<?php echo $areaGraphString; ?>`);
    let xAxisData = plantLabels.xAxis;
    let yAxisData = plantLabels.yAxis;
    console.log(xAxisData);
    console.log(yAxisData);
    console.log(plantLabels);
    var ctx = document.getElementById("areaChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: xAxisData,
            datasets: [{
                data: yAxisData,
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
</script>
</body>
</html>
