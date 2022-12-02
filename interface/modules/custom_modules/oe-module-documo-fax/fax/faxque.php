<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 *  This needs to be move into a set of classes to handle these process and break up this cluster !!@#$%
 */

use OpenEMR\Common\Twig\TwigContainer;
use OpenEMR\Modules\Documo\ApiDispatcher;
use OpenEMR\Modules\Documo\InboundFaxProcessor;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Modules\Documo\Database;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";


$path = dirname(__FILE__, 2) . "/templates";
$twigloader = new TwigContainer($path, $GLOBALS['kernel']);

$status = '';
try {
    $status = new ApiDispatcher();
} catch (Error $e) {
    print $e->getMessage();
}

$twig = $twigloader->getTwig();
$twig->addExtension(new Twig_Extension_Debug());

if (!$_POST['number'] && !$_GET['inbound']) {
    $userinfo = new Database();
    $account = $userinfo->getUserInfo();
    $status->useraccountid = $account['accountId'];
    $status->offset = 0;
    $status->direction = 'outbound';
    $status->limit = 25;
    $status->faxstatus = 'all';
    $history = $status->getFaxHistory();
    $history = json_decode($history, true);
    try {
        print $twig->render('history.twig', [
            'pageTitle' => 'Fax Outbound History',
            'data' => $history,
            'href' => "inbound.php"
        ]);
    } catch (LoaderError|RuntimeError|SyntaxError $e) {
        print $e->getMessage();
    }
} elseif (!$_GET['inbound']) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token"])) {
        CsrfUtils::csrfNotVerified();
    }
    $hook = new Database();
    $number_uuid = $hook->getFaxNumbers();
    $user = $hook->getUserInfo();
    $facility = $hook->getAllAccountInfo();
    $name = '';
    $the_number = explode("@", $_POST['number']);
    $scheduled = date('Y-m-d') . 'T' . date('H:i:s') . '.000Z';
    if ($the_number[0]) {
        $fax_number = $the_number[0];
    }
    if ($_POST['faxnumber']) {
        $fax_number = $_POST['faxnumber'];
    }
    if ($the_number[1]) {
        $name = $the_number[1];
    }
    if ($_POST['name'] && $name) {
        $name = $the_number[1] . " Attn: " . filter_input( INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    }
    //need country code
    $status->faxNumber = trim($hook->countryCode()['gl_value'] . str_replace('-', '', $fax_number));
    $status->filePost = new CURLFILE($_POST['file']);
    $status->name = $name;
    $status->senderName = $facility['accountName'];
    $status->subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
    $status->callerID = $number_uuid[0]['number'];
    $status->schedule = $scheduled;
    $status->tags = $user["account_id"];

    $sent = $status->sendFax();
    $response = json_decode($sent, true);

    $result = '';
    if ($response['resultInfo'] === 'OK') {
        $result = xlt('Fax Successfully Queued for transmission, Check status in the Fax History page');
    } else {
        $result = xlt($response['error']['name']) . " " . xlt($response['error']['message']);
    }

    try {
        print $twig->render('results.twig', [
           'pageTitle' => 'Fax Sent Status',
            'result' => $result,
            'messageId' => $response['messageId'],
            'pagesCount' => $response['pageCount'],
            'faxNumber' => $response['faxNumber']
        ]);
    } catch (LoaderError|RuntimeError|SyntaxError $e) {
        print $e->getMessage();
    }
    unlink($_POST['file']);
}

if ($_GET['inbound'] === 'yes')  {
    $data = new Database();
    $faxesFromWebHook = $data->getInboundFaxesLocal();
    if (empty($faxesFromWebHook)) {
        $account = $data->getUserInfo();
        $status->useraccountid = $account['accountId'];
        $status->offset = 0;
        $status->direction = 'inbound';
        $status->limit = 25;
        $status->faxstatus = 'success';
        $status->setFromDate('2022-01-31T00:00:00.000Z');
        $status->setToDate('2022-01-31T23:59:59.999Z');
        $history = $status->getFaxHistory();
        InboundFaxProcessor::messageIds($history);
        $history = json_decode($history, true);
    } else {
        try {
            print $twig->render('history.twig', [
                'pageTitle' => 'Fax Inbound History',
                'data' => $faxesFromWebHook
            ]);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            print $e->getMessage();
        }
    }


}
