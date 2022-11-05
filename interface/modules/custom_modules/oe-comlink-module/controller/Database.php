<?php

namespace Comlink\OpenEMR\Module;

class Database
{
    /**
     * @return array
     */
    public function getFacilities()
    {
        $sql = "select id, name from facility";
        $list = sqlStatement($sql);
        $facilities_list = [];
        while ($row = sqlFetchArray($list)) {
            $facilities_list[] = $row;
        }
        return $facilities_list;
    }

      /**
     * @return array
     */
    public function getProviders()
    {
        $sql = "SELECT id, fname, lname FROM users WHERE active ='1'";
        $list = sqlStatement($sql);
        $providers_list = [];
        while ($row = sqlFetchArray($list)) {
            $providers_list[] = $row;
        }

        return $providers_list;
    }
    public function getpatientdata()
    {
        $sql = "SELECT id, fname, lname,pid FROM patient_data";
        $list = sqlStatement($sql);
        $providers_list = [];
        while ($row = sqlFetchArray($list)) {
            $providers_list[] = $row;
        }

        return $providers_list;
    }
    public function getpatientDevices()
    {
        $sql = "SELECT id,`devicemodal`,`subehremrid` FROM devices_list WHERE pid IS NULL";
        $list = sqlStatement($sql);
        $providers_list = [];
        while ($row = sqlFetchArray($list)) {
            $providers_list[] = $row;
        }

        return $providers_list;
    }
    public function getpatientDevicesAll()
    {
        $sql = "SELECT * FROM devices_list " ;
        $list = sqlStatement($sql);
        $providers_list = [];
        while ($row = sqlFetchArray($list)) {
            $providers_list[] = $row;
        }

        return $providers_list;
    }
   
    public function getUuid($pid)
    {
        $sql = "SELECT `uuid` FROM `patient_data` WHERE `pid` = ?";
        return sqlQuery($sql, [$pid]);
    }

}
