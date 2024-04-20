@extends('layouts.admin.master')
@section('title', 'Plant Profile')
@section('content')
    <div class="container-fluid px-xl-5">
        <section class="py-2">
            <div class="row">
                <div class="col-12">
                    <div class="report-head-vt">
                        <h4>Plant Profile</h4>
                        <!-- <button type="button" class="plant-profile-btn-vt" data-toggle="modal" data-target="#exampleModalScrollable">
                            Edit Plant
                          </button> -->
                        @if(Auth::user()->roles == 1 || Auth::user()->roles == 3)
                            <a href="{{ url('admin/edit-plant/'.$plant['id'])}}">
                                <button type="button" class="btn-add-vt plant-profile-btn-vt">
                                    Edit Plant
                                </button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="col-lg-12 mb-4">
                   <div class="plant-profile-vt" style="background-image: url({{ $plant ? asset('plant_photo/'.$plant['plant_pic']) : asset('assets/images/plant-profile.jpg') }});">   
                        <img src="{{ $plant ? $plant->company ? asset('company_logo/'.$plant->company->logo): asset('assets/images/profile-img.jpg') : asset('assets/images/profile-img.jpg') }}" alt="profile">   
                    </div>  
                </div>  
                <div class="col-lg-12 mb-4">
                    <div class="plant-profile-detail-vt">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Plant Name</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['plant_name'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Plant Type </p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['plant_type'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>System Type </p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['system_type'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Designed Capacity</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['capacity'] : '' }} kWh</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Time Zone</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['timezone'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Contact Number</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['phone'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Benchmark Price</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['benchmark_price'] : '' }} PKR</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Angle</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['angle'] : ''}} &#176</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Azimuth</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['azimuth'] : '' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Latitude</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['loc_lat'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Longitude</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['loc_long'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Address</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant['location'] : '' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 alerts-head-text-vt">
                                        <p>Daily Expected Generation</p>
                                    </div>
                                    <div class="col-md-8 alerts-detail-text-vt">
                                        <p>{{ $plant ? $plant->expected_generation_log->where('plant_id',$plant->id)->last()->daily_expected_generation : '' }} kWp</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            @if(Auth::user()->roles == 1 || Auth::user()->roles == 3)
            <div class="row">
                <div class="col-12">
                    <h4 class="text-secd-vt">Plant Users</h4>
                </div>
                @if(count($users) > 0)
                    @foreach($users as $single_plant_user)
                        <div class="col-md-6 col-lg-3 col-xl-3">
                            <div class="card-company-vt">
                                <div class="dropdown float-right">
                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                    @if($single_plant_user->is_active == 'Y')
                                        <a class="dropdown-item"href="{{ url('admin/blockUser/'.$single_plant_user->id)}}"><i class="mdi mdi-alert-circle-outline text-warning"></i>&nbsp;&nbsp;Block</a>
                                    @else
                                        <a class="dropdown-item" href="{{ url('admin/unblockUser/'.$single_plant_user->id)}}"><i class="mdi mdi-alert-circle-outline text-warning"></i>&nbsp;&nbsp;Un-block</a>
                                    @endif
                                    <button type="button" class="dropdown-item viewuser" data-toggle="modal" data-target="#view-user-vt" data-id="{{ $single_plant_user->id }}" data-user_detail="{{ $single_plant_user }}" data-company_name="{{ $single_plant_user->company['company_name'] }}"data-plant_name="{{ $single_plant_user->pivot->pivotParent->plant_name }}"><i class="mdi mdi-eye text-info"></i>&nbsp;&nbsp;View</button>

                                    <button type="button" class="dropdown-item edituser" data-toggle="modal" data-target="#edit-user-vt" data-id="{{ $single_plant_user->id }}" data-user_detail="{{ $single_plant_user }}" data-plant_id="{{ $single_plant_user->pivot->pivotParent->id }}"><i class="mdi mdi-pencil text-primary"></i>&nbsp;&nbsp;Edit</button>

                                    <button type="button" class="dropdown-item deleteuser" data-toggle="modal" data-target="#delete-user-vt" data-id="{{ $single_plant_user->id }}"><i class="mdi mdi-trash-can text-danger"></i>
                                        &nbsp;&nbsp;Delete
                                    </button>
                                </div>
                                </div>
                                <img src="@if($single_plant_user->profile_pic) {{ asset('user_photo/'.$single_plant_user->profile_pic) }} @else {{ asset('user_photo/profile-picture.png') }} @endif" alt="company-logo" style="height: 65px;width: 65px;">
                                <h3>{{ $single_plant_user->username }}</h3>
                                <p>{{ $single_plant_user->email }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-6 col-lg-3 col-xl-3">
                        User not found.
                    </div>
                @endif
            </div>
            @endif
        </section>
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
    
    <!-- Modal Edit -->
    <div class="modal fade edit_user_detail" id="edit-user-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Edit User</h5>
                </div>
                <div class="modal-body">
                    <form class="parsley-examples" method="POST" action="{{ url('admin/update-user') }}" enctype="multipart/form-data">
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
                            <input type="text" placeholder="Name" id="name" name="name" class="form-control" required="">
                            <input type="hidden" id="user_id" name="user_id" value="">
                        </div>
                        <div class="form-group">
                            <label class="form-control-label">Email<span class="text-danger">*&nbsp;&nbsp;<span id="email_error"></span></span></label>
                            <input type="email" placeholder="Email" id="email" name="email" class="form-control" required="" readonly="">
                        </div>
                        <div class="form-group">
                            <label class="form-control-label">Username<span class="text-danger">*&nbsp;&nbsp;<span id="username_error"></span></span></label>
                            <input type="text" placeholder="Username" id="username" name="username" class="form-control" required="" readonly="">
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">User Type<span class="text-danger">*&nbsp;&nbsp;<span id="user_type_error"></span></span></label>
                            <select class="form-control report_user" id="user_type" name="user_type" required="">
                                <option value="">Report Type</option>
                                @if($roles)
                                    @foreach($roles as $key => $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group all_company">
                            <label for="exampleFormControlSelect1">Company<span class="text-danger">*&nbsp;&nbsp;<span id="company_error"></span></span></label>
                            <select class="form-control" id="company_id" name="company_id">
                                <option value="">Select Company</option>
                                @if($companies)
                                    @foreach($companies as $key => $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group all_plants">
                            <label for="exampleFormControlSelect1">Plant<span class="text-danger">*&nbsp;&nbsp;<span id="plant_error"></span></span></label>
                            <select class="form-control" id="plant_id" name="plant_id">
                                <option value="">Select Plant</option>
                                @if($plants)
                                @foreach($plants as $key => $plant)
                                <option value="{{ $plant->id }}">{{ $plant->plant_name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <button type="submit" class="btn-create-vt">Update</button>
                        <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade deletuser_modal" id="delete-user-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="deleteform" method="POST" action="{{ url('/admin/delete-user') }}" class="py-3">
                        @csrf
                        <i class="fas fa-exclamation"></i>
                        <input type="hidden" name="id" id="user_id" value="">
                        <h4 class="model-heading-vt">Are you sure to delete <br>this user ?</h4>
                        <button type="submit" class="btn-create-vt">Yes, Delete</button>
                        <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    
    function submit_user(){
        console.log($('.report_user').val());
        var error = 0;
        if($('#name').val() == ''){
            error = 1;
            $('#name_error').html('This field is required'); 
        }else{
            $('#name_error').html('');
        }

        if($('#email').val() == ''){
            error = 1;
            $('#email_error').html('This field is required'); 
        }else{
            $('#email_error').html('');
        }

        if($('#username').val() == ''){
            error = 1;
            $('#username_error').html('This field is required'); 
        }else{
            $('#username_error').html('');
        }

        if($('#password').val() == ''){
            error = 1;
            $('#password_error').html('This field is required'); 
        }else{
            $('#password_error').html('');
        }

        if($('#confirm_password').val() == ''){
            error = 1;
            $('#confirm_password_error').html('This field is required'); 
        }else{
            $('#confirm_password_error').html('');
        }

        if($('#user_type').val() == ''){
            error = 1;
            $('#user_type_error').html('This field is required'); 
        }else{
            $('#user_type_error').html('');
        }

        if($('.report_user').val() == 3 || $('.report_user').val() == 4){
            if($('#company_id').val() == ''){
                error = 1;
                $('#company_error').html('This field is required'); 
            }else{
                $('#company_error').html('');
            }
        }else if($('.report_user').val() == 5){
            if($('#company_id').val() == ''){
                error = 1;
                $('#company_error').html('This field is required'); 
            }else{
                $('#company_error').html('');
            }

            if($('#plant_id').val() == ''){
                error = 1;
                $('#plant_error').html('This field is required'); 
            }else{
                $('#plant_error').html('');
            }
        }else if($('.report_user').val() == 6){
            if($('#plant_id').val() == ''){
                error = 1;
                $('#plant_error').html('This field is required'); 
            }else{
                $('#plant_error').html('');
            }
        }

        if(error == 0)
        {
            return true;
        }else{
            event.preventDefault();
            return false;
        }
        
    }    
</script>         
@endsection
