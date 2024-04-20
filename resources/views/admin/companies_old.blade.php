@extends('layouts.admin.master')
@section('title', 'All Companies')
@section('content')
    <br>
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">            
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-lg-8">
                                <form class="form-inline">
                                    <div class="form-group mx-sm-2">
                                        <select class="custom-select" id="status-select">
                                            <option selected="">Status</option>
                                            <option value="1">Name</option>
                                            <option value="2">Post</option>
                                            <option value="3">Followers</option>
                                            <option value="4">Followings</option>
                                        </select>
                                    </div>
                                    <div class="form-group mx-sm-2">
                                        <select class="custom-select" id="status-select">
                                            <option selected="">Region</option>
                                            <option value="1">Name</option>
                                            <option value="2">Post</option>
                                            <option value="3">Followers</option>
                                            <option value="4">Followings</option>
                                        </select>
                                    </div>
                                    <div class="form-group mx-sm-2">
                                        <select class="custom-select" id="status-select">
                                            <option selected="">City</option>
                                            <option value="1">Name</option>
                                            <option value="2">Post</option>
                                            <option value="3">Followers</option>
                                            <option value="4">Followings</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-lg-right mt-3 mt-lg-0">
                                    <button type="button" class="btn btn-warning waves-effect waves-light mr-1">Clear</button>
                                    <a href="#custom-modal" class="btn btn-primary waves-effect waves-light" data-animation="fadein" data-plugin="custommodal" data-overlayColor="#38414a"><i class="mdi mdi-plus-circle mr-1"></i> Add New</a>
                                </div>
                            </div><!-- end col-->
                        </div> <!-- end row -->
                    </div> <!-- end card-box -->
                </div><!-- end col-->
            </div>
            <!-- end row -->        
            @include('alert')
            <div class="row">
                @if($companies) 
                    @foreach($companies as $key => $company)
                        <div class="col-lg-3">
                            <div class="text-center card-box">
                                <div class="pt-2 pb-2">
                                    <img src="{{ asset('company_logo/'.$company->logo) }}" class="img-thumbnail" alt="company-logo">
                                    <h4 class="mt-3">{{ $company->company_name }}</h4>
                                    <p class="text-muted">{{ $company->email }}</p>              
                                </div> <!-- end .padding -->
                            </div> <!-- end card-box-->
                        </div> <!-- end col -->                
                    @endforeach
                @endif
            </div>
            
        </div> <!-- container -->

        <!-- Modal -->
        <div id="custom-modal" class="modal-demo">
            <button type="button" class="close" onclick="Custombox.modal.close();">
                <span>&times;</span><span class="sr-only">Close</span>
            </button>
            <h4 class="custom-modal-title">Add Company</h4>
            <div class="custom-modal-text text-left">
                <form method="POST" action="{{ url('/admin/add-company') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="company">Add Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo">
                    </div>
                    <div class="form-group">
                        <label for="name">Company Name*</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Company Name">
                    </div>
                    <div class="form-group">
                        <label for="position">Contact Number*</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Enter Contact Number">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email*</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                        <button type="button" class="btn btn-danger waves-effect waves-light m-l-10" onclick="Custombox.close();">Cancel</button>
                    </div>
                </form>
            </div>
        </div> 
    </div>
@endsection
@section('cssheader')
    <!-- Custom box css -->
        <link href="{{ asset('assets/admin/libs/custombox/custombox.min.css')}}" rel="stylesheet">
@endsection
@section('jsfooter')
    <!-- third party js -->
    <script src="{{ asset('assets/admin/libs/custombox/custombox.min.js') }}"></script> 
@endsection
