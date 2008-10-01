
<div class="fullcontent">

   <h2><?php ___('Explore Featured Projects'); ?></h2>

	<table width="100%">
	<tr>
	<td>
	<p>
	<?php ___('sort by'); ?>:
	<?php
	$pagination->setPaging($paging);
	e($sortByName = $pagination->sortBy("name", ___("title", true), "Project")." ");
    e($sortByloveit = $pagination->sortBy("loveit", ___("lovers", true), "Project")." ");
    e($sortByViews = $pagination->sortBy("views", ___("views", true), "Project")." ");
    e($sortByCreationdate = $pagination->sortBy("created", ___("creation date", true), "Project")." ");
    e($sortByLastmod = $pagination->sortBy("timestamp", ___("last modified", true), "Project"));
	?>
	</p>
	</td>
	
	
	<?php
	$prev = $pagination->prevPage(___("Prev", true),false, null, "prevlink");
	$next = $pagination->nextPage(___("Next", true),false, null, "nextlink");
	$pages = $pagination->pageNumbers(" ", null, "", "");
	?>
	
	<td align="right"><p class="pagination"><?php echo "$prev  $pages  $next" ?></p></td>
	</tr>
	</table>		
	
	
	<?php
		foreach ($data as $featured):
		$project = $featured['Project'];
		$views = $project['views'];
		$pid = $project['id'];
		$urlname = $project['User']['urlname'];
		$username = $project['User']['username'];
		$project_url = "/projects/$urlname/$pid";
		$project_desc = $project['description'];
		$lovers = $project['loveit'];
		$project_name = htmlspecialchars($project['name']);
		$mini_img_src = getThumbnailImg($urlname,$pid);?>
		<div class="gallerythumb clearme"> <a href="<? e($project_url)?>"><img src="<?php e($mini_img_src)?>" alt="<?php e($project_name)?>" width="148" height="111"></a>
			<h3><a href="<? e($project_url)?>"><?php e($project_name)?> </a></h3>			
			<p><strong><?php ___('By'); ?>: </strong><a href="/users/<?php e($urlname)?>"><?php e($username)?></a></p>
			<p><strong><?php ___('Views'); ?>:</strong> <?php e($views)?> | <strong><?php ___("LoveIt's"); ?></strong>: <?php e($lovers)?> </p>
			<p><strong><?php ___('Description'); ?></strong>: <?e($project_desc)?></p>
		</div>
   	<?php endforeach;?>	
   
   
   	<table width="100%">
	<td align="right"><p class="pagination"><?php echo "$prev  $pages  $next" ?></p></td>
	</tr>
	</table>
</div>
