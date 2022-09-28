<?php
require_once "../../../../globals.php";
require_once "../includes/api.php";
$pid=$_POST['pid'];



$query = "SELECT * FROM patient_devices_list INNER JOIN patient_data 
ON patient_devices_list.pid=patient_data.pid
WHERE patient_devices_list.id=".$pid;
$res = sqlStatement($query);

while ($row = sqlFetchArray($res)) {

    $fname=$row['fname'];
    $lname=$row['lname'];
    $sub_ehr=$row['subehremrid'];
    $device_id=$row['deviceid'];
    $device_modal=$row['devicemodal'];
    $device_maker=$row['devicemaker'];
    $watch_os=$row['deviceos'];

    
}


$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$Api_url = 'https://proddevbrdg.comlinktelehealth.io:57483/ctsiDevBridge/deleteSubDevice';
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
$reponse=json_decode($resp);
// var_dump($reponse->errorDesc);die;
// if($reponse->errorCode == '200' && $reponse->errorDesc = "OK"){
    sqlQuery("DELETE FROM patient_devices_list WHERE id = '$pid'");
    sqlQuery("UPDATE devices_list set pid = NULL");

    echo 'successfully Delete device..!';
// }else{
//     echo 'Somthing Went Wrong '.$reponse->errorDesc;
// }
    