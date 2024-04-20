@extends('layouts.admin.master')
@section('title', 'Sources List')
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
                            Name
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
                    @foreach ($source as $sourc)
                    <tr>
                        <td>
                            {{$sourc->id}}
                        </td>
                        <td>
                            {{$sourc->name}}
                        </td>
                        <td>
                            <button type="button" class="dropdown-item editsource" data-toggle="modal" data-target="#edit-source-vt" data-source-detail="{{json_encode($sourc)}}">
                                <img src="{{ asset('assets/images/edit_users.svg')}}" alt="" width="15px">
                            </button>
                        </td>

                        <td>
                            <button type="button" class="dropdown-item deletesource" data-toggle="modal" data-target="#delete-source-vt" data-source-detail="{{json_encode($sourc)}}">
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
<div class="modal fade" id="addSource" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Add Source</h5>
                <i class="fa-regular fa-circle-xmark" data-dismiss="modal" style="color:white; position: absolute; right: 20px;"></i>
            </div>
            <div class="modal-body">
                <form class="parsley-examples" method="POST" action="{{ route('admin.complain.source.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Source Name" class="form-control" id="source_name" name="source_name" required>
                    </div>
                    <button type="submit" class="btn-create-vt">Add</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade edit_source_detail" id="edit-source-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Source</h5>
                <i class="fa-regular fa-circle-xmark" data-dismiss="modal" style="color:white; position: absolute; right: 20px;"></i>
            </div>
            <div class="modal-body">
                <form class="parsley-examples" method="POST" action="{{ route('admin.complain.source.update') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Source Name" id="source_name" name="source_name" class="form-control">
                        <input type="hidden" id="source_id" name="source_id">
                    </div>
                    <button type="submit" class="btn-create-vt">Update</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade delete_source_detail" id="delete-source-vt" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="deleteform" method="POST" action="{{ route('admin.complain.source.delete') }}" class="py-3">
                    @csrf
                    <i class="fas fa-exclamation"></i>
                    <input type="hidden" name="source_id" id="source_id">
                    <h4 class="model-heading-vt">Are you sure to delete <br>this Source ?</h4>
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

        $('.editsource').click(function() {

            var sourc_obj = $(this).attr('data-source-detail');
            sourc_obj = JSON.parse(sourc_obj);

            $('.edit_source_detail #source_id').val('');
            $('.edit_source_detail #source_name').val('');

            $('.edit_source_detail #source_id').val(sourc_obj.id);
            $('.edit_source_detail #source_name').val(sourc_obj.name);

        });

        $('.deletesource').click(function() {

            var sourc_obj = $(this).attr('data-source-detail');
            sourc_obj = JSON.parse(sourc_obj);

            $('.delete_source_detail #source_id').val('');

            $('.delete_source_detail #source_id').val(sourc_obj.id);

        });

    });
</script>

@endsection
