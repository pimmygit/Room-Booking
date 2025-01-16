<?php
/* 
** Description:	Creates yearly calendar table with room availability
**
** @package:	utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	04/10/2007
vim:tabstop=3
*/
include_once('utils/logger.php');

// User session check
if ( !isset($_SESSION['userMail']) ) {
	msg_log(WARN, "Unauthorized user is trying to gain access from [".$_SERVER['REMOTE_ADDR']."].", SILENT);
	$siteRoot = sprintf('http%s://%s%s', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''), $_SERVER['HTTP_HOST'], dirname($_SERVER['PHP_SELF']));
	header("Location: ".$siteRoot."index.php");
   	exit();
} 

include_once('utils/functions.php');
include_once('utils/mysql.php');

/*
* Name: generateDailyList
* Desc: Generates the table with bookings for a particular day
* Inpt:	$yearToShow	-> Type: INT, Value: [UNIX timestamp]
* Outp: Type: String, Value: HTML code
* Date: 04/10/2007
*/
function generateYearlyTable($yearToShow, $timeSlots) {

	$stringBuff = '<table class="tMatrix" id="yearlySchedule" align="center" cellspacing="0">'.PHP_EOL;

	// Prints 5 weeks in a row
	$stringBuff = getWeekDays($stringBuff, $yearToShow);
	
	// Read the availability of each room and create the table matrix
	$stringBuff = getDates($stringBuff, $yearToShow, $timeSlots);
	
	$stringBuff = $stringBuff.'</table>'.PHP_EOL;
	
	return $stringBuff;
}

/*
* Name: getWeekDays
* Desc: Creates the title for the table based on 5 week days
* Inpt:	$stringBuff		-> Type: String, Value: [HTML code]
*		$sellectedYear	-> Type: INT, Value: [UNIX timestamp]
* Outp: Type: String, Value: HTML code
* Date: 04/10/2007
*/
function getWeekDays($stringBuff, $sellectedYear) {
	
	$maxDays = 31 + 7;
	// little harmless hack
	if ( (dateUNIXToYear($sellectedYear) == 2008) || (dateUNIXToYear($sellectedYear) == 2014)) {
		$maxDays--;
	}
	$weekDays = array ('M', 'T', 'W', 'T', 'F', 'S', 'S');
	$stringBuff = $stringBuff.'	<tr>'.PHP_EOL;
	$stringBuff = $stringBuff.'		<td class="tableCorner">'.date('Y', $sellectedYear).'</td>'.PHP_EOL;
	
	$dayOfWeekOnTheTable = 0;
	// Print 6 weeks in a row
	for ($i = 1; $i < $maxDays; $i++) {
		
		if ( $dayOfWeekOnTheTable > 4 ) {
			$stringBuff = $stringBuff.'		<td class="titleOfWeekend">'.$weekDays[$dayOfWeekOnTheTable].'</td>'.PHP_EOL;
		} else {
			$stringBuff = $stringBuff.'		<td class="titleOfWeek">'.$weekDays[$dayOfWeekOnTheTable].'</td>'.PHP_EOL;
		}
		
		$dayOfWeekOnTheTable++;
		if ($dayOfWeekOnTheTable > 6) {
			$dayOfWeekOnTheTable = 0;
		}
	}
	$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
	
	return $stringBuff;
}

/*
* Name: getDates
* Desc: Creates the title for the table based on rooms data from the database
* Inpt:	$stringBuff		-> Type: String, Value: [HTML code]
*		$sellectedYear	-> Type: INT, Value: [UNIX timestamp]
* Outp: Type: String, Value: HTML code
* Date: 04/10/2007
*/
function getDates($stringBuff, $sellectedYear, $timeSlots) {
	
	// Number of days in a row
	$maxDays = 31 + 7;
	// little harmless hack
	if ( (dateUNIXToYear($sellectedYear) == 2008) || (dateUNIXToYear($sellectedYear) == 2014)) {
		$maxDays--;
	}
	$months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

	$year = date('Y', $sellectedYear);
	$currentBookings = getBookings($_SESSION['userName'], $timeSlots, $year);

	$allRooms = array();
	$sqlRoomsResult = getRoomsFromDB();
	if ($sqlRoomsResult !== FALSE && $sqlRoomsResult !== CONNECT_FAILED) {
		while ( $roomData = mysqli_fetch_array($sqlRoomsResult, MYSQLI_ASSOC) ) {
			$allRooms[] = $roomData['name'];
		}
	}
	mysqli_free_result($sqlRoomsResult);

	$mon = 1;

	// Important: mktime + MySQL UNIX_TIMESTAMP() by default don't play UTC vs BST/GMT together nicely
	date_default_timezone_set("Europe/London");
	
	// Populate the table for each month
	$today = mktime(0,0,0);
	foreach ( $months as $month ) {
		
		$day = 1;
		
		// Start populating the table
		$stringBuff .= '	<tr>'.PHP_EOL;
		$stringBuff .= '		<td class="tableTitleVert" id="'.$month.'">'.$month.'</td>'.PHP_EOL;

		$numDaysThisMonth = date("t",mktime(0,0,0,$mon,1,$year));
		
		$dayOfWeekOnTheTable = 1;
		for ($i = 1; $i < $maxDays; $i++) {
		
			$str = "";
			$cssClass = "";
			if($day>$numDaysThisMonth) {
				if ( $dayOfWeekOnTheTable > 5 ) {
					$cssClass = "weekend";
				} else {
					$cssClass = "weekday";
				}	
				$str = "&nbsp;";
			} else {
				$unixDate = mktime(0,0,0,$mon,$day,$year);
				
				$dayOfWeekOnThisDate = date('N', $unixDate);
				
				// If Day of the month matches the day of the week, and
				// the day of the month does not roll to the next month
				if ( $dayOfWeekOnTheTable == $dayOfWeekOnThisDate) {
					if($unixDate == $today) {
						$cssClass = "today";
					}
					
					if ( $dayOfWeekOnTheTable > 5 ) {
						$cssClass = "weekend";
					} else {
						$cssClass = "weekday";
					}	
					
					// Check if there are any bookings for any of the rooms
					$myBookingExist = false;
					$roomAvailability = 0;
						
					foreach( $allRooms as $roomName)  {
						// Booking available for that day
						if ( isset($currentBookings[$roomName][$unixDate]) ) {
							//error_log("Booking available for [".$roomName."][".$unixDate."]: " . isset($currentBookings[$roomName][$unixDate]));
							foreach ( $currentBookings[$roomName][$unixDate] as $roomDay ) {
								// If its my booking
								if ( $roomDay == $_SESSION['userName'] ) {
									$myBookingExist = true;
									// Exit the while loop
								} else {
									$roomAvailability++;
								}
							}
						}
					}
	
					// Rooms availability:
					// 7 rooms x 16 slots (from 9am to 5pm) = 112 timeslots
					// < 30% - Unmarked
					// > 35% - Yellow
					// > 55% - Orange
					// > 75% - Red (ish)
					if($roomAvailability > 84) {
						$cssClass = "percentage75";
					} else if ($roomAvailability > 62) {
						$cssClass = "percentage55";
					} else if ($roomAvailability > 39) {
						$cssClass = "percentage35";
					}	
					
					if($myBookingExist) {
						$cssClass = "mybooking";
					}
						
					// Workaround daylight saving (for the link only)
					if (date("I", $unixDate) == 1) {
						$unixDate += 3600;
					}
				
					$str = '<a href="bookingForm.php?currDay='.$unixDate.'&amp;currYear='.$sellectedYear.'">'.$day.'</a>';
					$day++;
				} else {
					if ( $dayOfWeekOnTheTable > 5 ) {
						$cssClass = "weekend";
					} else {
						$cssClass = "weekday";
					}	
					$str = "&nbsp;";
				}
			}

			$stringBuff .= '		<td onMouseOver="lightOn('."'$month'".');" onMouseOut="lightOff('."'$month'".');" class="'.$cssClass.'">'.$str.'</td>'.PHP_EOL;
			
			$dayOfWeekOnTheTable++;
			if ($dayOfWeekOnTheTable > 7) {
				$dayOfWeekOnTheTable = 1;
			}
		}

		$stringBuff = $stringBuff.'	</tr>'.PHP_EOL;
		$mon++;
	}					
						 
	return $stringBuff;
}
?>
