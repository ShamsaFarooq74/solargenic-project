
@extends('layouts.admin.master')
@section('title', 'Alert Center')
@section('content')

@php
    $alert_filter = Session::get('alert_filter');
    $where_array = Session::get('where_array');
    $is_filter = Session::get('is_filter');
    $str_limit = 4;
@endphp

<style>
    .pagination {
        margin: 0 15px;
        float: right;
        margin-top: -31px;
    }
</style>
<div class="col-lg-12 mb-1">
    <div class="alerts-area-vt viot_vt mt-3">
        <table class="table card-text alertCardText">
            <thead>
                <div class="home_head_vt">
                    <form id="alertFilterForm" action="{{route('admin.all.alerts')}}" method="GET">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <select class="form-control select2-multiple type_multi_select" name="types[]" id="types" data-toggle="select2" multiple>
                                        @if(isset($types) && count($types) > 0)
                                        @foreach($types as $type)
                                        <option value="{{ $type->type }}" @php echo isset($alert_filter['types']) && in_array($type->type, $alert_filter['types'])  ? 'selected' : '' @endphp>{{ $type->type }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control select2-multiple severity_multi_select" name="severity[]" id="severity" data-toggle="select2" multiple>
                                        @if(isset($importances) && count($importances) > 0)
                                        @foreach($importances as $importance)
                                        <option value="{{ $importance->severity }}" @php echo isset($alert_filter['severity']) && in_array($importance->severity, $alert_filter['severity']) ? 'selected' : '' @endphp>{{ $importance->severity }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control select2-multiple alarm_code_multi_select" name="alarm_code[]" id="alarm_code" data-toggle="select2" multiple>
                                        @if(isset($alarm_codes) && count($alarm_codes) > 0)
                                        @foreach($alarm_codes as $alarm_code)
                                        <option value="{{ $alarm_code->alarm_code }}" @php echo isset($alert_filter['alarm_code']) && in_array($alarm_code->alarm_code, $alert_filter['alarm_code']) ? 'selected' : '' @endphp>{{ $alarm_code->alarm_code }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control select2-multiple plant_name_multi_select" name="plant_id[]" id="plant_id" data-toggle="select2" multiple>
                                        @if(isset($plants) && count($plants) > 0)
                                        @foreach($plants as $plant)
                                        <option value="{{ $plant->id }}" @php echo isset($alert_filter['plant_id']) && in_array($plant->id, $alert_filter['plant_id']) ? 'selected' : '' @endphp>{{ $plant->plant_name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control select2-multiple site_id_multi_select" name="site_id[]" id="site_id" data-toggle="select2" multiple>
                                        @if(isset($site_ids) && count($site_ids) > 0)
                                        @foreach($site_ids as $site_id)
                                        <option value="{{ $site_id->site_id }}" @php echo isset($alert_filter['site_id']) && in_array($site_id->site_id, $alert_filter['site_id']) ? 'selected' : '' @endphp>{{ $site_id->site_id }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </td>
                            <td>
                                Alert Detail
                            </td>
                            <td>
                                Suggested Solution
                            </td>
                            <!-- <td>
                                Ticket Status
                            </td> -->
                            <td>
                                Date/Time
                            </td>
                            <td>
                                <div id="searchButtonDiv">
                                    <button type="submit" class="btn_se_cle_vt" id="searchFilters">
                                        <img src="{{ asset('assets/images/search_01.svg')}}" alt="search">
                                    </button>
                                    <a href="{{route('admin.all.alerts')}}" type="button" id="clearFilters">
                                        <img src="{{ asset('assets/images/cle_02.svg')}}" alt="clear">
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </form>
                </div>
            </thead>
            <tbody>
            @foreach ($faults as $fault)
                <tr>
                    <td>{{ $fault->type }}</td>
                    @if($fault->severity == 'Critical')
                    <td class="badge_danger" >{{ $fault->severity }}</td>
                    @elseif($fault->severity == 'Major')
                    <td class="badge_primary" >{{ $fault->severity }}</td>
                    @elseif($fault->severity == 'Minor')
                    <td class="badge_success" >{{ $fault->severity }}</td>
                    @endif
                    <td>{{ $fault->alarm_code }}</td>
                    <td><button class="alertModalLink" data-toggle="modal" data-target="#exampleModalCenter-{{$fault->id}}">{{ $fault->plant_name }}</button></td>
                    <td>{{ $fault->siteId }}</td>
                    <td>{{ strlen($fault->description) > 25 ? substr($fault->description,0,25) .'. . .' : $fault->description }}</td>
                    <td>{{ strlen($fault->correction_action) > 25 ? substr($fault->correction_action,0,25) .'. . .' : $fault->correction_action }}</td>
                    <!-- <td><img src="{{ asset('assets/images/all_alerts_05.svg')}}" alt="">In Progress</td> -->
                    <td>{{ date('d-m-Y h:i A',strtotime($fault->created_at)) }}<br>{{ "|" }} <br>{{ $fault->updated_at == null ? 'Current' : date('d-m-Y h:i A',strtotime($fault->updated_at)) }}</td>
                    <td></td>
                    <!--  Alerts Center Modal-->
                    <div class="modal fade" id="exampleModalCenter-{{$fault->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-center" id="exampleModalCenterTitle">Alerts Detail</h5>
                                    <button type="button" class="close-vt" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Type</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="type">{{$fault->type}}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Importance</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="importance">{{ $fault->severity }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Alert Code</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="alarm_code">{{ $fault->alarm_code }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Alert Detail</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="description">{{$fault->description}}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Plant Name</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="plant_name">{{ $fault->plant_name }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Site ID</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="plant_name">{{ $fault->siteId }}</p>
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Ticket Status</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class=""><img src="{{ asset('assets/images/all_alerts_01.svg')}}" alt=""> In Progress</p>
                                        </div>
                                    </div> -->


                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>Suggested Solution</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="correction_action">{{Str::words($fault->correction_action,3, '...')}}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">
                                            <p>From</p>
                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="from">{{ date('d-m-Y h:i A',strtotime($fault->created_at)) }}</p>
                                            <p class="ml-4">|</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 alerts-head-text-vt">

                                        </div>
                                        <div class="col-md-6 alerts-detail-text-vt">
                                            <p class="to">{{ $fault->updated_at == null ? 'Current' : date('d-m-Y h:i A',strtotime($fault->updated_at)) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Save changes</button>
                                    </div> -->
                            </div>
                        </div>
                    </div>

                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</div>
<div class="col-lg-12 mb-2 test">
    @include('admin.alert_center_faults')
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

{{$faults->links()}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('.pagination .page-item .page-link:eq(0)').html('Previous');
        $('.pagination .page-item .page-link').last().html('Next');
    });
</script>

<script type="text/javascript">

    var is_filter = {!! $is_filter !!};

    $(document).ready(function() {

        $(".type_multi_select").select2({
            placeholder: "Type"
        });

        $(".severity_multi_select").select2({
            placeholder: "Importance"
        });

        $(".alarm_code_multi_select").select2({
            placeholder: "Alarm Code"
        });

        $(".plant_name_multi_select").select2({
            placeholder: "Plant Name"
        });

        $(".site_id_multi_select").select2({
            placeholder: "Site ID"
        });

        $('#status-select').on('change',function() {

            const container = document.querySelector('.select_date');
            filter_value = this.value;

            if (filter_value == 'custom_date') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }

            if(filter_value == 'custom_date'){

                $('.date_range_val').change(function() {

                    call_filters();
                });
            }
            else {

                call_filters();
            }

        });

        $('#searchButtonDiv').click(function() {
            call_filters();
            // console.log($('#types').val());
            // console.log($('#severity').val());
            // console.log($('#alarm_code').val());
            // console.log($('#plant_id').val());
            // console.log($('#site_id').val());
        });

        /*$('#types').change(function() {

            call_filters();
        });
        $('#severity').change(function() {

            call_filters();
        });
        $('#alarm_code').change(function() {

            call_filters();
        });
        $('#plant_id').change(function() {

            call_filters();
        });
        $('#site_id').change(function() {

            call_filters();
        });*/
    });

    function call_filters() {
        $('#alertFilterForm').submit();
    }

    $(function() {
        $('body').on('click', '.pagination a', function(e) {
            e.preventDefault();

            if(is_filter == 1) {

                var type = $('#types').val();
                var severity = $('#severity').val();
                var alarm_code = $('#alarm_code').val();
                var plant_id = $('#plant_id').val();
                var site_id = $('#site_id').val();
                var date_range = $('#date_range').val();
                //var custom_date_range = $('#custom_date_range').val();

                var url = $(this).attr('href')+'&types='+type+'&severity='+severity+'&alarm_code='+alarm_code+'&plant_id='+plant_id+'&site_id='+site_id;
                // var url = $(this).attr('href')+'&types='+type+'&severity='+severity+'&alarm_code='+alarm_code+'&plant_id='+plant_id+'&site_id='+site_id+'&date_range='+date_range;
                // var url = $(this).attr('href')+'&types='+type+'&severity='+severity+'&alarm_code='+alarm_code+'&plant_id='+plant_id+'&site_id='+site_id+'&alert_detail='+description+'&sugg_sol='+correction_action+'&ticket_status=all&date_range='+date_range+'&custom_date_range='+custom_date_range;
            }
            else {

                var url = $(this).attr('href');
            }

            console.log(url);
            getArticles(url);
            window.history.pushState("", "", url);
        });

        function getArticles(url) {

            $.ajax({
                url : url,
                from: 'pagination'
            }).done(function (data) {
                location.reload();
            }).fail(function () {
                alert('Alerts could not be loaded.');
            });
        }
    });
</script>

@endsection
