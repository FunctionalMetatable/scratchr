jQuery.noConflict();

Ajax.Responders.register({
  onCreate: function() {
    Ajax.activeRequestCount++;
	$('ajax_indicator').setStyle({'visibility': 'visible'});
  },
  onComplete: function() {
    Ajax.activeRequestCount--;
	if(Ajax.activeRequestCount==0) {
		$('ajax_indicator').setStyle({'visibility': 'hidden'});
	}
  }
});
