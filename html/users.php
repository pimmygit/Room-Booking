<?php
/* 
** Description:	Basic user management
**
** @package:	utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	24/09/2007
*/
include_once('settings.php');
include_once('utils/logger.php');
include_once('utils/browser_detection.php');
include_once('utils/mysql.php');

session_start();
// User session check
if ( !isset($_SESSION['userMail']) ) {

	if ( !updateUsageStat("Unauthorized", basename($_SERVER['PHP_SELF'])) ) {
		msg_log(WARN, "Failed to update statistic for online usage of user [Unauthorized].", SILENT);
	}

	msg_log(WARN, "Unauthorized user is trying to gain access from [".$_SERVER['REMOTE_ADDR']."].", SILENT);
	$siteRoot = sprintf('http%s://%s%s', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''), $_SERVER['HTTP_HOST'], dirname($_SERVER['PHP_SELF']));
	header("Location: ".$siteRoot."index.php");
   	exit();
} 

// Write user statistic
if ( !isset($_SESSION['statSet']) || $_SESSION['statSet'] != $_SERVER['PHP_SELF']) {
	
	$_SESSION['statSet'] = $_SERVER['PHP_SELF'];
	
	if ( !updateUsageStat($_SESSION['userMail'], basename($_SERVER['PHP_SELF'])) ) {
		msg_log(WARN, "Failed to update statistic for online usage of user [".$_SESSION['userMail']."].", SILENT);
	}
}

require_once('utils/LoginLDAP.Class.php');
include_once('utils/functions.php');
include_once('utils/mysql.php');

$stringValues = '';
$statusString = 'Enter E-mail to add new user:';
$statusColour = '#404040';

if ( isset($_POST['action']) ) {
	$action = $_POST['action'];
} else {
	$action = '';
}

if ( isset($_POST['eMail']) ) {
	$eMail = $_POST['eMail'];
} else {
	$eMail = '';
}

if ( isset($_POST['isAdmin']) ) {
	$isAdmin = ' checked';
} else {
	$isAdmin = '';
}
			
if (!empty($action)){

	switch($action) {
		
		case 'Add User':
			
			if ( !empty($eMail) ) {
				
				$user = new LoginLDAP();
				
				if ( !$user->isValidUser($eMail) ) {
					msg_log(WARN, "Not an IBM E-mail address: [". $eMail ."].", SILENT);
					$statusString = 'Invalid IBM E-mail address.';
					$statusColour = 'red';
				} else {
					if ( !empty($isAdmin) ) {
						$accType = 'admin';
					} else {
						$accType = 'users';
					}
					
					$cmdStatus = authorizeUser($user->getUserName(), $eMail, $accType);
					
					if ($cmdStatus == COMMAND_OK) {
						$statusString = 'User added successfully.';
						$statusColour = 'green';
						$isAdmin = '';
						$eMail = '';
					} else if ($cmdStatus == ALREADY_EXIST) {
						$statusString = 'User already in the database.';
						$statusColour = 'green';
						$isAdmin = '';
						$eMail = '';
					} else {
						$statusString = 'Failed to add the user to database.';
						$statusColour = 'red';
					}
				}
			}

			break;
			
		case 'Delete Users':
			
			if ( isset($_POST['ibmUserList']) ) {
				$ibmUserList = $_POST['ibmUserList'];
			} else {
				$ibmUserList = '';
			}
			
			foreach ($ibmUserList as $ibmUser) {
				
				$cmdStatus = removeUser($ibmUser);
				
				if ($cmdStatus == COMMAND_OK) {
					$statusString = 'Users removed successfully.';
					$statusColour = 'green';
					$isAdmin = '';
					$eMail = '';
				} else {
					$statusString = 'Failed to remove users.';
					$statusColour = 'red';
				}
			}
			
			break;
						
		default:
			
			$eMail = '';
			$isAdmin = '';
	}
}

/*
* Name: generateUserList
* Desc: Generates the table with users and gives the functionality to add/delete users
* Inpt:	none
* Outp: Type: String, Value: HTML code
* Date: 09/10/2007
*/
function generateUserList($statusString, $statusColour, $eMail, $isAdmin) {
	
	if ( false ) { //$_SESSION('userType') != 'admin' ) {
		
		msg_log(ERROR, "Unauthorized user is trying to aceess the User Management System.", SILENT);
		header("Location: ".dirname($_SERVER['PHP_SELF'])."/index.php");
	}
	
	$stringBuff = '<form id="addUserForm" name="addUserForm" action="users.php" method="post">'.PHP_EOL;
	$stringBuff = $stringBuff.'<table id="userList" width="80%" align="center" cellspacing="0">'.PHP_EOL;

	// Create the title row
	$stringBuff = createTitleRow($stringBuff);
	// Generate the list of users
	$stringBuff = getUsers($stringBuff, $statusString);
	// Field to add new user
	$stringBuff = addUsers($stringBuff, $statusString, $statusColour, $eMail, $isAdmin);
	// Button for deleting selected users
	$stringBuff = delUsers($stringBuff);

	$stringBuff = $stringBuff.'</table></form>'.PHP_EOL;
	
	return $stringBuff;
}

/*
* Name: getTitleColumn
* Desc: Creates the title for the table
* Inpt:	$stringBuff	-> Type: String, Value: [HTML code]
* Outp: Type: String, Value: HTML code
* Date: 09/10/2007
*/
function createTitleRow($stringBuff) {
	
	$stringBuff = $stringBuff.'	<tr>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userThickbox">&nbsp;</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userTitle" id="name_title" style="width:33%;">Name</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userTitle" id="mail_title" style="width:33%;">E-mail</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userTitle" id="type_title" style="width:10%;">Type</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userTitle" id="time_title" style="width:20%;">Created</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
	
	return $stringBuff;
}

/*
* Name: getUsers
* Desc: Creates the list of users whith their attributes from the database
* Inpt:	$stringBuff	-> Type: String, Value: [HTML code]
* Outp: Type: String, Value: HTML code
* Date: 09/10/2007
*/
function getUsers($stringBuff) {
	
	$sqlUsersResult = readUsers();
	
	while ($userData = mysqli_fetch_array($sqlUsersResult, MYSQLI_ASSOC)) {
		$stringBuff = $stringBuff.'	<tr>'.PHP_EOL;
		$stringBuff = $stringBuff.'		<td class="userThickbox"><input id="ibmUserList" name="ibmUserList[]" type="checkbox" value="'.$userData['mail'].'"></td>'.PHP_EOL;
		$stringBuff = $stringBuff.'		<td class="userData" id="name_'.$userData['mail'].'" style="width:33%;">'.$userData['name'].'</td>'.PHP_EOL;
		$stringBuff = $stringBuff.'		<td class="userData" id="mail_'.$userData['mail'].'" style="width:33%;">'.$userData['mail'].'</td>'.PHP_EOL;
		$stringBuff = $stringBuff.'		<td class="userData" id="type_'.$userData['mail'].'" style="width:10%;">'.$userData['type'].'</td>'.PHP_EOL;
		$stringBuff = $stringBuff.'		<td class="userData" id="time_'.$userData['mail'].'" style="width:20%;">'.$userData['datetime'].'</td>'.PHP_EOL;
		$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
	}
						 
	return $stringBuff;
}

/*
* Name: addUsers
* Desc: Adds user to the database the database
* Inpt:	$stringBuff	-> Type: String, Value: [HTML code]
* Outp: Type: String, Value: HTML code
* Date: 09/10/2007
*/
function addUsers($stringBuff, $statusString, $statusColour, $eMail, $isAdmin) {
	
	$stringBuff = $stringBuff.'	<tr>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userThickbox">&nbsp;</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userData" id="new_name" style="width:33%; color:'.$statusColour.'; font-weight:bold;">'.$statusString.'</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userData" id="new_mail" style="width:33%;"><input type="text" name="eMail" id="eMail" value="'.$eMail.'"></td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userData" id="new_type" style="width:10%;"><input type="checkbox" name="isAdmin" id="isAdmin" value="admin"'.$isAdmin.'>If admin.</td>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="userData" id="new_time" style="width:20%;"><input type="submit" name="action" value="Add User" onClick="return checkUserData();"></td>'.PHP_EOL;
	$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
						 
	return $stringBuff;
}

/*
* Name: delUsers
* Desc: Creates the button to delete selected users from the database
* Inpt:	$stringBuff	-> Type: String, Value: [HTML code]
* Outp: Type: String, Value: HTML code
* Date: 09/10/2007
*/
function delUsers($stringBuff) {
	
	$stringBuff = $stringBuff.'	<tr><td class="legendText" style="width:100%;" colspan="5" align="left">&nbsp;</td></tr>'.PHP_EOL;
	$stringBuff = $stringBuff.'	<tr>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="legendText" id="new_time" style="width:100%;" colspan="5" align="left"><input type="submit" name="action" value="Delete Users" onClick="return confirmDelete();"></td>'.PHP_EOL;
	$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
						 
	return $stringBuff;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>IBM Tivoli Network Management - User Management</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="bookingForm.css" />
<script type="text/javascript" src="javascript/functions.js"></script>
</head>
<body>
	
<div class="titleBar"></div>
	
<div class="buttonPanel"><a href="bookingForm.php">Booking Form</a></div>
	<br>
	<?php echo generateUserList($statusString, $statusColour, $eMail, $isAdmin); ?>
	<br>
</body>
</html>