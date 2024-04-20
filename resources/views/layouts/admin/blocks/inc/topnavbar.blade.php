<!-- navbar-->

    <header class="header">

        <nav class="navbar navbar-expand-lg px-4 py-2 head-bg-vt shadow">

            <a href="#" class="sidebar-toggler mr-1 mr-md-4 lead logo-vt">

                <img src="{{ asset('img/logo.png') }}" alt="logo">

                <i class="fas fa-bars"></i>

            </a>

            <ul class="ml-auto d-flex align-items-center list-unstyled mb-0">

                <li class="nav-item">

                    <form id="searchForm" class=" d-none d-md-block">

                        <div class="form-group position-relative mb-0">

                            <input type="email" placeholder="Search Plants, Devices" class="form-control">

                        </div>

                    </form>

                </li>

                <li class="nav-item dropdown notification-vt mr-0 mr-md-3">

                    <a id="notifications" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle bell-vt px-1 hvr-wobble-bottom">

                        <i class="far fa-bell"></i>

                        <span class="notification-icon"></span>

                    </a>

                    <div aria-labelledby="notifications" class="dropdown-menu">

                        <a href="#" class="dropdown-item">

                            <div class="d-flex align-items-center">

                                <div class="icon icon-sm bg-violet text-white"><i class="fab fa-twitter"></i></div>

                                <div class="text ml-2">

                                    <p class="mb-0">You have 2 followers</p>

                                </div>

                            </div>

                        </a>

                        <a href="#" class="dropdown-item">

                            <div class="d-flex align-items-center">

                                <div class="icon icon-sm bg-green text-white"><i class="fas fa-envelope"></i></div>

                                <div class="text ml-2">

                                    <p class="mb-0">You have 6 new messages</p>

                                </div>

                            </div>

                        </a>

                        <a href="#" class="dropdown-item">

                            <div class="d-flex align-items-center">

                                <div class="icon icon-sm bg-blue text-white"><i class="fas fa-upload"></i></div>

                                <div class="text ml-2">

                                    <p class="mb-0">Server rebooted</p>

                                </div>

                            </div>

                        </a>

                        <a href="#" class="dropdown-item">

                            <div class="d-flex align-items-center">

                                <div class="icon icon-sm bg-violet text-white"><i class="fab fa-twitter"></i></div>

                                <div class="text ml-2">

                                    <p class="mb-0">You have 2 followers</p>

                                </div>

                            </div>

                        </a>

                        <div class="dropdown-divider"></div><a href="#" class="dropdown-item text-center"><small class="font-weight-bold headings-font-family text-uppercase">View all notifications</small></a>

                    </div>

                </li>

                <li class="nav-item dropdown ml-auto">

                    <a id="userInfo" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle profile-vt">

                    @if(!empty(auth()->user()->profile_picture))

                        <img src="{{ asset(auth()->user()->profile_picture) }}" alt="user-image" style="max-width: 2.5rem;" class="img-fluid rounded-circle shadow">

                    @else

                        <img src="{{ asset('img/avatar-6.jpg') }}" alt="user-image" style="max-width: 2.5rem;" class="img-fluid rounded-circle shadow">

                    @endif



                    <!-- <img src="img/avatar-6.jpg" alt="Jason Doe" style="max-width: 2.5rem;" class="img-fluid rounded-circle shadow">  -->

                    @if(!empty(auth()->user()->username))

                        <span class="pro-user-name ml-1">

                            {{ auth()->user()->username }} <i class="fas fa-angle-down"></i>

                        </span>

                    @else

                        <span class="pro-user-name ml-1">

                            Admin <i class="fas fa-angle-down"></i>

                        </span>

                    @endif

                        <!-- Hamza <i class="fas fa-angle-down"></i> -->

                    </a>

                    <div aria-labelledby="userInfo" class="dropdown-menu">

                        @if(!empty(auth()->user()->username))

                            <a href="#" class="dropdown-item">

                                <strong class="d-block text-uppercase headings-font-family"> {{ auth()->user()->username }}</strong>

                                <!-- <small>Web Developer</small> -->

                            </a>

                        @endif



                        <a href="{{ url('/my-account') }}" class="dropdown-item">Settings</a>

                        <a href="#" class="dropdown-item">Activity log</a>

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

        </nav>

    </header>