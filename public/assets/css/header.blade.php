<!DOCTYPE html>

<html lang="{{ config('app.locale') }}">



<head>



    <meta charset="utf-8" />

    <title>@yield('title')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />

    <meta content="Bel Energise" name="description" />

    <meta content="Viion Technology" name="author" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}">



    <!-- Plugins css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />

    <link href="{{ asset('assets/libs/mohithg-switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/selectize/css/selectize.bootstrap3.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/ladda/ladda-themeless.min.css')}}" rel="stylesheet" type="text/css" />


    <!-- App css -->

    <link href="{{ asset('assets/css/bootstrap-material.min.css')}}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />

    <link href="{{ asset('assets/css/app-material.min.css')}}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />



    <link href="{{ asset('assets/css/bootstrap-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled />

    <link href="{{ asset('assets/css/app-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled />



    <link href="{{ asset('assets/libs/mohithg-switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />


    <!-- Footable css -->
    <link href="{{ asset('assets/libs/footable/footable.core.min.css')}}" rel="stylesheet" type="text/css" />


    <!-- icons -->

    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Summernote css -->
    <link href="{{ asset('assets/libs/summernote/summernote-bs4.css')}}" rel="stylesheet" type="text/css" />

    <!-- custom -->

    <link href="{{ asset('assets/css/custom-stylesheet.css')}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('assets/css/datepicker.css')}}">

    <!-- C3 Chart css -->

    <link href="{{ asset('assets/libs/c3/c3.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.min.css" rel="stylesheet" type="text/css">


    <style type="text/css">
        #donut-chart .c3-chart-arcs-title {

            font-size: 30px !important;

        }

        .top_head_timedate {
            width: auto;
            float: right;
            padding: 17px 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .top_head_timedate p {
            color: #fff;
            margin: 0;
            margin-left: 15px;
        }

        .top_head_timedate a {
            background: #fff;
            border: none;
            color: #073c6e;
            display: block;
            padding: 6px 15px;
            margin: 0 11px 0 0;
            border-radius: 5px;
        }

        .top_head_timedate button {
            background: none;
            border: none;
        }
    </style>

</head>



<body data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "light", "size": "default", "showuser": false}, "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

    <div id="wrapper">



        <!-- Topbar Start -->

        <div class="navbar-custom">

            <div class="container-fluid">



                <ul class="list-unstyled topnav-menu float-right mb-0">

                    <li class="dropdown">
                        <div class="top_head_timedate">
                            <a href="#">Add New</a>
                            <button name="refresh" type="button" class="btn-clear-ref-vt">
                                <img src="{{ asset('assets/images/refresh.png')}}" alt="refresh">
                            </button>
                            <p><span>Updated at </span> 02:32 PM, 23-12-2020</p>
                        </div>

                    </li>



                    <?php $notifications = DB::select("SELECT * FROM notification WHERE notification.sent_status = 'N' AND notification.user_id = " . Auth::user()->id);

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



                            <!-- All-->

                            <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">

                                View all

                                <i class="
                                
                                fe-arrow-right"></i>

                            </a>



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

                            <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">

                                <i class="fe-settings"></i>

                                <span>Settings</span>

                            </a> -->



                            <!-- item-->

                            <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">

                                <i class="fe-lock"></i>

                                <span>Lock Screen</span>

                            </a> -->



                            <div class="dropdown-divider"></div>



                            <!-- item-->

                            <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">

                                    <i class="fe-log-out"></i>

                                    <span>Logout</span>

                                </a> -->

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



                <!-- LOGO -->

                <div class="logo-box">

                    <a href="index.html" class="logo logo-dark text-center">

                        <span class="logo-sm">

                            <img src="{{ asset('assets/images/logo-sm.png')}}" alt="" height="22">

                        </span>

                        <span class="logo-lg">

                            <img src="{{ asset('assets/images/logo-dark.png')}}" alt="" height="20">

                        </span>

                    </a>



                    <a href="" class="logo logo-light text-center">

                        <span class="logo-sm">

                            <img src="{{ asset('assets/images/bel_logo.png')}}" alt="" width="30px">

                        </span>

                        <span class="logo-lg">

                            <img src="{{ asset('assets/images/bel_logo.png')}}" alt="" width="50px">

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

                        <a class="navbar-toggle nav-link" data-toggle="collapse" data-target="#topnav-menu-content">

                            <div class="lines">

                                <span></span>

                                <span></span>

                                <span></span>

                            </div>

                        </a>

                    </li>

                    @if(Request::is('admin/user-plant-detail/*'))

                    <li class="plane_name_vt">

                        <h6>{{ Session::get('plant_name') }}</h6>

                    </li>

                    @elseif(Request::is('admin/dashboard'))

                    <li class="plane_name_vt">

                        <h6>Dashboard</h6>

                    </li>

                    @endif


                </ul>

                <div class="clearfix"></div>

            </div>

        </div>

        <!-- end Topbar -->