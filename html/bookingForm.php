<?php
/* 
** Description:	Creates the room booking form
**
** @package:	Room Booking
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

include_once('utils/functions.php');
include_once('dailyList.php');
include_once('yearCalendar.php');

if ( isset($_GET['currDay']) ) {
	$_SESSION['currDay'] = $_GET['currDay'];
} else {
	$_SESSION['currDay'] = time();
}
if ( isset($_GET['currYear']) ) {
	$_SESSION['currYear'] = $_GET['currYear'];
} else {
	$_SESSION['currYear'] = time();
}

$lastyear = $_SESSION['currYear'] - 31536000;
$yesterday = $_SESSION['currDay'] - 86400;
$tomorrow = $_SESSION['currDay'] + 86400;
$nextyear = $_SESSION['currYear'] + 31536000;

// Write user statistic
if ( !isset($_SESSION['statSet']) || $_SESSION['statSet'] != $_SERVER['PHP_SELF']) {
	
	$_SESSION['statSet'] = $_SERVER['PHP_SELF'];
	
	if ( !updateUsageStat($_SESSION['userMail'], basename($_SERVER['PHP_SELF'])) ) {
		msg_log(WARN, "Failed to update statistic for online usage of user [".$_SESSION['userMail']."].", SILENT);
	}
}
if ( !updateOnlineStat($_SESSION['userMail']) ) {
	msg_log(WARN, "Failed to update statistic for online usage of user [".$_SESSION['userMail']."].", SILENT);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Meeting room booking - IBM Tivoli Network Management</title>
<meta http-equiv="refresh" content="60">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="bookingForm.css" />
<script type="text/javascript" src="javascript/functions.js"></script>
<script type="text/javascript" src="javascript/clickHandlers.js"></script>
<script type="text/javascript" src="javascript/xmlHttpRequest.js"></script>
</head>

<body>
	
<div class="titleBar"></div>
	<?php if ( $_SESSION['userType'] == 'admin' ) { echo '<div class="buttonPanel"><a href="users.php">Manage Users</a></div>'; } ?>
	<div class="centering"> 
		<div class="dayChooser">
			
    <div id="buttonBack" onClick="window.location='bookingForm.php?currDay=<?php echo $yesterday ?>&amp;currYear=<?php echo $_SESSION['currDay'] ?>'"></div>
			<div id="today"><?php echo dateUNIXToString( $_SESSION['currDay'] ) ?></div>
			<div id="buttonForward" onClick="window.location='bookingForm.php?currDay=<?php echo $tomorrow ?>&amp;currYear=<?php echo $_SESSION['currDay'] ?>'"></div>
		</div>
	</div>
	<?php echo generateDailyList($_SESSION['currDay'], $timeSlots); ?>
	<br>
	<table width="90%" align="center" cellspacing="0">
		<tr>
		<!--	<td class="legendCell" style="width: 30px; background-color: #0000FF;">&nbsp;</td> -->
			<td class="legendText" style="width: 35%; background-color: white;">(C29) -> Big Conference room number 29</td>
		<!--	<td class="legendCell" style="width: 30px; background-color: #FF8000;">&nbsp;</td> -->
			<td class="legendText" style="width: 35%; background-color: white;">(29) -> Small room number 29</td>
		<!--	<td class="legendCell" style="width: 30px; background-color: #9900CC">&nbsp;</td> -->
		<!--	<td class="legendText" style="width: 25%; background-color: white;">(P34) -> Purple Zone, room 34</td> -->
            <td class="none" style="width:100%; background-color:white; border:none">&nbsp;</td>
		</tr>
	</table>
	<br>
	<div class="centering"> 
		<div class="yearChooser">		
			
    <div id="buttonBack" onClick="window.location='bookingForm.php?currDay=<?php echo $_SESSION['currDay'] ?>&amp;currYear=<?php echo $lastyear ?>'"></div>
			<div id="thisYear"><?php echo dateUNIXToYear( $_SESSION['currYear'] ) ?></div>
			<div id="buttonForward" onClick="window.location='bookingForm.php?currDay=<?php echo $_SESSION['currDay'] ?>&amp;currYear=<?php echo $nextyear ?>'"></div>
		</div>
	</div>
	<?php echo generateYearlyTable($_SESSION['currYear'], $timeSlots); ?>
	<br>
	<table width="90%" id="legend" align="center" cellspacing="0">
		<tr>
			<td class="legendCell" style="background-color: #00EE00;">&nbsp;</td>
			<td class="legendText" style="width: 15%; background-color: white;">My Bookings</td>
			<td class="legendCell" style="background-color: #E0FFFF">&nbsp;</td>
			<td class="legendText" style="width: 15%; background-color: white;">< 35% bookings</td>
			<td class="legendCell" style="background-color: FFFF66;">&nbsp;</td>
			<td class="legendText" style="width: 15%; background-color: white;">> 35% bookings</td>
			<td class="legendCell" style="background-color: FFCC33;">&nbsp;</td>
			<td class="legendText" style="width: 15%; background-color: white;">> 55% bookings</td>
			<td class="legendCell" style="background-color: #FF9900;">&nbsp;</td>
			<td class="legendText" style="width: 15%; background-color: white;">> 75% bookings</td>
            <td class="none" style="width:100%; background-color:white; border:none">&nbsp;</td>
		</tr>
	</table>
	<br>
</body>
</html>
