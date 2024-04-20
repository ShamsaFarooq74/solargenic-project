@extends('layouts.admin.master')
@section('title', 'List Tickets')
@section('content')
<div class="container-fluid px-xl-3">
    <div class="row">
        @include('layouts.admin.blocks.inc.ticket_div')
        <div class="row">
            <div class="col-md-12">
                <form class="tabs_comp_vt" action="{{ url('admin/list-ticket')}}">
                    <?php $filter = Session::get('filter'); ?>
                    <div class="form-group">
                        <select class="form-control" name="company_id" id="company_id">
                            <option value="all">Company</option>
                            @if($companies)
                                @foreach($companies as $key => $company)
                                    <option value="{{ $company->id }}" <?php echo isset($filter['company_id']) &&  $filter['company_id'] ==  $company->id ? 'selected' : '' ?> >{{ $company->company_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="plant_id" id="plant_id">
                            <option value="all">Plant</option>
                            @if($plants)
                                @foreach($plants as $key => $plant)
                                    <option value="{{ $plant->id }}" <?php echo isset($filter['plant_id']) &&  $filter['plant_id'] ==  $plant->id ? 'selected' : '' ?> >{{ $plant->plant_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="ticket_id" id="ticket_id">
                            <option value="all">Ticket</option>
                            @if($filter_ticket)
                                @foreach($filter_ticket as $key => $ticket)
                                    <option value="{{ $ticket->id }}" <?php echo isset($filter['ticket_id']) &&  $filter['ticket_id'] ==  $ticket->id ? 'selected' : '' ?> >{{ $ticket->title }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="assign_to" id="assign_to">
                            <option value="all">Assigned To</option>
                            @if($agents)
                                @foreach($agents as $key => $agent)
                                    <option value="{{ $agent->id }}" <?php echo isset($filter['assign_to']) &&  $filter['assign_to'] ==  $agent->id ? 'selected' : '' ?> >{{ $agent->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group clander_ticket">
                        <input type="text" id="range-datepicker" name="date_range" class="form-control" placeholder="01-01-2020 to {{ date('d-m-Y')}}" value="<?php echo isset($filter['date_range']) &&  $filter['date_range'] ? $filter['date_range'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="status" id="sstatus">
                            <option value="all">Status</option>
                            @if($status)
                                @foreach($status as $key => $single_status)
                                    <option value="{{ $single_status->id }}" <?php echo isset($filter['status']) &&  $filter['status'] ==  $single_status->id ? 'selected' : '' ?> >{{ $single_status->status }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="priority" id="source">
                            <option value="all">Priority</option>
                            @if($priority)
                                @foreach($priority as $key => $source)
                                    <option value="{{ $source->id }}" <?php echo isset($filter['priority']) &&  $filter['priority'] ==  $source->id ? 'selected' : '' ?> >{{ $source->priority }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="due_in" id="due_in">
                            <option value="all">Due In</option>
                            @if($due_in)
                                @foreach($due_in as $key => $due)
                                    <option value="{{ $due->id }}" <?php echo isset($filter['due_in']) &&  $filter['due_in'] ==  $due->id ? 'selected' : '' ?> >{{ $due->name }} hrs</option>
                                @endforeach
                            @endif

                        </select>
                    </div>
                    <div class="btn-companies-vt">
                        <button class="btn-clear-vt" type="submit">Apply</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-12 mb-2 head_titsearch_vt">
            <div class="row">
                <div class="col-sm-12 col-md-10 head_tit_vt">
                    <h4>
                        Ticket List
                    </h4>
                </div>
                <div class="col-sm-12 col-md-2">
                    <div class="dataTables_length" id="tickets-table_length"><label>Show
                            <select name="tickets-table_length" aria-controls="tickets-table" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select></label>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- <h5 class="mt-0">Inline edit</h5> -->
                    <div class="table-responsive">
                        <table class="table table-centered mb-0" id="inline-editable">
                            <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Title</th>
                                    <th>Company Name</th>
                                    <th>Medium</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Due In</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                </tr>
                            </thead>

                            <tbody>

                                @if(isset($tickets) )

                                    @php $i=0; @endphp
                                    @php $bool=0; @endphp
                                    @foreach($tickets as $ticket)
                                            @php $bool=1; @endphp
                                            @php
                                            $background='white';
                                            if($ticket->status_check->status == 'Inprogress' || $ticket->status_check->status == 'Assigned'){
                                                $background= '#b4eecb';
                                            }else if($ticket->status_check->status == 'Open'){
                                                $background= '#ffe6b4';
                                            }
                                            @endphp

                                                <tr style="background-color:{{$background}};border-bottom:solid 1px lightgray;">
                                                        <td>
                                                            <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                            @php  $i=$i+1; @endphp
                                                            {{$i}}
                                                            </a>
            {{--                                                {{ $ticket->id }}--}}
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                            {{ $ticket->title }}
                                                            </a>
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                            {{ $ticket->company->company_name }}
                                                            </a>
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                                Manual</a>
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                                {{ $ticket->priority_check->priority}}</a>
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                                {{ $ticket->status_check->status }}</a>
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                                {{ $ticket->due_in }} hrs
                                                            </a>
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                            {{$ticket->agents }}
                                                            </a>
                                                        </td>
                                                        <td> <a href="{{ url('admin/view-edit-ticket?ticket_id='. $ticket->id)}}" style="color:gray;">
                                                            {{ $ticket->created_at }}
                                                            </a>
                                                        </td>

                                                </tr>

                                    @endforeach
                                        @if($bool)
                                        @else
                                            <tr>
                                                <td>
                                                    <h6>No Record Found!</h6>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>

                                            </tr>
                                        @endif

                                @endif
                            </tbody>
                        </table>
                    </div> <!-- end .table-responsive-->
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div>
        <div class="col-md-12 mb-4">
            <?php echo $tickets->render(); ?>
            <!-- <ul class="pagination pagination-rounded justify-content-end mb-0 mt-2">
                <li class="page-item">
                    <a class="page-link" href="javascript: void(0);" aria-label="Previous">
                        <span aria-hidden="true">«</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="javascript: void(0);">1</a></li>
                <li class="page-item"><a class="page-link" href="javascript: void(0);">2</a></li>
                <li class="page-item"><a class="page-link" href="javascript: void(0);">3</a></li>
                <li class="page-item"><a class="page-link" href="javascript: void(0);">4</a></li>
                <li class="page-item"><a class="page-link" href="javascript: void(0);">5</a></li>
                <li class="page-item">
                    <a class="page-link" href="javascript: void(0);" aria-label="Next">
                        <span aria-hidden="true">»</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul> -->
        </div>
    </div>
</div>
<!--  Alerts Center Modal-->

@endsection
