<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="slimscroll-menu">

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul class="metismenu" id="side-menu">

                <li>
                    <a href="{{ url('/home') }}">
                        <i class="fe-home"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/all-plant') }}">
                        <i class="fe-server"></i>
                        <span> Plants </span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/all-inverter') }}">
                        <i class="fe-server"></i>
                        <span> Inverters </span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/all-user') }}">
                        <i class="fe-users"></i>
                        <span> User Managment </span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/all-company') }}">
                        <i class="fe-server"></i>
                        <span> Companies </span>
                    </a>
                </li>
                <li>
                    <a href="javascript: void(0);">
                        <i class="fe-settings"></i>
                        <span> Settings </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ url('admin/roles-&-permissions') }}">Roles & Permissions</a>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
