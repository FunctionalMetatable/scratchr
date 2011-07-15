<div style='font-size: 10px'>
Flaggers: <?php $i = 0; foreach($flaggers as $flagger): ?>
<?php $i++; if($i > 1) echo " | "; ?>
<a target='_blank' href='/users/<?php echo $flagger['User']['username']; ?>'> <?php echo $flagger['User']['username']; ?></a>
<a title='Admin notifications in past 60 days / Total admin+system notifications'  href='javascript:fetchNotes(<?php echo $flag['id']; ?>, <?php echo $flagger['User']['id']; ?>)'>[<?php echo $flagger['rcount'], '/', $flagger['count']; ?>]</a>
 - <a href='javascript:fetchComments(<?php echo $flag['id']; ?>, <?php echo $flagger['User']['id']; ?>)'>Comments</a>
<?php endforeach; ?>
 (<?php echo date("n/j/y, g:i a", strtotime($flag['created'])); ?>)
</div>
