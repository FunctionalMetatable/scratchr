<?php 

$charleft = 60;
$numgals = 0;
foreach($gallerylist as $gallery) {
	$ignored = $gallery['Gallery']['ignored'];
	if ($ignored) {
	} else {
		$numgals++;
	}
}
if ($numgals <= 0) return;

if ($numgals == 1) $text = ___("gallery", true);
else $text = ___("galleries", true);
printf(___("Shown in %s", true), $text);

$igal = $numgals;
foreach ($gallerylist as $gallery) {
	$gname = $gallery['Gallery']['name'];
	$gid = $gallery['Gallery']['id'];
	$ignored = $gallery['Gallery']['ignored'];
	$len = strlen($gname);
	if ($len >= $charleft)
		$gname = substr($gname, 0, $charleft - 3) . "...";
	$charleft -= $len;
        $igal--;
	
	if ($ignored) {
	} else {
		echo " <a href=\"/galleries/view/$gid/\">$gname</a>";
		if ($igal > 0) echo ", ";
		else echo ".";
		if ($charleft <= 0) break;
	}
}

echo "<br>"; ___("This project belongs to"); echo " <a href=\"/projects/$urlname/$pid/gallerylist\">$numgals $text</a>.";

?>

