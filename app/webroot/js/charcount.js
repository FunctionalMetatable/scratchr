	Event.observe(window, 'load', init);

	function init(){
		makeItCount('pcomment_textarea',500);
		makeItCount('tcomment_textarea',500);
		/* this textarea doesn't exist in the demo, 
		 but you see adding in the init does not return an error */
	}
	
	function charCounter(id, maxlimit, limited){
		if (!$('counter-'+id)){
			$(id).insert({after: '<div id="counter-'+id+'"></div>'});
		}
		if($F(id).length >= maxlimit){
			if(limited){	$(id).value = $F(id).substring(0, maxlimit); }
			$('counter-'+id).addClassName('charcount-limit');
			$('counter-'+id).removeClassName('charcount-safe');
		} else {	
			$('counter-'+id).removeClassName('charcount-limit');
			$('counter-'+id).addClassName('charcount-safe');
		}
		$('counter-'+id).update( $F(id).length + '/' + maxlimit );	
			
	}
	
	function makeItCount(id, maxsize, limited){
		if(limited == null) limited = true;
		if ($(id)){
			Event.observe($(id), 'keyup', function(){charCounter(id, maxsize, limited);}, false);
			Event.observe($(id), 'keydown', function(){charCounter(id, maxsize, limited);}, false);
			charCounter(id,maxsize,limited);
		}
	}
	
	function makeItCountProject(comment_id, maxsize, limited) {
		if(limited == null) limited = true;
		var id = 'project_comment_reply_input_' + comment_id;
		var text_input = document.getElementById(id);
		
		if (text_input) {
			Event.observe(text_input, 'keyup', function(){charCounter(id, maxsize, limited);}, false);
			Event.observe(text_input, 'keydown', function(){charCounter(id, maxsize, limited);}, false);
			charCounter(id,maxsize,limited);
		}
	}
	
	function addProjectCharCounter(comment_id) {
		var id = 'project_comment_reply_input_' + comment_id;
		var text_input = document.getElementById(id);
		var maxsize = 500;
		var limited = true;
		
		if (text_input) {
			Event.observe(text_input, 'keyup', function(){charCounterPro(id, maxsize, limited);}, false);
			Event.observe(text_input, 'keydown', function(){charCounterPro(id, maxsize, limited);}, false);
			charCounterPro(id,maxsize,limited);
		}
	}
	
	function addGalleryCharCounter(comment_id) {
		var id = 'gallery_comment_reply_input_' + comment_id;
		var text_input = document.getElementById(id);
		var maxsize = 500;
		var limited = true;
		
		if (text_input) {
			Event.observe(text_input, 'keyup', function(){charCounterPro(id, maxsize, limited);}, false);
			Event.observe(text_input, 'keydown', function(){charCounterPro(id, maxsize, limited);}, false);
			charCounterPro(id,maxsize,limited);
		}
	}
	
	function insertAfter(parent, newElement, referenceElement){
		parent.insertBefore(newElement, referenceElement.nextSibling);
	}
	
	function charCounterPro(id, maxlimit, limited) {
		if (!$('counter-'+id)){
			var parent = $(id).parentNode;
			var child = $(id);
			var counterDiv = document.createElement('div');
			counterDiv.id = "counter-"+id;
			insertAfter(parent, counterDiv, child);
		}
		
		if($F(id).length >= maxlimit){
			if(limited){	$(id).value = $F(id).substring(0, maxlimit); }
			$('counter-'+id).addClassName('charcount-limit');
			$('counter-'+id).removeClassName('charcount-safe');
		} else {	
			$('counter-'+id).removeClassName('charcount-limit');
			$('counter-'+id).addClassName('charcount-safe');
		}
		
		var targetElement = $("counter-"+id);
		var newHTML =  $F(id).length + '/' + maxlimit;
		setHTML(targetElement, newHTML);
	}
	
	function setHTML(targetElement, newHTML) {
		if (targetElement.firstChild)
			targetElement.removeChild(targetElement.firstChild);

		targetElement.appendChild(
			document.createTextNode(newHTML)
		);
	}

