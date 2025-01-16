<?php
/* 
** Description:	Contains functions for manipulating with MySQL database
** @package:	Utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	25/09/2007
*/

/*
* Name: getConnection
* Desc: retrieves existing / establishes new connection to MySQL
* Inpt:	
* Outp:	-> Type: resource handle on success / FALSE otherwise
* Date: 09.12.2008
*/
function getConnection() {
	if(array_key_exists("DBCONNECTION",$GLOBALS)) {
		return $GLOBALS["DBCONNECTION"];
	}
	
	// Connect to the database
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	// Actual value doesn't interest us here;
	// Calling functions need to respond accordingly.
	// PHP will autoclose the connection for us at the end of the script
	$GLOBALS["DBCONNECTION"] = $connection;
	return $connection;
}


/*
* Name: isAuthorized
* Desc: Checks if this user exists in the users table.
* Inpt:	$username	-> Type: String,	Value: Users E-mail
* Outp:				-> Type: Boolean,	Value: TRUE if exist, FALSE otherwise
* Date: 25.03.2007
*/
function isAuthorized($email) {
	
	// Create SQL statement
	$sqlQuery = "SELECT * FROM ".TB_USER." WHERE mail = '".$email."'";

	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection) {
		return CONNECT_FAILED;
	}
	
	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	
	if ( mysqli_num_rows($sqlResult) == 1 ) {
		return true;
	} else {		
		return false; 
	}
}

/*
* Name: getAccType
* Desc: Checks what type of account the user has.
* Inpt:	$username	-> Type: String,	Value: Users E-mail
* Outp:				-> Type: String,	Value: [admin||users]
* Date: 25.03.2007
*/
function getAccountType($email) {
	
	// Create SQL statement
	$sqlQuery = "SELECT type FROM ".TB_USER." WHERE mail = '".$email."'";

	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection) {
		return CONNECT_FAILED;
	}
	
	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	
	if ( mysqli_num_rows($sqlResult) == 1 ) {
		
		$userData = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
		
		if ($userData['type'] == 'admin')
			return 'admin';
	}	
		
	return 'users'; 
}

/*
* Name: getRoomsFromDB
* Desc: Reads from the DB the table with rooms and creates an array with their names.
* Inpt:	none
* Outp: SQL Result
* Date: 25.03.2007
*/
function getRoomsFromDB() {
	
	// Create SQL statement
	$sqlQuery = "SELECT * FROM ".TB_ROOM." ORDER BY number";

	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection) {
		return CONNECT_FAILED;
	}
	
	//error_log("SQL Statement: [".$sqlQuery."]");

	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	
	return $sqlResult;
}

/*
* Name: getRoomBooking
* Desc: Reads from the DB the table with rooms and creates an array with their names.
* Inpt:	$roomName	-> Type: String,	Value: Name of the room
*		$timeSlots	-> Type: Array,		Value: Time slots in the database		
* Outp: Type	-> Array of strings, Value: Table names
* Date: 25.03.2007
*/
function getRoomBooking($roomName) {
	
	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection) {
		return CONNECT_FAILED;
	}

	$day = dateUNIXToMySQL($_SESSION['currDay']);
	
	// Create SQL statement
	$sqlQuery = "SELECT * FROM ".$roomName." WHERE day = '$day'";
	
	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	
	// We expect zero rows to be returned.	
	if ( mysqli_num_rows($sqlResult) == 0 ) {
		$sqlQuery = "INSERT INTO ".$roomName." (day) VALUES ('$day')";
		// Get query result
		$sqlResult = mysqli_query($connection, $sqlQuery);
		// Go back to the old statement
		$sqlQuery = "SELECT * FROM ".$roomName." WHERE day = '$day'";
		// Get query result
		$sqlResult = mysqli_query($connection, $sqlQuery);
	}
	
	// We expect no more than ONE row to be returned.	
	$userData = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
	
	return $userData;
}

/*
* Name: getBookings
* Desc: Reads from the DB the table with rooms and creates an array with the bookings for this user.
* Inpt:	$userName	-> Type: String,			Value: Name of the user
* Outp:				-> Type: Array of strings,	Value: Bookings for this user in the format "Array[room_name][unix_date][time_slot] = user_name"
* Date: 25.03.2007
*/
function getBookings($userName, $timeSlots, $year) {
	
	$bookingList = array();
	
	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection) {
		return CONNECT_FAILED;
	}
	// Cache slot name conversions
	$slotNames = array();
	foreach ( $timeSlots as $slot ) {
		$slotNames[$slot] = slotToColName($slot);
	}
	
	// Create SQL statement
	$sqlQuery = "SELECT name FROM ".TB_ROOM;
	// Get query result
	$sqlRoomsResult = mysqli_query($connection, $sqlQuery);
	
	$roomsArray = array();
	while($row = mysqli_fetch_row($sqlRoomsResult)) {;
		$roomsArray[] = $row[0];
	}	
	mysqli_free_result($sqlRoomsResult);
		
	foreach($roomsArray as $roomName) {
		// create sub arrays
		$bookingList[$roomName] = array();
		// Create SQL statement
		$sqlQuery = "SELECT *, UNIX_TIMESTAMP(day) as unixtime FROM ".$roomName." where day >= '$year-01-01' and day <= '$year-12-31'";
		// Get query result
		$sqlDayResult = mysqli_query($connection, $sqlQuery);
		while ( $bookingData = mysqli_fetch_array($sqlDayResult, MYSQLI_ASSOC)) {
			
			$date = $bookingData['unixtime'];
			$bookingList[$roomName][$date] = array();

			foreach ( $timeSlots as $slot ) {
				$slotName = $slotNames[$slot];
				if($bookingData[$slotName] != 'free') { 
					$bookingList[$roomName][$date][$slotName] = $bookingData[$slotName];
				}
			}
		}
		mysqli_free_result($sqlDayResult);
	}
	
	return $bookingList;
}

/*
* Name: readUsers
* Desc: Read users from the database
* Inpt:	none
* Outp: SQL Result on success, error code if fail
* Date: 09.10.2007
*/
function readUsers() {
	
	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection)
		return CONNECT_FAILED;
	
	// Create SQL statement
	$sqlQuery = "SELECT * FROM ".TB_USER." ORDER BY name";
	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
		
	return $sqlResult;
}

/*
* Name: authorizeUser
* Desc: Adds user to the users table
* Inpt:	$userName	-> Type: String,	Value: Name of the user
*		$userMail	-> Type: String,	Value: E-mail of the user		
*		$accType	-> Type: Array,		Value: Type of the account [admin||users]		
* Outp: 			-> Type: INT,		Value: Status return code
* Date: 09.10.2007
*/
function authorizeUser($userName, $userMail, $accType) {
	
	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection)
		return CONNECT_FAILED;
	
	// Check if this account already exist
	///////////////////////////////////////////////
	// Create SQL statement
	$sqlQuery = "SELECT mail FROM ".TB_USER." WHERE mail = '".$userMail."'";
	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	if ( mysqli_num_rows($sqlResult) > 0 ) {
		return ALREADY_EXIST;
	}
	
	// Add the user to the database
	///////////////////////////////////////////////
	// Create SQL statement
	$sqlQuery = "INSERT INTO ".TB_USER." VALUES ('".mysql_real_escape_string($userName)."', '".$userMail."', '".$accType."', NOW())";
	// Get query result
	if (mysqli_query($connection, $sqlQuery)) {
	
		msg_log(INFO, "Admin: [". $_SESSION['userMail'] ."] created account for [". $userMail ."] of type [". $accType ."].", SILENT);
	
		return COMMAND_OK;
		
	} else {
	
		msg_log(ERROR, "SQL Error: Failed to create account for [". $userMail ."] of type [". $accType ."].", SILENT);
	
		return SQL_FAILED;
	}
}

/*
* Name: removeUser
* Desc: Adds user to the users table
* Inpt:	$userName	-> Type: String,	Value: Name of the user
*		$userMail	-> Type: String,	Value: E-mail of the user		
*		$accType	-> Type: Array,		Value: Type of the account [admin||users]		
* Outp: 			-> Type: INT,		Value: Status return code
* Date: 09.10.2007
*/
function removeUser($userMail) {
	
	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if (!$connection)
		return CONNECT_FAILED;
	
	// Remove the user
	///////////////////////////////////////////////
	// Create SQL statement
	$sqlQuery = "DELETE FROM ".TB_USER." WHERE mail = '".$userMail."'";
	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	
	// Verify deletion
	///////////////////////////////////////////////
	$sqlQuery = "SELECT mail FROM ".TB_USER." WHERE mail = '".$userMail."'";
	// Get query result
	$sqlResult = mysqli_query($connection, $sqlQuery);
	
	if ( mysqli_num_rows($sqlResult) > 0 ) {
		msg_log(ERROR, "SQL Error: Failed to delete account: [". $userMail ."].", SILENT);
		return COMMAND_FAIL;
	}
			
	msg_log(INFO, "Admin: [". $_SESSION['userMail'] ."] deleted the account: [". $userMail ."].", SILENT);

	return COMMAND_OK;
}

/*
* Name: updateOnlineStat
* Desc: Increments the number of minutes the user has been online
* Inpt:	$email		-> Type: String,	Value: Users E-mail
* Outp:	none
* Date: 23.05.2008
*/
function updateOnlineStat($email) {
	
	// Create SQL statement
	$sqlQuery = "SELECT cntr FROM stat_online WHERE user = '".$email."'";

	// Connect to the database
	$connection = getConnection();
	
	// Verify connection
	if ($connection) {

		// Get query result
		$sqlResult = mysqli_query($connection, $sqlQuery);
				
		if ( mysqli_num_rows($sqlResult) > 0 ) {
			
			$userData = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
			
			$counter = $userData['cntr'];
			$counter++;
			
			// Create SQL statement
			$sqlQuery = "UPDATE stat_online SET cntr = " . $counter . " WHERE user = '".$email."'";
			
			if ( mysqli_query($connection, $sqlQuery) ) {
				return true;
			} else {
				return false;
			}
		} else {
			// Create SQL statement
			$sqlQuery = "INSERT INTO stat_online VALUES ('".$email."', 1)";
			
			if ( mysqli_query($connection, $sqlQuery) ) {
				return true;
			} else {
				return false;
			}
		}
			
	} else {
		return false;
	}
}

/*
* Name: updateOnlineStat
* Desc: Increments the number of minutes the user has been online
* Inpt:	$email		-> Type: String,	Value: Users E-mail
*		$page		-> Type: String,	Value: Name of the page being accessed
* Outp:	none
* Date: 23.05.2008
*/
function updateUsageStat($email, $page) {
	
	$browserInfo = browser_detection( 'full' );
	
	// Create SQL statement
	$sqlQuery = "INSERT INTO stat_access VALUES ('".$email."', '".$_SERVER['REMOTE_ADDR']."', '".$page."', '".$browserInfo[5]."', '".$browserInfo[6]."', '".$browserInfo[0]."', '".$browserInfo[9]."', NOW())";
	
	// Connect to the database
	$connection = getConnection();
	
	if ( mysqli_query($connection, $sqlQuery) ) {
		// Close connection
		return true;
	} else {
		// Close connection
		return false;
	}
}
?>
