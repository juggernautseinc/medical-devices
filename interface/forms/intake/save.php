<?php

/* this page is intended to be the 'action=' target of a form object.
 * it is called to save the contents of the form into the database
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
require_once($GLOBALS['srcdir'].'/api.inc');
/* for ??? */
require_once($GLOBALS['srcdir'].'/forms.inc');

use OpenEMR\Common\Acl\AclMain;

/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_intake';

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Intake Form';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'intake';

require_once('array.php');

/**
* an array of all of the fields' names and their types.
* an array of the lists the fields may draw on.
*
* get each field from $_POST[], storing them into $field_names associated with their names.
*
*foreach($field_names as $key=>$val)
 * */
$field_names = [];
foreach($manual_layouts as $key=>$val)
{
    $pos = '';
    $neg = '';
    $field_names[$key] = $val['field_id'];
    $datatype = $val['data_type'];

    /*if ($val == 'textbox' || $val == 'textarea' || $val == 'provider' || $val == 'textfield')*/
    if ($datatype == 2 || $datatype == 3 || $datatype == 10)
    {
	if (isset($_POST['form_'.$key]))
	{
            $field_names[$key]=$_POST['form_'.$key];
	}
	else
	{
            $field_names[$key]="";
	}
    }
    /*if ($val == 'date')*/
    if ($datatype == 4)
    {
	if (isset($_POST[$key]))
	{
            $field_names[$key]=$_POST[$key];
	}
	else
	{
            $field_names[$key]="";
	}
    }
    /*if (($val == 'checkbox_list' ))*/
    if ($datatype == 21)
    {
        $field_names[$key]='';
        if (isset($_POST['form_'.$key]) && $_POST['form_'.$key] != 'none' ) /* if the form submitted some entries selected in that field */
        {
	    /*$lres=sqlStatement("select * from list_options where list_id = '".$lists[$key]."' ORDER BY seq, title");*/
            $list_id = $val['list_id'];
	    $lres=sqlStatement("select * from list_options where list_id = '".$list_id."' ORDER BY seq, title");
            while ($lrow = sqlFetchArray($lres))
            {
                if (isset($_POST['form_'.$key]) and is_array($_POST['form_'.$key]))
                    {
                        if (isset($_POST['form_'.$key][$lrow['option_id']]))
                        {
                            if ($field_names[$key] != '')
                              $field_names[$key]=$field_names[$key].'|';
	                    $field_names[$key] = $field_names[$key].$lrow['option_id'];
                        }
                    }
            }
        }
    }
    /*if (($val == 'checkbox_combo_list')) */
    if ($datatype == 25)
    {
        $field_names[$key]='';
        if (isset($_POST['check_'.$key]) && $_POST['check_'.$key] != 'none' ) /* if the form submitted some entries selected in that field */
        {
	    /*$lres=sqlStatement("select * from list_options where list_id = '".$lists[$key]."' ORDER BY seq, title");*/
            $list_id = $val['list_id'];
	    $lres=sqlStatement("select * from list_options where list_id = '".$list_id."' ORDER BY seq, title");
            while ($lrow = sqlFetchArray($lres))
            {
                if (is_array($_POST['check_'.$key]))
                {
                    if (isset($_POST['check_'.$key][$lrow['option_id']]))
                    {
                        if ($field_names[$key] != '')
                          $field_names[$key]=$field_names[$key].'|';
                        $field_names[$key] = $field_names[$key].$lrow['option_id'].":".$_POST['form_'.$key][$lrow['option_id']];
                    }
		    else
		    {
                        if ($field_names[$key] != '')
                          $field_names[$key]=$field_names[$key].'|';
                        $field_names[$key] = $field_names[$key].$lrow['option_id'].":";
		    }
                }
            }
        }
    }
    /*if (($val == 'dropdown_list'))*/
    if ($datatype == 1)
    {
        $field_names[$key]='';
        if (isset($_POST['form_'.$key]) && $_POST['form_'.$key] != 'none' ) /* if the form submitted some entries selected in that field */
        {
	    /*$lres=sqlStatement("select * from list_options where list_id = '".$lists[$key]."' ORDER BY seq, title");*/
            $list_id = $val['list_id'];
	    $lres=sqlStatement("select * from list_options where list_id = '".$list_id."' ORDER BY seq, title");
            while ($lrow = sqlFetchArray($lres))
            {
                if ($_POST['form_'.$key] == $lrow['option_id'])
                {
                    $field_names[$key]=$lrow['option_id'];
                    break;
                }
            }
        }
    }
}

/* at this point, field_names[] contains an array of name->value pairs of the fields we expected from the form. */

/* escape form data for entry to the database. */
foreach ($field_names as $k => $var) {
  $field_names[$k] = add_escape_custom($var);
}

if ($encounter == '') $encounter = date('Ymd');

if ($_GET['mode'] == 'new') {
    /* NOTE - for customization you can replace $_POST with your own array
     * of key=>value pairs where 'key' is the table field name and
     * 'value' is whatever it should be set to
     * ex)   $newrecord['parent_sig'] = $_POST['sig'];
     *       $newid = formSubmit($table_name, $newrecord, $_GET['id'], $userauthorized);
     */

    /* make sure we're at the beginning of the array */
    reset($field_names);

    /* save the data into the form's encounter-based table */
    $newid = formSubmit($table_name, $field_names, $_GET['id'], $userauthorized);
    /* link this form into the encounter. */
    addForm($encounter, $form_name, $newid, $form_folder, $pid, $userauthorized);
}

elseif ($_GET['mode'] == 'update') {
    /* make sure we're at the beginning of the array */
    reset($field_names);

    /* update the data in the form's table */
    $success = formUpdate($table_name, $field_names, $_GET['id'], $userauthorized);
    /* sqlStatement('update '.$table_name." set pid = {".$_SESSION['pid']."},groupname='".$_SESSION['authProvider']."',user='".$_SESSION['authUser']."',authorized=$userauthorized,activity=1,date = NOW(), where id=$id"); */
}


formHeader('Redirecting....');
/* defaults to the encounters page. */
formJump();

formFooter();
?>

