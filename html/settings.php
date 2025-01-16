<?php
/* 
** Description:	Contains the properies for the operation of the tool
** @package:	Config
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	25/09/2007
** @modified:	30/12/2015
*/

/***************************************************
* Site Administrator
***************************************************/
define('SITE_ADMIN_NAME', 'Kliment Stefanov');
define('SITE_ADMIN_MAIL', 'stefanov@uk.ibm.com');

/***************************************************
* Room Booking Administrator
***************************************************/
define('ROOM_ADMIN_NAME', 'Marie Miller');
define('ROOM_ADMIN_MAIL', 'MFEMILLER@uk.ibm.com');

/***************************************************
* LDAP Server settings
***************************************************/
define('LDAP_HOST', 'bluepages.ibm.com');						# Full host name of the LDAP server
define('LDAP_PORT', '389');										# Port to connect to the LDAP server							
define('LDAP_OU', 'bluepages');									# Organizational Unit
define('LDAP_O', 'ibm.com');									# Organization

/***************************************************
* MySQL database settings
***************************************************/
define('DB_HOST', 'us-cdbr-iron-east-03.cleardb.net');						# Host name of the database server ("localhost" if on the same server)
define('DB_PORT', '3306');									# Port to connect to the database server
define('DB_NAME', 'ad_eb8b3e9d3ab1007');							# Name of the database which contains L10N tables
define('DB_USER', 'b315a6b8f824d0');								# Username to connect as to the database
define('DB_PASS', 'e7884e13');									# Password to authenticate the user
define('TB_USER', 'users');									# Table containing all users allowed to access the booking form and their permissions
define('TB_ROOM', 'rooms');									# Table containing all projects and their properties

/***************************************************
* Log setting
***************************************************/
define('LOG_TABLE', 'historylog');								# Name of the table in the DB for history logging
define('LOG_FILE', '../logs/sbrooms_system.log');		# Location of the log file
define('LOG_LEVEL', 'info');									# Log level: info, warn, error

/***************************************************
* CONSTANTS - DO NOT EDIT
***************************************************/

// Timeslots for the booking form
$timeSlots = array( '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
					'14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30' );
					
// Function return values
define ('COMMAND_OK', 1);
define ('CONNECT_FAILED', 0);
define ('SQL_FAILED', -1);
define ('WRONG_PASSWORD', -2);
define ('DOES_NOT_EXIST', -3);
define ('ALREADY_EXIST', -4);
define ('COMMAND_FAIL', -5);
define ('BAD_REQUEST', -6);
define ('UNKNOWN_ERROR', -7);

// Message levels
define ('INFO', 'info');
define ('WARN', 'warn');
define ('ERROR', 'error');

//Notifications
define ('SILENT', false);
define ('NOTIFY', true);
?>
