<div><h3><?php echo "$username's notifications " ?> (<a target='_blank' href='/notifications/view/<?php echo $user_id ?>'>see all</a>)</h3></div>

<?php foreach($inappropriate_notifications as $notification): ?>
<?php
	$notification = $notification[0]; 
	$id = $notification['id'];
	$message = $template->create($notification, $username);
       $from_user_name = $notification['from_user_name'];
       if(empty($from_user_name)) {
           $from_user_name = ($notification['is_admin']) ? 'admin' : 'system';
       }
?>
<div style='background: #FFC'>
		<?php echo str_replace('<a', '<a target="_blank"', $message); ?>
		<div style="text-align:right; border-bottom: 1px solid #000">
			<small>By <?php echo $from_user_name; ?>
			&nbsp;
			<?php
			echo $notification['created'];
			?></small>
		</div>
 </div>  
<?php endforeach; ?>
