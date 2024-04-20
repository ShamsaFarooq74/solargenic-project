<!DOCTYPE html>

<html lang="{{ config('app.locale') }}">



<head>



    <meta charset="utf-8" />

    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Solar Monitoring System-Monitor Solar Power Efficiently" />
    <meta property="og:description" content="Best solar monitoring system software &amp; Remote PV monitoring system app easily to track your real-time system production &amp; household energy consumption." />
    <meta property="og:url" content="https://solargenic.net/" />
    <meta property="og:site_name" content="Solar Power Monitoring System Pakistan" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="Best solar monitoring system software &amp; Remote PV monitoring system app easily to track your real-time system production &amp; household energy consumption." />
    <meta name="twitter:title" content="Solar Monitoring System-Monitor Solar Power Efficiently" />
    <meta content="Viion Technology" name="author" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}">



    <!-- Plugins css -->


    <link href="{{ asset('assets/libs/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css" />



    <!-- App css -->

    <link href="{{ asset('assets/css/bootstrap-material.min.css')}}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />

    <link href="{{ asset('assets/css/app-material.min.css')}}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />



    <link href="{{ asset('assets/css/app-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled />




    <link href="{{ asset('assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />


    <link href="{{ asset('assets/libs/multiselect/css/multi-select.css')}}" rel="stylesheet" type="text/css" />
    <!-- Footable css -->


    <!-- icons -->

    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Summernote css -->
    <link href="{{ asset('assets/libs/summernote/summernote-bs4.css')}}" rel="stylesheet" type="text/css" />

    <!-- custom -->

    <link href="{{ asset('assets/css/custom-stylesheet.css?id=1')}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('assets/css/datepicker.css')}}">

    <!-- C3 Chart css -->




    <style type="text/css">
        #donut-chart .c3-chart-arcs-title {

            font-size: 30px !important;


        }

        .navbar {
            padding: 0 20px;
        }

        .nav-link {
            color: #fff;
        }

        .navbar-light .navbar-toggler {
            color: rgb(255 255 255);
            border-color: rgb(255 255 255);
        }
        .plane_tobtn_vt a{
            color: #fff;
            font-size: 13px;
            padding: 5px;
            margin: 20px 0px 0 20px;
            background: #68ad86;
            text-transform: capitalize;
            float: left;
            border-radius: 5px;
            font-weight: 500;
            min-width: 100px;
            text-align: center;
            font-family: "Roboto-Medium ,sans-serif";
        }
        .plane_tobtn_vt a:hover{
            color: #68ad86;
            background: #fff;
        }
        .plane_tobtn_vt a.active{
            color: #68ad86;
            background: #fff;
        }
        .go_vt{
            width: 50%;
            display: flex;
            color: #fff;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            padding: 5px;
            line-height: 36px;
            margin: 0 3%;
            background: #68ad86;
            text-transform: capitalize;
            float: left;
            border-radius: 5px;
            font-weight: 500;
            font-family: "Roboto-Medium ,sans-serif";

        }
        .model_area_body_vt{
            width: 100%;
            background-image: url('http://192.168.1.250/bel-hybrid/assets/images/050121143546.70017_6A.jpeg');
            min-height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.89;
        }

        .add_new_user_btn_vt{
            color: #fff;
            font-size: 13px;
            padding: 5px;
            margin: 20px 0px 0 20px;
            background: #68ad86;
            text-transform: capitalize;
            float: left;
            border-radius: 5px;
            font-weight: 500;
            min-width: 100px;
            text-align: center;
            font-family: "Roboto-Medium ,sans-serif";
            position: absolute;
            top: 0;
            left: -195px;
        }
        .breadcrumb-item>a {
            color: #fdfeff;
        }
        .breadcrumb_vt li {
            list-style: none;
            float: left;
            padding: 24px 15px 24px 0;
            color: #fff;
            position: relative;
        }




    </style>

</head>



<body data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "light", "size": "default", "showuser": false}, "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

    <div id="wrapper">
        <!-- Topbar Start -->
        <div class="navbar-custom">
            <div class="container-fluid">

{{--                @php--}}
{{--                    $plant_updated_at = DB::select("SELECT MAX(updated_at) AS updated_time FROM plants");--}}
{{--                @endphp--}}

                <ul class="list-unstyled topnav-menu float-right mb-0">
                    @if(Request::is('admin/dashboard') || Request::is('admin/Plants'))
                    <li class="ref_resh_vt">
                        <a href="{{ url('admin/dashboard') }}" style="margin:0 !important;">
                            <button name="refresh" type="button" class="btn-clear-ref-vt">
                                <img src="https://app.bel-energise.com/assets/images/refresh.png" alt="refresh">
                            </button>
                        </a>
                    </li>
                    @endif
                    <li class="dropdown notification-list topbar-dropdown">
                        @if(Request::is('admin/Plants/Data/bel') || Request::is('admin/Plants/Data/hybrid') || Request::is('admin/all-user') || Request::is('admin/all-company') || Request::is('admin/complain/priority/index') || Request::is('admin/complain/source/index') || Request::is('admin/complain/category/index') || Request::is('admin/complain/sub-category/index'))

                            @if(Request::is('admin/Plants/Data/bel') || Request::is('admin/Plants/Data/hybrid'))


                            @elseif(Request::is('admin/all-user'))

                                <button type="button" class="add_new_user_btn_vt " data-toggle="modal" data-target="#exampleModalScrollable">
                                    Add New
                                </button>

                            @elseif(Request::is('admin/all-company'))

                                <button type="button" class="add_new_user_btn_vt " data-toggle="modal" data-target="#exampleModalScrollable">
                                    Add New
                                </button>

                            @elseif(Request::is('admin/complain/priority/index'))

                                <button type="button" class="add_new_user_btn_vt " data-toggle="modal" data-target="#addPriority">
                                    Add New
                                </button>

                            @elseif(Request::is('admin/complain/source/index'))

                                <button type="button" class="add_new_user_btn_vt " data-toggle="modal" data-target="#addSource">
                                    Add New
                                </button>

                            @elseif(Request::is('admin/complain/source/index'))

                                <button type="button" class="add_new_user_btn_vt " data-toggle="modal" data-target="#addSource">
                                    Add New
                                </button>

                            @elseif(Request::is('admin/complain/category/index'))

                                <button type="button" class="add_new_user_btn_vt " data-toggle="modal" data-target="#addCategory">
                                    Add New
                                </button>

                            @elseif(Request::is('admin/complain/sub-category/index'))

                                <button type="button" class="add_new_user_btn_vt " data-toggle="modal" data-target="#addCategory">
                                    Add New
                                </button>

                            @endif
                        @endif
                        @if(Request::is('admin/Plants/Data/hybrid')|| Request::is('admin/Plants') || Request::is('admin/hybrid/user-plant-detail/*') )
                        <?php $plants = \App\Http\Models\Plant::where("system_type",4)->Select('updated_at')->latest()->first();
                        $time = date('H:i A', strtotime($plants->updated_at));
                        $date = date('d-m-Y', strtotime($plants->updated_at))

                        ?>
                        @else
                            <?php $plants = \App\Http\Models\Plant::Select('updated_at')->latest()->first();
                            $time = date('H:i A', strtotime($plants->updated_at));
                            $date = date('d-m-Y', strtotime($plants->updated_at))

                            ?>
                        @endif
                        <p>Updated at {{$time}}, {{$date}}</p>

                    </li>



                    <?php
//                    $notifications = DB::select("SELECT * FROM notification WHERE notification.sent_status = 'N' AND notification.user_id = " . Auth::user()->id);
                    $notifications =array();
                    ?>

                    <li class="dropdown notification-list topbar-dropdown">

                        <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">

                            <i class="fe-bell noti-icon"></i>

                            <span class="badge badge-danger rounded-circle noti-icon-badge">{{ $notifications ? count($notifications): 0 }}</span>

                        </a>

                        <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                            <!-- item-->

                            <div class="dropdown-item noti-title">

                                <h5 class="m-0">

                                    <span class="float-right">

                                        <a href="" class="text-dark">

                                            <small>Clear All</small>

                                        </a>

                                    </span>Notification

                                </h5>

                            </div>



                            <div class="noti-scroll" data-simplebar>



                                <!-- item-->

                                @if($notifications)

                                    @foreach($notifications as $notification)

                                        <a href="javascript:void(0);" class="dropdown-item notify-item active">

                                            <div class="notify-icon bg-info">

                                                <i class="mdi mdi-comment-account-outline"></i>

                                            </div>

                                            <p class="notify-details">{{ $notification->title }}</p>

                                            <p class="notify-details">{{ $notification->description }}

                                                <small class="text-muted">{{ date('d-m-Y H:i:s',strtotime($notification->entry_date)) }}</small>

                                            </p>

                                        </a>

                                    @endforeach

                                @endif

                            </div>




                        </div>

                    </li>

                    <li class="dropdown notification-list topbar-dropdown">

                        <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">

                            <img src="{{ Auth::user()->profile_pic ? asset('user_photo/'.Auth::user()->profile_pic) : asset('assets/images/users/profile.png')}}" alt="user-image" class="rounded-circle">

                            <span class="pro-user-name ml-1">

                                {{ Auth::user()->username }} <i class="mdi mdi-chevron-down"></i>

                            </span>

                        </a>

                        <div class="dropdown-menu dropdown-menu-right profile-dropdown ">

                            <!-- item-->

                            <div class="dropdown-header noti-title">

                                <h6 class="text-overflow m-0">Welcome !</h6>

                            </div>



                            <!-- item-->

                            <a href="{{ url('admin/myAccount/'.Auth::user()->id)}}" class="dropdown-item notify-item">

                                <i class="fe-user"></i>

                                <span>My Account</span>

                            </a>



                            <!-- item-->

                            <a href="javascript:void(0);" class="dropdown-item notify-item">

                                <i class="fe-settings"></i>

                                <span>Settings</span>

                            </a>



                            <!-- item-->

                            <a href="javascript:void(0);" class="dropdown-item notify-item">

                                <i class="fe-lock"></i>

                                <span>Lock Screen</span>

                            </a>



                            <div class="dropdown-divider"></div>

                            <a href="{{ url('/logout') }}" class="dropdown-item notify-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">

                                <i class="fe-log-out"></i>

                                <span>Logout</span>

                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">

                                @csrf

                            </form>



                        </div>

                    </li>



                </ul>
                <div class="logo-box">
                    <a href="index.html" class="logo text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/bel_logo.png')}}" alt="" width="50px">
                            <!-- <span class="logo-lg-text-light">UBold</span> -->
                        </span>
                        <span class="logo-sm">
                            <!-- <span class="logo-sm-text-dark">U</span> -->
                            <img src="{{ asset('assets/images/bel_logo.png')}}" alt=""  width="30px">
                        </span>
                    </a>
                </div>
                <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                    <li>
                        <button class="button-menu-mobile waves-effect waves-light">
                            <i class="fe-menu"></i>
                        </button>
                    </li>

                    <li>
                        <!-- Mobile menu toggle (Horizontal Layout)-->
                        <a class="navbar-toggle nav-link" data-toggle="collapse" data-target="#topnav-menu-content">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                        <!-- End mobile menu toggle-->
                    </li>

                    @if(Request::is('admin/Plants/Data/hybrid'))
                        <li class="plane_name_vt">
                            <h6>Hybrid Plants</h6>
                        </li>
                    @elseif(Request::is('admin/Plants/Data/bel'))
                        <li class="plane_name_vt">
                            <h6>On-Grid Plants</h6>
                        </li>
                    @endif
                    @if(Request::is('admin/dashboard') || Request::is('admin/user-dashboard'))
                    <li class="plane_name_vt">
                        <h6>On-Grid Dashboard</h6>
                    </li>
                    @elseif(Request::is('admin/complain/complain-mgm-system'))

                        <li class="plane_name_vt">

                            <h6>Ticket Dashboard</h6>

                        </li>
                    @elseif(Request::is('admin/bel/user-plant-detail/*') || Request::is('admin/hybrid/user-plant-detail/*'))

                        <?php
                        $plant_id_value = Request::segment(4);
//                        $plant_id_value = Session::get('plantHeaderID');
                        $plant = App\Http\Models\Plant::where('id', $plant_id_value)->first();
                        ?>

                        <li class="plane_name_vt">
                            <ul class="breadcrumb_vt">
                                @if(DB::table('plant_user')->where('user_id', Auth::user()->id)->count() > 1)
                                <li class="breadcrumb-item"><a href="{{route('admin.plants')}}">Plants</a></li>
                                @else
                                    <li class="breadcrumb-item"><a href="">Plants</a></li>
                                @endif
                                @if($plant->is_online == 'Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="check_pla_vt fas fa-check-circle" title="Online"></i></li>
                                @elseif($plant->is_online == 'P_Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="fas fa-exclamation-circle" style="color: #ffa61a;margin-left: 5px;" title="Partially Online"></i></li>
                                @else
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="cross_pla_vt fas fa-times-circle" title="Offline"></i></li>
                                @endif
                            </ul>
                        </li>
                    @elseif(Request::is('admin/bel/plant-inverter-detail/*') || Request::is('admin/hybrid/plant-inverter-detail/*') || Request::is('admin/Bel/plant-inverter-detail/*') || Request::is('admin/Hybrid/plant-inverter-detail/*'))

                        <?php

                        $plant_id_value = Session::get('plantHeaderID');
                        $plant = App\Http\Models\Plant::where('id', $plant_id_value)->first();
                        ?>

                        <li class="plane_name_vt">
                            <ul class="breadcrumb_vt">
                                <li class="breadcrumb-item"><a href="{{route('admin.plants')}}">Plants</a></li>
                                @if($plant->is_online == 'Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="check_pla_vt fas fa-check-circle" title="Online"></i></li>
                                @elseif($plant->is_online == 'P_Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="fas fa-exclamation-circle" style="color: #ffa61a;margin-left: 5px;" title="Partially Online"></i></li>
                                @else
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="cross_pla_vt fas fa-times-circle" title="Offline"></i></li>
                                @endif
                                <li class="breadcrumb-item">Inverters</li>

                            </ul>
                        </li>
                    @elseif(Request::is('admin/edit-plant/hybrid/*') || Request::is('admin/edit-plant/bel/*') )

                        <?php
                        $plant_id_value = Request::segment(4);
//                        $plant_id_value = Session::get('plantID');
                        $plant = App\Http\Models\Plant::where('id', $plant_id_value)->first();
                        ?>

                        <li class="plane_name_vt">
                            <ul class="breadcrumb_vt">
                                <li class="breadcrumb-item"><a href="{{route('admin.plants')}}">Edit Plant</a></li>
                                @if($plant->is_online == 'Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="check_pla_vt fas fa-check-circle" title="Online"></i></li>
                                @elseif($plant->is_online == 'P_Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="fas fa-exclamation-circle" style="color: #ffa61a;margin-left: 5px;" title="Partially Online"></i></li>
                                @else
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="cross_pla_vt fas fa-times-circle" title="Offline"></i></li>
                                @endif

                            </ul>
                        </li>
                    @elseif(Request::is('admin/complain/list-ticket'))

                        <li class="plane_name_vt">

                            <h6>Tickets List</h6>

                        </li>

                    @elseif(Request::is('admin/complain/add-ticket'))

                        <li class="plane_name_vt">

                            <h6>Add Ticket</h6>

                        </li>

                    @elseif(Request::is('admin/complain/priority/index'))

                        <li class="plane_name_vt">

                            <h6>Priorities List</h6>

                        </li>

                    @elseif(Request::is('admin/complain/source/index'))

                        <li class="plane_name_vt">

                            <h6>Sources List</h6>

                        </li>

                    @elseif(Request::is('admin/complain/category/index'))

                        <li class="plane_name_vt">

                            <h6>Categories List</h6>

                        </li>

                    @elseif(Request::is('admin/complain/sub-category/index'))

                        <li class="plane_name_vt">

                            <h6>Sub Categories List</h6>

                        </li>

                    @elseif(Request::is('admin/complain/view-edit-ticket/*'))

                        <li class="plane_name_vt">

                            <h6>View & Edit Ticket</h6>

                        </li>

                    @elseif(Request::is('admin/communication/index'))

                        <li class="plane_name_vt">

                            <h6>Communication</h6>

                        </li>


                    @elseif(Request::is('admin/all-company'))

                        <li class="plane_name_vt">

                            <h6>Companies List</h6>

                        </li>

                    @elseif(Request::is('admin/all-user'))

                        <li class="plane_name_vt">

                            <h6>Users List</h6>

                        </li>

                    @elseif(Request::is('admin/all-alerts'))

                        <li class="plane_name_vt">

                            <h6>Alert Center</h6>

                        </li>
                    @elseif( Request::segment(5)  == 'inverter')

                        <?php
                        $plantId = Request::segment(4);
//                        $plant_id_value = Session::get('plantHeaderID');
                        $plant = App\Http\Models\Plant::where('id', $plantId)->first();
                        ?>

                        <li class="plane_name_vt">
                            <ul class="breadcrumb_vt">
                                <li class="breadcrumb-item"><a href="{{route('admin.plants')}}">Plants</a></li>
                                @if($plant->is_online == 'Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="check_pla_vt fas fa-check-circle" title="Online"></i></li>
                                @elseif($plant->is_online == 'P_Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="fas fa-exclamation-circle" style="color: #ffa61a;margin-left: 5px;" title="Partially Online"></i></li>
                                @else
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="cross_pla_vt fas fa-times-circle" title="Offline"></i></li>
                                @endif
                                <li class="breadcrumb-item">Inverters</li>

                            </ul>
                        </li>

                    @elseif(Request::is('admin/plant-inverter-detail/*'))

                        <?php

                        $plant_id_value = Session::get('plantHeaderID');
                        $plant = App\Http\Models\Plant::where('id', $plant_id_value)->first();
                        ?>

                        <li class="plane_name_vt">
                            <ul class="breadcrumb_vt">
                                <li class="breadcrumb-item"><a href="{{route('admin.plants')}}">Plants</a></li>
                                @if($plant->is_online == 'Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="check_pla_vt fas fa-check-circle" title="Online"></i></li>
                                @elseif($plant->is_online == 'P_Y')
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="fas fa-exclamation-circle" style="color: #ffa61a;margin-left: 5px;" title="Partially Online"></i></li>
                                @else
                                    <li class="breadcrumb-item">{{$plant->plant_name}}<i class="cross_pla_vt fas fa-times-circle" title="Offline"></i></li>
                                @endif
                                <li class="breadcrumb-item">Inverters</li>

                            </ul>
                        </li>

                    @elseif(Request::is('admin/Plants'))

                        <li class="plane_name_vt">

                            <h6>Hybrid Dashboard</h6>

                        </li>

                    @endif


                </ul>

                <div class="clearfix"></div>
            </div>
        </div>
        <!-- end Topbar -->



