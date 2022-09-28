<?php 

require_once dirname(__FILE__, 5) . "/globals.php";
$id = $_GET['id'];

$sql = "SELECT * FROM devices_list WHERE id='$id'";
$list = sqlStatement($sql);
$providers_list = [];
while ($row = sqlFetchArray($list)) {
    $providers_list[] = $row;
}


echo json_encode($providers_list);