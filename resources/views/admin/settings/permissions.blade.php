@extends('layouts.admin.master')
@section('title', 'All User Permissions')
@section('content')
    <br>
    <div class="content">
        @include('alert')
        <div class="card">
            <div class="card-body">
                <h5><span class="fa fa-tools mr-2"></span>Quick Actions</h5>
                <a href="{{ url('admin/create-permission') }}" class="btn btn-success btn-sm ">Add Permission</a>
                <a href="{{ url('admin/roles-&-permissions') }}" class="btn btn-warning btn-sm ml-2">View Roles</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">All User Permissions</h4>
                        <br>
                        <table id="alternative-page-datatable" class="table dt-responsive nowrap">
                            <thead>
                            <tr>
                                <th>Permissions</th>
                                <th>Action(s)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <a href="{{ url('admin/edit-permission/'.$permission->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="{{ url('admin/delete-permission/'.$permission->id) }}" class="btn btn-sm btn-danger">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
        <!-- end row-->
    </div>
@endsection
@section('cssheader')
    <!-- third party css -->
    <link href="{{ asset('assets/admin/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/admin/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/admin/libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/libs/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css"/>
    <!-- third party css end -->
@endsection
@section('jsfooter')
    <!-- third party js -->
    <script src="{{ asset('assets/admin/libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/datatables/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/pages/datatables.init.js') }}"></script>
@endsection
