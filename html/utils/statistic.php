<?php
/* 
** Description:	Contains functions for logging usage information
** @package:	Utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	23/04/2008
*/

include_once("utils/mysql.php");

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
		return true;
	} else {
		return false;
	}
}

?>
