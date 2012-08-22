<!-- Displays Welcome to Scratch Community message for new accounts. Rendered by /app/views/users/myscratchr.thtml -->

<!-- Displays Welcome to Scratch Community message for new accounts.
     Rendered by /app/views/users/myscratchr.thtml -->
<style type='text/css'>
    #wb_c {
        border-left: 1px solid #d1d1d1;
        border-right: 1px solid #d1d1d1;
        width: 99.8%;
    }
    #w_caption {
        background-position: center bottom;
        background-image: url(/img/bg_flip.png);
        height: 60px;
    }
    #welcome_text {
        padding-left: 8px;
    }
    .tb_w {
        text-align: center;
        margin: 5px;
        margin-left: 19px;
        background-repeat: no-repeat;
        background-position: center top;
        background-size: 100%;
        border-radius: 10px;
        width: 148px;
        height: 130px;
        position: relative;
    }
    .tb_w:hover {
        margin: 0px;
        margin-left: 15px;
        margin-right: 1px;
        margin-top: 1px;
        border-radius: 10px;
    }
    #wb_c a {
        text-decoration: none;
    }
    
    .tb_w h3 {
      position: absolute;
      bottom: -2px;
      left: 5%;
      right: 5%;
    }
    
    #wb1 {
        background-image: url(/img/welcome_getstarted.png);
        border: 1px solid #009cb5;
    }
    
    #wb2 {
        background-image: url(/img/welcome_explore.png);
        border: 1px solid #11ba10;
    }
    
    #wb3 {
        background-image: url(/img/welcome_connect.png);
        border: 1px solid #de0101;
    }
    
    #wb1:hover {
        border: 5px solid #009cb5;
    }
    #wb2:hover {
        border: 5px solid #11ba10;
    }
    #wb3:hover {
        border: 5px solid #de0101;
    }
    #wb1 h3 {
        color: #009cb5 !important;
    }
    #wb2 h3 {
        color: #11ba10 !important;
    }
    #wb3 h3 {
        color: #de0101 !important;
    }
</style>
<div class='mystuff_container'>
        <div class='mystuff_header'>
                <h3><?php e(__('Welcome to the Scratch Community!')); ?></h3>
        </div>
        <div id='wb_c' class='mystuff_content'>
            <a href='<?php echo INFO_URL ?>/Support/Get_Started'>
              <div class='thumb tb_w' id='wb1'>
                      <h3><?php e(__('GET STARTED')); ?></h3>
              </div>
            </a>
            <a href='/channel/featured'>
              <div class='thumb tb_w' id='wb2'>
                      <h3><?php e(__('EXPLORE')); ?></h3>
              </div>
            </a>
	       <a href='<?php echo $wcProjectURL ?>'>
               <div class='thumb tb_w' id='wb3'>
                      <h3><?php e(__('CONNECT')); ?></h3>
              </div>
            </a>
	</div>

	<div class='mystuff_container' id='w_caption'>
		<h3><div id='welcome_text'>
		    <?php e(__('How would you like to begin?')); ?>
		</div></h3>

	</div>
</div>
<script type='text/javascript'>
function changeText(trigger) {
	text = '<?php e(__('How would you like to begin?')); ?>';
	cl = '#000';
	if(trigger == 'wb1')
	{
		text = '<?php e(__('Learn how you can install Scratch, create projects, and participate in the community.')); ?>';
		cl = '#009cb5';
	}
	else if(trigger == 'wb2')
	{
		text = '<?php e(__('Browse projects recently featured on the Scratch homepage.')); ?>';
		cl = '#11ba10';
	}
	else if(trigger == 'wb3')
	{
		text = '<?php e(__('Check out a welcome project made by a member of the Scratch community.')); ?>';
		cl = '#de0101';
	}
	$('welcome_text').setStyle({color: cl});
	$('welcome_text').innerHTML = text;
}

Event.observe('wb1', 'mouseover', function() { changeText('wb1') })
Event.observe('wb2', 'mouseover', function() { changeText('wb2') })
Event.observe('wb3', 'mouseover', function() { changeText('wb3') })
function leaveCheck(ev) { return ev.toElement != $('wb1') && ev.toElement != $('wb2') && ev.toElement != $('wb3') && ev.toElement != $('wb_c'); }
Event.observe('wb1', 'mouseout', function(ev) { if(leaveCheck(ev)) { changeText(''); } else { Event.stop(ev); return false } })
Event.observe('wb2', 'mouseout', function(ev) { if(leaveCheck(ev)) { changeText(''); } else { Event.stop(ev); return false } })
Event.observe('wb3', 'mouseout', function(ev) { if(leaveCheck(ev)) { changeText(''); } else { Event.stop(ev); return false } })
Event.observe('wb_c', 'mouseout', function(ev) { changeText('') })
</script>
