<?php
header("Content-Type:text/css");
$color = "#f0f"; // Change your Color Here

function checkhexcolor($color)
{
    return preg_match('/^#[a-f0-9]{6}$/i', $color);
}

if (isset($_GET['color']) and $_GET['color'] != '') {
    $color = "#" . $_GET['color'];
}

if (!$color or !checkhexcolor($color)) {
    $color = "#293846";
}
function hex2rgba($color, $opacity = false)
{

    $default = 'rgb(0,0,0)';

    //Return default if no color provided
    if (empty($color))
        return $default;

    //Sanitize $color if "#" is provided
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }

    //Convert hexadec to rgb
    $rgb =  array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if ($opacity) {
        if (abs($opacity) > 1)
            $opacity = 1.0;
        $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
    } else {
        $output = 'rgb(' . implode(",", $rgb) . ')';
    }

    //Return rgb(a) color string
    return $output;
}

?>
<style>
    .bg-tsk-0-1 {
        background: <?php echo hex2rgba($color, .1) ?>;
    }

    .card.card-tsk {
        border-color: <?php echo $color ?>;
    }

    .card.card-tsk .card-footer,
    .card.card-tsk .card-header {
        background: <?php echo hex2rgba($color, .5) ?>;
        color: #ffffff;
        padding-top: 2px;
        padding-bottom: 2px;
    }

    .card.card-tsk .card-header {
        border-bottom: 1px solid <?php echo $color ?>;
    }

    .card.card-tsk .card-footer {
        border-top: 1px solid <?php echo $color ?>;
    }

    .page-title {
        background: <?php echo hex2rgba($color, .1) ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $color ?>;
    }

    .page-title i,
    .page-title h1,
    .page-title h2,
    .page-title h3,
    .page-title h4,
    .page-title h5,
    .page-title h6 {
        font-size: 20px;
        color: <?php echo $color ?>;
    }

    .sidebar-dark ul li a:hover,
    .sidebar-dark ul .active a {
        color: rgba(255, 255, 255, 1);
    }

    .sidebar-dark ul li a:hover img {
        filter: brightness(0) invert(1);
    }

    .sidebar-dark ul .active a {
        background: <?php echo $color ?>;
    }

    .sidebar-dark ul li a:hover {
        background: <?php echo hex2rgba($color, .5) ?>;
    }

    .page-item.active .page-link {
        z-index: 1;
        color: #fff;
        background-color: <?php echo $color ?>;
        border-color: <?php echo $color ?>;
    }

    .pagination-center .pagination {
        justify-content: center;
    }

    .text-tsk {
        color: <?php echo $color ?>;
    }


    label {
        color: #676666;
        font-size: 13px;
        font-family: 'Open Sans', sans-serif;
        font-weight: 400;
        width: 100%;
    }

    ::-webkit-input-placeholder {
        /* Edge */
        color: #DCDCDC !important;
        font-size: 13px !important;
        font-family: 'Open Sans', sans-serif !important;
        font-weight: 400 !important;
    }

    :-ms-input-placeholder {
        /* Internet Explorer 10-11 */
        color: #DCDCDC;
        font-size: 13px;
        font-family: 'Open Sans', sans-serif;
        font-weight: 400;
    }

    ::placeholder {
        color: #DCDCDC;
        font-size: 13px;
        font-family: 'Open Sans', sans-serif;
        font-weight: 400;
    }


    a {
        color: #0cbeff;
        text-decoration: none;
    }

    a:hover {
        color: <?php echo $color ?>;
        text-decoration: none;
    }

    .bg-tsk {
        background-color: <?php echo $color ?> !important;
    }

    /* .bg-tsk-o-1 {
        background-color:  <?php echo hex2rgba($color, .1) ?> !important;
    } */
    .btn_action_vt {
        width: 74px;
        border-radius: 4px;
        background: #1BB394 !important;
        text-align: center;
        color: #fff;
        padding: 2px 0;
    }

    .card-header.bg-white h4,
    h2 {
        color: #676A6C;
        font-size: 15px;
        font-family: 'Open Sans', sans-serif;
    }

    .active-tsk {
        background: <?php echo $color ?> !important;
        color: #fff;
    }

    a.bg-tsk:hover,
    a.bg-tsk:focus,
    button.bg-tsk:hover,
    button.bg-tsk:focus {
        background-color: <?php echo $color ?> !important;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
        font-size: 13px;
        font-weight: 600;
        color: #393737;
        font-family: 'Open Sans', sans-serif;
    }

    .table-sm td {
        padding: .3rem;
        font-size: 13px;
        font-weight: 400;
        font-family: 'Open Sans', sans-serif;
    }

    .btn-tsk {
        color: #fff !important;
        background-color: <?php echo $color ?>;
        border-color: <?php echo $color ?>;
    }

    .btn-tsk:hover {
        color: #fff;
        background-color: <?php echo $color ?>;
        border-color: <?php echo $color ?>;
    }

    .btn-tsk:focus,
    .btn-tsk.focus {
        box-shadow: 0 0 0 0.2rem <?php echo hex2rgba($color, .5) ?>;
    }

    .btn-tsk.disabled,
    .btn-tsk:disabled {
        color: #fff;
        background-color: <?php echo $color ?>;
        border-color: <?php echo $color ?>;
    }

    .btn-tsk:not(:disabled):not(.disabled):active,
    .btn-tsk:not(:disabled):not(.disabled).active,
    .show>.btn-tsk.dropdown-toggle {
        color: #fff;
        background-color: <?php echo $color ?>;
        border-color: <?php echo $color ?>;
    }

    .btn-tsk:not(:disabled):not(.disabled):active:focus,
    .btn-tsk:not(:disabled):not(.disabled).active:focus,
    .show>.btn-tsk.dropdown-toggle:focus {
        box-shadow: 0 0 0 0.2rem <?php echo hex2rgba($color, .5) ?>;
    }

    .btn-outline-tsk {
        color: <?php echo $color ?> !important;
        background-color: transparent;
        background-image: none;
        border-color: <?php echo $color ?>;
    }

    .btn-outline-tsk:hover {
        color: #fff !important;
        background-color: <?php echo $color ?>;
        border-color: <?php echo $color ?>;
    }

    .btn-outline-tsk:focus,
    .btn-outline-tsk.focus {
        box-shadow: 0 0 0 0.2rem <?php echo hex2rgba($color, .5) ?>;
    }

    .btn-outline-tsk.disabled,
    .btn-outline-tsk:disabled {
        color: <?php echo $color ?>;
        background-color: transparent;
    }

    .btn-outline-tsk:not(:disabled):not(.disabled):active,
    .btn-outline-tsk:not(:disabled):not(.disabled).active,
    .show>.btn-outline-tsk.dropdown-toggle {
        color: #fff;
        background-color: <?php echo $color ?>;
        border-color: <?php echo $color ?>;
    }

    .btn-outline-tsk:not(:disabled):not(.disabled):active:focus,
    .btn-outline-tsk:not(:disabled):not(.disabled).active:focus,
    .show>.btn-outline-tsk.dropdown-toggle:focus {
        box-shadow: 0 0 0 0.2rem <?php echo hex2rgba($color, .5) ?>;
    }

    .toggle-handle {
        background: <?php echo $color ?>;
    }

    .form-control:focus {
        border-color: <?php echo hex2rgba($color, .5) ?>;
    }

    .card {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgb(0 0 0 / 0%) !important;
        border-radius: .25rem;
    }

    .breadcrumb {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        padding: 0px 0 10px 0;
        margin-bottom: 0;
        list-style: none;
        background-color: #e9ecef00;
        border-radius: 0;
        width: 100%;
    }

    .breadcrumb a {
        color: #676666;
    }

    .btn_group_search {
        width: 200px;
        display: flex;
        float: right;
    }

    .btn_group_search label {
        color: #676A6C;
        font-size: 12px;
        margin: 0;
        line-height: 24px;
        margin-right: 5px;
    }
</style>