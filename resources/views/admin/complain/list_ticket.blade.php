@extends('layouts.admin.master')
@section('title', 'Ticket List')
@section('content')

<style>
    th.dt-center,
    td.dt-center {
        text-align: center !important;
    }

    .dataTables_filter {
        display: none !important;
    }

    .pla_body_padd_vt .select2-container--default.select2-container--focus .select2-selection--multiple {
        border: none !important;
        outline: 0 !important;
        background: #ffffff !important;
    }

    .pla_body_padd_vt .select2-container .select2-selection--multiple {
        background: #ffffff !important;
    }

    .pla_body_padd_vt .select2-container .select2-selection--multiple {
        border: none !important;
        background-color: #ffffff !important;
        box-shadow: 0 0px 0px 0 rgba(0, 0, 0, .1) !important;
    }

    .select2-container .select2-selection--multiple .select2-selection__choice {
        color: #ffffff;
        font-size: 12px;
    }

    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #063c6e;
        border: none;
        color: #fff;
        border-radius: 3px;
        padding: 0 7px;
        margin-top: 6px;
    }
</style>

<div class="content">

    <div class="row">
        <div class="col-lg-12 mb-1">
            @if (session('error'))
            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('error') }}
            </div>
            @endif
            @if (session('success'))
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('success') }}
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card hum_tum_vt pla_body_padd_vt pb-2 mb-4">
                <div class="card-body mb-2">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-header border-0 mt-3" style="box-shadow: none !important;">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable_4" class="display table table-borderless table-centered table-nowrap" style="width:100%">
                            <thead class="thead-light vt_head_td">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Plant Name</th>
                                    <th>Company Name</th>
                                    <th>Source</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Due In</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $key => $ticket)
                                <tr class="clickable" href="{{route('admin.view.edit.ticket', ['id' => $ticket->id])}}" style="cursor: pointer;">
                                    <td>
                                        {{$ticket->id}}
                                    </td>
                                    <td>
                                        {{ $ticket->title }}
                                    </td>
                                    <td>
                                        {{ $ticket->plant_name }}
                                    </td>
                                    <td>
                                        {{ $ticket->company ? $ticket->company->company_name : '' }}
                                    </td>
                                    <td>
                                        {{ $ticket->source_check->name}}
                                    </td>
                                    <td>
                                        {{ $ticket->priority_check->priority}}
                                    </td>
                                    <td>
                                        {{ $ticket->status_check->status }}
                                    </td>
                                    <td>
                                        {{ isset($ticket->due_in) && $ticket->due_in != null ? $ticket->due_in.' hrs' : '' }}
                                    </td>
                                    <td>
                                        {{$ticket->agents }}
                                    </td>
                                    <td>
                                        {{ date('h:i A, d-m-Y', strtotime($ticket->created_at)) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        $('.clickable').click(function() {
            window.location.href = $(this).attr('href');
        });
    });
</script>
@endsection
