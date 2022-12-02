<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

require_once dirname(__FILE__, 6) . "/globals.php";

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo xlt("Create New User") ?></title>
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Font-->
	<link rel="stylesheet" type="text/css" href="css/montserrat-font.css">
	<link rel="stylesheet" type="text/css" href="fonts/material-design-iconic-font/css/material-design-iconic-font.min.css">
	<!-- Main Style Css -->
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body class="form-v10">
	<div class="page-content">
		<div class="form-v10-content">
			<form name="account" class="form-detail" action="create.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <input type="hidden" name="emailnotifysendoption" value="None">
                <input type="hidden" name="emailnotifyreceiveoption" value="None">
                <input type="hidden" name="allowemailtofax" value="true">
                <input type="hidden" name="emailNotifySendIncAttachment" value="true">
                <input type="hidden" name="emailNotifyReceiveIncAttachment" value="true">
                <input type="hidden" name="usersTokenLife" value="31622400">
                <input type="hidden" name="timezone" value="<?php echo $localtz['gl_value']; ?>">

				<div class="form-left">
					<h2><?php echo xlt("Create Fax Account") ?></h2>
					<div class="form-group">
						<div class="form-row">
							<input type="text" name="accountname" id="accountname" class="input-text" placeholder="<?php echo xlt("Practice Name") ?>" required>
						</div>
                        <div class="form-row">
                            <input type="text" name="faxcallerid" id="faxcallerid" class="input-text" placeholder="<?php echo xlt("Phone number") ?>" required>
                        </div>
					</div>
				</div>
				<div class="form-right">
				    <div class="form-row">
					<img src="https://www.afax.com/wp-content/uploads/2015/11/Hardware-Fax-icon.png">
					</div>

					<div class="form-row-last">
						<input type="submit"  class="register" value="<?php echo xlt("Create Account") ?>">
					</div>
				</div>
			</form>
		</div>
	</div>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
