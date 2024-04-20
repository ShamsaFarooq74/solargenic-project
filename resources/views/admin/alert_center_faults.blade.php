<style>
    .pagination {
        margin-top: 20px;
        margin-bottom: 10px;
        float: right;
    }
    table.table.card-text.alertCardText {
        background: #e7e9eb !important;
        margin-top: 0px;
    }
    .alerts-area-vt.viot_vt .select2-container .select2-selection--multiple {
        overflow-y: hidden !important;
        overflow-x: auto !important;
        height: 35px !important;
        line-height: 35px !important;
    }

    .select2-container .select2-selection--multiple .select2-selection__choice {
    background-color: #063c6e;
    border: none;
    color: #fff !important;
    border-radius: 3px;
    padding: 0 7px;
    margin-top: 10px;
    float: left;
    margin-right: 5px;
    height: 19px;
    line-height: 19px;
    text-align: center;
    width: auto;
}
.alerts-area-vt {
    width: 100%;
    background: #fff;
    max-width: 1050px !important;
    margin: 0 auto;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered {
    box-sizing: border-box;
    list-style: none;
    margin: 0;
    padding: 0 5px;
    width: 100%;
    display: flex;
}

.home-companise_dash-vt .select2-container .select2-selection--multiple {
    min-height: 34px;
    height: auto !important;
    overflow: hidden;
    margin-bottom: 15px;
}

    .select2-container .select2-selection--multiple {
        border: none !important;
        background-color: #ffffff !important;
        box-shadow: 0 0px 0px 0 rgba(0, 0, 0, .1) !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border: none !important;
        outline: 0 !important;
        background: #ffffff !important;
    }

</style>

<div class="alerts-area-vt viot_vt">
    <table class="table card-text faultAlertTable">
        <tbody class="text_wid_vt">
            @foreach ($faults as $fault)
                <tr>
                    <td style="text-align: center !important;padding-right: 0px;width: 110px;">{{ $fault->type }}</td>
                    @if($fault->severity == 'High')
                    <td class="badge badge-danger text-white severityBadge" style="text-align: center !important; padding-right: 0px;width: 72px;">{{ $fault->severity }}</td>
                    @elseif($fault->severity == 'Normal')
                    <td class="badge badge-primary text-white severityBadge" style="text-align: center !important; padding-right: 0px;width: 72px;">{{ $fault->severity }}</td>
                    @elseif($fault->severity == 'Low')
                    <td class="badge badge-success text-white severityBadge" style="text-align: center !important; padding-right: 0px;width: 72px;">{{ $fault->severity }}</td>
                    @endif
                    <td>{{ $fault->alarm_code }}</td>
                    <td style="text-align: left !important;padding-right: 0px;width: 125px;"><button class="alertModalLink" data-toggle="modal" data-target="#exampleModalCenter-{{$fault->id}}">{{ $fault->plant_name }}</button></td>
                    <td style="text-align: left !important;">{{ $fault->siteId }}</td>
                    <td style="text-align: center !important;padding-left: 36px;width: 72px;">{{ strlen($fault->description) > 25 ? substr($fault->description,0,25) .'. . .' : $fault->description }}</td>
                    <td style="text-align: center !important;padding-left: 36px;width: 72px;">{{ strlen($fault->correction_action) > 25 ? substr($fault->correction_action,0,25) .'. . .' : $fault->correction_action }}</td>
{{--                    <td><img src="{{ asset('assets/images/all_alerts_05.svg')}}" alt="">In Progress</td>--}}
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
{{--                                    <div class="row">--}}
{{--                                        <div class="col-md-6 alerts-head-text-vt">--}}
{{--                                            <p>Ticket Status</p>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-md-6 alerts-detail-text-vt">--}}
{{--                                            <p class=""><img src="{{ asset('assets/images/all_alerts_01.svg')}}" alt=""> In Progress</p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}


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

{{$faults->links()}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('.pagination .page-item .page-link:eq(0)').html('Previous');
        $('.pagination .page-item .page-link').last().html('Next');
    });
</script>
