

<div class="fullcontent">

   <h2><?php ___('Explore Users'); ?></h2>
   	
	<?php 
//	$session->flash(FLASH_ERROR_KEY);
	$session->flash(FLASH_NOTICE_KEY);
	?>
	
	<table width="100%">
	<tr>
	<td>
	<p>
	<?php ___('sort by:'); ?>
	<?php
	$pagination->setPaging($paging);
	e($sortByName = $pagination->sortBy("username", "name", "User")." ");
	?>
	</p>
	</td>
	
	<?php
	$prev = $pagination->prevPage(___("Prev", true),false, null, "prevlink");
	$next = $pagination->nextPage(___("Next",true),false, null, "nextlink");
	$pages = $pagination->pageNumbers(" ", null, "", "");
	?>
	
	<td align="right"><p class="pagination"><?php echo "$prev  $pages  $next" ?></p></td>
	</tr>
	</table>		
	
	
	<?php
		foreach ($users as $user):
		$user = $user['User'];
		$urlname = $user['urlname'];
		$user_name = $user['username'];
		$user_id = $user['id'];
		$buddyicon = getBuddyIconBySize($user['id'],'med', $user['timestamp']);
		$user_url = "/users/$urlname";?>
		<div class="gallerythumb clearme"> <a href="<? e($user_url)?>"><img src="<?php e($buddyicon)?>" title = "<?php e($user['username'])?>" alt="<?php e($user_name)?>" width="90px" height="90px"></a>
			<h3><a href="<? e($user_url)?>"><?php e($user_name)?> </a></h3>
		</div>
   	<?php endforeach;?>	
   
   
   	<table width="100%">
	<td align="right"><p class="pagination"><?php echo "$prev  $pages  $next" ?></p></td>
	</tr>
	</table>
</div>
