<?php
if (isset($_GET['v'])) {
	//make sure people don't make weird requests
	//YT ID's consist of alphanumeric characters and -_
	$v = rawurlencode($_GET['v']);
} else {
	die();
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title> Scratch | Youtube Video</title>
<link href="/ext/youtube/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="maincontainer">
	<div id="header">
		<div class="logo"></div>
	</div>
	
	<div class="box_fullwidth">
		<?php
			//get title from YT API
			$r = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$v?v=2");
			if ($r) {
				$starttag = '<media:title type=\'plain\'>';
				$endtag   = '</media:title>';
				$start  = strpos($r, $starttag) + strlen($starttag);
				$length = strpos($r, $endtag) - $start;
				$title = substr($r, $start, $length);
				echo '<h1>'.$title.'</h1>'."\n".
					'<iframe class="youtube-player" type="text/html" width="750" height="451" src="http://www.youtube.com/embed/' . $v . '?fs=1&rel=0&autoplay=1&showinfo=0" frameborder="0">'.
					'</iframe>';
			} else {
				echo '<h1>Youtube video not found!</h1>'."\n".
				'<br><br>The link you followed might have been outdated or incorrect.<br><br><br><br>';
			}
		?>
		<br><br>
		<a href="#" onClick="history.go(-1)"><< Back to the previous page...</a><br>
		<br><br>
		<b>Why do I see this page?</b><br>
		Links to youtube videos on the forums are automatically converted to links to this page, to hide comments and other content.
	</div>
	<div id="footer">
		<a href="/download">Download</a> | 
		<a href="/redirect/donate">Donate</a> | 
		<a href="/redirect/privacy">Privacy Policy</a> | 
		<a href="/redirect/terms">Terms of Use</a> | 
		<a href="/redirect/copyright">Copyright Policy</a> | 
		<a href="http://info.scratch.mit.edu/Research">Research</a> | 
		<a href="/contact/us">Contact Us </a>
	</div>
</div>
</body>

</html>
