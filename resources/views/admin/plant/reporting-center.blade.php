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
                        <button type="button" class="btn-report-vt" onclick="generateModule()">
                            Create Report
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="col-12">
                    <div class="card_area_reporting_vt">
                        <a href="{{route('energy.analytical.options')}}">
                            Energy Analytical Report
                        </a>
                        <div class="card_reporting2_vt">
                            <p data-toggle="modal" data-target="#reportingCenterModal">
                                My Electricity Bill
                            </p>
                        </div>
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Report File Name</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                               placeholder="Enter">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Select Plant</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Select Start Date</label>
                        <input type="Date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                               placeholder="Enter Date">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="btn_vt">
                        <button type="submit" class="btn_close">Cancel</button>
                        <button type="submit" class="btn_add">Export (esport 12 sec)</button>
                    </div>
                </div>
            </div> <!-- end row -->
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Select Plant</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Select Meter</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                               placeholder="Enter">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="btn_vt">
                        <button type="submit" class="btn_close">Cancel</button>
                        <button type="submit" class="btn_add">Export (esport 12 sec)</button>
                    </div>
                </div>
            </div> <!-- end row -->
        </section>
    </div>
    <div class="modal fade" id="reportingCenterModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Electricity Bill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body model_area_body_vt">
                    <form class="parsley-examples">
                        <div class="form-group">
                            <label class="form-control-label">Reference No<span class="text-danger">*</span></label>
                            <input type="text" id="reference_no" name="serial_no" class="form-control"
                                   placeholder="Type bill reference no." required/>
                            <span id="bill-error" class="text-danger" style="display: none">Please enter electricity bill reference no.</span><br>
                            <input type="radio" checked="checked" name="radio_value" value="industrial"
                                   class="check mt-3">
                            <span class="checkmark"></span>
                            <label class="mr-3">Industrial</label>
                            <input type="radio" checked="checked" name="radio_value" value="commercial" class="check">
                            <span class="checkmark"></span>
                            <label>Commercial</label>
                        </div>
                        <button type="button" class="btn-create-vt" onclick="reportingCenterData()">Show</button>
                    </form>
                </div>
            </div>
        </div>
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
