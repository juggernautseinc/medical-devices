<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";

$file = $_GET['filename'];
$location = $GLOBALS['OE_SITE_DIR'] . "/documents/documo/inbound/";
$display = file_get_contents($location . $file);
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=" . $file);
@readfile($location . $file);
