<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
	http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
   <?php foreach($sitemaps as $name => $sitemap): ?>
	<?php for($i =0; $i < $sitemap['count']; $i++): ?>
	   <sitemap>
	      <loc><?php echo Router::url('/', true); ?>sitemap_<?php echo $name; ?>_<?php echo $i ?>.xml<?php echo $ext; ?></loc>
	      <lastmod><?php echo $time->toAtom('-'.$i.'day'); ?></lastmod>
	   </sitemap>
	<?php endfor; ?>
   <?php endforeach; ?>
</sitemapindex>
