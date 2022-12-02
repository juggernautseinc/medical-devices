<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

use OpenEMR\Modules\Documo\Database;

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";
echo "header";
$dbcall = new Database();
$userData = $dbcall->getFaxNumbers();

var_dump($userData);
echo "shoud have dumped here";
