// apply inline-box only for mozilla
if( jQuery.browser.mozilla ) {
	// do when DOM is ready
	$( function() {
		// search form, hide it, search labels to modify, filter classes nocmx and error
		$( 'form.niceform' ).hide().find( 'p>label:not(.nocmx):not(.error)' ).each( function() {
			var $this = $(this);
			var labelContent = $this.html();
			var labelWidth = document.defaultView.getComputedStyle( this, '' ).getPropertyValue( 'width' );
			// create block element with width of label
			var labelSpan = $("<span>")
				.css("display", "inline")
				.width(labelWidth)
				.html(labelContent);
			// change display to mozilla specific inline-box
			$this.css("display", "-moz-inline-box")
				// remove children
				.empty()
				// add span element
				.append(labelSpan);
		// show form again
		}).end().show();
	});
};


function showLogin(){
	$('#logincontainer').animate({opacity: 'toggle'}, 'slow');
	return false;
}