var movieName = 'Save_User';


// This function sets up the reference to the Flash Object so that it is easily accessible by each browser type
// it also sets a value for the names in the case that the flash object fails to be properly set up.
var isIE = (navigator.appName.indexOf("Microsoft") != -1)
	function flashReady(event) {
		if (!event.success){
		    var names = 'no flash';
		}
		else {
		    var movieName = 'Save_User';
		  window.ref = event.ref;
		}
		 
	}

//This function is called by the Flash Object actionscript code when the Flash Object is fully loaded
// It collects the names from the LSO and puts them into a hidden element of the form on the signup page.
function readyToSave() {

		names = window.ref.getUsernames().toString();
		var myform = document.downform;		
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "data[User][prevNames]");
		hiddenField.setAttribute("value", names);

		document.downform.appendChild(hiddenField);
}

// This portion of the code was written by the Adobe project to use the functions of swfobject to create a Flash Object.
// The only portion of this that I wrote personally was the addition of the function flashReady as a parameter for embedSWF
// This makes it call the function flashReady as soon as it has finished attempting to create a Flash Object telling it whether
// or not it worked.
var getNames = function () {
            <!-- For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection. --> 
            var swfVersionStr = "10.0.0";
            <!-- To use express install, set to playerProductInstall.swf, otherwise the empty string. -->
            var xiSwfUrlStr = "";
            var flashvars = {};
            var params = {};
            params.quality = "high";
            params.bgcolor = "#ffffff";
            params.allowscriptaccess = "sameDomain";
            params.allowfullscreen = "true";
            var attributes = {};
            attributes.id = "Save_User";
            attributes.name = "Save_User";
            attributes.align = "middle";
            swfobject.embedSWF(
                "/static/Save_User.swf", "flashContent", 
                "1%", "1%", 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes, flashReady);
	<!-- JavaScript enabled so display the flashContent div in case it is not replaced with a swf object. -->
	swfobject.createCSS("#flashContent", "display:block;text-align:left;");


	var movieName = 'Save_User';				
	var isIE = (navigator.appName.indexOf("Microsoft") != -1);
}