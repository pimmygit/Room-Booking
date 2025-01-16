/* 
** Description:	Contains functions for producing log information
**				related to the L10N tool operation.
** @package:	utils
** @author:		Kliment Stefanov <stefanov@uk.ibm.com>
** @created:	14/09/2007
*/


/*
* Name: getXmlHttpRequest
* Desc: Creates the XML request object depending on the browser type
* Inpt:	none
* Outp: XMLHTTPRequest
* Date: 14/09/2007
*/
function getXmlHttpRequest() {

	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest(  );
		//xmlhttp.overrideMimeType('text/xml');
		return xmlhttp;
	} else {
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			return xmlhttp;
		} catch (e)	{
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				return xmlhttp;
			} catch (e) {
				return false;
			}
		}
	}
}

/*
* Name: sendXmlHttpRequest
* Desc: Sends the XML HTTP request to the php functions
* Inpt:	param	-> Type: String, Value: parameters to be send
*		src		-> Type: String, Value: source of the caller
* Outp: none
* Date: 14/09/2007
*/
function sendXmlHttpRequest(param) {

	var url = 'utils/dbBookRoom.php';
	var xmlhttp = getXmlHttpRequest();
	
	//xmlhttp.overrideMimeType('text/xml');
	url = url + '?' + param;
	//alert(url);
	xmlhttp.open('GET', url, true);
	xmlhttp.onreadystatechange = function() { //Call a function when the state changes.
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			//alert("XML HTTP Response: (" +xmlhttp.responseText+ ")");
			switch (xmlhttp.responseText) {
			
				case '-7':
					alert("Unknown server error.");
					break;
				case '-6':
					alert("Bad XML HTTP Request.");
					break;
				case '-5':
					alert("Internal server error.");
					break;
				case '-4':
					alert("This device already exist in the list.");
					break;
				case '-3':
					alert("User does not exist.");
					break;
				case '-2':
					alert("Wrong password.");
					break;
				case '-1':
					alert("Database error.");
					break;
				case  '0':
					alert("Failed to connect to database.");
					break;
				case '1':
					// Command executed successfully
					// Depending on the command, execute table manipulation

					break;
					
				default:
					alert("Illegal operation: [" + xmlhttp.responseText + "]");
					break;
			}
		}
	};
	xmlhttp.send('');
}