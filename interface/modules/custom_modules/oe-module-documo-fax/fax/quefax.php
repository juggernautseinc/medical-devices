<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

use OpenEMR\Modules\Documo\Database;
use OpenEMR\Modules\Documo\SendFaxConfig;
use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";

$file = explode("/", $_GET['file']);
$d = date("Y-m-d");
$s = date('H:i:s');
$queFile = $d . "_" . $s . "_" . $file[2];
$que = dirname(__FILE__, 6) . "/sites/" . $_SESSION['site_id'] . "/documents/documo/outbound/" . $queFile;

$dir = new SendFaxConfig();
$destinations = new Database();
$isDir = $dir::faxDir();
$places = $destinations->getOrganizations();
$move_file = copy($_GET['file'], $que);

if ($isDir == "Found" || $isDir == 'Created') {
    $move_file;
}  else {
    echo xlt('Fax directories were not created. Check php error log to see what the issue is.');
    die;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Que Fax Document</title>
    <?php Header::setupHeader(['common', 'select2']); ?>
    <script>
        $(function() {
            $(".select-dropdown").select2({
                theme: "bootstrap4",
                <?php require($GLOBALS['srcdir'] . '/js/xl/select2.js.php'); ?>
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2 class="mt-2 mb-2">Send Document to Fax Que</h2>
        <form action="faxque.php" method="post" id="theform" >
            <input type="hidden" name="csrf_token" value="<?php echo CsrfUtils::collectCsrfToken(); ?>">
            <input type="hidden" name="file" value="<?php echo $que; ?>">
            <label for="number">Select from address book</label>
            <select name="number" id="number" class="select-dropdown" onchange="changeAttribute()">
                <?php
                    print "<option>" . xlt('Select Destination') . "</option>";
                    while ($row = sqlFetchArray($places)) {
                        print "<option value='" . $row['fax'] ."@" . $row['organization'] . "'>" . $row['specialty'] . " " . $row['organization'] . " " . $row['street'] . " " . $row['fax'] . "</option>";
                    }
                ?>
            </select>
            <div class="form-group mt-2">
                <label for="subject"><?php echo xlt('Subject'); ?>:</label>
                <input type="text" class="form-control" name="subject" placeholder="Please add a subject">
            </div>
            <div class="form-group">
                <label for="name"><?php echo xlt('Name'); ?>:</label>
                <input type="text" class="form-control" placeholder="Name of person or organization. Use to add additional name also" name="name">
            </div>
            <div class="form-group" id="faxnumber">
                <label for="faxnumber"><?php echo xlt('Number'); ?>:</label>
                <input  type="text" class="form-control" placeholder="Fax Number. Use only if not selecting from address book" name="faxnumber">
            </div>
            <input class="btn btn-primary" type="submit" value="Send" onsubmit="return top.restoreSession()">
        </form>
    </div>
<script>
    function changeAttribute() {
        const x = document.getElementById('faxnumber');
        x.style.display = 'none';
    }
</script>
</body>
</html>

