<?php
/*
 * The page shown when the user requests a new form. allows the user to enter form contents, and save.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
require_once($GLOBALS['srcdir'].'/api.inc');
/* for generate_form_field, ?? */
require_once($GLOBALS['srcdir'].'/options.inc.php');
/* note that we cannot include options_listadd.inc here, as it generates code before the <html> tag */

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Intake Form';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'intake';

require_once('array.php');

$submiturl = $GLOBALS['rootdir'].'/forms/'.$form_folder.'/save.php?mode=new&amp;return=encounter';
/* no get logic here */

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>

<!-- declare this document as being encoded in UTF-8 -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" ></meta>

<!-- assets -->
<?php Header::setupHeader('datetime-picker'); ?>
<!-- Form Specific Stylesheet. -->
<link rel="stylesheet" href="../../forms/<?php echo $form_folder; ?>/style.css">

<script>
// this line is to assist the calendar text boxes
var mypcc = '<?php echo $GLOBALS['phone_country_code']; ?>';

<!-- a validator for all the fields expected in this form -->
function validate() {
  return true;
}

<!-- a callback for validating field contents. executed at submission time. -->
function submitme() {
 var f = document.forms[0];
 if (validate(f)) {
  top.restoreSession();
  f.submit();
 }
}

</script>



<title><?php echo htmlspecialchars('New '.$form_name); ?></title>

</head>
<body class="body_top">

<div id="title">
<a href="<?php echo $GLOBALS['form_exit_url']; ?>" onclick="top.restoreSession()">
<span class="title"><?php xl($form_name,'e'); ?></span>
<span class="back">(<?php xl('Back','e'); ?>)</span>
</a>
</div>

<form method="post" action="<?php echo $submiturl; ?>" id="<?php echo $form_folder; ?>">

<!-- Save/Cancel buttons -->
    <div  class="container-lg">
        <div class="m-auto">
            <div id="top_buttons" class="top_buttons">
                <fieldset class="top_buttons">
                    <input type="button" class="save" value="<?php xl('Save','e'); ?>" />
                    <input type="button" class="dontsave" value="<?php xl('Don\'t Save','e'); ?>" />
                </fieldset>
            </div><!-- end top_buttons -->

            <!-- container for the main body of the form -->
            <div id="form_container">
                <fieldset>

                    <?php require('layout.php') ?>

                </fieldset>
            </div> <!-- end form_container -->

            <!-- Save/Cancel buttons -->
            <div id="bottom_buttons" class="button_bar">
                <fieldset>
                    <input type="button" class="save" value="<?php xl('Save','e'); ?>" />
                    <input type="button" class="dontsave" value="<?php xl('Don\'t Save','e'); ?>" />
                </fieldset>
            </div><!-- end bottom_buttons -->
        </div>
    </div>
</form>
<script>
// jQuery stuff to make the page a little easier to use

$(function () {
    $(".save").click(function() { top.restoreSession(); document.forms["<?php echo $form_folder; ?>"].submit(); });
    $(".dontsave").click(function() { location.href='parent.closeTab(window.name, false)'; });

	$(".sectionlabel input").click( function() {
    	var section = $(this).attr("data-section");
		if ( $(this).prop('checked' ) ) {
			$("#"+section).show();
		} else {
			$("#"+section).hide();
		}
    });

    $(".sectionlabel input").prop( 'checked', true );
    $(".section").show();

    $('.datepicker').datetimepicker({
        <?php $datetimepicker_timepicker = false; ?>
        <?php $datetimepicker_showseconds = false; ?>
        <?php $datetimepicker_formatInput = false; ?>
        <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
        <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
    });
    $('.datetimepicker').datetimepicker({
        <?php $datetimepicker_timepicker = true; ?>
        <?php $datetimepicker_showseconds = false; ?>
        <?php $datetimepicker_formatInput = false; ?>
        <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
        <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
    });

});

    function dopclick(id, category, field) {
        top.restoreSession();
        if (category == 0) category = '';
        dlgopen('../summary/add_edit_issue.php?issue=' + encodeURIComponent(id) + '&thistype=' + encodeURIComponent(category)+ '&thisfield=' + field , '_blank', 650, 500, '', "Add\/Edit Issue");

    }

    function importDiagnosis(field) {
        document.getElementById(field).value = $('#diagnosisform').serializeArray();
    }

    function setDiagnosis(id, category, field) {
        let getD = new Promise(function(resolve) {
            dopclick(id, category, field);
            resolve(field);
        });
        getD.then(function(value) {
            let f = value;
            importDiagnosis(f);
        });
    }

</script>
</body>
</html>

