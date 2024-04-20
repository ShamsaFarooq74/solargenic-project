@extends('layouts.admin.master')
@section('title', 'Site dashboard')
@section('content')


@php
    $prussian_blue ="#3C3C3C";
    $white_color = "#FFFFFF";
    $verydark_blue = "#000000";
    $black_color = "#000000";
    $manatee_color = "#565455";
    $green_color = "#25BC1F";
    $kelly_green = "#02BD14";
    $yellow_color = "#dfe30ff5";
    $pale_silver_color = "#929292";
    $gray_color = "#ADADAD";

@endphp

<style>
    :root {
        --prussian-blue: <?=$prussian_blue ?>;
        --white-color: <?=$white_color ?>;
        --verydark-blue: <?=$verydark_blue ?>;
        --black-color: <?=$black_color ?>;
        --manatee-color: <?=$manatee_color ?>;
        --green-color: <?=$green_color ?>;
        --kelly-green: <?=$kelly_green ?>;
        --yellow-color: <?=$yellow_color ?>;
        --pale-silver-color: <?=$pale_silver_color ?>;
        --gray-color: <?=$gray_color ?>;
    }

    .inverterCard{
        height: 200px;
        overflow-y:scroll;
        border: 1px solid grey;
        padding: 14px;
    }
    .cardHeading{
        background: grey;
    }
    //ScrollBar
/* ===== Scrollbar CSS ===== */
  /* Firefox */
  * {
    scrollbar-width: auto;
    scrollbar-color: #2f2c30 #ffffff;
  }

  /* Chrome, Edge, and Safari */
  *::-webkit-scrollbar {
    width: 16px;
  }

  *::-webkit-scrollbar-track {
    background: var(--prussian-blue);
  }

  *::-webkit-scrollbar-thumb {
    background-color: grey;
    border-radius: 10px;
    border: 3px solid var(--prussian-blue);
  }
    table.dataTable.no-footer {
        border-bottom: none;
    }
    .content-page{
        background-color: var(--pale-silver-color);
    }

    .kWh_eng_vt {
        top: 26px !important;
        left: 27px !important;
    }

    .home-companies-area-vt {
        margin-bottom: 10px !important;
    }

    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #063c6e;
        border: none;
        color: #fff !important;
        border-radius: 3px;
        padding: 0 7px;
        margin-top: 6px;
    }

    .home-companise_dash-vt .select2-container .select2-selection--multiple {
        min-height: 34px;
        /* height: auto !important; */
        overflow: hidden;
        /* margin-bottom: 15px; */
    }

    .home-companise_dash-vt .select2-container--default .select2-search--inline .select2-search__field {
        min-width: 105px;
    }

    span.noRecord {
        width: 100%;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 110px;
    }

    .card.alerts_card_vt.hum_card_alar {
        min-height: 455px !important;
    }

    .pla_body_padd_vt .select2-container .select2-selection--multiple {
        border: none !important;
        background-color: #ffffff !important;
        box-shadow: 0 0px 0px 0 rgba(0, 0, 0, .1) !important;
    }

    .pla_body_padd_vt .select2-container--default.select2-container--focus .select2-selection--multiple {
        border: none !important;
        outline: 0 !important;
        background: #ffffff !important;
    }

    .card.energyg_vt {
        min-height: 440px;
    }

    .environmentalbenefits_vt {
        min-height: 414px;
    }

    /* @media only screen and (max-width: 1212px) and (min-width: 992px)  {
            .form-group{
                width: 10% !important;
            }
        } */
 #maincharts {
 width: 100%;
 height: 400px;
 margin: 0 auto;

 }
 .sitemultiselect{
     position: relative;
     display: flex;
     flex-direction: column;
     align-items: center;
     justify-content: end;
     gap: 10px;
    /* right:50px; */
    /* width: 150px; */
    /* float: left; */
    /* top: 0; */
    /* z-index: 99;    */
 }
 select{
    color:white !important;
 }
 .SiteSearchBtn{
    background-color: var(--prussian-blue);
    border-color:#cdcae9;
    box-shadow: 0 2px 6px 0 rgb(212 225 227 / 50%);
 }
 .SiteSearchBtn:hover{
    background-color: var(--prussian-blue);
    border-color : var(--prussian-blue);
 }
 .SiteSearchBtn:active{
    background-color: var(--prussian-blue);
    border-color : var(--prussian-blue);
 }
</style>

<!-- Content Start Here -->
<div class="content pale-silver-color">
    <div class="container-fluid medi_class_vt ">
        <!--Top Row Start Here -->
        <div class="row d-flex main-top">
            <div class="d-flex top-left">
                <i class="fa fa-home color-white" aria-hidden="true"></i>
                <span class="mx-2 black-color">{{$plant->plant_name ." - " . $inverterDetail->siteId}}</span>
            </div>
            <div class="d-flex top-right black-color">
                Updated At<span class="mx-2"> {{Date('H:i A d-m-Y' ,strtotime($plant->updated_at))}}</span>
            </div>
        </div>
        <!--Top Row End Here -->
        <!-- Weather Section Start Here -->
        <section class="mt-4">

            <div class="justify-content w-100 d-flex mb-2 flex-div">
                <div class="control-modes rounded" style="background-image:  url('{{ asset('plant_photo/plant_avatar.png') }}')">
                    <div class="close-button rounded-circle  m-2">
                        @if($plant->plant_pic != "") 
                            <img src="{{ asset('assets/images/poweroff.png') }}" alt=""
                            width="40">
                        @else
                            <img src="{{ asset('assets/images/poweroff.png') }}" alt=""
                                width="40">
                        @endif
                    </div>
                </div>
                <div class="control-mode rounded">
                 <img src="{{ asset('assets/images/controlMode.png') }}" alt="" class="img-fluid">
                    <h5 class="color-white">Control Mode</h5>
                    <h6>{{$inverterDetail->control_mode == 0 ? "Disabled" : "Enabled"}}</h6>
                </div>
                <div class="control-mode rounded">
                    <img src="{{ asset('assets/images/site-2.png') }}" alt="" class="img-fluid">
                    <h5 class="color-white">Site Status</h5>
                    <?php
                        if($plantMSGWSiteData){
                            if($plantMSGWSiteData->Fault == 1){
                                $siteStatus = 'Fault';
                            }elseif($plantMSGWSiteData->Standby == 1){
                                $siteStatus = 'Standby';
                            }elseif($plantMSGWSiteData->Derating_Run == 1){
                                $siteStatus = 'Derating Run';
                            }else if($plantMSGWSiteData->Dispatch_Run == 1){
                                $siteStatus = 'Dispatch Run';
                            }
                        }else{
                            $siteStatus = 'N/A';
                        }

                    ?>
                    <h6>{{ isset($siteStatus) ? $siteStatus : 'N/A' }}</h6>
                </div>
                <div class="control-mode rounded">
                      <img src="{{ asset('assets/images/faults-code.png') }}" alt=""
                          class="img-fluid">
                    <h5 class="color-white">Fault Code</h5>
                    <h6>{{ isset($plantMSGWSiteData->FaultCode) ? $plantMSGWSiteData->FaultCode : 'N/A'}}</h6>
                </div>
                <?php
                    if($saltecLiveData->installedMeterType == 1.0 ){
                        $meterType = 'PMM';
                    }elseif($saltecLiveData->installedMeterType == 2.0){
                        $meterType = 'Klemsan';
                    }elseif($saltecLiveData->installedMeterType == 3.0){
                        $meterType = 'PMW';
                    }else{
                        $meterType = 'None';
                    }
                ?>
                <div class="control-mode rounded">
                    <img src="{{ asset('assets/images/timer.png') }}" alt="" class="img-fluid">
                    <h5 class="color-white">Meter Type</h5>

                    <h6>{{ $meterType }}</h6>
                </div>
            </div>

            <div class="justify-content w-100 d-flex mb-2 flexs-div">

                @foreach($weatherDetails as $key=> $dailyWeather)
                @if($key == 0)
                <div class="control-modes-plant rounded manatee-color d-flex">
                    <div class="modes-plant-left">
                        <div class="degree-div">
                            <h3>{{ $dailyWeather['todayMin'] . " / " . $dailyWeather['todayMax']}}*</h3>
                            <div class="equal-widths d-flex color-white">
                                <p>Sunrise: {{$dailyWeather['sunrise']}} </p>
                                <p>Sunset: {{$dailyWeather['sunset']}} </p>
                            </div>
                            <p class="color-white">{{ substr($plant->location,0,50)}} ....</p>
                        </div>
                    </div>
                    <div class="modes-plant-right">
                          <img src="{{ asset('assets/images/clouds.png') }}" alt=""
                              class="img-fluid">
                    </div>
                </div>
                @else
                <div class="controls-mode rounded">
                    <h5 class="color-white">{{ isset($dailyWeather['day']) ? $dailyWeather['day'] : "" }}</h5>
                    <h6 class="color-white">{{$dailyWeather['todayMin'] . " / " . $dailyWeather['todayMax']}}*</h6>
                    <img src="http://openweathermap.org/img/w/{{ $dailyWeather['icon'] }}.png" alt="" class="img-fluid">
                </div>
                @endif
                @endforeach

                <div class="control-mode-plant rounded">
                    <div class="equals-width d-flex color-white">
                        <p>Plant Name</p><span>{{$plant->plant_name}}</span>
                    </div>
                    <div class="equals-width d-flex color-white">
                        <p>Plant Type</p><span>{{$plant->plant_type}}</span>
                    </div>
                    <div class="equals-width d-flex color-white">
                        <p>Designed</p><span>{{$plant->capacity}} kW</span>
                    </div>
                    <div class="equals-width d-flex color-white">
                        <p>Daily Expected Generation</p><span>{{$plant->expected_generation ?? ""}} kWh</span>
                    </div>

                </div>
            </div>
        </section>
        <!-- Weather Section End Here -->

        <!--Load Power Div Start Here -->
        <div class="row mt-3 inter-grid rounded white-color">

            <div class="col-lg-3 mb-3 mb-lg-0">
                <div class="mt-4 inverterCard">
                    <h5 class="white-color">Site Summary</h5>
                    <div class="equal-width d-flex">
                        <p>Import Energy</p><span>{{ $daily['boughtEnergy']}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Export Energy</p><span>{{ $daily['sellEnergy']}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Generated Energy</p><span>{{ $daily['generation']}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Consumed Energy</p><span>{{ $daily['consumption']}}</span>
                    </div>
                </div>
                <div class="mt-4 inverterCard">

                    <h5 class="white-color">Load Power</h5>
                    <div class="equal-width d-flex">
                        <p>Total Load Power</p><span> {{$saltecLiveData->totalLoadPower ?? 'N/A' }} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>L1 Load Power</p><span>{{$saltecLiveData->l1LoadPower ?? 0}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>L2 Load Power</p><span>{{$saltecLiveData->l2LoadPower ?? 0}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>L3 Load Power</p><span>{{$saltecLiveData->l3LoadPower ?? 0}} Kw</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 mb-3 mb-lg-0">
                <div class="mt-4 inverterCard">
                    <h5 class="white-color">Inverter Power</h5>
                    <div class="equal-width d-flex">
                        <p>Total Inverter Power</p><span>{{$inverterDetail->inverterPower}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Total Active Power</p><span>{{ isset($saltecLiveData->Total_Active_Power_kW) ? $saltecLiveData->Total_Active_Power_kW : 'N/A'}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Total Reactive Power</p><span>{{ isset($plantMSGWSiteData->TotalReactivePower) ? $plantMSGWSiteData->TotalReactivePower : 'N/A'}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Daily Inverter Energy</p><span>{{$daily['generation']}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>L1 Voltage</p><span>{{$saltecLiveData->l1Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>L2 Voltage</p><span>{{$saltecLiveData->l2Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>L3 Voltage</p><span>{{$saltecLiveData->l3Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Frequency</p><span>{{$inverterDetail->frequency}} Hz</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Internal Temperature</p><span>{{ isset($plantMSGWSiteData->InternalTemp ) ? $plantMSGWSiteData->InternalTemp : "N/A"}} C</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Power Factor</p><span>{{ isset($plantMSGWSiteData->PowerFactor) ? $plantMSGWSiteData->PowerFactor : 'N/A'}} C</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Array Isulation Resistance</p><span>{{ isset($plantMSGWSiteData->Array_Insulation_Resistance) ? $plantMSGWSiteData->Array_Insulation_Resistance : 'N/A'}} Ohm</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Grid Frequency</p><span>{{ isset($plantMSGWSiteData->Grid_Frequency_Hz) ?  $plantMSGWSiteData->Grid_Frequency_Hz : 'N/A'}} Hz</span>
                    </div>
                </div>
                
                @if($saltecLiveData->DeviceType != "MGCE")
                <div class="mt-4 inverterCard">
                    <h5 class="white-color">Connectivity </h5>
                    <div class="equal-width d-flex">
                        <p>Connectivity Channel</p><span>{{$saltecLiveData->Connectivity_Channel == 2 ? "Wifi" :"LAN"}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Data Type</p><span>{{$saltecLiveData->Data_Type == 1 ? "Live" :"non-live*"}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>SIM Connected</p><span>SIM{{$saltecLiveData->SIM_Connected}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>Ethernet Link Status</p><span>{{$saltecLiveData->Ethernet_Link_Status == 1 ? "Unplugged" :"Plugged"}}</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p>WiFi Signal Strength</p><span>{{$saltecLiveData->WiFi_Signal_Strength_dBm}} dBm</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-3 mb-3 mb-lg-0">
            <div class="mt-4 inverterCard">
                    <h5 class="white-color"> Inverter MPPT</h5>
                    @if($plantMSGWSiteData)
                    <div class="equal-width d-flex">
                        <p> MPPT Current 1</p><span>{{$plantMSGWSiteData->Mppt1Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 2</p><span>{{$plantMSGWSiteData->Mppt2Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 3</p><span>{{$plantMSGWSiteData->Mppt3Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 4</p><span>{{$plantMSGWSiteData->Mppt4Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 5</p><span>{{$plantMSGWSiteData->Mppt5Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 6</p><span>{{$plantMSGWSiteData->Mppt6Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 7</p><span>{{$plantMSGWSiteData->Mppt7Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 8</p><span>{{$plantMSGWSiteData->Mppt8Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Current 9</p><span>{{$plantMSGWSiteData->Mppt9Current}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 1</p><span>{{$plantMSGWSiteData->Mppt1Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 2</p><span>{{$plantMSGWSiteData->Mppt2Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 3</p><span>{{$plantMSGWSiteData->Mppt3Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 4</p><span>{{$plantMSGWSiteData->Mppt4Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 5</p><span>{{$plantMSGWSiteData->Mppt5Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 6</p><span>{{$plantMSGWSiteData->Mppt6Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 7</p><span>{{$plantMSGWSiteData->Mppt7Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 8</p><span>{{$plantMSGWSiteData->Mppt8Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> MPPT Voltage 9</p><span>{{$plantMSGWSiteData->Mppt9Voltage}} V</span>
                    </div>
                    @else
                        <span>No Data Available</span>
                    @endif
                </div>
                <div class="mt-4 inverterCard">
                    <h5 class="white-color"> Grid Power Meter</h5>
                    <div class="equal-width d-flex">
                        <p> L1 Power</p><span>{{$saltecLiveData->l1GridPower}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L2 Power</p><span>{{$saltecLiveData->l2GridPower}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L3 Power</p><span>{{$saltecLiveData->l3GridPower}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> Total Grid Power</p><span>{{$saltecLiveData->totalGridPower}} Kw</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L1 Voltage</p><span>{{$saltecLiveData->l1Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L2 Voltage</p><span>{{$saltecLiveData->l2Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L3 Voltage</p><span>{{$saltecLiveData->l3Voltage}} V</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L1 Current</p><span>{{$saltecLiveData->l1GridCurrent}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L2 Current</p><span>{{$saltecLiveData->l2GridCurrent}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> L3 Current</p><span>{{$saltecLiveData->l3GridCurrent}} A</span>
                    </div>
                    <div class="equal-width d-flex">
                        <p> Grid Frequency</p><span>{{$saltecLiveData->gridFrequency}} Hz</span>
                    </div>
                </div>
            </div>
        </div>
        <!--Load Power Div End Here -->
        <!-- chart -->
         <section class="mt-4 chartsection">
            <div class="row">
            <!-- <div class="card-header zoom_vt_area_vt card-export"> -->
                    <div class="col-md-6">
                        <h3 style="color:#fff;">History</h3>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <input type="date" class="form-control mx-1" style="width:30%;" id="siteHistoryGraphDate" name="date">

                        <div class="sitemultiselect mx-1">
                            <div class="selectBox" onclick="showCheckbox()">
                                <select style="background: white; color: black !important; height: calc(1.6em + 0.9rem + 2px); padding: 0.45rem 0.9rem;">
                                    <option selected>Select Parameter</option>
                                </select>
                                <div class="overSelect"></div>
                            </div>
                            <div id="checkbox" style="position:absolute;top:35px;z-index: 999;">
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                    value="l1_grid_power" checked/>L1 Grid Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l2_grid_power" checked/>L2 Grid Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l3_grid_power" checked/>L3 Grid Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="total_grid_power" checked/>Total Grid Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l1_grid_current" checked/>L1 Grid Current</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l2_grid_current" checked/>L2 Grid Current</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l3_grid_current" checked/>L3 Grid Current</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l1_load_power" />L1 Load Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l2_load_power"/>L2 Load Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="l3_load_power"/>L3 Load Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="total_load_power" />Total Load Power</label>
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                        value="total_pv_power" />Total PV Power</label>
                                <label for="id8" class="batteryPowerCheckBox">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                        id="id8"
                                        value="wifi_signal_strength" />Wifi Signal Strength</label>
                                <label for="id9" class="batterySocCheckBox">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                        id="id9"
                                        value="total_generated_energy" />Total Generated Energy</label>
                                <label for="id10">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                        id="id10"
                                        value="l1_grid_frequency" />L1 Grid Frequency</label>
                                <label for="id11">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                        id="id11"
                                        value="l2_grid_frequency" />L2 Grid Frequency</label>
                                        <label for="id11">
                                <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]"
                                        id="id11"
                                        value="l3_grid_frequency" />L3 Grid Frequency</label>
                            </div>
                        </div>
                        
                        <button type="submit" id="searchHistoryCheckBox" class="btn btn-primary SiteSearchBtn mx-1 my-0">Search</button>

                    </div>
                <!-- </div> -->
            </div>
            <div class="row">
              <div class="col-lg-12">
                

                <div class="history-card-box" dir="ltr" id="historyGraphDiv">
                    <div id="historyContainer"></div>
                    <br>
                </div>
              </div>
            </div>
         </section>
        <!-- chart end here -->
        <!-- start row -->
        <div class="row mt-4">
            <div class="col-12 color-prussian-blue">
                <div class="card hum_tum_vt pla_body_padd_vt pb-0 mb-4">
                    <div class="card-body mb-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-header color-prussian-blue">
                                    <h3 class="All-graph-heading-vt white-color">Plants</h3>
                                    <div class="dataTables_length_vt bs-select" id="dtBasicExample_length"><label>Show
                                            <select name="dtBasicExample_length" aria-controls="dtBasicExample"
                                                class="custom-select custom-select-sm form-control form-control-sm">
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
                        <div class="table-responsive">
                            <table id="datatable_2" class="display table table-borderless table-centered table-nowrap"
                                style="width:100%">
                                <thead class="thead-light vt_head_td">
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Alert Details</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Event Time</th>
                                        <th>Updated at</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td class="one_setting_vt">
                                            <p>1</p>
                                        </td>

                                        <td class="che_vt">
                                            System Fault
                                        </td>
                                        <td class="one_btn_vt">
                                            A System Fauit is an error that occuers.
                                        </td>
                                        <td>
                                            Active
                                        </td>
                                        <td>
                                            ----
                                        </td>
                                        <td>
                                            08:31 PM 10-04-2023
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
</div>
<!-- Content End Here -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
 <script src="https://cdn.jsdelivr.net/npm/echarts@latest/dist/echarts.min.js"></script>


 <script>
        var expanded = false;

        function showCheckbox() {
            var checkbox = document.getElementById("checkbox");
            if (!expanded) {
                checkbox.style.display = "block";
                expanded = true;
            } else {
                checkbox.style.display = "none";
                expanded = false;
            }
        }
    </script>
 <script>
    window.onload = function () {
        function getCurrentDate() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();

        return {
            todayDate: yyyy + '-' + mm + '-' + dd
        };
    }

    var currDate = getCurrentDate();
    $('#siteHistoryGraphDate').val(currDate.todayDate);
        var currDate = getCurrentDate();
        $('siteHistoryGraphDate').val(currDate.todayDate);
        var history_date = $('#siteHistoryGraphDate').val();
        sitehistoryGraphAjax(history_date,historyCheckBoxArray);
    }

    function sitehistoryGraphAjax(date,historyCheckBoxArray) 
    {
        var plantID = {!!json_encode($plant->id)!!};
        var plantSiteId = {!!json_encode( $inverterDetail->siteId)!!};
        $('#historyGraphDiv').empty();
        $.ajax({
            url: "{{ route('admin.bel.site.dashbaord.graph') }}",
            method: "GET",
            data: {
                'plantID': plantID,
                'plantSiteId': plantSiteId,
                'date': date,
                'historyCheckBoxArray': historyCheckBoxArray,
            },
            dataType: 'json',
            success: function (data) {

                console.log(data);
                $('#historyGraphDiv').append('<div id="plantsHistoryChart" style="height:300px;width:100%;"></div>');


                siteHistoryGraph(data);
            },
            error: function (data) {

                $('.historyGraphSpinner').hide();
                $('.historyGraphError').show();
            }
        });
    }
    function siteHistoryGraph(data) {

    // Chart initialization and configuration
        var chartDom = document.getElementById('plantsHistoryChart');
        var myChart = echarts.init(chartDom);
        var option;
        var app = {};

        option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                top: "2%",
                left: "5%",
                data: data.legend_array,
                textStyle: {
                    color: 'white'
                }
            },
            grid: {
                left: '1%',
                right: '4%',
                bottom: '17%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: data.time_array,
                axisLabel: {
                    textStyle: {
                        color: 'white'
                    }
                }
            },
            dataZoom: {
                    type: "slider"
                },
            // yAxis: [
            //     {
            //         type: 'value',
            //         name:"Kw",
            //         nameTextStyle: {
            //             color: 'white',
            //             fontSize: 12,
            //         },
            //         axisLabel: {
            //             textStyle: {
            //                 color: 'white'
            //             }
            //         }
            //     },
            //     {
            //         type: 'value',
            //         axisLabel: {
            //             textStyle: {
            //                 color: 'white'
            //             }
            //         }
            //     }
            // ],
            yAxis: data.y_axis_array,
            series: data.plant_history_graph
        };

        // Set the chart option
        option && myChart.setOption(option,false,false);
    }
    $('#searchHistoryCheckBox').click(function () {

        showCheckbox();

        sitehistoryGraphAjax($('#siteHistoryGraphDate').val(), historyCheckBoxArray);
    });
    var historyUnitArray = new Array();
    var historyUnit = '';

    historyCheckBoxArray = $("input[name='historyCheckBox[]']:checked").map(function () {

        if ($(this).val() == 'l1_grid_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'l2_grid_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'l3_grid_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'total_grid_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'l1_grid_current') {

            historyUnit = 'A';
        } else if ($(this).val() == 'l2_grid_current') {

            historyUnit = 'A';
        } else if ($(this).val() == 'l3_grid_current') {

            historyUnit = 'A';
        } else if ($(this).val() == 'l1_load_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'l2_load_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'l3_load_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'total_load_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'total_pv_power') {

            historyUnit = 'kW';
        } else if ($(this).val() == 'wifi_signal_strength') {

            historyUnit = 'dBm';
        } else if ($(this).val() == 'total_generated_energy') {

            historyUnit = 'KW';
        } else if ($(this).val() == 'l1_grid_frequency') {

            historyUnit = 'HZ';
        } else if ($(this).val() == 'l2_grid_frequency') {

            historyUnit = 'HZ';
        } else if ($(this).val() == 'l2_grid_frequency') {

            historyUnit = 'HZ';
        }

        if (historyUnitArray.indexOf(historyUnit) === -1) {

            historyUnitArray.push(historyUnit);
        }

        if (historyUnitArray.indexOf(historyUnit) === -1) {

            historyUnitArray.push(historyUnit);
        }

        if (historyUnitArray.length > 2) {

            $(this).prop('checked', false);
            alert('You cannot check more than 2 different units');
        } else {

            return $(this).val();
        }

    }).get();
    $('.historyCheckBox').change(function () {

        var historyUnitArray = new Array();
        var historyUnit = '';

        historyCheckBoxArray = $("input[name='historyCheckBox[]']:checked").map(function () {

            if ($(this).val() == 'l1_grid_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'l2_grid_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'l3_grid_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'total_grid_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'l1_grid_current') {

            historyUnit = 'A';
            } else if ($(this).val() == 'l2_grid_current') {

            historyUnit = 'A';
            } else if ($(this).val() == 'l3_grid_current') {

            historyUnit = 'A';
            } else if ($(this).val() == 'l1_load_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'l2_load_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'l3_load_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'total_load_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'total_pv_power') {

            historyUnit = 'kW';
            } else if ($(this).val() == 'wifi_signal_strength') {

            historyUnit = 'dBm';
            } else if ($(this).val() == 'total_generated_energy') {

            historyUnit = 'KW';
            } else if ($(this).val() == 'l1_grid_frequency') {

            historyUnit = 'HZ';
            } else if ($(this).val() == 'l2_grid_frequency') {

            historyUnit = 'HZ';
            } else if ($(this).val() == 'l2_grid_frequency') {

            historyUnit = 'HZ';
            }
            if (historyUnitArray.indexOf(historyUnit) === -1) {

                historyUnitArray.push(historyUnit);
            }

            if (historyUnitArray.length > 2) {

                $(this).prop('checked', false);
                alert('You cannot check more than 2 different units');
            } else {

                return $(this).val();
            }

        }).get();
});
</script>
@endsection
