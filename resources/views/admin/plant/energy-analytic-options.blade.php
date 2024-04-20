@extends('layouts.admin.master')
@section('title', 'Report Center')
@section('content')
    <style>
        .card_area_reporting_vt {
            width: 100%;
            float: left;
            display: flex;
            justify-content: space-around;
        }

        .card_area_reporting_vt a {
            background-image: url('https://belx.viion.net/assets/images/0002.png');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 300px;
            float: left;
            min-width: 35%;
            height: 178px;
            text-transform: capitalize;
            font-size: 27px;
            cursor: pointer;
        }

        .card_reporting2_vt {
            background-image: url('https://belx.viion.net/assets/images/0001.png');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 300px;
            float: left;
            min-width: 35%;
            height: 178px;
            text-transform: capitalize;
            font-size: 27px;
            cursor: pointer;
        }
    </style>
    <div class="container-fluid px-xl-3">
        <section class="py-2">
            <div class="row">
                <div class="col-12">
                    <div class="report-head-vt">
                        <h4>Report</h4>
                    </div>
                </div>
            </div>
        </section>

        <br><br>
        <section class="py-2">
            <div class="row">
                <div class="col-12">
                    <div class="report-head-vt">
                        <h4>Generate</h4>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <form method="post" action="{{ url('admin/energy-analytical-report') }}" enctype="multipart/form-data">
                    @csrf
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Report File Name</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                            name="File-Name"   placeholder="Enter File Name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Select Plant</label>
                        <select class="form-control" name="plant_id" id="exampleFormControlSelect1">
                            @foreach($plants as $plant)
                            <option value="{{$plant->id}}">{{$plant->plant_name}}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Select Month</label>
                        <input type="Month" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                             name="date"  placeholder="Enter Date">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="btn_vt">
{{--                        <button type="submit" class="btn_close">Cancel</button>--}}
                        <button type="submit" class="btn_add">Export</button>
                    </div>
                </div>
            </form>

            </div> <!-- end row -->
        </section>



    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function reportingCenterData() {
            let radioButtonData = $("input[type='radio'][name='radio_value']:checked").val();
            let inputValue = document.getElementById('reference_no').value;
            if (!inputValue) {
                document.getElementById('bill-error').style.display = 'inline';
            } else {
                // $("input[type='radio'][name='rate']:checked").val();
                document.getElementById('bill-error').style.display = 'none';
                let redirectWindow = window.open('https://dbill.pitc.com.pk/fescobill/' + radioButtonData + '?refno=' + inputValue, '_blank');
                redirectWindow.location;
            }
        }
        function generateModule()
        {

            $.ajax({
                type: 'get',
                url: "{{ route('admin.plant.hybrid.report') }}",
                success: function(res) {
                    console.log('hellllllllllll');
                },
                error: function(res) {
                    console.log('Failed');
                    console.log(res);
                }
            });
        }
    </script>
@endsection
