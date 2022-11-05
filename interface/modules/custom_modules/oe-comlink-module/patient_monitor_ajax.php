<?php

/**
 *
 *  @package       OpenEMR
 *  @link    https://www.open-emr.org https://affordablecustomehr.com
 *  @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  @copyright Copyright (c) 2022 Sherwin Gaddis <sherwingaddis@gmail.com>
 *  @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */


use Comlink\OpenEMR\Module\Controller\DisplayController;

require_once __DIR__ . "/../../../globals.php";
require_once __DIR__ . "/vendor/autoload.php";


$displayData = new DisplayController();
$query = $displayData->sqlQuery();
$dataArray = array();
$i = 0;
$res = $displayData->processQuery($query);

while ($row = sqlFetchArray($res)) {


    $form_vitals = "SELECT `bps`, `bpd`, `height`, `weight`, `temperature`, `respiration`, `oxygen_saturation` FROM form_vitals WHERE `pid` = " . $row['pid'] . " ORDER BY `id` DESC";
    $form_vitalsres = sqlStatement($form_vitals);
    $form_vitalsrow = sqlFetchArray($form_vitalsres);

    $facility = "SELECT facility.name FROM facility
    INNER JOIN openemr_postcalendar_events ON facility.id = openemr_postcalendar_events.pc_facility WHERE openemr_postcalendar_events.pc_pid = ?";

    $facilityres = sqlStatement($facility, [$row['pid']]) ?? [];
    $facilityrow = sqlFetchArray($facilityres) ?? [];

    $query2 = "SELECT * FROM patient_data  WHERE patient_data.pid = " . $row['pid'];
    $res2 = sqlStatement($query2);

    while ($row2 = sqlFetchArray($res2)) {

        $device_vitals = "SELECT count(*) FROM patient_devices_list WHERE pid=" . $row['pid'];
        $device_vitalsres = sqlStatement($device_vitals);
        $device_vitalsrow = sqlFetchArray($device_vitalsres);
        if (!empty($device_vitalsrow)) {
            if ($device_vitalsrow['count(*)'] > 0) {
                $icons='<a href="form/list_device.php?pid=' . $row['pid'] . '"><i class="material-icons" style="color:blue">ad_units</i></a>';
            } else {
                $icons = '';
            }
        }


        //automatically set if range is set
        if (!empty($row['bp_upper']) && !empty($row['bp_lower'])) {

            $bpUpper = explode("/", $row['bp_upper']);
            $bpLower = explode("/", $row['bp_lower']);

            if (($form_vitalsrow['bps'] > $bpUpper[0]) || ($form_vitalsrow['bpd'] > $bpUpper[1])) {
                if (($bpUpper[0] == 0) || ($bpUpper[0] == '')) {
                    $limit = '';
                    $limit = '<br>BP limits not set';
                }
                $alert = '<div class="alert alert-info" role="alert"> Needs Attention ' . $limit . '</div>';
            } elseif (($form_vitalsrow['bps'] < $bpLower[0]) || ($form_vitalsrow['bpd'] < $bpLower[1])) {
                $alert = '<div class="alert alert-info" role="alert"> Needs Attention </div>';
            }
        }
        //Manually set
        elseif  ($row['alert'] == "Need Attention") {
            $alert = '<div class="alert alert-info" role="alert">'.$row['alert'] . '</div>';
        }  elseif  ( $row['alert'] == "Monitored") {
            $alert = '<div class="alert alert-danger" role="alert">' . $row['alert'] . '</div>';
        } else {
            $alert ='';
        }

        $dataArray['data'][$i] =  [
            '<a href=form/edit_patient.php?pid=' . $row['pid'] . '>' . $row2['fname'] . $row2['lname'] . $row2['mname'] . '</a>' . $icons,
            $row2['DOB'],
            $row['pid'],
            $facilityrow['name'] ?? 0,
            $form_vitalsrow['bps'] . '/' . $form_vitalsrow['bpd'],
            round($form_vitalsrow['temperature'], 2),
            $row['bs_upper'],
            round($form_vitalsrow['respiration'], 2),
            '',
            $form_vitalsrow['oxygen_saturation'],
            round($form_vitalsrow['weight'], 2),
            round($form_vitalsrow['height'], 2),
            $row['pain_upper'],
            $alert,


        ];
        $i++;
    }
}

echo json_encode($dataArray);
