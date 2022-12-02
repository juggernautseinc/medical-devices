<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

use OpenEMR\Modules\Documo\Database;
use OpenEMR\Core\Header;
require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";

$data = new Database();
$faxesFromWebHook = $data->getInboundFaxesLocal();
$inbound = $GLOBALS['webroot'] . "/sites/" . $_SESSION['site_id'] . "/documents/documo/inbound/";

const TABLE_TD = "</td><td>";
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inbound Faxes</title>
    <?php Header::setupHeader(['common']); ?>
</head>
<body>
    <div class="container">
        <div class="pt-4">
            <h2><?php echo xlt("Received Faxes"); ?></h2>
        </div>
        <div class="table pt-4">
            <table class="table  table-striped table-bordered">
                <caption>Display of received faxes in the last 30 days</caption>
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Message ID</th>
                    <th scope="col">Fax Number</th>
                    <th scope="col">Page Count</th>
                    <th scope="col">File</th>
                </tr>
                    <?php
                    while ($iter = sqlFetchArray($faxesFromWebHook)) {
                            $message_info = json_decode($iter['message_json'], true);
                            echo "<tr><td>" . $iter['date'] . TABLE_TD . $message_info['messageId'] .
                                TABLE_TD . $message_info['faxNumber'] . TABLE_TD . $message_info['pagesCount'] .
                                TABLE_TD . "<a href='faxdisplay.php?filename=" . $iter['file_name'] . "' target='_blank'>" .
                                $iter['file_name'] . "</a></td></tr>";
                        }
                    ?>
            </table>
        </div>
        <div class="footer pt-4">
            &copy;  <?php echo date ('Y') .  xlt(" Juggernaut Systems Express");  ?>
        </div>
    </div>
</body>
</html>

