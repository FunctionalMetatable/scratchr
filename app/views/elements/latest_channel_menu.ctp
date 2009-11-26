<div id="gallery_menu">
    <a href="<?php echo $html->url('/latest/shared')?>">
    <span class="button2<?php if($option == 'shared'): ?>_selected<?php endif ?>">
    <?php ___("newest");?>
    </span>
    </a>
   
    &nbsp;
    <a href="<?php echo $html->url('/latest/topviewed')?>">
    <span class="button2<?php if($option == 'topviewed'): ?>_selected<?php endif ?>">
    <?php ___("top viewed");?>
    </span>
    </a>
    &nbsp;
    <a href="<?php echo $html->url('/latest/toploved')?>">
    <span class="button2<?php if($option == 'toploved'): ?>_selected<?php endif ?>">
    <?php ___("top loved");?>
    </span>
    </a>
    &nbsp;
    <a href="<?php echo $html->url('/latest/remixed')?>">
    <span class="button2<?php if($option == 'remixed'): ?>_selected<?php endif ?>">
    <?php ___("top remixed");?>
    </span>
    </a>
    
</div>