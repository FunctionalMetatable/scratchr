<div><h3><?php echo $username ?>'s comments
(<a target='_blank' href='/administration/viewuser/<?php echo $user_id ?>'>project</a> | 
<a target='_blank' href='/administration/viewuser/<?php echo $user_id ?>/gcomments'>gallery</a> |
<a target='_blank' href='/administration/viewuser/<?php echo $user_id ?>/dpcomments'>deleted project</a> | 
<a target='_blank' href='/administration/viewuser/<?php echo $user_id ?>/dgcomments'>deleted gallery</a>)</h3>
</a></div>

<?php foreach($final_comments as $comment): ?>
<?php
	if(isset($comment['Gcomment']))
	{
		$type = 'g';
		$user = $comment['User'];
		$gallery = $comment['Gallery'];
		$comment = $comment['Gcomment'];
		$deleted = false;
		switch($comment['comment_visibility']) {
		  case 'delbyadmin':
		  case 'censbyadmin':
		    $bg = '#FFE3FF'; 
		    $deleted = true;
		    break;
		  case 'censbycomm':
		  case 'delbyusr':
		    $bg = '#FFFBC2'; 
		    $deleted = true;
		    break;
		  default:
        $bg = '#FFF'; break;
		}
	}
	else
	{
		$type ='p';
		$user = $comment['User'];
		$project = $comment['Project'];
		$comment = $comment['Pcomment'];
		$deleted = false;
		switch($comment['comment_visibility']) {
		  case 'delbyadmin':
		  case 'censbyadmin':
		    $deleted = true;
		    $bg = '#FFE3FF';
		    break;
		  case 'censbycomm':
		  case 'delbyuse':
		    $deleted = true;
		    $bg = '#FFFBC2';
		    break;
		  default:
        $bg = '#F2F2FF'; break;
		}
	}
	$id = $notification['id'];
?>
<div style='background: <?php echo $bg ?>' id='c_<?php echo $comment['id']?>'>
		<?php echo $comment['content']; ?>
		<div style="text-align:right; border-bottom: 1px solid #000">
			<small>
				<?php 
				echo $comment['created'];
				if($type == 'g') 
				{
					echo " | <a target='_blank' href='/galleries/view/$gallery[id]'>$gallery[name]</a>";
				} else {
					echo " | <a target='_blank' href='/projects/$project[username]/$project[id]'>$project[name]</a>";
				}
				if(!$deleted)
				  echo " | <a href='javascript:deleteComment($user[id], $comment[id], \"{$type}comment\")'>[X]</a>";
				?>
			</small>
		</div>
 </div>  
<?php endforeach; ?>
