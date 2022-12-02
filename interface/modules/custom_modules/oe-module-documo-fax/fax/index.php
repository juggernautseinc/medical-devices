<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 */

use OpenEMR\Modules\Documo\Database;
use OpenEMR\Common\Logging\EventAuditLogger;

$ignoreAuth = true;
require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";

//webhook
//inbound files from documo

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: multipart/form-data; charset-UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorizations, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
file_put_contents("/var/www/html/errors/uriFile.txt", print_r($_POST, true));
file_put_contents("/var/www/html/errors/uriFile2.txt", print_r($_FILES, true));
$uri = explode('/', $uri);

if ($uri[7] == 'inbound' && !empty($_FILES['attachment']['name'])) {
    $data = new Database();
    $inboundFaxFilesize = $_FILES['attachment']['size'];
    $inbound = dirname(__FILE__, 6) . "/sites/" . $uri[8] . "/documents/documo/inbound/";
    move_uploaded_file($_FILES['attachment']['tmp_name'], $inbound . $_FILES['attachment']['name']);
    http_response_code(200);

    $data->setMessageJson($_POST['data']);
    $data->setFileName($_FILES['attachment']['name']);
    $data->inboundFaxSave();
    EventAuditLogger::instance()->newEvent('fax', '', '', 1, "Inbound Fax: Successful attachment missing");
} else {
    EventAuditLogger::instance()->newEvent('fax', '', '', 1, "Inbound Fax: Unsuccessful attachment missing");
}




