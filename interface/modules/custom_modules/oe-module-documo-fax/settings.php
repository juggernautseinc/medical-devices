<?php

/**
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */


require_once dirname(__FILE__, 4) . "/globals.php";
require_once dirname(__FILE__) . "/vendor/autoload.php";

use OpenEMR\Core\Header;
use OpenEMR\Modules\Documo\Database;

$dbcall = new Database();
$localtz = $dbcall->getTimeZone();
$hastable = $dbcall->tableCheck();
$hasAccount = $dbcall->hasSavedAccount();
$hasUser = $dbcall->hasUserAccount();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt("Documo Setting Page"); ?></title>
    <?php echo Header::setupHeader(['common']) ?>
    <script>
        function createAccount(type) {
            let href = "<?php echo $GLOBALS['web_root'] ?>/interface/modules/custom_modules/oe-module-documo-fax/account/create/create.php?type=" + encodeURIComponent(type);
            dlgopen(href, '', 1175, 700, false, '', {
                buttons: [
                    {text: <?php echo xlj('Close'); ?>, close: true, style: 'default btn-sm'}
                ],
                onClosed: 'reload'
            });
        }
    </script>
    <style>
        .account {
            padding-top: 50px;
        }
    </style>

</head>
<body>
    <div class="container account">
        <?php

            if (empty($localtz['gl_value'])) {
                echo "<h1>" . xlt("Time zone is not set, Please set time zone") . "</h1>";
                die;
             } elseif (!$dbcall->tableCheck()) {
                echo "<h3>" . xlt("Database tables not installed") . "</h3>";
                die;
            } elseif (!isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'on') {
                echo "<h3>" . xlt("You must have a public facing SSL certificate installed to use this module") . "</h3>";
                die;
            }
            ?>
        <div>
            <h1><?php echo xlt("Welcome to the Documo Module"); ?></h1>
            <?php if(!$hasAccount) { ?>
                <h3><?php echo xlt("There are a few steps to complete to enable this module"); ?></h3>
                <p><?php echo xlt("Your server must have a public facing IP to receive inbound faxes."); ?></p>
                <p><?php echo xlt("Your SSL certificate must include ca_bundle.crt file. Or inbound faxes will fail."); ?></p>
                <p><a href="https://www.ssllabs.com/ssltest/" target="_blank" title="please check your SSL certificate">
                        <?php echo xlt("Test your SSL to make sure it has an A rating. "); ?><?php echo xlt("Check SSL"); ?></a></p>
            <?php }
            if (!$hasUser) {?>
                <h3><?php echo xlt("Now, create a user"); ?></h3>
            <?php } else {
                print "<h3>" . xlt("Now, create an Account") . "</h3>";
            } ?>
        </div>
        <?php if(!$hasAccount) { ?>
        <div>
            <h3><?php echo xlt("Step 1"); ?></h3>
            <button class="btn btn-primary" onclick="createAccount('account')"><?php echo xlt("Create an account"); ?></button>
        </div>
        <?php } else {?>
            <div >
                <h3><?php echo xlt("Your fax account is active"); ?>. </h3>
            </div>
        <?php } ?>
        <?php if (!$hasUser) { ?>
        <div>
            <h3><?php echo xlt("Step 2"); ?></h3>
            <button class="btn btn-primary" onclick="createAccount('user')"><?php echo xlt(" Create a user "); ?></button>
        </div>
        <?php }  ?>
        <?php if ($hasAccount && $hasUser) {
            if (!$dbcall->hasNumbersProvisioned()) {
                header('Location: provision/provisioning.php');
            } else {
                header('Location: account/account_status.php');
            }
         } ?>
    </div>
</body>
</html>




