@extends('layouts.admin.master')
@section('title', 'Sub Categories List')
@section('content')
<div class="content">

<div class="card hum_tum_vt pla_body_padd_vt mt-3">
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
                            ID
                        </th>
                        <th>
                            Category Name
                        </th>
                        <th>
                           Sub-Category Name
                        </th>
                        <th>
                            SLA Duration
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
                    @foreach ($sub_category as $cate)
                    <tr>
                        <td>
                            {{$cate->id}}
                        </td>
                        <td>
                            {{$cate->category_name}}
                        </td>
                        <td>
                            {{$cate->sub_category_name}}
                        </td>
                        <td>
                            {{$cate->duration > 1 ? $cate->duration.' hrs': $cate->duration.' hr'}}
                        </td>
                        <td>
                            <button type="button" class="dropdown-item editcategory" data-toggle="modal" data-target="#edit-category-vt" data-category-detail="{{json_encode($cate)}}">
                                <img src="{{ asset('assets/images/edit_users.svg')}}" alt="" width="15px">
                            </button>
                        </td>

                        <td>
                            <button type="button" class="dropdown-item deletecategory" data-toggle="modal" data-target="#delete-category-vt" data-category-detail="{{json_encode($cate)}}">
                                <img src="{{ asset('assets/images/delete_users.svg')}}" alt="" width="15px">
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Add Sub-Category</h5>
                <i class="fa-regular fa-circle-xmark" data-dismiss="modal" style="color:white; position: absolute; right: 20px;"></i>
            </div>
            <div class="modal-body">
                <form class="parsley-examples" method="POST" action="{{ route('admin.complain.sub-category.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Category<span class="text-danger">*</span></label>
                        <select class="form-control" name="ticket_category_id" required>
                            <option value="">Select Category</option>
                            @if($category)
                            @foreach($category as $key => $cat)
                            <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Sub-Category Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Sub Category Name" class="form-control" name="sub_category_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">SLA Duration<span class="text-danger">*</span></label>
                        <input type="number" placeholder="Hour" class="form-control" name="duration" required>
                    </div>
                    <button type="submit" class="btn-create-vt">Add</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade edit_category_detail" id="edit-category-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Category</h5>
                <i class="fa-regular fa-circle-xmark" data-dismiss="modal" style="color:white; position: absolute; right: 20px;"></i>
            </div>
            <div class="modal-body">
                <form class="parsley-examples" method="POST" action="{{ route('admin.complain.sub-category.update') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Category<span class="text-danger">*</span></label>
                        <select class="form-control" id="ticket_category_id" name="ticket_category_id" required>
                            <option value="">Select Category</option>
                            @if($category)
                            @foreach($category as $key => $cat)
                            <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Sub Category Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Sub Category Name" id="sub_category_name" name="sub_category_name" class="form-control">
                        <input type="hidden" id="sub_category_id" name="sub_category_id">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">SLA Duration<span class="text-danger">*</span></label>
                        <input type="number" placeholder="Hour" id="duration" class="form-control" name="duration" required>
                    </div>
                    <button type="submit" class="btn-create-vt">Update</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade delete_category_detail" id="delete-category-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="deleteform" method="POST" action="{{ route('admin.complain.sub-category.delete') }}" class="py-3">
                    @csrf
                    <i class="fas fa-exclamation"></i>
                    <input type="hidden" name="sub_category_id" id="sub_category_id">
                    <h4 class="model-heading-vt">Are you sure to delete <br>this Category ?</h4>
                    <div class="btn_too_vt">
                        <button type="submit" class="btn-create-vt">Yes, Delete</button>
                        <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {

        $('.editcategory').click(function() {

            var cate_obj = $(this).attr('data-category-detail');
            cate_obj = JSON.parse(cate_obj);

            $('.edit_category_detail #ticket_category_id option:selected').removeAttr('selected');
            $('.edit_category_detail #sub_category_id').val('');
            $('.edit_category_detail #sub_category_name').val('');
            $('.edit_category_detail #duration').val('');

            $('.edit_category_detail #ticket_category_id option[value="'+cate_obj.category_id+'"]').attr("selected","selected");
            $('.edit_category_detail #sub_category_id').val(cate_obj.id);
            $('.edit_category_detail #sub_category_name').val(cate_obj.sub_category_name);
            $('.edit_category_detail #duration').val(cate_obj.duration);

        });

        $('.deletecategory').click(function() {

            var cate_obj = $(this).attr('data-category-detail');
            cate_obj = JSON.parse(cate_obj);

            $('.delete_category_detail #sub_category_id').val('');

            $('.delete_category_detail #sub_category_id').val(cate_obj.id);

        });

    });
</script>

@endsection
