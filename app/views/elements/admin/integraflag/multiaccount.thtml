<td class='flagbox' id='f_<?php echo $flag['id'] ?>'>
    <span class='inflag'>
        This account was created on a network used by the following banned accounts in the past <?php echo MULTI_DAYS_CHECK; ?> days:<br />
        
        <?php e(str_replace("Flaggers:", "", $this->renderElement('admin/integraflag/flagger_user_data', array('flag' => $flag, 'flaggers' => $flaggers)))); ?>
        
        <blockquote>
            IP actions: <a href='javascript:fetchIPMulti(<?php echo $flag['id'] ?>, "<?php echo long2ip($flagged['User']['ipaddress']); ?>");'><?php echo long2ip($flagged['User']['ipaddress']); ?></a>
        </blockquote>
        
        <div style='float:right; font-size: 11px; margin-right: 10px'>
        Registered: <?php echo $flagged['User']['created']; ?>
        </div>
    </span>
</td>
