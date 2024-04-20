@extends('layouts.admin.master')
@section('title', 'Add Ticket')
@section('content')
<link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
<div class="container-fluid px-xl-3">
    <div class="row">
        <div class="row">
                <div id="add_ticket_card_div" class="col-lg-12 mb-3 head_tit_vt mt-3">
                @include('alert')
                <div class="form_add_ticket_vt card-box">
                    <form id="addTicketForm" method="POST" action="{{route('admin.ticket.store')}}" role="form" enctype="multipart/form-data" >
                        <div class="row">
                            @csrf
                            @if(Auth::user()->roles != 5)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Company<span class="text-danger">*&nbsp;&nbsp;<span id="company_error"></span></span></label>
                                        <select class="form-control" id="company_id" name="company_id" required="">
                                            <option value="">Select Company</option>
                                            @if($companies)
                                                @foreach($companies as $key => $company)
                                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Plant<span class="text-danger">*&nbsp;&nbsp;<span id="plant_error"></span></span></label>
                                    <select class="form-control" id="plant_id" name="plant_id" required="" data-toggle="select2">
                                        <option value="">Select Plant</option>
                                        @if(Auth::user()->roles == 5 && $plants)
                                        <optgroup>
                                            @foreach($plants as $key => $plant)
                                                <option value="{{ $plant->id }}">{{ $plant->plant_name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @if(Auth::user()->roles != 5)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Status<span class="text-danger">*&nbsp;&nbsp;<span id=""></span></span></label>
                                        <select class="form-control" id="ticket_status" name="status" required="">
                                            <option value="">Select Status</option>
                                            @if($status)
                                                @foreach($status as $key => $single_status)
                                                    <option value="{{ $single_status->id }}">{{ $single_status->status }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Priority<span class="text-danger">*&nbsp;&nbsp;<span id="priority_error"></span></span></label>
                                    <select class="form-control" id="priority" name="priority" required="">
                                        <option value="">Select Priority</option>
                                        @if($priority)
                                            @foreach($priority as $key => $source)
                                                <option value="{{ $source->id }}">{{ $source->priority }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @if(Auth::user()->roles != 5)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Source<span class="text-danger">*&nbsp;&nbsp;<span id="source_error"></span></span></label>
                                        <select class="form-control" id="source" name="source" required="">
                                            <option value="">Select Source</option>
                                            @if($sources)
                                                @foreach($sources as $key => $source)
                                                    <option value="{{ $source->id }}">{{ $source->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Category<span class="text-danger">*&nbsp;&nbsp;<span id="category_error"></span></span></label>
                                    <select class="form-control" id="category" name="category" required="">
                                        <option value="">Select Category</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Sub Category<span class="text-danger">*</span></label>
                                    <select class="form-control" id="sub_category" name="sub_category">
                                        <option value="">Select Sub Category</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 {{Auth::user()->roles != 5 ? '' : 'd-none'}}">
                                <div class="form-group">
                                    <label>Due In (Hrs)<span id="duein_error"></span></label>
                                    <select class="form-control due_in_input" id="due_in" name="due_in" placeholder="Hours">
                                        <option value="">Select Due In</option>
                                    </select>
                                </div>
                            </div>
                            @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4)
                                <div class="col-lg-4 assign_to_div">
                                    <div class="form-group">
                                        <label>Assign To<span id="assign_to_error"></span></label>
                                        <select class="form-control" id="assign_to" name="assign_to" data-toggle="select2">
                                            <option value="">Select Technician</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Alternate Email<span id="alternate_email_error"></span></label>
                                    <input type="text" placeholder="Type here" id="alternate_email" name="alternate_email" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Alternate Contact<span id="alternate_contact_error"></span></label>
                                    <input type="text" placeholder="Type here" id="alternate_contact" name="alternate_contact" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label">Title<span class="text-danger">*&nbsp;&nbsp;<span id="title_error"></span></span></label>
                                    <input type="text" placeholder="Type here" id="title" name="title" class="form-control" required="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label">Notify by<span id="notify_by_error"></span></label>
                                        <div class="checkbox checkbox-pink mb-1">
                                            <input type="checkbox" name="notify_by[]" id="hobby1" value="sms">
                                            <label for="hobby1"> SMS </label>
                                        </div>
                                        <div class="checkbox checkbox-pink mb-1">
                                            <input type="checkbox" name="notify_by[]" id="hobby2" value="app" checked>
                                            <label for="hobby2"> App Notification </label>
                                        </div>
                                        <div class="checkbox checkbox-pink">
                                            <input type="checkbox" name="notify_by[]" id="hobby3" value="email">
                                            <label for="hobby3"> Email </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="form-control-label">Description<span class="text-danger">*&nbsp;&nbsp;<span id="description_error"></span></span></label>
                                <textarea class="form-control" name="description" id="description" rows="8" placeholder="Type here" required=""></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="custom-file mb-3">
                                <label class="form-control-label">Attachment<span id="attachment_error"></span></label>
                                <div class="file-upload">
                                    <div class="file-select">
                                        <div class="file-select-button" id="fileName">Upload File</div>
                                        <div class="file-select-name" id="noFile">No file chosen...</div>
                                        <input type="file" name="attachment[]" id="chooseFile" multiple="multiple">
                                    </div>
                                    <small>Max 3 Attachment, File size should be less than 10 Mb</small>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="button" class="btn-create-vt" id="addTicketSubmitBtn">Submit</button>
                                <button type="reset" class="btn-close-vt"> Cancel </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4 mt-3 plant_details_card_ticket" style="display: none;">
                    <div class="card-stat-vt p-0  mb-3">
                        <div class="stat-area-hed-vt" style="background: none;">
                            @if(Auth::user()->roles != 5)
                            <a id="ticket_edit_plant"><button class="eidt-profil-vt" style="
                                padding-top: 5px;
                                padding-bottom: 5px;
                                position: absolute;
                                z-index: 9;
                                right: 0;
                                top: 0;
                                min-width: 80px;">Edit Plant</button></a>
                            @endif

                            <img id="company_pic_img" style="position: absolute; z-index: 5;" alt=" " width="50">

                            <img id="plant_pic_img" style="background-size: cover;
                            position: absolute;
                            z-index: 1;
                            width: 100%;
                            height: 140px;" alt=" " width="50">

                        </div>
                        <h2 id="ticket_pl_name" class="stat-head-vt"></h2>
                        <p>Plant Type<span id="ticket_pl_plant_type"></span></p>
                        <div class="spinner-border text-info plant_detail_spinner" role="status" style="display: none;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>System Type<span id="ticket_pl_system_type"></span></p>
                        <p>Capacity<span id="ticket_pl_capacity"></span></p>
                        <p>Contact<span id="ticket_pl_contact"></span></p>
                        <p>Owner<span id="ticket_pl_owner"></span></p>
                        <p>Email<span id="ticket_pl_email"></span></p>
                        <!-- <p>Owner<span>faiizii awan</span></p> -->
                    </div>
                    <div class="card-stat-vt p-0  mb-3 plant_status_card_ticket" style="display: none;">

                        <div class="head_right_vt">
                            <h2>Current Status</h2>
                        </div>

                        <div id="online_status" style="display: none;">
                            <div class="off_img_vt">
                                <img src="{{ asset('assets/images/on_off.png')}}" alt="Current" width="50">
                            </div>
                            <h2 class="stat-head-vt">Working Properly</h2>
                        </div>

                        <div id="partial_online_status" style="display: none;">
                            <div class="off_img_vt">
                                <img src="{{ asset('assets/images/on_off_orange.svg')}}" alt="Current" width="50">
                            </div>
                            <h2 class="stat-head-vt">Partial Online</h2>
                        </div>

                        <div class="spinner-border text-info plant_status_spinner" role="status" style="display: none;">
                            <span class="sr-only">Loading...</span>
                        </div>

                        <div id="offline_status" style="display: none;">
                        <div class="off_img_vt">
                            <img src="{{ asset('assets/images/on_off_blue.png')}}" alt="Current" width="50">
                        </div>
                        <h2 class="stat-head-vt">Offline</h2>
                        </div>
                    </div>

                    <div class="card-stat-vt p-0  mb-3 plant_alert_graph_ticket" style="display: none;">
                        <div class="head_right_vt">
                            <h2>Alerts</h2>
                        </div>

                        <div class="clander_left_vt">
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_day">
                                <button><i id="alertGraphPreviousDay" class="fa fa-caret-left"></i></button>
                                <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-alert mt10">
                                    <input type="text" autocomplete="off" name="alertGraphDay" id="alertGraphDay" placeholder="Select" class="c-datepicker-data-input" value="">
                                </div>
                                <button><i id="alertGraphForwardDay" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_month">
                                <button><i id="alertGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-alert mt10">
                                        <input type="text" autocomplete="off" name="alertGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="alertGraphForwardMonth" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="alert_day_month_year_vt_year">
                                <button><i id="alertGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-alert mt10">
                                        <input type="text" autocomplete="off" name="alertGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button><i id="alertGraphForwardYear" class="fa fa-caret-right"></i></button>
                            </div>

                            <div class="day_my_btn_vt" id="alert_day_my_btn_vt">
                                <button class="day_bt_vt active" id="day">day</button>
                                <button class="month_bt_vt" id="month">month</button>
                                <button class="month_bt_vt" id="year">Year</button>
                            </div>
                        </div>
                        <div class="spinner-border text-primary" id="alertSpinner" role="status" style="display: none;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="noRecord" style="display: none;">
                            NO ALERTS to SHOW
                        </span>
                        <div id="alertChartDiv">
                            <div id="alertChart" style="width: 320px; height: 201px;"></div>
                        </div>
                        <div class="online-fault-vt" id="alertChartDetailDiv">
                            <p><samp class="color03_one_vt"></samp> Fault: <span> </span></p>
                            <p><samp class="color04_one_vt"></samp> Alarm: <span> </span></p>
                            <p><samp class="color05_one_vt"></samp> RTU: <span> </span></p>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>
<!--  Alerts Center Modal-->

<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
<script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script type="text/javascript">
    var plant_axis_grid = 4;
    var id = 0;
    var alert_date;
    var alert_time;
    var auth_id = {!! Auth::user()->roles !!};
	var otherSourceID = <?php echo $otherSource; ?>;

    $(document).ready(function() {

        if(auth_id == 1 || auth_id == 2 || auth_id == 3 || auth_id == 4) {

            var plants = <?php echo $plants; ?>;
            var pl_arr = [];

            $('#company_id').on('change', function () {

                $('#plant_id').empty();
                $('#plant_id').append('<option value="">Select Plant</option>')

                if(plants.length > 0) {

                    var com_id = $('#company_id').val();

                    for(var i = 0; i < plants.length; i++) {

                        if(com_id == plants[i].company_id) {

                            $('#plant_id').append('<option value='+plants[i].id+'>'+plants[i].plant_name+'</option>')
                        }
                    }
                }
            });

        }

        var employee = <?php echo $employees; ?>;
        var em_arr = [];

        $('#plant_id').on('change', function () {

            $('#assign_to').empty();
            $('#assign_to').append('<option value="">Select Technician</option>')

            if(employee.length > 0) {

                var pla_id = $('#plant_id').val();

                for(var i = 0; i < employee.length; i++) {

                    if(pla_id == employee[i].plant_id) {

                        $('#assign_to').append('<option value='+employee[i].id+'>'+employee[i].name+'</option>')
                    }
                }
            }
        });

        var cat = <?php echo $categories; ?>;
        var cat_arr = [];
        var sub_cat = <?php echo $sub_categories; ?>;
        var sub_cat_arr = [];

        if(auth_id == 5 || auth_id == 6) {

            $('#category').empty();
            $('#category').append('<option value="">Select Category</option>');

            if(cat.length > 0) {

                var cat_name = otherSourceID;
				console.log(cat);

                for(var i = 0; i < cat.length; i++) {

                    if(cat_name == cat[i].source_id) {

                        $('#category').append('<option value='+cat[i].category_id+'>'+cat[i].category_name+'</option>')
                    }
                }
            }
        }

        $('#ticket_status').on('change', function () {

            var status_val = $('#ticket_status').val();

            if(status_val == 2 || status_val == 5) {

                $('.assign_to_div').hide();
            }

            else {

                $('.assign_to_div').show();
            }
        });

        $('#source').on('change', function () {

            $('#category').empty();
            $('#category').append('<option value="">Select Category</option>');

            if(cat.length > 0) {

                var cat_name = $('#source').val();

                for(var i = 0; i < cat.length; i++) {

                    if(cat_name == cat[i].source_id) {

                        $('#category').append('<option value='+cat[i].category_id+'>'+cat[i].category_name+'</option>')
                    }
                }
            }
        });

        $('#category').on('change', function () {

            $('#sub_category').empty();
            $('#sub_category').append('<option value="">Select Sub Category</option>');

			console.log(sub_cat);
            if(sub_cat.length > 0) {

                var cat_name = $('#category').val();
				console.log(cat_name);

                for(var i = 0; i < sub_cat.length; i++) {

                    if(cat_name == sub_cat[i].ticket_category_id) {
						console.log(sub_cat[i].ticket_category_id);
                        $('#sub_category').append('<option value='+sub_cat[i].id+'>'+sub_cat[i].sub_category_name+'</option>')
                    }
                }
            }
        });

        $('#sub_category').on('change', function () {

            $('#due_in').empty();
            var duration = 0;

            if(sub_cat.length > 0) {

                var cat_name = $('#sub_category').val();

                for(var i = 0; i < sub_cat.length; i++) {

                    if(cat_name == sub_cat[i].id) {

                        duration = sub_cat[i].duration;
                        $('#due_in').append('<option value='+(parseInt(duration) - 3)+'>'+(parseInt(duration) - 3)+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) - 2)+'>'+(parseInt(duration) - 2)+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) - 1)+'>'+(parseInt(duration) - 1)+'</option>');
                        $('#due_in').append('<option value='+sub_cat[i].duration+' selected>'+sub_cat[i].duration+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) + 1)+'>'+(parseInt(duration) + 1)+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) + 2)+'>'+(parseInt(duration) + 2)+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) + 3)+'>'+(parseInt(duration) + 3)+'</option>');
                    }
                }
            }
        });

        $('#addTicketSubmitBtn').on('click', function() {

            var files = $('input#chooseFile')[0].files;

            if($('#company_id').val() != '' && $('#plant_id').val() != '' && $('#ticket_status').val() != '' && $('#source').val() != '' && $('#priority').val() != '' && $('#category').val() != '' && $('#sub_category').val() != '' && $('#title').val() != '' && $('#description').val() != '') {

                if($('#alternate_email').val() != '') {

                    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;

                    if (testEmail.test($('#alternate_email').val())) {

                        //
                    }
                    else {

                        alert('Enter a valid email address!');
                        return false;
                    }
                }

                if(files.length == 0) {

                    $('#addTicketForm').submit();
                }

                else if(files.length > 3){
                    alert(files.length+' files selected. You can select maximum 3 files!');
                }
                else {

                    var size = true;

                    for(var i = 0; i < files.length; i++) {

                        var sizes = (files[i].size / 1048576);

                        if(sizes > 10) {
                            size = false;
                        }
                    }

                    if(size) {
                        $('#addTicketForm').submit();
                    }
                    else {
                        alert('File size should not be greater than 10MB!');
                    }
                }
            }
            else {
                alert('Please fill all mandatory fields');
            }
        });

        var currDate = getCurrentDate();

        $('input[name="alertGraphDay"]').val(currDate.todayDate);
        $('input[name="alertGraphMonth"]').val(currDate.todayMonth);
        $('input[name="alertGraphYear"]').val(currDate.todayYear);

        alert_date = $('input[name="alertGraphDay"]').val();
        alert_time = 'day';

        $('.J-yearMonthDayPicker-single-alert').datePicker({
            format: 'YYYY-MM-DD',
            language: 'en',
            hide: function(type) {
                changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'day');
            }
        });

        $('.J-yearMonthPicker-single-alert').datePicker({
            format: 'MM-YYYY',
            language: 'en',
            hide: function(type) {
                console.log(this.$input.eq(0).val());
                changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'month');
            }
        });

        $('.J-yearPicker-single-alert').datePicker({
            format: 'YYYY',
            language: 'en',
            hide: function(type) {
                changeAlertDayMonthYear(id, this.$input.eq(0).val(), 'year');
            }
        });

        $('#alertGraphPreviousDay').on('click', function() {

            show_date = $("input[name='alertGraphDay']").val();
            var datess = new Date(show_date);
            console.log(datess);
            datess.setDate(datess.getDate() - 1);
            alert_date = formatDate(datess);
            $('input[name="alertGraphDay"]').val('');
            $('input[name="alertGraphDay"]').val(alert_date);
            console.log($("input[name='alertGraphDay']").val());
            alert_time = 'day';
            alertGraphAjax(id, alert_date, alert_time);
        });

        $('#alertGraphForwardDay').on('click', function() {

            show_date = $("input[name='alertGraphDay']").val();
            var datess = new Date(show_date);
            console.log(datess);
            datess.setDate(datess.getDate() + 1);
            alert_date = formatDate(datess);
            $('input[name="alertGraphDay"]').val('');
            $('input[name="alertGraphDay"]').val(alert_date);
            console.log($("input[name='alertGraphDay']").val());
            alert_time = 'day';
            alertGraphAjax(id, alert_date, alert_time);
        });

        $('#alertGraphPreviousMonth').on('click', function() {

            show_date = $("input[name='alertGraphMonth']").val();
            alert_date = formatPreviousMonth(show_date);
            $('input[name="alertGraphMonth"]').val('');
            $('input[name="alertGraphMonth"]').val(alert_date);
            console.log($("input[name='alertGraphMonth']").val());
            alert_time = 'month';
            alertGraphAjax(id, alert_date, alert_time);
        });

        $('#alertGraphForwardMonth').on('click', function() {

            show_date = $("input[name='alertGraphMonth']").val();
            alert_date = formatForwardMonth(show_date);
            $('input[name="alertGraphMonth"]').val('');
            $('input[name="alertGraphMonth"]').val(alert_date);
            console.log($("input[name='alertGraphMonth']").val());
            alert_time = 'month';
            alertGraphAjax(id, alert_date, alert_time);
        });

        $('#alertGraphPreviousYear').on('click', function() {

            show_date = $("input[name='alertGraphYear']").val();
            alert_date = formatPreviousYear(show_date);
            $('input[name="alertGraphYear"]').val('');
            $('input[name="alertGraphYear"]').val(alert_date);
            console.log($("input[name='alertGraphYear']").val());
            alert_time = 'year';
            alertGraphAjax(id, alert_date, alert_time);
        });

        $('#alertGraphForwardYear').on('click', function() {

            show_date = $("input[name='alertGraphYear']").val();
            alert_date = formatForwardYear(show_date);
            $('input[name="alertGraphYear"]').val('');
            $('input[name="alertGraphYear"]').val(alert_date);
            console.log($("input[name='alertGraphYear']").val());
            alert_time = 'year';
            alertGraphAjax(id, alert_date, alert_time);
        });

        $("#alert_day_my_btn_vt button").click(function() {

            $('#alert_day_my_btn_vt').children().removeClass("active");
            $(this).addClass("active");

            changeAlertDayMonthYear(id, alert_date, alert_time);

        });

        $('#plant_id').on('change', function() {

            id = $('#plant_id').val();
            console.log(id);

            getPlantDetail(id);

            changeAlertDayMonthYear(id, alert_date, alert_time);
            alertGraphAjax(id, alert_date, alert_time);
        });

        function changeAlertDayMonthYear(id, date, time) {
            console.log(id);
            var d_m_y = '';

            $('#alert_day_my_btn_vt').children('button').each(function () {
                if($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#alert_day_month_year_vt_year').hide();
                $('#alert_day_month_year_vt_month').hide();
                $('#alert_day_month_year_vt_day').show();
                date = $('input[name="alertGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#alert_day_month_year_vt_year').hide();
                $('#alert_day_month_year_vt_day').hide();
                $('#alert_day_month_year_vt_month').show();
                date = $('input[name="alertGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#alert_day_month_year_vt_day').hide();
                $('#alert_day_month_year_vt_month').hide();
                $('#alert_day_month_year_vt_year').show();
                date = $('input[name="alertGraphYear"]').val();
                time = 'year';
            }

            alertGraphAjax(id, date, time);
        }

        function alertGraphAjax(id, date, time) {
            console.log(id);
            $('#alertChartDiv div').remove();
            $('#alertChartDetailDiv').empty();
            $('#alertSpinner').show();
            $('.noRecord').hide();

            $.ajax({
                url: "{{ route('admin.dashboard.alert.graph') }}",
                method: "GET",
                data: {
                    'date': date,
                    'time' : time,
                    'plant_id' : id,
                    'from_url' : 'plant'
                },

                dataType: 'json',
                success: function(data) {
                    console.log(data);

                    if(data.total_fault != 0 || data.total_alarm != 0 || data.total_rtu != 0) {
                        $('.noRecord').hide();

                        if(time == 'day') {

                            $('#alertChartDiv div').remove();
                            $('#alertChartDetailDiv').empty();

                            $('#alertChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_a_day_vt"><span>'+data.today_time.split(',')[0]+'</span></div>');
                            $('#alertChartDiv').append('<div id="alertChart" style="height: 200px; width: 100%;" fault_log="' + data.alert_fault + '" alarm_log="' + data.alert_alarm + '" rtu_log="' + data.alert_rtu + '" today_time="' + data.today_time + '"></div>');
                            $('#alertChartDetailDiv').append('<p><samp class="color03_one_vt"></samp> Fault: <span> '+data.total_fault+'</span></p><p><samp class="color04_one_vt"></samp> Alarm: <span> '+data.total_alarm+'</span></p><p><samp class="color05_one_vt"></samp> RTU: <span> '+data.total_rtu+'</span></p>');
                            $('#alertSpinner').hide();

                            plantAlertGraph(date, data.max_fault, data.max_alarm, data.max_rtu);
                        }

                        else if(time == 'month' || time == 'year') {

                            $('#alertChartDiv div').remove();
                            $('#alertChartDetailDiv').empty();

                            if(time == 'month') {
                                $('#alertChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_a_month_vt"><span>2</span></div>');
                            }
                            else if(time == 'year') {
                                $('#alertChartDiv').append('<div class="kWh_eng_vt"></div><div class="ch_a_year_vt"><span>Jan</span></div>');
                            }

                            $('#alertChartDiv').append('<div id="alertChart" style="height: 200px; width: 100%;"></div>');
                            $('#alertChartDetailDiv').append('<p><samp class="color03_one_vt"></samp> Fault: <span> '+data.total_fault+'</span></p><p><samp class="color04_one_vt"></samp> Alarm: <span> '+data.total_alarm+'</span></p><p><samp class="color05_one_vt"></samp> RTU: <span> '+data.total_rtu+'</span></p>');
                            $('#alertSpinner').hide();

                            alert_month_gen(time, date, data.fault_log_data, data.alarm_log_data, data.rtu_log_data);
                        }
                    }

                    else {

                        $('.noRecord').show();
                        $('#alertSpinner').hide();
                    }
                },
                error: function(data) {
                    console.log(data);
                    alert('Some Error Occured!');
                }
            });
        }

        function plantAlertGraph(date, max_fault, max_alarm, max_rtu) {

            var today_time = $('#alertChart').attr('today_time').split(',');
            var faults_log= $('#alertChart').attr('fault_log').split(',');
            var alarms_log= $('#alertChart').attr('alarm_log').split(',');
            var rtus_log= $('#alertChart').attr('rtu_log').split(',');

            var faultss = [];
            var alarmss = [];
            var rtuss = [];

            var fault_max = max_fault;
            var alarm_max = max_alarm;
            var rtu_max = max_rtu;

            if(fault_max >= alarm_max && fault_max >= rtu_max) {
                var max_gen = fault_max;
            }
            else if(alarm_max >= fault_max && alarm_max >= rtu_max) {
                var max_gen = alarm_max;
            }
            else {
                var max_gen = rtu_max;
            }

            max_gen = Math.ceil((max_gen / plant_axis_grid));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10,number_format)) * Math.pow(10,number_format);
            var dateArr = date.split('-');

            for (var i = 0; i < faults_log.length; i++) {
                faultss[i] = {
                    label: today_time[i],
                    y: parseFloat(faults_log[i])
                };
            }

            for (var i = 0; i < alarms_log.length; i++) {
                alarmss[i] = {
                    label: today_time[i],
                    y: parseFloat(alarms_log[i])
                };
            }

            for (var i = 0; i < rtus_log.length; i++) {
                rtuss[i] = {
                    label: today_time[i],
                    y: parseFloat(rtus_log[i])
                };
            }

            var options = {
                axisX: {
                    interval: 1,
                },
                axisY: {
                    interval: max_gen,
                    gridThickness: 0.15
                },
                data: [{
                    toolTipContent: dateArr[2]+"-"+dateArr[1]+"-"+dateArr[0]+" {label}<br/> Faults: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#0F75BC",
                    dataPoints: faultss,
                    barMaxWidth: 6
                },
                {
                    toolTipContent: dateArr[2]+"-"+dateArr[1]+"-"+dateArr[0]+" {label}<br/> Alarms: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#68AD86",
                    dataPoints: alarmss,
                    barMaxWidth: 6
                },
                {
                    toolTipContent: dateArr[2]+"-"+dateArr[1]+"-"+dateArr[0]+" {label}<br/> RTU's: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#FF9768",
                    dataPoints: rtuss,
                    barMaxWidth: 6
                }]
            };
            $("#alertChart").CanvasJSChart(options);
        }

        function alert_month_gen(time, date, faultss, alarmss, rtuss) {

            var fault_max = Math.max.apply(Math, faultss.map(function(o) { return o.y; }));
            var alarm_max = Math.max.apply(Math, alarmss.map(function(o) { return o.y; }));
            var rtu_max = Math.max.apply(Math, rtuss.map(function(o) { return o.y; }));

            if(fault_max >= alarm_max && fault_max >= rtu_max) {
                var max_gen = fault_max;
            }
            else if(alarm_max >= fault_max && alarm_max >= rtu_max) {
                var max_gen = alarm_max;
            }
            else {
                var max_gen = rtu_max;
            }

            console.log(max_gen);

            max_gen = Math.ceil((max_gen / plant_axis_grid));

            var number_format = format_output(max_gen);

            max_gen = Math.round(max_gen / Math.pow(10,number_format)) * Math.pow(10,number_format);

            if(time == 'year') {

            var options = {

                axisX: {
                    interval: 1,
                },

                axisY: {
                    interval: max_gen,
                    gridThickness: 0.15
                },

                data: [{
                    toolTipContent: "{tooltip} "+date+"<br/>Faults: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#0F75BC",
                    dataPoints: faultss,
                    barMaxWidth: 6
                    },
                    {
                    toolTipContent: "{tooltip} "+date+"<br/>Alarms: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#68AD86",
                    dataPoints: alarmss,
                    barMaxWidth: 6
                    },
                    {
                    toolTipContent: "{tooltip} "+date+"<br/>RTU's: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#FF9768",
                    dataPoints: rtuss,
                    barMaxWidth: 6
                    }
                ]
            };
            }

            else if(time == 'month') {

            var dateArr = date.split('-');

            var options = {

                axisX: {
                    interval: 2,
                },

                axisY: {
                    interval: max_gen,
                    gridThickness: 0.15
                },

                data: [{
                    toolTipContent: "{x}-"+dateArr[1]+"-"+dateArr[0]+"<br/> Faults: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#0F75BC",
                    dataPoints: faultss,
                    barMaxWidth: 6
                    },
                    {
                    toolTipContent: "{x}-"+dateArr[1]+"-"+dateArr[0]+"<br/> Alarms: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#68AD86",
                    dataPoints: alarmss,
                    barMaxWidth: 6
                    },
                    {
                    toolTipContent: "{x}-"+dateArr[1]+"-"+dateArr[0]+"<br/> RTU's: {y}",
                    markerType: "none",
                    type: "column",
                    color: "#FF9768",
                    dataPoints: rtuss,
                    barMaxWidth: 6
                    }
                ]
            };
            }

            var chart = new CanvasJS.Chart("alertChart", options);
            chart.render();
        }

        function getPlantDetail(id) {

        $('#add_ticket_card_div').removeClass('col-lg-12');
        $('#add_ticket_card_div').addClass('col-lg-8');
        $('.plant_details_card_ticket').show();
        $('.plant_status_card_ticket').show();
        $('.plant_alert_graph_ticket').show();
        $('.plant_detail_spinner').show();
        $('.plant_status_spinner').show();

        $.ajax({
            url: "{{ route('admin.ticket.plant.details') }}",
            method: "GET",
            data: {
                'plant_id': id
            },

            dataType: 'json',
            success: function(data) {
                console.log(data);
                if(data.length > 0) {

                    $('#ticket_edit_plant').removeAttr('href');
                    $('#plant_pic_img').removeAttr('src');
                    $('#company_pic_img').removeAttr('src');
                    $('#ticket_pl_name').html();
                    $('#ticket_pl_plant_type').html();
                    $('#ticket_pl_system_type').html();
                    $('#ticket_pl_capacity').html();
                    $('#ticket_pl_contact').html();
                    $('#ticket_pl_owner').html();
                    $('#ticket_pl_email').html();
                    $('#online_status').hide();
                    $('#partial_online_status').hide();
                    $('#offline_status').hide();

                    $('.plant_detail_spinner').hide();
                    $('.plant_status_spinner').hide();

                    var url = '{{ url("admin/edit-plant/:id") }}';
                    url = url.replace(':id',data[0].id);

                    $('#ticket_edit_plant').attr('href', url);
                    $('#ticket_pl_name').html(data[0].plant_name);
                    $('#ticket_pl_plant_type').html(data[0].plant_type);
                    $('#ticket_pl_system_type').html(data[0].system_type);
                    $('#ticket_pl_capacity').html(data[0].capacity+'kW');
                    $('#ticket_pl_contact').html(data[0].phone);
                    $('#ticket_pl_owner').html(data[0].company_name);
                    $('#ticket_pl_email').html(data[0].company_email);
                    if(data[0].is_online == 'Y') {
                        $('#online_status').show();
                    }
                    else if(data[0].is_online == 'P_Y') {
                        $('#partial_online_status').show();
                    }
                    else if(data[0].is_online == 'N') {
                        $('#offline_status').show();
                    }

                    if(data[0].plant_pic && data[0].plant_pic != '') {

                        var pic = "{{asset('plant_photo/:pic_name')}}";
                        pic = pic.replace(':pic_name',data[0].plant_pic);
                        $('#plant_pic_img').attr('src', pic);
                    }
                    else {
                        $('#plant_pic_img').attr('src', "{{asset('plant_photo/plant_avatar.png')}}");
                    }


                    if(data[0].logo && data[0].logo != null && data[0].logo != '') {

                        var pic = "{{asset('company_logo/:pic_name')}}";
                        pic = pic.replace(':pic_name',data[0].logo);
                        $('#company_pic_img').attr('src', pic);
                    }
                    else {
                        $('#company_pic_img').attr('src', "{{asset('company_logo/com_avatar.png')}}");
                    }
                }

                else {

                    $('#add_ticket_card_div').removeClass('col-lg-8');
                    $('#add_ticket_card_div').addClass('col-lg-12');
                    $('.plant_details_card_ticket').hide();
                    $('.plant_status_card_ticket').hide();
                    $('.plant_alert_graph_ticket').hide();
                    $('.plant_detail_spinner').hide();
                    $('.plant_status_spinner').hide();
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
        }
    });

</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script src="{{ asset('assets/js/datepicker.all.js')}}"></script>
<script src="{{ asset('assets/js/datepicker.en.js')}}"></script>
@endsection
