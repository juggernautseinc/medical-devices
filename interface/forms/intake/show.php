<?php
/*
 * The page shown when the user requests to see this form in a "report view". does not allow editing contents, or saving. has 'print' and 'delete' buttons.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
require_once($GLOBALS['srcdir'].'/api.inc');
/* for display_layout_rows(), ?? */
require_once($GLOBALS['srcdir'].'/options.inc.php');

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


/* since we have no-where to return, abuse returnurl to link to the 'edit' page */
/* FIXME: pass the ID, create blank rows if necissary. */
$returnurl = "../../forms/$form_folder/view.php?mode=noencounter";

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
        return htmlspecialchars($record{"$var"},ENT_QUOTES);
}

function chkdata_Txt(&$record, $var) {
        return htmlspecialchars($record{"$var"},ENT_QUOTES);
}

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>

<!-- declare this document as being encoded in UTF-8 -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" ></meta>

<!-- assets -->
<?php Header::setupHeader(); ?>
<!-- Form Specific Stylesheet. -->
<link rel="stylesheet" href="../../forms/<?php echo $form_folder; ?>/style.css">

<script>

<!-- FIXME: this needs to detect access method, and construct a URL appropriately! -->
function PrintForm() {
    newwin = window.open("<?php echo $rootdir.'/forms/'.$form_folder.'/print.php?id='.$_GET['id']; ?>","print_<?php echo $form_name; ?>");
}

</script>
<title><?php echo htmlspecialchars('Show '.$form_name); ?></title>

</head>
<body class="body_top">

<div id="title">
<span class="title"><?php xl($form_name,'e'); ?></span>
<?php
 if ($thisauth_write_addonly)
  { ?>
<a href="<?php echo $returnurl; ?>" onclick="top.restoreSession()">
<span class="back"><?php xl($tmore,'e'); ?></span>
</a>
<?php }; ?>
</div>

<form method="post" id="<?php echo $form_folder; ?>" action="">

<!-- container for the main body of the form -->
<div id="form_container">

<div id="show">

<?php require('layout.php') ?>


</div><!-- end show -->

</div><!-- end form_container -->

<!-- Print button -->
<div id="button_bar" class="button_bar">
<fieldset class="button_bar">
<input type="button" class="print" value="<?php xl('Print','e'); ?>" />
</fieldset>
</div><!-- end button_bar -->

</form>
<script>
// jQuery stuff to make the page a little easier to use

$(function () {
    $(".print").click(function() { PrintForm(); });
});
</script>
</body>
</html>

