// This file was made mostly from get_comp_usernames.js as a template.
// The main difference is that this file adds a name to the LSO rather
// than retrieve names.

var movieName = 'Save_User';
var isIE = (navigator.appName.indexOf("Microsoft") != -1);

// This function sets up the reference to the Flash Object so that it is easily accessible by each browser type.
function flashReady(event) {
	    if (event.success){
	    var isIE = (navigator.appName.indexOf("Microsoft") != -1);
	    window.ref = event.ref;
	    }

	}
//This function is called by the Flash Object actionscript code when the Flash Object is fully loaded
// It sends a name, set in the addName function, to the LSO 
function readyToSave() {
	window.ref.saveUsername(window.newname);
}

// This portion of the code was written by the Adobe project to use the functions of swfobject to create a Flash Object.
// The portions of this that I wrote personally were the addition of the function flashReady as a parameter for embedSWF, and 
// the seting of a username to the window.
// Adding flashReady as a parameter makes it call the function flashReady as soon as it has finished attempting to create a 
// Flash Object, also telling it whether or not it worked.
// This is the function to be called by the html page
// params - username: the username that should be added to the LSO 
var addName = function (username) {
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
	    window.newname = username;
            swfobject.embedSWF(
                "/static/Save_User.swf", "flashContent", 
                "1%", "1%", 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes, flashReady);
	    
	<!-- JavaScript enabled so display the flashContent div in case it is not replaced with a swf object. -->
	swfobject.createCSS("#flashContent", "display:block;text-align:left;");
	var username = "<?php = $this->data['User']['username']?>";				
}