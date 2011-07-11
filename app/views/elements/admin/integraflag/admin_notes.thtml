<br />
<?php foreach(unserialize($flag['notes']) as $note): ?>
<div>
        <?php e(stripslashes($note['text'])); ?>
        <br />
		<div style="font-size: 10px; text-align:right; border-bottom: 1px solid #000">
            <?php e($note['admin']); ?> 
             - 
            <?php e(date("F j, Y, g:i a T", $note['time'])); ?>
        </div>
</div>
<?php endforeach; ?>
<div id='new_notes_<?php echo $flag['id']; ?>'>

</div>
<a onclick='$("fw_<?php echo $flag['id']; ?>").show();'>+ Add a note</a>
<div id='fw_<?php echo $flag['id']; ?>' style='display:none'>
<textarea id='fn_<?php echo $flag['id']; ?>' style='width:90%'></textarea><input type='button' value='Add note' onclick='saveAdminNote(<?php echo $flag['id']; ?>)' />
</div>
