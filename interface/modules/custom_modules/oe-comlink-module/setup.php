<?php

/**
 *
 * @package      Comlink OpenEMR
 * @link               https://www.open-emr.org
 *
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2021 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

use OpenEMR\Core\Header;
use OpenEMR\Modules\Comlink\Container;
use OpenEMR\Common\Acl\AclMain;

require_once dirname(__FILE__, 4) . "/globals.php";
require_once dirname(__FILE__) . "/controller/Container.php";

if (!AclMain::aclCheckCore('admin', 'manage_modules')) {
    echo xlt('Not Authorized');
    exit;
}

$installdatatable = new Container();
$loadTable = $installdatatable->getDatabase();
//table creation
$status = $loadTable->doesTableExist();

$import_table = $installdatatable->getDatabase(); //example code

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Comlink</title>
    <?php Header::setupHeader(['common'])?>
</head>
<body>
<div class="container-fluid">
    <h3>Comlink Inc.</h3>
</div>
</body>
</html>
