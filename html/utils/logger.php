<?php
/* 
** Description:	Contains functions for producing log information
**
** @package:	utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	27/09/2007
*/

/*
* Name: msg_log
* Desc: Logs information to a file (filename specified in 'config/settings.php')
* Inpt:	msg_level		-> Type: String, Value: ERROR, WARN, INFO
*		message			-> Type: String, Value: Description of the event
*		notifyUser		-> Type: Boolean, Value: [NOTIFY||SILENT]||[TRUE||FALSE]
* Outp: Type: Boolean	-> TRUE on success, FALSE otherwise.
* Date: 27.09.2007
*/
function msg_log($msglevel, $message, $notifyUser) {
	
	$timestamp = date('d/m/Y H:i:s');
	
	// If for any reason the user ID fails to get registered (unlikely),
	// then at least try to get the IP address from where he connected.
	if ( isset($_SESSION['userMail']) ) {
		$loggedUser = $_SESSION['userMail'];
	} else if ( !empty($_SERVER['REMOTE_ADDR']) ) {
		$loggedUser = $_SERVER['REMOTE_ADDR'];
	} else {
		$loggedUser = 'UNKNOWN';
	}
	
	switch(LOG_LEVEL) {
	
		case 'info':
		case 'INFO':
		
			if ( $msglevel === 'info' || $msglevel === 'INFO' ) {
				error_log('['.$timestamp.'] ['.$loggedUser.'] INFO: '.$message.PHP_EOL, 3, LOG_FILE);
			}
			if ( $msglevel === 'warn' || $msglevel === 'WARN' ) {
				error_log('['.$timestamp.'] ['.$loggedUser.'] WARN: '.$message.PHP_EOL, 3, LOG_FILE);
			}
			if ( $msglevel === 'error' || $msglevel === 'ERROR' ) {
				error_log('['.$timestamp.'] ['.$loggedUser.'] ERROR: '.$message.PHP_EOL, 3, LOG_FILE);
			}
							
			if ($notifyUser === true) {
				header("Location: utils/message.php?sevr=$msglevel&msg=$message");
				exit();
			}
					
			break;
		
		case 'warn':
		case 'WARN':
			
			if ( $msglevel === 'warn' || $msglevel === 'WARN' ) {
				error_log('['.$timestamp.'] ['.$loggedUser.'] WARN: '.$message.PHP_EOL, 3, LOG_FILE);
			}
			if ( $msglevel === 'error' || $msglevel === 'ERROR' ) {
				error_log('['.$timestamp.'] ['.$loggedUser.'] ERROR: '.$message.PHP_EOL, 3, LOG_FILE);
			}
							
			if ($notifyUser === true) {
				header("Location: utils/message.php?sevr=$msglevel&msg=$message");
				exit();
			}

			break;
			
		case 'error':
		case 'ERROR':
			
			if ( $msglevel === 'error' || $msglevel === 'ERROR' ) {
				error_log('['.$timestamp.'] ['.$loggedUser.'] ERROR: '.$message.PHP_EOL, 3, LOG_FILE);
			}
							
			if ($notifyUser === true) {
				header("Location: utils/message.php?sevr=$msglevel&msg=$message");
				exit();
			}

			break;
		
		default:
			
			error_log('['.$timestamp.'] ['.$loggedUser.'] ERROR: Logging level not defined.'.PHP_EOL, 3, LOG_FILE);
			break;
	}
}
?>