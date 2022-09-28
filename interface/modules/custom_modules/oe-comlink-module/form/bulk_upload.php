<?php

require_once "../../../../globals.php";
require_once dirname(__FILE__, 2)."/controller/Container.php";

use OpenEMR\Modules\Comlink\Container;
use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;

$container = new Container();
$loadDb = $container->getDatabase();

$facilities = $loadDb->getFacilities();
$providers = $loadDb->getProviders();
$patients = $loadDb->getpatientdata();


if($_POST){
    if($_POST['pro'] == "autocomplete"){
        $search_list = [];
        $type = $_POST['type'];
        if($type == 'name'){
            $sql='SELECT `lname`,`fname` FROM `patient_data`';
            $list = sqlStatement($sql);
            while ($row = sqlFetchArray($list)) {
                $search_list[] = $row['lname'].", ".$row['fname'];
            }
        }else{
            $sql='SELECT `subehremrid` FROM `patient_data`';
            $list = sqlStatement($sql);
            while ($row = sqlFetchArray($list)) {
                $search_list[] = $row['subehremrid'];
            }
        }

        echo(json_encode($search_list));
    }




}else{
?>
<!DOCTYPE html>

<head>
    <?php Header::setupHeader(['report-helper','opener']); ?>
    <meta charset="utf-8" />
    <title><?php echo xlt('Add Patients');?></title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    function sel_patient() {
        let title = '<?php echo xlt('Patient Search'); ?>';
        dlgopen('<? echo dirname(__FILE__,5) ?>\main\calendar\find_patient_popup.php', '_blank', 650, 300, '', title);
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

    .loading {
        font-size: 0;
        width: 30px;
        height: 30px;
        margin-top: 5px;
        border-radius: 15px;
        padding: 0;
        border: 3px solid #FFFFFF;
        border-bottom: 3px solid rgba(255, 255, 255, 0.0);
        border-left: 3px solid rgba(255, 255, 255, 0.0);
        background-color: transparent !important;
        animation-name: rotateAnimation;
        -webkit-animation-name: wk-rotateAnimation;
        animation-duration: 1s;
        -webkit-animation-duration: 1s;
        animation-delay: 0.2s;
        -webkit-animation-delay: 0.2s;
        animation-iteration-count: infinite;
        -webkit-animation-iteration-count: infinite;
    }
    </style>
</head>

<body>
    <form role="form" method="post" enctype="multipart/form-data" id="myform">
        <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />




        <div class="form-row mx-8">
            <div class="col-sm-11 ml-4">
                <label for="formFile" class="form-label">Choose Your Csv File</label>
                <input class="form-control" type="file" name='file' id='file' style="height: 43px;" accept=".csv"
                    required>

            </div>

        </div>
        <div class="form-row mx-2 mt-3">
            <button class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-primary" type="button" name="form_save" id="form_save"
                value="Upload"><i class="loader"></i>Upload</button>
            <input class="col-sm mx-sm-2 my-2 my-sm-auto btn btn-secondary" type="button" id="cancel"
                onclick="dlgclose()" value="Cancel">
        </div>
    </form>
</body>
<script>
$('#form_save').on('click', function(event) {

    var fd = new FormData();
    var files = $('#file')[0].files;

    if (files.length > 0) {
        $('.loader').addClass(`fa fa-spinner fa-spin`);
        // var pid = $('#pid').val();
        fd.append('file', files[0]);
        // fd.append('pid', pid);
        // alert(fd);
        $.ajax({
            type: 'post',
            url: 'bulk_upload_save.php',
            data: fd,
            contentType: false,
            processData: false,
            error: function(xhr, status, error) {
                $('.loader').removeClass(`fa fa-spinner fa-spin`);
                alert(error);
            },
            success: function(results) {
                alert(results);
                location.reload();
                dlgclose();
            }
        });
    } else {
        alert('Please Choose Csv File !...');
    }

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
