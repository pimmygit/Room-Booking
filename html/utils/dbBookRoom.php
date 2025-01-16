<?php
/* 
** Description:	Fetches and executes the XML HTTP Request
**
** @package:	Room Booking
** @subpackage:	XML HTTP Request
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	24/09/2007
*/
include_once('../utils/functions.php');
include_once('../utils/logger.php');
include_once('../utils/browser_detection.php');
include_once('../utils/mysql.php');
include_once('../settings.php');

session_start(); 
// User session check
if ( !isset($_SESSION['userMail']) ) {

	if ( !updateUsageStat("Unauthorized", "reserveRoom") ) {
		msg_log(WARN, "Failed to update statistic for online usage of user [Unauthorized].", SILENT);
	}

	msg_log(WARN, "Unauthorized user is trying to gain access from [".$_SERVER['REMOTE_ADDR']."].", SILENT);
	$siteRoot = sprintf('http%s://%s%s', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''), $_SERVER['HTTP_HOST'], dirname($_SERVER['PHP_SELF']));
	header("Location: ".$siteRoot."index.php");
   	exit();
} 

if (!empty($_GET['action'])) {
	 
   	$action = $_GET['action'];
	
	switch ($action) {
	
		case 'bookRoom':
			if ( !empty($_GET['room']) && !empty($_GET['slot']) && !empty($_GET['act']) ) {
				echo bookRoom( $_GET['room'], $_GET['slot'], $_GET['day'], $_GET['name'], $_GET['act'] );
			}
			break;
						
		case 'blahblah':
			
			break;
						
		default:
			echo BAD_REQUEST;
			break;	
	}
} else {
	echo BAD_REQUEST;
}

/*
* Name: bookRoom
* Desc: Reads from the DB the table with rooms and creates an array with their names.
* Inpt:	$roomName		-> Type: String,	Value: Name of the room
*		$timeSlots		-> Type: Array,		Value: Time slots in the database		
*		$userName		-> Type: String,	Value: Name of the persoon placing the booking		
*		$bookingType	-> Type: Boolean,	Value: If the booking should be placed or released		
* Outp:					-> Type: Boolean,	Value: TRUE on success, FALSE otherwise
* Date: 26.09.2007
*/
function bookRoom( $roomName, $timeSlot, $day, $userName, $bookingType ) {
	
	if ( !updateUsageStat($_SESSION['userMail'], "reserveRoom") ) {
		msg_log(WARN, "Failed to update statistic for online usage of user [".$_SESSION['userMail']."].", SILENT);
	}

	// Create SQL statement
	if ( $bookingType == 'true' ) {
		msg_log(INFO, "Room: [".$roomName."] RESERVED for [".dateUNIXToMySQL($day)." ".colNameToTimeSlot($timeSlot)."] by user [".$userName."].", SILENT);
		$sqlQuery = "UPDATE " .$roomName. " SET " .$timeSlot. " = '" .mysql_real_escape_string($userName). "' WHERE day = '" .dateUNIXToMySQL( $day ). "'";
	} else {
		msg_log(INFO, "Room: [".$roomName."] RELEASED for [".dateUNIXToMySQL($day)." ".colNameToTimeSlot($timeSlot)."] by user [".$userName."].", SILENT);
		$sqlQuery = "UPDATE " .$roomName. " SET " .$timeSlot. " = 'free' WHERE day = '" .dateUNIXToMySQL( $day ). "'";
	}

	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection) {
		return CONNECT_FAILED;
	}
	
	//error_log("SQL Statement: [".$sqlQuery."]");

	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	
	return COMMAND_OK;
}	
?>
