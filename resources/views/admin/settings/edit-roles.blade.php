@extends('layouts.admin.master')
@section('title', 'Edit Role')
@section('content')
    <br>
    <div class="content">
        @include('alert')
        <div class="card">
            <div class="card-body">
                <h5><span class="fa fa-tools mr-2"></span>QUICK ACTIONS</h5>
                <a href="{{ url('admin/roles-&-permissions') }}" class="btn btn-warning btn-sm ml-1">View Roles</a>
                <a href="{{ url('admin/permissions') }}" class="btn btn-primary btn-sm ml-1">View Permissions</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title">Edit Role</h4>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <form method="POST"
                              action="{{ url('/admin/update-role/'.$role->id) }}" class="needs-validation validateForm"
                              novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               placeholder="Enter Name" value="{{ $role->name }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label>Assign Permissions</label>
                                        @foreach ($permissions as $permission)
                                            <div class="checkbox checkbox-blue mb-2">
                                                <input id="checkbox{{ $permission->id }}" name="permissions[]"
                                                       type="checkbox" value="{{ $permission->id }}"
                                                {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                                                >
                                                <label for="checkbox{{ $permission->id }}">
                                                    {{ ucfirst($permission->name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update
                            </button>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>
            <!-- end col -->

        </div>
        <!-- end row -->
    </div>
@endsection
@section('cssheader')
@endsection
@section('jsfooter')
    <!-- Plugin js-->
@endsection
