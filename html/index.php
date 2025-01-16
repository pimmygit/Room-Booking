<?php
/* 
** @package:	Security
** @subpackage:	User Authentication
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	24/09/2007
*/

//header("Location: suspended.php");

require_once('settings.php');
require_once('utils/logger.php');
include_once('utils/browser_detection.php');
include_once('utils/mysql.php');
require_once('utils/functions.php');
require_once('utils/LoginLDAP.Class.php');

if ( !empty($_POST['ibmUSER']) || !empty($_POST['ibmPASS']) ) {
	
	$loginMail = $_POST['ibmUSER'];
	$loginPass = $_POST['ibmPASS'];
	$alertMess = '';
	
	$user = new LoginLDAP();
	
	if ( !$user->isValidUser($loginMail) ) {
		msg_log(WARN, "Not an IBM E-mail address: [". $loginMail ."].", SILENT);
		$alertMess = 'Invalid IBM E-mail address.';
	} else {
		if ( !$user->isValidPassword($loginPass) ) {
			msg_log(WARN, "Wrong password for: [". $loginMail ."].", SILENT);
			$alertMess = 'Incorrect password.';
		}
	}
	
	if ( empty($alertMess) ) {
		session_start();
		session_register("roomBooking");			
		
		// Get domain without www and any port (if present)
		$domain = getDomain();
		// Set expiration (30 days)
		$expire = time() + (86400*30);
		// Set the cookie
		setcookie('ibmMail', $loginMail, $expire, '/', $domain, 0);
		// Set session variables
		$_SESSION['userMail'] = $loginMail;
		$_SESSION['userName'] = $user->getUserName();
		$_SESSION['userType'] = $user->getAccType($loginMail);

		// Write user statistic
		if ( !isset($_SESSION['statSet']) || $_SESSION['statSet'] != $_SERVER['PHP_SELF']) {
			
			$_SESSION['statSet'] = $_SERVER['PHP_SELF'];
			
			if ( !updateUsageStat($_SESSION['userMail'], basename($_SERVER['PHP_SELF'])) ) {
				msg_log(WARN, "Failed to update statistic for online usage of user [".$_SESSION['userMail']."].", SILENT);
			}
		}

		$siteRoot = sprintf('http%s://%s%s', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''), $_SERVER['HTTP_HOST'], dirname($_SERVER['PHP_SELF']));
		
		if ( $user->hasAccess($loginMail) ) {
			msg_log(INFO, "User: [". $_SESSION['userMail'] ."] logged in.", SILENT);
			header("Location: bookingForm.php");
			exit();
		} else {
			msg_log(WARN, "Unauthorized IBM user tryed to log in.", SILENT);
			header("Location: subscribe.php");
			exit();
		}
	}
} else {
	
	if( isset($_COOKIE["ibmMail"]) ) {
		$loginMail = $_COOKIE["ibmMail"];
	} else {
		$loginMail = '';
	}
	
	$loginPass = '';
	$alertMess = '';
}
?>
<html>
<head>
<title>IBM Tivoli Network Management - Room booking</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="main.css" />
<script type="text/javascript" src="javascript/functions.js"></script>
</head>

<body onLoad="positionCursor();">
<div class="titleBar"></div>
<div id="mainContainer"><i></i>
	<div id="mainContainerVertCenter" align="center">
		<div class="infoPanel"><?php
			if ( empty($alertMess) ) {
				echo '<p class="regularText">Please log in with your IBM E-mail and password.</p>';
			} else {
				echo '<p class="regularText">'.$alertMess.'</p>';
			}
		?></div>
		<div class="formPanel">
		<form name="userCredentials" id="userCredentials" method="post" action="index.php">
			<div class="textField">
				<p align="left">
					Username: <input class="inputbox" type="text" name="ibmUSER" id="ibmUSER" value="<?php echo $loginMail; ?>" size="20" tabindex="1" />
				</p>
				<p align="left">
					Password: <input class="inputbox" type="password" name="ibmPASS" id="ibmPASS" value="" size="20" tabindex="2" />
				</p>
				<p align="right">
					<input class="formButtons" type="submit" name="Login" value="Login" onClick="return verifyFields();" tabindex="3" />
				</p>
			</div>
		</form>
		</div>
	</div>
</div>

</body>
</html>