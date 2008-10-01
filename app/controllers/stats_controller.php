<?php
class StatsController extends AppController {

    var $name = "Stats";
    var $uses = array('Mpcomment', 'Tcomment', 'Project', 'Tagger', 'FeaturedProject', 'User', 'Pcomment', 'ViewStat', 'ProjectTag', 'Tag', 'Lover', 'Favorite', 'Downloader', 'Flagger', 'Notification', 'Relationship','ProjectSave', 'ProjectShare','ThemeMembership','Theme','RemixedProject');
    var $comeonents = array('RequestHandler','Pagination');
    var $helpers = array('Javascript', 'Ajax', 'Html', 'Pagination', 'Tagcloud');
    

    function index()
    {
    }

    function display($N=5, $FROMDATE=NULL, $TODATE=NULL)
    {
	// Initialize parameters
	$this->set('N', $N);
	if($FROMDATE==NULL)
	{
		$FROMDATE="0000-00-00";
	}
	if($TODATE==NULL)
	{
		$TODATE=date("Y-m-d");
	}
	$FROMTIMESTAMP = $FROMDATE." 00:00:00";
	$TOTIMESTAMP = $TODATE." 00:00:00";
	$total_days = (strtotime($TOTIMESTAMP)-strtotime($FROMTIMESTAMP))/(24*60*60);

        $tags = $this->Tag->query("
            SELECT Tag.name, COUNT(Project.id) as tagcounter FROM projects Project
            JOIN project_tags tt ON Project.id = tt.project_id
            JOIN tags Tag ON tt.tag_id = Tag.id
	    WHERE Tag.timestamp>'$FROMTIMESTAMP' AND Tag.timestamp<='$TOTIMESTAMP'
            GROUP BY Tag.id
            ORDER BY tagcounter DESC
            LIMIT " . $N);
        $this->set('tags_cloud', $tags);
	
	// Top projects by comments
	$projects_by_comments = null;
	$project_ids_by_comments = $this->Project->query("SELECT project_id, COUNT(*) FROM `pcomments` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;
	foreach ($project_ids_by_comments as $project_id_by_comments)
	{
		$project_id = $project_id_by_comments['pcomments']['project_id'];
		$count_comments = $project_id_by_comments[0]['COUNT(*)'];
		$projects_by_comments[$counter]=$this->Project->find("Project.id=$project_id");
		$projects_by_comments[$counter]['Project']['count_comments'] = $count_comments;
		$counter++;
		$projects_by_comments[0]['linktocsv']="/stats/csv/projects_by_comments/$N/$FROMDATE/$TODATE";
	}
	$this->set('projects_by_comments', $projects_by_comments);

	// Top projects by downloads
	$projects_by_downloads = null;
	$project_ids_by_downloads = $this->Project->query("SELECT project_id, COUNT(*) FROM `downloaders` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;
	foreach ($project_ids_by_downloads as $project_id_by_downloads)
	{
		$project_id = $project_id_by_downloads['downloaders']['project_id'];
		$count_downloads = $project_id_by_downloads[0]['COUNT(*)'];
		$projects_by_downloads[$counter]=$this->Project->find("Project.id=$project_id");
		$projects_by_downloads[$counter]['Project']['count_downloads'] = $count_downloads;
		$counter++;
		$projects_by_downloads[0]['linktocsv']="/stats/csv/projects_by_downloads/$N/$FROMDATE/$TODATE";
	}
	$this->set('projects_by_downloads', $projects_by_downloads);

	// Top taggers
	$taggers = null;
	$tagger_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `taggers` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;

	foreach ($tagger_ids as $tagger_id_record)
	{
		$tagger_id = $tagger_id_record['taggers']['user_id'];
		$count_tags = $tagger_id_record[0]['COUNT(*)'];
		$taggers[$counter]=$this->User->find("User.id=$tagger_id");
		$taggers[$counter]['User']['count_tags'] = $count_tags;
		$counter++;
		$taggers[0]['linktocsv']="/stats/csv/taggers/$N/$FROMDATE/$TODATE";
	}
	$this->set('taggers', $taggers);

	// Top downloaders
	$downloaders = null;
	$downloader_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `downloaders` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;

	foreach ($downloader_ids as $downloader_id_record)
	{
		$downloader_id = $downloader_id_record['downloaders']['user_id'];
		$count_downloaders = $downloader_id_record[0]['COUNT(*)'];
		$downloaders[$counter]=$this->User->find("User.id=$downloader_id");
		$downloaders[$counter]['User']['count_downloaders'] = $count_downloaders;
		$counter++;
		$downloaders[0]['linktocsv']="/stats/csv/downloaders/$N/$FROMDATE/$TODATE";
	}
	$this->set('downloaders', $downloaders);

	// Top pcommenters
	$pcommenters = null;
	$pcommenter_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `pcomments` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;
	foreach ($pcommenter_ids as $pcommenter_id_record)
	{
		$pcommenter_id = $pcommenter_id_record['pcomments']['user_id'];
		$count_pcommenters = $pcommenter_id_record[0]['COUNT(*)'];
		$pcommenters[$counter]=$this->User->find("User.id=$pcommenter_id");
		$pcommenters[$counter]['User']['count_pcommenters'] = $count_pcommenters;
		$counter++;
		$pcommenters[0]['linktocsv']="/stats/csv/pcommenters/$N/$FROMDATE/$TODATE";
	}
	$this->set('pcommenters', $pcommenters);

	// Top tcommenters
	$tcommenters = null;
	$tcommenter_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `tcomments` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;
	foreach ($tcommenter_ids as $tcommenter_id_record)
	{
		$tcommenter_id = $tcommenter_id_record['tcomments']['user_id'];
		$count_tcommenters = $tcommenter_id_record[0]['COUNT(*)'];
		$tcommenters[$counter]=$this->User->find("User.id=$tcommenter_id");
		$tcommenters[$counter]['User']['count_tcommenters'] = $count_tcommenters;
		$counter++;
		$tcommenters[0]['linktocsv']="/stats/csv/tcommenters/$N/$FROMDATE/$TODATE";
	}
	$this->set('tcommenters', $tcommenters);

	// Top spriters
	$spriters = null;
	$spriter_ids = $this->Project->query("SELECT user_id, SUM(numberOfSprites) FROM `projects` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY SUM(numberOfSprites) DESC LIMIT ".$N);
	$counter=0;
	foreach ($spriter_ids as $spriter_id_record)
	{
		$spriter_id = $spriter_id_record['projects']['user_id'];
		$count_sprites = $spriter_id_record[0]['SUM(numberOfSprites)'];
		$spriters[$counter]=$this->User->find("User.id=$spriter_id");
		$spriters[$counter]['User']['count_sprites'] = $count_sprites;
		$counter++;
		$spriters[0]['linktocsv']="/stats/csv/spriters/$N/$FROMDATE/$TODATE";
	}
	$this->set('spriters', $spriters);

	// Top scripters
	$scripters = null;
	$scripter_ids = $this->Project->query("SELECT user_id, SUM(totalScripts) FROM `projects` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY SUM(totalScripts) DESC LIMIT ".$N);
	$counter=0;
	foreach ($scripter_ids as $scripter_id_record)
	{
		$scripter_id = $scripter_id_record['projects']['user_id'];
		$count_scripts = $scripter_id_record[0]['SUM(totalScripts)'];
		$scripters[$counter]=$this->User->find("User.id=$scripter_id");
		$scripters[$counter]['User']['count_scripts'] = $count_scripts;
		$counter++;
		$scripters[0]['linktocsv']="/stats/csv/scripters/$N/$FROMDATE/$TODATE";
	}
	$this->set('scripters', $scripters);

	// Top flaggers
	$flaggers = null;
	$flagger_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `flaggers` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;

	foreach ($flagger_ids as $flagger_id_record)
	{
		$flagger_id = $flagger_id_record['flaggers']['user_id'];
		$count_flaggers = $flagger_id_record[0]['COUNT(*)'];
		$flaggers[$counter]=$this->User->find("User.id=$flagger_id");
		$flaggers[$counter]['User']['count_flaggers'] = $count_flaggers;
		$counter++;
		$flaggers[0]['linktocsv']="/stats/csv/flaggers/$N/$FROMDATE/$TODATE";
	}
	$this->set('flaggers', $flaggers);

	// Top lovers
	$lovers = null;
	$lover_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `lovers` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;

	foreach ($lover_ids as $lover_id_record)
	{
		$lover_id = $lover_id_record['lovers']['user_id'];
		$count_lovers = $lover_id_record[0]['COUNT(*)'];
		$lovers[$counter]=$this->User->find("User.id=$lover_id");
		$lovers[$counter]['User']['count_lovers'] = $count_lovers;
		$counter++;
		$lovers[0]['linktocsv']="/stats/csv/lovers/$N/$FROMDATE/$TODATE";
	}
	$this->set('lovers', $lovers);

	// Top users by friends
	$users_by_friends = null;
	$user_ids_by_friends = $this->Relationship->query("SELECT user_id, COUNT(*) FROM `relationships` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
	$counter=0;
	foreach ($user_ids_by_friends as $user_id_by_friends)
	{
		$user_id = $user_id_by_friends['relationships']['user_id'];
		$count_friends = $user_id_by_friends[0]['COUNT(*)'];
		$users_by_friends[$counter]=$this->User->find("User.id=$user_id");
		$users_by_friends[$counter]['User']['count_friends'] = $count_friends;
		$counter++;
		$users_by_friends[0]['linktocsv']="/stats/csv/users_by_friends/$N/$FROMDATE/$TODATE";
	}
	$this->set('users_by_friends', $users_by_friends);

	// Total projects by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_projects_byday[$day]['date']=date("M j, Y", $current_day);
		$total_projects_byday[$day]['count']=$this->Project->findCount("Project.`created` > '".date("Y-m-d 00:00:00",$current_day)."' AND Project.`created` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_projects_byday[0]['linktocsv']="/stats/csv/projects_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_projects_byday', $total_projects_byday);

	// Total projects
	$total_projects = null;
	$total_projects = $this->Project->findCount("Project.`created` > '$FROMTIMESTAMP' AND Project.`created` <= '$TOTIMESTAMP'");
	$this->set('total_projects', $total_projects);

	// Total pcomments by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_pcomments_byday[$day]['date']=date("M j, Y", $current_day);
		$total_pcomments_byday[$day]['count']=$this->Pcomment->findCount("Pcomment.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomment.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_pcomments_byday[0]['linktocsv']="/stats/csv/pcomments_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_pcomments_byday', $total_pcomments_byday);

	// Total pcomments
	$total_pcomments = null;
	$total_pcomments = $this->Pcomment->findCount("Pcomment.`timestamp` > '$FROMTIMESTAMP' AND Pcomment.`timestamp` <= '$TOTIMESTAMP'");
	$this->set('total_pcomments', $total_pcomments);

	// Total tcomments by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_tcomments_byday[$day]['date']=date("M j, Y", $current_day);
		$total_tcomments_byday[$day]['count']=$this->Tcomment->findCount("Tcomment.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Tcomment.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_tcomments_byday[0]['linktocsv']="/stats/csv/tcomments_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_tcomments_byday', $total_tcomments_byday);

	// Total tcomments
	$total_tcomments = null;
	$total_tcomments = $this->Tcomment->findCount("Tcomment.`timestamp` > '$FROMTIMESTAMP' AND Tcomment.`timestamp` <= '$TOTIMESTAMP'");
	$this->set('total_tcomments', $total_tcomments);

	// Total pcommenters by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_pcommenters_byday[$day]['date']=date("M j, Y", $current_day);
		$total_pcommenters_data = $this->Pcomment->query("SELECT DISTINCT user_id FROM Pcomments WHERE Pcomments.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomments.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$total_pcommenters_byday[$day]['count']=count($total_pcommenters_data);
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_pcommenters_byday[0]['linktocsv']="/stats/csv/pcommenters_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_pcommenters_byday', $total_pcommenters_byday);

	// Total pcommenters
	$total_pcommenters = null;
	$total_pcommenters_data = $this->Pcomment->query("SELECT DISTINCT user_id FROM Pcomments WHERE Pcomments.`timestamp` > '$FROMTIMESTAMP' AND Pcomments.`timestamp` <= '$TOTIMESTAMP'");
	$total_pcommenters = count($total_pcommenters_data);
	$this->set('total_pcommenters', $total_pcommenters);

	// Total tcommenters by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_tcommenters_byday[$day]['date']=date("M j, Y", $current_day);
		$total_tcommenters_data = $this->Pcomment->query("SELECT DISTINCT user_id FROM Pcomments WHERE Pcomments.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomments.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$total_tcommenters_byday[$day]['count']=count($total_tcommenters_data);
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_tcommenters_byday[0]['linktocsv']="/stats/csv/tcommenters_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_tcommenters_byday', $total_tcommenters_byday);

	// Total tcommenters
	$total_tcommenters = null;
	$total_tcommenters_data = $this->Tcomment->query("SELECT DISTINCT user_id FROM Tcomments WHERE Tcomments.`timestamp` > '$FROMTIMESTAMP' AND Tcomments.`timestamp` <= '$TOTIMESTAMP'");
	$total_tcommenters = count($total_tcommenters_data);
	$this->set('total_tcommenters', $total_tcommenters);

	// Total tags by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_tags_byday[$day]['date']=date("M j, Y", $current_day);
		$total_tags_byday[$day]['count']=$this->Tag->findCount("Tag.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Tag.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_tags_byday[0]['linktocsv']="/stats/csv/tags_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_tags_byday', $total_tags_byday);

	// Total tags
	$total_tags = null;
	$total_tags = $this->Tag->findCount("Tag.`timestamp` > '$FROMTIMESTAMP' AND Tag.`timestamp` <= '$TOTIMESTAMP'");
	$this->set('total_tags', $total_tags);

	// Total taggers by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_taggers_byday[$day]['date']=date("M j, Y", $current_day);
		$total_taggers_data = $this->Tagger->query("SELECT DISTINCT user_id FROM taggers WHERE taggers.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND taggers.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$total_taggers_byday[$day]['count']=count($total_taggers_data);
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_taggers_byday[0]['linktocsv']="/stats/csv/taggers_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_taggers_byday', $total_taggers_byday);

	// Total taggers
	$total_taggers = null;
	$total_taggers_data = $this->Tag->query("SELECT DISTINCT user_id FROM Taggers WHERE Taggers.`timestamp` > '$FROMTIMESTAMP' AND Taggers.`timestamp` <= '$TOTIMESTAMP'");
	$total_taggers = count($total_taggers_data);
	$this->set('total_taggers', $total_taggers);

	// Total sprites by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_sprites_byday[$day]['date']=date("M j, Y", $current_day);
		$total_sprites_data = $this->Project->query("SELECT SUM(numberOfSprites) FROM Projects WHERE Projects.`timestamp` > '$FROMTIMESTAMP' AND Projects.`timestamp` <= '$TOTIMESTAMP'");
		$total_sprites_byday[$day]['count']=$total_sprites = $total_sprites_data[0][0]['SUM(numberOfSprites)'];
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_sprites_byday[0]['linktocsv']="/stats/csv/sprites_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_sprites_byday', $total_sprites_byday);

	// Total sprites
	$total_sprites = null;
	$total_sprites_data = $this->Project->query("SELECT SUM(numberOfSprites) FROM Projects WHERE Projects.`timestamp` > '$FROMTIMESTAMP' AND Projects.`timestamp` <= '$TOTIMESTAMP'");
	$total_sprites = $total_sprites_data[0][0]['SUM(numberOfSprites)'];
	$this->set('total_sprites', $total_sprites);

	// Total scripts by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_scripts_byday[$day]['date']=date("M j, Y", $current_day);
		$total_scripts_data = $this->Project->query("SELECT SUM(totalScripts) FROM Projects WHERE Projects.`timestamp` > '$FROMTIMESTAMP' AND Projects.`timestamp` <= '$TOTIMESTAMP'");
		$total_scripts_byday[$day]['count']=$total_scripts = $total_scripts_data[0][0]['SUM(totalScripts)'];
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_scripts_byday[0]['linktocsv']="/stats/csv/scripts_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_scripts_byday', $total_scripts_byday);

	// Total scripts
	$total_scripts = null;
	$total_scripts_data = $this->Project->query("SELECT SUM(totalScripts) FROM Projects WHERE Projects.`timestamp` > '$FROMTIMESTAMP' AND Projects.`timestamp` <= '$TOTIMESTAMP'");
	$total_scripts = $total_scripts_data[0][0]['SUM(totalScripts)'];
	$this->set('total_scripts', $total_scripts);

	// Total flags by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_flags_byday[$day]['date']=date("M j, Y", $current_day);
		$total_flags_byday[$day]['count']=$this->Flagger->findCount("Flagger.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Flagger.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_flags_byday[0]['linktocsv']="/stats/csv/flags_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_flags_byday', $total_flags_byday);

	// Total flags
	$total_flags = null;
	$total_flags = $this->Flagger->findCount("Flagger.`timestamp` > '$FROMTIMESTAMP' AND Flagger.`timestamp` <= '$TOTIMESTAMP'");
	$this->set('total_flags', $total_flags);

	// Total loves by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_loves_byday[$day]['date']=date("M j, Y", $current_day);
		$total_loves_byday[$day]['count']=$this->Lover->findCount("Lover.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Lover.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_loves_byday[0]['linktocsv']="/stats/csv/loves_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_loves_byday', $total_loves_byday);

	// Total loves
	$total_loves = null;
	$total_loves = $this->Lover->findCount("Lover.`timestamp` > '$FROMTIMESTAMP' AND Lover.`timestamp` <= '$TOTIMESTAMP'");
	$this->set('total_loves', $total_loves);

	// Total friendships by day
	$current_day = strtotime($FROMTIMESTAMP);
	$next_day = $current_day+(60*60*24);
	for($day=0; $day<$total_days+1; $day++)
	{
		$total_friendships_byday[$day]['date']=date("M j, Y", $current_day);
		$total_friendships_byday[$day]['count']=$this->Relationship->findCount("Relationship.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Relationship.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
		$current_day += (60*60*24);
		$next_day += (60*60*24);
	}
	$total_friendships_byday[0]['linktocsv']="/stats/csv/friendships_byday/$N/$FROMDATE/$TODATE";
	$this->set('total_friendships_byday', $total_friendships_byday);

	// Total friendships
	$total_friendships = null;
	$total_friendships = $this->Relationship->findCount("Relationship.`timestamp` > '$FROMTIMESTAMP' AND Relationship.`timestamp` <= '$TOTIMESTAMP'");
	$this->set('total_friendships', $total_friendships);

	$this->render('stats');
    }

    function csv($element, $N=5, $FROMDATE=NULL, $TODATE=NULL)
    {
	
		$this->autoRender = false;
		
		// Initialize parameters
		if($FROMDATE==NULL)
		{
			$FROMDATE="0000-00-00";
		}
		if($TODATE==NULL)
		{
			$TODATE=date("Y-m-d");
		}
		$FROMTIMESTAMP = $FROMDATE." 00:00:00";
		$TOTIMESTAMP = $TODATE." 00:00:00";
		$total_days = (strtotime($TOTIMESTAMP)-strtotime($FROMTIMESTAMP))/(24*60*60);
	
		if($element=="projects_by_comments")
		{
		// Top projects by comments
		$projects_by_comments = null;
		$delimiter_on = false;
		$project_ids_by_comments = $this->Project->query("SELECT project_id, COUNT(*) FROM `pcomments` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($project_ids_by_comments as $project_id_by_comments)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$project_id = $project_id_by_comments['pcomments']['project_id'];
			$count_comments = $project_id_by_comments[0]['COUNT(*)'];
			$project=$this->Project->find("Project.id=$project_id");
			$project_name = $project['Project']['name'];
			echo $project_id.",".$project_name.",".$count_comments;
			echo "\n";
		}
		}
	
		if($element=="projects_by_downloads")
		{
		// Top projects by downloads
		$projects_by_downloads = null;
		$delimiter_on = false;
		$project_ids_by_downloads = $this->Project->query("SELECT project_id, COUNT(*) FROM `downloaders` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($project_ids_by_downloads as $project_id_by_downloads)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$project_id = $project_id_by_downloads['downloaders']['project_id'];
			$count_downloads = $project_id_by_downloads[0]['COUNT(*)'];
			$project=$this->Project->find("Project.id=$project_id");
			$project_name = $project['Project']['name'];
			echo $project_id.",".$project_name.",".$count_downloads;
			echo "\n";
		}
		}
	
		if($element=="taggers")
		{
		// Top taggers
		$taggers = null;
		$delimiter_on = false;
		$tagger_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `taggers` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($tagger_ids as $tagger_id_record)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$tagger_id = $tagger_id_record['taggers']['user_id'];
			$count_tags = $tagger_id_record[0]['COUNT(*)'];
			$tagger = $this->User->find("User.id=$tagger_id");
			$tagger_name = $tagger['User']['username'];
			echo $tagger_id.",".$tagger_name.",".$count_tags;
			echo "\n";
		}
		}
	
		if($element=="downloaders")
		{
		// Top downloaders
		$downloaders = null;
		$delimiter_on = false;
		$downloader_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `downloaders` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($downloader_ids as $downloader_id_record)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$downloader_id = $downloader_id_record['downloaders']['user_id'];
			$count_tags = $downloader_id_record[0]['COUNT(*)'];
			$downloader = $this->User->find("User.id=$downloader_id");
			$downloader_name = $downloader['User']['username'];
			echo $downloader_id.",".$downloader_name.",".$count_tags;
			echo "\n";
		}
		}
	
		if($element=="pcommenters")
		{
		// Top pcommenters
		$pcommenters = null;
		$delimiter_on = false;
		$pcommenter_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `pcomments` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($pcommenter_ids as $pcommenter_id_record)
		{
			if($delimiter_on)
			{
			#	echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$pcommenter_id = $pcommenter_id_record['pcomments']['user_id'];
			$count_tags = $pcommenter_id_record[0]['COUNT(*)'];
			$pcommenter = $this->User->find("User.id=$pcommenter_id");
			$pcommenter_name = $pcommenter['User']['username'];
			echo  $pcommenter_id.",".$pcommenter_name.",".$count_tags;
			echo "\n";
		}
		}
	
		if($element=="tcommenters")
		{
		// Top tcommenters
		$tcommenters = null;
		$delimiter_on = false;
		$tcommenter_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `tcomments` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($tcommenter_ids as $tcommenter_id_record)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$tcommenter_id = $tcommenter_id_record['tcomments']['user_id'];
			$count_tags = $tcommenter_id_record[0]['COUNT(*)'];
			$tcommenter = $this->User->find("User.id=$tcommenter_id");
			$tcommenter_name = $tcommenter['User']['username'];
			echo $tcommenter_id.",".$tcommenter_name.",".$count_tags;
			echo "\n";
		}
		}
	
		if($element=="flaggers")
		{
		// Top flaggers
		$flaggers = null;
		$delimiter_on = false;
		$flagger_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `flaggers` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($flagger_ids as $flagger_id_record)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$flagger_id = $flagger_id_record['flaggers']['user_id'];
			$count_tags = $flagger_id_record[0]['COUNT(*)'];
			$flagger = $this->User->find("User.id=$flagger_id");
			$flagger_name = $flagger['User']['username'];
			echo $flagger_id.",".$flagger_name.",".$count_tags;
			echo "\n";
		}
		}
	
		if($element=="lovers")
		{
		// Top lovers
		$lovers = null;
		$delimiter_on = false;
		$lover_ids = $this->Project->query("SELECT user_id, COUNT(*) FROM `lovers` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($lover_ids as $lover_id_record)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$lover_id = $lover_id_record['lovers']['user_id'];
			$count_tags = $lover_id_record[0]['COUNT(*)'];
			$lover = $this->User->find("User.id=$lover_id");
			$lover_name = $lover['User']['username'];
			echo $lover_id.",".$lover_name.",".$count_tags;
			echo "\n";
		}
		}
	
		if($element=="users_by_friends")
		{
		// Top users by friends
		$users_by_friends = null;
		$delimiter_on = false;
		$user_ids_by_friends = $this->Relationship->query("SELECT user_id, COUNT(*) FROM `relationships` WHERE `timestamp` > '$FROMTIMESTAMP' AND `timestamp` <= '$TOTIMESTAMP' GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT ".$N);
		foreach ($user_ids_by_friends as $user_id_by_friends)
		{
			if($delimiter_on)
			{
				echo ",";
			}
			else
			{
				$delimiter_on = true;
			}
			$user_id = $user_id_by_friends['relationships']['user_id'];
			$count_friends = $user_id_by_friends[0]['COUNT(*)'];
			$user=$this->User->find("User.id=$user_id");
			$username = $user['User']['username'];
			echo $user_id.",".$username.",".$count_friends;
			echo "\n";
		}
		}
	
		if($element=="projects_byday")
		{
		// Total projects by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("Y/m/d", $current_day).",";
			echo ($this->Project->findCount("Project.`created` > '".date("Y-m-d 00:00:00",$current_day)."' AND Project.`created` <= '".date("Y-m-d 00:00:00",$next_day)."'"))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="pcomments_byday")
		{
		// Total pcomments by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("Ymd", $current_day).",";
			echo ($this->Pcomment->findCount("Pcomment.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomment.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'"))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="tcomments_byday")
		{
		// Total tcomments by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("Ymd", $current_day).",";
			echo ($this->Tcomment->findCount("Tcomment.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Tcomment.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'"))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="pcommenters_byday")
		{
		// Total pcommenters by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			$total_pcommenters_data = $this->Pcomment->query("SELECT DISTINCT user_id FROM Pcomments WHERE Pcomments.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomments.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
			echo (count($total_pcommenters_data))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="tcommenters_byday")
		{
		// Total tcommenters by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			$total_tcommenters_data = $this->Pcomment->query("SELECT DISTINCT user_id FROM Pcomments WHERE Pcomments.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomments.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
			echo (count($total_tcommenters_data))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="tags_byday")
		{
		// Total tags by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			echo ($this->Tag->findCount("Tag.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Tag.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'"))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="taggers_byday")
		{
		// Total taggers by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			$total_taggers_data = $this->Tagger->query("SELECT DISTINCT user_id FROM Taggers WHERE Taggers.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Taggers.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
			echo (count($total_taggers_data))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="sprites_byday")
		{
		// Total sprites by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			$total_sprites_data = $this->Project->query("SELECT SUM(numberOfSprites) FROM Projects WHERE Projects.`created` > '$FROMTIMESTAMP' AND Projects.`created` <= '$TOTIMESTAMP'");
			echo ($total_sprites = $total_sprites_data[0][0]['SUM(numberOfSprites)'])."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="scripts_byday")
		{
		// Total scripts by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			$total_scripts_data = $this->Project->query("SELECT SUM(totalScripts) FROM Projects WHERE Projects.`created` > '$FROMTIMESTAMP' AND Projects.`created` <= '$TOTIMESTAMP'");
			echo ($total_scripts = $total_scripts_data[0][0]['SUM(totalScripts)'])."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="flags_byday")
		{
		// Total flags by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			echo ($this->Flagger->findCount("Flagger.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Flagger.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'"))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="loves_byday")
		{
		// Total loves by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			echo ($this->Lover->findCount("Lover.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Lover.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'"))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	
		if($element=="friendships_byday")
		{
		// Total friendships by day
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("M j Y", $current_day).",";
			echo ($this->Relationship->findCount("Relationship.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Relationship.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'"))."\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
		}
	}	
	
		
	function all_byday($FROMDATE=NULL, $TODATE=NULL)
	{
		set_time_limit(666); 
		$this->autoRender = false;
			
		// Initialize parameters
		if($FROMDATE==NULL)
		{
			$FROMDATE="0000-00-00";
		}
		if($TODATE==NULL)
		{
			$TODATE=date("Y-m-d");
		}
		$FROMTIMESTAMP = $FROMDATE." 00:00:00";
		$TOTIMESTAMP = $TODATE." 00:00:00";
		$total_days = (strtotime($TOTIMESTAMP)-strtotime($FROMTIMESTAMP))/(24*60*60);
		//echo "total_days:$total_days,$FROMDATE,$TODATE"; exit;
		$current_day = strtotime($FROMTIMESTAMP);
		$next_day = $current_day+(60*60*24);
		echo 'date,projects,pcomments,tcomments,pcommenters,tcommenters,tags,taggers,sprites,scripts,flaggers,loveits,relationships'."\n";
		for($day=0; $day<$total_days+1; $day++)
		{
			echo date("Y/m/d", $current_day).",";
			echo ($this->Project->findCount("Project.`created` > '".date("Y-m-d 00:00:00",$current_day)."' AND Project.`created` <= '".date("Y-m-d 00:00:00",$next_day)."'")).",";
			echo ($this->Pcomment->findCount("Pcomment.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomment.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'")).",";
			echo ($this->Tcomment->findCount("Tcomment.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Tcomment.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'")).",";
			$total_pcommenters_data = $this->Pcomment->query("SELECT DISTINCT user_id FROM Pcomments WHERE Pcomments.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomments.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
			echo (count($total_pcommenters_data)).",";
			$total_tcommenters_data = $this->Pcomment->query("SELECT DISTINCT user_id FROM Pcomments WHERE Pcomments.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Pcomments.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
			echo (count($total_tcommenters_data)).",";
			echo ($this->Tag->findCount("Tag.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Tag.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'")).",";
			$total_taggers_data = $this->Tagger->query("SELECT DISTINCT user_id FROM Taggers WHERE Taggers.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Taggers.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'");
			echo (count($total_taggers_data)).",";
			$total_sprites_data = $this->Project->query("SELECT SUM(numberOfSprites) FROM Projects WHERE Projects.`timestamp` > '$FROMTIMESTAMP' AND Projects.`timestamp` <= '$TOTIMESTAMP'");
			echo ($total_sprites = $total_sprites_data[0][0]['SUM(numberOfSprites)']).",";
			$total_scripts_data = $this->Project->query("SELECT SUM(totalScripts) FROM Projects WHERE Projects.`timestamp` > '$FROMTIMESTAMP' AND Projects.`timestamp` <= '$TOTIMESTAMP'");
			echo ($total_scripts = $total_scripts_data[0][0]['SUM(totalScripts)']).",";
			echo ($this->Flagger->findCount("Flagger.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Flagger.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'")).",";
			echo ($this->Lover->findCount("Lover.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Lover.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'")).",";
			echo ($this->Relationship->findCount("Relationship.`timestamp` > '".date("Y-m-d 00:00:00",$current_day)."' AND Relationship.`timestamp` <= '".date("Y-m-d 00:00:00",$next_day)."'"));
			echo "\n";
			$current_day += (60*60*24);
			$next_day += (60*60*24);
		}
	}

	function all_users() {
		set_time_limit(9999999); 
		ini_set('memory_limit','500M');
		$users = $this->User->findAll('villager=0', 'id,username,created,gender,byear,bmonth,country,role','created asc');
		// connect to forums db	
		$dbh = mysql_connect('localhost', 'root', '')  or die("Unable to connect to mysql");
		$selected = mysql_select_db('betaforums',$dbh) or die("Could not select $db_name");
		echo "username,id,create,gender,byear,bmonth,country,role,projects,pcomments,gcomment,favorites,downloads,flags,loveits,saves,shares,relationships,tags,galleries,galleriesowned,views,fuser,fposts,ftopics\n";
		foreach($users as $user) {
			$user['User']['country'] = str_replace(',','',$user['User']['country']);
			echo "{$user['User']['username']},{$user['User']['id']},{$user['User']['created']},{$user['User']['gender']},{$user['User']['byear']},{$user['User']['bmonth']},{$user['User']['country']},{$user['User']['role']},";
			$select = "select count(*) as count from";  $where = "where user_id = {$user['User']['id']}";
			$out = $this->Project->query("$select projects $where");
			$projects = $out[0][0]['count'];
			echo "{$out[0][0]['count']},";
			$out = $this->Pcomment->query("$select pcomments $where");
			echo "{$out[0][0]['count']},";
			$pcomments = $out[0][0]['count'];
			$out = $this->Tcomment->query("$select tcomments $where");
			echo "{$out[0][0]['count']},";
			$out = $this->Favorite->query("$select favorites $where");
			echo "{$out[0][0]['count']},";
			$out = $this->Downloader->query("$select downloaders $where");
			echo "{$out[0][0]['count']},";
			$out = $this->Flagger->query("$select flaggers $where");
			echo "{$out[0][0]['count']},";
			$out = $this->Lover->query("$select lovers $where");
			echo "{$out[0][0]['count']},";
			$out = $this->ProjectSave->query("$select project_saves $where");
			echo "{$out[0][0]['count']},";
			$out = $this->ProjectShare->query("$select project_shares $where");
			echo "{$out[0][0]['count']},";
			$out = $this->Relationship->query("$select relationships $where");
			echo "{$out[0][0]['count']},";
			$out = $this->Tagger->query("$select taggers $where");
			echo "{$out[0][0]['count']},";
			$out = $this->ThemeMembership->query("$select theme_memberships $where");
			echo "{$out[0][0]['count']},";
			$out = $this->Theme->query("$select themes $where");	
			echo "{$out[0][0]['count']},";
			$out = $this->ViewStat->query("$select view_stats $where");
			echo "{$out[0][0]['count']},";
			$isinbb = mysql_query("select count(*) from punbb_users where username='{$user['User']['username']}'");
			if(mysql_result($isinbb, 0, 0)) {
				echo "1,";
				$numposts = mysql_query("select count(*) from punbb_posts where poster='" . $user['User']['username'] . "'");
				echo mysql_result($numposts,	0,0) . ",";
				$numtopics = mysql_query("select count(*) from punbb_topics where poster='" . $user['User']['username'] . "'");
				echo mysql_result($numtopics,	0,0);
			} else {
				echo "0,0,0";
			}
			echo "\n";
		}
		mysql_close($dbh);	
		exit;
	}

	function remixed_interactions() {
		set_time_limit(9999999); 
		ini_set('memory_limit','400M');
		$rmxs = "select 
	p.project_id as remix_id, p.related_project_id as oproject_id, 
	pt1.name as remix_name, pt2.name as oproject_name, 
	pt1.description as remix_desc, pt2.description as oproject_desc, 

	pt1.numberOfSprites as remix_sprites, pt2.numberOfSprites as oproject_sprites, 
	pt1.totalScripts remix_scripts,  pt2.totalScripts as oproject_scripts,

	u1.id as remixer_id, u2.id as originator_id, 
	u1.username as remixer_name, u2.username as originator_name,
	u1.gender as remixer_gender,  u2.gender as originator_gender, 
	u1.byear as remixer_byear, u2.byear originator_byear, 
	u1.bmonth as remixer_bmonth,  u2.bmonth as originator_bmonth
	from 
	project_shares p, users u1, users u2, projects pt1, projects pt2   
	where
	pt1.id = p.project_id and pt2.id = p.related_project_id  and p.user_id = u1.id  and p.related_user_id = u2.id 
	and  p.project_id != p.related_project_id and p.user_id != p.related_user_id 
	and p.project_id
	group by p.project_id, p.user_id  order by p.id asc 
";

		// find all remixed projects
		$rmxps = $this->Project->query($rmxs);
		echo "remix_project,remixer_age,remixer_gender,original_project,originator_age,originator_gender,";
		echo "rmxr2ocrtr,ocrtr2rmxr,similarty,flags_rmx2orig,flags_orig2rmx\n";
		foreach ($rmxps as $rmxp) {
			
		#	echo print_r($rmxp);
			$remix_id = $rmxp['p']['remix_id'];
			$oproject_id = $rmxp['p']['oproject_id'];
			$originator_id = $rmxp['u2']['originator_id'];
			$remixer_id = $rmxp['u1']['remixer_id'];
			$remixer_name = $rmxp['u1']['remixer_name'];
			$remixer_age = $age = date("Y") - $rmxp['u1']['remixer_byear'];
			if ($rmxp['u1']['remixer_bmonth'] - date('m') >= 0)
				$remixer_age--;
			$originator_age = $age = date("Y") - $rmxp['u2']['originator_byear'];
			if ($rmxp['u2']['originator_bmonth'] - date('m') >= 0)
				$originator_age--;
			echo "$remixer_name/$remix_id,";
			echo "$remixer_age,{$rmxp['u1']['remixer_gender']},";
			echo "/{$rmxp['u2']['originator_name']}/$oproject_id,";
			echo "$originator_age,{$rmxp['u2']['originator_gender']},";
			
			$comments_remixer2originator = $this->Pcomment->findAll("Pcomment.project_id = $oproject_id AND Pcomment.user_id = $remixer_id", 'content, timestamp', "Pcomment.timestamp asc");
			$all_comments_remixer2originator = '';
			foreach($comments_remixer2originator as $cr2c) {
				//$all_comments_remixer2originator .= " @" . $cr2c['Pcomment']['timestamp'] . ':' . $cr2c['Pcomment']['content'];
				$all_comments_remixer2originator .=  $cr2c['Pcomment']['content'] . "~ " ;
			}
			$all_comments_remixer2originator = str_replace(array(',',"\r\n","\n","\r"), '',  $all_comments_remixer2originator);
			echo "$all_comments_remixer2originator,";

			$comments_originator2remix = $this->Pcomment->findAll("Pcomment.project_id = $remix_id AND Pcomment.user_id = $originator_id", 'content, timestamp', "Pcomment.timestamp asc");
			$all_comments_originator2remixer  = '';
			foreach($comments_originator2remix as $cc2r) {
				//$all_comments_originator2remixer .= " @" . $cc2r['Pcomment']['timestamp'] . ':' . $cc2r['Pcomment']['content'];
				$all_comments_originator2remixer .=  $cc2r['Pcomment']['content'] . "~ ";
			}
			$all_comments_originator2remixer = str_replace(array(',',"\r\n","\n","\r"), '', $all_comments_originator2remixer);
			echo "$all_comments_originator2remixer,";
			// calculation of similarity
			if ($rmxp['pt1']['remix_sprites'] == $rmxp['pt2']['oproject_sprites'] 
				&& $rmxp['pt1']['remix_scripts'] == $rmxp['pt2']['oproject_scripts']){
				$path = "/llk/scratchr/beta/app/webroot/static/projects";
				$remix_path = "$path/$remixer_name/$remix_id.sb";
				$oproject_path = "$path/{$rmxp['u2']['originator_name']}/$oproject_id.sb";
				if (abs(filesize($remix_path) - filesize($oproject_path)) < 3000) {
					echo "likely_same,";
				} else {
					echo "maybe_different,";
				}
			} else {
				echo "different,";
			}

			$flags_rmx2orig = $this->Flagger->findAll("Flagger.project_id = $oproject_id AND Flagger.user_id = $remixer_id", 'reasons');
			if(count($flags_rmx2orig) > 0) {
				if ($flags_rmx2orig[0]['Flagger']['reasons']) {
					echo str_replace(',', '', $flags_rmx2orig[0]['Flagger']['reasons']);
				} 
				echo ",";
			} else {
			 	echo "N/A,";
			}

			$flags_orig2rmx = $this->Flagger->findAll("Flagger.project_id = $remix_id AND Flagger.user_id = $originator_id", 'reasons');
			if(count($flags_orig2rmx) > 0) {
				if ($flags_orig2rmx[0]['Flagger']['reasons']) {
					echo str_replace(',', '', $flags_orig2rmx[0]['Flagger']['reasons']);
				} 
				echo ",";
			} else {
			 	echo "N/A,";
			}
			echo "\n";
		}
		echo "***THE END****";
		exit;
	}
/*
		$remixedprojs = $this->RemixedProject->findAll(null, null, null, 10);
		foreach ($remixedprojs as $rmxp) {
			//echo print_r($rmxp);
			$rmxid = $rmxp['RemixedProject']['id'] ;
			$this->RemixedProject->id = $rmxp['RemixedProject']['id'];
			$oproject_id = $rmxp['RemixedProject']['oproject_id'];
			$remixer_id  = $rmxp['RemixedProject']['remixer_id'];
			$ocreator_name = $rmxp['RemixedProject']['ocreator_name'];
			$remix_name;
			$oproject_name;
			if (samescripts)
				same programming
				if(samesprites)
					same 
					very similar
					if(similar samename)
						almost the same
						if(similar desc)
						even more


			$comments_remixer2ocreator = $this->Pcomment->findAll("Pcomment.project_id = $oproject_id AND Pcomment.user_id = $remixer_id", 'content, timestamp', "Pcomment.timestamp asc");

			echo "^^^id:$rmxid^^\n";
			$all_comments_remixer2ocreator = '';
			foreach($comments_remixer2ocreator as $cr2c) {
				$all_comments_remixer2ocreator .= " @" . $cr2c['Pcomment']['timestamp'] . ':' . $cr2c['Pcomment']['content'];
				# echo "content: " . $cr2c['Pcomment']['content'] . "\n<br>";
			}
			if($all_comments_remixer2ocreator) {
			#	echo "remixer2creator:http://scratch.mit.edu/projects/$ocreator_name/$oproject_id<br>\n";
			#	echo "$all_comments_remixer2ocreator\n<br>";
				$this->RemixedProject->saveField('remixer2ocreator', $all_comments_remixer2ocreator);
			}

			$remix_id = $rmxp['RemixedProject']['remix_id'];
			$ocreator_id  = $rmxp['RemixedProject']['ocreator_id'];
			$remixer_name = $rmxp['RemixedProject']['remixer_name'];
			$comments_creator2remix = $this->Pcomment->findAll("Pcomment.project_id = $remix_id AND Pcomment.user_id = $ocreator_id", 'content, timestamp', "Pcomment.timestamp asc");
			$all_comments_ocreator2remixer  = '';
			foreach($comments_creator2remix as $cc2r) {
				#echo "content: " . $cc2r['Pcomment']['content'] . "\n<br>";
				$all_comments_ocreator2remixer .= " @" . $cc2r['Pcomment']['timestamp'] . ':' . $cc2r['Pcomment']['content'];
			}
			if ($all_comments_ocreator2remixer) {
			#	echo "creator2remixer:http://scratch.mit.edu/projects/$remixer_name/$remix_id:<br>\n";
			#	echo "$all_comments_ocreator2remixer\n<br>";
				$this->RemixedProject->saveField('ocreator2remixer', $all_comments_ocreator2remixer);
			}
			echo "********************\n<br>";
		} 

		exit;
	}
*/
	function remixes() {
		set_time_limit(9999999); 
		ini_set('memory_limit','400M');
		$remixes = $this->ProjectShare->findAll("related_project_id != project_id group by project_id and related_username = 'andresmh', user_id", NULL, 'id', 10);
                echo "id, projectid,userid,rmxedprojectid,rmxeeid,date,sameuser,";
		echo "sprites,scripts,";
		echo "rmxedsprites,rmxedscripts,";
		echo "diffsprites,diffscripts,";
		echo "psize,pfsize,phsize,";
		echo "rmxpsize,rmxfsize,rmxphsize,diffrealsize\n";
		$missrpsize = 0 ; $misspsize = 0; $missremix = 0; $missproject = 0;
		foreach ($remixes as $remix) {        
                        $pid = $remix['ProjectShare']['project_id'];
                        $uid = $remix['ProjectShare']['user_id'];
                        $rpid = $remix['ProjectShare']['related_project_id'];   
                        $ruid = $remix['ProjectShare']['related_user_id'];
                        $rusrn = $remix['ProjectShare']['related_username'];
                        echo "{$remix['ProjectShare']['id']},$pid,$uid,$rpid,$ruid,{$remix['ProjectShare']['date']},";
			if ($remix['ProjectShare']['user_id'] ==  $remix['ProjectShare']['related_user_id'])
				echo "1,";
			else 
				echo "0,";
                        $this->Project->bindUser();     
                        $project = $this->Project->findById($pid);
                        if ($project) {  
                                echo $project['Project']['numberOfSprites'] . ",";
                                echo $project['Project']['totalScripts'] . ",";
                        	$rproject = $this->Project->findById($rpid);
				if ($rproject) {
                                	echo $rproject['Project']['numberOfSprites'] . ",";
                                	echo $rproject['Project']['totalScripts'] . ",";
					echo $rproject['Project']['numberOfSprites'] - $project['Project']['numberOfSprites'] . ",";
					echo $rproject['Project']['totalScripts'] - $project['Project']['totalScripts'] . ",";

					$psizes = $this->getpsizes($pid,$project['User']['username']);
					$rpsizes = $this->getpsizes($rpid,$rproject['User']['username']);
					if (sizeof($psizes) == 3) {
						echo implode(',', $psizes) . ",";
						if (sizeof($psizes) == 3)  {
							echo implode(',', $rpsizes) . ",";
							$diffsize = $psizes[0] - $rpsizes[0] ;
							echo $diffsize;
						}
						else {
							$missrpsize++;
						}
					} else {
						$misspsize++;
					}
				} else {
					$this->log("cannot find rpid:$rpid");
					$missremix++;
				}
			} else {
				$missproject++;
				$this->log("cannot find pid:$pid");
			}
			echo "\n";
		}
		$this->log("missrpsize:$missrpsize,misspsize:$misspsize,missremix:$missremix,missproject:$missproject");
		exit;
	}

	function getpsizes($pid,$username) {
		$path = getcwd() . "/static/projects/$username/$pid.sb";
		if(! file_exists($path)) {
			$this->log("cannot find $path");
			return;
		}
		$jar = "/llk/scratchr/beta/app/misc/historyextraction/HeaderAnalyzer.jar";
		exec("java -jar $jar h $path", $retvals);
		preg_match("/^header (\d+)$/", $retvals[0], $matches);
		$sizewheaders = filesize($path);
		$headers = $matches[1];
		$real = $sizewheaders - $headers;
		$ret = array($real, $sizewheaders, $headers);
		return $ret;
	}

	function pflags ($fromdate=NULL, $todate=NULL, $limit = 5) {
		$this->autoRender = false;
		$fromtimestamp = $fromdate . " 00:00:00";
		$totimestamp = $todate . " 00:00:00";
		$flaggers = null;
		$flagger_ids = $this->Project->query("SELECT user_id, project_id, reasons, timestamp FROM `flaggers` WHERE `timestamp` > '$fromtimestamp' AND `timestamp` <= '$totimestamp' ORDER BY timestamp ASC");// LIMIT $limit");
		foreach ($flagger_ids as $flagger_id_record)
		{
			$project_url = 'DELETED';
			$pstatus  = 'DELETED';
			$flagger_id = $flagger_id_record['flaggers']['user_id'];
			$project_id = $flagger_id_record['flaggers']['project_id'];
			$reasons = $flagger_id_record['flaggers']['reasons'];
			$timestamp = $flagger_id_record['flaggers']['timestamp'];
			$flagger = $this->User->find("User.id=$flagger_id");
			$project = $this->Project->findReal("Project.id=$project_id");
			$pstatus = $project['Project']['proj_visibility'];
			$puser_name = $project['User']['username'];
			$flagger_name = $flagger['User']['username'];
			$flagger_byear = $flagger['User']['byear'];
			$flagger_byear = $flagger['User']['bmonth'];
			$flagger_gender = $flagger['User']['gender'];
			$age = -1;
			if (date('m', time()) >  $flagger['User']['bmonth']) {
                                $age =  date('Y', time()) - $flagger['User']['byear'] - 1;
                        } else {
                                $age = date('Y', time()) - $flagger['User']['byear'];
                        }
			if ($puser_name) {
				$project_url = "http://scratch.mit.edu/projects/$puser_name/$project_id";
			}
			$reasons = ereg_replace("'","",$reasons);
			$reasons = ereg_replace(",","",$reasons);
			
			if($age == 2007) {
				$age = 'UNAVAILABLE';
			}
			echo "$reasons,$flagger_name,$flagger_id,$project_url,$age,$flagger_gender,$pstatus,$timestamp";
			echo "\n";
		}
		exit;
	}

}
?>
