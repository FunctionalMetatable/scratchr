<?php

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
 define("NUM_THEME_PROJECTS", 4);
 define("NUM_NEW_MEMBERS", 4);
 define("NUM_RECENT_VISITORS", 4);
 define("MAX_LENGTH_PNAME_HOME", 20);
 define("MEMCACHE_PREFIX", "matea.dev");
 
 define("NUM_MAX_COMMENT_FLAGS", 1);
 define("NUM_MAX_PROJECT_FLAGS", 3);
 define("NUM_MAX_TAG_FLAGS", 1);
 define("MAX_COMMENT_LENGTH", 500);
 define("COMMENT_SPAM_MAX_DAYS", 3);
 define("COMMENT_SPAM_CLEAR_COMMENTS", 2);
 define("COMMENT_SPAM_CLEAR_MINUTES", 2);

/**
 * Email Address Related to Flagging
 */
 define("REPLY_TO_FLAGGED_PCOMMENT", "help@scratch.mit.edu");
 define("TO_FLAGGED_PCOMMENT", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_PROJECT", "help@scratch.mit.edu");
 define("TO_FLAGGED_PROJECT", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_GCOMMENT", "help@scratch.mit.edu");
 define("TO_FLAGGED_GCOMMENT", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_PTAG", "help@scratch.mit.edu");
 define("TO_FLAGGED_PTAG", "caution@scratch.mit.edu");
 define("REPLY_TO_FLAGGED_GTAG", "help@scratch.mit.edu");
 define("TO_FLAGGED_GTAG", "caution@scratch.mit.edu");
 
 /**
  * Themes config
  */
 define("NUM_NEW_THEME_PROJECTS", 0);

 /**
  * myscratchr configs
  */
 define("NUM_MYSCRATCHR_PROJECTS", 0);
 define("NUM_MYSCRATCHR_FRIENDS", 6);
 define("MAX_SHARIABLES", 3);

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

 /**
  * External webservices interface key for scratchr
  */
 define("SCRATCH_KEY", "ch4ng3me");

 /**
  * Tag clound consts
  */
 define("TAG_CLOUD_HOME", 30);
 define("TAG_CLOUD_BIG", 500);

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

define('CONTENT_STATUS', "safe");

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
 function getThumbnailImg($urlname, $pid, $type='mini', $prepend_slash=true, $ds="/") {
    $prefix = ($prepend_slash) ? $ds:"";
    return ($type === 'mini') ?
        $prefix."static".$ds."projects".$ds.$urlname.$ds.$pid."_sm".THUMBNAIL_EXTENSION:
        $prefix."static".$ds."projects".$ds.$urlname.$ds.$pid."_med".THUMBNAIL_EXTENSION;
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

	function isInappropriate($str) {
		#words or just part of words
		$badwords = array("pendej","chinga","chingo", "verga","cabron","pucha","ash0le", "ashole","asshole", "assface","assh0le","asswipe","azzhole","bassterds","bastard","basterd","bitch","blow job","butthole","buttwipe","c0ck", "c0k", "cockbiter", "cock-biter", "cockhead","cock-head","cocksucker",  "dild0","dild0s","dildo","dildos","dilld0","dilld0s","dyke", "f u c k","fag1t","faget","fagg1t","faggit","faggot","fagit","fuck", "fukah","fuken","fuker","fukin","fukk","fukkah","fukken","fukker","fukkin","g00k"," h00r","h0ar","h0re","hoar","hoore","jackoff","jerk-off","jisim","jiss","jizm","jizz", "lezzian","massterbait","masst", "masstrbate","masterbate","masterbates","motha fuker","n1gr","nigga", "nastt","nigger","nigur","niiger","niigr","packi","packie","packy","paki","pakie","pecker","phuc","phuck","phuk","phuker","phukker","polac","polack","polak","poonani","pr1c","pr1ck","pr1k","pusse","pussee","pussy","puuke","puuker","scank","schlong","sh1t","sh1ter","sh1ts","shtter","sh1tz","shit","shyt","skanck","skank", "slut", "wh00r","wh0re","whore","rape");
		#strpos is faster than preg_match
		foreach ($badwords as $badword) { $pos = strpos(strtolower($str), $badword); if ($pos === false) {} else { return true;} }
		#full words
		return preg_match("/(^|\b)(cock|cunt|fag|ass|crap|wanker|retard|puta|puto|hoor)((s|z)?)(\b|$)/i",$str);
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
				require_once('../vendors/i18n.php'); //loading cakephp1.2.-i18n-module from the vendor directory
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
?>
