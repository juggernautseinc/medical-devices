<?php

/**
 *  package   Comlink OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

namespace OpenEMR\Modules\Comlink;



class DisplayController
{
    public function sqlQuery(): string
    {
        return "SELECT * FROM patient_monitoring_form ";
    }
}
