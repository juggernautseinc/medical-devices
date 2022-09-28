<?php

require_once "../../../../globals.php";
require_once dirname(__FILE__, 2) . "/controller/Container.php";

use OpenEMR\Modules\Comlink\Container;
use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;

$container = new Container();
$loadDb = $container->getDatabase();
$facilities = $loadDb->getFacilities();
$providers = $loadDb->getProviders();

?>
<!DOCTYPE html>

<head>
    <?php Header::setupHeader(['report-helper', 'opener']); ?>
    <meta charset="utf-8" />
    <title><?php echo xlt('Add Patients'); ?></title>
    <script>
    function sel_patient() {
        let title = '<?php echo xlt('Patient Search'); ?>';
        dlgopen('<? echo dirname(__FILE__, 5) ?>\main\calendar\find_patient_popup.php', '_blank', 650, 400, '', title);
    }
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
    <div class="container">
        <div class="m-5">
        <div class="col-sm-4 form-group">
            <div class="form-row mx-2 mt-4 pt-4">
                <div class="col-sm form-group">
                    <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-primary" type="button" name="add_device"
                        id="add_device" value="Add Devices" onclick="add_device()">
                </div>
                <!-- <div class="col-sm form-group">
                    <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-primary" type="button" name="bulk_upload"
                        id="bulk_upload" value="Bulk Upload" onclick="bulk_upload()">
                </div> -->
            </div>
        </div>
        <form role="form" method='post'>
            <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />


            <div class="form-row mx-2 mt-4 pt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Facility'); ?>:</label>
                    <select class='form-control' name='facility' id='facility'>
                        <?php
                    foreach($facilities as $facility) {
                        echo "<option value='".$facility['id']."'>".$facility['name']."</option>";
                    }
                    ?>
                    </select>
                </div>
                <div class="col-sm form-group">
                    <label for='form_title'><?php echo xlt('Provider'); ?>:</label>
                    <select class='form-control' name='provider' id='provider'>
                        <?php
                    foreach($providers as $provider) {
                        if($provider['fname']){
                            echo "<option value='".$provider['id']."'>".$provider['lname'].", ".$provider['fname']."</option>";
                        }else{
                            echo "<option value='".$provider['id']."'>".$provider['lname']."</option>";
                        }
                    }
                    ?>
                    </select>
                </div>
            </div>
            <?php
        $id = $_GET['pid'];
        $sql = 'SELECT * FROM `patient_monitoring_form` WHERE pid=' . $id;
        $list = sqlStatement($sql);
        $form_vitals = "SELECT bps,bpd,height,weight,temperature,respiration,oxygen_saturation FROM form_vitals WHERE pid=".$id;
        $form_vitalsres = sqlStatement($form_vitals);
        $form_vitalsrow = sqlFetchArray($form_vitalsres);
        while ($row = sqlFetchArray($list)) {


        ?>
            <div class="form-row mx-2 mt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Weight'); ?>:</label>
                    <input class='form-control' type='hidden' name='pid' id='pid' autocomplete="off"
                        value='<?php echo $id; ?>' />
                    <input class='form-control' type='text' name='weight' id='weight' autocomplete="off"
                        value='<?php echo $form_vitalsrow['weight']; ?>'
                        placeholder='<?php echo xla('Enter Weight'); ?>' />
                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('height'); ?>:</label>

                    <input class='form-control' type='text' name='height' id='height' autocomplete="off"
                        value='<?php echo $form_vitalsrow['height']; ?>'
                        placeholder='<?php echo xla('Enter Height'); ?>' />
                    </select>
                </div>
            </div>
            <div class="form-row mx-2 mt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Blood Pressure Upper'); ?>:</label>

                    <input class='form-control' type='text' name='bp_upper' id='bp_upper' autocomplete="off"
                        value='<?php echo $form_vitalsrow['bps']; ?>'
                        placeholder='<?php echo xla('Enter Blood Pressure'); ?>' />
                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Blood Pressure Lower'); ?>:</label>

                    <input class='form-control' type='text' name='bp_lower' id='bp_lower' autocomplete="off"
                        value='<?php echo $form_vitalsrow['bpd']; ?>'
                        placeholder='<?php echo xla('Enter Blood Pressure Lower'); ?>' />

                </div>
            </div>
            <div class="form-row mx-2 mt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Temprature Upper'); ?>:</label>

                    <input class='form-control' type='text' name='temp_upper' id='temp_upper' autocomplete="off"
                        value='<?php echo $form_vitalsrow['temperature']; ?>'
                        placeholder='<?php echo xla('Enter Temprature Upper'); ?>' />
                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Temprature Lower'); ?>:</label>

                    <input class='form-control' type='text' name='temp_lower' id='temp_lower' autocomplete="off"
                        value='<?php echo $row['temp_lower']; ?>'
                        placeholder='<?php echo xla('Enter Temprature Lower'); ?>' />

                </div>
            </div>
            <div class="form-row mx-2 mt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Blood Sugar Upper'); ?>:</label>

                    <input class='form-control' type='text' name='bs_upper' id='bs_upper' autocomplete="off"
                        value='<?php echo $row['bs_upper']; ?>'
                        placeholder='<?php echo xla('Enter Blood Sugar Upper'); ?>' />
                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Blood Sugar Lower'); ?>:</label>

                    <input class='form-control' type='text' name='bs_lower' id='bs_lower' autocomplete="off"
                        value='<?php echo $row['bs_lower']; ?>'
                        placeholder='<?php echo xla('Enter Blood Sugar Lower'); ?>' />

                </div>
            </div>
            <div class="form-row mx-2 mt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Respiratory Upper'); ?>:</label>

                    <input class='form-control' type='text' name='resp_upper' id='resp_upper' autocomplete="off"
                        value='<?php echo $form_vitalsrow['respiration']; ?>'
                        placeholder='<?php echo xla('Enter Respiratory Upper'); ?>' />
                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Respiratory Lower'); ?>:</label>

                    <input class='form-control' type='text' name='resp_lower' id='resp_lower' autocomplete="off"
                        value='<?php echo $row['resp_lower'] ?>'
                        placeholder='<?php echo xla('Enter Respiratory Lower'); ?>' />

                </div>
            </div>
            <div class="form-row mx-2 mt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Oxygen Upper'); ?>:</label>

                    <input class='form-control' type='text' name='oxy_upper' id='oxy_upper' autocomplete="off"
                        value='<?php echo $form_vitalsrow['oxygen_saturation'] ?>'
                        placeholder='<?php echo xla('Enter Oxygen Upper'); ?>' />
                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Oxygen Lower'); ?>:</label>

                    <input class='form-control' type='text' name='oxy_lower' id='oxy_lower' autocomplete="off"
                        value='<?php echo $row['oxy_lower'] ?>'
                        placeholder='<?php echo xla('Enter Oxygen Lower'); ?>' />

                </div>
            </div>
            <div class="form-row mx-2 mt-4">
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Pain Upper'); ?>:</label>
                    <input class='form-control' type='text' name='pain_upper' id='pain_upper' autocomplete="off"
                        value='<?php echo $row['pain_upper']; ?>'
                        placeholder='<?php echo xla('Enter Pain Upper'); ?>' />
                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Pain Lower'); ?>:</label>
                    <input class='form-control' type='text' name='pain_lower' id='pain_lower' autocomplete="off"
                        value='<?php echo $row['pain_lower']; ?>'
                        placeholder='<?php echo xla('Enter Pain Lower'); ?>' />

                </div>
                <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt(' Select Alert Message'); ?>:</label>
                    <select class='form-control' name='alert' id='alert'>
                        <?php $active=$row['alert'];?>
                        <option value="Monitored" <?php if($active =="Monitored"){echo 'selected';}?>>Monitored</option>
                        <option value="Need Attention" <?php if($active =="Need Attention"){echo 'selected';}?>>Need
                            Attention</option>

                    </select>

                </div>
            </div>



            <div class="form-row mx-2 mt-3">
                <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-primary" type="submit" name="form_save"
                    id="form_save" value="Update">
                <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-primary" type="button" name="list_device"
                    id="list_device" value="List All Devices" onclick="listdevice()">
                <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-secondary" type="button" id="cancel"
                    onclick="goBack()" value="Return to Patient">
            </div>

        </form>
        </div>
    </div>

</body>


<script>
function add_device() {
    var pid = $('#pid').val();
    var url = 'add_devices.php?pid=' + pid;
    dlgopen(url, '_blank', 620, 460, '', 'Add Device', {
        onClosed: 'reload'
    });

}

function bulk_upload() {
    var pid = $('#pid').val();
    var url = 'bulk_upload.php?pid=' + pid;
    dlgopen(url, '_blank', 620, 460, '', 'Bulk Upload Devices', {
        onClosed: 'reload'
    });

}
$(function() {
    $('form').on('submit', function(e) {

        e.preventDefault();

        $.ajax({
            type: 'post',
            url: 'edit_patient_save.php',
            data: $('form').serialize(),
            error: function(xhr, status, error) {
                alert(error);
            },
            success: function(results) {
                alert(results);
                window.location.href =
                    '<?php echo $GLOBALS['webroot'];?>/interface/modules/custom_modules/oe-comlink-module/comlinkUI.php';
            }
        });

    });

});

function goBack() {
    window.history.back();
}

function listdevice() {
    var pid = $('#pid').val();
    window.location.href =
        '<?php echo $GLOBALS['webroot'];?>/interface/modules/custom_modules/oe-comlink-module/form/list_device.php?pid=' +
        pid;
}
</script>

</html>
<?php } ?>
