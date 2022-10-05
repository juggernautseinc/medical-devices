<?php

/*
 *  package   Comlink OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/includes/api.php";

$pid=$_POST['pid'];
$sub_ehr=$_POST['sub_ehr'];
$device_id=$_POST['device_id'];
$device_modal=$_POST['device_modal'];
$device_maker=$_POST['device_maker'];
$watch_os=$_POST['watch_os'];


    if (empty($sub_ehr)||empty($device_id)||empty($device_modal)||empty($device_maker)||empty($watch_os)) {
        echo 'please fill all data..!';
    } else {

    $query = "SELECT * FROM patient_devices_list INNER JOIN patient_data ON patient_devices_list.pid=patient_data.pid " .
        "WHERE patient_devices_list.id = " . $pid;
    $res = sqlStatement($query);

    while ($row = sqlFetchArray($res)) {
        $fname=$row['fname'];
        $lname=$row['lname'];
    }

    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
        "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $Api_url = 'https://proddevbrdg.comlinktelehealth.io:57483/ctsiDevBridge/changeSubDevice';
    $payload =
        [
            "firstName" => $fname,
            "lastName" => $lname,
            "subEhrEmrID" => $sub_ehr,
            "deviceData" => [
                "deviceID" => $device_id,
                "deviceModel" => $device_modal,
                "deviceMaker" => $device_maker,
                "deviceOS" => $watch_os,
                "ehrEmrCallBackURL" => $actual_link
            ]
        ];

    $resp = curl_get_content($Api_url, 'POST', json_encode($payload));
    $reponse = json_decode($resp);

    if ($reponse->errorCode == '200' && $reponse->errorDesc = "OK") {
        sqlQuery("UPDATE patient_devices_list SET subehremrid = '$sub_ehr',deviceid = '$device_id',devicemodal = '$device_modal', devicemaker = '$device_maker',deviceos = '$watch_os' WHERE id = '$pid'");

        echo 'successfully Update device..!';
    } else {
        echo 'Somthing Went Wrong '.$reponse->errorDesc;
    }
}
