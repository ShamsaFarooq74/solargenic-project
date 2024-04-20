@extends('layouts.admin.master')

@section('title', 'User Plant Detail')

@section('content')
    <div class="content">

        <style type="text/css">
            .ht2_plants {
                width: 100%;
                float: left;
                padding-left: 10px;
            }


            .ht2_plants h2 {
                font-size: 20px;
                font-family: "Sofia-Pro-Bold-Az  ,sans-serif";
                color: #636363;
                padding-left: 5px;
            }

            .card_box_vt {
                width: 100%;
                float: left;
                position: relative;
                background: #fff;
                border-radius: 15px;
                overflow: hidden;
                padding: 0 0 30px 0;
                min-height: 240px;
                box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);

            }

            .bg_back_vt .card_box_vt {
                box-shadow: none !important;

            }

            .data_name_vt ul {
                margin: 0;
                padding: 0;
            }

            .data_name_vt ul li {
                list-style: none;
                float: left;
                width: 100%;
                text-align: left;
                font-size: 16px;
                color: #636363;
            }

            .data_name_vt ul li span {
                font-size: 11px;
                color: #BBB8B8;
                font-weight: 300;
                display: flex;
            }

            .total_power_vt h4 {
                width: 100%;
                float: left;
                color: #BBB8B8;
                font-size: 10px;
                margin: 0px 0;


            }

            .total_power_vt h5 {
                width: 100%;
                float: left;
                color: #BBB8B8;
                padding-left: 25px;
                font-size: 10px;
                margin: 0 0;
                padding-bottom: 0;
            }

            .total_power_vt {
                width: 170px;
            }

            .data_vt {
                width: 65%;
                float: right;
                text-align: center;
            }

            .data_vt .name_vt {
                font-size: 14px;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .data_vt .pla_vt {
                font-size: 16px;
                font-family: "Sofia-Pro-Black-Az  ,sans-serif";
            }

            .color_01 {
                color: #009FFD;
            }

            .color_01 {
                color: #8FC34D;
            }

            .color_01 {
                color: #435EBE;
            }

            .color_01 {
                color: #46C1AB;
            }

            .color_01 {
                color: #F6A944;
            }

            .card_Profile_vt {
                width: 98%;
                padding: 10px;
                background: #f4fcfa;
                margin: 1%;
                border-radius: 15px;
                box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            }

            .card {
                box-shadow: none;
                overflow: hidden;
                box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            }

            .Profile_img {
                width: 33%;
                float: left;
                margin: 1%;
            }

            .Profile_text {
                width: 63%;
                float: right;
                position: relative;
                display: flex;
                justify-content: center;
                flex-direction: column;
                min-height: 236px;
            }

            .Profile_text samp {
                position: absolute;
                right: 15px;
                bottom: 15px;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
                font-size: 14px;
            }

            .Profile_text h2 {
                font-family: "Sofia-Pro-Black-Az  ,sans-serif";
                color: #46C1AB;
                position: relative;
            }

            .Profile_text p span {
                font-family: "Sofia-Pro-Black-Az  ,sans-serif";
            }

            .tab-content {
                min-height: 350px;
            }

            .table thead th {
                color: #fff;
            }

            .table td,
            .table th {
                border: none;
                color: #fff;
                line-height: 13px;

            }

            .graph_to_vt {
                width: 250px;
                height: 250px;
                float: none;
                border: 5px solid #fff;
                border-radius: 100%;
                padding: 10px;
                margin: 44px auto 0 auto;
            }

            .tol_area_vt {
                background: #0198FF;
                width: 220px;
                height: 220px;
                float: left;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 100%;
                flex-direction: column;
            }

            .tol_area_vt_red {
                background: #F82C1C;
                width: 220px;
                height: 220px;
                float: left;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 100%;
                flex-direction: column;
            }

            .tol_area_vt_red h4 {
                color: #fff !important;
            }

            .tol_area_vt h4,
            h1,
            h6 {
                padding: 0;
                margin: 0 0 15px 0;
                color: #fff;

            }

            .tol_area_vt h1 {
                font-size: 53px;
                font-weight: bold;

            }

            .menu_unt {
                width: 100%;
                float: left;
            }

            .plantsTodayYesterdayChart h4 {
                width: 100%;
                float: left;
                color: #BBB8B8;
                font-size: 12px;
                margin: 0px 0;
                text-align: center;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .plantsTodayYesterdayChart h4 span {
                float: left;
                width: 100%;
                font-size: 14px;
                color: #161616;
            }

            .plantsTodayYesterdayChart h5 {
                width: 100%;
                float: left;
                color: #BBB8B8;
                font-size: 12px;
                padding: 0;
                margin: 3px 0;
                padding-bottom: 3px;
                border-bottom: 1px solid #707070;
                text-align: center;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .plantsTodayYesterdayChart h5 span {
                float: left;
                width: 100%;
                color: #161616;
                font-size: 14px;
            }

            .menu_unt ul {
                margin: 30px 0;
                padding: 0;
            }

            .menu_unt ul li {
                width: 33.3%;
                float: left;
                list-style: none;
                text-align: center;
            }

            .menu_unt ul li h4 {
                color: #D9D6D6;
                font-size: 14px;
                margin: 0;
            }

            .menu_unt ul li h5 {
                color: #FFFFDF;
                font-size: 14px;
            }

            .offline_vt {
                min-width: 124px;
                border-radius: 10px;
                line-height: 39px;
                border: none;
                color: #ffffff;
                font-size: 18px;
                text-transform: capitalize;
                background: url(http://192.168.1.250/solargenic/assets/images/btn_bg_vt.png);
            }

            .menu_unt p {
                color: #D9D6D6;
                font-size: 14px;
                width: 100%;
                float: left;
                text-align: center;
                margin: 15px 0;
            }

            .ht2_plants {
                width: 100%;
                float: left;
                padding-left: 10px;
            }

            .ht2_plants h2 {
                font-size: 20px;
                font-family: "Sofia-Pro-Bold-Az  ,sans-serif";
                color: #636363;
            }

            .ht2_plants h2 span {
                color: #bbb8b8;
                font-size: 16px;
            }

            .ht3_plant {
                width: 100%;
                margin-left: 2%;
                float: left;
                padding-left: 33px;
                margin-bottom: 20px;
                box-shadow: 0 1px 18px 0 rgb(0 0 0 / 7%);
                border-bottom: none;
            }

            .ht3_plant h2 {
                font-size: 20px;
                font-family: "Sofia-Pro-Bold-Az  ,sans-serif";
                color: #2D2828;
                line-height: 50px;
                margin: 0;

            }

            .table td,
            .table th {
                border-bottom: 1px solid #d9d6d6 !important;
            }

            .weth_area_vt {
                width: 100%;
                background: #f5f5f5;
                padding: 0;
                float: left;
                border-radius: 10px;
            }

            .weth_area_vt ul {
                margin: 0;
                padding: 0;
            }

            .weth_area_vt ul li h5 {
                font-size: 13px;
                color: #504e4e;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .weth_area_vt h3 {
                width: 50%;
                float: left;
                color: #7b7a7a;
                font-size: 14px;
                text-align: center;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .weth_area_vt h3 span {
                color: #504e4e;
                display: flex;
                text-align: center;
                width: 100%;
                justify-content: center;
            }

            .weth_area_vt ul li {
                list-style: none;
                float: left;
                border-radius: 10px;
                width: 20.6%;
                margin-right: 2%;
                margin-bottom: 2%;
                text-align: center;
                background: #fff;
                padding: 10px 0;
                min-height: 150px;
                box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            }

            .weth_area_vt ul li:last-child {
                list-style: none;
                float: left;
                border-radius: 10px;
                width: 19.5%;
                margin-right: 0% !important;
                margin-bottom: 2%;
                text-align: center;
                background: #fff;
                padding: 10px 0;
                min-height: 150px;
            }

            .weth_area_vt p {
                color: #9c9c9c;
                font-size: 14px;
            }

            .single-dashboard-vt {
                background: #617B8E !important;
                margin-top: 40px;
            }

            .expectedGraphDiv {
                position: absolute;
                left: 0;
                bottom: 160px;
                width: 100%;
            }

            .expectedGraphDiv .data_pla_vt {
                width: 100%;
                float: right;
                margin-top: 0;
                position: absolute;
                left: 0;
                top: 102px;
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 99;
            }

            .edit_btn_plant {
                width: 100px;
                float: right;
                position: absolute;
                top: 0;
                right: 16px;
                border: none;
                font-size: 16px;
                background: #8DBE3F;
                color: #fff;
                border-radius: 5px;
                line-height: 28px;
                font-weight: 300 !important;
                text-align: center;
            }

            .edit_btn_plant:hover {
                color: #fff;
            }

            .carousel-control-next-icon,
            .carousel-control-prev-icon {
                width: 40px !important;
                height: 40px !important;
            }

            .carousel-control-next,
            .carousel-control-prev {
                width: 10%;
            }

            /* .graph_view_data .nav-pills .nav-link {
            position: absolute;
            top: 0;
            left: 0;
            background: none;
            transform-origin: 0 0;
            transform: rotate(90deg);
        } */
            .graph_view_data .nav-pills > a {
                color: #6c757d;
                font-weight: 500;
                min-height: 200px;
                display: flex;
                text-align: center;
                justify-content: center;
                align-items: center;
                width: 50px;
            }

            .graph_view_data .nav-pills > a span {
                transform: rotate(-90deg);
                min-width: 150px;
            }

            .part_to_vt {
                width: 100%;
                float: left;
                background: #2E386A;
                border-radius: 15px;
                padding: 15px 15px 25px 15px;
                margin-bottom: 15px;
            }

            .part_to_vt h4 {
                color: #fff;
                font-size: 18px;
                margin-bottom: 0;
            }

            .part_to_vt .table .thead-light th {
                background: none !important;
                color: #D9D6D6 !important;
                font-weight: 600 !important;
                padding: 15px 0 15px 12px !important;
                text-align: left !important;
            }

            .one_one_vt .table td,
            .table th {
                border: none;
                color: #fff;
                line-height: 21px !important;
                font-size: 14px;
            }

            .one_one_vt .table th {
                color: #D9D6D6;
            }

            .one_one_vt td {
                color: #fff !important;
            }

            .one_to_vt .table td,
            .table th {
                border: none;
                color: #fff;
                line-height: 13px !important;
                font-size: 14px;
            }

            .ac_vt {
                color: #D9D6D6 !important;
            }

            .one_border_vt {
                position: relative;
                border-right: 1px dashed #fff;
                padding-right: 20px;
            }

            .tot_deta_vt {
                float: left;
                width: 100%;
                background: #fff;
                text-align: center;
                border-radius: 15px;
                margin-bottom: 15px;
                padding: 15px 0;
                min-height: 240px;
                box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            }

            .tot_deta_vt h3 {
                width: 100%;
                float: left;
                color: #636363;
                font-size: 16px;
                margin: 6px 0;
            }

            .tot_deta_vt .ht2_plants h2 {
                width: 100%;
                float: left;
                text-align: center;
                display: flex;
                padding: 0 15px;
                margin: 0;
            }

            .plantsConsumptionChart_vt {
                height: 162px !important;
                width: 128px !important;
                position: relative !important;
                margin-left: 10px !important;
                margin-top: 25px !important;
            }

            .bg_blue2_vt {
                width: 140px;
                height: 168px;
                display: flex;
                justify-content: left;
                align-items: center;
                border-radius: 100px;
                float: left;
                padding-left: 5px;
            }

            .bg_blue2_vt img {
                width: 60%;
            }

            .bg_blue2_vt {
                position: relative;
                width: 100%;
            }

            div#plantsPowerChart {
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
            }

            .bg_back_vt {
                background: #fff;
                width: 100%;
                float: left;
                padding-left: 10px;
                border-radius: 15px;
                overflow: hidden;
                min-height: 225px;
                box-shadow: 0 1px 4px 0 rgb(0 0 0 / 10%);
            }

            .day_month_year_vt {
                background: #ffffff;
                width: 160px;
                display: flex;
                justify-content: center;
                max-height: 40px;
                float: left;
                border: 1px solid #e6e6e6;
                margin-top: 25px;
                margin-left: 30px;
                position: relative;
                border-radius: 5px;
                z-index: 99;
            }

            .day_my_btn_vt {
                width: 302px;
                justify-content: center;
                display: flex;
                background: none;
                border: 1px solid #E2E2E2;
                float: right;
                position: absolute;
                right: 18px;
                margin-top: 104px;
                z-index: 99;
                border-radius: 5px;
                overflow: hidden;
            }

            .date_mont_tabs {
                width: 214px;
                float: left;
                position: absolute;
                margin-right: -19px;
                margin-top: 0;
                right: 13rem;
                top: 112px;
            }

            .date_mont_tabs .fa-caret-left {
                color: #fff;
            }

            .date_mont_tabs .fa-caret-right {
                color: #fff;
            }


            .tab-content .card {
                background-color: #2E386A;
                border: 0 solid #2E386A;
            }

            .tab-content .card-box {
                background-color: #2E386A;
            }

            .history-card-box {
                height: 380px !important;
                padding-top: 25px;
            }

            .date_mont_tabs .day_my_btn_vt {
                margin-top: 0;
                background: #8699A8 !important;
                border: 1px solid #ABB8C2 !important;
            }

            .date_mont_tabs .day_my_btn_vt .day_bt_vt {
                color: #fff !important;
            }

            .date_mont_tabs .day_my_btn_vt .month_bt_vt {
                color: #fff !important;
            }

            .date_mont_tabs .day_month_year_vt {
                margin-top: 0;
            }

            .tab-content .card .day_my_btn_vt .month_bt_vt {
                border: none;
                color: #fff;
            }

            .tab-content .card .day_my_btn_vt .month_bt_vt:hover {
                background: #fff;
                color: #8DBE3F;
            }

            .btn_text_area {
                float: left;
                width: 250px;
                position: relative;
                margin-left: 76px;
                margin-top: 0;
            }

            .btn_text_area p {
                margin: 0 0 10px 0;
                color: #fff;
            }

            .btn_text_area span {
                margin: 0 0 10px 0;
                color: #fff;
                position: absolute;
                z-index: 9;
                left: 0;
                margin-top: 40px;
                font-size: 18px;
            }

            .btn_text_area .p_vt {
                background: #ffffff;
                width: 140px;
                line-height: 35px;
                border-radius: 5px;
                font-size: 16px;
                color: #8DBE3F;
                display: block;
                text-align: center;
            }

            .data_pla_vt {
                width: 100%;
                float: right;
                margin-top: 0;
                position: absolute;
                left: 0;
                top: -28px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .blue3_vt {
                background-color: #0A9405;
                min-width: 10px;
                max-width: 10px;
                height: 10px;
                border-radius: 100px;
                float: left;
                margin-right: 3px;
            }

            .blue4_vt {
                background-color: #F82C1C;
                min-width: 10px;
                max-width: 10px;
                height: 10px;
                border-radius: 100px;
                float: left;
                margin-right: 3px;
            }

            .blue5_vt {
                background-color: #F6A944;
                min-width: 10px;
                max-width: 10px;
                height: 10px;
                border-radius: 100px;
                float: left;
                margin-right: 3px;
            }

            .data_pla_vt ul {
                margin: 0;
                padding: 0;
            }

            .data_pla_vt ul li {
                list-style: none;
                float: left;
                display: flex;
                align-items: center;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
                font-size: 12px;
                margin: 20px 10px 0 10px;
                color: #bbb8b8;
            }

            .data_pla_vt ul li span {
                float: left;
                width: auto;
                display: flex;
                align-items: center;
                color: #636363;
                padding-left: 2px;
            }

            .data_pla_vt ul li strong {
                font-size: 12px;
                font-weight: 300;
                color: #636363;
                padding-left: 2px;
            }

            .plantGraphSpinner {
                position: absolute;
                left: 50%;
                top: 50%;
                z-index: 9999;

            }

            .plantGraphError {
                position: absolute;
                left: 50%;
                top: 50%;
                z-index: 99;
                transform: translateX(-50%);
            }

            .bg_blue2_vt h3 {
                width: 100px;
                float: left;
                color: #636363;
                font-size: 12px;
                margin: 6px 0;
                text-align: center;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                position: absolute;
                top: 77px;
                left: 50%;
                transform: translateX(-50%);
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
                padding-left: 0;
            }

            .card-header h3 {
                font-family: "Sofia-Pro-Bold-Az  ,sans-serif";
            }

            .bg_blue2_vt h3 {
                color: #BBB8B8;
                font-size: 12px;
                font-family: "Sofia-Pro-Light-Az ,sans-serif";
            }

            .bg_blue2_vt h3 span {
                color: #BBB8B8;
                font-size: 12px;
                font-family: "Sofia-Pro-Light-Az ,sans-serif";
            }

            .environmental_vt {
                text-align: center;
                min-height: 180px;
                border-right: 1px solid #f0f0f0;
            }

            .environmental_vt h5 {
                font-size: 27px;
                font-family: "Sofia-Pro-Bold-Az  ,sans-serif";
            }

            .environmental_vt h5 span {
                font-size: 16px;
                color: #7B7A7A;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .btn_left_vt {
                position: absolute;
                top: 0;
                left: -20px;
                top: 50%;
                height: 35px;
                transform: translateY(-50%);
            }

            .btn_right_vt {
                position: absolute;
                top: 0;
                right: -20px;
                top: 50%;
                height: 35px;
                transform: translateY(-50%);
            }

            .fa-calendar-alt {
                color: #E2E2E2;
                font-size: 20px;
                margin-top: 9px;
                width: auto;
                z-index: 1;
                position: absolute;
                left: 7px;
                top: 0px;
            }

            .c-datepicker-data-input {
                width: 100%;
                border: none;
                outline: 0;
                display: inline-block;
                height: 100%;
                margin: 0;
                padding: 0 0 0 32px;
                text-align: left !important;
                font-size: 14px;
                color: #a7a6a6;
                line-height: 1;
                vertical-align: top;
                background: 0 0 !important;
                cursor: pointer;
                position: absolute;
                z-index: 5;
                left: 0;
            }

            .c-datepicker-date-editor {
                background-color: #ffffff !important;
                height: 35px;
                line-height: 35px;
            }

            .body_th_vt th,
            td {
                color: #D9D6D6 !important;

            }

            .env_benefits_vt {
                width: 100%;
                float: left;
                position: relative;
                margin-bottom: 15px;
            }

            div#alertGraphDivv {

                margin-top: 0;
            }

            .env_benefits_vt .day_my_btn_vt {
                width: 190px;
                margin-top: 0;
            }

            .env_benefits_vt .day_month_year_vt {
                margin-top: 0;
            }


            .expectedGraphDiv h3 {
                width: 100px;
                float: left;
                color: #636363;
                font-size: 14px;
                margin: 6px 0;
                text-align: center;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
                padding-left: 0;
            }

            .expectedGraphDiv h3 span {
                border-top: 1px solid #636363;
            }

            .environmental_vt p {
                font-size: 13px;
                color: #bbb8b8;
            }

            /* .min_hig_vt_box{
            min-height: 463px !important;
        } */
            div#graphDiv_A1910264037 {
                width: 88% !important;
                float: left;
                overflow: hidden;
            }

            .plantsConsumptionChart {
                position: absolute;
                left: 10px;
                bottom: 80px;
                right: 0;
            }

            .plantsConsumptionChart h4 {
                font-size: 14px;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .data_name_vt {
                border-right: 1px solid #f5f5f5;
                float: left;
            }

            .data_name1_vt {
                border-right: none
                float: left;
            }

            .element_style {
                height: 260px;
                width: 100%;
                margin-top: -27px;
                margin-bottom: 27px;
            }

            .card-stat-vt .card-header {
                margin-bottom: 15px !important;
            }

            .card-header {
                padding: 1rem 1rem;
                background-color: #ffffff;
                box-shadow: 0 1px 18px 0 rgb(0 0 0 / 7%);
                width: 100%;
                float: left;
                border-radius: 0px !important;
                border-bottom: none;
            }

            .tab-pane.graphDataViewTabPane .card {
                border: none !important;
                box-shadow: none !important;
            }

            .tab-pane.graphDataViewTabPane .card-box {
                border: none !important;
                box-shadow: none !important;
            }

            .one_wather p {
                font-size: 24px;
                color: #504E4E;
            }

            .over_area_vt {
                height: 195px;
                overflow-y: auto;
            }

            .over_flow_vt {
                height: 36px;
                position: relative;
                top: 0;
                background: #2E386A;
                width: 100%;
            }

            .over_flow_vt th {
                padding: 15px 31px 15px 14px;
            }

            /* width */
            ::-webkit-scrollbar {
                width: 8px;
            }

            /* Track */
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            /* Handle */
            ::-webkit-scrollbar-thumb {
                background: #29475D;
            }

            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
                background: #29475D;
            }

            .exdaymy_btn_vt .day_my_btn_vt {
                width: 190px !important;
                margin-top: 80px !important;
            }

            .exdaymy_btn_vt {
                max-height: 378px;
            }

            .Generation_text {
                position: absolute;
                left: 0px;
                bottom: -115px;
                font-size: 14px;
                color: #fff;
                z-index: 99;
                text-align: center;
                width: auto;
            }

            .Generation_text span {
                display: block;
                width: 100%;
            }

            .Grid_text {
                position: absolute;
                right: 22px;
                bottom: -118px;
                font-size: 14px;
                color: #fff;
                z-index: 99;
                text-align: center;
                width: auto;
            }

            .Grid_text span {
                display: block;
                width: 100%;
            }

            .meter_text {
                position: absolute;
                left: 26px;
                bottom: -143px;
                font-size: 14px;
                color: #fff;
                z-index: 99;
                text-align: center;
                width: auto;
            }

            .meter_text span {
                display: block;
                width: 100%;
            }

            .output_text {
                position: absolute;
                left: 7px;
                bottom: 87px;
                font-size: 14px;
                color: #fff;
                z-index: 99;
                text-align: center;
                width: auto;
            }

            .output_text span {
                display: block;
                width: 100%;

            }

            .grid_hide {

                visibility: hidden !important;
            }

            .margin_vt {
                margin: 0;
                padding: 35px 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 250px;
                border-right: 1px solid rgb(0 0 0 / 10%);
            }

            .margin2_vt {
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 250px;
            }

            .img_therr {
                position: relative;
                margin: 0 15px;

            }

            .dataname_vt ul {
                margin: 0;
                padding: 0;
            }

            .dataname_vt ul li {
                list-style: none;
                float: left;
                width: 100%;
                text-align: left;
                font-size: 16px;
                color: #636363;

            }

            .dataname_vt ul li span {
                font-size: 11px;
                color: #BBB8B8;
                font-weight: 300;
                display: flex;
            }

            .cland_card_vt {
                float: right;
                margin-right: 10.5rem;
            }

            .cland_card_vt .day_month_year_vt button {
                background: none;
                border: none;
                color: #fff;
            }

            .drop_search_vt {
                float: left;
                margin-left: 6rem;
                position: relative;
                width: 220px;
                margin-top: 23px;
            }


            .drop_search_vt button {
                background: #fff;
                border: none;
                border-radius: 7px;
                margin-left: 10px;
                font-size: 14px;
                color: #a7a6a6;
                line-height: 32px;
                position: absolute;
                right: 0;
                top: 0;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";

            }

            .drop_search_history_vt {
                float: left;
                margin-left: 16rem;
                position: absolute;
                width: 220px;
                margin-top: 107px;
            }

            .drop_search_history_vt button {
                background: #fff;
                border: none;
                border-radius: 7px;
                margin-left: 10px;
                font-size: 14px;
                color: #a7a6a6;
                line-height: 32px;
                position: absolute;
                right: 0;
                top: 0;
                border: 1px solid #ccc;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";

            }

            .multiselect {
                width: 150px;
                float: left;
                z-index: 9999999;
                position: absolute;
                left: 0;
                top: 0;
            }

            .selectBox {
                position: relative;
            }

            .selectBox select {
                width: 100%;
                height: 35px;
                border-radius: 7px;
                font-size: 12px;
                color: #a7a6a6;
                border-color: #ccc !important;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            .overSelect {
                position: absolute;
                left: 0;
                right: 0;
                top: 0;
                bottom: 0;
            }

            #checkboxes {
                display: none;
                border: 1px #dadada solid;
                background: #fff;
                border-radius: 5px;
                padding: 5px;
            }

            #checkboxes label {
                display: block;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                font-size: 12px;
            }

            #checkboxes input {
                margin-right: 5px;
            }

            #checkboxes label:hover {
                background-color: #1e90ff;
            }

            #checkbox {
                display: none;
                border: 1px #ccc solid;
                background: #fff;
                border-radius: 5px;
                padding: 5px;
            }

            #checkbox label {
                display: block;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                font-size: 12px;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            #checkbox label input {
                margin-right: 3px;
            }

            #checkbox label:hover {
                background-color: #1e90ff;
            }

            .top_text_dc {
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 10px 0;
            }

            .class_porder_vt {
                width: 100%;
                border-bottom: 1px solid #ccc;
                margin: 10px 0;

            }

            .cd_powervt_vt .table td, .table th {
                border-bottom: none !important;
            }

            .top_text_dc p {
                color: #fff;
                font-size: 13px;
                margin: 0 10px;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            #checkboxinverter {
                display: none;
                border: 1px #dadada solid;
                background: #fff;
                padding: 5px;
            }

            #checkboxinverter label {
                display: block;
                margin-bottom: 5px !important;
                display: flex !important;
                align-items: center !important;
                font-size: 12px !important;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            #checkboxinverter label:hover {
                background-color: #1e90ff;
            }

            .drop_bt_area_vt {
                float: left;
                width: 250px;
                margin-left: 76px;
                top: 43px;
                left: -18px;
                margin-top: 68px;
                position: absolute;
            }

            div#inverterGraphChartDiv {
                margin-top: 60px;
            }

            .drop_bt_area_vt button {
                background: #8DBE3F;
                border: none;
                border-radius: 7px;
                margin-left: 10px;
                font-size: 14px;
                color: #fff;
                line-height: 32px;
                position: absolute;
                right: 0;
                top: 0;
                z-index: 99;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
            }

            ._carousl_vt_area {
                width: 100%;
                position: relative;
                float: left;
                z-index: 9;
                display: flex;
                min-height: 36px;
                justify-content: center;
                align-items: center;
                margin-bottom: 10px;
            }

            .check_area_vt {
                min-width: 270px;
                float: left;
                display: flex;
                justify-content: space-between;
            }

            ._check_op_vt {
                min-width: 250px;
                float: left;
                height: 36px;
                position: relative;
            }

            ._check_op_vt button {
                background: #8DBE3F;
                border: none;
                border-radius: 7px;
                margin-left: 10px;
                font-size: 14px;
                color: #fff;
                line-height: 32px;
                position: absolute;
                right: 36px;
                top: 0;
                font-family: "Sofia-Pro-Regular-Az ,sans-serif";
                z-index: 99;
            }

            #carousel .table-responsive {
                padding-right: 4rem;
            }

            .check_area_vt label {
                color: #fff !important;
            }

            ._date_vt {
                min-width: 20%;
                float: left;
                text-align: right;
            }

            ._date_vt .day_month_year_vt {
                float: right !important;
                margin-top: 0 !important;
                margin-left: 0 !important;
            }

            ._date_vt i {
                color: #fff !important;
            }

            #checkpv {
                display: none;
                border: 1px #dadada solid;
                background: #fff;
                width: 100%;
                float: left;
            }

            #checkpv label {
                display: block;
                margin-bottom: 5px !important;
                display: flex !important;
                align-items: center !important;
                font-size: 12px !important;
                width: 33%;
                float: left;
            }

            #checkpv label:hover {
                background-color: #1e90ff;
            }

            .pvSelectionCheckBox {

            }


        </style>


        @php
            $workStateCharLimit = 7;
    $graphicalDataInverterSerialNo = array();
        @endphp
        <input type="hidden" id="plantID" value="{{ $plant->id }}">
        <input type="hidden" id="plantMeterType" value="{{ $plant->meter_type }}">
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card bg_card">

                    <div class="btn_text_area" id="iverterCheckBoxSelectBtn" style="display: none;">
                        <div class="drop_bt_area_vt">
                            <div class="multiselect">
                                <div class="selectBox" onclick="showCheckboxinverter()">
                                    <select>
                                        <option>Select Parameter</option>
                                    </select>
                                    <div class="overSelect"></div>
                                </div>
                                <div id="checkboxinverter">
                                    <label for="one">
                                        <input type="checkbox" class="inverterCheckBox" name="inverterCheckBox[]"
                                               id="one" value="output_power" checked/>Output Power</label>
                                    <label for="two">
                                        <input type="checkbox" class="inverterCheckBox" name="inverterCheckBox[]"
                                               id="two" value="dc_power"/>Input DC Power</label>
                                    <label for="three">
                                        <input type="checkbox" class="inverterCheckBox" name="inverterCheckBox[]"
                                               id="three" value="normalize_power"/>Normalize Power</label>
                                </div>
                            </div>
                            <button type="submit" id="searchInverterCheckBox">Search</button>
                        </div>
                        <!-- <button type="button" id="ExportInverterGraph">Export</button> -->
                        <span
                            data-href="{{route('export.inverter.graph', ['plantID'=>$plant->id, 'Date'=>'2021-07-08'])}}"
                            id="exportgraph" class="btn btn-success btn-sm"
                            onclick="exportTasks(event.target);">Export</span>

                    </div>

                    <div class="date_mont_tabs graphDateTimeDiv" style="display: none;">
                        <div class="day_month_year_vt" id="inverter_day_month_year_vt_day">
                            <button class="btn_left_vt"><i id="inverterGraphPreviousDay" class="fa fa-caret-left"></i>
                            </button>
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-inverter mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="inverterGraphDay" id="inverterGraphDay"
                                       placeholder="Select" class="c-datepicker-data-input" value="">
                            </div>
                            <button class="btn_right_vt"><i id="inverterGraphForwardDay" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <div class="day_month_year_vt" id="inverter_day_month_year_vt_month" style="display: none;">
                            <button class="btn_left_vt"><i id="inverterGraphPreviousMonth" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-inverter mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="inverterGraphMonth" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt"><i id="inverterGraphForwardMonth"
                                                            class="fa fa-caret-right"></i></button>
                        </div>
                        <div class="day_month_year_vt" id="inverter_day_month_year_vt_year" style="display: none;">
                            <button class="btn_left_vt"><i id="inverterGraphPreviousYear" class="fa fa-caret-left"></i>
                            </button>
                            <div class="mt40">
                                <div
                                    class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-inverter mt10">
                                    <i class="fa fa-calendar-alt"></i>
                                    <input type="text" autocomplete="off" name="inverterGraphYear" placeholder="Select"
                                           class="c-datepicker-data-input" value="">
                                </div>
                            </div>
                            <button class="btn_right_vt"><i id="inverterGraphForwardYear" class="fa fa-caret-right"></i>
                            </button>
                        </div>
                        <!-- <div class="day_my_btn_vt" id="inverter_day_my_btn_vt">
                        <button class="day_bt_vt active" id="day">day</button>
                        <button class="month_bt_vt" id="month">month</button>
                        <button class="month_bt_vt" id="year">Year</button>
                    </div> -->
                    </div>

                    <ul class="nav nav-pills navtab-bg nav-justified" id="inverterTab">
                        <li class="nav-item">
                            <a href="#messages1" data-toggle="tab" aria-expanded="false"
                               class="nav-link inverterTabLink active">
                                Energy Flow
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#inverterTabID" data-toggle="tab" aria-expanded="false"
                               class="nav-link inverterTabLink ">
                                Inverter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#messages2" data-toggle="tab" aria-expanded="false"
                               class="nav-link inverterTabLink">
                                PV
                            </a>
                        </li>
                        @if($plant->plant_has_emi == 'Y')
                            <li class="nav-item">
                                <a href="#messages3" data-toggle="tab" aria-expanded="false"
                                   class="nav-link inverterTabLink">
                                    EMI
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane inverterTabPane show active" id="messages1">

                            <div class="single_dashboard_vt">

                                <div class="animation_soler_vt">
                                    <!-- Change Class -->
                                    <div id="{{ $currentDataValues['dc_power'] > 0 ? 'arrowbottomtoo' : 'stop_all' }}">
                                        <div class="arrowSliding">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay1">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay2">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay3">
                                            <div class="arrow"></div>
                                        </div>
                                    </div>

                                    <div class="btn_Consumption"><img src="{{ asset('assets/images/Consumption.png')}}"
                                                                      alt=""></div>
                                    <!-- Change Class -->
                                    <a href="#"
                                       class="{{ $currentDataValues['dc_power'] > 0 ? 'animated-button1' : 'Stop_left' }}">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </a>
                                    <!-- Change Class -->
                                    <div id="{{ $currentDataValues['dc_power'] > 0 ? 'arrowAnim' : 'stop_all' }}">
                                        <div class="arrowSliding">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay1">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay2">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay3">
                                            <div class="arrow"></div>
                                        </div>
                                    </div>
                                    <!-- Change Class -->
                                    <div
                                        id="{{ $currentDataValues['consumption'] > 0 ? 'arrowbottomtop' : 'stop_all' }}">
                                        <div class="arrowSliding">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay1">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay2">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay3">
                                            <div class="arrow"></div>
                                        </div>
                                    </div>
                                    <div class="btn_Generation"><img src="{{ asset('assets/images/Generation.png')}}"
                                                                     alt="">
                                        <div class="Generation_text">DC Input Power
                                            <span>{{ $current['dc_power']}}</span></div>
                                    </div>
                                    <!-- Change Class -->
                                    <a href="#"
                                       class="{{ $currentDataValues['grid'] > 0 ? ($currentDataValues['grid_type'] == '+ve' ? 'animated-button6' : 'animated-button12') : 'Stop_right' }}">
                                        {{-- <a href="#" class="{{ $plant->system_type_id != 1 ? ($currentDataValues['grid'] > 0 ? ($currentDataValues['grid_type'] == '+ve' ? 'animated-button6' : 'animated-button12') : 'Stop_right') : 'grid_hide' }}"> --}}
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </a>
                                    <!-- Change Class -->
                                    <div
                                        id="{{ $currentDataValues['grid'] > 0 ? ($currentDataValues['grid_type'] == '+ve' ? 'gridtohomeleft' : 'arrowright') : 'stop_all' }}">
                                        {{-- <div id="{{ $currentDataValues['grid'] > 0 ? ($currentDataValues['grid_type'] == '+ve' ? 'gridtohomeleft' : 'arrowright') : 'stop_all' }}" class="{{ $plant->system_type_id != 1 ? '' : 'grid_hide' }}"> --}}
                                        <div class="arrowSliding">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay1">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay2">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay3">
                                            <div class="arrow"></div>
                                        </div>
                                    </div>
                                    {{-- <div class="btn_Grid {{ $plant->system_type_id != 1 ? '' : 'grid_hide' }}"><img src="{{ asset('assets/images/Grid.png')}}" alt=""> --}}
                                    <div class="btn_Grid"><img src="{{ asset('assets/images/Grid.png')}}" alt="">
                                        <div class="Grid_text {{ $plant->system_type_id != 1 ? '' : 'grid_hide' }}">Net
                                            Grid
                                            <span>{{ $currentDataValues['grid_type'] == '-ve' ? '-' : '' }}{{ $current['grid'] }}</span>
                                        </div>
                                    </div>
                                    <!-- Change Class -->
                                    <a href="#"
                                       class="{{ $currentDataValues['consumption'] > 0 ? 'animated-button10' : 'Stop_bottom' }}">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </a>
                                    <div class="btn_meter">
                                        <div class="output_text">Output Power <span>{{ $current['generation'] }}</span>
                                        </div>
                                        <img src="{{ asset('assets/images/meter.png')}}" alt="">
                                        <div class="meter_text {{ $plant->system_type_id != 1 ? '' : 'grid_hide' }}">
                                            Load <span>{{ $current['consumption'] }}</span></div>
                                    </div>
                                    <!-- Change Class -->
                                    <div
                                        id="{{ $currentDataValues['grid'] > 0 ? ($currentDataValues['grid_type'] == '+ve' ? 'gridtohome' : 'arrowbottom') : 'stop_all' }}">
                                        {{-- <div id="{{ $currentDataValues['grid'] > 0 ? ($currentDataValues['grid_type'] == '+ve' ? 'gridtohome' : 'arrowbottom') : 'stop_all' }}" class="{{ $plant->system_type_id != 1 ? '' : 'grid_hide' }}"> --}}
                                        <div class="arrowSliding">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay1">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay2">
                                            <div class="arrow"></div>
                                        </div>
                                        <div class="arrowSliding delay3">
                                            <div class="arrow"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="tab-pane inverterTabPane" id="inverterTabID">

                            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel"
                                 data-interval="false">
                                <ol class="carousel-indicators inverterCarouselList">
                                    @foreach ($plantInverters as $key => $item)
                                        <li data-target="#carouselExampleCaptions" data-index="{{$key}}"
                                            data-slide-to="{{ $item->dv_inverter_serial_no }}"
                                            class="{{ $key == 0 ? 'active' : '' }}"></li>
                                    @endforeach
                                </ol>
                                <div class="carousel-inner" role="listbox">
                                    @foreach ($plantInverters as $key => $item)
                                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                            <div class="row">
                                                <div class="col-sm-1 graph_view_data">
                                                    <div
                                                        class="nav flex-column nav-pills nav-pills-tab inverterGraphDataTabs"
                                                        id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                                        <a class="nav-link mb-2 graphDataView" id="v-pills-profile-tab"
                                                           data-toggle="pill"
                                                           href="#v-pills-profile-{{ $item->dv_inverter_serial_no }}"
                                                           role="tab" aria-controls="v-pills-profile"
                                                           aria-selected="false">
                                                            <span>Graphical View</span></a>
                                                        <a class="nav-link active show mb-2 graphDataView"
                                                           id="v-pills-home-tab" data-toggle="pill"
                                                           href="#v-pills-home-{{ $item->dv_inverter_serial_no }}"
                                                           role="tab" aria-controls="v-pills-home" aria-selected="true">
                                                            <span>Data</span></a>
                                                    </div>
                                                </div> <!-- end col-->
                                                <div class="col-sm-11">
                                                    <div class="tab-content pt-0">
                                                        <div class="tab-pane graphDataViewTabPane fade"
                                                             id="v-pills-profile-{{ $item->dv_inverter_serial_no }}"
                                                             role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                        @php
                                                            $graphicalDataInverterSerialNo[] = 'v-pills-profile-'.$item->dv_inverter_serial_no;
                                                        @endphp
                                                        <!--                                                    --><?php //plantPowerGraphData
                                                            ?>
                                                            <div class="card">
                                                                <div
                                                                    class="spinner-border text-success inverterGraphSpinner plantGraphSpinner"
                                                                    role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div>
                                                                <div class="inverterGraphError plantGraphError"
                                                                     style="display: none;">
                                                                    <span>Some Error Occured</span>
                                                                </div>

                                                                <div class="card-box" dir="ltr"
                                                                     id="inverterGraphDiv-{{ $item->dv_inverter_serial_no }}">
                                                                    <div id="inverterGraphChartDiv"></div>
                                                                    <br>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane graphDataViewTabPane fade active show"
                                                             id="v-pills-home-{{ $item->dv_inverter_serial_no }}"
                                                             role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                            <div class="row">
                                                                <div class="col-md-4 one_one_vt">
                                                                    <div class="table-responsive">
                                                                        <table class="table mb-0">
                                                                            <thead>
                                                                            <tr style="border:none">
                                                                                <th style="color:  #fff;">Inverter</th>
                                                                                <th></th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Serial Number
                                                                                </td>
                                                                                <td>{{ $item->dv_inverter_serial_no }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Status
                                                                                </td>
                                                                                <td>{{ $item->inverter_status }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Total DC Input Power
                                                                                </td>
                                                                                <td>{{ $item->dc_input_power }} kW</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Today's Generation
                                                                                </td>
                                                                                <td>{{ $item->today_generation }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Generation Yesterday
                                                                                </td>
                                                                                <td>{{ $item->yesterday_generation }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Generation of Last Month
                                                                                </td>
                                                                                <td>{{ $item->last_month_generation }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Generation of Last Year
                                                                                </td>
                                                                                <td>{{ $item->last_year_generation }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:  #d9d6d6 !important">
                                                                                    Inner Temprature
                                                                                </td>
                                                                                @if ($item->temperature != null)
                                                                                    <td>{{ $item->temperature }}
                                                                                        <sup>o</sup>C
                                                                                    </td>
                                                                                @else
                                                                                    <td>N/A</td>
                                                                                @endif
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="graph_to_vt">
                                                                        <div
                                                                            class="{{ in_array($item->inverter_state_code, [768,769,770,771,772,773,774]) ? 'tol_area_vt_red' : 'tol_area_vt' }}">
                                                                            <h4>Power</h4>
                                                                            <h1>{{ round($item->power[0], 2) }}</h1>
                                                                            <h6>{{ $item->power[1] }}</h6>
                                                                        </div>
                                                                    </div>
                                                                    <div class="menu_unt">
                                                                        <ul>
                                                                            <li>
                                                                                <h4>Total Generation</h4>
                                                                                <h5>{{ $item->total_generation }}</h5>
                                                                            </li>
                                                                            <li>
                                                                                <button type="button"
                                                                                        class="offline_vt">{{ $item->inverter_state }}
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <h4>Start Time</h4>
                                                                                @if($item->start_time == null)
                                                                                    <h5>{{ '-----' }}</h5>
                                                                                @else
                                                                                    <h5>{{ date('h:i A, d-m-Y', strtotime($item->start_time)) }}</h5>
                                                                                @endif
                                                                            </li>
                                                                        </ul>
                                                                    </div>

                                                                </div>
                                                                <div class="col-md-4 one_to_vt">
                                                                    <div class="table-responsive pr-0 pr-md-3">
                                                                        <table class="table mb-0">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>Phase</th>
                                                                                <th>Voltage</th>
                                                                                <th>Current</th>
                                                                                <th>Frequency</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody class="body_th_vt">
                                                                            <tr>
                                                                                <th scope="row">R</th>
                                                                                <td>{{ $item->phase_voltage_r }} V</td>
                                                                                <td>{{ $item->phase_current_r }} A</td>
                                                                                <td>{{ $item->frequency }} Hz</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th scope="row">S</th>
                                                                                <td>{{ $item->phase_voltage_s }} V</td>
                                                                                <td>{{ $item->phase_current_s }} A</td>
                                                                                <td>{{ $item->frequency }} Hz</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th scope="row">T</th>
                                                                                <td>{{ $item->phase_voltage_t }} V</td>
                                                                                <td>{{ $item->phase_current_t }} A</td>
                                                                                <td>{{ $item->frequency }} Hz</td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> <!-- end col-->
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($plantInverters) > 1)
                                    <a class="left carousel-control-prev carousel_control_prev"
                                       href="#carouselExampleCaptions" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="right carousel-control-next carousel_control_next"
                                       href="#carouselExampleCaptions" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                @endif
                            </div>

                        </div>
                        <div class="tab-pane inverterTabPane cd_powervt_vt px-0" id="messages2">

                            <div class="_carousl_vt_area pvGraphTabDataDiv" style="display:none;">
                                <div class="_check_op_vt">
                                    <div class="multiselect">
                                        <div class="selectBox" onclick="showCheckboxpv()">
                                            <select>
                                                <option>Select Parameter</option>
                                            </select>
                                            <div class="overSelect"></div>
                                        </div>
                                        <div id="checkpv">
                                        </div>
                                    </div>
                                    <button type="submit" id="searchPVCheckBox">Search</button>
                                </div>

                                <div class="check_area_vt">
                                    <div class="form-check">
                                        <input type="checkbox" name="pvCheckBox[]" value="pv_current"
                                               class="form-check-input pvCheckBox" id="exampleCheck1" checked>
                                        <label class="form-check-label" for="exampleCheck1">Current</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="pvCheckBox[]" value="pv_voltage"
                                               class="form-check-input pvCheckBox" id="exampleCheck2">
                                        <label class="form-check-label" for="exampleCheck2">Voltage</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="pvCheckBox[]" value="pv_power"
                                               class="form-check-input pvCheckBox" id="exampleCheck3">
                                        <label class="form-check-label" for="exampleCheck3">Power</label>
                                    </div>
                                </div>
                                <div class="_date_vt">
                                    <div class="day_month_year_vt" id="pv_day_month_year_vt_day">
                                        <button class="btn_left_vt"><i id="pvGraphPreviousDay"
                                                                       class="fa fa-caret-left"></i></button>
                                        <div
                                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-pv mt10">
                                            <i class="fa fa-calendar-alt"></i>
                                            <input type="text" autocomplete="off" name="pvGraphDay" id="pvGraphDay"
                                                   placeholder="Select" class="c-datepicker-data-input" value="">
                                        </div>
                                        <button class="btn_right_vt"><i id="pvGraphForwardDay"
                                                                        class="fa fa-caret-right"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="carousel" class="carousel slide" data-ride="carousel" data-interval="false">
                                <ol class="carousel-indicators pvCarouselList">
                                    @foreach ($plantInverters as $key1 => $item1)
                                        <li data-target="#carousel" data-index="{{$key1}}"
                                            data-slide-to="{{ $item1->dv_inverter }}"
                                            class="{{ $key1 == 0 ? 'active' : '' }}"></li>
                                    @endforeach
                                </ol>
                                <div class="carousel-inner" role="listbox">
                                    @foreach ($plantInverters as $key1 => $item1)
                                        <div class="carousel-item {{ $key1 == 0 ? 'active' : '' }}">
                                            <div class="row">
                                                <div class="col-sm-1 graph_view_data">
                                                    <div class="nav flex-column nav-pills nav-pills-tab pvGraphDataTabs"
                                                         id="v-pills-tab"
                                                         role="tablist" aria-orientation="vertical">
                                                        <a class="nav-link mb-2 pvGraphTabCarousel"
                                                           id="v-pills-profile-tab-pv" data-toggle="pill"
                                                           href="#v-pills-profile-pv-{{ $item1->dv_inverter }}"
                                                           role="tab" aria-controls="v-pills-profile"
                                                           aria-selected="false">
                                                            <span>PV Graph</span></a>
                                                        <a class="nav-link active show mb-2 pvDataTabCarousel"
                                                           id="v-pills-home-tab-pv"
                                                           data-toggle="pill"
                                                           href="#v-pills-home-pv-{{ $item1->dv_inverter }}" role="tab"
                                                           aria-controls="v-pills-home" aria-selected="true">
                                                            <span>PV</span></a>
                                                    </div>
                                                </div> <!-- end col-->
                                                <div class="col-sm-11">
                                                    <div class="tab-content pt-0">
                                                        <div class="tab-pane fade"
                                                             id="v-pills-profile-pv-{{ $item1->dv_inverter }}"
                                                             role="tabpanel"
                                                             aria-labelledby="v-pills-profile-tab-pv">
                                                            <div class="pt-3">
                                                                <div
                                                                    class="spinner-border text-success pvGraphSpinner plantGraphSpinner"
                                                                    role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div>
                                                                <div class="pvGraphError plantGraphError"
                                                                     style="display: none;">
                                                                    <span>Some Error Occured</span>
                                                                </div>
                                                                <div class="col-md-12"
                                                                     id="pvGraphDivv-{{ $item1->dv_inverter }}"
                                                                     style="margin-top: 50px !important;">
                                                                    {{-- <div class="bg_blue2_vt pvGraphDiv" id="pvGraphDivv"> --}}
                                                                    <div class="plantsPVChart" id="plantsPVChart"></div>
                                                                    {{-- </div> --}}
                                                                    <div class="bg_blue2_vt noPVDiv"
                                                                         style="display: none;">
                                                                        <h3>No Alerts to Show</h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade active show"
                                                             id="v-pills-home-pv-{{ $item1->dv_inverter }}"
                                                             role="tabpanel"
                                                             aria-labelledby="v-pills-home-tab-pv">
                                                            <div class="top_text_dc">
                                                                <p>Inverter Serial No :
                                                                    <span>{{ $item1->dv_inverter_serial_no }}</span></p>
                                                                <p>Total Installed DC Power :
                                                                    <span>{{ $item1->installed_dc_input_power }}</span>
                                                                </p>
                                                                <p>Total DC input Power : <span>{{ $item1->dc_input_power }} kW</span>
                                                                </p>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-borderless mb-0">
                                                                    <tbody>
                                                                    <tr>
                                                                        <th scope="row">String</th>
                                                                        @for ($i = 1; $i <= 12; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_voltage > 0)
                                                                                    <th style="font-size: 16px;font-weight:600;">{{ 'PV'.$i }}</td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Voltage</th>
                                                                        @for ($i = 1; $i <= 12; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_voltage > 0)
                                                                                    <td>{{ number_format((float)$pv->mppt_voltage, 2, '.', '') }}
                                                                                        V
                                                                                    </td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Current</th>
                                                                        @for ($i = 1; $i <= 12; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_current > 0)
                                                                                    <td>{{ number_format((float)$pv->mppt_current, 2, '.', '') }}
                                                                                        A
                                                                                    </td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Power</th>
                                                                        @for ($i = 1; $i <= 12; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_power > 0)
                                                                                    <td>{{ number_format((float)$pv->mppt_power, 2, '.', '') }}
                                                                                        kW
                                                                                    </td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="class_porder_vt"></div>
                                                            <div class="table-responsive pvSecondRow">
                                                                <table class="table table-borderless mb-0">
                                                                    <tbody>
                                                                    <tr>
                                                                        <th scope="row">String</th>
                                                                        @for ($i = 13; $i <= 24; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_voltage > 0)
                                                                                    <th style="font-size: 16px;font-weight:600;">{{ 'PV'.$i }}</td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Voltage</th>
                                                                        @for ($i = 13; $i <= 24; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_voltage > 0)
                                                                                    <td>{{ number_format((float)$pv->mppt_voltage, 2, '.', '') }}
                                                                                        V
                                                                                    </td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Current</th>
                                                                        @for ($i = 13; $i <= 24; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_current > 0)
                                                                                    <td>{{ number_format((float)$pv->mppt_current, 2, '.', '') }}
                                                                                        A
                                                                                    </td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Power</th>
                                                                        @for ($i = 13; $i <= 24; $i++)
                                                                            @foreach ($item1->pv_values as $pv)
                                                                                @if($pv->mppt_number == $i && $pv->mppt_power > 0)
                                                                                    <td>{{ number_format((float)$pv->mppt_power, 2, '.', '') }}
                                                                                        kW
                                                                                    </td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endfor
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> <!-- end col-->
                                            </div> <!-- end row-->
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($plantInverters) > 1)
                                    <a class="carousel-control-prev leftPVCarousel" href="#carousel" role="button"
                                       data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next rightPVCarousel" href="#carousel" role="button"
                                       data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="tab-pane inverterTabPane" id="messages3">
                            <!-- search and Dropdown -->
                            <div class="drop_search_vt">
                                <div class="multiselect">
                                    <div class="selectBox" onclick="showCheckboxes()">
                                        <select>
                                            <option>Select Parameter</option>
                                        </select>
                                        <div class="overSelect"></div>
                                    </div>
                                    <div id="checkboxes">
                                        <label for="one">
                                            <input type="checkbox" class="emiCheckBox" name="emiCheckBox[]" id="one"
                                                   value="pv_temperature"/>PV Temperature</label>
                                        <label for="two">
                                            <input type="checkbox" class="emiCheckBox" name="emiCheckBox[]" id="two"
                                                   value="ambient_temperature"/>Ambient Temperature</label>
                                        <label for="three">
                                            <input type="checkbox" class="emiCheckBox" name="emiCheckBox[]" id="three"
                                                   value="irradiance" checked/>Irradiance</label>
                                        <label for="three">
                                            <input type="checkbox" class="emiCheckBox" name="emiCheckBox[]" id="four"
                                                   value="wind_speed"/>Wind Speed</label>
                                    </div>
                                </div>
                                <button type="submit" id="searchEMICheckBox">Search</button>
                            </div>
                            <div class="cland_card_vt">
                                <div class="day_month_year_vt" id="emi_day_month_year_vt_day">
                                    <button class="btn_left_vt"><i id="emiGraphPreviousDay"
                                                                   class="fa fa-caret-left"></i></button>
                                    <div
                                        class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-emi mt10">
                                        <i class="fa fa-calendar-alt"></i>
                                        <input type="text" autocomplete="off" name="emiGraphDay" id="emiGraphDay"
                                               placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                    <button class="btn_right_vt"><i id="emiGraphForwardDay"
                                                                    class="fa fa-caret-right"></i></button>
                                </div>
                                {{-- <div class="day_month_year_vt" id="emi_day_month_year_vt_month" style="display: none;">
                                <button class="btn_left_vt"><i id="emiGraphPreviousMonth" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-emi mt10">
                                        <i class="fa fa-calendar-alt"></i>
                                        <input type="text" autocomplete="off" name="emiGraphMonth" placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button class="btn_right_vt"><i id="emiGraphForwardMonth" class="fa fa-caret-right"></i></button>
                            </div>
                            <div class="day_month_year_vt" id="emi_day_month_year_vt_year" style="display: none;">
                                <button class="btn_left_vt"><i id="emiGraphPreviousYear" class="fa fa-caret-left"></i></button>
                                <div class="mt40">
                                    <div class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-emi mt10">
                                        <i class="fa fa-calendar-alt"></i>
                                        <input type="text" autocomplete="off" name="emiGraphYear" placeholder="Select" class="c-datepicker-data-input" value="">
                                    </div>
                                </div>
                                <button class="btn_right_vt"><i id="emiGraphForwardYear" class="fa fa-caret-right"></i></button>
                            </div> --}}
                            </div>
                            <div class="pt-3">
                                <div class="spinner-border text-success emiGraphSpinner plantGraphSpinner"
                                     role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div class="emiGraphError plantGraphError" style="display: none;">
                                    <span>Some Error Occured</span>
                                </div>
                                <div class="col-md-12" id="emiGraphDivv" style="margin-top: 50px !important;">
                                    {{-- <div class="bg_blue2_vt emiGraphDiv" id="emiGraphDivv"> --}}
                                    <div id="plantsEMIChart"></div>
                                    {{-- </div> --}}
                                    <div class="bg_blue2_vt noEMIDiv" style="display: none;">
                                        <h3>No Alerts to Show</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="allInverterDiv" style="display: none;">
            <div class="col-md-12">
                <div class="part_to_vt">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>All Inverters</h4>
                            <div class="table-responsive one_border_vt mt-2">
                                <table class="table mb-0">
                                    <thead class="thead-light">

                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th class="ac_vt" scope="row">AC Output Total Power</th>
                                        <td>{{ $plant->ac_output_power }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ac_vt" scope="row">Daily Generation (Active)</th>
                                        <td>{{ $plant->daily_generation }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ac_vt" scope="row">Monthly Generation</th>
                                        <td>{{ $plant->monthly_generation }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ac_vt" scope="row">Yearly Generation</th>
                                        <td>{{ $plant->annual_generation }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ac_vt" scope="row">Total Generation</th>
                                        <td>{{ $plant->total_generation }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive mt-4">
                                <table class="table mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Work State</th>
                                        <th>Today Generation</th>
                                        <th>DC Input Power</th>
                                        <th>Generation Yesterday</th>
                                        <th>Generation Last Month</th>
                                        <th>Generation Last Year</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($plantInverters as $key => $item)
                                        <tr>
                                            <th scope="row">{{ $item->dv_inverter_serial_no }}</th>
                                            <td title="{{ $item->inverter_state }}">{{ Str::limit($item->inverter_state, $workStateCharLimit)}}</td>
                                            <td>{{ $item->today_generation }}</td>
                                            <td>{{ $item->dc_input_power }}</td>
                                            <td>{{ $item->yesterday_generation }}</td>
                                            <td>{{ $item->last_month_generation }}</td>
                                            <td>{{ $item->last_year_generation }}</td>
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

        <div class="row">
            <div class="col-md-4 pr-md-0">
                <div class="card-stat-vt  mb-2" style="padding-bottom: 0;">
                    <div class="card-header">
                        <h2 class="All-graph-heading-vt">Plants</h2>
                        <a class="eidt-profil-vt" href="{{ route('admin.edit.plant', ['id' => $plant->id]) }}"
                           style="padding-top: 5px; padding-bottom: 5px;">Edit </a>
                    </div>
                    <br>
                    <div class="stat-area-hed-vt" style="background: none;">
                        <img
                            src="{{ $plant->faultLevel != 0 ? asset('assets/images/plant_detail_fault.png') : ($plant->is_online == 'P_Y' ? asset('assets/images/plant_detail_partial_online.png') :  ($plant->is_online == 'Y' ? asset('assets/images/plant_detail_online.png') : asset('assets/images/plant_detail_offline.png'))) }}"
                            alt="" style="
                    position: absolute;
                    z-index: 99;
                    width: 39px;
                    top: 75px;
                    right: 15px;
                ">
                        <h4 style="position: absolute;z-index: 9;color: #fff;">{{$plant->plant_name}}</h4>
                        <img style="    background-size: cover;
                        position: absolute;
                        z-index: 1;
                        width: 100%;
                        padding: 0 10px;
                        border-radius: 20px;
                        height: 140px;" src="{{ asset('assets/images/050121143546.70017_6A.jpeg') }}"
                             alt="Plant Picture" width="50">
                    </div>

                    <p class="mt-2">Plant Type<span>{{ $plant->plant_type }}</span></p>

                    <p>Designed Capacity<span>{{ $plant->capacity }} kW</span></p>

                    <p>Daily Expected Generation<span>{{ $plant->expected_generation }} kWh</span></p>

                    <p>Contact<span>{{ $plant->phone }}</span></p>

                    <p>Benchmark Price<span> {{ $plant->currency }} {{ $plant->benchmark_price }}/unit</span></p>

                    <p>Company<span>{{ $plant->company['company_name'] }}</span></p>

                    <p>Created Date<span>{{ date('h:i A, d-m-Y', strtotime($plant->created_at)) }}</span></p>


                </div>
                <div class="weth_area_vt">

                    <ul>
                        @for($i=0;$i<count($weatherDetails);$i++) @if($i==0)
                            <li class="one_wather" style="width: 33.3%;">

                                <h5 class="m-0 mb-1">{{$weatherDetails[0]['day']}}</h5>
                                <p>{{$weatherDetails[0]['todayMin']}}/{{$weatherDetails[0]['todayMax']}}* <img
                                        src="http://openweathermap.org/img/w/{{ $weatherDetails[0]['icon'] }}.png"
                                        alt="" width="50px"></p>

                                <h3>Sunrise <span>{{$weatherDetails[0]['sunrise']}}</span></h3>
                                <h3>Sunset <span>{{$weatherDetails[0]['sunset']}}</span></h3>
                            </li>
                        @endif
                        @if($i !== 0)
                            <li>

                                <h5>{{$weatherDetails[$i]['day']}}</h5>
                                <p>{{$weatherDetails[$i]['todayMin']}}/{{$weatherDetails[$i]['todayMax']}}*</p>
                                <img src="http://openweathermap.org/img/w/{{ $weatherDetails[$i]['icon'] }}.png" alt=""
                                     width="40px">
                            </li>
                        @endif
                        {{-- <li>--}}
                        {{-- <h5>Wednesday</h5>--}}
                        {{-- <p>23/23*</p>--}}
                        {{-- <img src="{{ asset('assets/images/weather.png') }}" alt="" width="40px">--}}
                        {{-- </li>--}}
                        {{-- <li>--}}
                        {{-- <h5>Wednesday</h5>--}}
                        {{-- <p>23/23*</p>--}}
                        {{-- <img src="{{ asset('assets/images/weather.png') }}" alt="" width="40px">--}}
                        {{-- </li>--}}
                        @endfor
                    </ul>

                </div>
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <div class="card_box_vt min_hig_vt_box">
                            <div class="row">
                                <div class="ht3_plant">
                                    <h2>Alerts</h2>
                                </div>
                                <div class="env_benefits_vt">
                                    <div class="day_month_year_vt" id="alert_day_month_year_vt_day">
                                        <button class="btn_left_vt"><i id="alertGraphPreviousDay"
                                                                       class="fa fa-caret-left"></i></button>
                                        <div
                                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-alert mt10">
                                            <i class="fa fa-calendar-alt"></i>
                                            <input type="text" autocomplete="off" name="alertGraphDay"
                                                   id="alertGraphDay" placeholder="Select"
                                                   class="c-datepicker-data-input" value="">
                                        </div>
                                        <button class="btn_right_vt"><i id="alertGraphForwardDay"
                                                                        class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_month_year_vt" id="alert_day_month_year_vt_month"
                                         style="display: none;">
                                        <button class="btn_left_vt"><i id="alertGraphPreviousMonth"
                                                                       class="fa fa-caret-left"></i></button>
                                        <div class="mt40">
                                            <div
                                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-alert mt10">
                                                <i class="fa fa-calendar-alt"></i>
                                                <input type="text" autocomplete="off" name="alertGraphMonth"
                                                       placeholder="Select" class="c-datepicker-data-input" value="">
                                            </div>
                                        </div>
                                        <button class="btn_right_vt"><i id="alertGraphForwardMonth"
                                                                        class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_month_year_vt" id="alert_day_month_year_vt_year"
                                         style="display: none;">
                                        <button class="btn_left_vt"><i id="alertGraphPreviousYear"
                                                                       class="fa fa-caret-left"></i></button>
                                        <div class="mt40">
                                            <div
                                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-alert mt10">
                                                <i class="fa fa-calendar-alt"></i>
                                                <input type="text" autocomplete="off" name="alertGraphYear"
                                                       placeholder="Select" class="c-datepicker-data-input" value="">
                                            </div>
                                        </div>
                                        <button class="btn_right_vt"><i id="alertGraphForwardYear"
                                                                        class="fa fa-caret-right"></i></button>
                                    </div>

                                    <div class="day_my_btn_vt" id="alert_day_my_btn_vt">
                                        <button class="day_bt_vt active" id="day">day</button>
                                        <button class="month_bt_vt" id="month">month</button>
                                        <button class="month_bt_vt" id="year">Year</button>
                                    </div>
                                </div>
                                <div class="spinner-border text-success alertGraphSpinner plantGraphSpinner"
                                     role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div class="alertGraphError plantGraphError" style="display: none;">
                                    <span>Some Error Occured</span>
                                </div>
                                <div class="col-md-12">
                                    <div class="bg_blue2_vt alertGraphDiv" id="alertGraphDivv">
                                        <div id="plantsAlertChart"></div>
                                        <h3>
                                            <div class="totalAlertDiv">0</div>
                                            <span>Total Alerts</span>
                                        </h3>
                                    </div>
                                    <div class="bg_blue2_vt noAlertDiv" style="display: none;">
                                        <h3>No Alerts to Show</h3>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="data_pla_vt alertGraphDiv">
                                        <ul>
                                            <li><samp class="blue5_vt"></samp> Alarm : <span
                                                    class="totalAlarmDiv"> 0</span></li>
                                            <li><samp class="blue4_vt"></samp> Fault : <span
                                                    class="totalFaultDiv"> 0</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="card_box_vt">
                            <div class="row">
                                <div class="ht3_plant">
                                    <h2>Environmental Benefits</h2>
                                </div>
                                <div class="env_benefits_vt">
                                    <div class="day_month_year_vt" id="env_day_month_year_vt_day">
                                        <button class="btn_left_vt"><i id="envGraphPreviousDay"
                                                                       class="fa fa-caret-left"></i></button>
                                        <div
                                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-env mt10">
                                            <i class="fa fa-calendar-alt"></i>
                                            <input type="text" autocomplete="off" name="envGraphDay" id="envGraphDay"
                                                   placeholder="Select" class="c-datepicker-data-input" value="">
                                        </div>
                                        <button class="btn_right_vt"><i id="envGraphForwardDay"
                                                                        class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_month_year_vt" id="env_day_month_year_vt_month"
                                         style="display: none;">
                                        <button class="btn_left_vt"><i id="envGraphPreviousMonth"
                                                                       class="fa fa-caret-left"></i></button>
                                        <div class="mt40">
                                            <div
                                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-env mt10">
                                                <i class="fa fa-calendar-alt"></i>
                                                <input type="text" autocomplete="off" name="envGraphMonth"
                                                       placeholder="Select" class="c-datepicker-data-input" value="">
                                            </div>
                                        </div>
                                        <button class="btn_right_vt"><i id="envGraphForwardMonth"
                                                                        class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_month_year_vt" id="env_day_month_year_vt_year"
                                         style="display: none;">
                                        <button class="btn_left_vt"><i id="envGraphPreviousYear"
                                                                       class="fa fa-caret-left"></i></button>
                                        <div class="mt40">
                                            <div
                                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-env mt10">
                                                <i class="fa fa-calendar-alt"></i>
                                                <input type="text" autocomplete="off" name="envGraphYear"
                                                       placeholder="Select" class="c-datepicker-data-input" value="">
                                            </div>
                                        </div>
                                        <button class="btn_right_vt"><i id="envGraphForwardYear"
                                                                        class="fa fa-caret-right"></i></button>
                                    </div>
                                    <div class="day_my_btn_vt" id="env_day_my_btn_vt">
                                        <button class="day_bt_vt active" id="day">day</button>
                                        <button class="month_bt_vt" id="month">month</button>
                                        <button class="month_bt_vt" id="year">Year</button>
                                    </div>
                                </div>
                                <div class="spinner-border text-success envGraphSpinner plantGraphSpinner"
                                     role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div class="envGraphError plantGraphError" style="display: none;">
                                    <span>Some Error Occured</span>
                                </div>
                                <div class="col-md-6">
                                    <div class="environmental_vt">
                                        <p>Trees Planted</p>
                                        <img src="{{ asset('assets/images/tree.png') }}" alt="tower">
                                        <h5 style="color: #0A9405;" class="envTreePlanting">0 <span>tree(s)</span></h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="environmental_vt" style="border: none;">
                                        <p>CO<sub>2</sub> Emission Reduction</p>
                                        <img src="{{ asset('assets/images/factory.png') }}" alt="tower"
                                             style="margin-top: 10px;">
                                        <h5 style="color: #F6A944;margin-top: 8px;" class="envEmissionReduction">145
                                            <span>T</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="data_pla_vt">
                                        <ul>
                                            <li><samp class="blue3_vt"></samp> Total Trees Planted :
                                                <strong>{{ $envArray['tree'] }}</strong>
                                            </li>
                                            <li><samp class="blue4_vt"></samp> Total CO<sub style="padding-right: 2px;">2 </sub>
                                                Emission Reduction :
                                                <strong>{{ $envArray['c02'] }}</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card_box_vt">
                            <div class="row">
                                <div class="ht2_plants pl-3">
                                    <h2>Power</h2>
                                </div>
                                <div class="col-md-12">
                                    <div class="bg_blue2_vt">
                                        <div id="plantsPowerChart" style="height: 250px;width: 250px;"></div>
                                        <div class="plantsTodayYesterdayChart">
                                            <h5><span>{{ $powerArray['current_power'] }}</span> Total Power</h5>
                                            <h4><span>{{ $powerArray['total_capacity'] }}</span> Installed Capacity</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 mb-2 pl-md-0">
                        <div class="bg_back_vt">
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <div class="card_box_vt">
                                        <div class="row">
                                            <div class="ht2_plants pl-2">
                                                <h2>Generation</h2>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="bg_blue2_vt">
                                                    <img src="{{ asset('assets/images/img_01.png') }}" alt="">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="data_name_vt">
                                                    <ul>
                                                        <li><span>Today's Generation</span> {{ $daily['generation'] }}
                                                        </li>
                                                        <li><span>Monthly Generation</span> {{ $monthly['generation'] }}
                                                        </li>
                                                        <li><span>Yearly Generation</span> {{ $yearly['generation'] }}
                                                        </li>
                                                        <li><span>Total Generation</span> {{ $total['generation'] }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-box-->
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="card_box_vt">
                                        <div class="row">
                                            <div class="ht2_plants">
                                                <h2>Saving</h2>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="bg_blue2_vt">
                                                    <img src="{{ asset('assets/images/img_02.png') }}" alt="">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="data_name_vt">
                                                    <ul>
                                                        <li><span>Today's Saving</span> {{ $daily['revenue'] }}</li>
                                                        <li><span>Monthly Saving</span> {{ $monthly['revenue'] }}</li>
                                                        <li><span>Yearly Saving</span> {{ $yearly['revenue'] }}</li>
                                                        <li><span>Total Saving</span> {{ $total['revenue'] }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-box-->
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($plant->system_type_id != 1)
                        <div class="col-md-4">
                            <div class="tot_deta_vt">
                                <div class="row">
                                    <div class="ht2_plants">
                                        <h2>Consumption</h2>
                                    </div>
                                    <div class="col-md-6" style="position: relative;">
                                        <div class="bg_blue2_vt plantsConsumptionChart_vt">
                                            <div id="plantsConsumptionChart" style="height: 250px;width: 250px;"></div>
                                            <div class="plantsConsumptionChart">
                                                <h4><span>{{ $consumptionArray['current_consumption'] }}</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data_name_vt">
                                            <ul>
                                                <li><span>Daily Consumption</span> {{ $daily['consumption'] }}</li>
                                                <li><span>Monthly Consumption</span> {{ $monthly['consumption'] }}</li>
                                                <li><span>Yearly Consumption</span> {{ $yearly['consumption'] }}</li>
                                                <li><span>Total Consumption</span> {{ $total['consumption'] }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 pl-md-0">
                            <div class="bg_back_vt">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="card_box_vt">
                                            <div class="row">
                                                <div class="ht2_plants pl-2">
                                                    <h2>Energy Buy</h2>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="bg_blue2_vt">
                                                        <img src="{{ asset('assets/images/img_03.png') }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="data_name_vt">
                                                        <ul>
                                                            <li><span>Daily Bought</span> {{ $daily['boughtEnergy'] }}
                                                            </li>
                                                            <li>
                                                                <span>Monthly Bought</span> {{ $monthly['boughtEnergy'] }}
                                                            </li>
                                                            <li><span>Yearly Bought</span> {{ $yearly['boughtEnergy'] }}
                                                            </li>
                                                            <li><span>Total Bought</span> {{ $total['boughtEnergy'] }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- end card-box-->
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <div class="card_box_vt">
                                            <div class="row">
                                                <div class="ht2_plants">
                                                    <h2>Energy Sell</h2>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="bg_blue2_vt">
                                                        <img src="{{ asset('assets/images/img_04.png') }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="data_name_vt">
                                                        <ul>
                                                            <li><span>Daily Sell</span> {{ $daily['sellEnergy'] }}</li>
                                                            <li><span>Monthly Sell</span> {{ $monthly['sellEnergy'] }}
                                                            </li>
                                                            <li><span>Yearly Sell</span> {{ $yearly['sellEnergy'] }}
                                                            </li>
                                                            <li><span>Total Sell</span> {{ $total['sellEnergy'] }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- end card-box-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @if($plant->meter_type == 'Huawei' && $plant->plant_has_emi == 'Y')
                    <div class="card">
                        <div class="card-header">
                            <h3>Environmental Monitoring - Realtime</h3>
                        </div>
                        <div class="bg_back_vt">
                            <div class="row">
                                <div class="col-sm-12 col-md-3 pr-0">
                                    <div class="card_box_vt">
                                        <div class="margin_vt">
                                            <div class="img_therr">
                                                <img src="{{ asset('assets/images/wind_speed.png') }}" alt="">
                                            </div>
                                            <div class="dataname_vt">
                                                <ul>
                                                    <li>
                                                        <span> Wind Speed </span> {{ $plant->latest_inverter_emi_details && $plant->latest_inverter_emi_details->wind_speed ? $plant->latest_inverter_emi_details->wind_speed : '---' }}
                                                        m/s
                                                    </li>
                                                    <li>
                                                        <span>Wind Direction</span> {{ $plant->latest_inverter_emi_details && $plant->latest_inverter_emi_details->wind_direction ? $plant->latest_inverter_emi_details->wind_direction : '---' }}
                                                        North
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div> <!-- end card-box-->
                                </div>
                                <div class="col-sm-12 col-md-3 px-0">
                                    <div class="card_box_vt">
                                        <div class="margin_vt">
                                            <div class="img_therr">
                                                <img src="{{ asset('assets/images/pv_temprature.png') }}" alt="">
                                            </div>
                                            <div class="dataname_vt">
                                                <ul>
                                                    <li>
                                                        <span>PV Temprature</span> {{ $plant->latest_inverter_emi_details && $plant->latest_inverter_emi_details->pv_temperature ? $plant->latest_inverter_emi_details->pv_temperature : '---' }}
                                                        <sup>o</sup>C
                                                    </li>
                                                    <li>
                                                        <span>Ambient Temprature</span> {{ $plant->latest_inverter_emi_details && $plant->latest_inverter_emi_details->temperature ? $plant->latest_inverter_emi_details->temperature : '---' }}
                                                        <sup>o</sup>C
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div> <!-- end card-box-->
                                </div>
                                <div class="col-sm-12 col-md-6 pl-0">
                                    <div class="card_box_vt">
                                        <div class="margin2_vt">
                                            <div class="img_therr">
                                                <img src="{{ asset('assets/images/Irradiance.png') }}" alt="">
                                            </div>
                                            <div class="dataname_vt">
                                                <ul>
                                                    <li>
                                                        <span>Irradiance</span> {{ $plant->latest_inverter_emi_details && $plant->latest_inverter_emi_details->radiant_line ? $plant->latest_inverter_emi_details->radiant_line : '---' }}
                                                        W/m<sup>2</sup></li>
                                                    <li>
                                                        <span>Horizontal Irradiance</span> {{ $plant->latest_inverter_emi_details && $plant->latest_inverter_emi_details->horiz_radiant_line ? $plant->latest_inverter_emi_details->horiz_radiant_line : '---' }}
                                                        W/m<sup>2</sup></li>
                                                    <li>
                                                        <span>Total Horizontal radiation exposure</span> {{ $plant->latest_inverter_emi_details && $plant->latest_inverter_emi_details->horiz_radiant_total ? $plant->latest_inverter_emi_details->horiz_radiant_total : '---' }}
                                                        W/m<sup>2</sup></li>
                                                </ul>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row margin3_vt">
                                                    <div class="col-md-12">
                                                        <div class="data_name_vt pt-2">
                                                            <ul>
                                                                <li>
                                                                    <span>Today's Irradiance</span> {{ $daily['irradiance'] }}
                                                                    kWh/m<sup>2</sup></li>
                                                                <li>
                                                                    <span>Monthly Irradiance</span> {{ $monthly['irradiance'] }}
                                                                    kWh/m<sup>2</sup></li>
                                                                <li>
                                                                    <span>Yearly Irradiance</span> {{ $yearly['irradiance'] }}
                                                                    kWh/m<sup>2</sup></li>
                                                                <li>
                                                                    <span>Total Irradiance</span> {{ $total['irradiance'] }}
                                                                    kWh/m<sup>2</sup></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end card-box-->
                                        </div>
                                    </div> <!-- end card-box-->
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3>Actual Generation / Expected Generation</h3>
                    </div>

                    <div class="day_month_year_vt" id="expected_day_month_year_vt_day">
                        <button class="btn_left_vt"><i id="expectedGraphPreviousDay" class="fa fa-caret-left"></i>
                        </button>
                        <div
                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-expected mt10">
                            <i class="fa fa-calendar-alt"></i>
                            <input type="text" autocomplete="off" name="expectedGraphDay" id="expectedGraphDay"
                                   placeholder="Select" class="c-datepicker-data-input" value="">
                        </div>
                        <button class="btn_right_vt"><i id="expectedGraphForwardDay" class="fa fa-caret-right"></i>
                        </button>
                    </div>
                    <div class="day_month_year_vt" id="expected_day_month_year_vt_month" style="display: none;">
                        <button class="btn_left_vt"><i id="expectedGraphPreviousMonth" class="fa fa-caret-left"></i>
                        </button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-expected mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="expectedGraphMonth" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button class="btn_right_vt"><i id="expectedGraphForwardMonth" class="fa fa-caret-right"></i>
                        </button>
                    </div>
                    <div class="day_month_year_vt" id="expected_day_month_year_vt_year" style="display: none;">
                        <button class="btn_left_vt"><i id="expectedGraphPreviousYear" class="fa fa-caret-left"></i>
                        </button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-expected mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="expectedGraphYear" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button class="btn_right_vt"><i id="expectedGraphForwardYear" class="fa fa-caret-right"></i>
                        </button>
                    </div>

                    <div class="day_my_btn_vt" id="expected_day_my_btn_vt">
                        <button class="day_bt_vt active" id="day">day</button>
                        <button class="month_bt_vt" id="month">month</button>
                        <button class="month_bt_vt" id="year">Year</button>
                    </div>
                    <div class="spinner-border text-success expectedGraphSpinner plantGraphSpinner" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="expectedGraphError plantGraphError" style="display: none;">
                        <span>Some Error Occured</span>
                    </div>
                    {{-- <div class="card-box" dir="ltr" id="expectedGraphDiv">--}}
                    {{-- <div id="expectedContainer"></div>--}}
                    {{-- <br>--}}
                    {{-- </div>--}}
                    {{-- <div class="expectedGraphDiv">--}}
                    {{-- <h3 class="actualPercentage">5.2% <span>173.46 MWh</span></h3>--}}
                    {{-- <div class="data_pla_vt">--}}
                    {{-- <ul>--}}
                    {{-- <li><samp class="blue3_vt"></samp> Actual : <strong class="actualTotalValue">208.15 MWh</strong></li>--}}
                    {{-- <li><samp class="blue5_vt"></samp> Expected <strong class="expectedTotalValue">208.15 MWh</strong></li>--}}
                    {{-- </ul>--}}
                    {{-- </div>--}}
                    {{-- </div> --}}
                    <div class="element_style">
                        <div class="card-box" dir="ltr" id="expectedGraphDiv">
                            <div id="expectedContainer"></div>
                            {{-- <br>--}}
                        </div>
                    </div>
                    <div class="expectedGraphDiv">
                        <h3 class="actualPercentage">5.2% <span>173.46 MWh</span></h3>
                        <div class="data_pla_vt">
                            <ul>
                                <li><samp class="blue3_vt"></samp> Actual : <strong class="actualTotalValue"> 208.15
                                        MWh</strong></li>
                                <li><samp class="blue5_vt"></samp> Expected : <strong class="expectedTotalValue"> 208.15
                                        MWh</strong></li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>History</h3>
                    </div>
                    <!-- search and Dropdown -->
                    <div class="drop_search_history_vt">
                        <div class="multiselect">
                            <div class="selectBox" onclick="showCheckbox()">
                                <select>
                                    <option>Select Parameter</option>
                                </select>
                                <div class="overSelect"></div>
                            </div>
                            <div id="checkbox">
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                           value="generation" checked/>Generation</label>
                                @if($plant->system_type_id != 1)
                                    <label for="one">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                               value="consumption" checked/>Consumption</label>
                                    <label for="one">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                               value="grid" checked/>Grid</label>
                                    <label for="one">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                               value="buy" checked/>Buy</label>
                                    <label for="one">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                               value="sell" checked/>Sell</label>
                                @endif
                                <label for="one">
                                    <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                           value="saving"/>Cost Saving</label>
                                @if($plant->plant_has_emi == 'Y')
                                    <label for="one">
                                        <input type="checkbox" class="historyCheckBox" name="historyCheckBox[]" id="one"
                                               value="irradiance" checked/>Irradiance</label>
                                @endif
                            </div>
                        </div>
                        <button type="submit" id="searchHistoryCheckBox">Search</button>
                    </div>
                    <div>
                        <button id="ExportGenerationGraph">Export</button>
                    </div>
                    <div class="day_month_year_vt" id="history_day_month_year_vt_day">
                        <button class="btn_left_vt"><i id="historyGraphPreviousDay" class="fa fa-caret-left"></i>
                        </button>
                        <div
                            class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthDayPicker-single-history mt10">
                            <i class="fa fa-calendar-alt"></i>
                            <input type="text" autocomplete="off" name="historyGraphDay" id="historyGraphDay"
                                   placeholder="Select" class="c-datepicker-data-input" value="">
                        </div>
                        <button class="btn_right_vt"><i id="historyGraphForwardDay" class="fa fa-caret-right"></i>
                        </button>
                    </div>
                    <div class="day_month_year_vt" id="history_day_month_year_vt_month" style="display: none;">
                        <button class="btn_left_vt"><i id="historyGraphPreviousMonth" class="fa fa-caret-left"></i>
                        </button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearMonthPicker-single-history mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="historyGraphMonth" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button class="btn_right_vt"><i id="historyGraphForwardMonth" class="fa fa-caret-right"></i>
                        </button>
                    </div>
                    <div class="day_month_year_vt" id="history_day_month_year_vt_year" style="display: none;">
                        <button class="btn_left_vt"><i id="historyGraphPreviousYear" class="fa fa-caret-left"></i>
                        </button>
                        <div class="mt40">
                            <div
                                class="c-datepicker-date-editor c-datepicker-single-editor J-yearPicker-single-history mt10">
                                <i class="fa fa-calendar-alt"></i>
                                <input type="text" autocomplete="off" name="historyGraphYear" placeholder="Select"
                                       class="c-datepicker-data-input" value="">
                            </div>
                        </div>
                        <button class="btn_right_vt"><i id="historyGraphForwardYear" class="fa fa-caret-right"></i>
                        </button>
                    </div>

                    <div class="day_my_btn_vt" id="history_day_my_btn_vt">
                        <button class="day_bt_vt active" id="day">day</button>
                        <button class="month_bt_vt" id="month">month</button>
                        <button class="month_bt_vt" id="year">Year</button>
                    </div>
                    <div class="spinner-border text-success historyGraphSpinner plantGraphSpinner" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="historyGraphError plantGraphError" style="display: none;">
                        <span>Some Error Occured</span>
                    </div>
                    <div class="history-card-box" dir="ltr" id="historyGraphDiv">
                        <div id="historyContainer"></div>
                        <br>
                    </div>
                    <div class="history_gr_vt">
                        <ul>
                            <li><samp class="color1_vt"></samp> Generation : <strong
                                    class="generationTotalValue"></strong></li>
                            @if($plant->system_type_id != 1)
                                <li><samp class="color3_vt"></samp> Consumption : <strong
                                        class="consumptionTotalValue"></strong></li>
                                <li><samp class="color2_vt"></samp> Grid : <strong class="gridTotalValue"></strong></li>
                                <li><samp class="color4_vt"></samp> Buy : <strong class="buyTotalValue"></strong></li>
                                <li><samp class="color5_vt"></samp> Sell : <strong class="sellTotalValue"></strong></li>
                            @endif
                            <li><samp class="color6_vt"></samp> Cost Saving : <strong class="savingTotalValue"></strong>
                            </li>
                            @if($plant->meter_type == 'Huawei' && $plant->plant_has_emi == 'Y')
                                <li><samp class="color7_vt"></samp> Irradiance : <strong
                                        class="irradianceTotalValue"></strong></li>
                            @endif
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <!-- end row -->
    </div>

    <div id="alertGraphDiv">

    </div>

    <script type="text/javascript" src="{{ asset('assets/js/echarts.min.js')}}"></script>

    <script>
        var plantsPowerGraphData = {!!json_encode($plantPowerGraphData)!!};
        var plantsConsumptionGraphData = {!!json_encode($plantConsumptionGraphData)!!};
        var plantID = {!!json_encode($plant->id)!!};
        var graphicalDataInverterSerialNo = {!!json_encode($graphicalDataInverterSerialNo)!!};
        var plantAllInvertersArray = <?php echo json_encode($plantAllInvertersArray); ?>;
        var emiCheckBoxArray = new Array();
        var pvSerialNo = '';
        var Timedata = [];
        var TimeDetails = [];
        var generationdata = [];
        var TimeType = [];
        var costsaving = [];
        var InverterDCOutput = [];
        var InverterTimeType = [];
        var InverterTimeData = [];
        var InverterName = [];
        var InputDcPower = new Array();
        var InverterDate = [];
        var NoOfInverters = [];
        var InverteroutputPower = [];
        var Inverter = [];
        var timeData1 = '';
        window.onload = function () {
            // InverteroutputPower
            // Inverter = Object.entries(InverteroutputPower);

//     var myChart;
//     $('#ExportInverterGraph').click(function () {
//     var _headers = ['Date','Time', 'Inverter Sn','DC Output Power','Input Dc Power'];
//     // prepare CSV data
//     var csvData = new Array();

//     csvData.push(_headers);
//         console.log("asg" + typeof(InverterName));
//         // console.log("asg" + (NoOfInverters));
//         console.log ("abcdefghijklmnop" + Object.entries(InverteroutputPower));

//     for (i = 0; i < NoOfInverters.length; i++) {
//         // csvData.push(new Array(InverterDate,InverterTimeData[j], InverterName[i][j],InverajaxteroutputPower[j],InputDcPower[j]));
//         for (j = 0; j < InverterTimeData.length; j++) {
//         // console.log("outputpower aaaaaaacccccccccc" + typeof(InverteroutputPower[j]));
//             csvData.push(new Array(InverterDate,InverterTimeData[j], InverterName[i],InverteroutputPower[j],InputDcPower[j]));
//             // for(k = 0; k < InverteroutputPower; k++)
//             // {
//             // console.log("output poweraaaaaaaaaaaddddddddddd" + InverteroutputPower[j][k]);
//             // csvData.push(new Array(InverteroutputPower[j][k],InputDcPower[j]));
//             // }
//         }

//     }

//     console.log(csvData);

//     var lineArray = [];
//     csvData.forEach(function (infoArray, index) {
//         var line = infoArray.join(",");
//         lineArray.push(index == 0 ? "\uFEFF" + line : line); // 為了讓Excel開啟csv，需加上BOM：\uFEFF
//     });
//     var csvContent = lineArray.join("\n");

//     console.log(csvContent);
//     //var encodedUri = encodeURI(csvContent);
//     //window.open(encodedUri);

//     // download stuff
//     var blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
//     var link = document.createElement("a");

//     if (link.download !== undefined) { // feature detection
//         // Browsers that support HTML5 download attribute
//         link.setAttribute("href", window.URL.createObjectURL(blob));
//         link.setAttribute("download", "data.csv");
//         link.setAttribute("hidden", true);
//     }
//     else {
//         // it needs to implement server side export
//         console.log('error');
//         link.setAttribute("href", "#");
//     }
//     //link.innerHTML = "Export to CSV";
//     //document.body.appendChild(link);
//     link.click();

// });


            $('#ExportGenerationGraph').click(function () {
                var _headers = [timeData1, 'GenerationData'];
                // var _headers = ['Day', 'GenerationData', "CostSaving"];
                // prepare CSV data
                var csvData = new Array();

                csvData.push(_headers);

                for (i = 0; i < TimeDetails.length; i++) {
                    csvData.push(new Array(TimeDetails[i], generationdata[i]));
                }

                console.log(csvData);

                var lineArray = [];
                csvData.forEach(function (infoArray, index) {
                    var line = infoArray.join(",");
                    lineArray.push(index == 0 ? "\uFEFF" + line : line); // 為了讓Excel開啟csv，需加上BOM：\uFEFF
                });
                var csvContent = lineArray.join("\n");

                console.log(csvContent);
                //var encodedUri = encodeURI(csvContent);
                //window.open(encodedUri);

                // download stuff
                var blob = new Blob([csvContent], {type: "text/csv;charset=utf-8;"});
                var link = document.createElement("a");

                if (link.download !== undefined) { // feature detection
                    // Browsers that support HTML5 download attribute
                    link.setAttribute("href", window.URL.createObjectURL(blob));
                    link.setAttribute("download", "data.csv");
                    link.setAttribute("hidden", true);
                } else {
                    // it needs to implement server side export
                    console.log('error');
                    link.setAttribute("href", "#");
                }
                //link.innerHTML = "Export to CSV";
                //document.body.appendChild(link);
                link.click();

            });

            var currDate = getCurrentDate();

            $('input[name="historyGraphDay"]').val(currDate.todayDate);
            $('input[name="historyGraphMonth"]').val(currDate.todayMonth);
            $('input[name="historyGraphYear"]').val(currDate.todayYear);

            $('input[name="expectedGraphDay"]').val(currDate.todayDate);
            $('input[name="expectedGraphMonth"]').val(currDate.todayMonth);
            $('input[name="expectedGraphYear"]').val(currDate.todayYear);

            $('input[name="envGraphDay"]').val(currDate.todayDate);
            $('input[name="envGraphMonth"]').val(currDate.todayMonth);
            $('input[name="envGraphYear"]').val(currDate.todayYear);

            $('input[name="alertGraphDay"]').val(currDate.todayDate);
            $('input[name="alertGraphMonth"]').val(currDate.todayMonth);
            $('input[name="alertGraphYear"]').val(currDate.todayYear);

            $('input[name="emiGraphDay"]').val(currDate.todayDate);
            $('input[name="pvGraphDay"]').val(currDate.todayDate);

            $('input[name="inverterGraphDay"]').val(currDate.todayDate);
            $('input[name="inverterGraphMonth"]').val(currDate.todayMonth);
            $('input[name="inverterGraphYear"]').val(currDate.todayYear);

            var history_date = $('input[name="historyGraphDay"]').val();
            var history_time = 'day';
            var expected_date = $('input[name="expectedGraphDay"]').val();
            var expected_time = 'day';
            var env_date = $('input[name="envGraphDay"]').val();
            var env_time = 'day';
            var alert_date = $('input[name="alertGraphDay"]').val();
            var alert_time = 'day';
            var emi_date = $('input[name="emiGraphDay"]').val();
            var pv_date = $('input[name="pvGraphDay"]').val();
            var inverter_date = $('input[name="inverterGraphDay"]').val();
            var inverter_time = 'day';

            pvSerialNo = $('.pvCarouselList').find('li.active').attr('data-slide-to');
            pvSelectionAjax();

            var emiUnitArray = new Array();
            var emiUnit = '';

            emiCheckBoxArray = $("input[name='emiCheckBox[]']:checked").map(function () {

                if ($(this).val() == 'pv_temperature') {

                    emiUnit = 'C';
                } else if ($(this).val() == 'ambient_temperature') {

                    emiUnit = 'C';
                } else if ($(this).val() == 'irradiance') {

                    emiUnit = 'W/m2';
                } else if ($(this).val() == 'wind_speed') {

                    emiUnit = 'm/s';
                }

                if (emiUnitArray.indexOf(emiUnit) === -1) {

                    emiUnitArray.push(emiUnit);
                }

                if (emiUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }

            }).get();

            $('.emiCheckBox').change(function () {

                var emiUnitArray = new Array();
                var emiUnit = '';

                emiCheckBoxArray = $("input[name='emiCheckBox[]']:checked").map(function () {

                    if ($(this).val() == 'pv_temperature') {

                        emiUnit = 'C';
                    } else if ($(this).val() == 'ambient_temperature') {

                        emiUnit = 'C';
                    } else if ($(this).val() == 'irradiance') {

                        emiUnit = 'W/m2';
                    } else if ($(this).val() == 'wind_speed') {

                        emiUnit = 'm/s';
                    }

                    if (emiUnitArray.indexOf(emiUnit) === -1) {

                        emiUnitArray.push(emiUnit);
                    }

                    if (emiUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();
            });

            var historyUnitArray = new Array();
            var historyUnit = '';

            historyCheckBoxArray = $("input[name='historyCheckBox[]']:checked").map(function () {

                if ($(this).val() == 'generation') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'consumption') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'grid') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'buy') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'sell') {

                    historyUnit = 'kW';
                } else if ($(this).val() == 'saving') {

                    historyUnit = 'PKR';
                } else if ($(this).val() == 'irradiance') {

                    historyUnit = 'W/m2';
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

                    if ($(this).val() == 'generation') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'consumption') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'grid') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'buy') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'sell') {

                        historyUnit = 'kW';
                    } else if ($(this).val() == 'saving') {

                        historyUnit = 'PKR';
                    } else if ($(this).val() == 'irradiance') {

                        historyUnit = 'W/m2';
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

            var inverterUnitArray = new Array();
            var inverterUnit = '';

            inverterCheckBoxArray = $("input[name='inverterCheckBox[]']:checked").map(function () {

                if ($(this).val() == 'output_power') {

                    inverterUnit = 'kW';
                } else if ($(this).val() == 'dc_power') {

                    inverterUnit = 'kW';
                } else if ($(this).val() == 'normalize_power') {

                    inverterUnit = '%';
                }

                if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                    inverterUnitArray.push(inverterUnit);
                }

                if (inverterUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }

            }).get();

            $('.inverterCheckBox').change(function () {

                var inverterUnitArray = new Array();
                var inverterUnit = '';

                inverterCheckBoxArray = $("input[name='inverterCheckBox[]']:checked").map(function () {

                    if ($(this).val() == 'output_power') {

                        inverterUnit = 'kW';
                    } else if ($(this).val() == 'dc_power') {

                        inverterUnit = 'kW';
                    } else if ($(this).val() == 'normalize_power') {

                        inverterUnit = '%';
                    }

                    if (inverterUnitArray.indexOf(inverterUnit) === -1) {

                        inverterUnitArray.push(inverterUnit);
                    }

                    if (inverterUnitArray.length == 2) {

                        $(this).prop('checked', false);
                        inverterUnitArray.pop();
                        alert('You cannot check normalize power with other parameters');
                    } else {

                        return $(this).val();
                    }

                }).get();
                console.log(inverterCheckBoxArray)
                exportCsvDataValues(serial_no, $('input[name="inverterGraphDay"]').val(), 'day',inverterCheckBoxArray);

            });

            var pvUnitArray = new Array();
            var pvUnit = '';

            pvCheckBoxArray = $("input[name='pvCheckBox[]']:checked").map(function () {

                if ($(this).val() == 'pv_current') {

                    pvUnit = 'A';
                } else if ($(this).val() == 'pv_voltage') {

                    pvUnit = 'V';
                } else if ($(this).val() == 'pv_power') {

                    pvUnit = 'kW';
                }

                if (pvUnitArray.indexOf(pvUnit) === -1) {

                    pvUnitArray.push(pvUnit);
                }

                if (pvUnitArray.length > 2) {

                    $(this).prop('checked', false);
                    alert('You cannot check more than 2 different units');
                } else {

                    return $(this).val();
                }

            }).get();

            $('.pvCheckBox').change(function () {

                var pvUnitArray = new Array();
                var pvUnit = '';

                pvCheckBoxArray = $("input[name='pvCheckBox[]']:checked").map(function () {

                    if ($(this).val() == 'pv_current') {

                        pvUnit = 'A';
                    } else if ($(this).val() == 'pv_voltage') {

                        pvUnit = 'V';
                    } else if ($(this).val() == 'pv_power') {

                        pvUnit = 'kW';
                    }

                    if (pvUnitArray.indexOf(pvUnit) === -1) {

                        pvUnitArray.push(pvUnit);
                    }

                    if (pvUnitArray.length > 2) {

                        $(this).prop('checked', false);
                        alert('You cannot check more than 2 different units');
                    } else {

                        return $(this).val();
                    }

                }).get();

                pvGraphAjax($('input[name="pvGraphDay"]').val(), pvCheckBoxArray);

            });

            $('.pvGraphTabCarousel').on('click', function () {

                $('.pvGraphTabDataDiv').show();
                $('#searchPVCheckBox').trigger('click');
                showCheckboxpv();
            });

            $('.pvDataTabCarousel').on('click', function () {

                $('.pvGraphTabDataDiv').hide();
            });

            // next click event --
            $('.rightPVCarousel').on('click', function (e) {

                pvSelectionAjax();

                $('.pvGraphDataTabs').children('a').each(function () {

                    if ($(this).attr('id') == 'v-pills-home-tab-pv') {

                        $(this).trigger('click');
                    }

                });
            });

            // previous click
            $('.leftPVCarousel').on('click', function (e) {

                pvSelectionAjax();

                $('.pvGraphDataTabs').children('a').each(function () {

                    if ($(this).attr('id') == 'v-pills-home-tab-pv') {

                        $(this).trigger('click');
                    }

                });
            });

            var serial_no = $('.carousel-indicators li').data('slide-to');

            var totalItems = $('.carousel-item').length;
            var currentIndex = $('div.carousel-item.active').index();

            console.log(currentIndex);
            console.log(serial_no);

            changeHistoryDayMonthYear(history_date, history_time, historyCheckBoxArray);
            changeExpectedDayMonthYear(expected_date, expected_time);
            changeENVDayMonthYear(env_date, env_time);
            changeAlertDayMonthYear(alert_date, alert_time);
            changeInverterDayMonthYear(serial_no, inverter_date, inverter_time, inverterCheckBoxArray);

            $('#searchEMICheckBox').click(function () {

                showCheckboxes();
                emiGraphAjax($('input[name="emiGraphDay"]').val(), emiCheckBoxArray);
            });

            $('#searchPVCheckBox').click(function () {

                showCheckboxpv();
                pvGraphAjax($('input[name="pvGraphDay"]').val(), pvCheckBoxArray);
            });

            $('#searchHistoryCheckBox').click(function () {

                showCheckbox();

                var historyTimeValue = $('#history_day_my_btn_vt').find('button.active').attr('id');
                timeData1 = historyTimeValue;
                historyGraphAjax($('input[name="historyGraphDay"]').val(), historyTimeValue, historyCheckBoxArray);
            });
            // exportCsvDataValues(serial_no, $('input[name="inverterGraphDay"]').val(), 'day',inverterCheckBoxArray);

            $('#searchInverterCheckBox').click(function () {
                showCheckboxinverter();
                exportCsvDataValues(serial_no, $('input[name="inverterGraphDay"]').val(), 'day');
                inverterGraphAjax(serial_no, $('input[name="inverterGraphDay"]').val(), 'day', inverterCheckBoxArray);
            });

            $('.J-yearMonthDayPicker-single-history').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeHistoryDayMonthYear(this.$input.eq(0).val(), 'day', historyCheckBoxArray);
                }
            });

            $('.J-yearMonthPicker-single-history').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    changeHistoryDayMonthYear(this.$input.eq(0).val(), 'month', historyCheckBoxArray);
                }
            });

            $('.J-yearPicker-single-history').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeHistoryDayMonthYear(this.$input.eq(0).val(), 'year', historyCheckBoxArray);
                }
            });

            $('.J-yearMonthDayPicker-single-expected').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeExpectedDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-expected').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeExpectedDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-expected').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeExpectedDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-env').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeENVDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-env').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeENVDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-env').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeENVDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-alert').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeAlertDayMonthYear(this.$input.eq(0).val(), 'day');
                }
            });

            $('.J-yearMonthPicker-single-alert').datePicker({
                format: 'MM-YYYY',
                language: 'en',
                hide: function (type) {
                    console.log(this.$input.eq(0).val());
                    changeAlertDayMonthYear(this.$input.eq(0).val(), 'month');
                }
            });

            $('.J-yearPicker-single-alert').datePicker({
                format: 'YYYY',
                language: 'en',
                hide: function (type) {
                    changeAlertDayMonthYear(this.$input.eq(0).val(), 'year');
                }
            });

            $('.J-yearMonthDayPicker-single-emi').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    emiGraphAjax(this.$input.eq(0).val(), emiCheckBoxArray);
                }
            });

            $('.J-yearMonthDayPicker-single-pv').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    pvGraphAjax(this.$input.eq(0).val(), pvCheckBoxArray);
                }
            });

            $('.J-yearMonthDayPicker-single-inverter').datePicker({
                format: 'YYYY-MM-DD',
                language: 'en',
                hide: function (type) {
                    changeInverterDayMonthYear(serial_no, this.$input.eq(0).val(), 'day', inverterCheckBoxArray);
                }
            });

            $('#historyGraphPreviousDay').on('click', function () {

                show_date = $("input[name='historyGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                history_date = formatDate(datess);
                $('input[name="historyGraphDay"]').val('');
                $('input[name="historyGraphDay"]').val(history_date);
                console.log($("input[name='historyGraphDay']").val());
                history_time = 'day';
                timeData1 = history_time;
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
            });

            $('#historyGraphForwardDay').on('click', function () {

                show_date = $("input[name='historyGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                history_date = formatDate(datess);
                $('input[name="historyGraphDay"]').val('');
                $('input[name="historyGraphDay"]').val(history_date);
                console.log($("input[name='historyGraphDay']").val());
                history_time = 'day';
                timeData1 = history_time;
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
            });

            $('#historyGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='historyGraphMonth']").val();
                history_date = formatPreviousMonth(show_date);
                $('input[name="historyGraphMonth"]').val('');
                $('input[name="historyGraphMonth"]').val(history_date);
                console.log($("input[name='historyGraphMonth']").val());
                history_time = 'month';
                timeData1 = history_time;
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
            });

            $('#historyGraphForwardMonth').on('click', function () {

                show_date = $("input[name='historyGraphMonth']").val();
                history_date = formatForwardMonth(show_date);
                $('input[name="historyGraphMonth"]').val('');
                $('input[name="historyGraphMonth"]').val(history_date);
                console.log($("input[name='historyGraphMonth']").val());
                history_time = 'month';
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
            });

            $('#historyGraphPreviousYear').on('click', function () {

                show_date = $("input[name='historyGraphYear']").val();
                history_date = formatPreviousYear(show_date);
                $('input[name="historyGraphYear"]').val('');
                $('input[name="historyGraphYear"]').val(history_date);
                console.log($("input[name='historyGraphYear']").val());
                history_time = 'year';
                timeData1 = history_time;
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
            });

            $('#historyGraphForwardYear').on('click', function () {

                show_date = $("input[name='historyGraphYear']").val();
                history_date = formatForwardYear(show_date);
                $('input[name="historyGraphYear"]').val('');
                $('input[name="historyGraphYear"]').val(history_date);
                console.log($("input[name='historyGraphYear']").val());
                history_time = 'year';
                timeData1 = history_time;
                historyGraphAjax(history_date, history_time, historyCheckBoxArray);
            });

            $('#expectedGraphPreviousDay').on('click', function () {

                show_date = $("input[name='expectedGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                expected_date = formatDate(datess);
                $('input[name="expectedGraphDay"]').val('');
                $('input[name="expectedGraphDay"]').val(expected_date);
                console.log($("input[name='expectedGraphDay']").val());
                expected_time = 'day';
                expectedGraphAjax(expected_date, expected_time);
            });

            $('#expectedGraphForwardDay').on('click', function () {

                show_date = $("input[name='expectedGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                expected_date = formatDate(datess);
                $('input[name="expectedGraphDay"]').val('');
                $('input[name="expectedGraphDay"]').val(expected_date);
                console.log($("input[name='expectedGraphDay']").val());
                expected_time = 'day';
                expectedGraphAjax(expected_date, expected_time);
            });

            $('#expectedGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='expectedGraphMonth']").val();
                expected_date = formatPreviousMonth(show_date);
                $('input[name="expectedGraphMonth"]').val('');
                $('input[name="expectedGraphMonth"]').val(expected_date);
                console.log($("input[name='expectedGraphMonth']").val());
                expected_time = 'month';
                expectedGraphAjax(expected_date, expected_time);
            });

            $('#expectedGraphForwardMonth').on('click', function () {

                show_date = $("input[name='expectedGraphMonth']").val();
                expected_date = formatForwardMonth(show_date);
                $('input[name="expectedGraphMonth"]').val('');
                $('input[name="expectedGraphMonth"]').val(expected_date);
                console.log($("input[name='expectedGraphMonth']").val());
                expected_time = 'month';
                expectedGraphAjax(expected_date, expected_time);
            });

            $('#expectedGraphPreviousYear').on('click', function () {

                show_date = $("input[name='expectedGraphYear']").val();
                expected_date = formatPreviousYear(show_date);
                $('input[name="expectedGraphYear"]').val('');
                $('input[name="expectedGraphYear"]').val(expected_date);
                console.log($("input[name='expectedGraphYear']").val());
                expected_time = 'year';
                expectedGraphAjax(expected_date, expected_time);
            });

            $('#expectedGraphForwardYear').on('click', function () {

                show_date = $("input[name='expectedGraphYear']").val();
                expected_date = formatForwardYear(show_date);
                $('input[name="expectedGraphYear"]').val('');
                $('input[name="expectedGraphYear"]').val(expected_date);
                console.log($("input[name='expectedGraphYear']").val());
                expected_time = 'year';
                expectedGraphAjax(expected_date, expected_time);
            });

            $('#envGraphPreviousDay').on('click', function () {

                show_date = $("input[name='envGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                env_date = formatDate(datess);
                $('input[name="envGraphDay"]').val('');
                $('input[name="envGraphDay"]').val(env_date);
                console.log($("input[name='envGraphDay']").val());
                env_time = 'day';
                envGraphAjax(env_date, env_time);
            });

            $('#envGraphForwardDay').on('click', function () {

                show_date = $("input[name='envGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                env_date = formatDate(datess);
                $('input[name="envGraphDay"]').val('');
                $('input[name="envGraphDay"]').val(env_date);
                console.log($("input[name='envGraphDay']").val());
                env_time = 'day';
                envGraphAjax(env_date, env_time);
            });

            $('#envGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatPreviousMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(env_date, env_time);
            });

            $('#envGraphForwardMonth').on('click', function () {

                show_date = $("input[name='envGraphMonth']").val();
                env_date = formatForwardMonth(show_date);
                $('input[name="envGraphMonth"]').val('');
                $('input[name="envGraphMonth"]').val(env_date);
                console.log($("input[name='envGraphMonth']").val());
                env_time = 'month';
                envGraphAjax(env_date, env_time);
            });

            $('#envGraphPreviousYear').on('click', function () {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatPreviousYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(env_date, env_time);
            });

            $('#envGraphForwardYear').on('click', function () {

                show_date = $("input[name='envGraphYear']").val();
                env_date = formatForwardYear(show_date);
                $('input[name="envGraphYear"]').val('');
                $('input[name="envGraphYear"]').val(env_date);
                console.log($("input[name='envGraphYear']").val());
                env_time = 'year';
                envGraphAjax(env_date, env_time);
            });


            $('#alertGraphPreviousDay').on('click', function () {

                show_date = $("input[name='alertGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                alert_date = formatDate(datess);
                $('input[name="alertGraphDay"]').val('');
                $('input[name="alertGraphDay"]').val(alert_date);
                console.log($("input[name='alertGraphDay']").val());
                alert_time = 'day';
                alertGraphAjax(alert_date, alert_time);
            });

            $('#alertGraphForwardDay').on('click', function () {

                show_date = $("input[name='alertGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                alert_date = formatDate(datess);
                $('input[name="alertGraphDay"]').val('');
                $('input[name="alertGraphDay"]').val(alert_date);
                console.log($("input[name='alertGraphDay']").val());
                alert_time = 'day';
                alertGraphAjax(alert_date, alert_time);
            });

            $('#alertGraphPreviousMonth').on('click', function () {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatPreviousMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(alert_date, alert_time);
            });

            $('#alertGraphForwardMonth').on('click', function () {

                show_date = $("input[name='alertGraphMonth']").val();
                alert_date = formatForwardMonth(show_date);
                $('input[name="alertGraphMonth"]').val('');
                $('input[name="alertGraphMonth"]').val(alert_date);
                console.log($("input[name='alertGraphMonth']").val());
                alert_time = 'month';
                alertGraphAjax(alert_date, alert_time);
            });

            $('#alertGraphPreviousYear').on('click', function () {

                show_date = $("input[name='alertGraphYear']").val();
                alert_date = formatPreviousYear(show_date);
                $('input[name="alertGraphYear"]').val('');
                $('input[name="alertGraphYear"]').val(alert_date);
                console.log($("input[name='alertGraphYear']").val());
                alert_time = 'year';
                alertGraphAjax(alert_date, alert_time);
            });

            $('#alertGraphForwardYear').on('click', function () {

                show_date = $("input[name='alertGraphYear']").val();
                alert_date = formatForwardYear(show_date);
                $('input[name="alertGraphYear"]').val('');
                $('input[name="alertGraphYear"]').val(alert_date);
                console.log($("input[name='alertGraphYear']").val());
                alert_time = 'year';
                alertGraphAjax(alert_date, alert_time);
            });


            $('#emiGraphPreviousDay').on('click', function () {

                show_date = $("input[name='emiGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                emi_date = formatDate(datess);
                $('input[name="emiGraphDay"]').val('');
                $('input[name="emiGraphDay"]').val(emi_date);
                console.log($("input[name='emiGraphDay']").val());
                emi_time = 'day';
                emiGraphAjax(emi_date, emiCheckBoxArray);
            });

            $('#emiGraphForwardDay').on('click', function () {

                show_date = $("input[name='emiGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                emi_date = formatDate(datess);
                $('input[name="emiGraphDay"]').val('');
                $('input[name="emiGraphDay"]').val(emi_date);
                console.log($("input[name='emiGraphDay']").val());
                emi_time = 'day';
                emiGraphAjax(emi_date, emiCheckBoxArray);
            });

            $('#pvGraphPreviousDay').on('click', function () {

                show_date = $("input[name='pvGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                pv_date = formatDate(datess);
                $('input[name="pvGraphDay"]').val('');
                $('input[name="pvGraphDay"]').val(pv_date);
                console.log($("input[name='pvGraphDay']").val());
                pv_time = 'day';
                pvGraphAjax(pv_date, pvCheckBoxArray);
            });

            $('#pvGraphForwardDay').on('click', function () {

                show_date = $("input[name='pvGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                pv_date = formatDate(datess);
                $('input[name="pvGraphDay"]').val('');
                $('input[name="pvGraphDay"]').val(pv_date);
                console.log($("input[name='pvGraphDay']").val());
                pv_time = 'day';
                pvGraphAjax(pv_date, pvCheckBoxArray);
            });

            $('#inverterGraphPreviousDay').on('click', function () {

                show_date = $("input[name='inverterGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() - 1);
                inverter_date = formatDate(datess);
                $('input[name="inverterGraphDay"]').val('');
                $('input[name="inverterGraphDay"]').val(inverter_date);
                console.log($("input[name='inverterGraphDay']").val());
                inverter_time = 'day';
                exportCsvDataValues(serial_no, inverter_date, inverter_time);
                inverterGraphAjax(serial_no, inverter_date, inverter_time, inverterCheckBoxArray);
            });

            $('#inverterGraphForwardDay').on('click', function () {

                show_date = $("input[name='inverterGraphDay']").val();
                var datess = new Date(show_date);
                console.log(datess);
                datess.setDate(datess.getDate() + 1);
                inverter_date = formatDate(datess);
                $('input[name="inverterGraphDay"]').val('');
                $('input[name="inverterGraphDay"]').val(inverter_date);
                console.log($("input[name='inverterGraphDay']").val());
                inverter_time = 'day';
                exportCsvDataValues(serial_no, inverter_date, inverter_time);
                inverterGraphAjax(serial_no, inverter_date, inverter_time, inverterCheckBoxArray);
            });

            $("#history_day_my_btn_vt button").click(function () {

                $('#history_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeHistoryDayMonthYear(history_date, history_time, historyCheckBoxArray);

            });

            $("#expected_day_my_btn_vt button").click(function () {

                $('#expected_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeExpectedDayMonthYear(expected_date, expected_time);

            });

            $("#env_day_my_btn_vt button").click(function () {

                $('#env_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeENVDayMonthYear(env_date, env_time);

            });

            $("#alert_day_my_btn_vt button").click(function () {

                $('#alert_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeAlertDayMonthYear(alert_date, alert_time);

            });

            $("#inverterTab .inverterTabLink").each(function (i) {
                $(this).click(function () {

                    if ($('.inverterTabPane')[i].id == 'inverterTabID') {

                        var inverterSelectedTab = new Array();

                        $('.inverterGraphDataTabs').children('a').each(function () {

                            if ($(this).hasClass('active')) {
                                console.log($(this));

                                inverterSelectedTab.push($(this).attr('id'));
                            }
                        });

                        if ($.inArray("v-pills-profile-tab", inverterSelectedTab) !== -1) {

                            $('.graphDateTimeDiv').show();
                            $('#iverterCheckBoxSelectBtn').show();
                            $('#allInverterDiv').hide();
                        } else {

                            $('.graphDateTimeDiv').hide();
                            $('#iverterCheckBoxSelectBtn').hide();
                            $('#allInverterDiv').show();
                        }
                    } else if ($('.inverterTabPane')[i].id == 'messages2') {

                        $('.pvGraphDataTabs').children('a').each(function () {

                            if ($(this).attr('id') == 'v-pills-home-tab-pv') {

                                $(this).trigger('click');
                            }

                        });

                        $('.graphDateTimeDiv').hide();
                        $('#iverterCheckBoxSelectBtn').hide();
                        $('#allInverterDiv').hide();
                    } else if ($('.inverterTabPane')[i].id == 'messages3') {

                        emiGraphAjax(emi_date, emiCheckBoxArray);

                        $('.graphDateTimeDiv').hide();
                        $('#iverterCheckBoxSelectBtn').hide();
                        $('#allInverterDiv').hide();
                    } else {

                        $('.graphDateTimeDiv').hide();
                        $('#iverterCheckBoxSelectBtn').hide();
                        $('#allInverterDiv').hide();
                    }
                });
            });

            $("#v-pills-tab .graphDataView").each(function (i) {
                $(this).click(function () {

                    console.log($('.graphDataViewTabPane')[i].id);
                    if (graphicalDataInverterSerialNo.indexOf($('.graphDataViewTabPane')[i].id) > -1) {

                        $('.carousel_control_prev').hide();
                        $('.carousel_control_next').hide();
                        $('.carousel-indicators').hide();
                        $('.graphDateTimeDiv').show();
                        $('#iverterCheckBoxSelectBtn').show();
                        changeInverterDayMonthYear(serial_no, inverter_date, inverter_time, inverterCheckBoxArray);
                    } else {

                        $('.graphDateTimeDiv').hide();
                        $('#iverterCheckBoxSelectBtn').hide();
                        $('.carousel_control_prev').show();
                        $('.carousel_control_next').show();
                        $('.carousel-indicators').show();
                    }
                });
            });

            $("#inverter_day_my_btn_vt button").click(function () {

                $('#inverter_day_my_btn_vt').children().removeClass("active");
                $(this).addClass("active");

                changeInverterDayMonthYear(serial_no, inverter_date, inverter_time, inverterCheckBoxArray);

            });

            plantPowerGraph(plantsPowerGraphData);
            plantConsumptionGraph(plantsConsumptionGraphData);
        }

        function pvSelectionAjax() {

            pvSerialNo = $('.pvCarouselList').find('li.active').attr('data-slide-to');
            var plantID = $('#plantID').val();

            $('#checkpv').empty();

            $.ajax({
                url: "{{ route('admin.inverter.mppt.number') }}",
                method: "POST",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'plantID': plantID,
                    'serialNo': pvSerialNo,
                },
                dataType: 'json',
                success: function (data) {

                    console.log(data);

                    if (data.mpptNumber > 0) {

                        if (data.mpptNumber <= 12) {

                            $('.class_porder_vt').hide();
                            $('.pvSecondRow').hide();
                        } else {

                            $('.class_porder_vt').show();
                            $('.pvSecondRow').show();
                        }

                        for (var pi = 1; pi <= data.mpptNumber; pi++) {

                            $('#checkpv').append('<label for="pv' + pi + '"><input type="checkbox" class="pvSelectCheckBox" name="pvSelectCheckBox[]" id="pv' + pi + '" value="' + pi + '" checked/>PV' + pi + '</label>');
                        }
                    }
                },
                error: function (data) {

                    console.log(data);
                }
            });
        }

        function changeHistoryDayMonthYear(date, time, historyCheckBoxArray) {

            var d_m_y = '';

            $('#history_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#history_day_month_year_vt_year').hide();
                $('#history_day_month_year_vt_month').hide();
                $('#history_day_month_year_vt_day').show();
                date = $('input[name="historyGraphDay"]').val();
                console.log(date)
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#history_day_month_year_vt_year').hide();
                $('#history_day_month_year_vt_day').hide();
                $('#history_day_month_year_vt_month').show();
                date = $('input[name="historyGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#history_day_month_year_vt_day').hide();
                $('#history_day_month_year_vt_month').hide();
                $('#history_day_month_year_vt_year').show();
                date = $('input[name="historyGraphYear"]').val();
                console.log(date)
                time = 'year';
            }
            timeData1 = time;
            historyGraphAjax(date, time, historyCheckBoxArray);
        }

        function changeExpectedDayMonthYear(date, time) {

            console.log(date);
            var d_m_y = '';

            $('#expected_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            console.log(d_m_y);

            if (d_m_y == 'day') {
                $('#expected_day_month_year_vt_year').hide();
                $('#expected_day_month_year_vt_month').hide();
                $('#expected_day_month_year_vt_day').show();
                date = $('input[name="expectedGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#expected_day_month_year_vt_year').hide();
                $('#expected_day_month_year_vt_day').hide();
                $('#expected_day_month_year_vt_month').show();
                date = $('input[name="expectedGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#expected_day_month_year_vt_day').hide();
                $('#expected_day_month_year_vt_month').hide();
                $('#expected_day_month_year_vt_year').show();
                date = $('input[name="expectedGraphYear"]').val();
                time = 'year';
            }

            expectedGraphAjax(date, time);
        }

        function changeENVDayMonthYear(date, time) {

            console.log(date);
            var d_m_y = '';

            $('#env_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            console.log(d_m_y);

            if (d_m_y == 'day') {
                $('#env_day_month_year_vt_year').hide();
                $('#env_day_month_year_vt_month').hide();
                $('#env_day_month_year_vt_day').show();
                date = $('input[name="envGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#env_day_month_year_vt_year').hide();
                $('#env_day_month_year_vt_day').hide();
                $('#env_day_month_year_vt_month').show();
                date = $('input[name="envGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#env_day_month_year_vt_day').hide();
                $('#env_day_month_year_vt_month').hide();
                $('#env_day_month_year_vt_year').show();
                date = $('input[name="envGraphYear"]').val();
                time = 'year';
            }

            envGraphAjax(date, time);
        }

        function changeInverterDayMonthYear(serial_no, date, time, inverterCheckBoxArray) {

            var d_m_y = '';

            $('#inverter_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            if (d_m_y == 'day') {
                $('#inverter_day_month_year_vt_year').hide();
                $('#inverter_day_month_year_vt_month').hide();
                $('#inverter_day_month_year_vt_day').show();
                date = $('input[name="inverterGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#inverter_day_month_year_vt_year').hide();
                $('#inverter_day_month_year_vt_day').hide();
                $('#inverter_day_month_year_vt_month').show();
                date = $('input[name="inverterGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#inverter_day_month_year_vt_day').hide();
                $('#inverter_day_month_year_vt_month').hide();
                $('#inverter_day_month_year_vt_year').show();
                date = $('input[name="inverterGraphYear"]').val();
                time = 'year';
            }
            exportCsvDataValues(serial_no, date, time);
            inverterGraphAjax(serial_no, date, time, inverterCheckBoxArray);

            // exportinverterCsv(serial_no, date, time, inverterCheckBoxArray);
        }

        function changeAlertDayMonthYear(date, time) {

            var d_m_y = '';

            $('#alert_day_my_btn_vt').children('button').each(function () {
                if ($(this).hasClass('active')) {
                    d_m_y = $(this).attr('id');
                }
            });

            console.log(d_m_y);

            if (d_m_y == 'day') {
                $('#alert_day_month_year_vt_year').hide();
                $('#alert_day_month_year_vt_month').hide();
                $('#alert_day_month_year_vt_day').show();
                date = $('input[name="alertGraphDay"]').val();
                time = 'day';
            } else if (d_m_y == 'month') {
                $('#alert_day_month_year_vt_year').hide();
                $('#alert_day_month_year_vt_day').hide();
                $('#alert_day_month_year_vt_month').show();
                date = $('input[name="alertGraphMonth"]').val();
                time = 'month';
            } else if (d_m_y == 'year') {
                $('#alert_day_month_year_vt_day').hide();
                $('#alert_day_month_year_vt_month').hide();
                $('#alert_day_month_year_vt_year').show();
                date = $('input[name="alertGraphYear"]').val();
                time = 'year';
            }

            alertGraphAjax(date, time);
        }

        function historyGraphAjax(date, time, historyCheckBoxArray) {
console.log(historyCheckBoxArray);
            $('.historyGraphSpinner').show();
            $('#historyGraphDiv').empty();
            $('.historyGraphError').hide();
            $('.generationTotalValue').html('');
            $('.consumptionTotalValue').html('');
            $('.gridTotalValue').html('');
            $('.buyTotalValue').html('');
            $('.sellTotalValue').html('');
            $('.savingTotalValue').html('');
            $('.irradianceTotalValue').html('');

            var plantID = $('#plantID').val();
            var plantMeterType = $('#plantMeterType').val();

            $.ajax({
                url: "{{ route('admin.graph.plant.history') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time,
                    'plantMeterType': plantMeterType,
                    'historyCheckBoxArray': JSON.stringify(historyCheckBoxArray)
                },
                dataType: 'json',
                success: function (data) {

                    Timedata = data['time_array'];
                    TimeDetails = data['time_details'];
                    timetype = time;
                    console.log(data);
                    console.log(data.plant_history_graph);
                    console.log(Timedata);

                    //var generationdata=data.plant_history_graph;
                    $.each(data.plant_history_graph, function (index, item) {
                        if ("Generation" == item.name) {
                            generationdata = item.data;
                        }
                        if ("Cost Saving" == item.name) {
                            costsaving = item.data;
                        }
                    });

                    $('.historyGraphSpinner').hide();

                    $('#historyGraphDiv').append('<div id="plantsHistoryChart" style="height:300px;width:100%;"></div>');
                    $('.generationTotalValue').html(data.total_generation);
                    $('.consumptionTotalValue').html(data.total_consumption);
                    $('.gridTotalValue').html(data.total_grid);
                    $('.buyTotalValue').html(data.total_buy);
                    $('.sellTotalValue').html(data.total_sell);
                    $('.savingTotalValue').html(data.total_saving);
                    if (time == 'day') {

                        $('.irradianceTotalValue').html(data.total_irradiation + ' W/m<sup>2</sup>');
                    } else if (time == 'month' || time == 'year') {

                        $('.irradianceTotalValue').html(data.total_irradiation + ' kWh/m<sup>2</sup>');
                    }

                    plantHistoryGraph(data);
                },
                error: function (data) {

                    $('.historyGraphSpinner').hide();
                    $('.historyGraphError').show();
                }
            });
        }

        function expectedGraphAjax(date, time) {

            $('.expectedGraphSpinner').show();
            $('#expectedGraphDiv').empty();
            $('.expectedGraphError').hide();
            $('.actualPercentage').html('0% <span>0 kWh</span>');
            $('.actualTotalValue').html('0 kWh');
            $('.expectedTotalValue').html('0 kWh');

            var plantID = $('#plantID').val();

            $.ajax({
                url: "{{ route('admin.graph.plant.actual.expected') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time
                },
                dataType: 'json',
                success: function (data) {

                    console.log(data);

                    $('.expectedGraphSpinner').hide();

                    $('.actualPercentage').html(data.actual_percentage + '% <span>' + data.expected_converted_value + '</span>');
                    $('.actualTotalValue').html(data.actual_converted_value);
                    $('.expectedTotalValue').html(data.expected_converted_value);
                    $('#expectedGraphDiv').append('<div id="plantsExpectedChart" style="height:340px;width:340px;margin:0 auto;"></div>')

                    plantExpectedGraph(data, time);
                },
                error: function (data) {

                    $('.expectedGraphSpinner').hide();
                    $('.expectedGraphError').show();
                }
            });
        }

        function envGraphAjax(date, time) {

            $('.envGraphSpinner').show();
            $('.envTreePlanting').html('');
            $('.envEmissionReduction').html('');
            $('.envGraphError').hide();

            var plantID = $('#plantID').val();

            $.ajax({
                url: "{{ route('admin.graph.plant.environmental.benefits') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time
                },
                dataType: 'json',
                success: function (data) {

                    $('.envGraphSpinner').hide();

                    $('.envTreePlanting').html(data.tree_value + ' <span>tree(s)</span>');
                    $('.envEmissionReduction').html(data.co2_value + ' <span>T</span>');
                },
                error: function (data) {

                    $('.envGraphSpinner').hide();
                    $('.envGraphError').show();
                }
            });
        }

        function alertGraphAjax(date, time) {

            $('.alertGraphSpinner').show();
            $('.alertGraphError').hide();
            $('#alertGraphDivv div').remove();
            $('.totalAlertDiv').html('0');
            $('.totalAlarmDiv').html('0');
            $('.totalFaultDiv').html('0');

            var plantID = $('#plantID').val();

            $.ajax({
                url: "{{ route('admin.graph.plant.alert') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'time': time
                },
                dataType: 'json',
                success: function (data) {

                    console.log(data);

                    $('.alertGraphSpinner').hide();

                    $('.totalAlertDiv').html(data.total_value);
                    $('.totalAlarmDiv').html(data.alarm_value);
                    $('.totalFaultDiv').html(data.fault_value);
                    $('#alertGraphDivv').append('<div id="plantsAlertChart" style="height:320px;width:100%;isplay: flex; justify-content: center;margin: 70px 46px 0 46px;"></div>')

                    plantAlertGraph(data);
                },
                error: function (data) {

                    $('.alertGraphSpinner').hide();
                    $('.alertGraphError').show();
                }
            });
        }

        function emiGraphAjax(date, emiCheckBoxArray) {

            var plantID = $('#plantID').val();

            $('#emiGraphDivv div').remove();

            $.ajax({
                url: "{{ route('admin.graph.plant.emi') }}",
                method: "GET",
                data: {
                    'plantID': plantID,
                    'date': date,
                    'emiCheckBoxArray': JSON.stringify(emiCheckBoxArray)
                },
                dataType: 'json',
                success: function (data) {

                    console.log(data);

                    $('.emiGraphSpinner').hide();

                    $('#emiGraphDivv').append('<div id="plantsEMIChart" style="height:320px;width:100%;"></div>')

                    plantEMIGraph(data, emiCheckBoxArray);
                },
                error: function (data) {

                    $('.emiGraphSpinner').hide();
                    $('.emiGraphError').show();
                }
            });
        }

        function pvGraphAjax(date, pvCheckBoxArray) {

            var pvValuesArray = $('input[name="pvSelectCheckBox[]"]:checked').map(function () {

                return $(this).val();

            }).get();

            if (pvValuesArray.length == 0) {

                $('.pvGraphSpinner').hide();
                alert('No PV Exists!');
                return false;
            } else {

                var pvSerialNo = $('.pvCarouselList').find('li.active').attr('data-slide-to');

                var plantID = $('#plantID').val();

                $('#pvGraphDivv-' + pvSerialNo).empty();

                $.ajax({
                    url: "{{ route('admin.graph.plant.pv') }}",
                    method: "GET",
                    data: {
                        'plantID': plantID,
                        'serialNo': pvSerialNo,
                        'date': date,
                        'pvCheckBoxArray': pvCheckBoxArray,
                        'pvValuesArray': pvValuesArray
                    },
                    dataType: 'json',
                    success: function (data) {

                        console.log(data);

                        $('.pvGraphSpinner').hide();

                        $('#pvGraphDivv-' + pvSerialNo).append(`<div id="plantsPVChart-${pvSerialNo}" style="height:320px;width:100%;"></div>`)

                        plantPVGraph(data, pvCheckBoxArray, pvSerialNo);
                    },
                    error: function (data) {

                        console.log(data);
                    }
                });
            }
        }

        function exportCsvDataValues(serialNo, date, time,inverterArray=["output_power"]) {
            let exportDataRef = document.getElementById("exportgraph").getAttribute('data-href');
            let splitData = exportDataRef.split('?');
            var plantID = $('#plantID').val();
            let url = splitData[0] + '?plantID=' + plantID + '&Date=' + date + '&serialNo=' + serialNo + '&time=' + time + '&inverterArray=' + JSON.stringify(inverterArray);
            document.getElementById("exportgraph").removeAttribute('data-href');
            document.getElementById("exportgraph").setAttribute('data-href', url);
        }

        function inverterGraphAjax(serial_no, date, time, inverterCheckBoxArray) {


            var inverterSerialNo = $('.inverterCarouselList').find('li.active').attr('data-slide-to');
            console.log(inverterSerialNo);
            $graph_div = 'div#graphDiv_' + serial_no;
            $('#inverterGraphDiv-' + inverterSerialNo).empty();
            $('.inverterGraphSpinner').show();
            $('.inverterGraphError').hide();
            $('.generationPlantGraph').html('');
            var plantID = $('#plantID').val();
            // let exportDataRef = document.getElementById("exportgraph").getAttribute('data-href');
            //     let splitData = exportDataRef.split('?');
            //     let url = splitData[0]+'?plantID='+plantID+'&Date='+date;
            //     document.getElementById("exportgraph").removeAttribute('data-href');
            //     document.getElementById("exportgraph").setAttribute('data-href',url);
            //     console.log(document.getElementById("exportgraph"));


            // setTimeout(function(){
            // alert("missgygy");
            //  let exportDataRef = document.getElementById("exportgraph").getAttribute('data-href');
            // let splitData = exportDataRef.split('?');
            // let url = splitData[0]+'?plantID='+plantID+'&Date='+date;
            // document.getElementById("exportgraph").removeAttribute('data-href');
            // document.getElementById("exportgraph").setAttribute('data-href',url);
            // console.log(document.getElementById("exportgraph"));
            //  }, 200);
            // exportCsvDataValues(date);
            Inverter = [];

            $.ajax({
                type: 'GET',
                data: {
                    'serialNo': serial_no,
                    'date': date,
                    'time': time,
                    'plantID': plantID,
                    'inverterArray': inverterCheckBoxArray
                },
                url: "{{ url('admin/graph/plant/inverter') }}",
                success: function (data) {
                    //     let exportDataRef = document.getElementById("exportgraph").getAttribute('data-href');
                    // let splitData = exportDataRef.split('?');
                    // let url = splitData[0]+'?plantID='+plantID+'&Date='+date;
                    // document.getElementById("exportgraph").removeAttribute('data-href');
                    // document.getElementById("exportgraph").setAttribute('data-href',url);
                    // console.log(document.getElementById("exportgraph"));
                    console.log(data);
                    console.log("CHNAGE ON DATE CHANGE");
                    InverterTimeData = data['time_array'];
                    InverterTimeType = time;
                    InverterDate = data.tooltip_date;
                    NoOfInverters = data.legend_array;
                    // console.log("Inverters" + InverterTimeData);
                    $.each(data.plant_inverter_graph, function (index, item) {

                        InverterName.push(item.name);
                        InverterDCOutput = item.data;

                        if (item.name.includes("DC Power")) {
                            Inverter = Object.values(item.data)
                            InputDcPower.push(Inverter);
                            console.log("Inpput Dc Power Test" + InputDcPower);
                            console.log(typeof (InputDcPower));
                        }
                        if (item.name.includes("Output Power")) {
                            InverteroutputPower.push(item.data);
                            console.log("Output power  Test aaaaaaaa" + InverteroutputPower);
                            console.log(typeof (InverteroutputPower));
                        }
                        // Inverter.push(item.data);

                    });


                    $('.inverterGraphError').hide();
                    $('.inverterGraphSpinner').hide();
                    //$('.generationPlantGraph').html('Generation: ' + data.total_generation_value);

                    $('#inverterGraphDiv-' + inverterSerialNo).append(`<div class="ch_tr_vt"><span></span></div><div id="inverterGraphChartDiv-${inverterSerialNo}" style="height: 330px;margin-top: 70px;width: 100%;">`);

                    plantInverterGraph(serial_no, data, date, inverterSerialNo);
                },
                error: function (data) {
                    console.log(data);
                    $('.inverterGraphError').show();
                    $('.inverterGraphSpinner').hide();
                }
            });

            console.log(Inverter)
        }

        // function inverterExportGraphAjax(serial_no, date, time, inverterCheckBoxArray) {


        //     var inverterSerialNo = $('.inverterCarouselList').find('li.active').attr('data-slide-to');
        //     console.log(inverterSerialNo);
        //     $graph_div = 'div#graphDiv_' + serial_no;
        //     $('#inverterGraphDiv-'+inverterSerialNo).empty();
        //     $('.inverterGraphSpinner').show();
        //     $('.inverterGraphError').hide();
        //     $('.generationPlantGraph').html('');
        //     var plantID = $('#plantID').val();
        //     Inverter=[];

        //     $.ajax({
        //         type: 'GET',
        //         data: {
        //             'serialNo': serial_no,
        //             'date': date,
        //             'time': time,
        //             'plantID': plantID,
        //             'inverterArray': inverterCheckBoxArray
        //         },
        //         url: "{{ route('export.inverter.graph') }}",
        //         success: function(data) {

        //             console.log(data);
        //             $('.inverterGraphError').hide();
        //             $('.inverterGraphSpinner').hide();
        //             //$('.generationPlantGraph').html('Generation: ' + data.total_generation_value);

        //             $('#inverterGraphDiv-'+inverterSerialNo).append(`<div class="ch_tr_vt"><span></span></div><div id="inverterGraphChartDiv-${inverterSerialNo}" style="height: 330px;margin-top: 70px;width: 100%;">`);

        //             exportinverterCsv(serial_no, data, date, inverterSerialNo);
        //         },
        //         error: function(data) {
        //             console.log(data);
        //             $('.inverterGraphError').show();
        //             $('.inverterGraphSpinner').hide();
        //         }
        //     });

        //     console.log(Inverter)
        //     }

        function plantPowerGraph(plantsPowerGraphData) {

            var data = plantsPowerGraphData.plant_power_graph;
            var powerValue = plantsPowerGraphData.power_value;
            var capacityValue = plantsPowerGraphData.capacity_value;
            var tooltipCapacityValue = plantsPowerGraphData.total_capacity_value;
            var totalValue = plantsPowerGraphData.total_value;
            var dom = document.getElementById("plantsPowerChart");
            var myChart = echarts.init(dom);
            var app = {};

            option = {
                tooltip: {
                    trigger: 'item',
                    position: ['13%', '1%'],
                    intersect: false,
                    formatter: function (p) {
                        if (p.name == 'Installed Capacity') {

                            return `${p.name}: ${energyFormatter(tooltipCapacityValue)}Wp`;
                        } else {

                            return `${p.name}: ${energyFormatter(p.value)}W`;
                        }
                    }
                },
                series: [{
                    startAngle: 180,
                    endAngle: 360,
                    type: 'pie',
                    radius: ['80%', '90%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: false,
                            fontSize: '10',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    color: totalValue != 0 ? (powerValue >= totalValue ? ['#f82c1c'] : ['#86BD4D', '#e0e0e0']) : ['#e0e0e0'],
                    data: totalValue != 0 ? data : [
                        {
                            value: 1,
                            name: '',
                            tooltip: {
                                show: false
                            }
                        },
                        {
                            value: 1,
                            name: null,
                            itemStyle: {
                                opacity: 0
                            },
                            tooltip: {
                                show: false
                            }
                        }
                    ]
                }],
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantConsumptionGraph(plantsConsumptionGraphData) {

            console.log(plantsConsumptionGraphData);

            var data = plantsConsumptionGraphData.plant_consumption_graph;
            var totalValue = plantsPowerGraphData.total_value;
            var dom = document.getElementById("plantsConsumptionChart");
            var myChart = echarts.init(dom);
            var app = {};
            console.log('consumption' + data);

            option = {
                tooltip: {
                    show: false,
                    trigger: 'item',
                    position: ['15%', '20%'],
                    fontSize: 8,
                    /*formatter: function(p) {
                    return `${p.name}: ${p.value} kWh`;
                }*/
                },
                series: [{
                    startAngle: 180,
                    endAngle: 360,
                    type: 'pie',
                    radius: ['75%', '95%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: false,
                            fontSize: '10',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    color: ['#86BD4D', '#e0e0e0'],
                    data: data
                }],
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantExpectedGraph(plantsActualExpectedGraphData, timeType) {

            var data = plantsActualExpectedGraphData.plant_actual_expected_graph;
            var tooltipData = plantsActualExpectedGraphData.total_expected_value;
            var totalValue = plantsActualExpectedGraphData.total_value;
            var actualValue = plantsActualExpectedGraphData.actual_value;
            var expectedValue = plantsActualExpectedGraphData.expected_value;
            var dom = document.getElementById("plantsExpectedChart");
            var myChart = echarts.init(dom);
            var app = {};
            console.log('expected' + data);

            option = {
                tooltip: {
                    trigger: 'item',
                    position: ['40%', '20%'],
                    intersect: false,
                    formatter: function (p) {

                        if (timeType == 'day') {

                            if (p.name == 'Expected') {

                                return `${p.name}: ${tooltipData} kWh`;
                            } else {

                                return `${p.name}: ${p.value} kWh`;
                            }
                        }
                    }
                },
                series: [{
                    startAngle: 180,
                    endAngle: 360,
                    type: 'pie',
                    radius: ['80%', '90%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: false,
                            fontSize: '10',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    color: totalValue != 0 ? (actualValue > totalValue ? ['#f82c1c'] : ['#86BD4D', '#F6A944']) : ['#e0e0e0'],
                    data: totalValue != 0 ? data : [{
                        value: 1,
                        name: '',
                        tooltip: {
                            show: false
                        },
                    },
                        {
                            value: 1,
                            name: null,
                            itemStyle: {
                                opacity: 0
                            },
                            tooltip: {
                                show: false
                            },
                        }
                    ]
                }],
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }

        }

        function energyFormatter(num) {

            if (Math.abs(num) > Math.pow(10, 3) && Math.abs(num) <= Math.pow(10, 6)) {
                return Math.sign(num) * ((Math.abs(num) / Math.pow(10, 3)).toFixed(2)) + ' M';
            } else if (Math.abs(num) > Math.pow(10, 6) && Math.abs(num) <= Math.pow(10, 9)) {
                return Math.sign(num) * ((Math.abs(num) / Math.pow(10, 6)).toFixed(2)) + ' G';
            } else if (Math.abs(num) > Math.pow(10, 9) && Math.abs(num) <= Math.pow(10, 12)) {
                return Math.sign(num) * ((Math.abs(num) / Math.pow(10, 9)).toFixed(2)) + ' T';
            } else {
                return Math.sign(num) * Math.abs(num) + ' k';
            }

        }

        function costFormatter(num) {

            if (Math.abs(num) > 999 && Math.abs(num) <= 999999) {
                return Math.sign(num) * ((Math.abs(num) / 1000).toFixed(2)) + ' K';
            } else if (Math.abs(num) > 999999 && Math.abs(num) <= 9999999999) {
                return Math.sign(num) * ((Math.abs(num) / 1000000).toFixed(2)) + ' M';
            } else {
                return Math.sign(num) * Math.abs(num);
            }

        }

        function plantHistoryGraph(plantsHistoryGraphData) {

            var data = plantsHistoryGraphData.plant_history_graph;
            var axisData = plantsHistoryGraphData.y_axis_array;
            var time = plantsHistoryGraphData.time_array;
            var timeType = plantsHistoryGraphData.time_type;
            var legendArray = plantsHistoryGraphData.legend_array;
            var tooltipDate = plantsHistoryGraphData.tooltip_date;
            var dom = document.getElementById("plantsHistoryChart");
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {

                tooltip: {
                    trigger: 'axis',
                    textStyle: {
                        //fontFamily: 'poppins, sans-serif',
                        fontStyle: 'bold',
                        fontSize: 12,
                        //color: '#504E4E',
                    },
                    formatter: function (p) {
                        let output = '';
                        for (let i = 0; i < p.length; i++) {
                            if (timeType == 'day') {
                                if (p[i].seriesName == 'Cost Saving') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/saving_history.png')}}"><span style="color:#009FFD;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${costFormatter(p[i].value)} PKR</span>`;
                                } else if (p[i].seriesName == 'Generation') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Consumption') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/consumption_history.png')}}"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Grid') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_history.png')}}"><span style="color:#E38595;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Buy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8FC34D;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Sell') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}W</span>`;
                                } else if (p[i].seriesName == 'Irradiance') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} W/m<sup>2</sup></span>`;
                                }
                            } else {
                                if (p[i].seriesName == 'Cost Saving') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/saving_history.png')}}"><span style="color:#009FFD;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${costFormatter(p[i].value)} PKR</span>`;
                                } else if (p[i].seriesName == 'Generation') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/generation_history.png')}}"><span style="color:#F6A944;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Consumption') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/consumption_history.png')}}"><span style="color:#46C1AB;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Grid') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/grid_history.png')}}"><span style="color:#E38595;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Buy') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/buy_history.png')}}"><span style="color:#8FC34D;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Sell') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/sell_history.png')}}"><span style="color:#3173DA;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${energyFormatter(p[i].value)}Wh</span>`;
                                } else if (p[i].seriesName == 'Irradiance') {
                                    output += `<img src="{{ asset('assets/images/graph_icons/irradiance_history.png')}}"><span style="color:#F933C8;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kWh/m<sup>2</sup></span>`;
                                }
                            }
                            if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                output += '<br/>'
                            }

                        }

                        if (timeType == 'day') {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ', ' + tooltipDate + '</span><br/><br/>' + output;
                        } else if (timeType == 'month') {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + '-' + tooltipDate + '</span><br/><br/>' + output;
                        } else {
                            return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ' ' + tooltipDate + '</span><br/><br/>' + output;
                        }
                    }
                },
                legend: {
                    data: legendArray,
                    /*selected: {
                    'Cost Saving': false,
                },*/
                    //bottom: '-15px',
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '17%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: (timeType == 'day') ? false : true,
                    data: time,
                    // axisTick: {
                    //     interval: (timeType == 'day') ? parseInt((time.length) / 6) : (timeType == 'month') ? 1 : 0
                    // },
                    // axisLabel: {
                    //     interval: (timeType == 'day') ? parseInt((time.length) / 6) : (timeType == 'month') ? 1 : 0
                    // },
                    // axisLabel: {
                    //     formatter: (function(value, index) {
                    //         let array = ['00', '02', '04', '06', '08', '10', '12', '14', '16', '18', '20', '22'];
                    //         let zeroValue = value.split(':');
                    //         if (array[index] !== zeroValue[0]) {
                    //             return array[index];
                    //         } else {
                    //             return array[index] + '00'
                    //         }
                    //     })
                    // },
                    axisLine: {
                        lineStyle: {
                            color: '#666666',
                        },
                        onZero: false,
                    },
                },
                dataZoom: {
                    type: "slider"
                },
                yAxis: axisData,
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantEMIGraph(plantsEMIGraphData, emiCheckBoxArray) {

            var data = plantsEMIGraphData.plant_emi_graph;
            var axisData = plantsEMIGraphData.y_axis_array;
            var time = plantsEMIGraphData.time_array;
            var legendArray = plantsEMIGraphData.legend_array;
            var tooltipDate = plantsEMIGraphData.tooltip_date;
            var dom = document.getElementById("plantsEMIChart");
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {
                tooltip: {
                    trigger: 'axis',
                    textStyle: {
                        fontStyle: 'bold',
                        fontSize: 12,
                        color: '#fff'
                    },
                    backgroundColor: '#2e386a',
                    formatter: function (p) {
                        let output = '';
                        for (let i = 0; i < p.length; i++) {
                            if (p[i].seriesName == 'PV Temperature') {
                                output += `<img src="{{ asset('assets/images/graph_icons/pv_temperature_emi.png')}}"><span style="color:#fff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="color:'#fff';margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} °C</span><br>`;
                            } else if (p[i].seriesName == 'Ambient Temperature') {
                                output += `<img src="{{ asset('assets/images/graph_icons/ambient_temperature_emi.png')}}"><span style="color:#fff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="color:'#fff';margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} °C</span><br>`;
                            } else if (p[i].seriesName == 'Irradiance') {
                                output += `<img src="{{ asset('assets/images/graph_icons/irradiance_emi.png')}}"><span style="color:#fff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="color:'#fff';margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} W/m<sup>2</sup></span><br>`;
                            } else if (p[i].seriesName == 'Wind Speed') {
                                output += `<img src="{{ asset('assets/images/graph_icons/wind_speed_emi.png')}}"><span style="color:#fff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="color:'#fff';margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} m/s</span>`;
                            }
                        }

                        return '<span style="color:#fff;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ', ' + tooltipDate + '</span><br/><br/>' + output;
                    }
                },
                legend: {
                    data: legendArray,
                    textStyle: {
                        color: '#ffffff'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '17%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: time,
                    axisLine: {
                        lineStyle: {
                            color: '#ffffff',
                        },
                        labelStyle: {
                            color: '#ffffff',
                        },
                    },
                },
                dataZoom: {
                    type: "slider"
                },
                yAxis: axisData,
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantPVGraph(plantsPVGraphData, pvCheckBoxArray, pvSerialNo) {

            var data = plantsPVGraphData.plant_pv_graph;
            var axisData = plantsPVGraphData.y_axis_array;
            var time = plantsPVGraphData.time_array;
            var legendArray = plantsPVGraphData.legend_array;
            var tooltipDate = plantsPVGraphData.tooltip_date;
            var dom = document.getElementById("plantsPVChart-" + pvSerialNo);
            var myChart = echarts.init(dom);
            var app = {};

            var option;

            option = {
                tooltip: {
                    trigger: 'axis',
                    textStyle: {
                        fontStyle: 'bold',
                        fontSize: 12,
                        color: '#fff'
                    },
                    backgroundColor: '#2e386a',
                    formatter: function (p) {
                        let output = '';
                        for (let i = 0; i < p.length; i++) {
                            output += `<span style="color:#fff;margin-left:5px;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].seriesName}</span><span style="color:'#fff';margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value}</span><br>`;
                        }

                        return '<span style="color:#fff;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ', ' + tooltipDate + '</span><br/><br/>' + output;
                    }
                },
                legend: {
                    data: legendArray,
                    textStyle: {
                        color: '#ffffff'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '17%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: time,
                    axisLine: {
                        lineStyle: {
                            color: '#ffffff',
                        },
                        labelStyle: {
                            color: '#ffffff',
                        },
                    },
                },
                dataZoom: {
                    type: "slider"
                },
                yAxis: axisData,
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantAlertGraph(plantsAlertGraphData) {

            var data = plantsAlertGraphData.plant_alert_graph;
            var totalValue = plantsAlertGraphData.total_value;

            var dom = document.getElementById("plantsAlertChart");
            var myChart = echarts.init(dom);
            var app = {};

            option = {
                tooltip: {
                    trigger: 'item',
                    position: ['30%', '12%'],
                    intersect: false,
                    formatter: function (p) {
                        return `${p.name}: ${p.value} (${p.percent * 2}%)`;
                    }
                },
                series: [{
                    startAngle: 180,
                    endAngle: 360,
                    type: 'pie',
                    radius: ['70%', '80%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: false,
                            fontSize: '10',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    color: totalValue != 0 ? ['#f6a944', '#f82c1c'] : ['#e0e0e0'],
                    data: totalValue != 0 ? data : [{
                        value: 1,
                        name: '',
                        tooltip: {
                            show: false
                        }
                    },
                        {
                            value: 1,
                            name: null,
                            itemStyle: {
                                opacity: 0
                            },
                            tooltip: {
                                show: false
                            }
                        }
                    ]
                }],
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }
        }

        function plantInverterGraph(serialNo, plantsInverterGraphData, date, inverterSerialNo) {

            var data = plantsInverterGraphData.plant_inverter_graph;
            var axisData = plantsInverterGraphData.y_axis_array;
            var totalGenerationValue = plantsInverterGraphData.total_generation_value;
            //document.getElementByClass('generationPlantGraph').innerHTML = 'Generation: ' + totalGenerationValue;
            var time = plantsInverterGraphData.time_array;
            var timeType = plantsInverterGraphData.time_type;
            var legendArray = plantsInverterGraphData.legend_array;
            var tooltipDate = plantsInverterGraphData.tooltip_date;

            var dom = document.getElementById("inverterGraphChartDiv-" + inverterSerialNo);
            var myChart = echarts.init(dom);
            var app = {};

            var option;
            option = {

                tooltip: {
                    trigger: 'axis',
                    textStyle: {
                        fontFamily: 'roboto',
                        fontStyle: 'bold',
                        fontSize: 12,
                        color: '#504E4E',
                    },
                    formatter: function (p) {
                        let output = '';

                        for (let i = 0; i < p.length; i++) {

                            if (p[i].seriesName.includes('Output') || p[i].seriesName.includes('DC')) {
                                output += p[i].marker + `<span>${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} kW</span>`;
                            } else {
                                output += p[i].marker + `<span>${p[i].seriesName}</span><span style="margin-left:20px;float:right;font-family:'Poppins',sans-serif;font-weight:bold;">${p[i].value} %</span>`;
                            }

                            if (i != p.length - 1) { // Append a <br/> tag if not last in loop
                                output += '<br/>'
                            }

                        }

                        return '<span style="color:#BBB8B8;font-family:"Poppins",sans-serif;font-weight:bold;">' + p[0].axisValue + ', ' + tooltipDate + '</span><br/><br/>' + output;
                    }
                },
                legend: {
                    data: legendArray,
                    textStyle: {
                        color: '#ffffff'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: time,
                    axisLine: {
                        lineStyle: {
                            color: '#fff',
                        }
                    },
                },
                dataZoom: {
                    bottom: -20,
                    type: "slider"
                },
                yAxis: axisData,
                series: data
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }

        }
    </script>
    <script>
        function exportTasks(_this) {
            let exportDataRef = document.getElementById("exportgraph").getAttribute('data-href');
            let _url = exportDataRef;
            window.location.href = _url;

        }
    </script>
    <script>
        var expanded = false;

        function showCheckboxes() {
            var checkboxes = document.getElementById("checkboxes");
            if (!expanded) {
                checkboxes.style.display = "block";
                expanded = true;
            } else {
                checkboxes.style.display = "none";
                expanded = false;
            }
        }
    </script>
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
        var expanded = false;

        function showCheckboxpv() {
            var checkbox = document.getElementById("checkpv");
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
        var expanded = false;

        function showCheckboxinverter() {

            var checkbox1 = document.getElementById("checkboxinverter");

            if (!expanded) {

                checkbox1.style.display = "block";
                expanded = true;
            } else {
                checkbox1.style.display = "none";
                expanded = false;
            }

        }

    </script>
    <!-- @section('script') -->
<script>

</script>
<!-- @endsection -->


@endsection
