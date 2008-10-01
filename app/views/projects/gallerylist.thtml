<?php

if ($error == true) {
	echo '<h5>';
	___('Error: Invalid Request');
echo '</h5>'; }
else { 
	echo '<p><h1>';
	printf(___('Project \'%s\' belongs to the following galleries:', true), $proj_name);
	echo '</h1>';
	echo $this->renderElement('galleryexplorer', array('option'=>$option, 'data'=>$data));
}
?>
