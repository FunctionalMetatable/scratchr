<style>
	.tbox {position:absolute; display:none; padding:14px 17px; z-index:900}
	.tinner {padding:15px; -moz-border-radius:5px; border-radius:5px; background:#fff url(../img/preload.gif) no-repeat 50% 50%; border-right:1px solid #333; border-bottom:1px solid #333}
	.tmask {position:absolute; display:none; top:0px; left:0px; height:100%; width:100%; background:#000; z-index:800}
	.tclose {position:absolute; top:0px; right:0px; width:30px; height:30px; cursor:pointer; background:url(../img/close.png) no-repeat}
	.tclose:hover {background-position:0 -30px}
	#bluemask {background:#4195aa}
</style>
<?php echo $javascript->link('tinybox.js'); ?>
<script type="text/javascript">
var Survey = {
	key: null,
	url: null,
	init : function(key, url) {
		Survey.key = key;
		Survey.url = url;
	},
	done : function() {
		Survey.take();
	},
	cancel : function() {
		Survey.take();
	},
	take: function() {
		setCookie(Survey.key, 'done', 365*2);
	},
	didTake: function() {
		if(readCookie(Survey.key)) {
			return false;
		}
		return false;
	},
	show : function() {
		if(Survey.didTake()) {
			return false;
		}
		var htmlText = '<div style="font-size: 16px;">Do you want to take a survey?</div><br><input value="Okay" type="button" onclick="window.open(\'' + Survey.url + '\');"><input value="Cancel" style="margin-left: 10px;" type="button" onclick="TINY.box.hide();">';
		TINY.box.show( { html : htmlText, boxid : 'frameless', fixed : false, maskid : 'bluemask', maskopacity : 40,
						 width: 240, height: 65, closejs : function() { Survey.cancel() }
		});
	}
}

Survey.init('k1', 'http://google.com');
Survey.show();
</script>