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
	function openJS(){alert('loaded')}
	function closeJS(){alert('closed')}
	TINY.box.show({html:'Do you want to take a survey?', boxid:'frameless', fixed:false, maskid:'bluemask', maskopacity:40, closejs:function(){closeJS()}});
</script>