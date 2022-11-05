<?php

/*
 *  package   Comlink OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

require_once dirname(__FILE__, 5) . "/globals.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Comlink\OpenEMR\Module\Container;
use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;

$container = new Container();
$loadDb = $container->getDatabase();

$facilities = $loadDb->getFacilities();
$providers = $loadDb->getProviders();
$patients = $loadDb->getpatientdata();

if ($_POST) {
    if ($_POST['pro'] == "autocomplete") {
        $search_list = [];
        $type = $_POST['type'];
        if ($type == 'name') {
            $sql='SELECT `lname`,`fname` FROM `patient_data`';
            $list = sqlStatement($sql);
            while ($row = sqlFetchArray($list)) {
                $search_list[] = $row['lname'] . ", " . $row['fname'];
            }
        } else {
            $sql='SELECT `pid` FROM `patient_data`';
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
    <?php Header::setupHeader(['report-helper','opener']); ?>
    <meta charset="utf-8" />
    <title><?php echo xlt('Add Patients'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    function sel_patient() {
        let title = '<?php echo xlt('Patient Search'); ?>';
        dlgopen( '<? echo dirname(__FILE__,5) ?>\main\calendar\find_patient_popup.php', '_blank', 650, 300, '', title);
    }

    const $disabledResults = $(".js-example-disabled-results");
    $disabledResults.select2();
    </script>
    <style>
        .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        width:37.5%;
        margin:5.8% 0% 0% 37%;
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
<body >
<form role="form" method='post' name='theform' id='theform' action='add_patient_save.php'>
     <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
    <div class="form-row mx-2">
        <div class="col-sm form-group">
            <label for='form_facility'><?php echo xlt('Facility'); ?>:</label>
            <select class='form-control' name='facility' id='facility'>
                <?php
                foreach($facilities as $facility) {
                    echo "<option value='".$facility['id']."'>".$facility['name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-sm form-group">
            <label for='form_title'><?php echo xlt('Provider'); ?>:</label>
            <select class='form-control' name='provider' id='provider'>
                <?php
                foreach ($providers as $provider) {
                    if($provider['fname']){
                        echo "<option value='" . $provider['id'] . "'>" . $provider['lname'] . ", " . $provider['fname']."</option>";
                    }else{
                        echo "<option value='" . $provider['id'] . "'>" . $provider['lname'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-sm form-group">
            <label for='form_title'><?php echo xlt('Select Patient'); ?>:</label>

            <select class='form-control js-example-disabled-results' name='pid' id='pid' required>
                <option value="" hidden>Select patient</option>
                <?php
                foreach ($patients as $patient) {
                     echo "<option value='" . $patient['pid'] . "'>" . $patient['lname'] . "</option>";

                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-row mx-2">
        <div class="col-sm form-group">
            <label for='form_facility'><?php echo xlt('Weight'); ?>:</label>

            <input class='form-control' type='text' name='weight' id='weight' autocomplete="off"
                    placeholder='<?php echo xla('Enter Your Weight'); ?>' value="0"/>
        </div>
        <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('height'); ?>:</label>

                <input class='form-control' type='text' name='height' id='height' autocomplete="off"
                        placeholder='<?php echo xla('Enter Your Height'); ?>' value="0"/>
            </div>
        </div>
        <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('Blood Pressure Upper Limit'); ?>:</label>

                <input class='form-control' type='text' name='bp_upper' id='bp_upper' autocomplete="off" placeholder='<?php echo xla('Enter Blood Pressure'); ?>'/>
            </div>
        </div>
    </div>

    <div class="form-row mx-2">
        <div class="col-sm form-group">
            <label for='form_facility'><?php echo xlt('Blood Pressure Lower Limit'); ?>:</label>

            <input class='form-control' type='text' name='bp_lower' id='bp_lower' autocomplete="off"

                placeholder='<?php echo xla('Enter Blood Pressure Lower'); ?>' value="0"/>
        </div>
         <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('Temprature Upper'); ?>:</label>

                <input class='form-control' type='text' name='temp_upper' id='temp_upper' autocomplete="off"

                    placeholder='<?php echo xla('Enter Temprature Upper'); ?>' value="0"/>
            </div>
        </div>
        <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('Temprature Lower'); ?>:</label>

                <input class='form-control' type='text' name='temp_lower' id='temp_lower' autocomplete="off"

                    placeholder='<?php echo xla('Enter Temprature Lower'); ?>' />
            </div>
        </div>
    </div>

    <div class="form-row mx-2">
        <div class="col-sm form-group">
            <label for='form_facility'><?php echo xlt('Blood Sugar Upper'); ?>:</label>

            <input class='form-control' type='text' name='bs_upper' id='bs_upper' autocomplete="off"

                placeholder='<?php echo xla('Enter Blood Sugar Upper'); ?>' />
        </div>
        <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('Blood Sugar Lower'); ?>:</label>

                <input class='form-control' type='text' name='bs_lower' id='bs_lower' autocomplete="off"

                    placeholder='<?php echo xla('Enter Blood Sugar Lower'); ?>' />
            </div>
        </div>
        <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('Respiratory Upper'); ?>:</label>

                <input class='form-control' type='text' name='resp_upper' id='resp_upper' autocomplete="off"

                    placeholder='<?php echo xla('Enter Respiratory Upper'); ?>' />
            </div>
        </div>
    </div>
    <div class="form-row mx-2">
        <div class="col-sm form-group">
            <label for='form_facility'><?php echo xlt('Respiratory Lower'); ?>:</label>

            <input class='form-control' type='text' name='resp_lower' id='resp_lower' autocomplete="off"

                placeholder='<?php echo xla('Enter Respiratory Lower'); ?>' />
        </div>
         <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('Oxygen Upper'); ?>:</label>

                <input class='form-control' type='text' name='oxy_upper' id='oxy_upper' autocomplete="off"

                    placeholder='<?php echo xla('Enter Oxygen Upper'); ?>' />
            </div>
        </div>
        <div class="col-sm form-group">
            <div class="col-sm form-group">
                <label for='form_facility'><?php echo xlt('Oxygen Lower'); ?>:</label>

                <input class='form-control' type='text' name='oxy_lower' id='oxy_lower' autocomplete="off"

                    placeholder='<?php echo xla('Enter Oxygen Lower'); ?>' />
            </div>
        </div>

    </div>
    <div class="form-row mx-2">
        <div class="col-sm form-group">
            <label for='form_facility'><?php echo xlt('Pain Upper'); ?>:</label>

            <input class='form-control' type='text' name='pain_upper' id='pain_upper' autocomplete="off"
                        placeholder='<?php echo xla('Enter Pain Upper'); ?>' value="0"/>
        </div>
         <div class="col-sm form-group">
            <div class="col-sm form-group">
               <label for='form_facility'><?php echo xlt('Pain Lower'); ?>:</label>

                <input class='form-control' type='text' name='pain_lower' id='pain_lower' autocomplete="off"
                     placeholder='<?php echo xla('Enter Pain Lower'); ?>' value="0"/>
            </div>
        </div>
        <div class="col-sm form-group">
            <div class="col-sm form-group">
                    <label for='form_facility'><?php echo xlt('Select Alert'); ?>:</label>
                    <select class='form-control' name='alert' id='alert'>
                        <option value="Monitored">Monitored</option>
                        <option value="Need Attention">Need Attention</option>
                    </select>
            </div>
        </div>

    </div>

    <div class="form-row mx-2 mt-3">
        <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-primary" type="submit" name="form_save" id="form_save" value="Add Patient">
        <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-secondary" type="button" id="cancel" onclick="dlgclose()" value="Cancel">
    </div>
</form>
</body>
<script>

//var $disabledResults = $("#pid");


$('.js-example-disabled-results').on('change', function (e) {
    const optionSelected = $("option:selected", this);
    const valueSelected = this.value;
    const height = document.getElementById('height');
    const weight = document.getElementById('weight');
    const temp_upper = document.getElementById('temp_upper');
    const bp_upper = document.getElementById('bp_upper');
    const bp_lower = document.getElementById('bp_lower');
    const oxy_upper = document.getElementById('oxy_upper');
    if(valueSelected){
        $.ajax({
            type: 'post',
            url: '../get_values_pop.php',
            data: {dataID:valueSelected},
            dataType: "json",
            error: function(xhr, status, error) {
                alert('error'+error);
            },
            success: function(results) {
                height.value=results.height?results.height:0;
                weight.value=results.weight?results.weight:0;
                temp_upper.value=results.temperature?results.temperature:0;
                bp_upper.value=results.bps?results.bps:0;
                bp_lower.value=results.bpd?results.bpd:0;
                oxy_upper.value=results.oxygen_saturation?results.oxygen_saturation:0;
            }
        });
    }


});

$(function() {
    $('form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: 'add_patient_save.php',
            data: $('form').serialize(),
            error: function(xhr, status, error) {
                alert(error);
            },
            success: function(results) {
                alert(results);
                location.reload();
            }
        });
    });
});

function autocomplete(inp, arr) {
    let currentFocus;

    inp.addEventListener("input", function(e) {
        let a, b, i, val = this.value;
        closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      this.parentNode.appendChild(a);

      for (i = 0; i < arr.length; i++) {

        if (arr[i].substr(0, val.length).toUpperCase() === val.toUpperCase()) {

          b = document.createElement("DIV");
          b.innerHTML = "<strong>" + arr[i].substring(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substring(val.length);
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
      let x = document.getElementById(this.id + "autocomplete-list");
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
    for (let i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
      const x = document.getElementsByClassName("autocomplete-items");
      for (let i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}


function onChange(){
    let search_list = [];
    const search = document.getElementById("form_search");
    const pro = "autocomplete";

    $.ajax({
            type: 'POST',
            url: "add_patient.php",
            dataType:'text',
            data:{pro:pro ,type:search.value},
            async: false,
            success:function(response){
                console.log(response);
                const result = JSON.parse(response);
                search_list = result;
                console.log(search_list);
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(xhr + " " + ajaxOptions + " " + thrownError);
            }
        });
        autocomplete(document.getElementById("search_name"), search_list);
}
window.onload = onChange();
function addName() {
    const search = document.getElementById("form_search").value;
    const sel_search = document.getElementById("search_name").value;
    const data = "pro=add&type=" + search;
    $.ajax({
            type: 'POST',
            url: "add_patient.php",
            dataType:'text',
            data:data,
            async: false,
            success:function(response){
                const result = JSON.parse(response);
                const search_list = result;
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(xhr + " " + ajaxOptions + " " + thrownError);
            }
        });
}


</script>
</html>
<?php } ?>
