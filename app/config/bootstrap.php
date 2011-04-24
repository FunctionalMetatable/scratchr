<?php
require_once('aws.php');
require_once('recaptchalib.php');
define("CAPTCHA_PUBLICKEY", "6LfkVAIAAAAAABlcomxpr4KzuPR0VBcG8eLGnsli"); // from http://recaptcha.net/
define("CAPTCHA_PRIVATEKEY", "6LfkVAIAAAAAAEUSKkasdLGfAGSczVHj4IvWbWGp");


/**
 * Module file for global configs and functions
 */

/*------------------------------------*
TODO: if web_services mode flag set
then skip declarations:
$this->params['webservices']
*------------------------------------*/

/**
 * home page configs
 */
 define("NUM_FEATURED_PROJECTS", 3);
 define("NUM_FEATURED_THEMES", 3);
 define("NUM_TOP_RATED", 3);
 define("NUM_TOP_VIEWED", 3);
 define("NUM_TOP_REMIXED", 3);
 define("NUM_NEW_PROJECTS", 3);
 define("NUM_NEW_PROJECTS_CACHE", 20);
 define("NUM_DESIGN_STUDIO_PROJECT", 3);
 define("NUM_DESIGN_STUDIO_PROJECT_CACHE", 20);
 define("NUM_CURATOR_FAV_PROJECT", 3);
 define("MAX_FRIENDS_PROJECTS", 100);
 define("NUM_THEME_PROJECTS", 4);
 define("NUM_NEW_MEMBERS", 4);
 define("NUM_RECENT_VISITORS", 4);
 define("MAX_LENGTH_PNAME_HOME", 20);
 define("MEMCACHE_PREFIX", "scratch.mit.edu");
 
 define("NUM_MAX_COMMENT_FLAGS", 1);
 define("NUM_MAX_PROJECT_FLAGS", 3);
 define("NUM_MAX_TAG_FLAGS", 1);
 define("MAX_COMMENT_LENGTH", 500);
  define("COMMENT_LENGTH", 10);
 define("COMMENT_SPAM_MAX_DAYS", 3);
 define("COMMENT_SPAM_CLEAR_COMMENTS", 5);
 define("COMMENT_SPAM_CLEAR_MINUTES", 2);
 define("NUM_TOP_COUNTRIES", 6);
 define("PROJECT_COMMENT_PAGE_LIMIT", 60);
 define("GALLERY_COMMENT_PAGE_LIMIT", 40);
 //day interval to calculate top viewed project on home controller
 define("TOP_VIEWED_DAY_INTERVAL", 4);
 //day interval to calculate top remixed project on home controller for filterd host
 define("TOP_VIEWED_DAY_INTERVAL_SAFE", 20);
 //day interval to calculate top remixed project on home controller
 define("TOP_REMIXED_DAY_INTERVAL", 10);
 //day interval to calculate top remixed project on home controller for filterd host
 define("TOP_REMIXED_DAY_INTERVAL_SAFE", 20);
 //day interval to calculate top loved project on home controller
 define("TOP_LOVED_DAY_INTERVAL", 10);
 //day interval to calculate top download project on home controller
 define("TOP_DOWNLOAD_DAY_INTERVAL", 10);
 define("NUM_TOP_DOWNLOAD", 3);
 define("NUM_MIN_SCRIPT_FOR_TOP_VIWED", 1);
 define("NUM_MIN_SCRIPT_FOR_TOP_REMIX", 1);
 define("NUM_MIN_SCRIPT_FOR_TOP_LOVED", 1);
 //List of country for custumizable for home page.
 define("CUSTOMIZABLE_COUNTRIES", "IL,MX");
 define("DEFAULT_COUNTRY", "worldwide");
/**
 * Email Address Related to Flagging
 */
 define("REPLY_TO_FLAGGED_PCOMMENT", "caution@scratch.mit.edu");
 define("TO_FLAGGED_PCOMMENT", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_PROJECT", "caution@scratch.mit.edu");
 define("TO_FLAGGED_PROJECT", "caution@scratch.mit.edu");
 define("FROM_FLAGGED_PROJECT", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_GCOMMENT", "caution@scratch.mit.edu");
 define("TO_FLAGGED_GCOMMENT", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_PTAG", "caution@scratch.mit.edu");
 define("TO_FLAGGED_PTAG", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_GTAG", "caution@scratch.mit.edu");
 define("TO_FLAGGED_GTAG", "caution@scratch.mit.edu");
 //Emais id to send mail to request create multiple account using same ip.
 define("TO_REQUEST_FOR_MULTIPLE_ACCOUNT", "help@scratch.mit.edu");
 //Put on/off change of email on MyStuff should also change it on the forums.1 for ON and 0 for OFF.
 define("ENABLE_TO_CHANGE_FORUM_EMAIL", 1);
 
 define("DEFAULT_EMAIL_FROM", "help@scratch.mit.edu");
 //Used in componenets/email 
 define("DEFAULT_EMAIL_TO", "help@scratch.mit.edu");
 define("CONTACTUS_EMAIL", "help@scratch.mit.edu");
 //Used in  users password recovery reply email id.
 define("REPLY_TO_PASSWORD_RECOVERY", "help@scratch.mit.edu");
 
 // Used for banned account registration detection
 define("TO_BANNED_EC", "caution@scratch.mit.edu");
 
 /**
  * Themes config
  */
 define("NUM_NEW_THEME_PROJECTS", 0);
 //total option for welcome message
 define("WELCOME_EXPERIMENT_TOTAL_OPTIONS", 0);

 /**
  * myscratchr configs
  */
 define("NUM_MYSCRATCHR_PROJECTS", 0);
 define("NUM_MYSCRATCHR_FRIENDS", 6);
 define("MAX_SHARIABLES", 3);
 /**
 Show ribbon on featured project. Value 1 means fetured is active and 0 means deactive.
 */
 define("SHOW_RIBBON", 0);
 /**
 To enable/disable to write debug log for service controller.Value 1 for enable and 0 for desable
 */
 define("WRITE_LOG", 0);
 /**
 Set how many friend projects to be show.
 */
 define("NUM_FRIEND_PROJECTS", 6);
 
 /**
 Set how many comment reply to be show.
 */
 define("NUM_COMMENT_REPLY", 4);
 
 
 /**
 Allow multiple accounts to be created from the same IP after X minutes
 */
 define("SIGNUP_INTERVAL", 0);
 define("USED_IP_BY_BLOCKED_ACCOUNT_IN_PAST_N_DAYS", 30);

 /**
  * sets the time interval (hrs) in which
  * the home page projects are displayed. For instance,
  * after the rating period has expired, new top rated
  * projects will be calculated based on the numbers of
  * ratings since the last rating period
  */
 define("TOP_RATED_PERIOD", 24);
 define("TOP_VIEWED_PERIOD", 24);
 define("NEW_PERIOD", 24);

/*
 * cesored by community is activated from the date - 2009-09-02
 */
    define("CENSBYCOMM_ACTIVE_FROM", "2009-08-31 12:00:00");

 /**
  * External webservices interface key for scratchr
  */
 define("SCRATCH_KEY", "XXXXX");

 /**
  * Tag cloud consts
  */
 define("TAG_CLOUD_HOME", 30);
 define("TAG_PAGINATION_CACHE_TTL", 720); //12 hours
 define("TAG_PAGINATION_CACHE_TTL_CREATION", 360); //6 hours
 define("TAG_CLOUD_BIG", 500);
 define("TAG_CLOUD_TTL", 1440); // 24 hrs
 define("NUM_TAGED_PROJECT", 200); 
 define("TAGED_PROJECT_CACHE_TTL", 720); // 12 hrs
 
 define("NUM_SHARED_TAGED_PROJECT", 100); // 
 define("TAGED_SHARED_PROJECT_CACHE_TTL", 360); 
 define("LATEST_SHARED_DAY_INTERVAL", 30);
 define("NUM_RAMIXED_TAGED_PROJECT", 100); // 
 define("TAGED_RAMIXED_PROJECT_CACHE_TTL", 360); 
 define("LATEST_RAMIXED_DAY_INTERVAL", 30);
 define("NUM_TOPVIEWED_TAGED_PROJECT", 100); // 
 define("TAGED_TOPVIEWED_PROJECT_CACHE_TTL", 360); 
  define("LATEST_TOPVIEWED_DAY_INTERVAL", 30);
 define("NUM_TOPLOVED_TAGED_PROJECT", 100); // 
 define("TAGED_TOPLOVED_PROJECT_CACHE_TTL", 360); 
  define("LATEST_TOPLOVED_DAY_INTERVAL", 30);
  
  /**
  *Api constant
  */
  define("API_REGISTERED_USERS_TTL", 60); //1 hours
  define("API_PROJECT_CREATORS_TTL", 60); //1 hours
  define("API_TOTAL_PROJECT_TTL", 60); //1 hours
  define("API_TOTAL_SCRIPTS_TTL", 60); //1 hour
  define("API_TOTAL_SPRITES_TTL", 60); //1 hours
  define("API_USER_PROJECTS_TTL", 60); //1 hours
  define("API_USER_FRIENDS_TTL", 60); //1 hours
  define("API_USER_GALLERIES_TTL", 60); //1 hours
  define("API_USER_INFO_TTL", 60); //1 hours
  define("API_PROJECTS_BY_GALLERY_TTL", 60); //1 hours
  define("API_PROJECT_INFO_TTL", 60); //1 hours
  define("API_AUTHENTICATE_USER_TTL", 60); //1 hours
  define("API_GALLERY_INFO_TTL", 60); //1 hours
  define("API_PCOMMENT_BY_ID_TTL", 60); //1 hours
  define("API_FAVORITE_PROJECTS_BY_UID_TTL", 60); //1 hours
  define("API_PROJECT_BLOCK_COUNT_TTL", 60); //1 hours
  define("API_PROJECT_BLOCK_TTL", 60); //1 hours
  define("API_GETBLOCKCOUNT_PER_CATEGORY_TTL", 60); //1 hours

 /**
  * IMAGE EXTENSIONS
  */
 define("ICON_EXTENSION", ".png"); // form ".XxX"
 define("THUMBNAIL_EXTENSION", ".png");
 define("BINARY_EXTENSION", ".sb");
 define("SPRITE_EXTENSION", ".png");

/**
 * Controller URL prefixes
 */
 define("THEME_URL_PREFIX", "galleries");
 define("PROJECT_URL_PREFIX", "projects");
 define("USER_URL_PREFIX", "users");

/**
 * flash error keys
 * these actually correspond to matching css id definitions
 * where the key is prepended to "Message" to represent
 * the flash element's div id
 */
 define("FLASH_ERROR_KEY", "error");
 define("FLASH_NOTICE_KEY", "notice");

/**
 * Resource URLs
 */
define ('INFO_URL', 'http://info.scratch.mit.edu'); 
define ('INFO_ABOUT_ADMINS_URL', 'http://info.scratch.mit.edu/Scratch_Team'); 
define ('TOPLEVEL_URL', 'http://scratch.mit.edu');
define ('WIKI_URL', 'http://wiki.scratch.mit.edu');
define ('ABOUT_SCRATCH_URL', 'info.scratch.mit.edu');
define ('SUPPORT_URL', 'info.scratch.mit.edu');
define ('FRONPAGE_FAQ_URL', 'http://info.scratch.mit.edu/Support/FAQ/Scratch_Website_FAQ/How_the_Scratch_front_page_works');
define ('FAQ_QUESTIONS_URL', 'http://info.scratch.mit.edu/Support/FAQ/Scratch_Website_FAQ#Questions');
define ('TOS_URL', 'http://info.scratch.mit.edu/Terms_of_use');
define ('RESOURCE_URL', 'static/');
define ('HREF_RESOURCE_PROJECT', 'static/projects/');
define ('HREF_RESOURCE_MINI_THUMBS', 'static/projects/');
define ('HREF_RESOURCE_MEDIUM_THUMBS', 'static/projects/');
define ('HREF_RESOURCE_BINARIES', 'static/projects/');
define ('HREF_RESOURCE_ICON_BASE', 'static/icons/');
define ('HREF_RESOURCE_ICON_THEME', 'static/icons/gallery/');
define ('HREF_RESOURCE_ICON_BUDDY', 'static/icons/buddy/');

define ('RESOURCE_BASE', WWW_ROOT.'static/');
define ('THUMBS_MINI_PATH', WWW_ROOT . HREF_RESOURCE_MINI_THUMBS);
define ('THUMBS_MEDIUM_PATH', WWW_ROOT . HREF_RESOURCE_MEDIUM_THUMBS);
define ('BINARIES_PATH', WWW_ROOT . HREF_RESOURCE_BINARIES);
define ('BUDDY_ICON_PATH', WWW_ROOT . HREF_RESOURCE_ICON_BUDDY);
define ('THEME_ICON_PATH', WWW_ROOT . HREF_RESOURCE_ICON_THEME);
define ('PROJECT_PATH', WWW_ROOT . HREF_RESOURCE_PROJECT);

define('CONTENT_STATUS', "all");

//list of url patterns that will be converted to hyperlinks, separated by |
define('WHITELISTED_URL_PATTERN', 'wikipedia.org|\.ac.uk|flickr.com|\.edu');

//list of url patterns that will be show in gallery description, separated by |
define('WHITELISTED_URL', '\.edu');

//remix notification related constants
define('SEND_REMIX_NOTIFICATION', true);
define('ASSIGN_REMIX_NOTIFICATION', true);
define('REMIX_NOTIFICATION_DAYS_SPAN', 30);
define('REMIX_NOTIFICATION_TO_ROOT_BASED', true);
define('REMIX_NOTIFICATION_TO_LAST_BASED', true);

/**
 * Email addresses for contact page
 */
define("TOPIC1", "help@scratch.mit.edu");
define("TOPIC2", "help@scratch.mit.edu");
define("TOPIC3", "help@scratch.mit.edu");
define("TOPIC4", "help@scratch.mit.edu");
define("TOPIC5", "help@scratch.mit.edu");
define("TOPIC6", "help@scratch.mit.edu");
define("TOPIC7", "help@scratch.mit.edu");
define("TOPIC8", "help@scratch.mit.edu");

define("SUBTOPIC1", "help@scratch.mit.edu");
define("SUBTOPIC2", "help@scratch.mit.edu");
define("SUBTOPIC3", "help@scratch.mit.edu");

/**
 * Sitemap constants
 */
define('MAX_LINKS_PER_SITEMAP', 5000);
define('CACHE_DURATION', '4 days');

/**
 * Memcache constants
 */
define("MEMCACHE_SERVER", 'scratchdb'); //memcache server
define("MEMCACHE_PORT", 11211); //memcache port
define("HOMEL_PAGE_TTL", 60); //for home page
define("HOMEL_NEW_PROJECTS_TTL", 5);
define("HOME_SCRATCH_CLUB_TTL", 15);
define("HOME_FEATURED_THEMES_TTL", 60);
define("HOME_FRIENDS_PROJECTS_TTL", 60);
define("HOME_RECENT_VISITORS_TTL", 10);
define("HOME_NEW_MEMBERS_TTL", 10);
define("HOME_TOTAL_VISIBLE_PROJECTS_TTL", 240);
define("HOME_TOTAL_PROJECTS_TTL", 240);
define("HOME_TOTAL_SCRIPTS_TTL", 240);
define("HOME_TOTAL_SPRITES_TTL", 240);
define("HOME_TOTAL_CREATOR_TTL", 240);
define("HOME_TOTAL_USERS_TTL", 240);
define("HOME_TAGS_TTL", 720); // 12 hours
define("CHANNEL_RECENT_CACHE_TTL", 30); //0.5 hour
define("CHANNEL_FEATURED_CACHE_TTL", 30); //0.5 hour
define("CHANNEL_TOPVIEWED_CACHE_TTL", 30); //0.5 hour
define("CHANNEL_TOPLOVED_CACHE_TTL", 30); //0.5 hour
define("CHANNEL_REMIXED_CACHE_TTL", 30); //0.5 hour
define("FRIENDS_PROJECT_CACHE_TTL", 30); //0.5 hour
define("REMIXES_CACHE_TTL", 30); //0.5 hour
define('PCOMMENT_CACHE_NUMPAGE', 3); //cache first 3 project comment pages
define('GCOMMENT_CACHE_NUMPAGE', 3); //cache first 3 gallery comment pages

//Latest channel constant
define("LATEST_SHARED_CACHE_TTL", 60); // 1 hr
define("LATEST_TOPVIEWED_CACHE_TTL", 30); //0.5 hour
define("LATEST_TOPLOVED_CACHE_TTL", 30); //0.5 hour
define("LATEST_REMIXED_CACHE_TTL", 30); //0.5 hour
define("LATEST_ACTIVEMEMBER_CACHE_TTL", 3); //0.5 hour
define("LATEST_TAGED_PROJECT_CACHE_TTL", 30); //0.5 hour
define("NUM_LATEST_COMMENT", 1);
define("ACTIVEMEMBER_PROJECT_MAX_DAYS", 1);
define("NUM_LATEST_SHARED", 100);
define("NUM_LATEST_TOPVIWED", 100);
define("NUM_LATEST_TOPLOVED", 100);
define("NUM_LATEST_REMIXED", 100);
define("NUM_LATEST_GETFEEDBACK", 100);
define("NUM_LATEST_TAGED_PROJECT", 100);

//Survey
define('SPECIAL_USER_LIST', '');
define('SPECIAL_USER_SURVEY_KEY', 'special_user_survey_key_v1');
define('SPECIAL_USER_SURVEY_URL', 'http://www.google.com/?q=special_user_survey_key');
define('LOGGED_IN_USER_SURVEY_KEY', 'logged_in_user_survey_key_v1');
define('LOGGED_IN_USER_SURVEY_URL', 'http://www.google.com/?q=logged_in_user_survey_key');
define('LOGGED_OUT_USER_SURVEY_KEY', 'logged_out_user_survey_key_v1');
define('LOGGED_OUT_USER_SURVEY_URL', 'http://www.google.com/?q=logged_out_user_survey_key');
define('ALL_USER_SURVEY_KEY', 'all_user_survey_key_v1');
define('ALL_USER_SURVEY_URL', 'http://www.google.com/?q=all_user_survey_key');

// Announcement
define("RANDOM_ANNOUNCEMENT_CACHE_TTL", 60); // 1 hr
// Tags 
define("DELETED_USERS_TTL", 15); // deleted users

//Feeds memcache constants
define("FEEDS_TTL", 300); // 5 hrs


//Allow people to give thank you after n hours.
define("THANKS_INTERVAL",5);

/***
* Define block time
**/
define('BLOCK_CHECK_INTERVAL', '30 minutes');
define('TEMP_BLOCK_INTERVAL', '90 minutes');
 
 /***
* Define filtered site
**/
define('FILTERED_HOST', 'filtered.scratch.mit.edu');

/***
* Define Java path
**/
define('JAVA_PATH', '/usr/java/latest/bin/java');

/***
*Prompt CAPTCHA after number of failed attempt to login
**/
define('MAX_LOGIN_ATTEMPT', 2);

/***
*number of days after unblock user automatically
**/
define('USER_UNBLOCK_DAYS', '3 days');

/***
*authentication_key to get latest project
**/
define('GET_LATEST_PROJECT_AUTH_KEY', 'XXXXXX');

/**
  * Returns theme url for html link destination
  */
 function getThemeHref($themeid) {
	return "/galleries/".$theme_id;
 }

 /**
  * Returns project url for html link destination
  */
 function getProjectHref($urlname, $pid) {
	return "/projects/".$urlname."/".$pid;
 }

 /**
  * Returns user (personal page) url for html link destination
  */
 function getUserHref($urlname) {
	return "/users/".$urlname;
 }

 /**
  * Returns scratch binary file location
  * if file doesn't exist it returns the path to the default project
  */
 function getBinary($urlname, $pid, $prepend_slash=true, $ds="/") {
    $prefix = ($prepend_slash) ? $ds:"";
    $location = "{$prefix}static{$ds}projects{$ds}{$urlname}{$ds}{$pid}" . BINARY_EXTENSION;
    return $location;
 }

 /**
  * Returns thumbnail location
  */
 function getThumbnailImg($urlname, $pid, $type='mini', $prepend_slash=true, $ds="/", $visibility='visible') {
    $prefix = ($prepend_slash) ? $ds : "";
    $size = ($type === 'mini') ? "_sm" : "_med";
    
	if($visibility === 'visible') {
        return $prefix."static".$ds."projects".$ds.$urlname.$ds.$pid.$size.THUMBNAIL_EXTENSION;
	}
	else if($visibility === 'censbyadmin') {
        return $prefix."static".$ds."misc".$ds."thumbs".$ds."censoredbyadmin".$size.THUMBNAIL_EXTENSION;
	}
	else if($visibility === 'censbycomm') {
        return $prefix."static".$ds."misc".$ds."thumbs".$ds."censoredbycommunity".$size.THUMBNAIL_EXTENSION;
	}
	else if($visibility === 'delbyadmin') {
        return $prefix."static".$ds."misc".$ds."thumbs".$ds."deletedbyadmin".$size.THUMBNAIL_EXTENSION;
	}
	else if($visibility === 'delbyusr') {
        return $prefix."static".$ds."misc".$ds."thumbs".$ds."deletedbyuser".$size.THUMBNAIL_EXTENSION;
	}
 }

 /**
  * Returns buddy icon location
  */
 function getBuddyIcon($userid, $prepend_slash=true, $ds="/", $type='mini', $content = false, $withExtension = true) {
	if ($content == "safe") {
		$prefix = ($prepend_slash) ? $ds:"";
		if($type == 'mini')
			$prefix.="static".$ds."icons".$ds."buddy".$ds."00000"."_sm";
		else
			$prefix.="static".$ds."icons".$ds."buddy".$ds."00000"."_med";

		if($withExtension)
		{
			/*$config =& Configure::getInstance();
			if(0 && $config->userpic_suffix!="")
				return $prefix.".".$config->userpic_suffix;
			else*/
				return $prefix.".png";
		}
		else
			return $prefix;
	} else {
		$prefix = ($prepend_slash) ? $ds:"";
		if($type == 'mini')
			$prefix.="static".$ds."icons".$ds."buddy".$ds.$userid."_sm";
		else
			$prefix.="static".$ds."icons".$ds."buddy".$ds.$userid."_med";

		if($withExtension)
		{
			/*
			$config =& Configure::getInstance();
			if(isset($config->userpic_suffix) && $config->userpic_suffix!="")
				return $prefix.".".$config->userpic_suffix;
			else*/
				return $prefix.ICON_EXTENSION;
			/*}*/
		}
		else
			return $prefix;
	}

 }

 function getBuddyIconBySize($userid, $type='mini', $content=false, $timestamp = '') {
	return getBuddyIcon($userid, true, "/", $type, $content).'?t='. urlencode($timestamp);
 }

/* Discontinued
function getBuddyIconWidth($userid, $type='mini')
{
	$config =& Configure::getInstance();
	if($type=='mini')
	{
		if(isset($config->userpicsmwidth))
			return $config->userpicsmwidth;
		else
			return 28;
	}
	else if($type=='med')
	{
		if(isset($config->userpicmedwidth))
			return $config->userpicmedwidth;
		else
			return 90;
	}
}

function getBuddyIconHeight($userid, $type='mini')
{
	$config =& Configure::getInstance();
	if($type=='mini')
	{
		if(isset($config->userpicsmheight))
			return $config->userpicsmheight;
		else
			return 28;
	}
	else if($type=='med')
	{
		if(isset($config->userpicmedheight))
			return $config->userpicmedheight;
		else
			return 90;
	}
}
*/

 /**
  * Returns theme icon location
  */
 function getThemeIcon($themeid, $prepend_slash=true, $ds="/", $withExtension = true) {
     $prefix = ($prepend_slash) ? $ds:"";
     $prefix.="static".$ds."icons".$ds."gallery".$ds.$themeid;

     if($withExtension)
     {
	return $prefix.ICON_EXTENSION;
     }
     else
	return $prefix;
 }


 /**
  * Returns sprite icon location
  */
 function getSpriteIcon($sprite_id, $prepend_slash=true, $ds="/") {
     $prefix = ($prepend_slash) ? $ds:"";
     return $prefix."static".$ds."icons".$ds."sprite".$ds.$sprite_id.SPRITE_EXTENSION;
 }

 /*----------------------------------------------------------
	BEGIN UTIL FUNCTIONS
 -----------------------------------------------------------*/

/**
 * Validates the given variable as a number
 * @param mixed(int,string) $num
 */
function validateNumber($num) {
	if ($num == null)
		return false;
	return (intval($num) == $num);
}

/**
 * Validates the given variable as a string
 * @param mixed(int,string) $str
 */
function validateString($str) {
	if ($str == null)
		return false;
	return (!strcmp(trim($str), $str));
}

/**
 * Returns a true if the string argument
 * is empty
 * @param string $str
 */
function emptyString($str=null) {
	if (!$str)
		return true;
	$str = trim($str);
	return empty($str);
}

/**
 * Recursively creates all directories and subdirectories
 * specified in $dir. $dir is an absolute or relative path name
 */
function mkdirR($dir, $mode=0755) {
	if (is_dir($dir) || @mkdir($dir,$mode))
		return true;
	if (!mkdirR(dirname($dir),$mode))
		return false;
	return @mkdir($dir,$mode);
}

/**
 * Recursively trims array values within the
 * given multidimensional array. Uses array_walk.
 * Modifies the array
 */
function trim_array_byref(&$array) {
	if (is_array($array))
		array_walk($array, "trim_array_byref");
	else
		$array = trim($array);
}

/**
 * Recursively trims array values within th
 * given multidimensional array. Uses array_map.
 * Returns the array containing all the elements
 * after trimming
 */
function trim_array_byval($array) {
   return is_array($array) ? array_map('trim_array_byval', $array) : trim($array);
}

/**
 * TODO: check it works
 * Recursively applies $function_str callback function
 * to elements of the given multidemsional array $array.
 * Modifies the array. Assumes call back function only
 * takes in one argument
 */
function evaluateOnArray(&$array_item, $key, $function_name)
{
	if (is_array($array_item))
		array_walk($array_item, "evaluateOnArray", $function_name);
	else
		$array = call_user_func($funtion_name, $array);
}

function stampToDate($original) {
	$actual_date = date("r", strtotime($original));
	return substr($actual_date, 0, 17);
}

function friendlyDate($original) {
	// array of time period chunks
	$chunks = array(
		array(60 * 60 * 24 * 365 , ___('year', true) ),
		array(60 * 60 * 24 * 30 , ___('month', true) ),
		array(60 * 60 * 24 * 7, ___('week', true) ),
		array(60 * 60 * 24 , ___('day', true) ),
		array(60 * 60 , ___('hour', true) ),
		array(60 , ___('minute', true) ),
	);
	$today = time(); /* Current unix time */
	$since = $today - strtotime($original);

	// $j saves performing the count function each time around the loop
	for ($i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0) {
			// DEBUG print "<!-- It's $name -->n";
			break;
		}
	}
	$print = ($count == 1) ? '1 '.$name : "$count {$name}" . ___("s", true);
	if ($i + 1 < $j) {
		// now getting the second item
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		// add second item if it's greater than 0
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
			$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}" . ___("s", true);
		}
	}
	if ($since < 15) {
		$print = "Now";
	}
	return "$print"; // the "ago" is removed since this structure is not compatible with other languages...
}


function isInappropriate($content) {
    $whitelist = array("grape", "packing", "skyscraper");
    $blacklist = array("pendej","chinga","chingo", "verga","cabron","pucha","ash0le", "ashole","asshole", "assface","assh0le","asswipe","azzhole","bassterds","bastard","basterd","bitch","blow job","butthole","buttwipe","c0ck", "c0k", "cockbiter", "cock-biter", "cockhead","cock-head","cocksucker",  "dild0","dild0s","dildo","dildos","dilld0","dilld0s","dyke", "f u c k","fag1t","faget","fagg1t","faggit","faggot","fagit","fuck", "ƒuck", "fukah","fuken","fuker","fukin","fukk","fukkah","fukken","fukker","fukkin","g00k","h00r","h0ar","h0re","hoar","hoore","jackoff","jerk-off","jisim","jiss","jizm","jizz", "lezzian","massterbait","masst", "masstrbate","masterbate","masterbates","motha fuker","n1gr","nigga", "nastt","nigger","nigur","niiger","niigr","packi","packie","packy","paki","pakie","pecker","phuc","phuck","phuk","phuker","phukker","polac","polack","polak","poonani","pr1c","pr1ck","pr1k","pusse","pussee","pussy","puuke","puuker","scank","schlong","sh1t","sh1ter","sh1ts","shtter","sh1tz","shit","shyt","skanck","skank", "slut", "wh00r","wh0re","whore","rape","gay", "g4y", "it sux", "it sucks","lemonparty","goatse","meatspin","tubgirl","hai2u","bottleguy","goregasm","tubboy","youraresogay","bagslap","ls.gd");
// while the word gay is not inappropriate, our analysis show that it is almost always used as a derogatory term and given our resources moderating the website we decided to add it to this list

    $content = strtolower($content);
    
    preg_match_all('/'.implode($blacklist, '|').'/', $content, $mb);
    preg_match_all('/\b('.implode($whitelist, '|').')\b/', $content, $mw);
    $is_inappropriate = (count($mb[0]) > count($mw[0]));
    //found inappropriate
    if($is_inappropriate) {
        return true;
    }

    //full word check
    return preg_match("/(^|\b)(cock|cunt|fag|ass|crap|wanker|retard|puta|puto|hoor)((s|z)?)(\b|$)/", $content);
}


function getCountryNameByCode($code){
	$country_list = array("worldwide" =>"worldwide","A1" => "Anonymous Proxy" , "A2" => "Satellite Provider", "O1" => "Other Country","AD" => "Andorra", "AE" => "United Arab Emirates", "AF" => "Afghanistan", "AG" => "Antigua and Barbuda", "AI" => "Anguilla", "AL" => "Albania","AM" => "Armenia","AN" => "Netherlands Antilles","AO" => "Angola","AP" => "Asia/Pacific Region","AQ" => "Antarctica","AR" => "Argentina","AS" => "American Samoa","AT" => "Austria","AU" => "Australia","AW" => "Aruba","AX" => "Aland Islands","AZ" => "Azerbaijan","BA" => "Bosnia and Herzegovina","BB" => "Barbados","BD" => "Bangladesh","BE" => "Belgium","BF" => "Burkina Faso","BG" => "Bulgaria","BH" => "Bahrain","BI" => "Burundi","BJ" => "Benin","BM" => "Bermuda","BN" => "Brunei Darussalam","BO" => "Bolivia","BR" => "Brazil","BS" => "Bahamas","BT" => "Bhutan","BV" => "Bouvet Island","BW" => "Botswana","BY" => "Belarus","BZ" => "Belize","CA" => "Canada",
"CC" => "Cocos (Keeling) Islands","CD" => "Congo =>  The Democratic Republic of the","CF" => "Central African Republic","CG" => "Congo","CH" => "Switzerland","CI" => "Cote d'Ivoire","CK" => "Cook Islands","CL" => "Chile","CM" => "Cameroon","CN" => "China",
"CO" => "Colombia","CR" => "Costa Rica","CU" => "Cuba","CV" => "Cape Verde","CX" => "Christmas Island","CY" => "Cyprus","CZ" => "Czech Republic","DE" => "Germany","DJ" => "Djibouti","DK" => "Denmark","DM" => "Dominica","DO" => "Dominican Republic","DZ" => "Algeria","EC" => "Ecuador","EE" => "Estonia","EG" => "Egypt","EH" => "Western Sahara","ER" => "Eritrea","ES" => "Spain","ET" => "Ethiopia","EU" => "Europe","FI" => "Finland","FJ" => "Fiji","FK" => "Falkland Islands (Malvinas)","FM" => "Micronesia =>  Federated States of","FO" => "Faroe Islands","FR" => "France","GA" => "Gabon","GB" => "United Kingdom","GD" => "Grenada","GE" => "Georgia","GF" => "French Guiana","GG" => "Guernsey","GH" => "Ghana","GI" => "Gibraltar","GL" => "Greenland","GM" => "Gambia","GN" => "Guinea","GP" => "Guadeloupe","GQ" => "Equatorial Guinea","GR" => "Greece","GS"=> "South Georgia and the South Sandwich Islands","GT" => "Guatemala","GU" => "Guam","GW" => "Guinea-Bissau","GY" => "Guyana","HK" => "Hong Kong","HM" => "Heard Island and McDonald Islands","HN" => "Honduras","HR" => "Croatia","HT" => "Haiti","HU" => "Hungary","ID" => "Indonesia","IE" => "Ireland","IL" => "Israel","IM"=> "Isle of Man","IN" => "India","IO" => "British Indian Ocean Territory","IQ" => "Iraq","IR" => "Iran,Islamic Republic of","IS" => "Iceland","IT" => "Italy","JE" => "Jersey","JM" => "Jamaica","JO" => "Jordan","JP" => "Japan","KE" => "Kenya","KG" => "Kyrgyzstan","KH" => "Cambodia","KI" => "Kiribati","KM" => "Comoros","KN" => "Saint Kitts and Nevis","KP" => "Korea,Democratic People's Republic of","KR" => "Korea ,Republic of","KW" => "Kuwait","KY" => "Cayman Islands","KZ" => "Kazakhstan","LA" => "Lao People's Democratic Republic","LB" => "Lebanon","LC" => "Saint Lucia","LI" => "Liechtenstein","LK" => "Sri Lanka","LR" => "Liberia","LS" => "Lesotho","LT" => "Lithuania","LU" => "Luxembourg","LV" => "Latvia","LY" => "Libyan Arab Jamahiriya","MA" => "Morocco","MC" => "Monaco","MD" => "Moldova , Republic of","ME" => "Montenegro","MG" => "Madagascar","MH" => "Marshall Islands","MK" => "Macedonia","ML" => "Mali","MM" => "Myanmar","MN" => "Mongolia","MO" => "Macao","MP" => "Northern Mariana Islands","MQ" => "Martinique","MR" => "Mauritania","MS" => "Montserrat","MT" => "Malta","MU" => "Mauritius","MV" => "Maldives","MW" => "Malawi",
"MX" => "Mexico","MY" => "Malaysia","MZ" => "Mozambique","NA" => "Namibia","NC" => "New Caledonia","NE" => "Niger","NF" => "Norfolk Island","NG" => "Nigeria","NI" => "Nicaragua","NL" => "Netherlands","NO" => "Norway","NP" => "Nepal","NR" => "Nauru","NU" => "Niue","NZ" => "New Zealand","OM" => "Oman","PA" => "Panama","PE" => "Peru","PF" => "French Polynesia","PG" => "Papua New Guinea","PH" => "Philippines","PK" => "Pakistan","PL" => "Poland","PM" => "Saint Pierre and Miquelon","PN" => "Pitcairn","PR" => "Puerto Rico","PS" => "Palestinian Territory","PT" => "Portugal","PW" => "Palau","PY" => "Paraguay","QA" => "Qatar","RE" => "Reunion","RO" => "Romania","RS" => "Serbia","RU" => "Russian Federation","RW" => "Rwanda","SA" => "Saudi Arabia","SB" => "Solomon Islands","SC" => "Seychelles","SD" => "Sudan","SE" => "Sweden","SG" => "Singapore","SH" => "Saint Helena","SI" => "Slovenia","SJ" => "Svalbard and Jan Mayen","SK" => "Slovakia","SL" => "Sierra Leone","SM" => "San Marino","SN" => "Senegal","SO" => "Somalia","SR" => "Suriname","ST" => "Sao Tome and Principe","SV" => "El Salvador","SY" => "Syrian Arab Republic","SZ" => "Swaziland",
"TC" => "Turks and Caicos Islands","TD" => "Chad","TF" => "French Southern Territories","TG" => "Togo","TH" => "Thailand","TJ" => "Tajikistan","TK" => "Tokelau","TL" => "Timor-Leste","TM" => "Turkmenistan","TN" => "Tunisia","TO" => "Tonga","TR" => "Turkey","TT" => "Trinidad and Tobago","TV" => "Tuvalu","TW" => "Taiwan","TZ" => "Tanzania, United Republic of","UA" => "Ukraine","UG" => "Uganda","UM" => "United States Minor Outlying Islands","US" => "United States","UY" => "Uruguay","UZ" => "Uzbekistan","VA" => "Holy See (Vatican City State)","VC" => "Saint Vincent and the Grenadines","VE" => "Venezuela","VG" => "Virgin Islands, British","VI" => "Virgin Islands, U.S.","VN" => "Vietnam","VU" => "Vanuatu","WF" => "Wallis and Futuna","WS" => "Samoa","YE" => "Yemen",
"YT" => "Mayotte","ZA" => "South Africa","ZM" => "Zambia","ZW" => "Zimbabwe");
return $country_list[$code];
}

/* Helper function for redirect controller
	check if cookie lang find then return cookie land else browser lang if lang exists in language list.
*/
function get_client_language( $cookie_lang = null){

	$availableLanguages = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja', 'kor' => 'ko', 'ara' => 'ar','zh-cn' => 'zh-hans','zh-tw' => 'zh-hant','pol' => 'pl','ukr' => 'uk' ,'scr' => 'hr');
	if(isset($availableLanguages[$cookie_lang])){ 
		return $availableLanguages[$cookie_lang];
	}else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
		$langs=explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		//start going through each one
		foreach ($langs as $value){
			$choice=substr($value,0,2);
			if(in_array($choice, $availableLanguages)){
				return $choice;
			}else{
			return null;
			}
			
		}
	}else{
		return null;
	}	
	
}



/**
 *
 * Returns a translated string if one is found, or the submitted message if not found.
 *
 * @param string $singular Text to translate
 * @param boolean $return Set to true to return translated string, or false to echo
 * @return mixed translated string if $return is false string will be echoed
 */
	function ___($singular, $return = false) {
		/*
		 * If you use CakePHP 1.2, you can set the following variable
		 * to "true" to use the built-in translation mechanism.
		*/
		$we_use_cake_1_2 = false;   //default value is "false" because cake 1.2 isnt\' released yet

		if ($we_use_cake_1_2) {
			if ($return === false) {
				echo __($singular, $return);
			} else {
				return __($singular, $return);
			}
		} else {
			if (!class_exists('I18n')) {
				require_once('vendors/i18n.php'); //loading cakephp1.2.-i18n-module from the vendor directory
			}

			//$calledFrom = debug_backtrace();
			//$dir = dirname($calledFrom[0]['file']);
			$dir = dirname(__FILE__);

			if ($return === false) {
				$translated = I18n::translate($singular, null, null, 5, null, $dir);
				echo $translated;
			} else {
				$translated = I18n::translate($singular, null, null, 5, null, $dir);
				return $translated;
			}
		}
	}
	
	$otherBlockSet = array(  //MOTOR AND WTF (feel free to categorize what you recognize) (no slice of pie for these guys)
    "allMotorsOff",
    "allMotorsOn",
    "comment_",
    "motorOnFor_elapsed_from_",
    "startMotorPower_",
    "setMotorDirection_",
    "yourself",
    "askYahoo",
    "wordOfTheDay_",
    "jokeOfTheDay_",
    "synonym_",
    "info_fromZip_",
    "scratchrInfo_forUser_",
    "other"
);
?>
