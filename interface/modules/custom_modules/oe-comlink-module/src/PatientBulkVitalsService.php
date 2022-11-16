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

namespace Comlink\OpenEMR\Module;

use OpenEMR\Common\Database\QueryUtils;
use OpenEMR\Common\Uuid\UuidRegistry;
use OpenEMR\RestControllers\EncounterRestController;
use OpenEMR\Services\EncounterService;
use OpenEMR\Services\FacilityService;
use OpenEMR\Services\UserService;
use OpenEMR\Services\VitalsService;
use OpenEMR\Validators\ProcessingResult;

class PatientBulkVitalsService
{
    const PRIMARY_USER = 1;

    /**
     * Comlink Medical devices bulk import of vital records into the patient chart
     * Inserts/Updated a new patient record.
     *
     * @param $data The patient fields (array) to insert.
     * @return ProcessingResult which contains validation messages, internal error messages, and the data
     * payload.
     */
    public static function getUsernames($deviceid)
    {
        // why was this function called getUsernames when it returns the pid here?
        $pid = QueryUtils::fetchSingleValue("SELECT pid FROM patient_devices_list WHERE deviceid=?", 'pid', [$deviceid]);

        return $pid;
    }

    public static function getuser_facility()
    {
        $userService = new UserService();
        $user = $userService->getUser(self::PRIMARY_USER);
        // TODO: this seems odd to default to the facility of the admin user....
        $facilityService = new FacilityService();
        $facility = $facilityService->getFacilityForUser(self::PRIMARY_USER);

        if (!empty($facility)) {
            $facility['username'] = $user['username'];
        }
        return $facility;
    }

    public static function get_uuid($pid)
    {
        $uuid = QueryUtils::fetchSingleValue("SELECT uuid FROM `patient_data` WHERE `pid`=?", 'uuid', [$pid]);
        if (!empty($uuid)) {
            return UuidRegistry::uuidToString($uuid);
        }
        return null;
    }

    public static function getform_encounter_id($pid)
    {
        $encounterService = new EncounterService();
        $encounter = $encounterService->getMostRecentEncounterForPatient($pid);
        return $encounter['eid'] ?? null;
    }

    public function insertbulkpatient($data)
    {
        $re = [];

        $re ['numRecords'] = count($data['bulkVitals']);
        $re_in = [];
        $re_in_total = [];
        foreach ($data['bulkVitals'] as $d) {
            $deviceid = $d['subDeviceID'];
            $pid = $this->getUsernames($deviceid);
            // TODO: what happens if facility is null, this will blow up
            $getuser_facility = $this->getuser_facility();
            $d['facility'] = $getuser_facility['name'];
            $d['facility_id'] = $getuser_facility['facility_id'];
            $puuid = self::get_uuid($pid);
            $d['sensitivity'] = "normal";
            $d["onset_date"] = date('Y-m-d h:i:s');
            $d["reason"] = 'Vitals';
            // TODO: should these be setup as settings in the globals for the module?  This requires a code change if the provider is deleted or anything.
            $d['provider_id'] = "1";
            $b['billing_facility'] = "3";

            // TODO: not sure why use the rest controller when the EncounterService controller could be used just as well, odd to jump up an abstraction layer instead of staying at the data access layer
            $geteid = (new EncounterRestController())->post($puuid, $d);
            $getform_encounter_id = $this->getform_encounter_id($pid);

            $d['username'] = $getuser_facility['username'];
            $d['groupname'] = 'Default';
            $d['bps'] = $d['vitalsData']['ctsiSystolic'];
            $d['bpd'] = $d['vitalsData']['ctsiDiastolic'];
            $d['weight'] = $d['vitalsData']['ctsiWeight'];
            $d['height'] = "";
            $d['temperature'] = $d['vitalsData']['ctsiTemperature'];
            $d['temp_method'] = "";
            $d['pulse'] = $d['vitalsData']['ctsiPulse'];
            $d['respiration'] = "";
            $d['note'] = "";
            $d['waist_circ'] = "";
            $d['head_circ'] = "";
            $d['oxygen_saturation'] = $d['vitalsData']['ctsiSpo2'];
            $d['temp_method'] = "Device";

            $serviceResult = $this->insertVital($pid, $getform_encounter_id, $d);
            $re_in['actionCode'] = 'ADD';
            $re_in['errorCode'] = '200';
            $re_in['errorDesc'] = 'Success';
            $re_in['subEhrEmrID'] = $d['subEhrEmrID'];
            $re_in['deviceID'] = $deviceid;
            array_push($re_in_total, $re_in);
        }
        $re['bulkDataResp'] = $re_in_total;
        return $re;
    }

    private function insertVital($pid, $eid, $data)
    {
        $vitalService = new VitalsService();
        $data['pid'] = $pid;
        $data['eid'] = $eid;
        $result = $vitalService->save($data);
        return $result;

        // TODO: not sure why we were doing manual insertion here of the vitals instead of using the vital service
        // which will handle uuids, and any other vital setup here, including notifying any event listeners etc
//
//        $vitalSql  = " INSERT INTO form_vitals SET";
//        $vitalSql .= "     date = NOW(),";
//        $vitalSql .= "     activity = 1,";
//        $vitalSql .= "     pid = ?,";
//        $vitalSql .= "     bps = ?,";
//        $vitalSql .= "     bpd = ?,";
//        $vitalSql .= "     weight = ?,";
//        $vitalSql .= "     height = ?,";
//        $vitalSql .= "     temperature = ?,";
//        $vitalSql .= "     temp_method = ?,";
//        $vitalSql .= "     pulse = ?,";
//        $vitalSql .= "     respiration = ?,";
//        $vitalSql .= "     note = ?,";
//        $vitalSql .= "     waist_circ = ?,";
//        $vitalSql .= "     head_circ = ?,";
//        $vitalSql .= "     oxygen_saturation = ?,";
//        $vitalSql .= "     user = ?,";
//        $vitalSql .= "     groupname = ?";
//
//        $vitalResults = sqlInsert(
//            $vitalSql,
//            array(
//                $pid,
//                $data["bps"],
//                $data["bpd"],
//                $data["weight"],
//                $data["height"],
//                $data["temperature"],
//                $data["temp_method"],
//                $data["pulse"],
//                $data["respiration"],
//                $data["note"],
//                $data["waist_circ"],
//                $data["head_circ"],
//                $data["oxygen_saturation"],
//                $data['username'],
//                $data['groupname'],
//
//            )
//        );
//
//        if (!$vitalResults) {
//            return false;
//        }
//
//        $formSql = "INSERT INTO forms SET";
//        $formSql .= "     date = NOW(),";
//        $formSql .= "     encounter = ?,";
//        $formSql .= "     form_name = 'Vitals',";
//        $formSql .= "     authorized = '1',";
//        $formSql .= "     form_id = ?,";
//        $formSql .= "     pid = ?,";
//        $formSql .= "     user = ?,";
//        $formSql .= "     groupname = ?,";
//        $formSql .= "     formdir = 'vitals'";
//
//        $formResults = sqlInsert(
//            $formSql,
//            array(
//                $eid,
//                $vitalResults,
//                $pid,
//                $data['username'],
//                $data['groupname']
//            )
//        );
//
//        return array($vitalResults, $formResults);
    }
}