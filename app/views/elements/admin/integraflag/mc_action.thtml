<td style='border: 1px solid #000'>
<?php if($flag['status'] != 'review'): ?>
<input title="Mark flag for further review" onclick='mark(<?php echo $flag['id'] ?>, "review")' type='button' value='Rev' id='rv_button_<?php echo $flag['id'] ?>' class='button2' style='background: #427D1D; background-image: url()' />
<?php endif; if($flag['status'] != 'closed'): ?>
<input title="Mark flag as closed/done" onclick='mark(<?php echo $flag['id'] ?>, "closed")' type='button' value='&#10003;' id='close_button_<?php echo $flag['id'] ?>' class='button2' style='background: #427D1D; background-image: url()' />
<?php endif; ?>
<br />
<a id='fetchnotes_<?php echo $flag['id'] ?>' onclick='fetchAdminNotes(<?php echo $flag['id'] ?>)'>Notes:<?php $n = @unserialize($flag['notes']); echo (empty($n)) ? 0 : count($n); ?></a>

<?php if($handler != null): ?>
<br />~<?php echo $handler['User']['username'] ?>
<?php endif; ?>
</td>
