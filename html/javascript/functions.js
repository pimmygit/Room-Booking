/* 
** Description:	Contains functions for user authentication and
**				account creation.
** @package:	security
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	27/09/2007
*/


/*
* Name: positionCursor
* Desc: Places the cursor in the first empty field
* Inpt:	none
* Outp: none
* Date: 23.11.2007
*/
function positionCursor() {
	
	var userField = document.getElementById("ibmUSER");
	var passField = document.getElementById("ibmPASS");
		
	if ( !userField.value ) {
		
		try {
			userField.select();
		} catch (e) {
			userField.focus();
		}
	} else {
		
		try {
			passField.select();
		} catch (e) {
			passField.focus();
		}
	}
}

/*
* Name: verifyFields
* Desc: Check if user supplied correct credentials
* Inpt:	none
* Outp: none
* Date: 27.09.2007
*/
function verifyFields() {
	
	var user = document.getElementById("ibmUSER").value;
	var pass = document.getElementById("ibmPASS").value;
		
	if ( !user || !verifyMailFormat(user) ) {
		alert("Please enter a valid IBM E-mail address.");
		return false;
	}
	
	if ( !pass ) {
		alert("Please enter your IBM Password.");
		return false;
	}
}

/*
* Name: checkUserData
* Desc: Check if user supplied correct data
* Inpt:	none
* Outp: none
* Date: 10.10.2007
*/
function checkUserData() {
	
	var userMail = document.getElementById("eMail").value;
	var userType = document.getElementById("isAdmin").checked;
		
	if ( !userMail || !verifyMailFormat(userMail) ) {
		alert("Please enter a valid IBM E-mail address.");
		return false;
	}
	
	if ( userType ) {
		var nsr=confirm("Are you sure you want to give administrative\nprivileges to this user:\n\n" + userMail);
		if ( nsr == true ) {
			return true;
		} else {
			return false;
		}
	}
}

/*
* Name: confirmDelete
* Desc: Confirms user deletion
* Inpt:	none
* Outp: none
* Date: 10.10.2007
*/
function confirmDelete() {

	var isSelected = false;
	for (var i=0; i < document.addUserForm.ibmUserList.length; i++) {

		if (document.addUserForm.ibmUserList[i].checked) {
			isSelected = true;
		}
   }
   
	if ( isSelected ) {
		return confirm("Are you sure you want to delete the selected users?");
	} else {
		alert("No users selected for deletion.");
		return false;
	}
}

/*
* Name: verifyMailFormat
* Desc: Check if user supplied E-mail addres in the correct format
* Inpt:	none
* Outp: none
* Date: 27.09.2007
*/
function verifyMailFormat(e_mail) {
	
	var emailRegxp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	
	if (emailRegxp.test(e_mail) != true) {
		return false;
	} else {
		return true;
	}
}

/*
* Name: lightOn
* Desc: Highlights the time column
* Inpt:	cellID	-> Type: String,	Value: ID of the cell to highlight
* Outp: none
* Date: 27.09.2007
*/
function lightOn(cellID) {
	
	document.getElementById(cellID).style.backgroundColor = '#87B4C0';
}

/*
* Name: lightOff
* Desc: Highlights the time column
* Inpt:	cellID	-> Type: String,	Value: ID of the cell to highlight
* Outp: none
* Date: 27.09.2007
*/
function lightOff(cellID) {
	
	document.getElementById(cellID).style.backgroundColor = '#BEC8D1';
}