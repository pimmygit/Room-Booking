<?php
/* 
** Description:	Data Object representing a list of available
**				for booking rooms, and their properties.
** @package:	Utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	25/09/2007
*/
Class Rooms
{
	// Pointer
	private $ptr = 0;
	// Array containing rooms
	private $rooms = array();
   
	// Class constructor
    public function __construct($anArray=false) {
	
		if($anArray != false) {
		
			if(is_array($anArray)) {
			
				$this->rooms[] = $anArray;
			}
		}
	}
   
	/*
	* Name: addRoom
	* Desc: Pushes an element (room properties) to the object
	* Inpt:	$roomName			-> Type: String,	Value: Name of the room
			$roomCapacity		-> Type: INT,		Value: Max number of people
			$roomLocation		-> Type: String,	Value: max 30 chars; Location within the building
			$roomDescription	-> Type: String,	Value: max 90 chars; Description of the room
	* Outp: 					-> Type: INT,		Value: Number of elements in the object
	* Date: 25.09.2007
	*/
	public function dbGetRooms() {
		
		$sqlResponse = getRoomsFromDB();
		
		if ($sqlResponse) {
			
			while ($userData = mysqli_fetch_array($sqlResponse, MYSQLI_ASSOC)) {
				//error_log("Room: [".$userData["name"]."], [".$userData["number"]."], [".$userData["capacity"]."], [".$userData["location"]."], [".$userData["description"]."]");
				// Populate the array with room names
				$this->addRoom($userData["name"], $userData["number"], $userData["capacity"], $userData["location"], $userData["description"]);
			}
		}
	}

	/*
	* Name: addRoom
	* Desc: Pushes an element (room properties) to the object
	* Inpt:	$roomName			-> Type: String,	Value: Name of the room
			$roomNumber			-> Type: String,	Value: Nunmber of the room
			$roomCapacity		-> Type: INT,		Value: Max number of people
			$roomLocation		-> Type: String,	Value: max 30 chars; Location within the building
			$roomDescription	-> Type: String,	Value: max 90 chars; Description of the room
	* Outp: 					-> Type: INT,		Value: Number of elements in the object
	* Date: 25.09.2007
	*/
	public function addRoom($roomName, $roomNumber, $roomCapacity, $roomLocation, $roomDescription)
    {
		// properties for each room
		$roomProps = array();
		$roomProps['name'] = $roomName;
		$roomProps['number'] = $roomNumber;
		$roomProps['capacity'] = $roomCapacity;
		$roomProps['location'] = $roomLocation;
		$roomProps['description'] = $roomDescription;

		if($this->getNumRows() > 0) {
			return array_push($this->rooms, $roomProps);
		} else {
			return $this->rooms[] = $roomProps;
		}
	}
   
	/*
	* Name: getRoom
	* Desc: Fetches the data under the pointer
	* Inpt:	none
	* Outp: Type: Array, Value: Array of room properties
	* Date: 25.09.2007
	*/
	public function getRoom() {
	
		if(isset($this->rooms[$this->ptr]))
			return $this->rooms[$this->ptr++];
		
		return false;
	}
	
	/*
	* Name: moveNext
	* Desc: Moves to the next element in the data object
	* Inpt:	none
	* Outp: Type: boolean
	* Date: 25.09.2007
	*/
	public function moveNext() {
		
		$newPtr = $this->ptr + 1;
		
		if(isset($this->rooms[$newPtr])) {
			
			$this->ptr = $newPtr;
			return true;
		}
		
		return false;
	}
	
	/*
	* Name: movePrevious
	* Desc: Moves to the previous element in the data object
	* Inpt:	none
	* Outp: Type: boolean
	* Date: 25.09.2007
	*/
	public function movePrevious() {
		
		$newPtr = $this->ptr - 1;
		
		if(isset($this->rooms[$newPtr]))
            return $this->rooms[$newPtr];

		return false;
	}

	/*
	* Name: getNumRows
	* Desc: Counts the number of elements (rooms)
	* Inpt:	none
	* Outp: Type: INT, Value: Number of elements in the object
	* Date: 25.09.2007
	*/
	public function getNumRows() {
		return count($this->rooms);
	}

	/*
	* Name: resetPointer
	* Desc: Resets the pointer to the beginning of the array
	* Inpt:	none
	* Outp: none
	* Date: 26.09.2007
	*/
	public function resetPointer() {
		$this->ptr = 0;
	}
}
?>