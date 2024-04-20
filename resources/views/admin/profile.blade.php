@extends('layouts.admin.master')
@section('title', 'My Account')
@section('content')
<div class="container-fluid px-xl-5">
    <section class="py-2">
        <div class="row">
            <div class="col-12 mb-1">
                <div class="report-head-vt">
                    <h4>My Account</h4>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    @include('alert')
                    <form class="parsley-examples" method="post" action="{{ url('admin/update-profile') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <div class="img_log_them_vt">
                                            <img src="{{ $user && $user->profile_pic ? asset('user_photo/'.$user->profile_pic) : asset('assets/images/users/profile.png')}}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label>Picture</label>
                                            <div class="file-upload">
                                                <div class="file-select">
                                                    <div class="file-select-button" id="fileName">Choose File</div>
                                                    <div class="file-select-name" id="noFile">No file chosen...</div>
                                                    <input type="file" name="user_photo" id="chooseFile">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" placeholder="Enter Name" value="{{ $user && $user->name ? $user->name : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter Email" value="{{ $user && $user->email ? $user->email : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" name="username" placeholder="Enter Username" value="{{ $user && $user->username ? $user->username : '' }}"/>
                                </div>
                            </div>
                            @if($user && $user->company)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company Name</label>
                                    <input type="text" class="form-control" placeholder="Company Name" value="{{ $user && $user->company ? $user->company->company_name : '' }}" readonly="" />
                                </div>
                            </div>
                            @endif
                            <?php if($user && $user->plants){
                                $plant_name = array();
                                foreach ($user->plants as $plant) {
                                    $plant_name[] = $plant->plant_name;
                                }
                                $plant_name = isset($plant_name) && !empty($plant_name) ? implode(' , ',$plant_name) : 0;
                            }?>
                            @if($plant_name)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Plant Name(s)</label>
                                    <input type="text" class="form-control" placeholder="Plant Name" value="{{ $user && $plant_name ? $plant_name : '' }}" readonly="" />
                                </div>
                            </div>
                            @endif
                            <input type="hidden" name="user_id" value="{{ $user && $user->id ? $user->id : '' }}">
                        </div>
                        <div class="form-group mb-0">
                            <div>
                                <button type="submit" class="btn-create-vt">
                                    Update Profile
                                </button>
                                <button type="reset" class="btn-close-vt">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-box">
                    <form class="parsley-examples" method="post" action="{{ url('admin/update-password') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Enter Current Password" required="" />
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter New Password" required="" />
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter Confirm Password"  required="" />
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="checkbox mb-2">
                                    <input id="checkbox0" type="checkbox" onclick="showPass()">
                                    <label for="checkbox0">
                                        Show Password
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="user_id" value="{{ $user && $user->id ? $user->id : '' }}">
                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="form-group mb-0">
                            <div>
                                <button type="submit" class="btn-create-vt" style="width: 150px;">
                                    Update Password
                                </button>
                                <button type="reset" class="btn-close-vt">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    function showPass() {
        var curpass = document.getElementById("current_password");
        var newpass = document.getElementById("new_password");
        var conpass = document.getElementById("confirm_password");
        if (curpass.type === "password" && newpass.type === "password" && conpass.type === "password") {
            curpass.type = "text";
            newpass.type = "text";
            conpass.type = "text";
        } else {
            curpass.type = "password";
            newpass.type = "password";
            conpass.type = "password";
        } 
    }
</script>
@endsection