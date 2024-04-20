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


    <link href="{{ asset('assets/libs/jquery-nice-select/nice-select.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/switchery/switchery.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
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
    </style>

</head>



<body data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "light", "size": "default", "showuser": false}, "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

    <div id="wrapper">



        <!-- Topbar Start -->

        <div class="navbar-custom">

            <div class="container-fluid">



                <ul class="list-unstyled topnav-menu float-right mb-0">



                    <li class="d-none d-lg-block">

                        <form class="app-search">

                            <div class="app-search-box dropdown">

                                <div class="input-group">

                                    <input type="search" class="form-control" placeholder="Search Plants, devices" id="top-search">

                                </div>

                                <div class="dropdown-menu dropdown-lg" id="search-dropdown">

                                    <!-- item-->

                                    <div class="dropdown-header noti-title">

                                        <h5 class="text-overflow mb-2">Found 22 results</h5>

                                    </div>



                                    <!-- item-->

                                    <a href="javascript:void(0);" class="dropdown-item notify-item">

                                        <i class="fe-home mr-1"></i>

                                        <span>Analytics Report</span>

                                    </a>



                                    <!-- item-->

                                    <a href="javascript:void(0);" class="dropdown-item notify-item">

                                        <i class="fe-aperture mr-1"></i>

                                        <span>How can I help you?</span>

                                    </a>



                                    <!-- item-->

                                    <a href="javascript:void(0);" class="dropdown-item notify-item">

                                        <i class="fe-settings mr-1"></i>

                                        <span>User profile settings</span>

                                    </a>



                                    <!-- item-->

                                    <div class="dropdown-header noti-title">

                                        <h6 class="text-overflow mb-2 text-uppercase">Users</h6>

                                    </div>



                                    <div class="notification-list">

                                        <!-- item-->

                                        <a href="javascript:void(0);" class="dropdown-item notify-item">

                                            <div class="media">

                                                <img class="d-flex mr-2 rounded-circle" src="{{ asset('assets/images/users/user-2.jpg')}}" alt="Generic placeholder image" height="32">

                                                <div class="media-body">

                                                    <h5 class="m-0 font-14">Erwin E. Brown</h5>

                                                    <span class="font-12 mb-0">UI Designer</span>

                                                </div>

                                            </div>

                                        </a>



                                        <!-- item-->

                                        <a href="javascript:void(0);" class="dropdown-item notify-item">

                                            <div class="media">

                                                <img class="d-flex mr-2 rounded-circle" src="{{ asset('assets/images/users/user-5.jpg')}}" alt="Generic placeholder image" height="32">

                                                <div class="media-body">

                                                    <h5 class="m-0 font-14">Jacob Deo</h5>

                                                    <span class="font-12 mb-0">Developer</span>

                                                </div>

                                            </div>

                                        </a>

                                    </div>



                                </div>

                            </div>

                        </form>

                    </li>



                    <li class="dropdown d-inline-block d-lg-none">

                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">

                            <i class="fe-search noti-icon"></i>

                        </a>

                        <div class="dropdown-menu dropdown-lg dropdown-menu-right p-0">

                            <form class="p-3">

                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">

                            </form>

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

                                <i class="fe-arrow-right"></i>

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

                    @endif

                </ul>

                <div class="clearfix"></div>

            </div>

        </div>

        <!-- end Topbar -->