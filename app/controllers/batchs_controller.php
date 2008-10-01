<?php

class BatchsController extends AppController {

    var $uses = array('Mpcomment','Project','Tagger','FeaturedProject', 'User','Pcomment','ViewStat','ProjectTag', 'Tag','Lover', 'Favorite', 'Downloader','Flagger', 'Notification', 'ProjectShare', 'ProjectSave');
    var $components = array('RequestHandler','Pagination', 'Email');


	function notifyglitch() {
		$badps = array(27866 => 'OJY321', 27866 => 'OJY321', 27867 => 'OJY321', 27963 => 'sephiroph', 27964 => 'hgaman116', 27965 => 'samir321', 27966 => 'RAYMEN29', 27963 => 'sephiroph', 27963 => 'sephiroph', 27967 => 'sephiroph', 27968 => 'samir321', 27969 => 'caleb9798', 27970 => 'robbie', 27971 => 'mkolpnji', 27972 => 'chelsealee', 27973 => 'juangabriel', 27974 => 'lyssa295', 27975 => 'cbbc', 27976 => 'Troom0', 27977 => 'chelsealee', 27974 => 'lyssa295', 27975 => 'cbbc', 27978 => 'Troom0', 27979 => 'millamat', 27980 => 'natalie', 27981 => 'natalie', 27980 => 'natalie', 27982 => 'prinsissangel', 27983 => 'natalie', 27984 => 'piepiedude', 27985 => 'kittykat98', 27986 => 'stallian999', 27987 => 'natalie', 27988 => 'caddo1975', 27989 => 'Mick', 27990 => 'jmo1', 27991 => 'natalie', 27992 => 'CE_group01', 27993 => 'Mick', 27994 => 'ericks', 27995 => 'cbbc', 27996 => 'Jens', 27997 => 'RAYMEN29', 27998 => 'sephiroph', 27999 => 'DragonGirl', 28000 => 'ericks', 28001 => 'daeheryn', 28002 => 'Mick', 27999 => 'DragonGirl', 28003 => 'OJY321', 28004 => 'daeheryn', 27999 => 'DragonGirl', 28005 => 'daeheryn', 28006 => 'DragonGirl', 28007 => 'Jasper', 27988 => 'caddo1975', 28008 => 'MldLogan', 28009 => 'natalie9511', 27697 => 'mman', 28010 => 'MldLogan', 28011 => 'retireddevil', 28012 => 'retireddevil', 28013 => 'DragonGirl', 28014 => 'retireddevil', 28015 => 'retireddevil', 28016 => 'retireddevil', 28017 => 'retireddevil', 28018 => 'pedroskivich', 28013 => 'DragonGirl', 28019 => 'inferno', 28020 => 'Scratch-This', 28021 => 'matnanosh', 28022 => 'sonicmarsh', 28023 => 'sean1111', 28020 => 'Scratch-This', 28024 => 'pooper993', 28025 => 'mman', 28026 => 'emarcos', 28027 => 'YAYALEC', 28028 => 'jonnie339', 28029 => 'MahoAshley', 28030 => 'scout18509', 28031 => 'RAYMEN29', 28032 => 'Zombien', 28033 => 'BabbageHaxor', 28034 => 'HopeD', 28035 => 'Deweybears', 28036 => 'sephiroph', 28034 => 'HopeD', 28037 => 'Swimgal123', 28038 => 'ganov', 28039 => 'Deweybears', 28040 => 'HopeD', 28041 => 'mman', 28042 => 'Brainee1', 28043 => 'mman', 28044 => 'Brainee1', 28045 => 'HopeD', 28046 => 'Deweybears', 28047 => 'Deweybears', 28048 => 'HopeD', 28049 => 'Deweybears', 28050 => 'Deweybears', 28051 => 'Kaydoodle13', 28052 => 'Bluecrystal', 28053 => 'HopeD', 28054 => 'MahoAshley', 28055 => 'elise6699', 28056 => 'The-Whiz', 28057 => 'HazelleafKitty', 27866 => 'OJY321', 27866 => 'OJY321', 27867 => 'OJY321', 27963 => 'sephiroph', 27964 => 'hgaman116', 27965 => 'samir321', 27966 => 'RAYMEN29', 27963 => 'sephiroph', 27963 => 'sephiroph', 27967 => 'sephiroph', 27968 => 'samir321', 27969 => 'caleb9798', 27970 => 'robbie', 27971 => 'mkolpnji', 27972 => 'chelsealee', 27973 => 'juangabriel', 27974 => 'lyssa295', 27975 => 'cbbc', 27976 => 'Troom0', 27977 => 'chelsealee', 27974 => 'lyssa295', 27975 => 'cbbc', 27978 => 'Troom0', 27979 => 'millamat', 27980 => 'natalie', 27981 => 'natalie', 27980 => 'natalie', 27982 => 'prinsissangel', 27983 => 'natalie', 27984 => 'piepiedude', 27985 => 'kittykat98', 27986 => 'stallian999', 27987 => 'natalie', 27988 => 'caddo1975', 27989 => 'Mick', 27990 => 'jmo1', 27991 => 'natalie', 27992 => 'CE_group01', 27993 => 'Mick', 27994 => 'ericks', 27995 => 'cbbc', 27996 => 'Jens', 27997 => 'RAYMEN29', 27998 => 'sephiroph', 27999 => 'DragonGirl', 28000 => 'ericks', 28001 => 'daeheryn', 28002 => 'Mick', 27999 => 'DragonGirl', 28003 => 'OJY321', 28004 => 'daeheryn', 27999 => 'DragonGirl', 28005 => 'daeheryn', 28006 => 'DragonGirl', 28007 => 'Jasper', 27988 => 'caddo1975', 28008 => 'MldLogan', 28009 => 'natalie9511', 27697 => 'mman', 28010 => 'MldLogan', 28011 => 'retireddevil', 28012 => 'retireddevil', 28013 => 'DragonGirl', 28014 => 'retireddevil', 28015 => 'retireddevil', 28016 => 'retireddevil', 28017 => 'retireddevil', 28018 => 'pedroskivich', 28013 => 'DragonGirl', 28019 => 'inferno', 28020 => 'Scratch-This', 28021 => 'matnanosh', 28022 => 'sonicmarsh', 28023 => 'sean1111', 28020 => 'Scratch-This', 28024 => 'pooper993', 28025 => 'mman', 28026 => 'emarcos', 28027 => 'YAYALEC', 28028 => 'jonnie339', 28029 => 'MahoAshley', 28030 => 'scout18509', 28031 => 'RAYMEN29', 28032 => 'Zombien', 28033 => 'BabbageHaxor', 28034 => 'HopeD', 28035 => 'Deweybears', 28036 => 'sephiroph', 28034 => 'HopeD', 28037 => 'Swimgal123', 28038 => 'ganov', 28039 => 'Deweybears', 28040 => 'HopeD', 28041 => 'mman', 28042 => 'Brainee1', 28043 => 'mman', 28044 => 'Brainee1', 28045 => 'HopeD', 28046 => 'Deweybears', 28047 => 'Deweybears', 28048 => 'HopeD', 28049 => 'Deweybears', 28050 => 'Deweybears', 28051 => 'Kaydoodle13', 28052 => 'Bluecrystal', 28053 => 'HopeD', 28054 => 'MahoAshley', 28055 => 'elise6699', 28056 => 'The-Whiz', 28057  => 'HazelleafKitty', 27627 => 'andresmh', 1231=>'adada', 123 => 'andresmh');
		$countdeleted = 0;
		$counterror   = 0;
		$countstillthere = 0;
		foreach($badps as $pid => $username) {
			$this->Project->id = $pid;
			$project = $this->Project->read();
			if (empty($project)) {
				$countdeleted++;
				$user = $this->User->find(array('username' => $username));
				$userid = $user['User']['id'];
				$msg = 'Due to a glitch in our website one of your projects was not uploaded correctly and it was being shown as if it was censored. We hope you can <strong>upload your project again</strong>. Our sincere <strong>apologies</strong>. <a href="/contact/us">Contact us</a> if you have any questions.';
			} else {
				$countstillthere++;
				$userid = $project['Project']['user_id'];
				$name = $project['Project']['name'];
				$msg = "Due to a software glitch in our server, your project <a href='/projects/$username/$pid'>$name</a> was not uploaded correctly and it is being shown as if it was censored. We hope you can <strong>upload your project again</strong>. Our sincere <strong>apologies</strong>. The Scratch Administrators.";
			} 

			if(empty($userid)) {
				$counterror++;
				echo "<font color='red'>Cannot find userid for pid:$pid,username:$username</font><br>\n";
			} else {
				$this->Notification->save(array('Notification'=>array("user_id"=>$userid, "custom_message"=>$msg, "status"=>'unread')));
				echo "Notified:user_id=>$userid,custom_message=>$msg<br>\n";
			}
		}
		echo "<br><hr>deleted:$countdeleted, errors: $counterror, successful:$countstillthere<br>";
		exit;
	}
	
	// Counts number of scripts and sprites
	// for those projects where both values are null
	function countscriptsandsprites($count = null) {
		if(empty($count)) { $count = 300; }
		$projects = $this->Project->findAll(Array('totalScripts' => NULL, 'numberOfSprites' => NULL), Array('id','user_id'), 'id DESC', $count);
		$total = 0;
		foreach($projects as $project) {
			$projectid = $project['Project']['id'];
			echo "pid:$projectid\n";
			$user = $this->User->findById($project['Project']['user_id']);
			$jar = "java -jar /home/llk/ScratchAnalyzer.jar";
			$sbfilepath = '/llk/scratchr/beta/app/webroot/static/projects/' . $user['User']['username'] . "/$projectid.sb";
			if (file_exists($sbfilepath)) {
				unset($retval);
				exec("$jar $sbfilepath", $retval);
				$sc_sp = explode("\t", $retval[0]);
				$this->Project->id = $projectid;
				$this->Project->saveField('totalScripts', $sc_sp[0]);
				$this->Project->saveField('numberOfSprites', $sc_sp[1]);
				$total++;
			} else {
				echo "notexists:$sbfilepath\n";
			}
		}
		echo "counted:$total.";
		exit;
	}

	// finds projects with no .sb file (censored and deleted)
	function findorphans() {
		$projects = $this->Project->findAll(NULL,  Array('id','user_id'));
		$total = 0;
               foreach($projects as $project) {
                        $projectid = $project['Project']['id'];
                        $user = $this->User->findById($project['Project']['user_id']);
                    	$sbfilepath = '/llk/scratchr/beta/app/webroot/static/projects/' . $user['User']['username'] . "/$projectid.sb";
                        if (! file_exists($sbfilepath)) {
                                echo $user['User']['username'] . "/$projectid" . "\n";
				$total++;
                        }
                }
                echo "counted:$total.";
                exit;
        }

	// calculates the number of scripts and sprites from .sb file and populates the db with it
	// needed because scratch was not sending that info 
	function calc() {
		$path  = '/llk/scratchr/beta/app/webroot/static/projects';
		$jar = "java -jar /home/llk/ScratchAnalyzer.jar";
		$counter = 0;
		foreach (new DirectoryIterator($path) as $file) {
   			// if the file is not this file, and does not start with a '.' or '..',
   			// then store it for later display
   			if ( (!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF'])) ) {
      				// if the element is a directory add to the file name "(Dir)"
				$username = $file->getFilename();
      				if($file->isDir()) {
					foreach (new DirectoryIterator("$path/$username") as $file2) {
   						if ( (!$file2->isDot()) && ($file2->getFilename() != basename($_SERVER['PHP_SELF'])) ) {
							$fname = $file2->getFilename();
							if (preg_match("/^([0-9]+)\.sb$/",$fname,$matches)) {
								$projectid = $matches[1];
      								$fullpath =  "$path/$username/$fname";
								unset($retval);
								exec("$jar $fullpath", $retval);
								$arr = explode("\t", $retval[0]);
								$sprites = $arr[0]; $scripts = $arr[1]; $sbfile = $arr[2];
								echo "pid:$projectid,fp:$fullpath:sprites:$sprites,scripts:$scripts.<br>\n";
								$this->Project->save(array('id' => $projectid,
												'totalScripts' => $scripts, 
												'numberOfSprites' => $sprites));
								$counter++;
							}
						}
					}
				}
   			}
		}
		echo "TOTAL:$counter<br>\n";
		exit;
	}
	
	// extract project history and puts in the db for lots of projects at once
	// batch process
	function extracthistoryall() {

	$inc = 100;
	$m = 27349;
	$x = $m + 1000 - $inc;
	$total = 0;
	for($min = $m; $min <= $x; $min += $inc) {
		$max = $min + $inc;
		echo "^^^min:$min,max:$max<br>\n";
		$this->log("^^^min:$min,max:$max<br>\n");
		$projects = $this->Project->findAll("Project.related_username is null and Project.id <= $max  and Project.id > $min", NULL, 'Project.id'); 
		//$projects = $this->Project->findAll(NULL, NULL, 'Project.id', $limit); 
		$this->log( "count:"  . count($projects));
		//print_r($projects);

		foreach($projects as $project) {
			$project_shared_id  = $project['Project']['id'];
			$user_shared_id = $project['Project']['user_id'];
			echo"project_shared_id:$project_shared_id,user_shared_id:$user_shared_id<br>\n";	
			$this->extracthistory($project_shared_id, $user_shared_id);
			$total++;
		}
		$total += $total;
	}
		echo"\n<br>TOTAL:$total.<br>\n";
		$this->log("\n<br>TOTAL:$total.<br>\n");
        	exit;
	}
	// extracts history for a specific project owned by a specific user
	// uses java extractor
	function extracthistory($project_shared_id, $user_shared_id) {
		//$this->log("extracting for $project_shared_id, $user_shared_id");
		$ppath = '/llk/scratchr/beta/app/webroot/static/projects/';
//		$ppath = 'z:/scratchr/app/webroot/static/projects/';
		$jar = "/llk/scratchr/beta/app/misc/historyextraction/ScratchAnalyzer.jar";
//		$jar = "z:/scratchr/app/misc/historyextraction/ScratchAnalyzer.jar";
		$jar = escapeshellcmd($jar);
        $powner = $this->User->findById($user_shared_id);
		$sbfilepath =  $ppath . $powner['User']['username'] . "/" . $project_shared_id . ".sb";		
		if (! file_exists($sbfilepath)) {
			echo("\n<br>!NOTFOUND!:$sbfilepath<br>\n");
			$this->log("!NOTFOUND!:$sbfilepath");
			return false;
		} else {
			$sbfilepath = escapeshellcmd($sbfilepath);
		}
	
		unset($retvals);
		exec("java -jar $jar h $sbfilepath", $retvals);
		if(count($retvals)) {
			//echo "java output:"; print_r($retvals); echo "<br>\n";
		} else {
			echo("failed executing java program: $ret<br>");
			return false; 
		}

		foreach ($retvals as $retval) {
			if(! $this->isempty($retval)) {
				$this->storehistory($project_shared_id, $user_shared_id, $retval);
			}
		}
		
		$condition = "user_id = '$user_shared_id' AND project_id = '$project_shared_id' AND related_user_id != user_id"; 
		$relprojects = $this->ProjectShare->findAll($condition, "id, related_username, related_user_id, related_project_id, related_project_name", "id desc");
		foreach ($relprojects as $relproject) {
			if ($relproject['ProjectShare']['related_username']) {
				$this->Project->id = $project_shared_id;			
				$newinfo = array('id' => $project_shared_id, 'related_project_id' => $relproject['ProjectShare']['related_project_id'], 'related_username' => $relproject['ProjectShare']['related_username']);
				//print_r($newinfo);
				$this->Project->save($newinfo);
				return true;
			}
		}
				
		return true;
	}
	
	// stores history information 
	// input: tab delimited string with values (coming from java analyzer)
	function storehistory($project_shared_id, $user_shared_id, $retval) {
		echo("storehistory:project_shared_id:$project_shared_id, user_shared_id:$user_shared_id, retval:<font color='blue'>$retval</font><br>\n");
		//return;
		$retval = str_replace('!undefined!', '', $retval);
		list($date, $event, $pname , $username, $savername) = explode("\t", $retval);
		
		// fix problem with times that are 24 instead of 00 for midnight times
		if(! $this->isempty($date)) { 
			if (preg_match("/^(\d{4}-\d{1,2}-\d{1,2}) (\d{2}):(.+)$/", $date, $matches) && $matches[2] > 23) {
				$hour = $matches[2] % 24;
				$date = preg_replace("/^(\d{4}-\d{2}-\d{1,2}) (\d{1,2}):(.+)$/","$1 $hour:$3", $date); 
			}
		}
		
		$record = array(
				'id'					=> null, 
				'project_id'			=> $project_shared_id,	// project shared
				'user_id' 				=> $user_shared_id,		// user who shared this project
				'related_project_name'	=> $pname,				// name of modded project 
				'related_username'		=> $username,			// scratchr username of who uploaded modded project
				'related_savername'		=> $savername,			// author name when storing locally
				'date'					=> $date				// date of event
				);
		// For sharing events
		if ($event == 'share') {
			echo"#";
			if ($this->isempty($pname) || $this->isempty($username)) {
				// No point in adding record if there is no way to refer to another project
				echo("\n<br>!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username<br>\n");
				$this->log("!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username");
			} else {
				// echo"-";
				// look for ids to have more linkable info
				echo"looking for id for username:$username<br>\n";
				if ($eventuser = $this->User->find(array('username' => $username))) {
					$record['related_user_id']  = $eventuser['User']['id'];
					 echo"/";
					 echo"found id for $username = " . $record['related_user_id'] . "<br>\n";
					echo "looking for project...";
					if ($citedproject = $this->Project->find(array(
							'user_id'	=> $record['related_user_id'], 
							'name'		=> $pname))
						) 
					{
						$record['related_project_id'] =  $citedproject['Project']['id'];
						 echo"*";
					}
				}
				echo"share-record:<b>";  print_r($record);  echo"</b><br>\n";
				if($this->ProjectShare->save($record)) {
					echo"."; 
				} else {
					 echo"\n<br>!COULDNTSAVE!:";
					 $this->log("!COULDNTSAVE!:" . print_r($record, true));
					 print_r($record);
					 echo"<br>\n";
				}
			}
		}
		// For saves, olds and empty
		elseif($event == 'save' || $this->isempty($event) || $event == 'old' ) {
			echo"+";
			if ($this->isempty($date)) {
				echo("\n<br>!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username,savername:$savername<br>\n");	
			} else {
				// echo"-";
				if (! $this->isempty($savername)) {
					// look for ids to have more linkable info
					if($saveuserobj = $this->User->find(array('username' => $savername))) {
						$record['related_saver_id'] = $saveuserobj['User']['id'];
						if($record['related_saver_id']) {
							// echo"/";
							if ($saveproject = $this->Project->find(array(
									'user_id' => $record['related_saver_id'],  'name' => $pname))) {
								$record['related_project_id'] = $saveproject['Project']['id'];
								// echo"*";
							}
						}
					}
				}	
				// echo("save-record:<b>"); // print_r( $record); echo("</b><br>\n");					
				if($this->ProjectSave->save($record)) {
						echo"."; 
				} else {
					// echo"\n<br>!COULDNTSAVE!:";
					// print_r($record);
					// echo"<br>\n";
				}
			}
		}
		else {
			echo("\n<br>!WRONGEVENT!:date:$date,event:$event,pname:$pname,username:$username,savername:$savername<br>\n");							
		}
		return true;
	}
	
		
	function isempty($var) {
		if (((is_null($var) || rtrim($var) == "") && $var !== false) || (is_array($var) && empty($var)) ) {
			return true;
		} else {
			return false;
		}
	}
}
?>