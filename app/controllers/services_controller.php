<?php

/**
 * Rest/XML service return err codes
 */
define("INVALID_REQUEST", 400);
define("INVALID_USER", 401);
define("INVALID_PROJECT_REQUEST", 402);
define("DB_SAVE_ERROR", 403);
define("BINARY_UPLOAD_ERROR", 404);
define("THUMBNAIL_UPLOAD_ERROR", 405);
define("UNSUPPORTED_SERVICE", 406);
define("INVALID_PROJECT", 407);
define("USER_BLOCKED_ERROR", 408);
define("IP_BLOCKED_ERROR", 409);
Class ServicesController extends AppController {
	// var $components = array("Security");
    var $uses = array("Project", "User", "ProjectTag", "Tag", "Notification", 'ProjectShare', 'ProjectSave','ProjectScript','BlockedIp','Remix');
	var $helpers = array('Javascript', 'Ajax', 'Html', 'Pagination');
	 var $components = array('RequestHandler','Pagination', 'Email', 'PaginationSecondary','Thumb');
    var $doc = null;
    var $service = null;
    var $debug = null;

    var $err_codes = array(
        INVALID_REQUEST => "invalid request, file might be too big",
        INVALID_USER => "invalid user",
		UNSUPPORTED_SERVICE => "service not yet available",
		INVALID_PROJECT => "project might have been censored or deleted.",
		
        );

    function beforeFilter() {
	if ($this->action == 'share_project') {
	$this->autoRender = true;
	}
	else
	{
        $this->autoRender = false;
        $this->autoLayout = false;
        $this->service = $service = $this->params['action'];

        // setup xml & http return headers
        if (isset($this->passed_args[0])) {
            if ($this->passed_args[0] == 'debug') {
                pr("POST_VARS:");
                pr($_POST);
                e("\n\n");
                pr("REQUEST_INFO:");
                pr($this->params);
//                $this->log("POST_VARS:");
//                $this->log($_POST);
//                $this->log("\n\n");
//                $this->log("REQUEST_INFO:");
//                $this->log($this->params);
                $this->debug = true;
            }
        } else {
            header('Content-Type: text/xml charset=UTF-8');
        }

        $doc = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
        $this->doc = $doc . "<scratchr-$service>";

        // $this->Security->requirePost('upload');
        if ((count($this->params['url']) > 1) ||
            (count($this->passed_args) > 1) ||
            !$this->__validServicePostArgs()) {
            $this->__failed(INVALID_REQUEST);
            $this->afterFilter();
            die;
        }
	  }//$this->action	
    }
	
	function __addError($code, $msg) {
		$this->err_codes[$code] = $msg;
	}

    function __failed($error_code) {
        $msg = $this->err_codes[$error_code];
        $this->doc = $this->doc . "<status>failed</status>" . "<reason code=\"$error_code\">$msg</reason>";
    }

    function __success() {
        $this->doc = $this->doc . "<status>success</status>";
    }

    function afterFilter() {
	if ($this->action == 'share_project') {
	}
	else
	{
        // write xml return footer
        $this->doc = $this->doc . "</scratchr-" . $this->service . ">";
        echo($this->doc);
        exit();
	}	
  }


    // clients should really send an encrypted scratch key
    function __validServicePostArgs() {
        return (isset($_POST) &&
            !empty($this->params['form']['scratchkey']) &&
                !empty($this->params['form']['username']) &&
                    !empty($this->params['form']['password']) &&
                        $this->params['form']['scratchkey'] === SCRATCH_KEY);
    }
	
	
	function __setUploadErrCodes() {
		$this->err_codes[DB_SAVE_ERROR] = "error saving to database";
		$this->err_codes[BINARY_UPLOAD_ERROR] = "project might be too big (maximum is 10MB).";
		$this->err_codes[DB_SAVE_ERROR] = "unable to save info to database";
		$this->err_codes[THUMBNAIL_UPLOAD_ERROR] = "unable to upload thumbnail file";
	}
	
	
	/**
	@author ashok gond
	share project
	*/
	function share_project()
	{	
		$this->pageTitle = ___("Scratch | Share project", true);
		$user_record = $this->Session->read('User');
        if (empty($user_record)) {
			$this->setFlash(___("You need to login to upload", true), FLASH_NOTICE_KEY);
			$this->redirect('/login');
        }
		if(!empty($this->data['Project']['more_tags1']))
		array_push($this->data['Project']['tags'],$this->data['Project']['more_tags1']);
		if(!empty($this->data['Project']['more_tags2']))
		array_push($this->data['Project']['tags'],$this->data['Project']['more_tags2']);
		if(!empty($this->data['Project']['more_tags3']))
		array_push($this->data['Project']['tags'],$this->data['Project']['more_tags3']);
		if(!empty($this->data['Project']['more_tags4']))
		array_push($this->data['Project']['tags'],$this->data['Project']['more_tags4']);
		
		if(!empty($this->data))
		{	
			$project_name =  (!empty($this->data['Project']['project_name'])) ? trim($this->data['Project']['project_name']) : null;
			
			$binary_file = (!empty($this->params["form"]["binary_file"])) ? $this->params["form"]["binary_file"]:null;
        	$thumbnail_file = (!empty($this->params["form"]["priview_image"])) ? $this->params["form"]["priview_image"]:null;
        	$preview_file = (!empty($this->params["form"]["priview_image"])) ? $this->params["form"]["priview_image"]:null;
		
			// binary file required
			if (!isset($binary_file['error']) || $binary_file['error']) 
			{
				$this->setFlash(___("Select a scratch file", true));
					$this->redirect('/services/share_project');
				
			}
			$banary_file_type = explode('.',$binary_file['name']);
			if($banary_file_type['1'] !='sb')
			{
					$this->setFlash(___("Select a scratch file with extension .sb only", true));
					$this->redirect('/services/share_project');
			}
			
			//Make sure  Scratch file contains string "Scratch"
			$fp=fopen($binary_file['tmp_name'],"r");
			$content=fread($fp,10);
			fclose($fp);
			$pos=strpos($content,'Scratch');
			if($pos===0){
			}
			else{
					$this->setFlash(___("Invalid scratch file", true));
					$this->redirect('/services/share_project');
			}
			
			
			if (!isset($preview_file['error']) || $preview_file['error']) 
			{
				$this->setFlash(___("Select a priview image", true));
					$this->redirect('/services/share_project');
				
			}
			if($preview_file['type'] == "image/png"){
			}
			else{
					$this->setFlash(___("Upload only a PNG image", true));
					$this->redirect('/services/share_project');
			}
			if (empty($project_name)) 
			{
				
				$this->setFlash(___("Please Enter Project name", true));
				$this->redirect('/services/share_project');
				
			}
		
        // get project info
        $user_id = $user_record['id'];
		$user_status = $user_record['status'];
        $urlname = $user_record['urlname'];
		
		//check if user is banned
		if ($user_status != 'locked') {
			$inappropriates = array();
			
			// save project data
			$project = null;
			
			if(isInappropriate($project_name))
			{
				$project_name = "Untitled";
				$inappropriates[] = 'inappropriate_ptitle_upload';
				
			}
			$project_description = (!empty($this->data['Project']['project_description'])) ? trim($this->data['Project']['project_description']) : null;
			
			$project_text_Scripts = (!empty($this->params['form']['allScripts'])) ? trim($this->params['form']['allScripts']) : null;
			if(isInappropriate($project_description))
			{
				$project_description = "";
				$inappropriates[] = 'inappropriate_pdesc_upload';
			}
			
			$project = $this->Project->find("Project.name = "."'".$project_name."' AND user_id = ". $user_id);
			$project_id = null;
			$new_project = null;
			$project_version = null;
			
			if (!empty($project)) 
			{
				// update existing
				$project_id = $project['Project']['id'];
				$project_version = ((int)$project['Project']['version']); // + 1;
				$this->Project->id = $project_id;
				$data['Project']['version'] = $project_version + 1;
				if($project['Project']['proj_visibility']=='delbyadmin' || $project['Project']['proj_visibility']=='censbyadmin' ||$project['Project']['proj_visibility']=='censbycomm')
				{
					$this->setFlash(___("Invalid Project", true), FLASH_NOTICE_KEY);
					$this->redirect('/services/share_project');
				}
				else
				{
					$data['Project']['proj_visibility'] = "visible";
				}	
			} 
			else 
			{
				// create new project
				$Project->id = null;
				$data['Project']['name'] = $project_name;
				$data['Project']['user_id'] = $user_id;
				$data['Project']['version'] = 1;
				$new_project = true;
			}
			
			if ($project_description)
				$data['Project']['description'] = $project_description;
			
			if ($this->data['Project']['version_date'])
				$data['Project']['scratch_version_date'] = $this->data['Project']['version_date'];

		/* performs INET_ATON on the IP string aaa.bbb.ccc.ddd, converts to 4
		4 byte int (long). To view in sql do 
		USE mysql;
		SELECT INET_NTOA(longintval); */	
		if ($_SERVER['REMOTE_ADDR'])
			$data['Project']['uploader_ip'] = ip2long($_SERVER['REMOTE_ADDR']);
			
			if (!$this->Project->save($data['Project'])) {
				$this->setFlash(___("Error with project saving", true));
				$this->redirect('/services/share_project');
			}
			
			
				if ($new_project)
					$project_id = $this->Project->getLastInsertID();
					
				//Save allScripts content to table project_scripts.
				$project_text_info = array('project_id' => $project_id, 'text_scripts' => $project_text_Scripts);
				$this->ProjectScript->save($project_text_info);	
					
				// upload binary file
				$bin_file = WWW_ROOT . getBinary($urlname, $project_id, false, DS);
				mkdirR(dirname($bin_file) . DS);
				/**
				For write log to app/tmp/project_$projectid.log during sharing project.
				*/
				if(WRITE_LOG == 1){
				$current_date = date('d M,Y')."\n";
				$root_path = APP.'tmp'.'/'.'project'.'_'.$project_id.'.log';
				$fh = fopen($root_path,'w');
				$str = str_repeat("-", 20)."\n";
				fwrite($fh,$current_date);
				fwrite($fh,$str);
				fwrite($fh, print_r($this->params,true));
				fwrite($fh,$str);
				fclose($fh);
				}
				if (!$new_project)
				{
					// rename old version
					$new_file = WWW_ROOT . getBinary($urlname, $project_id . "." . $project_version, false, DS);
					rename($bin_file, $new_file);
				}
				
				if (!move_uploaded_file($binary_file['tmp_name'], $bin_file)) {
				$this->setFlash(___("Binary upload error", true));
				$this->redirect('/services/share_project');
			}
					
			if (isset($preview_file['error']) && !$preview_file['error']) {
				 $med_thumbnail_file = WWW_ROOT . getThumbnailImg($urlname, $project_id, 'medium', false, DS);
				mkdirR(dirname($med_thumbnail_file) . DS);
				$this->Thumb->resizeThumb($preview_file,$med_thumbnail_file ,480,360);
				
			}
			
			if (isset($thumbnail_file['error']) && !$thumbnail_file['error']) {
				 $sm_thumbnail_file = WWW_ROOT . getThumbnailImg($urlname, $project_id, 'mini', false, DS);
				mkdirR(dirname($sm_thumbnail_file) . DS);
				$this->Thumb->resizeThumb($thumbnail_file,$sm_thumbnail_file,133,100);
				 
			}
				// process tags
			if (!empty($this->data['Project']['tags'])) {
				
				
				foreach ($this->data['Project']['tags'] as $tag) {
					$ntag = strtolower(trim($tag));
					if (!strcmp($ntag,""))
						continue;
						
					if(isInappropriate($ntag))
					{
						$this->notify('inappropriate_ptag_upload', $user_id,
										array('project_id' => $project_id,
										'project_owner_name' => $urlname),
										array($ntag));
						continue;
					}
					$this->Tag->bindProjectTag(array('project_id' => $project_id));
					$tag_record = $this->Tag->find("name = '$ntag'",null,null,2);

					if (!empty($tag_record)) {
						if (empty($tag_record['ProjectTag'])) {
							// create project_tag record
							$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id' => $project_id, 'tag_id' =>$tag_record['Tag']['id'], 'user_id' => $user_id)));
							$this->ProjectTag->id=null;
						}
					} else {
						// create tag record
						$this->Tag->save(array('Tag'=>array('name'=>$ntag)));
						$this->Tag->id=null; // otherwise things will be overridden
						$tag_id = $this->Tag->getLastInsertID();
						$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id'=>$project_id, 'tag_id'=>$tag_id, 'user_id' => $user_id)));
						$this->ProjectTag->id=null;
					}
				}
			}
				foreach($inappropriates as $inappropriate) {
				$this->notify($inappropriate, $user_id,
					array('project_id' => $project_id));
			}
			
			$this->extracthistory($project_id, $user_id);
			
			$this->redirect('/users/'.$user_record['urlname']);
			
		}//check if user is banned	
	}//$this->data
		
	}//function
	
	
	
	
	
	function upload() {
		//$this->__addError(UNSUPPORTED_SERVICE, "upload not yet available");
		//$this->__failed(UNSUPPORTED_SERVICE);
		$this->__upload();
	}
	
	
	function admin_upload() {
		$this->__upload();
	}
	
	
    /**
     * Direct rest upload service
     */
    function __upload() {
		$this->__setUploadErrCodes();		
		
		$submit_username = $this->params['form']['username'];
        $submit_pwd = $this->params['form']['password'];
        $user_record = $this->User->findByUsername($submit_username);
		$client_ip = ip2long($this->RequestHandler->getClientIP());
		$IP = long2ip($client_ip);
		$ip_count = $this->BlockedIp->findCount("ip = $client_ip");
		
		if ($ip_count > 0){
			$this->err_codes[IP_BLOCKED_ERROR]="Unable to accept project because the IP address '$IP' has been blocked.";
			$this->__failed(IP_BLOCKED_ERROR);
			return;
		}
        if (empty($user_record['User']['password']) || $user_record['User']['password'] !== sha1($submit_pwd)) 
		{
            $this->__failed(INVALID_USER);
            return;
        }
        
        $binary_file = (!empty($this->params["form"]["binary_file"])) ? $this->params["form"]["binary_file"]:null;
        $thumbnail_file = (!empty($this->params["form"]["thumbnail_image"])) ? $this->params["form"]["thumbnail_image"]:null;
        $preview_file = (!empty($this->params["form"]["preview_image"])) ? $this->params["form"]["preview_image"]:null;
		
		// binary file required
		if (!isset($binary_file['error']) || $binary_file['error']) 
		{
			$this->__failed(BINARY_UPLOAD_ERROR);
			return;
		}
		
        // get project info
        $user_id = $user_record['User']['id'];
		$user_status = $user_record['User']['status'];
        $urlname = $user_record['User']['urlname'];
		
		//check if user is banned
		if ($user_status != 'locked') {
			$inappropriates = array();
			
			// save project data
			$project = null;
			$project_name =  (!empty($this->params['form']['project_name'])) ? trim($this->params['form']['project_name']) : null;
			if(isInappropriate($project_name))
			{
				$project_name = "Untitled";
				$inappropriates[] = 'inappropriate_ptitle_upload';
				
			}
			$project_description = (!empty($this->params['form']['project_description'])) ? trim($this->params['form']['project_description']) : null;
			if(isInappropriate($project_description))
			{
				$project_description = "";
				$inappropriates[] = 'inappropriate_pdesc_upload';
			}
			$project_numberOfSprites = (!empty($this->params['form']['numberOfSprites'])) ? trim($this->params['form']['numberOfSprites']) : null;
			$project_totalScripts = (!empty($this->params['form']['totalScripts'])) ? trim($this->params['form']['totalScripts']) : null;
			$project_text_Scripts = (!empty($this->params['form']['allScripts'])) ? trim($this->params['form']['allScripts']) : null;
			if($this->params['form']['hasSoundSensorBlocks']=='false')
			$project_has_sound_blocks = 0;
			else
			$project_has_sound_blocks = 1;
			if($this->params['form']['hasScratchBoardSensorBlocks']=='false')
			$project_has_sensorboard_blocks = 0;
			else
			$project_has_sensorboard_blocks = 1;
			
			$project_scratch_version_date = (!empty($this->params['form']['version-date'])) ? trim($this->params['form']['version-date']) : null;
			
			if (empty($project_name)) 
			{
				$this->__failed(INVALID_PROJECT_REQUEST);
				return;
			}
			

				 
			$project = $this->Project->find("Project.name = "."'".$project_name."' AND user_id = ". $user_id);
			$project_id = null;
			$new_project = null;
			$project_version = null;
			
			if (!empty($project)) 
			{
				// update existing
				$project_id = $project['Project']['id'];
				$project_version = ((int)$project['Project']['version']); // + 1;
				$this->Project->id = $project_id;
				$this->data['Project']['version'] = $project_version + 1;
				if($project['Project']['proj_visibility']=='delbyadmin' || $project['Project']['proj_visibility']=='censbyadmin' ||$project['Project']['proj_visibility']=='censbycomm')
				{
					$this->__failed(INVALID_PROJECT);
				return;
				}
				else
				{
					$this->data['Project']['proj_visibility'] = "visible";
				}	
			} 
			else 
			{
				// create new project
				$this->Project->id = null;
				$this->data['Project']['name'] = $project_name;
				$this->data['Project']['user_id'] = $user_id;
				$this->data['Project']['version'] = 1;
				$new_project = true;
			}
			
			if ($project_description)
				$this->data['Project']['description'] = $project_description;
			if ($project_numberOfSprites)
				$this->data['Project']['numberOfSprites'] = $project_numberOfSprites;
			if ($project_totalScripts)
				$this->data['Project']['totalScripts'] = $project_totalScripts;
			
				$this->data['Project']['has_sound_blocks'] = $project_has_sound_blocks;
				$this->data['Project']['has_sensorboard_blocks'] = $project_has_sensorboard_blocks;
			if ($project_scratch_version_date)
				$this->data['Project']['scratch_version_date'] = $project_scratch_version_date;
				
			

		/* performs INET_ATON on the IP string aaa.bbb.ccc.ddd, converts to 4
		4 byte int (long). To view in sql do 
		USE mysql;
		SELECT INET_NTOA(longintval); */	
		if ($_SERVER['REMOTE_ADDR'])
			$this->data['Project']['uploader_ip'] = ip2long($_SERVER['REMOTE_ADDR']);
		
			if (!$this->Project->save($this->data['Project'])) {
				$this->__failed(DB_SAVE_ERROR);
				return;
			}

			if ($new_project)
				$project_id = $this->Project->getLastInsertID();
				
			//Save allScripts content to table project_scripts.
			$project_text_info = array('project_id' => $project_id, 'text_scripts' => $project_text_Scripts);
			$this->ProjectScript->save($project_text_info);
			
			// upload binary file
			$bin_file = WWW_ROOT . getBinary($urlname, $project_id, false, DS);
			mkdirR(dirname($bin_file) . DS);
			/**
			For write log to app/tmp/project_$projectid.log during sharing project.
			*/
			if(WRITE_LOG == 1){
			$current_date = date('d M,Y')."\n";
			$root_path = APP.'tmp'.'/'.'project'.'_'.$project_id.'.log';
			$fh = fopen($root_path,'w');
			$str = str_repeat("-", 20)."\n";
			fwrite($fh,$current_date);
			fwrite($fh,$str);
			fwrite($fh, print_r($this->params,true));
			fwrite($fh,$str);
			fclose($fh);
			}
			if (!$new_project)
			{
				// rename old version
				$new_file = WWW_ROOT . getBinary($urlname, $project_id . "." . $project_version, false, DS);
				rename($bin_file, $new_file);
			}
			if (!move_uploaded_file($binary_file['tmp_name'], $bin_file)) {
				$this->__failed(BINARY_UPLOAD_ERROR);
				return;
			}

			if (isset($thumbnail_file['error']) && !$thumbnail_file['error']) {
				$sm_thumbnail_file = WWW_ROOT . getThumbnailImg($urlname, $project_id, 'mini', false, DS);
				mkdirR(dirname($sm_thumbnail_file) . DS);
				if (!move_uploaded_file($thumbnail_file['tmp_name'], $sm_thumbnail_file)) {
					$this->__failed(THUMBNAIL_UPLOAD_ERROR);
					return;
				}
			}

			if (isset($preview_file['error']) && !$preview_file['error']) {
				$med_thumbnail_file = WWW_ROOT . getThumbnailImg($urlname, $project_id, 'medium', false, DS);
				mkdirR(dirname($med_thumbnail_file) . DS);
				if (!move_uploaded_file($preview_file['tmp_name'], $med_thumbnail_file)) {
					$this->__failed(THUMBNAIL_UPLOAD_ERROR);
					return;
				}
			}

			// process tags
			if (!empty($this->params['form']['tags'])) {
				
				$tagsarray = explode(",",htmlspecialchars($this->params['form']['tags']));
				foreach ($tagsarray as $tag) {
					$ntag = strtolower(trim($tag));
					if (!strcmp($ntag,""))
						continue;
						
					if(isInappropriate($ntag))
					{
						$this->notify('inappropriate_ptag_upload', $user_id,
										array('project_id' => $project_id,
										'project_owner_name' => $urlname),
										array($ntag));
						continue;
					}
					$this->Tag->bindProjectTag(array('project_id' => $project_id));
					$tag_record = $this->Tag->find("name = '$ntag'",null,null,2);

					if (!empty($tag_record)) {
						if (empty($tag_record['ProjectTag'])) {
							// create project_tag record
							$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id' => $project_id, 'tag_id' =>$tag_record['Tag']['id'], 'user_id' => $user_id)));
							$this->ProjectTag->id=null;
						}
					} else {
						// create tag record
						$this->Tag->save(array('Tag'=>array('name'=>$ntag)));
						$this->Tag->id=null; // otherwise things will be overridden
						$tag_id = $this->Tag->getLastInsertID();
						$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id'=>$project_id, 'tag_id'=>$tag_id, 'user_id' => $user_id)));
						$this->ProjectTag->id=null;
					}
				}
			}
			
			//set notifications
			foreach($inappropriates as $inappropriate) {
				$this->notify($inappropriate, $user_id,
					array('project_id' => $project_id));
			}
			//$this->log("extracting for:$project_id, $user_id...");
			$this->extracthistory($project_id, $user_id, $newproject);
			//$this->log("done extracthistory");
			$this->doc = $this->doc . "<pid>$project_id</pid>";
			$this->__success();
			
			$this->doc = $this->doc . "<pid>$project_id</pid>";
			$this->__success();
		} else {
			
			$this->err_codes[USER_BLOCKED_ERROR]="Unable to accept project because the account '$urlname' has been blocked.";
			$this->__failed(USER_BLOCKED_ERROR);
			return;
		}
    }

	/**
	* sends notification to specified user
	*/
	function notify($type, $to_user_id, $data, $extra = array()) {
		//store the notification
		App::import('Model', 'Notification');
		$this->Notification =& ClassRegistry::init('Notification');
		$this->Notification->addNotification($type, $to_user_id,
											$data, $extra);
	}
	
	// THE FOLLOWING METHODS SHOULD BE IN THE MODEL OR CONTROLLER OF PROJECT
	// but couldn't call them remotely
	// extracts history for a specific project owned by a specific user
	// uses java extractor
	function extracthistory($project_shared_id, $user_shared_id, $newproject=null) {
//		$this->log("extracting for $project_shared_id, $user_shared_id");
		$ppath = APP.'webroot/static/projects/';
		//$ppath = '/llk/scratchr/production/app/webroot/static/projects/';
		//$ppath = 'e:/scratchr/app/webroot/static/projects/';
		$jar = APP."misc/historyextraction/ScratchAnalyzer.jar";
		//$jar = "/llk/scratchr/production/app/misc/historyextraction/ScratchAnalyzer.jar";
		//$jar = "e:/scratchr/app/misc/historyextraction/ScratchAnalyzer.jar";
		$jar = escapeshellcmd($jar);
        $powner = $this->User->findById($user_shared_id);
		$sbfilepath =  $ppath . $powner['User']['username'] . "/" . $project_shared_id . ".sb";		
//		$this->log("checking if $sbfilepath exists");
		if (! file_exists($sbfilepath)) {
			$this->log("\n<br>!NOTFOUND!:$sbfilepath<br>\n");
			return false;
		} else {
			$sbfilepath = escapeshellcmd($sbfilepath);
		}
//		$this->log("file exists");
	
		unset($retvals);
		exec("java -jar $jar h $sbfilepath", $retvals);
		if(count($retvals)) {
			//echo "java output:"; print_r($retvals); echo "<br>\n";
		} else {
			$this->log("failed executing java program: $ret<br>");
			return false; 
		}
//$this->log(print_r($retvals,true));
		
		foreach ($retvals as $retval) {
			if(! $this->isempty($retval)) {
				$this->storehistory($project_shared_id, $user_shared_id, $retval);
				
			}
			
		}
		
		$revretvals = array_reverse($retvals);
		foreach ($revretvals as $retval) {
		$retval = str_replace('!undefined!', '', $retval);
		list($date, $event, $pname , $username, $savername) = explode("\t", $retval);
		if ($event == 'share') {
		if ($this->isempty($pname) || $this->isempty($username)) {
				// No point in adding record if there is no way to refer to another project
				$this->log("\n<br>!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username<br>\n");
			} else {
			$eventuser = $this->User->find(array('username' => $username),'id');
			$eventuser_id = $eventuser['User']['id'];
			$citedproject = $this->Project->find(array('user_id' => $eventuser_id,'name' => $pname));
			$citedproject_id =$citedproject['Project']['id'];
			if($project_shared_id != $citedproject_id  || $user_shared_id != $eventuser_id){
					$this->storeremix($project_shared_id, $citedproject_id);
					break;
				}
			}//else
		}//event
		
		}//foreach revretvals
		
		
		$condition = "user_id = '$user_shared_id' AND project_id = '$project_shared_id' AND related_project_id != project_id"; 
		$relprojects = $this->ProjectShare->findAll($condition, "id, related_username, related_user_id, related_project_id, related_project_name", "id desc");
		foreach ($relprojects as $relproject) {
			if ($relproject['ProjectShare']['related_username']) {
				$this->Project->id = $project_shared_id;			
				$newinfo = array('id' => $project_shared_id,
                                  'based_on_pid' => $relproject['ProjectShare']['related_project_id'],
                                );
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
		//$this->log("storehistory:project_shared_id:$project_shared_id, user_shared_id:$user_shared_id, retval:<font color='blue'>$retval</font><br>\n");
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
			// echo"#";
			if ($this->isempty($pname) || $this->isempty($username)) {
				// No point in adding record if there is no way to refer to another project
				$this->log("\n<br>!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username<br>\n");
			} else {
				// echo"-";
				// look for ids to have more linkable info
				// echo"looking for id for username:$username<br>\n";
				if ($eventuser = $this->User->find(array('username' => $username))) {
					$record['related_user_id']  = $eventuser['User']['id'];
					// echo"/";
					// echo"found id for $username = " . $record['related_user_id'] . "<br>\n";
					if ($citedproject = $this->Project->find(array(
							'user_id'	=> $record['related_user_id'], 
							'name'		=> $pname))
						) 
					{
						$record['related_project_id'] =  $citedproject['Project']['id'];
						// echo"*";
					}
				}
				// echo"share-record:<b>"; // print_r($record); // echo"</b><br>\n";
				if($this->ProjectShare->save($record)) {
					$related_project_id = $record['related_project_id'];
					$this->Project->id = $related_project_id;
					$current_related_project = $this->Project->read();
					if (empty($current_related_project)) {
					} else {
						$total_remixes = $current_related_project['Project']['remixes'] + 1;
						
						$this->Project->saveField("remixes", $total_remixes);
						$project_record = $this->Project->find(array('name' => $pname));
						$project_record_id = $project_record['Project']['id'];
						$project_share_count = $this->ProjectShare->findCount("ProjectShare.related_project_id = $project_record_id AND ProjectShare.user_id = $user_shared_id AND ProjectShare.project_id!=ProjectShare.related_project_id");
						
						if($project_share_count ==1)
						{
							$total_remixer = $project_record['Project']['remixer'] + 1;
							$this->Project->id = $project_record_id;
							$this->Project->saveField("remixer", $total_remixer);
						}
					}
				} else {
					// echo"\n<br>!COULDNTSAVE!:";
					// print_r($record);
					// echo"<br>\n";
				}
			}
		}
		// For saves, olds and empty
		elseif($event == 'save' || $this->isempty($event) || $event == 'old' ) {
			// echo"+";
			if ($this->isempty($date)) {
				$this->log("\n<br>!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username,savername:$savername<br>\n");	
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
						// echo"."; 
				} else {
					// echo"\n<br>!COULDNTSAVE!:";
					// print_r($record);
					// echo"<br>\n";
				}
			}
		}
		else {
			$this->log("\n<br>!WRONGEVENT!:date:$date,event:$event,pname:$pname,username:$username,savername:$savername<br>\n");							
		}
		return true;
	}


	function storeremix($remix_project_id, $original_project_id) {
		$record = array('id'=> null, 'remix_project_id'	=> $remix_project_id, 'original_project_id' => $original_project_id);
		$remix_count = $this->Remix->findCount(array('remix_project_id'=>$remix_project_id, 'original_project_id' => $original_project_id));
		if($remix_count ==0)
		$this->Remix->save($record);
				
	}//function
	
	
	function isempty($var) {
		if (((is_null($var) || rtrim($var) == "") && $var !== false) || (is_array($var) && empty($var)) ) {
			return true;
		} else {
			return false;
		}
	}


	
	
	/*--------------------------------------------------------*
    Sample Response:
    <?xml version="1.0" encoding="utf-8" ?>
    <scratchr-upload>
    <status>failed</status>
    <reason code="200">db connection failed</status>
    </scratchr-upload>
    
    Codes:
    Error: 400, 401, 402, 403...
    Success: 0
    
    Sample Response (deprecated):
    <?xml version="1.0" encoding="utf-8" ?>
    <scratchr-upload>
    <state>failed</state>
    <reason>db connnection failed</reason>
    </scratchr-upload>
    
    <?xml version="1.0" encoding="utf-8" ?>
    <scratchr-upload>
    <pid>23</pid>
    <state>success</state>
    </sratchr-upload>
    *--------------------------------------------------------*/
}


?>
