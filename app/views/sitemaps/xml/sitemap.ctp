<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
	http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach($items as $item): ?>
   <?php
	$param_array = array();
	foreach($url_params as $url_param) {
		$param_array[] = htmlspecialchars($item[$url_param[0]][$url_param[1]]);
	}
   ?>
   <url>
		<loc><?php vprintf($url_format, $param_array); ?></loc>
		<lastmod><?php echo $time->toAtom($item[$lastmod[0]][$lastmod[1]]); ?></lastmod>
		<changefreq><?php echo $changefreq ?></changefreq>
		<priority><?php echo $priority ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
