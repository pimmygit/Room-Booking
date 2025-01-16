<?php
/* 
** Description:	Various helper functions
**
** @package:	utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	24/09/2007
*/
include_once('utils/logger.php');

// User session check
if ( !isset($_SESSION['userMail']) ) {
	msg_log(WARN, "Unauthorized user is trying to gain access from [".$_SERVER['REMOTE_ADDR']."].", SILENT);
	$siteRoot = sprintf('http%s://%s%s', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''), $_SERVER['HTTP_HOST'], dirname($_SERVER['PHP_SELF']));
	header("Location: ".$siteRoot."index.php");
   	exit();
} 

include_once('utils/Rooms.Class.php');
include_once('utils/functions.php');
include_once('utils/mysql.php');

/*
* Name: generateDailyList
* Desc: Generates the table with bookings for a particular day
* Inpt:	$dayToShow	-> Type: INT, Value: [UNIX timestamp]
*		$timeSlots	-> Type: Array, Value: [Timeslots defined in settings.php]
* Outp: Type: String, Value: HTML code
* Date: 24/09/2007
*/
function generateDailyList($dayToShow, $timeSlots) {
	
	// Get all rooms properties from the database
	$rooms = new Rooms();
	$rooms->dbGetRooms();
	
	$stringBuff = '<table class="tMatrix" id="dailySchedule" align="center" cellspacing="0">'.PHP_EOL;

	// Get all rooms and create the title row of the table
	$stringBuff = getTitleColumn($stringBuff, $rooms, $dayToShow);
	
	// Read the availability of each room and create the table matrix
	$stringBuff = getRoomsStatus($stringBuff, $rooms, $timeSlots);
	
	$stringBuff = $stringBuff.'</table>'.PHP_EOL;
	
	return $stringBuff;
}

/*
* Name: getTitleColumn
* Desc: Creates the title for the table based on rooms data from the database
* Inpt:	$stringBuff	-> Type: String, Value: [HTML code]
*		$rooms		-> Type: Object, Value: [Room data]
*		$dayToShow	-> Type: INT, Value: [UNIX timestamp]
* Outp: Type: String, Value: HTML code
* Date: 24/09/2007
*/
function getTitleColumn($stringBuff, $rooms, $dayToShow) {
	
	$stringBuff = $stringBuff.'	<tr>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="tableCorner">'.date('l', $dayToShow).'</td>'.PHP_EOL;
	
	$rooms->resetPointer();
	while ( $room = $rooms->getRoom() ) {
		
		if ( $_SESSION['currDay'] > time() ) {
			$stringBuff = $stringBuff.'		<td class="tableTitleHor" id="'.$room['name'].'_title" onClick="bookRoomForDay(\''.$room['name'].'\', '.$_SESSION['currDay'].', \''.$_SESSION['userName'].'\');">'.ucwords($room['name']).' ('.$room['number'].')</td>'.PHP_EOL;
		} else {
			$stringBuff = $stringBuff.'		<td class="tableTitleHor" id="'.$room['name'].'_title" style="cursor: default;">'.ucwords($room['name']).' ('.$room['number'].')</td>'.PHP_EOL;
		}
	}

	$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
	
	return $stringBuff;
}

/*
* Name: getRoomsStatus
* Desc: Creates the title for the table based on rooms data from the database
* Inpt:	$stringBuff	-> Type: String, Value: [HTML code]
*		$rooms		-> Type: Object, Value: [Room data]
*		$timeSlots	-> Type: Array, Value: [Timeslots defined in settings.php]
* Outp: Type: String, Value: HTML code
* Date: 24/09/2007
*/
function getRoomsStatus($stringBuff, $rooms, $timeSlots) {
	
	// Day starts at 8.00 am. Required to restrict the user to modify meetings passed or already started
	$dayStartAt = mktime(0, 0, 0, date('m', $_SESSION['currDay']), date('d', $_SESSION['currDay']), date('Y', $_SESSION['currDay'])) + 28800;
	$roomBooking = array();
	
	// Cache slot name conversions
	$slotNames = array();
	foreach ( $timeSlots as $slot ) {
		$slotNames[$slot] = slotToColName($slot);
	}

	// Check what has been booked for each room
	$rooms->resetPointer();
	while ( $room = $rooms->getRoom() ) {
		$roomBooking[$room['name']] = getRoomBooking($room['name']);
	}


	// Populate the booking for each room for each timeslot
	foreach ( $timeSlots as $slot ) {

		// Start populating the table
		$stringBuff = $stringBuff.'	<tr>'.PHP_EOL;
		$stringBuff = $stringBuff.'		<td class="tableTitleVert" id="'.$slotNames[$slot].'">'.slotToRowName($slot).'</td>'.PHP_EOL;

		$rooms->resetPointer();
		while ( $room = $rooms->getRoom() ) {
		
			if ( ($roomBooking[$room['name']][$slotNames[$slot]] == 'free') ) {
				// We allow users to book a room max 5 min after the time slot has started
				if ( $dayStartAt > (time() - 300) ) {
					// If this slot is free, then we don't colour it and logged-in user can book it
					$stringBuff = $stringBuff.'		<td class="cellAvail" id="'.$room['name'].'_'.$slotNames[$slot].'" onMouseOver="lightOn(\''.$slotNames[$slot].'\');" onMouseOut="lightOff(\''.$slotNames[$slot].'\');" onClick="bookRoom(this.id, \''.$room['name'].'\', \''.$slotNames[$slot].'\', '.$_SESSION['currDay'].', \''.rawurlencode ($_SESSION['userName']).'\', \''.$_SESSION['userType'].'\');"><span>&nbsp;</span></td>'.PHP_EOL;
				} else {
					// This is in the past so user should not be allowed to modify the history
					$stringBuff = $stringBuff.'		<td class="cellAvail" id="'.$room['name'].'_'.$slotNames[$slot].'" onMouseOver="lightOn(\''.$slotNames[$slot].'\');" onMouseOut="lightOff(\''.$slotNames[$slot].'\');" onClick="msgTooLate()" style="cursor: default;"><span>&nbsp;</span></td>'.PHP_EOL;
				}
			} else {
				
				// Either the user who booked this slot or the administrator can modify it
				if ( $roomBooking[$room['name']][$slotNames[$slot]] == $_SESSION['userName'] ) {
					if ( $dayStartAt > time() - 300) { // We allow users to un-book a room max 5 min after the time slot has started
						// If this slot is booked by the user logged-in, then we mark it as green and the user can release the booking
						$stringBuff = $stringBuff.'		<td class="cellBooked" id="'.$room['name'].'_'.$slotNames[$slot].'" onMouseOver="lightOn(\''.$slotNames[$slot].'\');" onMouseOut="lightOff(\''.$slotNames[$slot].'\');" onClick="bookRoom(this.id, \''.$room['name'].'\', \''.$slotNames[$slot].'\', '.$_SESSION['currDay'].', \''.rawurlencode ($_SESSION['userName']).'\', \''.$_SESSION['userType'].'\');"><span>'.$roomBooking[$room['name']][$slotNames[$slot]].'</span></td>'.PHP_EOL;
					} else {
						// This is in the past so user should not be allowed to modify the history
						$stringBuff = $stringBuff.'		<td class="cellBooked" id="'.$room['name'].'_'.$slotNames[$slot].'" onMouseOver="lightOn(\''.$slotNames[$slot].'\');" onMouseOut="lightOff(\''.$slotNames[$slot].'\');" style="cursor: default;"><span>'.$roomBooking[$room['name']][$slotNames[$slot]].'</span></td>'.PHP_EOL;
					}
				} else {
					// If this slot is booked by another user, then we mark it as red and the user cannot book it unless its the Booking Administrator
					if ( ($_SESSION['userType'] == 'admin') && ($dayStartAt > time())  ) {
						$stringBuff = $stringBuff.'		<td class="cellTaken" id="'.$room['name'].'_'.$slotNames[$slot].'" onMouseOver="lightOn(\''.$slotNames[$slot].'\');" onMouseOut="lightOff(\''.$slotNames[$slot].'\');" style="cursor: pointer;" onClick="bookRoom(this.id, \''.$room['name'].'\', \''.$slotNames[$slot].'\', '.$_SESSION['currDay'].', \''.rawurlencode ($_SESSION['userName']).'\', \''.$_SESSION['userType'].'\');"><span>'.$roomBooking[$room['name']][$slotNames[$slot]].'</span></td>'.PHP_EOL;
					} else {
						$stringBuff = $stringBuff.'		<td class="cellTaken" id="'.$room['name'].'_'.$slotNames[$slot].'" onMouseOver="lightOn(\''.$slotNames[$slot].'\');" onMouseOut="lightOff(\''.$slotNames[$slot].'\');"><span>'.$roomBooking[$room['name']][$slotNames[$slot]].'</span></td>'.PHP_EOL;
					}
				}
			}
		}

		$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
		$dayStartAt = $dayStartAt + 1800;
	}					
						 
	return $stringBuff;
}
?>
