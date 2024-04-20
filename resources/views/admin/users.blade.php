@extends('layouts.admin.master')
@section('title', 'All Users')
@section('content')


    <div class="content">
        <div class="bred_area_vt">
            <div class="row">
                <div class="col-xl-12">
                    <div class="home-companies-area-vt">
                        <form id="filtersForm" class="home-companise_dash-vt" action="{{route('admin.user.all')}}" method="GET">
                            <?php
                            $filter = Session::get('filter');
                            ?>
                            <div class="form-group">
                                <select class="form-control multiCompanyFilter" name="company[]" id="company" multiple>
                                    <option value="all">Company Name</option>
                                    @if(isset($filter_data['company_array']) && $filter_data['company_array'])
                                        @foreach($filter_data['company_array'] as $company_data)
                                            <option value="{{ $company_data->id }}" <?php echo isset($filter['company']) && in_array($company_data->id, $filter['company'])  ? 'selected' : '' ?>>{{ $company_data->company_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group" style="min-width: 90px;">
                                <select class="form-control multiPlantFilter" name="plant_name[]" id="plant_name" multiple>
                                    @if(isset($filter_data['plants']) && $filter_data['plants'])
                                        @foreach($filter_data['plants'] as $plant)
                                            <option value="{{ $plant->id }}" <?php echo isset($filter['plant_name']) && in_array($plant->id, $filter['plant_name'])  ? 'selected' : '' ?>>{{ $plant->plant_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="role" id="role">
                                    <option value="all">Role</option>
                                    @if(isset($filter_data['role_array']) && $filter_data['role_array'])
                                        @foreach($filter_data['role_array'] as $role_data)
                                            <option value="{{ $role_data->id }}" <?php echo isset($filter['role']) && $filter['role'] == $role_data->id  ? 'selected' : '' ?>>{{ $role_data->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="btn-companiescl-vt" id="searchButtonDiv">
                                <button type="submit" class="btn_se_cle_vt" id="searchFilters">
                                    <img src="{{ asset('assets/images/search_01.svg')}}" alt="search">
                                </button>
                                <button type="button" class="btn_se_cle_vt" id="clearFilters">
                                    <img src="{{ asset('assets/images/cle_02.svg')}}" alt="clear">
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card hum_tum_vt pla_body_padd_vt">
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
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-header border-0 mt-3" style="box-shadow: none !important;">
                            <div class="dataTables_length_vt bs-select" id="dtBasicExample_length"><label>Show <select name="dtBasicExample_length" aria-controls="dtBasicExample" class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive table_user_hed_vt">
                    <table id="datatable_3" class="table table-borderless table-centered table-nowrap mb-2">
                        <thead class="thead-light vt_head_td">
                        <tr>
                            <th>
                                Name
                            </th>
                            <th>
                                Email
                            </th>
                            <th>
                                Role
                            </th>
                            <th>
                                Company Name
                            </th>
                            <th>
                                Plant Name
                            </th>
                            <th>
                                Edit
                            </th>
                            <th>
                                Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody class="btn_a_vt">
                        @if(count($users) > 0)
                            @foreach ($users as $usr)
                                <tr>
                                    <td>
                                        {{isset($usr->username) ? $usr->username : ""}}
                                    </td>
                                    <td>
                                        {{isset($usr->email) ? $usr->email : ""}}
                                    </td>
                                    <td>
                                        {{isset($usr->role->name) ? $usr->role->name : ""}}
                                    </td>
                                    <td>
                                        @if($usr->roles != 1 && $usr->roles != 2)
                                            @foreach ($usr->user_companies as $us)
                                                {{isset($us->company['company_name']) ? $us->company['company_name'] : ""}}<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        {{--                                @if($usr->roles != 1 && $usr->roles != 2)--}}
                                        @foreach ($usr->plant_user as $pl)
                                            {{isset($pl->plant['plant_name']) ? $pl->plant['plant_name'] : ""}}<br>
                                        @endforeach
                                        {{--                                @endif--}}
                                    </td>
                                    <td>
                                        <button type="button" class="dropdown-item edit_users" data-toggle="modal" data-target="#edit-user-vt" data-user="{{json_encode($usr)}}">
                                            <img src="{{ asset('assets/images/edit_users.svg')}}" alt="" width="15px">
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="dropdown-item" data-toggle="modal" data-target="#delete-user-vt-{{$usr->id}}">
                                            <img src="{{ asset('assets/images/delete_users.svg')}}" alt="" width="15px">
                                        </button>
                                    </td>

                                    <!-- Modal Delete -->
                                    <div class="modal fade" id="delete-user-vt-{{$usr->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <form id="deleteform" method="POST" action="{{ url('/admin/delete-user') }}" class="py-3">
                                                        @csrf
                                                        <i class="fas fa-exclamation"></i>
                                                        <input type="hidden" name="id" value="{{$usr->id}}">
                                                        <h4 class="model-heading-vt">Are you sure to delete <br>this user ?</h4>
                                                        <div class="btn_too_vt">
                                                            <button type="submit" class="btn-create-vt">Yes, Delete</button>
                                                            <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade edit_user_modal" id="edit-user-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Edit User</h5>
                        <i class="fa-regular fa-circle-xmark" data-dismiss="modal" style="color:white; position: absolute; right: 20px;"></i>
                    </div>
                    <div class="modal-body">
                        <form class="parsley-examples updateUserForm" method="POST" action="{{ url('admin/update-user') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="custom-file mb-3">
                                <div class="file-upload">
                                    <div class="file-select">
                                        <div class="file-select-button" id="fileName1">Choose File</div>
                                        <div class="file-select-name" id="noFile1">No file chosen...</div>
                                        <input type="file" name="profile_pic" id="chooseFile1">
                                    </div>
                                </div>
                                <!-- <input type="file" class="custom-file-input" id="profile_pic" name="profile_pic">
                                <label class="custom-file-label" for="customFile">Choose file</label> -->
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Name<span class="text-danger">*&nbsp;&nbsp;<span id="name_error"></span></span></label>
                                <input type="text" placeholder="Name" id="name" name="name" class="form-control edit_user_name" required="">
                                <input type="hidden" class="edit_user_id" id="user_id" name="user_id">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Email<span class="text-danger">*&nbsp;&nbsp;<span id="email_error"></span></span></label>
                                <input type="email" placeholder="Email" id="email" name="email" class="form-control edit_user_email" required>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Password</label>
                                <input type="password" placeholder="Password" id="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Confirm Password</label>
                                <input type="password" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">User Role<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>
                                <select class="form-control report_user edit_user_type_select edit_user_type" id="user_type" name="user_type" required="">
                                    <option value="">User Role</option>
                                    @if($roles)
                                        @foreach($roles as $key => $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group all_company">
                                <label for="exampleFormControlSelect1">Company<span class="text-danger">*&nbsp;&nbsp;<span id="company_error"></span></span></label>
                                <select class="form-control edit_company_id editMultiCompany" id="company_id" name="company_id[]" multiple>
                                    @if($companies)
                                        {{-- <option value="all">Select all</option> --}}
                                        @foreach($companies as $key => $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group all_plants">
                                <label for="exampleFormControlSelect1">Plant<span class="text-danger">*&nbsp;&nbsp;<span id="plant_error"></span></span></label>
                                <select class="form-control edit_all_plants_select edit_plant_id editMultiPlant" id="plant_id" name="plant_id[]" multiple="multiple">
                                    @if($plants)
                                        @foreach($plants as $key => $plant)
                                            <option value="{{ $plant->id }}">{{ $plant->plant_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <button type="button" class="btn-create-vt" onclick="update_user()">Update</button>
                            <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Add new-->
        <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Add User</h5>
                    </div>
                    <div class="modal-body">
                        <form class="parsley-examples" id="loginForm" method="POST" action="{{ url('/admin/add-user') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="custom-file mb-3">
                                <div class="file-upload">
                                    <div class="file-select">
                                        <div class="file-select-button" id="fileName">Choose File</div>
                                        <div class="file-select-name" id="noFile">No file chosen...</div>
                                        <input type="file" name="profile_pic" id="chooseFile">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Name<span class="text-danger">*&nbsp;&nbsp;<span id="name_error"></span></span></label>
                                <input type="text" placeholder="Name" id="name" name="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Email<span class="text-danger">*&nbsp;&nbsp;<span id="email_error"></span></span></label>
                                <input type="email" placeholder="Email" id="email" name="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Username<span class="text-danger">*&nbsp;&nbsp;<span id="username_error"></span></span></label>
                                <input type="text" placeholder="Username" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Password<span class="text-danger">*&nbsp;&nbsp;<span id="password_error"></span></span></label>
                                <input type="password" placeholder="Password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Confirm Password<span class="text-danger">*&nbsp;&nbsp;<span id="confirm_password_error"></span></span></label>
                                <input type="password" placeholder="Confirm Password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">User Role<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>
                                <select class="form-control add_user_type_select report_user" id="user_type" name="user_type" required>
                                
                                <option value="">User Role</option>
                                    @if($roles)
                                        @foreach($roles as $key => $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
{{--                            <div class="form-group all_company" style="display: none;">--}}
{{--                                <label for="exampleFormControlSelect1">Company<span class="text-danger">*&nbsp;&nbsp;<span id="company_error"></span></span></label>--}}
{{--                                <select class="form-control add_company_id addMultiCompany" id="company_id" name="company_id[]" multiple>--}}
{{--                                    --}}{{-- <option value="all">Select all</option> --}}
{{--                                    @if($companies)--}}
{{--                                        @foreach($companies as $key => $company)--}}
{{--                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    @endif--}}
{{--                                </select>--}}
{{--                            </div>--}}



                            <div class="form-group all_company" style="display: none;">
                                <label for="exampleFormControlSelect1">Company<span class="text-danger">*&nbsp;&nbsp;<span id="company_error"></span></span></label>

                                <select class="select2_demo_2 form-control add_company_id addMultiCompany" name="company_id[]" id="company_id" multiple="multiple">
                                    @if($companies)
                                        @foreach($companies as $key => $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group all_plants" style="display: none;">
                                <label for="exampleFormControlSelect1">Plant<span class="text-danger">*&nbsp;&nbsp;<span id="plant_error"></span></span></label>
                                <select class="select2_demo_2 form-control all_plants_select addMultiPlant" id="plant_id" name="plant_id[]" multiple="multiple">
                                </select>
                            </div>
                            <button type="submit" class="btn-create-vt addSubmitBtn">Add</button>
                            <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal View -->
        <div class="modal fade" id="view-user-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="exampleModalCenterTitle">User Detail</h5>
                        <button type="button" class="close-vt" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 alerts-head-text-vt">
                                <p>Name</p>
                            </div>
                            <div class="col-md-6 alerts-detail-text-vt">
                                <p id="name_val"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 alerts-head-text-vt">
                                <p>Email</p>
                            </div>
                            <div class="col-md-6 alerts-detail-text-vt">
                                <p id="email_val"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 alerts-head-text-vt">
                                <p>Username</p>
                            </div>
                            <div class="col-md-6 alerts-detail-text-vt">
                                <p id="username_val"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 alerts-head-text-vt">
                                <p>Report Type</p>
                            </div>
                            <div class="col-md-6 alerts-detail-text-vt">
                                <p id="report_type_val"></p>
                            </div>
                        </div>
                        <div class="row new_company_val">
                            <div class="col-md-6 alerts-head-text-vt">
                                <p>Company Name</p>
                            </div>
                            <div class="col-md-6 alerts-detail-text-vt">
                                <p id="company_val"></p>
                            </div>
                        </div>
                        <div class="row new_plant_val">
                            <div class="col-md-6 alerts-head-text-vt">
                                <p>Plant Name</p>
                            </div>
                            <div class="col-md-6 alerts-detail-text-vt">
                                <p id="plant_val"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script>

        $(document).ready(function() {

            var user_obj;
            var plants = <?php echo $plants; ?>;
            var comArr = <?php echo json_encode($companyArray); ?>;
            var selectedArr = [];

            $(".multiCompanyFilter").select2({

                "placeholder": "Select Company"
            });

            $(".multiPlantFilter").select2({

                "placeholder": "Select Plant"
            });

            $(".addMultiCompany").select2({

                "placeholder": "Select Company"
            });

            $(".addMultiPlant").select2({

                "placeholder": "Select Plant"
            });

            $(".editMultiCompany").select2({

                "placeholder": "Select Company"
            });

            $(".editMultiPlant").select2({

                "placeholder": "Select Plant"
            });

            $('.addMultiCompany').on('change', function() {

                $('.addMultiPlant').empty();

                if(plants.length > 0) {

                    var com_id = $('.addMultiCompany').val();
                    console.log(com_id);

                    for(var i = 0; i < com_id.length; i++) {

                        for(var j = 0; j < plants.length; j++) {

                            if(com_id[i] == plants[j].company_id) {

                                $('.addMultiPlant').append('<option value='+plants[j].id+'>'+plants[j].plant_name+'</option>')
                            }
                        }
                    }
                }
            });

            $('#company').on('change', function() {

                changeFilterData(plants);
            });

            $('.editMultiCompany').select2().on('change', function() {

                $('.editMultiPlant').empty();

                if(plants.length > 0) {

                    var com_id = $('.editMultiCompany').val();
                    console.log(com_id);

                    for(var i = 0; i < com_id.length; i++) {

                        for(var j = 0; j < plants.length; j++) {

                            if(com_id[i] == plants[j].company_id) {

                                $.each(user_obj.plant_user, function(i,e){

                                    $('.editMultiPlant option[value="'+e.plant_id+'"]').attr("selected","selected");
                                });

                                $('.editMultiPlant').append('<option value='+plants[j].id+'>'+plants[j].plant_name+'</option>')
                            }
                        }
                    }
                }
            });

            $('.edit_users').click(function() {
                user_obj = $(this).attr('data-user');
                user_obj = JSON.parse(user_obj);

                changeRole($('.report_user'));

                var comp_id = user_obj.user_companies;

                $.each(user_obj.user_companies, function(i,e){

                    selectedArr.push(e.company_id);
                });

                $('.editMultiCompany').select2().val(selectedArr).trigger('change');
                $('.edit_plant_id').select2().val(selectedArr).trigger('change');

                $('.edit_plant_id').empty();

                $.ajax({
                    url: "{{ route('admin.user.company.plants') }}",
                    method: "GET",
                    data: {
                        'company_id': comp_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);

                        if(data.length != 0) {

                            $.each(data, function (i, item) {
                                $('.edit_plant_id').append($('<option>', {
                                    value: item.id,
                                    text : item.plant_name
                                }));
                            });

                            $('.edit_user_modal .edit_plant_id option:selected').removeAttr('selected');

                            $.each(user_obj.plant_user, function(i,e){
                                console.log(e);
                                console.log(e.plant_id);
                                $('.edit_user_modal .edit_plant_id option[value="'+e.plant_id+'"]').attr("selected","selected");
                            });
                        }
                        else {

                            $('.edit_user_modal .all_company').hide();
                            $('.edit_user_modal .all_plants').hide();
                        }

                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

                $('.edit_user_modal .edit_user_id').val('');
                $('.edit_user_modal .edit_user_name').val('');
                $('.edit_user_modal .edit_user_email').val('');
                $('.edit_user_modal .edit_user_username').val('');
                $('.edit_user_modal .edit_user_type option:selected').removeAttr('selected');
                $('.edit_user_modal .edit_company_id option:selected').removeAttr('selected');

                $('.edit_user_modal .edit_user_id').val(user_obj.id);
                $('.edit_user_modal .edit_user_name').val(user_obj.name);
                $('.edit_user_modal .edit_user_email').val(user_obj.email);
                $('.edit_user_modal .edit_user_username').val(user_obj.username);
                $('.edit_user_modal .edit_user_type option[value="'+user_obj.roles+'"]').attr("selected","selected");
                $('.edit_user_modal .edit_company_id option[value="'+user_obj.company_id+'"]').attr("selected","selected");

            });

            $('.report_user').change(function() {

                changeRole($(this));

            });

            $('.add_user_type_select').on('change', function() {

                console.log($('.add_user_type_select').val());

                if($('.add_user_type_select').val() == 3 || $('.add_user_type_select').val() == 4 || $('.add_user_type_select').val() == 5 || $('.add_user_type_select').val() == 6) {

                    $('.add_company_id').attr('required', 'required');
                }
                if($('.add_user_type_select').val() == 5 || $('.add_user_type_select').val() == 6) {

                    $('.all_plants_select').attr('required', 'required');
                }
                if($('.add_user_type_select').val() == 1 || $('.add_user_type_select').val() == 2) {

                    $('.add_company_id').removeAttr('required');
                    $('.all_plants_select').removeAttr('required');
                }
            });

            $('.edit_user_type_select').on('change', function() {

                if($('.edit_user_type_select').val() == 3 || $('.edit_user_type_select').val() == 4 || $('.edit_user_type_select').val() == 5 || $('.edit_user_type_select').val() == 6) {

                    $('.edit_company_id').attr('required', 'required');
                }
                if($('.edit_user_type_select').val() == 5 || $('.edit_user_type_select').val() == 6) {

                    $('.edit_all_plants_select').attr('required', 'required');
                }
                if($('.edit_user_type_select').val() == 1 || $('.edit_user_type_select').val() == 2) {

                    $('.edit_company_id').removeAttr('required');
                    $('.edit_all_plants_select').removeAttr('required');
                }
            });

            /*$('.add_company_id').on('change', function() {

                var comp_id = $('.add_company_id').val();
                $('.all_plants_select').empty();

                $.ajax({
                    url: "{{ route('admin.user.company.plants') }}",
                method: "GET",
                data: {
                    'company_id': comp_id
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);

                    if(data.length != 0) {

                        $.each(data, function (i, item) {
                            $('.all_plants_select').append($('<option>', {
                                value: item.id,
                                text : item.plant_name
                            }));
                        });
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });
        });*/

            /*$('.edit_company_id').on('change', function() {

                var comp_id = $('.edit_company_id').val();
                $('.edit_plant_id').empty();

                $.ajax({
                    url: "{{ route('admin.user.company.plants') }}",
                method: "GET",
                data: {
                    'company_id': comp_id
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);

                    if(data.length != 0) {

                        $.each(data, function (i, item) {
                            $('.edit_plant_id').append($('<option>', {
                                value: item.id,
                                text : item.plant_name
                            }));
                        });
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });
        });*/

            $('#clearFilters').on('click', function(e) {

                $('#company').prop('selectedIndex', 0);
                $('#user').prop('selectedIndex', 0);

                $(".select2-multiple").val("");
                $(".select2-multiple").trigger("change");

                $('#filtersForm').trigger('submit');

            });

            $('#company').on('change', function() {

                $('#plant_name').empty();

                if (plants.length > 0) {

                    var com_id = $('#company').val();

                    for (var i = 0; i < plants.length; i++) {

                        if (com_id == 'all') {

                            $('#plant_name').append('<option value=' + plants[i].id + '>' + plants[i].plant_name + '</option>')
                        } else {

                            if (com_id == plants[i].company_id) {

                                $('#plant_name').append('<option value=' + plants[i].id + '>' + plants[i].plant_name + '</option>')
                            }
                        }
                    }
                }
            });

        });

        function changeRole(obj) {

            if (obj.val() == 1 || obj.val() == 2) {

                $('.all_company').hide();

                $('.all_plants').hide();

            } else if (obj.val() == 3 || obj.val() == 4) {

                $('.all_plants').hide();

                $('.all_company').show();

            } else if (obj.val() == 5 || obj.val() == 6) {

                $('.all_company').show();

                $('.all_plants').show();

            }
        }

        function changeFilterData(plants) {

            $('#plant_name').empty();

            if(plants.length > 0) {

                var com_id = $('#company').val();

                for(var i = 0; i < com_id.length; i++) {

                    for(var j = 0; j < plants.length; j++) {

                        if(com_id[i] == plants[j].company_id) {

                            $('#plant_name').append('<option value='+plants[j].id+'>'+plants[j].plant_name+'</option>')
                        }
                    }
                }
            }
        }
    </script>

    <script type="text/javascript">

        /*function submit_user() {
            console.log($('.report_user').val());
            var error = 0;
            if ($('#name').val() == '') {
                error = 1;
                $('#name_error').html('This field is required');
            } else {
                $('#name_error').html('');
            }

            if ($('#email').val() == '') {
                error = 1;
                $('#email_error').html('This field is required');
            } else {
                $('#email_error').html('');
            }

            if ($('#username').val() == '') {
                error = 1;
                $('#username_error').html('This field is required');
            } else {
                $('#username_error').html('');
            }

            if ($('#password').val() == '') {
                error = 1;
                $('#password_error').html('This field is required');
            } else {
                $('#password_error').html('');
            }

            if ($('#confirm_password').val() == '') {
                error = 1;
                $('#confirm_password_error').html('This field is required');
            } else {
                $('#confirm_password_error').html('');
            }

            if ($('#user_type').val() == '') {
                error = 1;
                $('#user_type_error').html('This field is required');
            } else {
                $('#user_type_error').html('');
            }

            if ($('.report_user').val() == 3 || $('.report_user').val() == 4) {
                if ($('#company_id').val() == '') {
                    error = 1;
                    $('#company_error').html('This field is required');
                } else {
                    $('#company_error').html('');
                }
            } else if ($('.report_user').val() == 5) {
                if ($('#company_id').val() == '') {
                    error = 1;
                    $('#company_error').html('This field is required');
                } else {
                    $('#company_error').html('');
                }

                if ($('#plant_id').val() == '') {
                    error = 1;
                    $('#plant_error').html('This field is required');
                } else {
                    $('#plant_error').html('');
                }
            } else if ($('.report_user').val() == 6) {
                if ($('#plant_id').val() == '') {
                    error = 1;
                    $('#plant_error').html('This field is required');
                } else {
                    $('#plant_error').html('');
                }
            }

            if (error == 0) {
                return true;
            } else {
                event.preventDefault();
                return false;
            }

        }*/

        function update_user() {

            if($('#password').val() != '' || $('#password_confirmation').val() != '') {

                if($('#password').val() !== $('#password_confirmation').val()) {

                    alert('Password and confirm password should be same');
                }

                else {

                    $('.updateUserForm').submit();
                }
            }

            else {

                $('.updateUserForm').submit();
            }

            /*var report_user = $('.edit_user_detail .report_user').val();
            var error = 0;
            if (report_user == 3 || report_user == 4) {
                if ($('.edit_user_detail #company_id').val() == '') {
                    error = 1;
                    $('.edit_user_detail #company_error').html('This field is required');
                } else {
                    $('.edit_user_detail #company_error').html('');
                }
            } else if (report_user == 5) {
                if ($('.edit_user_detail #company_id').val() == '') {
                    error = 1;
                    $('.edit_user_detail #company_error').html('This field is required');
                } else {
                    $('.edit_user_detail #company_error').html('');
                }

                if ($('.edit_user_detail #plant_id').val() == '') {
                    error = 1;
                    $('.edit_user_detail #plant_error').html('This field is required');
                } else {
                    $('.edit_user_detail #plant_error').html('');
                }
            } else if (report_user == 6) {
                if ($('.edit_user_detail #plant_id').val() == '') {
                    error = 1;
                    $('.edit_user_detail #plant_error').html('This field is required');
                } else {
                    $('.edit_user_detail #plant_error').html('');
                }
            }

            console.log(error);
            if (error == 0) {
                return true;
            } else {
                event.preventDefault();
                return false;
            }*/

        }
    </script>


@endsection
