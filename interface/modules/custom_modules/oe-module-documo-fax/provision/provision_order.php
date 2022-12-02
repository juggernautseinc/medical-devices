<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

use OpenEMR\Modules\Documo\Database;
use OpenEMR\Modules\Documo\Provisioning;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Modules\Documo\SendFaxConfig;

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";

if (!CsrfUtils::verifyCsrfToken($_POST['token'])) {
    CsrfUtils::csrfNotVerified();
}

$provision = new Provisioning();

//set order type
$provision->setType($_POST['type']);

//remove token & order type
array_shift($_POST);
array_shift($_POST);

//Need number count
$quantity = count($_POST);
$provision->setQuanity($quantity);

//convert list to a comma seperated string if more than one number is selected
$numbers_list = implode(", ", $_POST);

//set number values
$provision->setNumbers($numbers_list);

//set account ID after retrieving it from database
$accountdata = new Database();
$a = $accountdata->getAccountId();
$provision->setAccountId($a);

//make provision request to documo and set webhook
$request = $provision->numberProvisioning();
if ($request !== 400) {
    //Save the returned JSON
    $accountdata->saveProvisionedNumbers($request);
    //Call the webhook method for inbound faxes
    $wHook = implimentWebHook();
     if ($wHook['error']) {
         echo xlt("There has been an error. ") . $wHook['name'] . ":" . $wHook['message'];
         die;
     } else {
         header("Location: ../account/account_status.php");
     }
}

function implimentWebHook()
{
    //set web hook for inbound faxes
    $dbcall = new Database();
    $setWebHook = new SendFaxConfig();
    //First get user account id and fax number uuid
    $userData = $dbcall->getUserInfo();
    $numberId = $dbcall->getFaxNumbers();
    $setWebHook->setUserEmail($userData['email']);
    $setWebHook->setUserAccount($userData['accountId']);
    $setWebHook->setUserUuid($numberId[0]['uuid']);
    return $setWebHook->createWebHookURI();
}
