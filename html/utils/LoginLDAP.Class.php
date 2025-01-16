<?php
/* 
** Description:	Authentication against LDAP server
**
** @package:	Security
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	24/09/2007
*/
require_once('Login.interface.php');
require_once('mysql.php');

Class LoginLDAP implements Login {

	private $ldapconn;
	private $manInfo;
	private $info;

	function __construct() {
		
		// Connect to the LDAP server
		$this->ldapconn = ldap_connect(LDAP_HOST, LDAP_PORT)
			or die("Could not connect to LDAP server.");
		// Set LDAP protocol version to be used
		ldap_set_option($this->ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); 
		
		if ($this->ldapconn) {
		   // Anonymous binding to ldap server
		   $ldapbind = ldap_bind($this->ldapconn); //, $ldaprdn, $ldappass);
		
		   // Verify binding
		   if (!$ldapbind) {
			   msg_log(ERROR, "LDAP bind failed..", NOTIFY);
			   exit();
		   }
		}
	}

	function isValidUser($username) {
	
		$dn = "o=".LDAP_O;
		$filter = "(&(mail=$username*)(objectclass=person))";
		
		if (($res_id = ldap_search( $this->ldapconn, $dn, $filter)) == false) {
							   
			 msg_log(ERROR, "Search in LDAP-tree failed. Please contact system administrator", NOTIFY);
			 return false;
		}
		
		$this->info = ldap_get_entries($this->ldapconn,$res_id);

		if( $this->info["count"] > 1) {
			msg_log(ERROR, "Found [" . $this->info["count"] . "] entries for [" . $username . "]!", NOTIFY);
			return false;
		}

		if( $this->info["count"] < 1) {
			msg_log(INFO, "User [" . $username . "] not found in ".LDAP_HOST."!", SILENT);
			return false;
		}
		
		return true;		
	}
	
	function isValidPassword($password) {
		
		if ( !empty($this->info[0]["dn"]) ) {
			
			$user_dn = $this->info[0]["dn"];
			$bind = @ldap_bind($this->ldapconn, $user_dn, $password);
			
			if(!$bind) {
				return false;	
			}
					
			return true;
		} else {
			return false;
		}
	}
	
	function hasAccess($email) {//	return false;
	
		return isAuthorized($email);
	}
	
	function getUserName() {
	
		if ( !empty($this->info[0]["dn"]) ) {
			return trim($this->info[0]["givenname"][0]." ".$this->info[0]["sn"][0]);
		} else {
			return 'Unknown';
		}
	}
	
	function getAccType($email) {
	
		return getAccountType($email);
	}
}
?>