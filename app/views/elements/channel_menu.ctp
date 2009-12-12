<div id="gallery_menu">
<!--
    <a href="<?php echo $html->url('/channel/recent')?>">
    <span class="button2<?php if($option == 'recent'): ?>_selected<?php endif ?>">
    <?php ___("newest");?>
    </span>
    </a>
    &nbsp;
//-->
    <a href="<?php echo $html->url('/channel/featured')?>">
    <span class="button2<?php if($option == 'featured'): ?>_selected<?php endif ?>">
    <?php ___("featured");?>
    </span>
    </a>
    &nbsp;
<!--
    <a href="<?php echo $html->url('/channel/topviewed')?>">
    <span class="button2<?php if($option == 'topviewed'): ?>_selected<?php endif ?>">
    <?php ___("top viewed");?>
    </span>
    </a>
    &nbsp;
    <a href="<?php echo $html->url('/channel/toploved')?>">
    <span class="button2<?php if($option == 'toploved'): ?>_selected<?php endif ?>">
    <?php ___("top loved");?>
    </span>
    </a>
    &nbsp;
    <a href="<?php echo $html->url('/channel/remixed')?>">
    <span class="button2<?php if($option == 'remixed'): ?>_selected<?php endif ?>">
    <?php ___("top remixed");?>
    </span>
    </a>
//-->
    &nbsp;
    <a href="<?php echo $html->url('/channel/surprise')?>">
    <span class="button2<?php if($option == 'surprise'): ?>_selected<?php endif ?>">
    <?php ___("surprise!");?>
    </span>
    </a>
    &nbsp;
    <?php if($isLoggedIn): ?>
    <a href="<?php echo $html->url('/channel/friends_latest')?>">
    <span class="button2<?php if($option == 'friends_latest'): ?>_selected<?php endif ?>">
    <?php ___("friends' latest");?>
    </span>
    </a>
    <?php endif; ?>
</div>
