@extends('layouts.admin.master')
@section('title', 'Dashboard')
@section('content')
<style>
    .content-page {
        overflow: hidden;
        padding: 0 !important;
        min-height: auto;
        margin-top: 70px;
    }

    .single-dashboard-vt {
        width: 100%;
        float: left;
        min-height: 278px;
        background: none !important;
        padding: 0;
        position: relative;
        overflow-x: auto;
        margin-bottom: 0;
        height: 89vh !important;
        padding-top: 0;
    }

    .single-dashb_vt {
        min-width: 625px;
        max-width: 625px;
        float: none;
        margin: 0 auto;
        position: absolute;
        z-index: 99;
        top: 30px;
        border-radius: 25px;
        left: 0;
        right: 0;
        background: hsl(83deg 50% 50% / 49%);
        border: 50px solid rgb(141 190 63 / 0%);
    }

    .wij_area_vt {
        width: 230px;
        position: absolute;
        bottom: 27px;
        left: auto;
        right: 0;
        z-index: 9;
        margin: 0 auto;
        border-radius: 25px;
        padding: 15px;
    }

    .wij_area_vt ul {
        margin: 0;
        padding: 0;
    }

    .wij_area_vt ul li {
        list-style: none;
        width: 100%;
        background: #fff;
        margin: 2% 0;
        border-radius: 20px;
    }

    .wij_area_vt ul li img {
        width: 100%;
    }
</style>

<div class="single-dashboard-vt">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d465843.31155781244!2d55.466674514375214!3d24.19273604428339!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e8ab145cbd5a049%3A0xf56f8cea5bf29f7f!2sAl+Ain+-+Abu+Dhabi+-+United+Arab+Emirates!5e0!3m2!1sen!2s!4v1479300781817" width="100%" height="550px" frameborder="0" style="border:0" allowfullscreen></iframe>
    <div class="single-dashb_vt">

        <div class="single-dashboard-row-vt">

            <img src="{{ asset('assets/images/tower.png') }}" alt="tower" width="45">

            <div class="single-area-vt">

                <h4 style="padding-left: 15px;">Grid</h4>

                <span>28.32 kW</span>



                <div class="size_power active-animatioon"></div>



            </div>

        </div>

        <div class="single-dashboard-tow-vt">

            <img src="{{ asset('assets/images/sensor.png') }}" alt="sensor" class="img" width="45">

            <div class="single-area-tow-vt">

                <h4>Consumption</h4>

                <span>66.2 kW</span>


                <div class="size_consumption active-animatioon"></div>


            </div>

            <img src="{{ asset('assets/images/home.png')}}" alt="home" width="45">

        </div>

        <div class="single-dashboard-row-vt">

            <img src="{{ asset('assets/images/power.png') }}" alt="power" width="45">

            <div class="single-area-vt">

                <h4>Generation</h4>

                <span>37.88 kW</span>


                <div class="size_generation active-animatioon"></div>


            </div>

        </div>

    </div>
    <div class="wij_area_vt">
        <ul>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
        </ul>
    </div>
    <div class="wij_area_vt">
        <ul>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
            <li><img src="{{ asset('assets/images/graph.png')}}" alt=""></li>
        </ul>
    </div>

</div>

@endsection