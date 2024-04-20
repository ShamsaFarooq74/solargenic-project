@extends('layouts.admin.master')
@section('title', 'All Users')
@section('content')
    <style>
        .users_list_vt {
            width: 100%;
            padding: 15px;
        }

        .users_list_vt .nav-pills > li > a,
        .nav-tabs > li > a {
            color: #A7A6A6;
            font-weight: 300;
        }

        .users_list_vt .nav-tabs {
            float: right;
            z-index: 9;
            margin-top: -78px;
            border: 1px solid #435EBE;
            display: block;
            border-radius: 6px;
            width: 220px;
            overflow: hidden;
        }

        .users_list_vt .nav-item {
            border: none !important;
            float: left;
            width: 50%;
        }

        .users_list_vt .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            color: #fff !important;
            background-color: #435ebe;
            border-color: #435ebe #dee2e6 #fff;
            border-radius: 5px;
        }

        .btn_action_vt {
            color: #fff;
            background-color: #8DBE3F;
            border-color: #8DBE3F;
            border-radius: 5px;
        }

        .btn_action_vt:hover {
            color: #8DBE3F;
            background-color: #fff;
        }

        .table tr {
            cursor: pointer;
        }

        .table tbody tr:hover {
            background: #8DBE3F;
            color: #fff;
        }

        .table {
            background-color: #fff !important;
        }

        .hedding h1 {
            color: #fff;
            font-size: 25px;
        }

        .main-section {
            margin-top: 120px;
        }

        .hiddenRow {
            padding: 0 !important;
            background-color: #ebeef8;
        }

        .accordian-body span {
            color: #a2a2a2 !important;
        }

        /* .accordian-body p {
            margin: 0;
            float: left;
            width: 16%;
            padding-bottom: 12px;
        } */
        .user_email_vt {
            width: 100%;
            float: left;
            border-bottom: 1px solid #eaebef;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .user_email_vt span {
            width: 40px;
            float: left;
            font-size: 12px;
            font-weight: 300;
        }

        .user_email_vt p {
            width: auto;
            float: left;
            color: #a2a2a2 !important;
            font-size: 12px;
            font-weight: 300;
            margin: 0;
        }

        .user_email_vt p:hover {
            color: #a2a2a2 !important;
        }

        .accordian-body .card {
            border-radius: 4px !important;
            box-shadow: none !important;
        }

        .accordian-body .collapse.show {
            background: #f6f7fc !important;
        }

        .accordian-body .card-header {
            box-shadow: none !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__rendered {
            margin: 0;
            height: 26px;
            border-radius: 5px;
        }

        .table-hover tr {
            cursor: default !important;
            background: #fff !important;
            color: #1C1B1B;
        }

        .table-hover thead tr:hover {
            background: #fff !important;
            color: #1C1B1B;

        }

        .table-hover tbody tr {
            background: #fff !important;
            color: #9C9C9C !important;

        }

        .table-hover tbody tr:hover {
            background: #fff !important;
            color: #9C9C9C !important;

        }

        .fa-exclamation {
            border: 3px solid #E11818;
            width: 70px;
            height: 70px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #E11818;
            font-size: 27px;
            margin: 0 auto;
        }

        .btn_warning {
            color: #fff;
            background-color: #8DBE3F;
            border-color: #8DBE3F;
            min-width: 101px;
        }

        .btn-success {
            color: #fff;
            background-color: #E11818;
            border-color: #E11818;
        }

        .btn_warning:hover {
            color: #fff;
            background-color: #8DBE3F;
            border-color: #8DBE3F;
        }

        .btn-success:hover {
            color: #fff;
            background-color: #E11818;
            border-color: #E11818;
        }

        .btn_foot_vt {
            width: 215px;
            margin: 0 auto;
        }

        .modal-body.p-3 h3 {
            text-align: center;
            margin: 25px 0;
        }

        .modal-header .close {
            color: #fff;
        }

        .select2-container .select2-selection--multiple {
            min-height: 36px;
            box-shadow: none !important;
            background: #f9f9f9 !important;
            border: none !important;
            border-radius: .2rem !important;
        }

        .select2-container .select2-search--inline .select2-search__field {
            background: #f9f9f9 !important;
        }

        .select2-result.select2-result-unselectable.select2-disabled {
            display: none !important;
        }

    </style>
    <div class="col-md-12 mt-3 pb-3">
        @include('admin.alert-message')
        <div class="card">
        <div class="card-header">
                <h2 class="head_real_vt">Users List</h2>
            </div>

            <div class="users_list_vt">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="#home" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Add New
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#profile" data-toggle="tab" aria-expanded="true" class="nav-link active">
                            User list
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="home">
                        <form class="row" method="post" action="{{route('add.user')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Name*</label>
                                    <input type="text" placeholder="Name" name="name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Email*</label>
                                    <input type="text" placeholder="address@website.com" name="email"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Password*</label>
                                    <input type="password" placeholder="Password" name="password" class="form-control"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Confirm Password*</label>
                                    <input type="password" placeholder="ConfirmPassword" name="confirm_password"
                                           class="form-control" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Role*</label>
                                    <select class="form-control" id="example-select" name="role">
                                        <option>Select</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Company*</label>
                                    <select class="select2_demo_2 form-control" multiple="multiple" id="company_detail"
                                            name="company_detail[]" multiple="multiple">
                                        @foreach($companies as $company)
                                            <option value="{{$company->id}}">{{$company->company_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Plant*</label>
                                    <select class="select2_demo_2 form-control" multiple="multiple" id="plant-detail"
                                            name="plant_detail[]">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="btn_vt">
                                    <button type="button" class="btn_close" onclick="cancelAddUser()">Cancel</button>
                                    <button type="submit" class="btn_add">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane show active" id="profile">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="border-collapse:collapse;">
                                <thead>
                                <tr>
                                    <th>Sr #</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $key => $item)
                                    <tr colspan="6" data-toggle="collapse" data-target="#user-{{$key}}"
                                        class="accordion-toggle">
                                        <th scope="row">{{$key+1}}</th>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->email}}</td>
                                        <td>{{isset($item->role) && isset($item->role['name']) ? $item->role['name'] : ''}}</td>
                                        <td style="width: 144px;position: relative;">
                                            <div class="btn-group">
                                                <button data-toggle="dropdown"
                                                        class="btn btn_action_vt btn-xs dropdown-toggle">Action <i
                                                        class="mdi mdi-chevron-down"></i></button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button type="button" class="dropdown-item editUser"
                                                                data-toggle="modal" data-target=".bs-example-modal-lg"
                                                                data-id="{{$item->id}}">Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item deleteUser"
                                                                data-toggle="modal"
                                                                data-target=".bs-example-modal-center"
                                                                data-id="{{$item->id}}">Delete
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <i class="mdi mdi-chevron-down"
                                               style="font-size: 24px;position: absolute;top: 8px;right: 13px;"></i>
                                        </td>
                                        <!-- <td><i class="mdi mdi-chevron-down"></i></td> -->
                                    </tr>
                                    <tr class="p">
                                        <td colspan="6" class="hiddenRow">
                                            <div class="accordian-body collapse p-2" id="user-{{$key}}">
                                                <div id="accordion" class="mb-3">
                                                    @foreach($item->user_companies as $key1 => $userCompany)
                                                        <div class="card mb-1">
                                                            <div class="card-header" id="headingOne">
                                                                <h5 class="m-0">
                                                                    <a class="text-dark" data-toggle="collapse"
                                                                       href="#comp-{{$key1}}" aria-expanded="true">
                                                                        {{isset($userCompany->company) && ($userCompany->company['company_name']) ? $userCompany->company['company_name'] : ''}}
                                                                        <i class="mdi mdi-chevron-down"
                                                                           style="font-size: 24px;position: absolute;top: 8px;right: 13px;"></i>
                                                                    </a>
                                                                </h5>
                                                            </div>

                                                            <div id="comp-{{$key1}}" class="collapse show"
                                                                 aria-labelledby="headingOne" data-parent="#accordion">
                                                                <div class="card-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-hover mb-0">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>Sr#</th>
                                                                                <th>Plant Name</th>
                                                                                <th>Plant Type</th>
                                                                                <th>System Type</th>
                                                                                <th>Contact</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            @foreach($item->plant_user as $key2 => $companyPlant)
                                                                                @if(isset($companyPlant->plant) && isset($companyPlant->plant['plant_name']))
                                                                                    <tr>
                                                                                        <th scope="row">{{$key2+1}}</th>
                                                                                        <td>{{$companyPlant->plant['plant_name']}}</td>
                                                                                        <?php $plantType = \App\Http\Models\PlantType::where('id', $companyPlant->plant['plant_type'])->first('type') ?>
                                                                                        <td>{{$plantType ? $plantType->type : ''}}</td>
                                                                                        <?php $systemType = \App\Http\Models\SystemType::where('id', $companyPlant->plant['system_type'])->first('type') ?>
                                                                                        <td>{{$systemType ? $systemType->type : ''}}</td>
                                                                                        <td>{{isset($companyPlant->plant) && isset($companyPlant->plant['phone']) ? $companyPlant->plant['phone'] : ''}}</td>
                                                                                    </tr>
                                                                                @endif
                                                                            @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-center deleteModal" tabindex="-1" role="dialog"
         aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel">Are you example</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div> -->
                <div class="modal-body p-3">
                    <input type="hidden" id="dltUserID">
                    <i class="fas fa-exclamation"></i>
                    <h3>Are you sure to delete this user ?</h3>
                    <div class="btn_foot_vt">
                        <button type="button" class="btn btn-success waves-effect waves-light deleteConfirm"
                                id="user-delete">Yes, Delete
                        </button>
                        <!-- Small modal -->
                        <button type="button" class="btn btn_warning waves-effect waves-light" data-dismiss="modal"
                                aria-hidden="true" id="user-cancel">Cancel
                        </button>
                        <!-- Center modal -->
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="modal fade bs-example-modal-lg editModal" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-6" id="editForm">
                        <div class="form-group">
                            <label class="form-control-label">Name*</label>
                            <input type="text" placeholder="Name" name="name" class="form-control" id="userName"
                                   value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Email*</label>
                            <input type="text" placeholder="address@website.com" name="email"
                                   class="form-control" id="userEmail">
                            <span class="text-danger" id="emailError"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Role*</label>
                            <select class="form-control" id="example-select" name="role">
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Company*</label>
                            <select class="select2_demo_2 form-control" multiple="multiple" id="company_detail123"
                                    name="company_detail[]">
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->company_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Plant*</label>
                            <select class="select2_demo_2 form-control" multiple="multiple" id="plant-detail123"
                                    name="plant_detail[]">
                            </select>
                        </div>
                    </div>
                    {{--                            <div class="col-md-12">--}}
                    {{--                                <div class="form-group">--}}
                    {{--                                    <label class="form-control-label">Plant*</label>--}}
                    {{--                                    <select class="select2_demo_2 form-control" multiple="multiple">--}}
                    {{--                                        <option value="Monaco">Monaco</option>--}}
                    {{--                                        <option value="Mongolia">Mongolia</option>--}}
                    {{--                                        <option value="Montenegro">Montenegro</option>--}}
                    {{--                                        <option value="Montserrat">Montserrat</option>--}}
                    {{--                                        <option value="Morocco">Morocco</option>--}}
                    {{--                                        <option value="Mozambique">Mozambique</option>--}}
                    {{--                                        <option value="Myanmar">Myanmar</option>--}}
                    {{--                                        <option value="Namibia">Namibia</option>--}}
                    {{--                                        <option value="Nauru">Nauru</option>--}}
                    {{--                                        <option value="Nepal">Nepal</option>--}}
                    {{--                                    </select>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    <div class="col-md-12">
                        <div class="btn_vt">
                            <button class="btn_close" id="update-cancel" data-dismiss="modal">Cancel</button>
                            <button class="btn_add editConfirm">Update</button>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>


    <script src="{{ asset('assets/js/jquery.min.js')}}"></script>
    <script src="{{ asset('assets/js/sweetalert.min.js')}}"></script>
    <script>
        function cancelAddUser() {
            window.location.reload();
        }

        $('.accordion-toggle').click(function () {
            $('.hiddenRow').hide();
            $(this).next('tr').find('.hiddenRow').show();
        });
        $(document).ready(function () {
            $('#company_detail').change(function () {
                let id = $(this).val();
                let companyId = id.length > 0 ? id : [];
                $('#plant-detail').empty();
                $.ajax({
                    url: 'plant-details',
                    type: 'get',
                    data: {'companyIds': companyId},
                    dataType: 'json',
                    success: function (response) {
                        if (response.status) {
                            for (let i = 0; i < response.data.length; i++) {
                                let id = response.data[i].id;
                                let plantName = response.data[i].plant_name;
                                let option = "<option value='" + id + "'>" + plantName + "</option>";
                                $("#plant-detail").append(option);
                            }
                        } else {
                            $("#plant-detail").find('option').get(0).remove();
                        }

                    }
                });
            });
        });
        $(document).ready(function () {
            $('#company_detail123').change(function () {
                let id = $(this).val();
                let companyId = id.length > 0 ? id : [];
                $('#plant-detail').empty();
                $.ajax({
                    url: 'plant-details',
                    type: 'get',
                    data: {'companyIds': companyId},
                    dataType: 'json',
                    success: function (response) {
                        if (response.status) {
                            for (let i = 0; i < response.data.length; i++) {
                                let id = response.data[i].id;
                                let plantName = response.data[i].plant_name;
                                let option = "<option value='" + id + "'>" + plantName + "</option>";
                                $("#plant-detail").append(option);
                            }
                        } else {
                            $("#plant-detail").find('option').get(0).remove();
                        }

                    }
                });
            });
        });
        $('.deleteUser').click(function () {
            var data = $(this).attr('data-id');
            $('.deleteModal #dltUserID').val(data);

        });

        $('.deleteConfirm').click(function () {

            var id = $('.deleteModal #dltUserID').val();
            $.ajax({
                url: 'delete-user',
                type: 'post',
                data: {"_token": "{{csrf_token()}}", 'id': id},
                dataType: 'json',
                success: function (response) {
                    if (response.status) {
                        swal("User Deleted Successfully!");
                        document.getElementById('user-cancel').click();
                        window.location.reload();
                    }

                }
            });
        });
        $(document).ready(function () {
            $('#company_detail-modal').change(function () {
                let id = $(this).val();
                let companyId = id.length > 0 ? id : [];
                $('#plant-detail-modal').find('option').remove();
                $.ajax({
                    url: 'plant-details',
                    type: 'get',
                    data: {'companyIds': companyId},
                    dataType: 'json',
                    success: function (response) {
                        if (response.status) {
                            for (let i = 0; i < response.data.length; i++) {
                                let id = response.data[i].id;
                                let plantName = response.data[i].plant_name;
                                let option = "<option value='" + id + "'>" + plantName + "</option>";
                                $("#plant-detail-modal").append(option);
                            }
                        } else {
                            $("#plant-detail-modal").find('option').get(0).remove();
                        }

                    }
                });

            });
        });
        $(document).ready(function () {
            $('.editModal #company_detail123').change(function () {
                let id = $(this).val();
                let companyId = id.length > 0 ? id : [];
                addCompanyPlant(companyId)
            });
        });

        function addCompanyPlant(companyId) {
            $('.editModal #plant-detail123').empty();
            $.ajax({
                url: 'plant-details',
                type: 'get',
                data: {'companyIds': companyId},
                dataType: 'json',
                success: function (response) {
                    if (response.status) {

                        for (let i = 0; i < response.data.length; i++) {
                            let id = response.data[i].id;
                            let plantName = response.data[i].plant_name;
                            let option = "<option value='" + id + "'>" + plantName + "</option>";
                            $('.editModal #plant-detail123').append(option);
                        }
                    } else {
                        $('.editModal #plant-detail123').empty()
                    }

                }
            });
        }

        function addCompanyPlantData(companyId, plant) {
            $.ajax({
                url: 'plant-details',
                type: 'get',
                data: {'companyIds[]': companyId},
                dataType: 'json',
                success: function (response) {
                    if (response.status) {
                        $('.editModal #plant-detail123').empty();
                        for (let i = 0; i < response.data.length; i++) {
                            let id = response.data[i].id;
                            let plantName = response.data[i].plant_name;
                            let option = "<option value='" + id + "'>" + plantName + "</option>";
                            $('.editModal #plant-detail123').append(option);

                        }
                        for (let i = 0; i < plant.length; i++) {
                            console.log('okkkk' + plant[i]['id']);
                            $('.editModal #plant-detail123 option[value="' + plant[i]['id'] + '"]').attr("selected", "selected");
                            $('.editModal #plant-detail123').trigger('change');
                        }
                    } else {
                        $('.editModal #plant-detail123').empty()
                    }

                }
            });
        }

        $('.editUser').click(function () {
            var data = $(this).attr('data-id');
            $('.editModal #editForm').val(data);
            $.ajax({
                url: 'get-users',
                type: 'get',
                data: {'userId': data},
                dataType: 'json',
                success: function (response) {
                    $('.editModal #userName').val(response.name);
                    $('.editModal #userEmail').val(response.email);
                    $('.editModal #example-select option[value="' + response.role.id + '"]').attr("selected", "selected");
                    let company = response.company;
                    let companyArray = [];
                    $.each(response.company, function (i, e) {
                        $('.editModal #company_detail123 option[value="' + e.id + '"]').attr("selected", "selected");
                        $('.editModal #company_detail123').trigger('change');

                        companyArray.push(e.id)

                    });
                    addCompanyPlantData(companyArray, response.plant)
                    console.log(companyArray)
                    // for(let i=0;i<response.plant.length;i++) {
                    //     $('.editModal #plant-detail123 option[value="' + response.plant[i]['id'] + '"]').attr("selected", "selected");
                    //     $('.editModal #plant-detail123').trigger('change');
                    // }

                }
            });

        });

        $('.editConfirm').click(function () {

            var id = $('.editModal #editForm').val();
            let userName = $('.editModal #userName').val();
            let userEmail = $('.editModal #userEmail').val();
            let userRole = $('.editModal #example-select').val();
            let userCompany = $('.editModal #company_detail123').val();
            let userPlant = $('.editModal #plant-detail123').val();
            console.log(userPlant)
            {{--return--}}
            {{--$.ajax({--}}
            {{--    url: 'update-user',--}}
            {{--    type: 'post',--}}
            {{--    data: {--}}
            {{--        "_token": "{{csrf_token()}}",--}}
            {{--        'user_id': id,--}}
            {{--        'name': userName,--}}
            {{--        'email': userEmail,--}}
            {{--        'role': userRole,--}}
            {{--        'plant_detail[]': userPlant,--}}
            {{--        'company_detail[]': userCompany--}}
            {{--    },--}}
            {{--    dataType: 'json',--}}
            {{--    success: function (response) {--}}
            {{--        if (response.status) {--}}
            {{--            swal("User Updated Successfully!");--}}
            {{--            document.getElementById('update-cancel').click();--}}
            {{--            window.location.reload();--}}
            {{--        } else {--}}
            {{--            if (response.message) {--}}
            {{--                $('.editModal #emailError').html(response.message);--}}
            {{--            }--}}
            {{--        }--}}

            {{--    }--}}
            {{--});--}}
        });

    </script>

@endsection
