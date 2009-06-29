<?php $head->register_jsblock("
	function init() {
		document.getElementById('WhitelistedIpAddressContactName').focus();
	}
	window.onload = init;
");
?>

<!-- |||||| Begin main content area ||||||| -->
<div id="main">
<div class="container" id="loginForm">
  <form action="<?php echo $html->url('/users/whitelistip')?>" method="POST" accept-charset="UTF-8">
  <div class="login_header">
    <h4><?php ___('Request for allowing multiple accounts from the same IP address'); ?></h4>
  </div>
	<?php
			echo "<div id='success_msg'>";
				if ($session->check('Message.flash')):
						$session->flash();
				endif;
				echo "</div>";
			
		?>
<!-- testing //-->	
	<div id="send_request">
		<div class="login_row">
			 <label for="User" ><?php ___('IP Address'); ?>&nbsp;:</label>
			<?php echo $form->input('WhitelistedIpAddress.ipaddress',array('size'=>28,'value'=>$client_ip,'style'=>'font-size:14px','label'=>''))?>	
		</div>
		<div class="login_row">
		 	<label for="User" ><?php ___('Contact name'); ?>&nbsp;:</label>
			<?php echo $form->input('WhitelistedIpAddress.contact_name',array('size'=>28,'label'=>''))?>	
		</div>
		
		<div class="login_row">
		<label for="User" ><?php ___('E-mail'); ?>&nbsp;:</label>
		<?php echo $form->input('WhitelistedIpAddress.email',array('size'=>28,'label'=>''))?>	
		</div>
		
		<div class="login_row">
		<label for="User" ><?php ___("School or organization's name"); ?>&nbsp;:</label>
		<?php echo $form->input('WhitelistedIpAddress.school_name',array('size'=>28,'label'=>''))?>	
		</div>
		<div class="login_row">
		 <label for="User" ><?php ___('Comments'); ?>&nbsp;:</label>
		 <?php echo $form->input('WhitelistedIpAddress.comments',array('label'=>''))?>	
		
		
		</div>
		<div class="login_row">
		 <label for="User" ><?php ___('Estimated number of users sharing the same IP'); ?>&nbsp;:</label>
		<?php echo $form->input('WhitelistedIpAddress.no_of_student',array('size'=>28,'label'=>''))?>	
		</div>
		<div class="login_submit">
			<input type="submit" class="button" value="<?php ___('Send'); ?>">
		</div>
  </div>
  </form>
</div>
</div>
<!-- ////// End main content ////// -->


<!-- |||||| Begin sidebar ||||||| -->
  <div id="sidebar">
    <div id="download">
      <h2><?php ___('Download Scratch'); ?></h2>
      <p>
	  <a href="<?php echo $html->url('/download')?>"><?php echo $html->image('/img/download_scratch.gif',array('alt'=>'Download Scratch','width'=>'74','height'=>'32'))?></a>
	  <?php ___('Get the')?>&nbsp;<a href="<?php echo $html->url('/download')?>"><?php ___('latest version of Scratch')?></a>&nbsp;<?php ___('for Windows or Mac')?>
	  </p>
    </div>
  </div>
<!-- ////// End main sidebar ////// -->
<style type="text/css">
#send_request input,textarea{
margin:2px!important;

}
</style>
