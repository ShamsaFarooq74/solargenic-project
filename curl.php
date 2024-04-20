
<?php
    $data1 = [
    'userName' => 'viper.bel',
    'password' => 'vdotb021',
    'lifeMinutes' => '240',
];

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://67.23.248.117:8089/api/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30000,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POSTFIELDS => json_encode($data1),
    CURLOPT_HTTPHEADER => array(
    	// Set here requred headers
        "accept: */*",
        "accept-language: en-US,en;q=0.8",
        "content-type: application/json",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
}
    $res = json_decode($response);
    $token = $res->data;
    // echo '<pre>';print_r($token);exit;

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://67.23.248.117:8089/api/site/live/SP201026",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_TIMEOUT => 30000,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_HTTPHEADER => array(
    	// Set Here Your Requesred Headers
        'Content-Type: application/json',
        'X-API-Version: 1.0',
        'Authorization: Bearer ' . $token,
    ),
));
$response1 = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo '<pre>';print_r(json_decode($response1));
}



?>