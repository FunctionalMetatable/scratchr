jQuery.noConflict();

Ajax.Responders.register({
	onCreate: function() {
		var agt=navigator.userAgent.toLowerCase();
		var is_major = parseInt(navigator.appVersion);
		var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
		var is_ie6    = (is_ie && (is_major == 4) && (agt.indexOf("msie 6.")!=-1) );
		
		if (is_ie6) {
		} else {
			if($('ajax_indicator') && Ajax.activeRequestCount > 0)
				showNotification();
		}
	},
	onComplete: function() {
		var agt=navigator.userAgent.toLowerCase();
		var is_major = parseInt(navigator.appVersion);
		var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
		var is_ie6    = (is_ie && (is_major == 4) && (agt.indexOf("msie 6.")!=-1) );
		
		if (is_ie6) {
		} else {
			if($('ajax_indicator') && Ajax.activeRequestCount == 0)
				hideNotification();
		}
	}
});

function showNotification() {
	$('ajax_indicator').style.display = 'block';
}

function hideNotification() {
	$('ajax_indicator').style.display = 'none';
}