<?php
$domain = 'http://' . $_SERVER["SERVER_NAME"];
?>
<a href="#" onclick="shareTo('html');return false;" title="<?php ___('Put this project on your own site'); ?>" class="projectsharelink" id="htmlsharelink"><?php ___('Embed'); ?></a> 
<!--<a href="#" onclick="shareTo('email');return false;" class="projectsharelink" id="emailsharelink">Email</a>-->
<a href="#" onclick="shareTo('share');return false;" title=" <?php ___('Share this project with other sites.'); ?>" class="projectsharelink" id="sharesharelink"></a>
<br />

<?php e("
<link rel='image_src' href='$domain/static/projects/$urlname/{$pid}_sm.png' />
"); ?>

<!-- ::: Share ::: -->
<script type="text/javascript">
var share_uri="<?php e("$domain/projects/$urlname/$pid"); ?>";
var share_title="<?php e($project['Project']['name']); ?>";
var share_description='<?php
                  $notes = htmlspecialchars($project['Project']['description'], ENT_QUOTES);
                  $pattern = '/[\r\n]/';
                  $rep = ' ';
                  $notes = preg_replace($pattern, $rep, $notes);
                  echo $notes;
?>';
var share_windowoptions=',toolbar=0,status=0,scrollbars=yes,resizable=yes';
</script>

<!-- ::: HTML Embed Code ::: -->
<div id="htmlshare" class="projectsharesub">
<span style="font-weight:bold;"><?php ___('Copy this code onto your website'); ?></span><br />
<span onclick="$('htmlforimg').focus();$('htmlforimg').select();return false;"> <?php ___('As an image'); ?></span> <span style="font-size:7pt;">(<?php ___('works on most sites'); ?>)</span>: 
<br />
<?php
	$htmlforimg_value = htmlspecialchars("<a href='$domain/projects/$urlname/$pid'><img src='$domain/static/projects/$urlname/{$pid}_med.png' width='425' height='319' alt='Scratch Project'></a>");
?>
<input type="text" size="30" id="htmlforimg" value="<?php e($htmlforimg_value); ?>">
<span onclick="$('htmlforapplet').focus();$('htmlforapplet').select();return false;"><?php ___('As an applet'); ?></span>:
<br />
<?php
	$learnmore = ___('Learn more about this project', true);
	$htmlforapplet_value = htmlspecialchars("<applet id='ProjectApplet' style='display:block' code='ScratchApplet' codebase='$domain/static/misc' archive='ScratchApplet.jar' height='387' width='482'><param name='project' value='../../static/projects/$urlname/$pid.sb'></applet> <a href='$domain/projects/$urlname/$pid'>$learnmore</a>");
?>
<input type="text" size="30" id="htmlforapplet" value="<?php e($htmlforapplet_value); ?>">
</div>

<div id="htmlviewedshare" class="projectsharesub">
<?php ___('This will play your project when the page loads.'); ?>
<br />
<?php
	$learnmore = ___('Learn more about this project', true);
	$htmlforapplet_value = htmlspecialchars("<applet id='ProjectApplet' style='display:block' code='ScratchApplet' codebase='$domain/static/misc' archive='ScratchApplet.jar' height='387' width='482'><param name='project' value='../../static/projects/$urlname/$pid.sb'></applet> <a href='$domain/projects/$urlname/$pid'>$learnmore</a>");
?>
<input type="text" size="30" value="<?php e($htmlforapplet_value); ?>">
</div>

<div id="linkpicshare" class="projectsharesub">
<?php ___('This will put a picture on your site that is linked to this page.'); ?>
<br />
<?php
	$htmlforimg_value = htmlspecialchars("<a href='$domain/projects/$urlname/$pid'><img src='$domain/static/projects/$urlname/{$pid}_med.png' width='425' height='319' alt='Scratch Project'></a>");
?>
<input type="text" size="30" value="
<?php e($htmlforimg_value); ?>
">
</div>

<script type="text/javascript">
function hideAllShare() {
	$$('.projectsharesub').each(function(e){
		$(e).hide();
	});
	$$('.projectsharelink').each(function(e){
                $(e).style.fontWeight="normal";
        });
}
function shareTo(e) {
	var el = $(e + 'share');
	var l = $(e + 'sharelink');
	var eV = el.visible();
	var eL = (l.style.fontWeight == 'bold');
	hideAllShare();
	if (!eV) el.show();
	if (!eL) l.style.fontWeight='bold';
}
hideAllShare();
</script>
