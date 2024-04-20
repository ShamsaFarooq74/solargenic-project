@extends('layouts.admin.master')
@section('title', 'Message')
@section('content')
<style>
    .checkbox_check {
        display: flex;
        width: auto;
        justify-content: space-between;
        margin: 0 !important;
    }

    .notification_vt .hum_tum_vt .table .thead-light th {
        text-align: left !important;
    }

    .notification_vt .hum_tum_vt td {
        text-align: left !important;
    }

    .notification_vt .checkbox-primary input[type=checkbox]:checked+label::before {
        background-color: #063c6e;
        border-color: #063c6e;
    }

    .notification_vt .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #000 !important;
        background-color: #fff;
        border-color: #504E4E !important;
    }

    .notification_vt .modal-title {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        font-size: 14px;
    }
    .notification_vt .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
                        color: #000 !important;
                        background: #fff !important;
                        border-bottom: 2px solid #000!important;
                    }


    .modal-header {
        background: #323A46;
    }

    .notification_vt .checkbox label {
        cursor: pointer;
    }

    .btn_pin_vt {
        background: none !important;
        border: none !important;
        color: #063C6E !important;
    }

    .card_header_vt {
        padding: 1rem 1rem;
        background-color: #ffffff;
        box-shadow: 0 1px 4px 0 #ececf2;
        width: 100%;
        float: left;
        border-bottom: 2px solid #ececf2;
        margin: 0 !important;
        padding: 0 !important;
    }

    .message_vt {
        text-transform: capitalize;
        color: #2D2828;
        font-family: "Roboto-Bold";
        font-size: 16px;
        float: left;
        margin: 0;
        font-weight: 700;
        text-align: center;
        width: 100%;
        line-height: 50px;
    }

    .email_btn {
        background: #003366 !important;
        border: 1px solid #003366 !important;
        border-radius: 4px;
        width: auto;
        height: 37px;
        color: #ffffff;
        padding: 5px 30px;
        position: relative;
        float: right;
        margin-top: 15px;
    }
    .bootstrap-tagsinput{
        width: 100% !important;
    }
    .bootstrap-tagsinput input{
        width: 100% !important;
    }
    .i-checks {
    float: left;
    width: 100%;
    margin-top: 19px;
}
</style>
<div class="content">
    <div class="row">

        <div class="col-12 mt-2">

            @if (session('success'))
                <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('error') }}
                </div>
            @endif

        </div>

        <div class="col-12 notification_vt mt-3">
            <div class="card hum_tum_vt pla_body_padd_vt pb-2 mb-4">
                <div class="card-body mb-2">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card_header_vt">
                                <h2 class="message_vt">Message</h2>
                            </div>
                        </div>
                    </div>
                    <div class="modal-content">
                        <div class="modal-body">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item">
                                    <a href="#home-b1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                        Email
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#profile-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        SMS
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#messages-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Mobile App
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane show active" id="home-b1">
                                    <form action="{{route('admin.communication.email.store')}}" method="post">
                                        @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group"><label>Company</label>
                                            <select class="form-control select2-multiple emailMultiCompany" name="company[]" data-toggle="select2" data-select2-id="Email" multiple>
                                                <option value="all">Select all</option>
                                                @foreach ($company as $key => $com)
                                                    <option value="{{$com->id}}">{{$com->company_name}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group"><label>To</label>
                                            <select class="form-control select2-multiple emailMultiPlant" name="plant_name[]" data-toggle="select2" multiple>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="w-100">Enter Additional Email</label>
                                                <input type="text" class="form-control w-100" name="additional_email" id="additional_email" data-role="tagsinput">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group"><label>Subject <span class="text-danger">*</span></label><input type="text" placeholder="Add Subject" name="email_subject" class="form-control" required></div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3"><label>Body <span class="text-danger">*</span></label>
                                    <textarea name="email_body" id="summernote-editor" required>
                                    </textarea>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="i-checks"><label> <input type="radio" value="email_now" name="email_option_schedule" checked> <i></i> Send Now </label></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="i-checks"><label> <input type="radio" value="email_schedule" name="email_option_schedule"> <i></i> Schedule </label></div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group" id="datePickerDiv" style="display: none;">
                                                <label>Date</label>
                                                <input type="date" class="form-control" id="datepicker" width="100%" name="schedule_date" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="timePickerDiv" style="display: none;">
                                                <label>To</label>
                                                <input type="time" class="form-control" id="timepicker" width="100%" name="schedule_time" />
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="email_btn">Send Email</button>
                                    </form>
                                </div>
                                <div class="tab-pane" id="profile-b1">
                                    <form action="{{route('admin.communication.sms.store')}}" method="post">
                                        @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group"><label>Company</label>
                                            <select class="form-control select2-multiple smsMultiCompany" name="company[]" data-toggle="select2" data-select2-id="SMS" multiple>
                                                <option value="all">Select all</option>
                                                @foreach ($company as $key => $com)
                                                    <option value="{{$com->id}}">{{$com->company_name}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group"><label>To</label>
                                                <select class="form-control select2-multiple smsMultiPlant" name="plant_name[]" data-toggle="select2" multiple>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group"><label>To</label><input type="text" class="form-control w-100" name="additional_phone" id="additional_phone" data-role="tagsinput" placeholder="Add additional phone number"></div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1">Message <span class="text-danger">*</span></label>
                                                <textarea name="sms_body" class="form-control rounded-0" id="exampleFormControlTextarea1" rows="6" placeholder="Write something"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="i-checks"><label> <input type="radio" value="sms_now" name="sms_option_schedule" checked> <i></i> Send Now </label></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="i-checks"><label> <input type="radio" value="sms_schedule" name="sms_option_schedule"> <i></i> Schedule </label></div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group" id="datePickerDiv1" style="display: none;">
                                                <label>Date</label>
                                                <input type="date" class="form-control" id="datepicker1" width="100%" name="schedule_date" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="timePickerDiv1" style="display: none;">
                                                <label>To</label>
                                                <input type="time" class="form-control" id="timepicker1" width="100%" name="schedule_time"/>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="email_btn">Send SMS</button>
                                    </form>
                                </div>
                                <div class="tab-pane" id="messages-b1">
                                    <form action="{{route('admin.communication.app-notification.store')}}" method="post">
                                        @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group"><label>Company<span class="text-danger">*</span></label>
                                            <select class="form-control select2-multiple appMultiCompany" name="company[]" data-toggle="select2" data-select2-id="App" multiple required>
                                                <option value="all">Select all</option>
                                                @foreach ($company as $key => $com)
                                                    <option value="{{$com->id}}">{{$com->company_name}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group"><label>To<span class="text-danger">*</span></label>
                                                <select class="form-control select2-multiple appMultiPlatform" name="devices[]" data-toggle="select2" data-select2-id="Devices" multiple required>
                                                    <option value="all">Select all</option>
                                                    <option value="iOS">iOS</option>
                                                    <option value="android">Android</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group"><label>To</label>
                                                <select class="form-control select2-multiple appMultiPlant" name="plant_name[]" data-toggle="select2" multiple>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group"><label>Title <span class="text-danger">*</span> </label><input type="text" placeholder="Add Title" name="notification_title" class="form-control" required></div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1">Message<span class="text-danger">*</span></label>
                                                <textarea name="notification_body" class="form-control rounded-0" id="exampleFormControlTextarea1" rows="6" placeholder="Write something" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="i-checks"><label> <input type="radio" value="noti_now" name="noti_option_schedule" checked> <i></i> Send Now </label></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="i-checks"><label> <input type="radio" value="noti_schedule" name="noti_option_schedule"> <i></i> Schedule </label></div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group" id="datePickerDiv2" style="display: none;">
                                                <label>Date</label>
                                                <input type="date" class="form-control" id="datepicker2" width="100%" name="schedule_date" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="timePickerDiv2" style="display: none;">
                                                <label>To</label>
                                                <input type="time" class="form-control" id="timepicker2" width="100%" name="schedule_time" />
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="email_btn">Send Notification</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end row -->
                <!-- end row -->
            </div>
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script>

    $(document).ready(function() {

        var plants = <?php echo $plants; ?>;
        var comArr = <?php echo json_encode($companyArray); ?>;

        $(".emailMultiCompany").select2({

            placeholder: "Select Company"
        });

        $(".smsMultiCompany").select2({

            placeholder: "Select Company"
        });

        $(".appMultiCompany").select2({

            placeholder: "Select Company"
        });

        $(".emailMultiPlant").select2({

            placeholder: "Select Plant"
        });

        $(".smsMultiPlant").select2({

            placeholder: "Select Plant"
        });

        $(".appMultiPlant").select2({

            placeholder: "Select Plant"
        });

        $(".appMultiPlatform").select2({

            placeholder: "Select Platform"
        });

        $('.emailMultiCompany').on('change', function() {

            if($('.emailMultiCompany').find(':selected').val() == 'all') {

                console.log(comArr);
                $('select').select2().val(comArr).trigger('change')
            }

            $('.emailMultiPlant').empty();

            if(plants.length > 0) {

                var com_id = $('.emailMultiCompany').val();

                for(var i = 0; i < com_id.length; i++) {

                    for(var j = 0; j < plants.length; j++) {

                        if(com_id[i] == plants[j].company_id) {

                            $('.emailMultiPlant').append('<option value='+plants[j].id+'>'+plants[j].plant_name+'</option>')
                        }
                    }
                }
            }
        });

        $('.smsMultiCompany').on('change', function() {

            if($('.smsMultiCompany').find(':selected').val() == 'all') {

                console.log(comArr);
                $('select').select2().val(comArr).trigger('change')
            }

            $('.smsMultiPlant').empty();

            if(plants.length > 0) {

                var com_id = $('.smsMultiCompany').val();

                for(var i = 0; i < com_id.length; i++) {

                    for(var j = 0; j < plants.length; j++) {

                        if(com_id[i] == plants[j].company_id) {

                            $('.smsMultiPlant').append('<option value='+plants[j].id+'>'+plants[j].plant_name+'</option>')
                        }
                    }
                }
            }
        });

        $('.appMultiCompany').on('change', function() {

            if($('.appMultiCompany').find(':selected').val() == 'all') {

                console.log(comArr);
                $('select').select2().val(comArr).trigger('change')
            }

            $('.appMultiPlant').empty();

            if(plants.length > 0) {

                var com_id = $('.appMultiCompany').val();

                for(var i = 0; i < com_id.length; i++) {

                    for(var j = 0; j < plants.length; j++) {

                        if(com_id[i] == plants[j].company_id) {

                            $('.appMultiPlant').append('<option value='+plants[j].id+'>'+plants[j].plant_name+'</option>')
                        }
                    }
                }
            }
        });

        $('.appMultiPlatform').on('change', function() {

            if($('.appMultiPlatform').find(':selected').val() == 'all') {

                $('.appMultiPlatform').select2().val(['iOS', 'android']).trigger('change')
            }
        });

        $("#summernote-editor").summernote({
            height: 400,
            minHeight: null,
            maxHeight: null,
            focus: !1
        });


        $('input[type=radio][name=email_option_schedule]').change(function() {

            if (this.value == 'email_now') {

                $('#datePickerDiv').hide();
                $('#timePickerDiv').hide();
                $('#datepicker').removeAttr('required');
                $('#timepicker').removeAttr('required');
            }

            else if (this.value == 'email_schedule') {

                $('#datePickerDiv').show();
                $('#timePickerDiv').show();
                $('#datepicker').attr('required', 'required');
                $('#timepicker').attr('required', 'required');
            }
        });

        $('input[type=radio][name=sms_option_schedule]').change(function() {

            if (this.value == 'sms_now') {

                $('#datePickerDiv1').hide();
                $('#timePickerDiv1').hide();
                $('#datepicker1').removeAttr('required');
                $('#timepicker1').removeAttr('required');
            }

            else if (this.value == 'sms_schedule') {

                $('#datePickerDiv1').show();
                $('#timePickerDiv1').show();
                $('#datepicker1').attr('required', 'required');
                $('#timepicker1').attr('required', 'required');
            }
        });

        $('input[type=radio][name=noti_option_schedule]').change(function() {

            if (this.value == 'noti_now') {

                $('#datePickerDiv2').hide();
                $('#timePickerDiv2').hide();
                $('#datepicker2').removeAttr('required');
                $('#timepicker2').removeAttr('required');
            }

            else if (this.value == 'noti_schedule') {

                $('#datePickerDiv2').show();
                $('#timePickerDiv2').show();
                $('#datepicker1').attr('required', 'required');
                $('#timepicker1').attr('required', 'required');
            }
        });
    });
</script>
@endsection
