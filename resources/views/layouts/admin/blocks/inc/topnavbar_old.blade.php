<!-- Topbar Start -->
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#"
               role="button" aria-haspopup="false" aria-expanded="false">
                @if(!empty(auth()->user()->profile_picture))
                    <img src="{{ asset(auth()->user()->profile_picture) }}" alt="user-image" class="rounded-circle">
                @else
                    <img src="{{ asset('assets/admin/images/users/user-4.jpg') }}" alt="user-image"
                         class="rounded-circle">
                @endif 
                @if(!empty(auth()->user()->username))
                    <span class="pro-user-name ml-1">
                                {{ auth()->user()->name }} <i class="mdi mdi-chevron-down"></i>
                            </span>
                @else
                    <span class="pro-user-name ml-1">
                                Admin <i class="mdi mdi-chevron-down"></i>
                            </span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Welcome {{ !empty(auth()->user()->name)?auth()->user()->name:'Admin' }}!</h6>
                </div>

                <!-- item-->
                <a href="{{ url('/my-account') }}" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>My Account</span>
                </a>

                <div class="dropdown-divider"></div>

                <!-- item-->
                <a href="{{ url('/logout') }}" class="dropdown-item notify-item" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
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
        <a href="{{ url('/') }}" class="logo text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets/admin/images/logo.png') }}" alt="" height="60" width="100">
                            <!-- <span class="logo-lg-text-light">UBold</span> -->
                        </span>
            <span class="logo-sm">
                            <!-- <span class="logo-sm-text-dark">U</span> -->
                            <img src="{{ asset('assets/admin/images/favicon.png') }}" alt="" height="45">
                        </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
            <button class="button-menu-mobile waves-effect waves-light">
                <i class="fe-menu"></i>
            </button>
        </li>
    </ul>
</div>
<!-- end Topbar -->
