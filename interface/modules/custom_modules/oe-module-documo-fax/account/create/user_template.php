<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
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
				<div class="form-left">
					<h2>User Info</h2>
					<div class="form-group">
						<div class="form-row form-row-1">
							<input type="text" name="first_name" id="first_name" class="input-text" placeholder="<?php echo xlt('First Name') ?>" required>
						</div>
						<div class="form-row form-row-2">
							<input type="text" name="last_name" id="last_name" class="input-text" placeholder="<?php echo xlt('Last Name') ?>" required>
						</div>
					</div>
					<div class="form-row">
						<input type="text" name="your_email" id="your_email" class="input-text" required pattern="[^@]+@[^@]+.[a-zA-Z]{2,6}"
                               placeholder="<?php echo xlt('Email') ?>">
					</div>
					<div class="form-row">
						<input type="password" name="password" class="password" id="password" placeholder="<?php echo xlt('Password') ?>" required>
					</div>
					<div class="form-row">
						<input type="password" name="reenterpassword" class="password" id="password" placeholder="<?php echo xlt('ReEnter Password') ?>" required>
					</div>
				</div>
				<div class="form-right">
					<h2><?php echo xlt('More Info') ?></h2>
					<div class="form-row">
						<!-- <input type="text" name="userrole" class="userrole" id="userrole" placeholder="<?php echo xlt('User Role i.e. Admin') ?>" required>-->
                        <select name="userrole" class="userrole" id="userrole">
                            <option ><?php echo xlt('Select your Job Position') ?></option>
                            <option value="admin"><?php echo xlt('Admin') ?></option>
                            <option value="user"><?php echo xlt('Office Staff') ?></option>
                        </select>
					</div>
					<div class="form-group">
						<div class="form-row form-row-1">
							<select name="jobposition">
								<option ><?php echo xlt('Job Position') ?></option>
							    <option value="provider"><?php echo xlt('Provider') ?></option>
							    <option value="officestaff"><?php echo xlt('Office Staff') ?></option>
							    <option value="administrator"><?php echo xlt('Administrator') ?></option>
							    <option value="support"><?php echo xlt('Tech Support') ?></option>
							</select>
							<span class="select-btn">
							  	<i class="zmdi zmdi-chevron-down"></i>
							</span>
						</div>
						<div class="form-row form-row-1">
							<input type="hidden" name="accountId" class="accountId" id="accountId" value="[@accountId]">
						</div>
					</div>
					<div class="form-group">
						<div class="form-row form-row-1">
							<input type="text" name="phone" class="phone" id="phone" placeholder="<?php echo xlt('Phone Number') ?>" required>
						</div>
					</div>

					<div class="form-checkbox">
						<label class="container"><p>I do accept the <a href="legal.html" class="text" target="_blank"><?php echo xlt('Terms and Conditions') ?></a> of Documo.</p>
						  	<input type="checkbox" required name="checkbox">
						  	<span class="checkmark"></span>
						</label>
					</div>
					<div class="form-row-last">
						<input type="submit" name="register" class="register" value="<?php echo xlt('Register User') ?>">
					</div>
				</div>
			</form>
		</div>
	</div>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
