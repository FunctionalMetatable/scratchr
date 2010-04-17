<?php if(!$isLogged): ?>
<?php
	$postfix = 'us';
	if(!empty($cookieCountry)) {
		$country = strtolower($cookieCountry);
		if(file_exists(WWW_ROOT.'static-locale/img/bannerbg_' . $country . '.png')) {
			$postfix = $country;
		}
	}
?>

<div class="bannerbg" style="margin-top:15px; background-image:url(../static-locale/img/bannerbg_<?= $postfix; ?>.png)">
<div class="balloontext">
	<?php printf(___('%sCheck out%s the %s projects from around the world!', true), '<a href="/channel/featured">', '</a>', $totalprojects); ?>
	<p style="margin-top: 6px;"><?php ___('To create your own projects:'); ?></p>
</div>
<div style="margin-top: 180px; margin-left: -250px; float: left;">
<a href="download"><img width="194" height="44" src="static-locale/img/download2_<?= $postfix; ?>.png"/></a>
</div>
    <div style="margin: 25px 20px 0px 0px; float: right;">
        <a href="static-locale/html/video_<?= $postfix; ?>.html?width=647&height=485" class="thickbox" rel="AjaxGroup" title="Video showing what people can do in Scratch" target="_blank"><img width="244" height="182" src="/img/video.png"/></a>
    </div>
</div>
<?php endif; ?>