<?php
/* 
* @package:		Security
* @subpackage:	Initial User Creation
* @author:		Kliment Stefanov <stefanov@uk.ibm.com>
*/
include_once('settings.php');
include_once('utils/logger.php');
include_once('utils/LoginLDAP.Class.php');

// Get a file into an array.  In this example we'll go through HTTP to get
// the HTML source of a URL.
$lines = file('userList.txt');

// Loop through our array, show HTML source as HTML source; and line numbers too.
foreach ($lines as $line_num => $line) {

    echo "Adding <b>" . substr($line, 0, -14) . "</b>";
	
	// Connect to the LDAP server
	$ldapconn = ldap_connect(LDAP_HOST, LDAP_PORT)
		or die("Could not connect to LDAP server.");
	// Set LDAP protocol version to be used
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); 
	
	if ($ldapconn) {
	   // Anonymous binding to ldap server
	   $ldapbind = ldap_bind($ldapconn); //, $ldaprdn, $ldappass);
	
	   // Verify binding
	   if (!$ldapbind) {
		   echo "LDAP bind failed..";
		   exit();
	   }
	}
	
	$dn = "o=".LDAP_O;
	$filter = "(&(cn=".substr($line, 0, -14)."*)(objectclass=person))";
		
		if (($res_id = ldap_search( $ldapconn, $dn, $filter)) == false) {
							   
			 echo "</b><br />\nSearch in LDAP-tree failed. Please contact system administrator.";
			 exit();
		}
		
		$info = ldap_get_entries($ldapconn,$res_id);

		if( $info["count"] > 1) {
			echo " - found [" . $info["count"] . "] entries!<br />\n";
			continue;
		}

		if( $info["count"] < 1) {
			echo " - not found in ".LDAP_HOST."!<br />\n";
			continue;
		}
		
		echo " - Name: [" . trim($info[0]["givenname"][0]." ".$info[0]["sn"][0]) . "], Mail: [" . $info[0]["mail"][0] . "]!<br />\n";
		
		authorizeUser(trim($info[0]["givenname"][0]." ".$info[0]["sn"][0]), $info[0]["mail"][0], 'users');
}



?>