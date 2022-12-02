<?php

/**
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

use OpenEMR\Core\Header;
use OpenEMR\Modules\Documo\ApiDispatcher;
use OpenEMR\Modules\Documo\Database;

require_once dirname(__FILE__, 4) . "/globals.php";
require_once dirname(__FILE__) . "/vendor/autoload.php";

$clinicData = new Database();
$registration = new ApiDispatcher();
$clinic = $clinicData->registerFacility();
$registration->registration($clinic);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Welcome to the module</title>
        <?php echo Header::setupHeader(); ?>
        <style>
            .note {
                color: #942a25;
                font-size: medium;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div>
                <h1><?php echo xlt("Welcome") ?></h1>
            </div>
            <div>
                <p><?php echo xlt("Our document workflow solutions make sending faxes within this application easy, more secure, and better for our planet") ?>.</p>
            </div>
            <div>
                <h2 class="font-weight-bold pb-4"><?php echo xlt("Enterprise") ?> <span class="text-primary"><?php echo xlt("cloud fax") ?></span> <?php echo xlt("for regulated industries") ?></h2>
                <p class="pr-xl-9 font-size-md-font-weight-bold"><?php echo xlt("The easiest way for both large and small businesses to achieve real digital transformation. Save time and money by eliminating hardware and outsourcing fax to the cloud. Easily integrate secure and reliable cloud fax into existing apps and workflows") ?>.</p>
            </div>
            <div>
                <p class="note"><?php echo xlt("Use the Fax Module selection from the main menu to configure the settings") ?>.</p>
                <p><?php echo  xlt("Log out and log back in to the program to see the new menu item") ?>.</p>
            </div>
            <div>
                <p><?php echo xlt("This module was developed by") ?> <a href="https://affordablecustomehr.com" target="_blank" ><?php echo xlt("Affordable Custom EHR") ?></a></p>
                <p><?php echo xlt("Please contact them for technical support of this module") ?></p>
                <p>&copy; <?php echo date('Y')?> <?php echo xlt("Juggernaut Systems Express"); ?></p>
            </div>
        </div>
    </body>
</html>
