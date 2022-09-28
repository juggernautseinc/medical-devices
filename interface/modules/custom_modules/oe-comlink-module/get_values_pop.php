<?php
require_once "../../../globals.php";


$id=$_POST['dataID'];
$query = "SELECT * FROM form_vitals WHERE pid=".$id;
$res = sqlStatement($query);
$response=[];
while ($row = sqlFetchArray($res)) {
    $response['height']=$row['height'];
    $response['weight']=$row['weight'];
    $response['temperature']=$row['temperature'];
    $response['bps']=$row['bps'];
    $response['bpd']=$row['bpd'];
    $response['oxygen_saturation']=$row['oxygen_saturation'];
}
echo json_encode($response);
?>