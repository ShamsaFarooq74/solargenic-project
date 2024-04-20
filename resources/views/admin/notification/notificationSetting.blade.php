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

    .notification_vt .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #000 !important;
        background-color: #fff;
        border-color: #504E4E !important;
    }

    .notification_vt .modal-title {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        font-size: 14px;
    }

    .modal-header {
        background: #8DBE3F;
    }

    .notification_vt .checkbox label {
        cursor: pointer;
    }

    .btn_pin_vt {
        background: none !important;
        border: none !important;
        color: #063C6E !important;
    }

    .table .thead-light th {
        text-align: left !important;
    }
</style>



<div class="col-md-12 mt-3">
    <div class="card">
        <div class="card-header">
            <h3>Notification</h3>
        </div>

        <div class="pb-3">
            <div class="table-responsive">
                <table class="table table-borderless table-centered table-nowrap">
                    <thead class="thead-light vt_head_td">
                        <tr>
                            <th>Type</th>
                            <th>Option</th>
                            <th>Sample Message</th>
                        </tr>
                    </thead>
                    <tbody class="btn_a_vt">
                        <tr>
                            <td>
                                Password Reset
                            </td>
                            <td>
                                <button class="btn_pin_vt" data-toggle="modal" data-animation="fadein" data-target=".bs-example-modal-center"><i class="fa fa-edit"></i></button> Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                            </td>
                            <td class="checkbox_check">
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox1" type="checkbox">
                                    <label for="checkbox1">
                                        Email
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox2" type="checkbox">
                                    <label for="checkbox2">
                                        SMS
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox3" type="checkbox">
                                    <label for="checkbox3">
                                        Mobile APP
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Password Reset
                            </td>
                            <td>
                                <button class="btn_pin_vt" data-toggle="modal" data-animation="fadein" data-target=".bs-example-modal-center"><i class="fa fa-edit"></i></button> Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                            </td>
                            <td class="checkbox_check">
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox1" type="checkbox">
                                    <label for="checkbox1">
                                        Email
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox2" type="checkbox">
                                    <label for="checkbox2">
                                        SMS
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox3" type="checkbox">
                                    <label for="checkbox3">
                                        Mobile APP
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Password Reset
                            </td>
                            <td>
                                <button class="btn_pin_vt" data-toggle="modal" data-animation="fadein" data-target=".bs-example-modal-center"><i class="fa fa-edit"></i></button> Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                            </td>
                            <td class="checkbox_check">
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox1" type="checkbox">
                                    <label for="checkbox1">
                                        Email
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox2" type="checkbox">
                                    <label for="checkbox2">
                                        SMS
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox3" type="checkbox">
                                    <label for="checkbox3">
                                        Mobile APP
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Password Reset
                            </td>
                            <td>
                                <button class="btn_pin_vt" data-toggle="modal" data-animation="fadein" data-target=".bs-example-modal-center"><i class="fa fa-edit"></i></button> Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                            </td>
                            <td class="checkbox_check">
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox1" type="checkbox">
                                    <label for="checkbox1">
                                        Email
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox2" type="checkbox">
                                    <label for="checkbox2">
                                        SMS
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox3" type="checkbox">
                                    <label for="checkbox3">
                                        Mobile APP
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Password Reset
                            </td>
                            <td>
                                <button class="btn_pin_vt" data-toggle="modal" data-animation="fadein" data-target=".bs-example-modal-center"><i class="fa fa-edit"></i></button> Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                            </td>
                            <td class="checkbox_check">
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox1" type="checkbox">
                                    <label for="checkbox1">
                                        Email
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox2" type="checkbox">
                                    <label for="checkbox2">
                                        SMS
                                    </label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="checkbox3" type="checkbox">
                                    <label for="checkbox3">
                                        Mobile APP
                                    </label>
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
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
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
                                    <div class="btn_vt">
                                        <button type="button" class="btn_add">Save</button>
                                    </div>
                                </div>
                                <div class="tab-pane show active" id="profile-b1">
                                    <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                    <p class="mb-0">Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>
                                    <div class="btn_vt">
                                        <button type="button" class="btn_add">Save</button>
                                    </div>
                                </div>
                                <div class="tab-pane" id="messages-b1">
                                    <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>
                                    <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                    <div class="btn_vt">
                                        <button type="button" class="btn_add">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><img src="{{ asset('assets/images/nex.svg') }}" alt="" style="transform: translateY(-1.5px);"> Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next <img src="{{ asset('assets/images/down-pr.svg') }}" alt="" style="transform: translateY(-1.5px);margin-left: 2px;"></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<link href="{{ asset('assets/css/summernote.min.css')}}" rel="stylesheet">
<script src="{{ asset('assets/js/summernote.min.js')}}"></script>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js')}}" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $("#summernote-editor").summernote({
            height: 250,
            minHeight: null,
            maxHeight: null,
            focus: !1
        });
    });
</script>
@endsection