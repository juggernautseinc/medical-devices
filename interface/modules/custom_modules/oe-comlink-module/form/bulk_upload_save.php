<?php
require_once "../../../../globals.php";
require_once "../includes/api.php";


$file = $_FILES['file'];

$allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/csv', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
if (in_array($_FILES["file"]["type"], $allowedFileType)) {
    if ($_FILES["file"]["size"] > 0) {
        $targetPath = '../uploads/' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        $file = fopen($targetPath, "r");
        while (($getData = fgetcsv($file, 1000, ",")) !== FALSE) {
            if ($getData[0] == "subEhrEmrID") continue;

            // $pid = $getData[0];
            $sub_ehr = $getData[0];
            $device_id = $getData[1];
            $device_modal = $getData[2];
            $device_maker = $getData[3];
            $watch_os = $getData[4];
            $action = $getData[5];
            $actual_link = '';

            $sql= "SELECT * FROM `devices_list` WHERE `subehremrid` = '$sub_ehr' and `deviceid` = '$device_id' and `devicemodal` = '$device_modal'  and `devicemaker` = '$device_maker' and `deviceos` = '$watch_os'";
            $ad= sqlQuery($sql);

            if($ad == ''){
                $sql ="INSERT INTO `devices_list` (`id`, `subehremrid`,`deviceid`,`devicemodal`, `devicemaker`, `deviceos`) VALUES('','$sub_ehr','$device_id','$device_modal','$device_maker','$watch_os')";
                $ad= sqlQuery($sql);
            }

        }
        echo "successfully uploaded !!!!";
    }

}
