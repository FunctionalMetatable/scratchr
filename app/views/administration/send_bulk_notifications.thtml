<script>
    
</script>

<div class="fullcontent">
	<h2>Send Bulk Notifications</h2>
	<br/>
	<h3>Present notifications only to some users</h3>
    <br/>
	<form action="/administration/send_bulk_notifications" method="post">
        <p>
        <strong style="width:120px; display:block; float:left; text-align:right; padding-right:10px;">Enter User Names:</strong>
        <input id="notif_type" name="data[usernames]" type="text" style="width:230px" />
        <span>&nbsp;*separated by commas</span>
        </p>
        <p>
        <strong style="width:120px; display:block; float:left; text-align:right; padding-right:10px;">Text:</strong>
        <textarea name="data[text]" style="width:230px; height:80px"></textarea>
        </p>
        <p style="margin-left: 129px;">
        <input type="submit" value="Send">
        </p>
	</form>
	<br/>

    <?php if(!empty($users['valids'])): ?>
    <h3>Notifications were sent successfully to the following users:</h3>
    <?php foreach($users['valids'] as $user): ?>
        <a href="/users/<?= $user; ?>">
            <?= $user; ?>
        </a>&nbsp;
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if(!empty($users['invalids'])): ?>
    <h3>Could not send notifications to the following users (usernames are invalid):</h3>
    <?php foreach($users['invalids'] as $user): ?>
        <span>
            <?= $user; ?>
        </span>&nbsp;
    <?php endforeach; ?>
    <?php endif; ?>
        
</div>