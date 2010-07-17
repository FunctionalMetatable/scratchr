<?php
/*
 *  Sample Response:
 *     <?xml version="1.0" encoding="utf-8" ?>
 *     <scratchr-upload>
 *     <status>failed</status>
 *     <reason code="200">db connection failed</status>
 *     </scratchr-upload>
 *
 *  Codes:
 *  Error: 400, 401, 402, 403...
 *  Success: 0
 *
 */

/*
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
define("USER_DELETED_ERROR", 410);

Class ServicesController extends AppController {

    var $uses = array("Project", "User", "ProjectTag", "Tag", "Notification", 'ProjectShare', 'ProjectSave','ProjectScript','BlockedIp', 'RemixNotification');
	var $helpers = array('Javascript', 'Ajax', 'Html', 'Pagination');
	var $components = array('RequestHandler','Pagination', 'Email', 'PaginationSecondary','Thumb','GeoIp');
    var $doc = null;
    var $service = null;
    var $debug = null;

    var $err_codes = array(
        INVALID_REQUEST => "invalid request - project might be larger than 10MB",
        INVALID_USER => "invalid user",
		UNSUPPORTED_SERVICE => "service not yet available",
		INVALID_PROJECT => "project cannot be uploaded again"
    );

    function beforeFilter() {
       if ($this->action == 'share_project') {
            $this->autoRender = true;
        }
        else {
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
                    $this->debug = true;
                }
            }
            else {
                header('Content-Type: text/xml charset=UTF-8');
            }

            $doc = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
            $this->doc = $doc . "<scratchr-$service>";

            if ((count($this->params['url']) > 1) ||
                (count($this->passed_args) > 1) ||
                !$this->__validServicePostArgs()) {
                $this->__failed(INVALID_REQUEST);
                $this->afterFilter();
                exit();
            }
        }//$this->action
    }

    function afterFilter() {
        if ($this->action != 'share_project') {
            // write xml return footer
            $this->doc = $this->doc . "</scratchr-" . $this->service . ">";
            echo($this->doc);
            exit();
        }
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

    // clients should really send an encrypted scratch key
    function __validServicePostArgs() {
        return (isset($_POST)
                && !empty($this->params['form']['scratchkey'])
                && !empty($this->params['form']['username'])
                && !empty($this->params['form']['password'])
                && $this->params['form']['scratchkey'] === SCRATCH_KEY);
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
			$project_name =  null;
			if(!empty($this->data['Project']['project_name'])) {
				$project_name =  strip_tags(trim($this->data['Project']['project_name']));
			}

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
			
			$project_description =  null;
			if(!empty($this->data['Project']['project_description'])) {
				$project_description = strip_tags(trim($this->data['Project']['project_description']));
			}
			
			$project_text_Scripts = (!empty($this->params['form']['allScripts'])) ? trim($this->params['form']['allScripts']) : null;
			if(isInappropriate($project_description))
			{
				$project_description = "";
				$inappropriates[] = 'inappropriate_pdesc_upload';
			}

			$project = $this->Project->find(array('Project.name'=>$project_name, 'Project.user_id'=>$user_id));
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
		$client_ip = $this->RequestHandler->getClientIP();
		if ($client_ip)
			$data['Project']['upload_ip'] = ip2long($client_ip);

			if (!$this->Project->save($data['Project'])) {
				$this->setFlash(___("Error with project saving", true));
				$this->redirect('/services/share_project');
			}


				if ($new_project)
					$project_id = $this->Project->getLastInsertID();

				//Save allScripts content to table project_scripts.
				$project_text_info = array('project_id' => $project_id, 'text_scripts' => $project_text_Scripts);
				$this->ProjectScript->save($project_text_info);

				//set Country name based on ip
				$this->setCountryName(ip2long($client_ip),$project_id);
				
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
					$ntag = strtolower(strip_tags(trim($tag)));
					if (!strcmp($ntag,""))
						continue;

					if(isInappropriate($ntag))
					{
						$this->__notify('inappropriate_ptag_upload', $user_id,
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
				$this->__notify($inappropriate, $user_id,
					array('project_id' => $project_id));
			}

			$this->__extract_data($project_id, $user_id);

			$this->redirect('/users/'.$user_record['urlname']);

		}//check if user is banned
	}//$this->data

	}//function

	function upload() {
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
		
		 // get project info
        $user_id = $user_record['User']['id'];
		$user_status = $user_record['User']['status'];
        $urlname = $user_record['User']['urlname'];
		
		if ($user_status == 'delbyadmin' || $user_status == 'delbyusr'){
			$this->err_codes[USER_DELETED_ERROR]="Unable to accept project because the account '$urlname' has been deleted.";
			$this->__failed(USER_DELETED_ERROR);
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

       

		//check if user is banned
		if ($user_status != 'locked') {
			$inappropriates = array();

			// save project data
			$project = null;
			
			$project_name =  null;
			if(!empty($this->params['form']['project_name'])) {
				$project_name =  strip_tags(trim($this->params['form']['project_name']));
			}
			
			
			if(isInappropriate($project_name))
			{
				$project_name = "Untitled";
				$inappropriates[] = 'inappropriate_ptitle_upload';

			}
			
			$project_description =  null;
			if(!empty($this->params['form']['project_description'])) {
				$project_description = strip_tags(trim($this->params['form']['project_description']));
			}
			
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



			$project = $this->Project->find(array('Project.name'=>$project_name, 'Project.user_id'=>$user_id));
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
				if(($project['Project']['proj_visibility']=='delbyusr' && $project['Project']['status']=='censored') || ($project['Project']['proj_visibility']=='delbyadmin' && $project['Project']['status']=='censored')|| $project['Project']['proj_visibility']=='censbyadmin' || $project['Project']['proj_visibility']=='censbycomm')
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
		if ($client_ip)
			$this->data['Project']['upload_ip'] = $client_ip;

			if (!$this->Project->save($this->data['Project'])) {
				$this->__failed(DB_SAVE_ERROR);
				return;
			}

			if ($new_project)
				$project_id = $this->Project->getLastInsertID();
			
			//Save allScripts content to table project_scripts.
			$project_text_info = array('project_id' => $project_id, 'text_scripts' => $project_text_Scripts);
			$this->ProjectScript->save($project_text_info);
			
			//set Country name based on ip
			$this->setCountryName($client_ip,$project_id);

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
					$ntag = strtolower(strip_tags(trim($tag)));
					if (!strcmp($ntag,""))
						continue;

					if(isInappropriate($ntag))
					{
						$this->__notify('inappropriate_ptag_upload', $user_id,
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
				$this->__notify($inappropriate, $user_id,
					array('project_id' => $project_id));
			}

			$this->__extract_data($project_id, $user_id);
			
			$this->doc = $this->doc . "<pid>$project_id</pid>";
			$this->__success();
		}
        else {

			$this->err_codes[USER_BLOCKED_ERROR]="Unable to accept project because the account '$urlname' has been blocked.";
			$this->__failed(USER_BLOCKED_ERROR);
			return;
		}
    }

	/*
     * sends notification to specified user
     */	
	function __notify($type, $to_user_id, $data, $extra = array()) {
		//store the notification
		App::import('Model', 'Notification');
		$this->Notification =& ClassRegistry::init('Notification');
		$this->Notification->addNotification($type, $to_user_id,
											$data, $extra);
	}

	function __extract_data($project_shared_id, $user_shared_id) {
        $this->log("\nDBG: Extraction Starts: PROJECT-ID: $project_shared_id, USER-ID: $user_shared_id\n");
        $sbfilepath = $this->__get_sbfilepath($project_shared_id, $user_shared_id);
        //file does not exist, we should just give it a break :)
        if(empty($sbfilepath)) {
            return false;
        }

        //running main scratch analyzer and collecitng entries
        $entries = $this->__run_scratch_analyzer($sbfilepath);

        //TODO: need to refactor storehistory()
        //store in project_shares
        foreach ($entries as $entry) {
			if(!$this->__is_empty($entry)) {
                $this->storehistory($project_shared_id, $user_shared_id, $entry);
            }
        }

        $this->__store_based_ons($project_shared_id, $user_shared_id, $entries);

        $this->__run_full_analyzer($sbfilepath);

        $this->log("\nDBG: Extraction Ends: PROJECT-ID: $project_shared_id, USER-ID: $user_shared_id\n");
        return true;
	}

    function __store_based_ons($project_shared_id, $user_shared_id, $entries) {
        //clear the based on cache for this project
        $this->Project->clear_based_on_data($project_shared_id);
        
        if(empty($entries)) { return false; }
        
        //find based_on_pid and root_based_on_pid - reverse way
        $based_on_pid = 0;
        $root_based_on_pid = 0;
        $override_based_on= true;

        for($i = (count($entries) -1); $i >=0; $i--) {
			if($this->__is_empty($entries[$i])) {
                continue;
            }

            $entry = str_replace('!undefined!', '', $entries[$i]);
            $words = explode("\t", $entry);
            //ignore, if it has less than 5 values
            if(count($words) < 5) {
                continue;
            }
            list($date, $event, $projectname, $username, $author) = $words;
            //$this->log("\nDBG: Scanning: $event $projectname $username\n");

            if($event != 'share' || $this->__is_empty($projectname)
                || $this->__is_empty($username)) {
                continue;
            }
            
            //find out the user's id
            $parent_user = $this->User->find(array('username' => $username), 'id');
            if(empty($parent_user)) {
                $this->log("\nDBG: Parent user not found\n");
                continue;
            }
            $parent_uid  = $parent_user['User']['id'];
            $this->log("\nDBG: Parent uid: $parent_uid\n");
            
            //find out the project's id
            $this->Project->recursive = -1;
            $parent_project = $this->Project->find(
                             array('user_id' => $parent_uid, 'name' => $projectname));
            if(empty($parent_project)) {
                $this->log("\nDBG: Parent project not found\n");
                continue;
            }
            $parent_pid  = $parent_project['Project']['id'];
            $this->log("\nDBG: Parent pid: $parent_pid\n");
            $this->log("\nDBG: Uploaded pid: $project_shared_id\n");

            //uploaded project's id is not the same as parent's
            if(!empty($parent_pid)
            && $project_shared_id != $parent_pid) {
                //can override based_on, as we are not yet sure about it
                if($override_based_on) {
                    //gotcha, parent is from different user
                    if($user_shared_id != $parent_uid) {
                        $based_on_pid = $parent_pid;
                        $override_based_on= false;
                    }
                    else {
                        $based_on_pid = $parent_pid;
                    }
                }

                $root_based_on_pid = $parent_pid;
            }            
        }//end for

        if($root_based_on_pid) {
            $project = array('id' => $project_shared_id,
                            'based_on_pid' => $based_on_pid,
                            'root_based_on_pid' => $root_based_on_pid);
            $this->Project->save($project);
            $this->log("\nDBG: SUCCESSFULLY STORED: $project_shared_id "
                    ."is based on $based_on_pid and root is $root_based_on_pid\n");

			if(REMIX_NOTIFICATION_TO_ROOT_BASED) {
                $this->__notify_remix($root_based_on_pid, $project_shared_id);
            }
            $this->__update_remixes_remixer($root_based_on_pid);
            if($based_on_pid != $root_based_on_pid)  {
				//we should send remix notification to only root based
                //if(REMIX_NOTIFICATION_TO_LAST_BASED) {
                 //   $this->__notify_remix($based_on_pid, $project_shared_id);
                //}
                $this->__update_remixes_remixer($based_on_pid);
            }
        }
        else {
            $this->log("\nDBG: SUCCESSFULLY ANALYZED: $project_shared_id "
                    ."has no based on \n");
        }
    }

    function __run_scratch_analyzer($sbfilepath) {
        $jar = APP."misc/historyextraction/ScratchAnalyzer.jar";
        return $this->__run_analyzer($jar, $sbfilepath, 'h');
    }

    function __run_full_analyzer($sbfilepath) {
        $this->log("\nDBG: Running Full Analyzer \n");
        config('database');
        $db =& new DATABASE_CONFIG();
        
        if(!isset($db->analysis)) {
            return false;
        }

        $db = $db->analysis;
        if(empty($db['host']) || empty($db['database']) || empty($db['login'])) {
            return false;
        }
        
        $jar = APP."misc/historyextraction/analyzer.jar";
        $arg = '"' . $db['host'] . ':' . $db['port'] . '/' . $db['database']
                . '?user=' . $db['login'] . '&password=' . $db['password'].'"';
        $arg .= ' -f -r "' . $db['host'] . ':' . $db['port'] . '/' .  'beta' 
                . '?user=' . $db['login'] . '&password=' . $db['password'].'"';
        return $this->__run_analyzer($jar, $sbfilepath, $arg);
    }
    
    function __run_analyzer($jar, $sbfilepath, $arg) {
        $sbfilepath = escapeshellcmd($sbfilepath);
        $sbfilepath = '"'.$sbfilepath.'"';
        $jar        = escapeshellcmd($jar);
        
        $exec = JAVA_PATH . ' -jar ' . $jar . '  ' . $arg . ' ' . $sbfilepath;
        $this->log("\nDBG: Executing: $exec\n");
        unset($entries);
        exec("LANG=en_US.utf-8; $exec 2>&1", $entries, $err);

        $output = join("\n", $entries);
        if($err || empty($entries)) {
            $this->log("\nERR: Analyzer returns error: $output\n");
            return false;
        }
        $this->log("\nDBG: Analyzer returns: $output\n");
        return $entries;
    }

    function __get_sbfilepath($project_shared_id, $user_shared_id) {
        $ppath = APP.'webroot/static/projects/';
        $powner = $this->User->findById($user_shared_id);
        $sbfilepath =  $ppath . $powner['User']['username'] . "/" . $project_shared_id . ".sb";

        if (!file_exists($sbfilepath)) {
            $this->log("\nERR: .SB NOT FOUND: $sbfilepath\n");
            return false;
        }
        $this->log("\nDBG: .SB FILE FOUND: $sbfilepath\n");
        return $sbfilepath;
    }

    function __update_remixes_remixer($pid) {
        $this->Project->recursive = -1;
        $project = $this->Project->find('based_on_pid = ' . $pid . ' OR root_based_on_pid = ' . $pid,
                                        'COUNT(*) AS remixes, COUNT(DISTINCT user_id) AS remixer');
        $project[0]['id'] = $pid;
        $this->Project->save($project[0]);
        $this->log("\nDBG: SUCCESSFULLY UPDATED: " . $pid
            . ' has ' .$project[0]['remixes'] . ' remixes and '
            . $project[0]['remixer'] . " remixer \n");
    }

	function __set_remix_notification_type($user_id) {
		$notification_types = array('positive', 'neutral', 'generosity', 'conformity', 'reputation', 'nonotification');
		$this->RemixNotification->mc_connect();

		$counter = $this->RemixNotification->mc_get('remix_notification_counter');
		$counter = (int) $counter;
		$index = $counter % count($notification_types);
		$counter++;
		$this->RemixNotification->mc_set('remix_notification_counter', $counter);
		
		$this->RemixNotification->save(array('user_id' => $user_id, 'ntype' => $notification_types[$index]));

		$this->RemixNotification->mc_close();

		return $notification_types[$index];
	}

	function __notify_remix($base_project_id, $remixed_project_id) {
        $this->Project->recursive = 0;
        //find out the users
        $base = $this->Project->find('Project.id = '.$base_project_id,
                                   'Project.id, Project.name, User.id, User.username');
        $remixed = $this->Project->find('Project.id = '.$remixed_project_id,
                                    'Project.id, Project.name, User.id, User.username');
        
        //find out if the base user is different than remix user
        if(empty($base['User']['id']) || $base['User']['id']==$remixed['User']['id']) {
            return false;
        }

        //find out if the base user has remix notification type set up
        $notify = $this->RemixNotification->find('user_id = '.$base['User']['id']);

		if(ASSIGN_REMIX_NOTIFICATION && empty($notify)) {
			//set a notification type for him
			$ntype = $this->__set_remix_notification_type($base['User']['id']);
		}
		else {
			$ntype = $notify['RemixNotification']['ntype'];
		}

	    
		if(SEND_REMIX_NOTIFICATION && !empty($ntype) && $ntype != 'nonotification') {
			//time duration calculation
			$duration = strtotime('-'.REMIX_NOTIFICATION_DAYS_SPAN.' day') - strtotime($notify['RemixNotification']['timestamp']);
			//within the timespan
			if($duration <= 0) {
				$this->__notify('project_remixed_'.$ntype,
                            $base['User']['id'],
                            array('project_id' => $base['Project']['id'],
                             'from_user_name' => $remixed['User']['username']),
                            array($remixed['Project']['id'], $remixed['Project']['name']));
			}
        }
    }
    
    /*
     *
     * stores history information
     * input: tab delimited string with values (coming from java analyzer)
     */
	function storehistory($project_shared_id, $user_shared_id, $retval) {
		$retval = str_replace('!undefined!', '', $retval);
		list($date, $event, $pname , $username, $savername) = explode("\t", $retval);

		// fix problem with times that are 24 instead of 00 for midnight times
		if(! $this->__is_empty($date)) {
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
			if ($this->__is_empty($pname) || $this->__is_empty($username)) {
				// No point in adding record if there is no way to refer to another project
				$this->log("\n<br>!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username<br>\n");
			}
            else {
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
					}
				}
				$this->ProjectShare->save($record);
			}
		}
		// For saves, olds and empty
		elseif($event == 'save' || $this->__is_empty($event) || $event == 'old' ) {
			// echo"+";
			if ($this->__is_empty($date)) {
				$this->log("\n<br>!MISSINGDATA-SH!:date:$date,pname:$pname,username:$username,savername:$savername<br>\n");
			}
            else {
				// echo"-";
				if (! $this->__is_empty($savername)) {
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
				}
                else {
				}
			}
		}
		else {
			$this->log("\n<br>!WRONGEVENT!:date:$date,event:$event,pname:$pname,username:$username,savername:$savername<br>\n");
		}
		return true;
	}

	function __is_empty($var) {
		return ( ((is_null($var) || rtrim($var) == "") && $var !== false)
                || (is_array($var) && empty($var)) );
	}
	
	/*
	function to set country name based on ip and project id
	*/
	function setCountryName($ip, $pid){
		$this->Project->id = $pid;
		$country = $this->GeoIp->lookupCountryCode(long2ip($ip));
		$this->Project->saveField('country', $country);
	}
}
?>
