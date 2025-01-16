/* 
** Description:	Contains functions for various actons requested by user selection (mouse clicks)
** @package:	utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	26/09/2007
*/

/*
* Name: bookRoom
* Desc: Books the room for the entire day if no other bookings are available
* Inpt:	roomName	-> Type: String,	Value: Name of the room to be booked
*		day			-> Type: INT,		Value: Time slot for the booking
*		userName	-> Type: String,	Value: Name of the Current user logged in
* Outp: none
* Date: 01/10/2007
*/
function msgTooLate() {
	alert("Rooms can be booked max 5 min after the time slot has started.\n" +
		  "After that rooms are available on 'First-come, first-serve' basis.\n" +
		  ".\n" +
		  ".\n" +
		  "Should you find this message too annoying, please send me an E-mail: stefanov@uk.ibm.com\n" +
		  "and once I get more than 20 complaints, I will remove it. Cheers.");
}
	
/*
* Name: bookRoom
* Desc: Books the room for the entire day if no other bookings are available
* Inpt:	roomName	-> Type: String,	Value: Name of the room to be booked
*		day			-> Type: INT,		Value: Time slot for the booking
*		userName	-> Type: String,	Value: Name of the Current user logged in
* Outp: none
* Date: 01/10/2007
*/
function bookRoomForDay(roomName, day, userName) {
	
// Timeslots for the booking form (morrored from the DB
	var timeSlots = new Array(	'slot0800', 'slot0830', 'slot0900', 'slot0930', 'slot1000', 'slot1030', 'slot1100', 'slot1130', 'slot1200', 'slot1230', 'slot1300', 'slot1330',
								'slot1400', 'slot1430', 'slot1500', 'slot1530', 'slot1600', 'slot1630', 'slot1700', 'slot1730', 'slot1800', 'slot1830', 'slot1900', 'slot1930' );
	
	// Small routine to determine the type of the action:
	// If the majority of the cells are not booked then we book all,
	// otherwise we unbook all.
	var numChecked = 0;
	var mid = (timeSlots.length - 1) / 2;
	
	for ( slot in timeSlots ) {
		
		var cellID = roomName + '_' + timeSlots[slot];
		var cell = document.getElementById(cellID);
						
		// Create the cell value by the userName
		var cellValue = '<span>' + userName + '</span>';
		if ( (cell.innerHTML.toLowerCase() != '<span>&nbsp;</span>') && (cell.innerHTML.toLowerCase() != cellValue.toLowerCase()) ) {
			alert("There are already bookings for that day");
			return false;
		} else {
		
			if ( cell.innerHTML.toLowerCase() == cellValue.toLowerCase() )
				numChecked++;
		}
	}
		
	for ( slot in timeSlots ) {
		
		var cellID = roomName + '_' + timeSlots[slot];
		var cell = document.getElementById(cellID);
		
		if (numChecked <= mid) {
			// Book the room
			//alert("Room: " + roomName + ", Timeslot: " + timeSlots[slot] + ", Day: " + day + ", User: " + userName);
			dbBookRoom(roomName, timeSlots[slot], day, userName, true);
			// Change the status of the cell
			cell.style.backgroundColor = '#00FF66';
			cell.innerHTML = '<span>' + userName + '</span>';
		} else {
			// Room has been booked by this user already, so remove the booking
			//alert("Room: " + roomName + ", Timeslot: " + timeSlots[slot] + ", Day: " + day + ", User: " + userName);
			dbBookRoom(roomName, timeSlots[slot], day, userName, false);
			// Change the status of the cell
			cell.style.backgroundColor = '#E0FFFF';
			cell.innerHTML = '<span>&nbsp;</span>';
		}
	}
}

/*
* Name: bookRoom
* Desc: Modifies the cell status and books the room
* Inpt:	cellID		-> Type: String,	Value: ID of the clicked cell
*		roomName	-> Type: String,	Value: Name of the room to be booked
*		timeSlot	-> Type: String,	Value: Time slot for the booking
*		day			-> Type: INT,		Value: Time slot for the booking
*		userName	-> Type: String,	Value: Name of the Current user logged in
* Outp: none
* Date: 26/09/2007
*/
function bookRoom(cellID, roomName, timeSlot, day, userName, accType) {
	
	var cell = document.getElementById(cellID);
	
	//alert("User: [" + userName + "] is booking cell: [" + cellID + "], with value: [" + cell.innerHTML + "], from acc type: ["+ accType +"].");
	// If the slot is empty
	if ( cell.innerHTML.toLowerCase() == '<span>&nbsp;</span>' ) {
		
		// The admin can make bookings for someone else
		if (accType == 'admin') {
		
			otherUser = prompt("If you want to book this slot for someone else,\n\nplease enter their name and press 'OK'.");

			if (otherUser != null && otherUser.length >= 1) {
				userName = otherUser;
			}
		}
		
		// Book the room
		dbBookRoom(roomName, timeSlot, day, userName, true);
		// Change the status of the cell
		cell.style.backgroundColor = '#00FF66';
		cell.innerHTML = '<span>' + unescape(userName).replace(/\+/, ' ') + '</span>';
	} else {
		
		// Create the cell value by the userName
		var cellValue = '<span>' + unescape(userName).replace(/\+/, ' ') + '</span>';
		
		if ( (accType == 'admin') && (cell.innerHTML.toLowerCase() != cellValue.toLowerCase()) ) {
			
			var proceed = confirm("You are cancelling someones booking !!!\n\nDo you want to proceed?\n\n");
			
			if (proceed==true) {

				dbBookRoom(roomName, timeSlot, day, userName, false);
				// Change the status of the cell
				cell.style.backgroundColor = '#E0FFFF';
				cell.innerHTML = '<span>&nbsp;</span>';
			}
		} else {
		
			// Check if the room has been booked by someone else
			if ( cell.innerHTML.toLowerCase() == cellValue.toLowerCase() ) {
			
				// Room has been booked by this user already, so remove the booking
				dbBookRoom(roomName, timeSlot, day, userName, false);
				// Change the status of the cell
				cell.style.backgroundColor = '#E0FFFF';
				cell.innerHTML = '<span>&nbsp;</span>';
			}
		}
	}
}

/*
* Name: dbBookRoom
* Desc: Creates the XML request to book the room
* Inpt:	roomName	-> Type: String,	Value: Name of the room to be booked
*		timeSlot	-> Type: String,	Value: Time slot for the booking
*		day			-> Type: INT,		Value: Time slot for the booking
*		action		-> Type: Boolean,	Value: if TRUE room is booked, on FALSE booking is cancelled
* Outp: none
* Date: 26/09/2007
*/
function dbBookRoom(roomName, timeSlot, day, userName, action) {

	var param = 'action=bookRoom&room=' + roomName + '&slot=' + timeSlot + '&day=' + day + '&name=' + userName + '&act=' + action;
	
	sendXmlHttpRequest(param);
}