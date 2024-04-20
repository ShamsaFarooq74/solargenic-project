<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">
    <div class="h-100" data-simplebar>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul id="side-menu">
                <?php
                $currentUrl = \Request::route()->getName();
                if($currentUrl == 'admin.plants')
                {
                    $type = 'hybrid';
                }
                else
                {
                    $type = 'bel';
                }
                $value = Session::get('dashboardtype');
                ?>
                @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4)
                    <li class="menuitem-active">
                        @if($value == "bel")
                             <a href="{{ url('/home') }}" class="sidebar-link text-muted {{ Request::is('admin/dashboard') || Request::is('admin/Plants') || \Request::route()->getName() === 'user.dashboard' ? 'active' : ''}}"> <img src="{{ asset('assets/images/dashboard.png') }}" alt="dashboard" /> &nbsp;&nbsp;&nbsp;<span> Dashboard</span> </a>
                        @else
                            <a href="{{ url('admin/Plants') }}" class="sidebar-link text-muted {{ Request::is('admin/dashboard') || Request::is('admin/Plants') || \Request::route()->getName() === 'user.dashboard' ? 'active' : ''}}"> <img src="{{ asset('assets/images/dashboard.png') }}" alt="dashboard" /> &nbsp;&nbsp;&nbsp;<span> Dashboard</span> </a>
                        @endif
                    </li>
                @endif
                    <?php

                    $noOfPlants = DB::table('plant_user')->where('user_id', Auth::user()->id)->count();
                    ?>
                @if(Auth::user()->roles == 5 && $noOfPlants != 1)

                    <li class="menuitem-active">
                        <a href="{{ url('admin/user-dashboard') }}" class="sidebar-link text-muted {{ Request::is('admin/user-dashboard')}}"> <img src="{{ asset('assets/images/dashboard.png') }}" alt="dashboard" /> &nbsp;&nbsp;&nbsp;<span> Dashboard</span> </a>
                    </li>
                @endif
                <li>
                    <a

                    @if(Auth::user()->roles == 6)
                    $type = 'bel';
                    href="{{ url('admin/Plants/Data/'.$type) }}"
                    class="sidebar-link text-muted {{Request::segment(2) === 'plant-detail' ? 'active' : ''}}">
                    <img src="{{ asset('assets/images/plant.png') }}" alt="Plants" /> &nbsp;&nbsp;&nbsp;<span>Plants</span>
                    @elseif(Auth::user()->roles == 5 && $noOfPlants == 1)
                            <?php
                            $plantId = DB::table('plant_user')->where('user_id', Auth::user()->id)->first();
                            $plantMeterType = DB::table('plants')->select('meter_type')->where('id', $plantId->plant_id)->first();
                            if($plantMeterType->meter_type == 'solis')
                            {
                                $typePlant = 'hybrid';
                            }
                            else
                            {
                                $typePlant = 'bel';
                            }
                            ?>
                            href="{{ url('admin/'.$typePlant.'/user-plant-detail/'.$plantId->plant_id) }}"
                                class="sidebar-link text-muted {{Request::segment(3) === 'user-plant-detail' ? 'active' : ''}}">
                                <img src="{{ asset('assets/images/plant.png') }}" alt="Plants" /> &nbsp;&nbsp;&nbsp;<span>Plants</span>
                        @else
                        href="{{ url('admin/Plants/Data/'.$value) }}"
                        class="sidebar-link text-muted {{ Request::is('home') || Request::is('admin/plant-ex') || Request::is('admin/'.$type.'/user-plant-detail/*')  || Request::is('admin/plant-inverter-detail/*') || Request::segment(2) === 'plant-detail' ? 'active' : ''}}">

                        @if(Request::is('admin/dashboard') || Request::is('admin/Plants/Data/bel') || $value == "bel" )
                            <img src="{{ asset('assets/images/plant.png') }}" alt="Plants" /> &nbsp;&nbsp;&nbsp;<span>OnGrid Plants</span>
                        @elseif(Request::is('admin/Plants') || Request::is('admin/Plants/Data/hybrid') || Request::is('admin/hybrid/user-plant-detail/*') || $value == "hybrid")
                            <img src="{{ asset('assets/images/plant.png') }}" alt="Plants" /> &nbsp;&nbsp;&nbsp;<span>Hybrid Plants</span>
                        @else
                            <img src="{{ asset('assets/images/plant.png') }}" alt="Plants" /> &nbsp;&nbsp;&nbsp;<span>OnGrid Plants</span>
                        @endif
                    @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.all.alerts') }}" class="sidebar-link text-muted {{ Request::is('admin/all-alerts') ? 'active' : ''}}">
                        <img src="{{ asset('assets/images/alert.png') }}" alt="Alert" /> &nbsp;&nbsp;&nbsp;<span>Alert Center</span>
                    </a>
                </li>

                @if(Auth::user()->roles == 1 || Auth::user()->roles == 3)
                    <li>
                        <a href="{{ route('admin.user.all') }}" class="sidebar-link text-muted {{ Request::is('admin/all-user') ? 'active' : ''}}">
                            <img src="{{ asset('assets/images/user.png') }}" alt="Management" /> &nbsp;&nbsp;&nbsp;<span>User Management</span>
                        </a>
                    </li>
                @endif
                @if(Auth::user()->roles == 1)
                    <li>
                        <a href="{{route('admin.company.all')}}" class="sidebar-link text-muted {{ Request::is('admin/all-company') ? 'active' : ''}}">
                            <img src="{{ asset('assets/images/comapnies.png') }}" alt="Companies" /> &nbsp;&nbsp;&nbsp;<span>Companies</span>
                        </a>
                    </li>
                @endif
                @if(Auth::user()->roles == 1)
                    <li>
                        <a href="{{route('admin.communication.index')}}" class="sidebar-link text-muted {{ Request::is('admin/communication') ? 'active' : ''}}">
                            <img src="{{ asset('assets/images/communication.png') }}" alt="Report" /> &nbsp;&nbsp;&nbsp;<span>Communication</span>
                        </a>
                    </li>
                @endif
                <li class="dropdown drop_vt">
                    <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="true">
                        <img src="{{ asset('assets/images/Complaints.png') }}" alt="Complaints" /> &nbsp;&nbsp;&nbsp;<span>Complaints</span><span class="menu-arrow"></span>
                    </a>
                    <div class="dropdown-menu">
                        <!-- item-->
                        @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4)
                            <a href="{{route('admin.complain.mgm.system')}}" class="dropdown-item ">
                                <span class="pl-4">Dashboard</span>
                            </a>
                        @endif
                    <!-- item-->
                        <a href="{{route('admin.ticket.list')}}" class="dropdown-item">
                            <span class="pl-4">Ticket List</span>
                        </a>
                        @if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4 || Auth::user()->roles == 5)
                            <a href="{{route('admin.ticket.add')}}" class="dropdown-item">
                                <span class="pl-4">Add Ticket</span>
                            </a>
                        @endif
                        @if(Auth::user()->roles == 1)
                        <!-- item-->
                            <a href="{{route('admin.complain.priority.index')}}" class="dropdown-item">
                                <span class="pl-4">Priority</span>
                            </a>
                            <!-- item-->
                            <a href="{{route('admin.complain.source.index')}}" class="dropdown-item">
                                <span class="pl-4">Source</span>
                            </a>
                            <!-- item-->
                            <a href="{{route('admin.complain.category.index')}}" class="dropdown-item">
                                <span class="pl-4">Category</span>
                            </a>
                            <!-- item-->
                            <a href="{{route('admin.complain.sub-category.index')}}" class="dropdown-item">
                                <span class="pl-4">Sub-Category</span>
                            </a>
                        @endif
                    </div>
                </li>
                <li class="dropdown drop_vt">
                    <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="true">
                        <img src="{{ asset('assets/images/Complaints.png') }}" alt="Complaints" /> &nbsp;&nbsp;&nbsp;<span>Reporting Center</span><span class="menu-arrow"></span>
                    </a>
                    <div class="dropdown-menu">
                        <!-- item-->
                        <a href="{{route('admin.dashboard.reporting.center')}}" class="dropdown-item ">
                            <span class="pl-4">Engergy Anlytical Report</span>
                        </a>
                        <a href="{{route('admin.dashboard.reporting.center')}}" class="dropdown-item ">
                            <span class="pl-4">My Electricity Bill</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
        <!-- End Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->
