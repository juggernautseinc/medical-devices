<?php

/**
 *  package   Comlink OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

require_once "../../../../globals.php";
require_once dirname(__FILE__, 2) . "/controller/Container.php";

use Laminas\Console\Console;
use OpenEMR\Common\Uuid\UuidRegistry;
use OpenEMR\Modules\Comlink\Container;
use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;

$container = new Container();
$loadDb = $container->getDatabase();

$facilities = $loadDb->getFacilities();
$providers = $loadDb->getProviders();
$patients = $loadDb->getpatientdata();
$patient_devices_list = $loadDb->getpatientDevices();
$patient_devices_listAll = $loadDb->getpatientDevicesAll();
$u = $loadDb->getUuid($_GET['pid']);
$uuid = UuidRegistry::uuidToString($u['uuid']);


if ($_POST) {
    if ($_POST['pro'] == "autocomplete") {
        $search_list = [];
        $type = $_POST['type'];
        if ($type == 'name') {
            $sql = 'SELECT `lname`,`fname` FROM `patient_data`';
            $list = sqlStatement($sql);
            while ($row = sqlFetchArray($list)) {
                $search_list[] = $row['lname'] . ", " . $row['fname'];
            }
        } else {
            $sql = 'SELECT `pid` FROM `patient_data`';
            $list = sqlStatement($sql);
            while ($row = sqlFetchArray($list)) {
                $search_list[] = $row['pid'];
            }
        }

        echo (json_encode($search_list));
    }
} else {
?>
    <!DOCTYPE html>

    <head>
        <?php Header::setupHeader(['report-helper', 'opener']); ?>
        <meta charset="utf-8" />
        <title><?php echo xlt('Add Patients'); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            function sel_patient() {
                let title = '<?php echo xlt('Patient Search'); ?>';
                dlgopen('<? echo dirname(__FILE__, 5) ?>\main\calendar\find_patient_popup.php', '_blank', 650, 300, '', title);
            }
            var $disabledResults = $(".js-example-disabled-results");
            $disabledResults.select2();
        </script>
        <style type="text/css">
            .autocomplete-items {
                position: absolute;
                border: 1px solid #d4d4d4;
                border-bottom: none;
                border-top: none;
                z-index: 99;
                width: 37.5%;
                margin: 5.8% 0% 0% 37%;
            }

            .autocomplete-items div {
                padding: 10px;
                cursor: pointer;
                background-color: #fff;
                border-bottom: 1px solid #d4d4d4;
                position: static;
            }

            /*when hovering an item:*/
            .autocomplete-items div:hover {
                background-color: #e9e9e9;
            }

            /*when navigating through the items using the arrow keys:*/
            .autocomplete-active {
                background-color: DodgerBlue !important;
                color: #ffffff;
            }
        </style>
    </head>

    <body>
        <form role="form" method='post' name='theform' id='theform' action='add_patient.php'>
            <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />

            <input type="hidden" name="pid" id="pid" value="<?php echo $_GET['pid']; ?>" />
            <div class="form-row mx-2">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt("Select from stored devices"); ?>:</label>
                    <select class="form-control opSelect" aria-label="Default select example">
                        <option value="0" selected>Select One</option>
                        <?php foreach ($patient_devices_list as $patient_devices) {

                            echo ' <option value="' . $patient_devices['id'] . '"> ' . $patient_devices['subehremrid'] . '&nbsp;&nbsp;' . $patient_devices['devicemodal'] . '</option>';
                        }

                        ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <h4><?php echo xlt('Or enter a new device'); ?></h4>
            </div>
            <div class="form-row">
                <input class='form-control' type='hidden' name='sub_ehr' id='sub_ehr' autocomplete="off"  value="<?php echo $_SESSION['pid']?>" required />
            </div>
            <div class="form-row mx-2">
                <div class="col-sm form-group">
                    <div class="col-sm form-group">
                        <label for='form_facility'><?php echo xlt('Device Id'); ?>:</label>

                        <input class='form-control' type='text' name='device_id' id='device_id' autocomplete="off" placeholder='<?php echo xla(''); ?>' value='' required />
                    </div>
                </div>
                <div class="col-sm form-group">
                    <div class="col-sm form-group">
                        <label for='form_facility'><?php echo xlt('Device Name'); ?>:</label>

                        <input class='form-control' type='text' name='device_modal' id='device_modal' autocomplete="off" placeholder='<?php echo xla(''); ?>' value='' required />
                    </div>
                </div>
            </div>

            <div class="form-row mx-2">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Device Maker'); ?>:</label>

                    <input class='form-control' type='text' name='device_maker' id='device_maker' autocomplete="off" placeholder='<?php echo xla(''); ?>' value='' required />
                </div>
                <div class="col-sm form-group">
                    <div class="col-sm form-group">
                        <label for='form_facility'><?php echo xlt('Device OS'); ?>:</label>

                        <input class='form-control' type='text' name='watch_os' id='watch_os' autocomplete="off" placeholder='<?php echo xla(''); ?>' value='' required />
                    </div>
                </div>
                <div class="col-sm form-group">
                    <div class="col-sm form-group">


                    </div>
                </div>
            </div>





            <div class="form-row mx-2 mt-3">
                <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-primary" type="submit" name="form_save" id="form_save" value="Add/Assign Device">
                <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-secondary" type="button" id="cancel" onclick="dlgclose()" value="Cancel">
            </div>
        </form>
    </body>
    <script>
        $('.opSelect').change(function(e){
            e.preventDefault();
            var id = $(this).val();
            $.ajax({
                type: 'get',
                url: 'database_list_device.php?id=' + id ,
                dataType: "json",
                error: function(xhr, status, error) {
                    alert(error);
                },
                success: function(results) {

                    $.map(results, function(val, key) {
                        $('#sub_ehr').val(val.subehremrid);
                        $('#device_id').val(val.deviceid);
                        $('#device_modal').val(val.devicemodal);
                        $('#device_maker').val(val.devicemaker);
                        $('#watch_os').val(val.deviceos);
                    });
                }
            });

        });
        $(function() {
            $('form').on('submit', function(e) {

                e.preventDefault();

                $.ajax({
                    type: 'post',
                    url: 'add_device_save.php',
                    data: $('form').serialize(),
                    error: function(xhr, status, error) {
                        alert(error);
                    },
                    success: function(results) {
                        alert(results);
                        location.reload();
                        dlgclose();
                    }
                });

            });

        });

        function autocomplete(inp, arr) {
            var currentFocus;

            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);

                for (i = 0; i < arr.length; i++) {

                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {

                        b = document.createElement("DIV");
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";

                        b.addEventListener("click", function(e) {
                            inp.value = this.getElementsByTagName("input")[0].value;
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    e.preventDefault();
                    if (currentFocus > -1) {
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }


        function onChange() {
            var search_list = [];
            var search = document.getElementById("form_search").value;
            var pro = "autocomplete";

            $.ajax({
                type: 'POST',
                url: "add_patient.php",
                dataType: 'text',
                data: {
                    pro: pro,
                    type: search
                },
                async: false,
                success: function(response) {
                    console.log(response);
                    result = JSON.parse(response);
                    search_list = result;
                    console.log(search_list);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr + " " + ajaxOptions + " " + thrownError);
                }
            });
            autocomplete(document.getElementById("search_name"), search_list);
        }
        window.onload = onChange();

        function addName() {
            var search = document.getElementById("form_search").value;
            var sel_search = document.getElementById("search_name").value;
            var data = "pro=add&type=" + search;
            $.ajax({
                type: 'POST',
                url: "add_patient.php",
                dataType: 'text',
                data: data,
                async: false,
                success: function(response) {
                    // console.log(response);
                    result = JSON.parse(response);
                    search_list = result;
                    // console.log(search_list);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr + " " + ajaxOptions + " " + thrownError);
                }
            });
        }

    </script>

    </html>
<?php } ?>

