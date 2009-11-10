//if(jQuery !== 'undefined') {
    jQuery.noConflict();
//}

Ajax.Responders.register({
  onCreate: function() {
    $('ajax_indicator').setStyle({'visibility': 'visible'});
  },
  onComplete: function() {
    $('ajax_indicator').setStyle({'visibility': 'hidden'});
  },
  onException: function() {
	$('ajax_indicator').setStyle({'visibility': 'hidden'});
  }
});
