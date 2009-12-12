<div id="gallery_menu">
    <a href="<?php echo $html->url('/latest/shared')?>">
    <span class="button2<?php if($option == 'shared'): ?>_selected<?php endif ?>">
    <?php ___("most recent");?>
    </span>
    </a>
   
    &nbsp;
    <a href="<?php echo $html->url('/latest/topviewed')?>">
    <span class="button2<?php if($option == 'topviewed'): ?>_selected<?php endif ?>">
    <?php ___("most viewed recently");?>
    </span>
    </a>
    &nbsp;
    <a href="<?php echo $html->url('/latest/toploved')?>">
    <span class="button2<?php if($option == 'toploved'): ?>_selected<?php endif ?>">
    <?php ___("most loved recently");?>
    </span>
    </a>
    &nbsp;
    <a href="<?php echo $html->url('/latest/remixed')?>">
    <span class="button2<?php if($option == 'remixed'): ?>_selected<?php endif ?>">
    <?php ___("most remixed recently");?>
    </span>
    </a>
	&nbsp;
<!--
    <?php if($isLoggedIn): ?>
    <a href="<?php echo $html->url('/latest/activemembers')?>">
    <span class="button2<?php if($option == 'activemembers'): ?>_selected<?php endif ?>">
    <?php ___("active members");?>
    </span>
    </a>
    <?php endif; ?>
//-->    
</div>
