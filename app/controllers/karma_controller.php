<?php
class KarmaController extends AppController {
    var $name = 'Karma';
    var $components = array('Session', 'Cookie');
	var $uses = array('KarmaRating', 'GalleryProject', 'Flagger', 'Lover', 'User', 'Pcomment', 'Project', 'Gallery', 'Gcomment', 'Mpcomment', 'Mgcomment', 'Tags', 'ProjectTag', 'GalleryTag', 'MgalleryTag', 'MprojectTag');

	
}
?>
