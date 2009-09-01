<?php
/**
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 */
class AppController extends Controller {

    var $helpers = array("head",'Time');
    var $uses = array('AdminTag', 'Relationship', 'Project', 'Pcomment', 'Gcomment', 'Gallery', 'GalleryProject', 'GalleryMembership', 'BlockedUser', 'Notification', 'User', 'Announcement', 'BlockedIp', 'FriendRequest');
	var $components = array('RequestHandler', 'Cookie', 'Session');
    var $layout = 'scratchr_default';
    var $sanitize = true;

    function __construct() {
        uses('sanitize');
        $this->Sanitize = &new Sanitize;
        parent::__construct();
    }

	function beforeFilter() {

		// lets trim post variables
		$this->trimAllPostData();
	}

	function trimAllPostData() {
		if (!empty($this->params['form'])) {
			trim_array_byref($this->params['form']);
			if (!empty($this->params['form']['data']) && !empty($this->data)) // what if "data" is name of input field name in form...just make sure it's not
				$this->data = $this->params['form']['data'];
		}
	}

	/**
	 * Called after every controller action but before view is rendered.
	 * Sets view variables.
	 */
	function beforeRender() {
		$this->set('this_controller_here', $this->here);
		$isLoggedIn = $this->isLoggedIn();
		$this->set('isLoggedIn', $isLoggedIn);
		$users_permission =$this->isAnyPermission();
		$this->set('users_permission',$users_permission);
		$this->set('content_status', $this->getContentStatus());
		$this->set('client_ip', $this->RequestHandler->getClientIP());
		$blocked = $this->checkIP();
		$isBanned = false;
		$annoucements = $this->Announcement->findAll("content != ''");
		$empties = $this->Announcement->findCount("content = ''");

        $announcement_id = -1;
        $isAnnouncementOn = false;
		if ($empties == 3) {
			$announcement_id = -1;
			$isAnnouncementOn = false;
		} else if(!empty($annoucements[0])) {
			$isAnnouncementOn = $annoucements[0]['Announcement']['isOn'];
			$announcement_id = $this->getAnnouncementId();
		}
		
		if ($announcement_id == -1) {
			$announcement = "";
			$isAnnouncementOn = false;
		} else if(!empty($annoucements[$announcement_id])){
			$announcement = $annoucements[$announcement_id]['Announcement']['content'];
		}
		
		if (isset($this->params['controller'])) {
			$controller = $this->params['controller'];
		} else {
			$controller = "none";
		}
		
		if ($blocked) {
			$isBanned = true;
			if ($controller == "contact") {
				$client_ip = ip2long($this->RequestHandler->getClientIP());

				$ban_record = $this->BlockedIp->find("ip = $client_ip");
				$ban_reason = $ban_record['BlockedIp']['reason'];
				$this->set('isBanned', $isBanned);
				$this->set('ban_reason', $ban_reason);
			} else {
				$client_ip = ip2long($this->RequestHandler->getClientIP());

				$ban_record = $this->BlockedIp->find("ip = $client_ip");
				$ban_reason = $ban_record['BlockedIp']['reason'];
				$this->set('ban_reason', $ban_reason);
				$this->set('isBanned', $isBanned);
				$this->redirect("/contact/us_banned");
				exit();
			}
		}

		if ($isLoggedIn) {
			$user_id = $this->getLoggedInUserID();
			$this->set('notify_count', $this->Notification->countAll($user_id));
			$user = $this->User->find("User.id = $user_id");
						
			$isLocked = false;
			
			//locked user
			if (isset($user['User']['status']) && $user['User']['status'] == 'locked') {
				//if temp blocked user
				if($user['User']['blocked_till'] != '0000-00-00 00:00:00') {
					//blocked_till time is in past
					if($user['User']['blocked_till'] <= date("Y-m-d H:i:s", time())) {
						//so unblock the user
						$this->User->tempunblock($user['User']['id']);
					}
					//block time is in future..
					else {
						$isLocked = true;
					}
				}
				//permanent block..
				else {
					$isLocked = true;
				}
			}
			
			if ($isLocked) {
				$isBanned = true;
				
				$ban_record = $this->BlockedUser->find("BlockedUser.user_id = $user_id");
				$ban_reason = $ban_record['BlockedUser']['reason'];
				
				if ($controller == "contact") {
					$this->set('ban_reason', $ban_reason);
					$this->set('isBanned', $isBanned);
				} else {
					$this->set('ban_reason', $ban_reason);
					$this->set('isBanned', $isBanned);
					$this->redirect("/contact/us_banned");
					exit();
				}
			}
			
			
			$this->set('loggedInUrlname', $this->getLoggedInUrlname());
			$this->set('loggedInUsername', $this->getLoggedInUsername());
			$this->set('loggedInUID', $this->getLoggedInUserID());
			$this->set('isAdmin', $this->isAdmin());
			$this->set('isLogged', true);

			/*
			$userpic = $this->User->read("userpicext",$this->getLoggedInUserID());
			$userpicext = $userpic['User']['userpicext'];

			$this->set('userpic_suffix',$userpicext);
			$config =& Configure::getInstance();
			$config->userpic_suffix = $userpicext;
			*/
		} else {
			$this->set('loggedInUrlname', null);
			$this->set('loggedInUsername', null);
			$this->set('loggedInUID', null);
			$this->set('isAdmin', false);
			$this->set('isLogged', false);
		}
		
		$this->set_active_tab($controller);
		$this->set_predefined_tags();
		$this->set('announcement', $announcement);
		$this->set('isAnnouncementOn', $isAnnouncementOn);
		$this->set('isBanned', $isBanned);
		$this->Session->delete('FLASH_NOTICE_KEY');
		$this->Session->delete('FLASH_ERROR_KEY');
	}
	/**
	* Sets date and name for showing ribbon on featred project
	**/
	function convertDate($original=null)
	{
		$actual_date = date('n/Y', strtotime($original));
		return $actual_date;
	}
	
	function ribbonImageName($original=null)
	{
		$actual_date = date('M_Y', strtotime($original));
		return $actual_date.'.gif';
	}
	
	/**
	* Sets currently active predefined tags
	**/
	function set_predefined_tags() {
		$admin_tags = $this->AdminTag->findAll("AdminTag.status = 'active'");
		$this->set('predefined_tags', $admin_tags);
	}
	
	/**
	* Determines which tab is currently active 
	**/
	function set_active_tab($controller) {
		if ($controller == "galleries") {
			$active_tab = "galleries";
		} elseif ($controller == "contact") {
			$active_tab = "contact";
		} elseif ($controller == "projects" || $controller == "channel") {
			$active_tab = "projects";
		} elseif ($controller == "user") {
			$active_tab = "user";
		} elseif ($controller == "home") {
			$active_tab = "home";
		} else {
			$active_tab = "none";
		}
		
		$this->set('active_tab', $active_tab);
	}
	
	/**
	* Determine if an ip has been blocked
	**/
	function checkIP() {
		$client_ip = ip2long($this->RequestHandler->getClientIP());

		$ip_count = $this->BlockedIp->findCount("ip = $client_ip");
		if ($ip_count > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* Randomly determines the announcement id to show
	**/
	function getAnnouncementId() {
		$bit_index = Array();
		$counter = 0;
		$record = $this->Announcement->findAll();
		foreach ($record as $announcement) {
			$id = $announcement['Announcement']['id'] - 1;
			$content = $announcement['Announcement']['content'];
			if ($content == "" || $content == null) {
			} else {
				$bit_index[$counter] = $id;
				$counter++;
			}
		}
        if($counter) {
            return rand()%$counter;
        } else {
            return 0;
        }
	}
    /**
     * Called mostly by views
     */
    function isLoggedIn() {
        return $this->activeSession(null);
    }

    /**
     * Called mostly by views
     */
    function getLoggedInUrlname() {
        return $this->Session->read('User.urlname');
    }

    /**
     * Called mostly by views
     */
    function getLoggedInUserID() {
        return $this->Session->read('User.id');
    }

	/**
     * Called mostly by views
     */
    function getLoggedInUsername() {
        return $this->Session->read('User.username');
    }


	function isAdmin() {
		return $this->Session->read('User.role') === 'admin';
	}
	
	function isAnyPermission()
	{
		return $this->Session->read('UsersPermission');
	}
	
	function checkPermission($permission_url=null)
	{
		if ( ! $this->isAdmin()){
			$users_permission = $this->isAnyPermission(); 
			if (array_key_exists($permission_url, $users_permission)) {
			}
			else
			{
				
				$this->Session->setFlash(___("You do not have the permission to perform this operation",true));
				$this->redirect('/');	
			}
		}
	}

    /**
     * TODO: seems very insecure to set
     * a session variable name 'User'...it
     * should probably be some kind of reverse encrypted session
     * variable that represents the user information. I'm not
     * too sure of the internals of cake sessions but it seems
     * we're are storing user info, password, etc, on the clear, unecrypted
     *
     * Verifies active session
     */
    function activeSession($uid=null) {
        if (!$uid)
            return $this->Session->check('User');
        return ($this->Session->read('User.id') == $uid);
    }

    /**
     * TODO: hash session info (?)
     * Filter function to redirect anonymous users
     * Notes: the session component is simply used to
     * specify what actions require user login. It does
     * not provide access control/permission for particular
     * user/group/project actions
     */
    function redirectOnSessionCheck() {
        if (!$this->Session->check('User')) {
            $this->redirect('/login');
            die;
        }
    }


		/**
	 * Returns true if this controller
	 * was call through a URL with query params
	 * appended
	 */
	function hasQueryParams() {
		return (count($this->params['url']) > 1);
	}

	/**
	 * Returns true if this controller
	 * was called with additional url arguments
	 * following the action (e.g. /controller/action/arg1/arg2)
	 */
	function hasExtraUrlArgs() {
		return !empty($this->params['pass']);
	}

	/**
     * Renders error if the number of parameters to the
     * action to match the number given in count
     * @param int $count => expected number of arguments
     */
	 
    function exitOnInvalidArgCount($correct_count) {
	
        if (count($this->passedArgs) !== $correct_count)
		//if (count($this->passed_args) !== $correct_count)
            $this->__err();
    }

	/**
	 * DEPRECATED
	 * Requires overhead of a RequestHandler component
	 * specified in this class which will result in
	 * overhead for all subclasses
	 *
     * Renders error if the number of parameters to the
     * action to match the number given in count
     * @param int $count => expected number of arguments
     */
    function exitOnInvalidRequestType($correct_type) {
		switch($correct_type)
		{
		case "Ajax":
			if (!$this->RequestHandler->isAjax())
				$this->__err();
			break;

		case "Post":
			if (!$this->RequestHandler->isPost())
				$this->__err();
			break;

		case "Get":
			if (!$this->RequestHandler->isGet())
				$this->__err();
			break;

		default:
			$this->err();
		}
    }


	/**
	 * Flashes an invalid request message
	 * and terminates the request
	 */
	function __err() {
        $this->flash("Invalid Request","/");
        die;
    }


	/**
	 * Sets msg to be display as a flash message in a view that uses
	 * $this->controller->Session->flash($flashKeyStr) or
	 * a view that uses $this->controller->Session->flash()
	 * if no key was specified in which case defaults to 'flash'
	 */
	function setFlash($msg = "", $flashKeyStr = 'flash') {
		$this->Session->setFlash($msg, 'default', NULL, $flashKeyStr);
	}

	/**
	* sends notification to specified user
	*/
	function notify($type, $to_user_id, $data, $extra = array(), $flashit = false) {
		$session_user_id = $this->getLoggedInUserID();
		if (!$session_user_id)
		   $this->__err();

		$this->User->id = $to_user_id;
		$user = $this->User->read();
		if (empty($user))
		   $this->__err;

		//store the notification
		$this->Notification->addNotification($type, $to_user_id,
											$data, $extra);

		if($flashit) {
			$this->setFlash("Notification sent.", FLASH_NOTICE_KEY);
		}
	}

		/**
	/* Sets the status of the content on the site (all | safe)
	useless now because we are doing based on url
	**/
	function setContentStatus($status, $redirect = false) {
		return;
		$this->Session->write('Config.safe', $status);
        $this->Cookie->write('safe', $status,  null, '+350 day');

		if ($redirect == false) {
			return;
		} else {
			$this->redirect("/");
		}
	}

	/* Based on the URL used it will render different content */
	function getContentStatus() {
		$host = env('HTTP_HOST');
		if($host == FILTERED_HOST) {
			return 'safe';
		}
		return 'all';
	}

/*
JPEG / GIF / PNG Resizer / Image Viewer

Parameters (passed via URL):
src = path / url of jpeg or png image file
percent = if this is defined, image is resized by it's
          value in percent (i.e. 50 to divide by 50 percent)
w = image width
h = image height
thumb: set = displays thumbnail.
        not set = displays original image.
force: by default this script will constrain the image proportions. If you wish to
        force with width/height settings, set &force in the URL
        width and height and thumb MUST be set.

Requires the PHP GD Extension

Originally By: Michael John G. Lopez - www.sydel.net
Modified By: Brian DiChiara - www.briandichiara.com

Modifications:
Supports Transparent PNG and GIF
Does not resize images smaller than specified w/h
Supports Remote files (http://www.notyoursite.com/image.jpg)
*/
	function getExtension($f){
	     /* Old method
	     $pos = strrpos($f, '.');
	     if(!$pos) {
	         return '';
	     }
	     $str = substr($f, $pos + 1, strlen($f));
	     return strtolower($str);
	     */

	     $size = getimagesize($f);
	     return substr($size['mime'],strpos($size['mime'],'/')+1,strlen($size['mime']));
	}

    function resize($path, $path_to, $width, $height, $aspect = true, $htmlAttributes = array(), $percent = false, $forceit = false, $thumb = true) {
	$size = getimagesize($path);
        if ($aspect) { // adjust to aspect.
            if (($size[1]/$height) > ($size[0]/$width))  // $size[0]:width, [1]:height, [2]:type
                $width = ceil(($size[0]/$size[1]) * $height);
            else
                $height = ceil($width / ($size[0]/$size[1]));
        }

$img = $path;

$ext = substr($size['mime'],strpos($size['mime'],'/')+1,strlen($size['mime']));
$force = (isset($forceit) && isset($width) && isset($height) && !empty($width) && !empty($height)) ? true : false;

$remote = @file ($img);
if($remote){
    $fp = @fopen ($img, 'rb');
    $data = '';
    while (!feof ($fp)){
        $data .= fgets($fp, 4096);
    }
    $src = @imagecreatefromstring($data);
    $sw = @imagesx($src);
    $sh = @imagesy($src);
} else {
    $dims = @getimagesize($img); // get width/height of original image

    $sw = $dims[0]; //returns width
    $sh = $dims[1]; //returns height
}

$w = (isset($width) && !empty($width)) ? $width : 0; //desired maximum width
$h = (isset($height) && !empty($height)) ? $height : 0; //desired maximum height

$resize = false; //assume no resize is needed

if ($percent> 0) {
    // calculate resized height and width if percent is defined
    $percent = $percent * 0.01;
    $w = $sw * $percent;
    $h = $sh * $percent;
} else {
    $resize = (($sh> $h && isset($h) && $h != 0) || ($sw> $w && isset($w) && $w != 0)) ? true : $resize;
    $resize = ($force) ? true : $resize;
    if(($resize && isset($thumb))){
        if (isset ($w) && (!isset ($h) || $h == 0)) {
            // autocompute height if only width is set
            $h = (100 / ($sw / $w)) * .01;
            $h = @round ($sh * $h);
        } elseif (isset ($h) && (!isset ($w) || $w == 0)) {
            // autocompute width if only height is set
            $w = (100 / ($sh / $h)) * .01;
            $w = @round ($sw * $w);
        } elseif (isset ($h) && isset ($w) && !$force) {
            // get the smaller resulting image dimension if both height
            // and width are set and $constrain is also set
            $hx = (100 / ($sw / $w)) * .01;
            $hx = @round ($sh * $hx);

            $wx = (100 / ($sh / $h)) * .01;
            $wx = @round ($sw * $wx);

            if ($hx <$h) {
                $h = (100 / ($sw / $w)) * .01;
                $h = @round ($sh * $h);
            } else {
                $w = (100 / ($sh / $h)) * .01;
                $w = @round ($sw * $w);
            }
        }
    } else {
        $w = $sw;
        $h = $sh;
    }
}

switch ($ext){
    case "jpg"  : $type = "jpeg";    break;
    case "bmp"  : $type = "x-ms-bmp";  break;
    case "png"  : $type = "x-png"; break;
    case "tif"  : $type = "x-tiff";    break;
    case "ico"  : $type = "x-icon";    break;
    default  :    $type = $ext;      break;
}


if((!$resize || !isset($thumb)) && !remote){
    //display contents
} else {
    $im = ($remote) ? $src : false;

ini_set("memory_limit", 100000000000); // Arbitrarily large memory limit size

     switch ($ext){
        case "jpg"  :	$im = @imagecreatefromjpeg($img);   break;
        case "jpeg" :    $im = @imagecreatefromjpeg($img); break;
        case "wbmp" :    $im = @imagecreatefromwbmp($img);   break;
        case "png"  : $im = @imagecreatefrompng($img); break;
        case "gif"  : $im = @imagecreatefromgif($img); break;
        default  :    $im = false; break;

ini_restore ("memory_limit");
    }

    if (!$im) {
        // just return the contents of the actual image
	copy($path,$path_to);
    } else {
        // Create the resized image destination
        $thumb = @imagecreatetruecolor ($w, $h);

        //preserve alpha opacity - thanks to Martin Schmidt
        $colorcount = @imagecolorstotal($im);
        @imagetruecolortopalette($thumb,true,$colorcount);
        @imagepalettecopy($thumb,$im);
        $transparentcolor = @imagecolortransparent($im);
        @imagefill($thumb,0,0,$transparentcolor);
        @imagecolortransparent($thumb,$transparentcolor);

        // Copy from image source, resize it, and paste to image destination
        @imagecopyresampled ($thumb, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);

        // Output resized image
        switch ($ext){
            case "wbmp" :    @imagewbmp ($thumb, $path_to);    break;
            case "png"  : @imagepng ($thumb, $path_to);   break;
            case "gif"  : @imagegif ($thumb, $path_to);    break;
            default  :    @imagejpeg ($thumb, $path_to);    break;
        }

        imagedestroy($thumb);
        imagedestroy($im);
    }
	}

	$dimensions[0]=$height;
	$dimensions[1]=$width;
	return $dimensions;
 }

  /**
   * Function to delete the uploaded file. $filename requires the full path of the file to be deleted.
   */
  function deleteMovedFile($fileName)
  {
    if (!$fileName || !is_file($fileName))
    {
      return true;
    }
    if(unlink($fileName))
    {
      return true;
    }
    return false;
  }

	/*
	Resizes and saves a given image
	Note: If userid (or themeid, if type is gallery) remains 0, then the function will
	generate the userid from the filename
	*/
	function resizeImage($orig_filepath, $userid=0, $resizegifs=false, $type='buddy')
	{
		$orig_filename = substr($orig_filepath, strrpos($orig_filepath, DS)+1, strlen($orig_filepath));
		if($userid==0)
		{
			if($type=='buddy')
				$userid = substr($orig_filename, 0, strpos($orig_filename, '_'));
			else if($type=='gallery')
			     $userid = substr($orig_filename, 0, strpos($orig_filename, '.'));
		}

		if($type=='buddy')
		{
			$buddy_icon_file_small = WWW_ROOT . getBuddyIcon($userid, false, DS, 'mini', false, false);
			$buddy_icon_file_medium = WWW_ROOT . getBuddyIcon($userid, false, DS, 'med', false, false);
		}
		else if($type=='gallery')
		{
			$buddy_icon_file_medium = WWW_ROOT . getThemeIcon($userid, false, DS, false);
			$ext=ICON_EXTENSION;
		}

		//mkdirR(dirname($buddy_icon_file_small) . DS);
		//mkdirR(dirname($buddy_icon_file_medium) . DS);

		if($this->getExtension($orig_filepath)=="gif" && !$resizegifs)
		{
			/*
			$buddy_icon_file_small.=".".$this->getExtension($orig_filepath);
			$buddy_icon_file_medium.=".".$this->getExtension($orig_filepath);
			$ext="gif";
			*/
			if($type!="gallery")
			{
				$buddy_icon_file_small.=".png";
			}
			$buddy_icon_file_medium.=".png";
			$ext="gif";
		}
		else
		{
			if($type!="gallery")
			{
				$buddy_icon_file_small.=".png";
			}
			$buddy_icon_file_medium.=".png";
			$ext="png";
		}

		if($ext=="gif")
		{
			copy($orig_filepath, $buddy_icon_file_medium);
			if($type!='gallery')
			{
				copy($orig_filepath, $buddy_icon_file_small);
			}
			$this->setFlash(___("Picture uploaded.", true), FLASH_NOTICE_KEY);
		}
		else
		{
			// make small size copy
			$temp_array = array();
			$dimensionsmed = $this->resize($orig_filepath, $buddy_icon_file_medium, 90, 90, true, $temp_array, false);
			if($type!='gallery')
				$dimensionssm = $this->resize($orig_filepath, $buddy_icon_file_small, 28, 28, true, $temp_array, false);
			$this->setFlash(___("Picture uploaded.", true), FLASH_NOTICE_KEY);
		}
		//$this->deleteMovedFile($orig_filepath);

		if($type!='gallery')
		{
			// save file path to database
			if($this->User->find("id=".$userid))
			{
				$this->User->id = $userid;
				$this->User->saveField("userpicext",$ext);
			}
		}

		return $this->getExtension($orig_filepath);

		/* Discontinued
		$this->User->saveField("userpicmedwidth",$dimensionsmed[0]);
		$this->User->saveField("userpicmedheight",$dimensionsmed[1]);
		$this->User->saveField("userpicsmwidth",$dimensionssm[0]);
		$this->User->saveField("userpicsmheight",$dimensionssm[1]);
		*/
	}
	
	/**
	* Hides site elements associated with an user including the user
	**/
	function hide_user($user_id, $visibility = "delbyusr") {
		$pcomments = $this->Pcomment->findAll("Pcomment.user_id = $user_id");
		$gcomments = $this->Gcomment->findAll("Gcomment.user_id = $user_id");
		$projects = $this->Project->findAll("Project.user_id = $user_id");
		$galleries = $this->Gallery->findAll("Gallery.user_id = $user_id");
		$friends = $this->Relationship->findAll("Relationship.friend_id = $user_id");
		
		$this->User->id = $user_id;
		$user = $this->User->read();
		$this->User->saveField("status", "delbyadmin");
		
		foreach ($friends as $friend) {
			$friend_id = $friend['Relationship']['id'];
			$this->Relationship->del($friend_id);
		}
		
		foreach ($pcomments as $pcomment) {
			$comment_id = $pcomment['Pcomment']['id'];
			$this->hide_pcomment($comment_id, $visibility);
		}
		
		foreach ($gcomments as $gcomment) {
			$comment_id = $gcomment['Gcomment']['id'];
			$this->hide_gcomment($comment_id, $visibility);
		}
		
		foreach ($projects as $project) {
			$project_id = $project['Project']['id'];
			$this->hide_project($project_id, $visibility);
		}
		
		foreach ($galleries as $gallery) {
			$gallery_id = $gallery['Gallery']['id'];
			$this->hide_gallery($gallery_id, $visibility);
		}
	}
	
	/**
	* Hides gallery comment
	**/
	function hide_gcomment($gcomment_id, $visibility = "delbyusr") {
		$this->Gcomment->id = $gcomment_id;
		$gcomment = $this->Gcomment->read();
		$this->Gcomment->saveField("comment_visibility", $visibility);
	}
	
	/**
	* Hides project comment
	**/
	function hide_pcomment($pcomment_id, $visibility = "delbyurs") {
		$this->Pcomment->id = $pcomment_id;
		$pcomment = $this->Pcomment->read();
		$this->Pcomment->saveField("comment_visibility", $visibility);
	}
	
	/**
	* Hides project
	**/
	function hide_project($project_id, $visibility = "delbyusr") {
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		$this->Project->saveField("proj_visibility", $visibility);
	}
	
	/**
	* Hides a gallery and removes all gallery memberhips
	**/
	function hide_gallery($gallery_id, $visibility = "delbyusr") {
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$this->Gallery->saveField("visibility", $visibility);
		$this->Gallery->bindHABTMProject();
		$this->Gallery->_deleteLinks($gallery_id);
		$gallery_memberships = $this->GalleryMembership->findAll("gallery_id = $gallery_id");
		foreach ($gallery_memberships as $record) {
			$this->GalleryMembership->delete($record['GalleryMembership']['id']);
		}
	}
	
	/**
	* Helper for converting links in comments to links to site
	**/
	function set_comment_content($initial_content) {
		$comment_content = $initial_content;
		$comment_content  = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/projects/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to project', true) . ")</a>",  $comment_content);
		$comment_content  = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/forums/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to forums', true) . ")</a>",  $comment_content);
		$comment_content = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/galleries/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to gallery', true) . ")</a>",  $comment_content);
		
		return $comment_content;
	}
	
	/**
	* Sets additional variables needed by the gallery
	**/
	function finalize_gallery($gallery) {
		$gallery_id = $gallery['Gallery']['id'];
		$gallery_icon_src = getThemeIcon($gallery_id);
		$gallery_icon_src_final = substr($gallery_icon_src, 1, 100);
		if(!file_exists($gallery_icon_src_final)) {
			$gallery_projects = $this->GalleryProject->findAll("GalleryProject.gallery_id = $gallery_id");
			if (empty($gallery_projects)) {
				$gallery['Gallery']['icon_src'] = $gallery_icon_src;
			} else {
				$project = $gallery_projects[0];
				$project_id = $project['Project']['id'];
				$owner_id = $project['Project']['user_id'];
				$owner_record = $this->User->find("User.id = $owner_id");
				$project_url = $owner_record['User']['username'];
				$project_icon_src = getThumbnailImg($project_url, $project_id);
				$gallery['Gallery']['icon_src'] = $project_icon_src;
			}
		} else {
			$gallery['Gallery']['icon_src'] = $gallery_icon_src.'?t='.urlencode($gallery['Gallery']['modified']);
		}
		
		return $gallery;
	}
	
	/**
	* Sets additional variables needed by galleries
	**/
	function finalize_galleries($galleries) {
		$final_array = Array();
		foreach ($galleries as $gallery) {
			$gallery_id = $gallery['Gallery']['id'];
			$gallery_icon_src = getThemeIcon($gallery_id);
			$gallery_icon_src_final = substr($gallery_icon_src, 1, 100);
			if(!file_exists($gallery_icon_src_final)) {
				$gallery_projects = $this->GalleryProject->findAll("GalleryProject.gallery_id = $gallery_id", null, "GalleryProject.timestamp DESC");
				if (empty($gallery_projects)) {
					$gallery['Gallery']['icon_src'] = $gallery_icon_src;
				} else {
					$project = $gallery_projects[0];
					$project_id = $project['Project']['id'];
					$owner_id = $project['Project']['user_id'];
					$owner_record = $this->User->find("User.id = $owner_id");
					$project_url = $owner_record['User']['username'];
					$project_icon_src = getThumbnailImg($project_url, $project_id);
					$gallery['Gallery']['icon_src'] = $project_icon_src;
				}
			} else {
				$gallery['Gallery']['icon_src'] = $gallery_icon_src.'?t='.urlencode($gallery['Gallery']['modified']);
			}
			array_push($final_array, $gallery); 
		}
		
		return $final_array;
	}

    /*
     * returns an encrypted urlencoded string from $id
     */
    function encode($id) {
        $salt = Configure::read('Security.salt');
        $plain_str = $id.'-'.substr(sha1($id.$salt), 0, 6);
        $base64_str = base64_encode($plain_str);
        $base64url_str = strtr($base64_str, '+/=', '-_,');
        return $base64url_str;
    }

    /*
     * returns the user id from the given encrypted urlencoded string
     * returns 0 if the string is invalid
     */    
    function decode($base64url_str) {
        $salt = Configure::read('Security.salt');
        $base64_str = strtr($base64url_str, '-_,', '+/=');
        $plain_str = base64_decode($base64_str);
        $parts = explode('-', $plain_str);
        if(count($parts) != 2) {
            return 0;
        }
        $id = $parts[0];
        return substr(sha1($id.$salt), 0, 6) === $parts[1]
            ? (int) $id
            : 0;
    }
}
?>
