@extends('layouts.admin.master')
@section('title', 'Update Ticket')
@section('content')

<style>
    .closeTicket {
        text-align: center;
        vertical-align: bottom;
        line-height: 35px;
    }
</style>

@php
$lap_string = '';

$diff = abs(strtotime($ticket->created_at) - strtotime(date('Y-m-d H:i:s')));

$years   = (int)floor($diff / (365*60*60*24));
$months  = (int)floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days    = (int)floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

$hours   = (int)floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));

$minuts  = (int)floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

if($years > 0) {
    if($years > 1) {
        $lap_string .= $years.' Years ';
    }
    else {
        $lap_string .= $years.' Year ';
    }
}

if($months > 0) {
    if($months > 1) {
        $lap_string .= $months.' Months ';
    }
    else {
        $lap_string .= $months.' Month ';
    }
}

if($days > 0) {
    if($days > 1) {
        $lap_string .= $days.' Days ';
    }
    else {
        $lap_string .= $days.' Day ';
    }
}

if($hours > 0) {
    if($hours > 1) {
        $lap_string .= $hours.' Hours ';
    }
    else {
        $lap_string .= $hours.' Hour ';
    }
}

if($minuts > 0) {
    if($minuts > 1) {
        $lap_string .= $minuts.' Minutes ';
    }
    else {
        $lap_string .= $minuts.' Minute ';
    }
}
@endphp

<div class="container-fluid px-xl-3">
    <div class="row">

        <div class="col-lg-12">
            @if (session('success'))
                <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><i class="mdi mdi-alert-circle-outline mr-2"></i> {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="col-lg-8 mb-3 head_tit_vt">
                <div class="form_add_ticket_vt card-box">
                <form id="updateTicketForm" method="POST" action="{{ route('admin.update-ticket', ['id' => $ticket['id']])}}" role="form" enctype="multipart/form-data">
                    @csrf

                <div class="row">
                    <input type="hidden" name="company_id" value="{{$plant_detail->company->id}}">
                    <input type="hidden" name="plant_id" value="{{$plant_detail->id}}">

                    <div class="col-lg-12">
                        <label for="">Ticket # <strong>{{$ticket['id']}}</strong></label>
                    </div>

                        <div class="col-lg-6 mt-2">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Source<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>
                            <select class="form-control disAttr" id="source" name="source" required="">
                                <option value="">Select Source</option>
                                @if($sources)
                                    @foreach($sources as $key => $source)
                                        <option value="{{ $source->id }}" <?php echo isset($ticket['source']) &&  $ticket['source'] ==  $source->id ? 'selected' : '' ?>>{{ $source->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-2">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Priority<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>
                            <select class="form-control disAttr" name="priority" id="priority">
                                <option value="">Priority</option>
                                @if($priority)
                                    @foreach($priority as $key => $source)
                                        <option value="{{ $source->id }}" <?php echo isset($ticket['priority']) &&  $ticket['priority'] ==  $source->id ? 'selected' : '' ?> >{{ $source->priority }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4)
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Status<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>
                                <select class="form-control disAttr" name="status" id="ticket_status" style="display:block;">
                                    <option value="">Status</option>
                                    @if($status)
                                        @foreach($status as $key => $single_status)
                                            <option value="{{ $single_status->id }}" <?php echo isset($ticket['status']) &&  $ticket['status'] ==  $single_status->id ? 'selected' : '' ?> >{{ $single_status->status }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Received By<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>
                                <select class="form-control report_user disAttr" id="received_medium" name="received_medium" required="">
                                    <option value="Manual" <?php echo isset($ticket['received_medium']) &&  $ticket['received_medium'] ==  'Manual' ? 'selected' : '' ?>>Manual</option>
                                    <option value="Proactive" <?php echo isset($ticket['received_medium']) &&  $ticket['received_medium'] ==  'Proactive' ? 'selected' : '' ?>>Proactive</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Category<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>

                                <select class="form-control disAttr" id="category" name="category" required="">
                                    <option value="">Select Category</option>
                                    @if($categories)
                                        @foreach($categories as $key => $category)
                                            <option value="{{ $category->category_id }}" <?php echo isset($ticket['category']) &&  $ticket['category'] ==  $category->category_id ? 'selected' : '' ?> >{{ $category->category_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Sub Category<span class="text-danger">*</span></label>
                            <select class="form-control disAttr" id="sub_category" name="sub_category" required>

                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4)
                    <div class="col-lg-6 assign_to_div">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Assigned To</label>
                            <select class="form-control disAttr" id="assign_to" name="assign_to" data-toggle="select2">
                                <option value="">Select Technician</option>
                                @if($employees_list)
                                    @foreach($employees_list as $key => $employee)
                                        <option value="{{ $employee->id }}" {{ $ticket_agent['employee_id'] ==  $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-6 {{Auth::user()->roles != 5 ? '' : 'd-none'}}">
                        <div class="form-group">
                            <label>Due In (Hrs)<span id="duein_error"></span></label>
                            <select class="form-control disAttr" id="due_in" name="due_in" placeholder="Hours">
                                <option value="">Select Due In</option>
                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4)
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Laps Time</label>
                            <input type="text" value="{{$lap_string}}" class="form-control" disabled style="background: #f6f6f6;">
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Alternate Contact</label>
                            <input type="text" placeholder="Type here" id="alternate_contact" name="alternate_contact" value="<?php echo isset($ticket['alternate_contact'])  ? $ticket['alternate_contact'] : '' ?>" class="form-control disAttr">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Alternate Email</label>
                            <input type="text" placeholder="Type here" id="alternate_email" value="<?php echo isset($ticket['alternate_email'])  ? $ticket['alternate_email'] : '' ?>" name="alternate_email" class="form-control disAttr">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-control-label">Complain Creater</label>
                            <input type="text" placeholder="Type here" id="complain_creater" value="<?php echo isset($ticketCreatedByuser)  ? $ticketCreatedByuser : '' ?>" name="complain_creater" class="form-control disAttr" disabled>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-control-label">Title</label>
                            <input type="text" placeholder="Type here" id="title" name="title" class="form-control disAttr" value="<?php echo isset($ticket['title'])  ? $ticket['title'] : '' ?>" readonly style="background:#f6f6f6;">
                        </div>
                    </div>
                    @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4)
                    <div class="col-lg-12 closeTicketHiddenField">
                        <div class="form-group">
                            <label class="form-control-label">Notify by<span id="notify_by_error"></span></label>
                            <div class="checkbox checkbox-pink mb-1">
                                <input type="checkbox" name="notify_by[]" id="hobby1" value="sms" @if($ticket['notify_by'] != null) @foreach (explode(',', $ticket['notify_by']) as $tic) {{ $tic == "sms" ? 'checked' : '' }} @endforeach @endif>
                                <label for="hobby1"> SMS </label>
                            </div>
                            <div class="checkbox checkbox-pink mb-1">
                                <input type="checkbox" name="notify_by[]" id="hobby2" value="app" @if($ticket['notify_by'] != null) @foreach (explode(',', $ticket['notify_by']) as $tic) {{ $tic == "app" ? 'checked' : '' }} @endforeach @endif>
                                <label for="hobby2"> App Notification </label>
                            </div>
                            <div class="checkbox checkbox-pink">
                                <input type="checkbox" name="notify_by[]" id="hobby3" value="email" @if($ticket['notify_by'] != null) @foreach (explode(',', $ticket['notify_by']) as $tic) {{ $tic == "email" ? 'checked' : '' }} @endforeach @endif>
                                <label for="hobby3"> Email </label>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-control-label">Description</label>
                            <div style="background:#f6f6f6;padding:15px;">
                                <p style="text-align: justify;">{{isset($default_description->description) ? $default_description->description : ''}}</p>
                            </div>
                            @if($default_description)
                                @if($default_description->attachment != null || $default_description->attachment != '')
                                <label class="form-control-label mt-1">Attachment</label>
                                <div style="background:#f6f6f6;padding:15px;">
                                    @foreach(explode(',', $default_description->attachment) as $attach)
                                        <a href="{{url('admin/complain/ticket/download/'.$attach)}}">{{ $attach }}</a>
                                        <br>
                                    @endforeach
                                </div>
                                @endif
                            @endif
                         </div>
                    </div>
                    <div class="col-12 mt-2">
                        <label class="form-control-label">History</label>
                        <div class="form-group" style="background:#f6f6f6;padding:15px;">
                            @if($description_detail)
                                    @foreach ($description_detail as $key => $des_detail)
                                        <div class="listtext_area_vt">
                                            <div class="date_vt"><span>Added By: {{$des_detail->name}}</span><span style="float: right;">{{ date('d-M-Y h:i A', strtotime($des_detail->created_at)) }}</span></div>
                                            @if($des_detail->history_changes != null)
                                                @foreach($des_detail->history_changes as $key1 => $ab)
                                                    @if($ab != '')
                                                        <span>{{$key1}}: {{$ab}}<span>
                                                        <br>
                                                    @endif
                                                @endforeach
                                            @endif
                                                <br>
                                            <label>Description</label><p style="text-align: justify;">{{$des_detail->description}}</p>
                                            @if($des_detail->attachment != null || $des_detail->attachment != '')
                                            <label for="">Attachment</label>
                                            <br>
                                                @foreach(explode(',', $des_detail->attachment) as $attach)
                                                    <a href="{{url('admin/complain/ticket/download/'.$attach)}}">{{ $attach }}</a>
                                                    <br>
                                                @endforeach
                                            @endif
                                        </div>
                                        <hr style="border-bottom: 1px solid #504E4E;width:60%;">
                                    @endforeach
                                @else
                                <span>No History Found</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12 closeTicketHiddenField">
                        <div class="form-group">
                            <label class="form-control-label">Description<span class="text-danger">*&nbsp;&nbsp;<span id=""></span></span></label>
                            <textarea class="form-control" name="description" id="description" rows="8"  required="" placeholder="Type here"></textarea>
                         </div>
                    </div>
                    <div class="col-lg-6 closeTicketHiddenField">
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
                    </div>
                    <div class="col-md-12">
                        <button type="button" id="updateTicketSubmitBtn" class="btn-create-vt">Update Ticket</button>
                        @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 6)
                            <a href="{{route('admin.ticket.status.update',['id' => $ticket['id']])}}" type="button" class="btn-create-vt bg-secondary text-white closeTicket">Close Ticket</a>
                        @endif
                        <a href="{{route('admin.ticket.re.open',['id' => $ticket['id']])}}" type="button" class="btn-create-vt reOpenTicket" style="display: none;text-align:center;line-height:34px;">Re-open Ticket</a>
                    </div>
                </div>
            </form>

            </div>
        </div>
        <div class="col-lg-4 mt-3 plant_details_card_ticket">
            <div class="card-stat-vt p-0  mb-3">
                <div class="stat-area-hed-vt" style="background: none;">

                    @if(Auth::user()->roles != 5)
{{--                         @if($plant_detail->system_type == 2)--}}
{{--                            $type = 'bel'; ?>--}}
{{--                        @else--}}
{{--                           $type = "hybrid';--}}
{{--                        @endif--}}
                    <?php
                        $type= '';
                        if($plant_detail->system_type == 2 ||  $plant_detail->system_type == 1)
                            {
                                $type='bel';
                            }
                        elseif($plant_detail->system_type == 4)
                            {
                                $type='hybrid';
                            }
                        ?>
                            <a href="{{route('admin.edit.plant', [ 'type'=>$type ,'id' => $plant_detail->id])}}" id="ticket_edit_plant"><button class="eidt-profil-vt" style="
                                padding-top: 5px;
                                padding-bottom: 5px;
                                position: absolute;
                                z-index: 9;
                                right: 0;
                                top: 0;
                                min-width: 80px;">Edit Plant</button></a>
                            @endif

                    @if(isset($plant_detail->company) && $plant_detail->company && isset($plant_detail->company->logo) && $plant_detail->company->logo && $plant_detail->company->logo != null)

                        <img style="position: absolute; z-index: 5;" src="{{ $plant_detail->company && $plant_detail->company->logo ? asset('company_logo/'.$plant_detail->company->logo) : asset('company_logo/com_avatar.png')}}" alt="Company Logo" width="50">

                    @endif

                    <img style="    background-size: cover;
                    position: absolute;
                    z-index: 1;
                    width: 100%;
                    height: 140px;" src="{{ $plant_detail->plant_pic ? asset('plant_photo/'.$plant_detail->plant_pic) : asset('plant_photo/plant_avatar.png')}}" alt="Plant Picture" width="50">

                </div>
                <h2 id="ticket_pl_name" class="stat-head-vt">{{$plant_detail->plant_name }}</h2>
                <p>Plant Type<span id="ticket_pl_plant_type">{{ $plant_detail->plant_type }}</span></p>
                <p>System Type<span id="ticket_pl_system_type">{{$plant_detail->system_type_name}}</span></p>
                <p>Capacity<span id="ticket_pl_capacity">{{ $plant_detail->capacity }} kW</span></p>
                <p>Contact<span id="ticket_pl_contact">{{ $plant_detail->phone }}</span></p>
                <p>Owner<span id="ticket_pl_owner">{{$plant_detail->company->company_name}}</span></p>
                <p>Email<span id="ticket_pl_email">{{ $plant_detail->company->email }}</span></p>
                <!-- <p>Owner<span>faiizii awan</span></p> -->
            </div>
            <div class="card-stat-vt p-0  mb-3">


                <div class="head_right_vt">
                    <h2>Current Status</h2>
                </div>

                @if($plant_detail->is_online == 'Y')
                <div class="off_img_vt">
                    <img src="{{ asset('assets/images/on_off.png')}}" alt="Current" width="50">
                </div>
                <h2 class="stat-head-vt">Working Properly</h2>
                @elseif($plant_detail->is_online == 'P_Y')
                <div class="off_img_vt">
                    <img src="{{ asset('assets/images/on_off_orange.svg')}}" alt="Current" width="50">
                </div>
                <h2 class="stat-head-vt">Partial Online</h2>
                @else
                <div class="off_img_vt">
                    <img src="{{ asset('assets/images/on_off_blue.png')}}" alt="Current" width="50">
                </div>
                <h2 class="stat-head-vt">Offline</h2>

                @endif

            </div>

            <div class="card-stat-vt p-0  mb-3 plant_alert_graph_ticket">
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
                <div class="spinner-border text-primary" id="alertSpinner" role="status">
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
<!--  Alerts Center Modal-->

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script>

    var auth_id = {!! Auth::user()->roles !!};
    var plant_axis_grid = 4;
    var id = 0;
    var alert_date;
    var alert_time;

    $(document).ready(function() {

        var cat = <?php echo $categories; ?>;
        var ticket = <?php echo $ticket; ?>;
        var cat_arr = [];
        var sub_cat = <?php echo $sub_categories; ?>;
        var sub_cat_arr = [];

        var employee = <?php echo $employees; ?>;
        var em_arr = [];

        if(ticket.status == 6 || auth_id == 5 || auth_id == 6) {
            if(ticket.status == 6) {
                $('#updateTicketSubmitBtn').hide();
                $('.closeTicketHiddenField').hide();
                $('.closeTicket').hide();
                $('.reOpenTicket').show();
            }
            if(auth_id == 5) {
                $('.closeTicket').hide();
            }
            $('.disAttr').attr("disabled", "disabled");
            $('.disAttr').css("background-color", "#F6F6F6");
        }

        var status_val = $('#ticket_status').val();

        if(status_val == 2 || status_val == 5) {

            $('.assign_to_div').hide();
        }

        else {

            $('.assign_to_div').show();
        }

        getCategoryFields(cat);
        $('#category option[value="'+ticket.category+'"]').attr("selected","selected");
        getSubCategoryFields(sub_cat);
        $('#sub_category option[value="'+ticket.sub_category+'"]').attr("selected","selected");
        changeDueIn(sub_cat);
        $("#due_in option:selected").removeAttr("selected");
        $('#due_in option[value="'+ticket.due_in+'"]').attr("selected","selected");

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

            getCategoryFields(cat);
        });

        $('#category').on('change', function () {

            getSubCategoryFields(sub_cat);
        });

        $('#sub_category').on('change', function () {

            changeDueIn(sub_cat);
        });

        $('#updateTicketSubmitBtn').on('click', function() {

            var files = $('input#chooseFile')[0].files;

            if($('#ticket_status').val() != '' && $('#source').val() != '' && $('#priority').val() != '' && $('#category').val() != '' && $('#sub_category').val() != '' && $('#title').val() != '' && $('#description').val() != '') {

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

                    $('#updateTicketForm').submit();
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
                        $('#updateTicketForm').submit();
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
        id = {!!$ticket->plant_id!!};

        changeAlertDayMonthYear();
        alertGraphAjax(id, alert_date, alert_time);

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

        function getCategoryFields(cat, ticket) {

            $('#category').empty();
            $('#category').append('<option value="">Select Category</option>');
            $('#sub_category').empty();
            $('#sub_category').append('<option value="">Select Sub Category</option>');

            if(cat.length > 0) {

                var cat_name = $('#source').val();
                console.log(cat, cat_name);

                for(var i = 0; i < cat.length; i++) {

                    if(cat_name == cat[i].source_id) {

                        $('#category').append('<option value='+cat[i].category_id+'>'+cat[i].category_name+'</option>');
                    }
                }
            }
        }

        function getSubCategoryFields(sub_cat) {

            $('#sub_category').empty();
            $('#sub_category').append('<option value="">Select Sub Category</option>');

            if(sub_cat.length > 0) {

                var cat_name = $('#category').val();

                for(var i = 0; i < sub_cat.length; i++) {

                    if(cat_name == sub_cat[i].ticket_category_id) {

                        $('#sub_category').append('<option value='+sub_cat[i].id+'>'+sub_cat[i].sub_category_name+'</option>')
                    }
                }
            }
        }

        function changeDueIn(sub_cat) {

            $('#due_in').empty();
            var duration = 0;

            if(sub_cat.length > 0) {

                var cat_name = $('#sub_category').val();

                for(var i = 0; i < sub_cat.length; i++) {

                    if(cat_name == sub_cat[i].id) {

                        duration = sub_cat[i].duration;
                        if((parseInt(duration) - 3) > 0) {
                            $('#due_in').append('<option value='+(parseInt(duration) - 3)+'>'+(parseInt(duration) - 3)+'</option>');
                        }
                        if((parseInt(duration) - 2) > 0) {
                            $('#due_in').append('<option value='+(parseInt(duration) - 2)+'>'+(parseInt(duration) - 2)+'</option>');
                        }
                        if((parseInt(duration) - 1) > 0) {
                            $('#due_in').append('<option value='+(parseInt(duration) - 1)+'>'+(parseInt(duration) - 1)+'</option>');
                        }
                        $('#due_in').append('<option value='+sub_cat[i].duration+' selected>'+sub_cat[i].duration+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) + 1)+'>'+(parseInt(duration) + 1)+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) + 2)+'>'+(parseInt(duration) + 2)+'</option>');
                        $('#due_in').append('<option value='+(parseInt(duration) + 3)+'>'+(parseInt(duration) + 3)+'</option>');
                    }
                }
            }
        }

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
    });

    function submit_form(){
        var files = $('input#chooseFile')[0].files;
        console.log(files.length);
        if(files.length > 3){
            event.preventDefault();
            $('#attachment_error').html('Your selected attachments more than 3');
            return false;
        }else{
            $('#attachment_error').html('');
            return true;
        }
    }

</script>

@endsection
