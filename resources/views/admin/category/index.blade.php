@extends('layouts.admin.master')
@section('title', 'Categories List')
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
                            Category Name
                        </th>
                        <th>
                            Source Name
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
                    @foreach ($category as $cate)
                    <tr>
                        <td>
                            {{$cate->category_name}}
                        </td>
                        <td>
                            @foreach ($cate->ticket_source_has_category as $ct)
                            {{$ct->ticket_source['name']}}<br>
                            @endforeach
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
                <h5 class="modal-title" id="exampleModalScrollableTitle">Add Category</h5>
                <i class="fa-regular fa-circle-xmark" data-dismiss="modal" style="color:white; position: absolute; right: 20px;"></i>
            </div>
            <div class="modal-body">
                <form class="parsley-examples" method="POST" action="{{ route('admin.complain.category.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Source<span class="text-danger">*</span></label>
                        <select class="form-control select2-multiple" name="ticket_source_id[]" required multiple>
                            @if($source)
                            @foreach($source as $key => $sourc)
                            <option value="{{$sourc->id}}">{{$sourc->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Category Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Category Name" class="form-control" name="category_name" required>
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
                <form class="parsley-examples" method="POST" action="{{ route('admin.complain.category.update') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Source<span class="text-danger">*</span></label>
                        <select class="form-control" name="ticket_source_id[]" id="ticket_source_id" required multiple>
                            @if($source)
                            @foreach($source as $key => $sourc)
                            <option value="{{$sourc->id}}">{{$sourc->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Category Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Category Name" id="category_name" name="category_name" class="form-control">
                        <input type="hidden" id="category_id" name="category_id">
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
                <form id="deleteform" method="POST" action="{{ route('admin.complain.category.delete') }}" class="py-3">
                    @csrf
                    <i class="fas fa-exclamation"></i>
                    <input type="hidden" name="category_id" id="category_id">
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

        $(".select2-multiple").select2({
            placeholder: "Source Name"
        });

        $('.editcategory').click(function() {

            var cate_obj = $(this).attr('data-category-detail');
            cate_obj = JSON.parse(cate_obj);

            $('.edit_category_detail #ticket_source_id option:selected').removeAttr('selected');
            $('.edit_category_detail #category_id').val('');
            $('.edit_category_detail #category_name').val('');

            //$('.edit_category_detail #ticket_source_id option[value="'+cate_obj.source_id+'"]').attr("selected","selected");
            $.each(cate_obj.ticket_source_has_category, function(i,e){
                $('.edit_category_detail #ticket_source_id option[value="'+e.source_id+'"]').attr("selected","selected");
            });
            $('.edit_category_detail #category_id').val(cate_obj.id);
            $('.edit_category_detail #category_name').val(cate_obj.category_name);

        });

        $('.deletecategory').click(function() {

            var cate_obj = $(this).attr('data-category-detail');
            cate_obj = JSON.parse(cate_obj);

            $('.delete_category_detail #category_id').val('');

            $('.delete_category_detail #category_id').val(cate_obj.id);

        });

    });
</script>

@endsection
