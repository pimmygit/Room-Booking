<?php
/* 
** Description:	Various helper functions
**
** @package:	utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	24/09/2007
*/

/*
* Name: dateUNIXToString
* Desc: Converts time from UNIX timestamp to a readable string
* Inpt:	$timestamp	-> Type: INT, Value: [UNIX timestamp]
* Outp: Type: String, Value: Time in the format [01. Jan. 2007.]
* Date: 24/09/2007
*/
function dateUNIXToString( $unixTimestamp ) {
	return gmdate('D, d M Y', $unixTimestamp);
}

/*
* Name: dateUNIXToYear
* Desc: Converts time from UNIX timestamp to just the year
* Inpt:	$timestamp	-> Type: INT, Value: [UNIX timestamp]
* Outp: Type: String, Value: Time in the format [2007]
* Date: 24/09/2007
*/
function dateUNIXToYear( $unixTimestamp ) {
	return gmdate('Y', $unixTimestamp);
}

/*
* Name: dateUNIXToMySQL
* Desc: Converts time from UNIX timestamp to acceptable by MySQL format
* Inpt:	$timestamp	-> Type: INT, Value: [UNIX timestamp]
* Outp: Type: String, Value: Time in the format [2007-12-31]
* Date: 24/09/2007
*/
function dateUNIXToMySQL( $unixTimestamp ) {
	return date('Y-m-d', $unixTimestamp);
}

/*
* Name: dateMySQLtoUNIX
* Desc: Converts time from MySQL date to a UNIX timestamp
* Inpt:	$mysqlDate	-> Type: String, Value: [MySQL Date]
* Outp: Type: INT, Value: Time in seconds
* Date: 24/09/2007
*/
function dateMySQLtoUNIX( $mysqlDate ) {
	
	list($year, $month, $day) = split('[-]', $mysqlDate);
	return mktime(0, 0, 0, $month, $day, $year);
}

/*
* Name: slotToColName
* Desc: Converts time slot from '08:00 to DB column name 'slot0800'
* Inpt:	$timeSlot	-> Type: String, Value: [hh:mm]
* Outp: Type: String, Value: Column name
* Date: 24/09/2007
*/
function slotToColName( $timeSlot ) {
	
	$trimmer = array(':' => '');
	return 'slot'.strtr($timeSlot, $trimmer);
}

/*
* Name: slotToRowName
* Desc: Converts time slot from '08:00 to row name: '08:00 - 08:30'
* Inpt:	$timeSlot	-> Type: String, Value: [hh:mm]
* Outp: Type: String, Value: Row value in the format '08:00 - 08:30'
* Date: 04/10/2007
*/
function slotToRowName( $timeSlot ) {
	
	$hrs = 0 + substr($timeSlot, 0, 2);
	$min = 0 + substr($timeSlot, -2);
	
	if ( $min == 30 ) {
		
		$hrs++;
		
		if ( $hrs < 10 ) {
			return $timeSlot . " - 0" . $hrs . ":00";
		} else {
			return $timeSlot . " - " . $hrs . ":00";
		}
	} else {
	
		if ( $hrs < 10 ) {
			return $timeSlot . " - 0" . $hrs . ":30";
		} else {
			return $timeSlot . " - " . $hrs . ":30";
		}
	}
}

/*
* Name: colNameToTimeSlot
* Desc: Converts column name (slot0800) to time slot (08:00)
* Inpt:	$timeSlot	-> Type: String, Value: [slot0800]
* Outp: Type: String, Value: time in format [hh:mm]
* Date: 24/09/2007
*/
function colNameToTimeSlot( $colName ) {
	
	$timeSlot = substr($colName, -4);
	$timeSlot = substr($timeSlot, 0, 2) . ':' . substr($timeSlot, -2);
	return $timeSlot;
}

/*
* Name: getDomain
* Desc: Determines the domain name used to store the COOKIES
* Inpt:	none
* Outp: Type: String, Value: domain name
* Date: 23/11/2007
*/
function getDomain() {

	if ( isset($_SERVER['HTTP_HOST']) ) {
		
		// Get domain
		$dom = $_SERVER['HTTP_HOST'];
		
		// Strip www from the domain
		if (strtolower(substr($dom, 0, 4)) == 'www.') { 
			$dom = substr($dom, 4);
		}
		
		// Check if a port is used, and if it is, strip that info
		$uses_port = strpos($dom, ':');
		if ($uses_port) {
			$dom = substr($dom, 0, $uses_port);
		}
		
		// Add period to Domain (to work with or without www and on subdomains)
		$dom = '.' . $dom;
	} else {
		$dom = false;
	}
	
	return $dom;  
}
?>
