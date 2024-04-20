@extends('layouts.admin.master')
@section('title', 'All User Roles')
@section('content')
<style>
    .checkbox_check {
        display: flex;
        width: auto;
        justify-content: space-between;
        margin: 0 !important;
    }

    .notification_vt .hum_tum_vt .table .thead-light th {
        text-align: left !important;
    }

    .notification_vt .hum_tum_vt td {
        text-align: left !important;
    }

    .notification_vt .checkbox-primary input[type=checkbox]:checked+label::before {
        background-color: #063c6e;
        border-color: #063c6e;
    }

    .notification_vt .plant_ch_vt .checkbox label {
        margin-bottom: 17px !important;
        cursor: pointer;
    }
</style>
<div class="content">
    <div class="bred_area_vt">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title">
                        <ol class="breadcrumb m-0 p-0">
                            <!-- <li class="breadcrumb-item"><a>Notification</a></li> -->
                        </ol>
                    </div>
                </div>
                <div class="btn-companies-vt">
                    <a href="">
                        <button name="refresh" type="button" class="btn-clear-ref-vt">
                            <img src="{{ asset('assets/images/refresh.png')}}" alt="refresh">
                        </button>
                    </a>
                    <p>Updated at 12:00 PM, 21-10-2020</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 notification_vt">
            <div class="card hum_tum_vt pla_body_padd_vt pb-2 mb-4">
                <div class="card-body mb-2">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-header">
                                <h2 class="All-graph-heading-vt">Roles Management</h2>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless table-centered table-nowrap">
                            <thead class="thead-light vt_head_td">
                                <tr>
                                    <th>Modules</th>
                                    <th>Feature</th>
                                    <th>View</th>
                                    <th>Add</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody class="btn_a_vt plant_ch_vt">
                                <tr>
                                    <td>
                                        Plants
                                    </td>
                                    <td>
                                        Plant Listing
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox1" type="checkbox">
                                            <label for="checkbox1"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox2" type="checkbox">
                                            <label for="checkbox2"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox3" type="checkbox">
                                            <label for="checkbox3"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox4" type="checkbox">
                                            <label for="checkbox4"> </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Plants
                                    </td>
                                    <td>
                                        Plant Listing
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox1" type="checkbox">
                                            <label for="checkbox1"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox2" type="checkbox">
                                            <label for="checkbox2"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox3" type="checkbox">
                                            <label for="checkbox3"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox4" type="checkbox">
                                            <label for="checkbox4"> </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Alert Center
                                    </td>
                                    <td>
                                        Plant Listing
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox1" type="checkbox">
                                            <label for="checkbox1"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox2" type="checkbox">
                                            <label for="checkbox2"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox3" type="checkbox">
                                            <label for="checkbox3"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox4" type="checkbox">
                                            <label for="checkbox4"> </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Plants
                                    </td>
                                    <td>
                                        Plant Listing
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox1" type="checkbox">
                                            <label for="checkbox1"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox2" type="checkbox">
                                            <label for="checkbox2"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox3" type="checkbox">
                                            <label for="checkbox3"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox4" type="checkbox">
                                            <label for="checkbox4"> </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        User Management
                                    </td>
                                    <td>
                                        Environmental & Economic Benefits
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox1" type="checkbox">
                                            <label for="checkbox1"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox2" type="checkbox">
                                            <label for="checkbox2"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox3" type="checkbox">
                                            <label for="checkbox3"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox4" type="checkbox">
                                            <label for="checkbox4"> </label>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center" id="myCenterModalLabel">Template</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <ul class="nav nav-tabs nav-bordered">
                                        <li class="nav-item">
                                            <a href="#home-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                Email
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#profile-b1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                                SMS
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#messages-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                Mobile App
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="home-b1">
                                            <div id="summernote-editor">
                                                <h6>This is an simple editable area.</h6>
                                            </div>
                                        </div>
                                        <div class="tab-pane show active" id="profile-b1">
                                            <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                            <p class="mb-0">Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>
                                        </div>
                                        <div class="tab-pane" id="messages-b1">
                                            <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>
                                            <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                </div> <!-- end card-body-->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1"><img src="{{ asset('assets/images/nex.svg')}}" alt="" style="transform: translateY(-1.5px);"> Previous</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next <img src="{{ asset('assets/images/down-pr.svg')}}" alt="" style="transform: translateY(-1.5px);margin-left: 2px;"></a>
                        </li>
                    </ul>
                </nav>
                <!-- end row -->
                <!-- end row -->
            </div>
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div>
@endsection