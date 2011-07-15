<td style='border: 1px solid #000'>
<a target='_blank' href='/users/<?php echo $flagged['User']['username'] ?>'>
<?php
echo $html->image(getBuddyIconBySize($flagged['User']['id'],'mini', $content_status),array('alt'=>'user_icon','width'=>'28','height'=>'28'));
echo "&nbsp;" . $flagged['User']['username'];
?>
</a>&nbsp;<a title="Admin notifications in past 60 days / Total admin+system notifications" href='javascript:fetchNotes(<?php echo $flag['id'] ?>, <?php echo $flagged['User']['id'] ?>)'>[<?php echo $rcount ?>/<?php echo $count ?>]</a><br />
<a href='javascript:fetchComments(<?php echo $flag['id'] ?>, <?php echo $flagged['User']['id'] ?>)'>Comments</a> |
<a target='_blank' href='javascript:fetchIP(<?php echo $flag['id'] ?>, <?php echo $flagged['User']['id'] ?>)'>IP</a> |
<a target='_blank' href='/administration/ban_user/<?php echo $flagged['User']['id']?>/<?php echo $ban_reason ?>'>Ban</a><br />
</td>
