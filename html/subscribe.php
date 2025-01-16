<?php
/* 
** @package:	Security
** @subpackage:	User Authentication
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	27/09/2007
*/
session_start(); 
// User session check
if ( !isset($_SESSION['userMail']) ) {
	msg_log(WARN, "Unauthorized user is trying to gain access from [".$_SERVER['REMOTE_ADDR']."].", SILENT);
	$siteRoot = sprintf('http%s://%s%s', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''), $_SERVER['HTTP_HOST'], dirname($_SERVER['PHP_SELF']));
	header("Location: ".$siteRoot."index.php");
   	exit();
} 

include_once('settings.php');
include_once('utils/logger.php');

if ( isset($_POST['formRead']) ) {
	
	$from_header = "From: ".$_SESSION['userMail'];
	$subject = "Meeting rooms access request: ".$_SESSION['userName'];
	
	if ( isset($_POST['requestSummary']) && !empty($_POST['requestSummary']) ) {
		$requestSummary = $_POST['requestSummary'];
	} else {
		$requestSummary = "User did not provide any reason for this request.";
	}
	
	$mailContent =	"Dear ".ROOM_ADMIN_NAME.",".PHP_EOL.PHP_EOL.
					"Please review the following request for access to the Network Management Meeting Rooms from ".$_SESSION['userName'].".".PHP_EOL.PHP_EOL.
					"Users request is:".PHP_EOL.
					"-------------------------------------------------------".PHP_EOL.
					$requestSummary.PHP_EOL.
					"-------------------------------------------------------".PHP_EOL.PHP_EOL.
					"If the user is entitled to use the Network Management meeting rooms,".PHP_EOL.
					"please approve this request by adding his E-mail to the list of users.".PHP_EOL.PHP_EOL.
					"If however he is not part of the Network Management team, please reject this request.".PHP_EOL.PHP_EOL.PHP_EOL.
					"Have a great day,".PHP_EOL.
					":-)";
					
	if ( mail(ROOM_ADMIN_MAIL, $subject, $mailContent, $from_header) ) {
	
		$infoMessage =	"Your request has been sent to ".ROOM_ADMIN_NAME."<br />".PHP_EOL.
						"You will be notified when access is granted.";
	} else {
		$infoMessage =	"Your request has failed to be sent to ".ROOM_ADMIN_NAME."<br />".PHP_EOL.
						"Please send your request via E-mail.";
	}
} else {
	$infoMessage =	"You are not authorized<br />".PHP_EOL.
					"to access the Network Management meeting rooms.<br /><br />".PHP_EOL.
					"To subscribe please submit a request to the booking administrator.";
}
?>
<html>
<head>
<title>IBM Tivoli Network Management - Meeting room booking subscription</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<body>
<div class="titlebar"></div>
<div id="mainContainer"><i></i>
	<div id="mainContainerVertCenter" align="center">
		<div class="formPanel">
		<form name="userRegistration" id="userRegistration" method="post" action="subscribe.php">
			<div class="infoPanel">
				<p align="center">
					<?php echo $infoMessage; ?>
				</p>
			</div>
			<div class="textArea">
				<table id="subscribeUser" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" width="100%">
					<tr>
						<td align="left" height="10" width="50%"></td>
						<td align="left" height="10" width="50%"></td>
					</tr>
					<tr>
						<td class="infoTitle" align="left" height="30" width="50%">
							Booking Administrator:
						</td>
						<td class="regularText" align="left" height="30" width="50%">
							<?PHP echo ROOM_ADMIN_NAME; ?>
						</td>
					</tr>
					<tr>
						<td align="left" height="30" width="50%">
							
						</td>
						<td align="left" height="30" width="50%">
							
						</td>
					</tr>
				</table>
				<p align="left">
					Summary of the request: <textarea name="requestSummary" id="requestSummary" class="inputbox" cols="50" rows="6"></textarea>
				</p>
				<p align="right">
					<input name="formRead" type="hidden" value="yes" />
					<input class="formButtons" type="submit" name="Submit" value="Submit" />
				</p>
			</div>
		</form>
		</div>
	</div>
</div>

</body>
</html>