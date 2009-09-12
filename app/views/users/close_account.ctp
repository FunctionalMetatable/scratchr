<div class="fullcontent">
<p>
<?php printf(___('"Sorry to see you go. In order to confirm you want to close your account %s and make all your projects unavailable, please type below the reasons for doing this. Your account will be closed immediately after that."',true),'<b>'.$username.'</b>')?>
</p>
<br/>
<form action="<?php echo $html->url('/users/close_account/'.$user_id)?>" method="POST" accept-charset="UTF-8">
<div>
<?php ___('Reason'); ?>:<br/>
<?php echo $form->input('User.reasons',array('type'=>'textArea','rows'=>10,'cols'=>60,'label'=>''))?>
</div>
<br/>
<p>
		<input type="submit" class="button" value="<?php ___('Close the account'); ?>">
        <a href="<?php echo $html->url('/users/'.$username)?>" ><span class='button2'><?php ___('Nevermind, I don\'t want to close my account')?></span></a>
</p>
</form>
</div>



