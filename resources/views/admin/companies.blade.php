@extends('layouts.admin.master')
@section('title', 'All Companies')
@section('content')
<div class="content">

    <div class="bred_area_vt">
        <div class="row">
            <div class="col-xl-12">
                <div class="home-companies-area-vt">
                    <form id="filtersForm" class="home-companise_dash-vt" action="{{route('admin.company.all')}}" method="GET">
                        <?php
                            $filter = Session::get('filter');
                        ?>
                        <div class="form-group">
                            <select class="form-control" name="company" id="company">
                                <option value="all">Company Name</option>
                                @if(isset($filter_data['company_array']) && $filter_data['company_array'])
                                @foreach($filter_data['company_array'] as $company_data)
                                <option value="{{ $company_data->id }}" <?php echo isset($filter['company']) && $filter['company'] == $company_data->id  ? 'selected' : '' ?>>{{ $company_data->company_name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group" style="min-width: 90px;">
                            <select class="form-control select2-multiple" name="plant_name[]" id="plant_name" data-toggle="select2" multiple>
                                @if(isset($filter_data['plants']) && $filter_data['plants'])
                                @foreach($filter_data['plants'] as $plant)
                                <option value="{{ $plant->id }}" <?php echo isset($filter['plant_name']) && in_array($plant->id, $filter['plant_name'])  ? 'selected' : '' ?>>{{ $plant->plant_name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="user" id="user">
                                <option value="all">Users</option>
                                @if(isset($filter_data['user_array']) && $filter_data['user_array'])
                                @foreach($filter_data['user_array'] as $user_data)
                                <option value="{{ $user_data->id }}" <?php echo isset($filter['user']) && $filter['user'] == $user_data->id  ? 'selected' : '' ?>>{{ $user_data->username }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="btn-companiescl-vt" id="searchButtonDiv">
                            <button type="submit" class="btn_se_cle_vt" id="searchFilters">
                                <img src="{{ asset('assets/images/search_01.svg')}}" alt="search">
                            </button>
                            <button type="button" class="btn_se_cle_vt" id="clearFilters">
                                <img src="{{ asset('assets/images/cle_02.svg')}}" alt="clear">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<div class="card hum_tum_vt pla_body_padd_vt">
    @if (session('success'))
                    <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('error') }}
                    </div>
                @endif
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-header border-0 mt-3" style="box-shadow: none !important;">
                    <div class="dataTables_length_vt bs-select" id="dtBasicExample_length"><label>Show <select name="dtBasicExample_length" aria-controls="dtBasicExample" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive table_user_hed_vt">
            <table id="datatable_3" class="table table-borderless table-centered table-nowrap mb-2">
                <thead class="thead-light vt_head_td">
                    <tr>
                        <th>
                            Company Logo
                        </th>
                        <th>
                            Company Name
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Contact
                        </th>
                        <th>
                            Plant
                        </th>
                        <th>
                            Users
                        </th>
                        <th>
                            Edit
                        </th>
                        <th>
                            Delete
                        </th>
                    </tr>
                </thead>
                <tbody class="btn_a_vt">
                    @foreach ($companies as $com)
                    <tr>
                        <td>
                            @if($com->logo != null)
                            <img src="{{ asset('company_logo/'.$com->logo)}}" alt="" width="40px">
                            @else
                            <img src="{{ asset('company_logo/com_avatar.png')}}" alt="" width="40px">
                            @endif
                        </td>
                        <td>
                            {{$com->company_name}}
                        </td>
                        <td>
                            {{$com->email}}
                        </td>
                        <td>
                            {{$com->contact_number}}
                        </td>
                        <td>
                            @if($com->plant)
                                @foreach ($com->plant as $pl)
                                    {{isset($pl) && isset($pl->plant_name) && $pl->plant_name ? $pl->plant_name : ''}}<br>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if($com->plant)
                                @foreach ($com->plant as $item)
                                    @if(isset($item) && isset($item->plant_user) && $item->plant_user)
                                        @foreach ($item->plant_user as $us)
                                            {{isset($us) && isset($us->user->username) && $us->user->username ? $us->user->username : ''}}<br>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <button type="button" class="dropdown-item editcompany" data-toggle="modal" data-target="#edit-company-vt" data-company-detail="{{json_encode($com)}}">
                                <img src="{{ asset('assets/images/edit_users.svg')}}" alt="" width="15px">
                            </button>
                        </td>

                        <td>
                            <button type="button" class="dropdown-item deleteuser" data-toggle="modal" data-target="#delete-company-vt-{{$com->id}}">
                                <img src="{{ asset('assets/images/delete_users.svg')}}" alt="" width="15px">
                            </button>
                        </td>
                        <!-- Modal Delete -->
                        <div class="modal fade deleteuser_modal" id="delete-company-vt-{{$com->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form id="deleteform" method="POST" action="{{ url('admin/delete-company') }}" class="py-3">
                                            @csrf
                                            <i class="fas fa-exclamation"></i>
                                            <input type="hidden" name="company_id" id="company_id" value="{{$com->id}}">
                                            <h4 class="model-heading-vt">Are you sure to delete <br>this Company ?</h4>
                                            <div class="btn_too_vt">
                                                <button type="submit" class="btn-create-vt">Yes, Delete</button>
                                                <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Add Company</h5>
            </div>
            <div class="modal-body">
                <form class="parsley-examples" method="POST" action="{{ url('admin/add-company') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="custom-file mb-3">
                        <div class="file-upload">
                            <div class="file-select">
                                <div class="file-select-button" id="fileName">Choose File</div>
                                <div class="file-select-name" id="noFile">No file chosen...</div>
                                <input type="file" name="logo" id="chooseFile">
                            </div>
                        </div>
                        <!-- <input type="file" class="custom-file-input" id="logo" name="logo">
                            <label class="custom-file-label" for="customFile">Choose file</label> -->
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Company Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Company Name" class="form-control" id="company_name" name="company_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Contact Number<span class="text-danger">*</span></label>
                        <input type="number" placeholder="123-4567-89" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Email<span class="text-danger">*</span></label>
                        <input type="email" placeholder="Email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn-create-vt">Add</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade edit_company_detail" id="edit-company-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Company</h5>
                <i class="fa-regular fa-circle-xmark" data-dismiss="modal" style="color:white; position: absolute; right: 20px;"></i>
            </div>
            <div class="modal-body">
                <form class="parsley-examples" method="POST" action="{{ url('admin/update-company') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="custom-file mb-3">
                        <div class="file-upload">
                            <div class="file-select">
                                <div class="file-select-button" id="fileName1">Choose File</div>
                                <div class="file-select-name"  id="noFile1">No file chosen...</div>
                                <input type="file" name="logo" id="chooseFile1">
                            </div>
                        </div>
                        <!-- <input type="file" class="custom-file-input" id="logo" name="logo ">
                            <label class="custom-file-label" for="customFile">Choose file</label> -->
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Company Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Company Name" id="company_name" name="company_name" class="form-control">
                        <input type="hidden" id="company_id" name="company_id">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Contact Number<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Contact Number" id="contact_number" name="contact_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Email<span class="text-danger">*</span></label>
                        <input type="email" placeholder="Email" id="email" name="email" class="form-control">
                    </div>
                    <button type="submit" class="btn-create-vt">Update</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal View -->
<div class="modal fade" id="view-company-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalCenterTitle">Company Detail</h5>
                <button type="button" class="close-vt" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 alerts-head-text-vt">
                        <p>Company Name</p>
                    </div>
                    <div class="col-md-6 alerts-detail-text-vt">
                        <p id="company_name_val"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 alerts-head-text-vt">
                        <p>Contact Number</p>
                    </div>
                    <div class="col-md-6 alerts-detail-text-vt">
                        <p id="phone_val"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 alerts-head-text-vt">
                        <p>Email</p>
                    </div>
                    <div class="col-md-6 alerts-detail-text-vt">
                        <p id="email_val"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {

        $('.editcompany').click(function() {
            var com_obj = $(this).attr('data-company-detail');
            com_obj = JSON.parse(com_obj);

            $('.edit_company_detail #company_id').val('');
            $('.edit_company_detail #company_name').val('');
            $('.edit_company_detail #contact_number').val('');
            $('.edit_company_detail #email').val('');

            $('.edit_company_detail #company_id').val(com_obj.id);
            $('.edit_company_detail #company_name').val(com_obj.company_name);
            $('.edit_company_detail #contact_number').val(com_obj.contact_number);
            $('.edit_company_detail #email').val(com_obj.email);

        });

        $(".select2-multiple").select2({
            placeholder: "Plant Name"
        });

        $('#clearFilters').on('click', function(e) {

            $('#company').prop('selectedIndex', 0);
            $('#user').prop('selectedIndex', 0);

            $(".select2-multiple").val("");
            $(".select2-multiple").trigger("change");

            $('#filtersForm').trigger('submit');

        });

        // $('#company').on('change', function() {

        //     var company_id = $('#company').val();
        //     var pl_arr = [];
        //     var user_arr = [];

        //     $.ajax({
        //         url: "{{ route('admin.company.all.filter') }}",
        //         method: "GET",
        //         data: {
        //             'company': company_id,
        //             'plant_name' : null,
        //             'user' : null
        //         },
        //         success: function(data) {
        //             console.log(data);
        //             pl_arr = data[0];
        //             user_arr = data[1];
        //             console.log(user_arr);
        //             //console.log(user_arr.length);
        //             //console.log(user_arr[0].user_id);
        //             console.log(user_arr[0]);

        //             $('#plant_name').empty();
        //             $('#user').empty();

        //             for(var i = 0; i < pl_arr.length; i++) {

        //                 $('#plant_name').append('<option value="'+pl_arr[i].id+'" <?php echo isset($filter["plant_name"]) && in_array($plant->id, $filter["plant_name"])  ? "selected" : " " ?>>'+pl_arr[i].plant_name+'</option>');
        //             }

        //             for(var i = 0; i < user_arr.length; i++) {

        //                 $('#user').append('<option value="all">Users</option>');
        //                 $('#user').append('<option value="'+user_arr[i].user_id+'" <?php echo isset($filter["user"]) && $filter["user"] == $user_data->id  ? "selected" : " " ?>>'+user_arr[i]+'</option>');
        //             }
        //         },
        //         error: function(data) {
        //             console.log(data);
        //             alert('Some Error Occured!');
        //         }
        //     });

        // });

    });
</script>

@endsection
