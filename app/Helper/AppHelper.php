<?php

function standard_date_time_format($datetime) {

    return date('h:i A, d-m-Y', strtotime($datetime));
}

function productImagePath($image_name)
{
    return public_path('images/products/'.$image_name);
}

function previousTenMinutesDateTime($date) {

    $currentDataDateTime = new \DateTime($date);
    $currentDataDateTime->modify('-10 minutes');
    $finalCurrentDataDateTime = $currentDataDateTime->format('Y-m-d H:i:s');

    return $finalCurrentDataDateTime;
}