//the div which contains the login form
var toggled = false;

function showLogin() {
    if(toggled) { //visible
        $('#logincontainer').fadeOut('medium', null)
        toggled = false;
    }
    else {
        $('#logincontainer').fadeIn('slow',
                function() { $('#UserInput').focus() } );
        toggled = true;
    }
    return false;
}


function hideUserCountryDiv(fast) {
	

	var userCountryDiv = document.getElementById("userCountryDiv");
	if(typeof(fast) !== 'undefined') {
		$('#userCountryDiv').hide();
	}
	else {
		$('#userCountryDiv').fadeOut('medium', null);
	}
	setCookie('country_welcomed','1', 365);
	return false;
}

function setCookie(name,value,days) {
	if (days) 
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*3600000));
		var expires = "; expires="+date.toGMTString();
	}
	else 
		var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var prefix = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) 
	{
		var c = ca[i];
		while (c.charAt(0)==' ') 
			c = c.substring(1,c.length);
		if (c.indexOf(prefix) == 0) 
			return c.substring(prefix.length,c.length);
	}
	return null;
}

