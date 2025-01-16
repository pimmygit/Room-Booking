<?php
/* 
** Description:	Login interface
**
** @package:	Security
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	24/09/2007
*/
interface Login {

	// Check if the user exists in the LDAP directory
	function isValidUser($username);
	// Check if the user supplied the correct pasword
	function isValidPassword($password);
	// Check if the user has aces o the requested resourse
	function hasAccess($username);
	// Get the name of the user logged in
	function getUserName();
}
?>