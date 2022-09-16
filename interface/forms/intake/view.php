<?php
/*
 * The page shown when the user requests to see this form. Allows the user to edit form contents, and save. has a button for printing the saved form contents.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
require_once($GLOBALS['srcdir'].'/api.inc');
/* for generate_form_field, ?? */
require_once($GLOBALS['srcdir'].'/options.inc.php');
/* note that we cannot include options_listadd.inc here, as it generates code before the <html> tag */

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Core\Header;

/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_intake';

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Intake Form';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'intake';

/* Use the formFetch function from api.inc to load the saved record */
$xyzzy = formFetch($table_name, $_GET['id']);

require_once('array.php');

$submiturl = $GLOBALS['rootdir'].'/forms/'.$form_folder.'/save.php?mode=update&amp;return=encounter&amp;id='.$_GET['id'];
if (isset($_GET['mode'])) {
 if ($_GET['mode']=='noencounter') {
 $submiturl = $GLOBALS['rootdir'].'/forms/'.$form_folder.'/save.php?mode=new&amp;return=show&amp;id='.$_GET['id'];
 $returnurl = 'show.php';
 }
}
else
{
 $returnurl = $GLOBALS['form_exit_url'];
}


/* remove the time-of-day from all date fields */
if ($xyzzy['legal_court_dates'] != '') {
    $dateparts = explode(' ', $xyzzy['legal_court_dates']);
    $xyzzy['legal_court_dates'] = $dateparts[0];
}
if ($xyzzy['mh_inpatient_hospitalizations_dates'] != '') {
    $dateparts = explode(' ', $xyzzy['mh_inpatient_hospitalizations_dates']);
    $xyzzy['mh_inpatient_hospitalizations_dates'] = $dateparts[0];
}
if ($xyzzy['mh_er_crisis_involvement_dates'] != '') {
    $dateparts = explode(' ', $xyzzy['mh_er_crisis_involvement_dates']);
    $xyzzy['mh_er_crisis_involvement_dates'] = $dateparts[0];
}
if ($xyzzy['mh_outpatient_therapy_dates'] != '') {
    $dateparts = explode(' ', $xyzzy['mh_outpatient_therapy_dates']);
    $xyzzy['mh_outpatient_therapy_dates'] = $dateparts[0];
}
if ($xyzzy['med_hist_date'] != '') {
    $dateparts = explode(' ', $xyzzy['med_hist_date']);
    $xyzzy['med_hist_date'] = $dateparts[0];
}
if ($xyzzy['medication_date_started_1'] != '') {
    $dateparts = explode(' ', $xyzzy['medication_date_started_1']);
    $xyzzy['medication_date_started_1'] = $dateparts[0];
}
if ($xyzzy['medication_date_started_2'] != '') {
    $dateparts = explode(' ', $xyzzy['medication_date_started_2']);
    $xyzzy['medication_date_started_2'] = $dateparts[0];
}
if ($xyzzy['medication_date_started_3'] != '') {
    $dateparts = explode(' ', $xyzzy['medication_date_started_3']);
    $xyzzy['medication_date_started_3'] = $dateparts[0];
}
if ($xyzzy['medication_date_started_4'] != '') {
    $dateparts = explode(' ', $xyzzy['medication_date_started_4']);
    $xyzzy['medication_date_started_4'] = $dateparts[0];
}
if ($xyzzy['medication_date_started_5'] != '') {
    $dateparts = explode(' ', $xyzzy['medication_date_started_5']);
    $xyzzy['medication_date_started_5'] = $dateparts[0];
}
if ($xyzzy['medication_date_started_6'] != '') {
    $dateparts = explode(' ', $xyzzy['medication_date_started_6']);
    $xyzzy['medication_date_started_6'] = $dateparts[0];
}

/* define check field functions. used for translating from fields to html viewable strings */

function chkdata_Date(&$record, $var) {
        return htmlspecialchars($record["$var"],ENT_QUOTES);
}

function chkdata_Txt(&$record, $var) {
        return htmlspecialchars($record["$var"],ENT_QUOTES);
}

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

<!-- FIXME: this needs to detect access method, and construct a URL appropriately! -->
function PrintForm() {
    newwin = window.open("<?php echo $rootdir.'/forms/'.$form_folder.'/print.php?id='.$_GET['id']; ?>","print_<?php echo $form_name; ?>");
}

</script>
<title><?php echo htmlspecialchars('View '.$form_name); ?></title>

</head>
<body class="body_top">

<div id="title">
<a href="<?php echo $returnurl; ?>" onclick="top.restoreSession()">
<span class="title"><?php htmlspecialchars(xl($form_name,'e')); ?></span>
<span class="back">(<?php xl('Back','e'); ?>)</span>
</a>
</div>

<form method="post" action="<?php echo $submiturl; ?>" id="<?php echo $form_folder; ?>">

<!-- Save/Cancel buttons -->
<div id="top_buttons" class="top_buttons">
<fieldset class="top_buttons">
<input type="button" class="save" value="<?php xl('Save Changes','e'); ?>" />
<input type="button" class="dontsave" value="<?php xl('Don\'t Save Changes','e'); ?>" />
<input type="button" class="print" value="<?php xl('Print','e'); ?>" />
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
<input type="button" class="save" value="<?php xl('Save Changes','e'); ?>" />
<input type="button" class="dontsave" value="<?php xl('Don\'t Save Changes','e'); ?>" />
<input type="button" class="print" value="<?php xl('Print','e'); ?>" />
</fieldset>
</div><!-- end bottom_buttons -->
</form>
<script>
// jQuery stuff to make the page a little easier to use

$(function () {
    $(".save").click(function() { top.restoreSession(); document.forms["<?php echo $form_folder; ?>"].submit(); });

<?php if ($returnurl == 'show.php') { ?>
    $(".dontsave").click(function() { location.href='<?php echo $returnurl; ?>'; });
<?php } else { ?>
    $(".dontsave").click(function() { parent.closeTab(window.name, false); });
<?php } ?>

    $(".print").click(function() { PrintForm(); });
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
</script>
</body>
</html>

