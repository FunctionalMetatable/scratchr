<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/


// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');	// When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');		// For HTTP/1.0 compability


// Load the template
if (defined('PUN_ADMIN_CONSOLE'))
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/admin.tpl');
else if (defined('PUN_HELP'))
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/help.tpl');
else
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/main.tpl');


// START SUBST - <pun_include "*">
while (preg_match('#<pun_include "([^/\\\\]*?)\.(php[45]?|inc|html?|txt)">#', $tpl_main, $cur_include))
{
	if (!file_exists(PUN_ROOT.'include/user/'.$cur_include[1].'.'.$cur_include[2]))
		error('Unable to process user include '.htmlspecialchars($cur_include[0]).' from template main.tpl. There is no such file in folder /include/user/');

	ob_start();
	include PUN_ROOT.'include/user/'.$cur_include[1].'.'.$cur_include[2];
	$tpl_temp = ob_get_contents();
	$tpl_main = str_replace($cur_include[0], $tpl_temp, $tpl_main);
    ob_end_clean();
}
// END SUBST - <pun_include "*">


// START SUBST - <pun_content_direction>
$tpl_main = str_replace('<pun_content_direction>', $lang_common['lang_direction'], $tpl_main);
// END SUBST - <pun_content_direction>


// START SUBST - <pun_char_encoding>
$tpl_main = str_replace('<pun_char_encoding>', $lang_common['lang_encoding'], $tpl_main);
// END SUBST - <pun_char_encoding>


// START SUBST - <pun_head>
ob_start();

// Is this a page that we want search index spiders to index?
if (!defined('PUN_ALLOW_INDEX'))
	echo '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />'."\n";

?>
<title><?php echo $page_title ?></title>
<link rel="stylesheet" type="text/css" href="style/<?php echo $pun_user['style'].'.css' ?>" />
<?php

if (defined('PUN_ADMIN_CONSOLE'))
	echo '<link rel="stylesheet" type="text/css" href="style/imports/base_admin.css" />'."\n";

if (isset($required_fields))
{
	// Output JavaScript to validate form (make sure required fields are filled out)

?>
<script type="text/javascript">
<!--
function process_form(the_form)
{
	var element_names = new Object()
<?php

	// Output a JavaScript array with localised field names
	while (list($elem_orig, $elem_trans) = @each($required_fields))
		echo "\t".'element_names["'.$elem_orig.'"] = "'.addslashes(str_replace('&nbsp;', ' ', $elem_trans)).'"'."\n";

?>

	if (document.all || document.getElementById)
	{
		for (i = 0; i < the_form.length; ++i)
		{
			var elem = the_form.elements[i]
			if (elem.name && elem.name.substring(0, 4) == "req_")
			{
				if (elem.type && (elem.type=="text" || elem.type=="textarea" || elem.type=="password" || elem.type=="file") && elem.value=='')
				{
					alert("\"" + element_names[elem.name] + "\" <?php echo $lang_common['required field'] ?>")
					elem.focus()
					return false
				}
			}
		}
	}

	return true
}
// -->

var browserType;

if (document.layers) {browserType = "nn4"}
if (document.all) {browserType = "ie"}
if (window.navigator.userAgent.toLowerCase().match("gecko")) {
 browserType= "gecko"
}


  window.onload = function() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };


function divsdisappear() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };



function motion() {
 
  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "inline";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };





function looks() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "inline";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };
  
function sound() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "inline";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };
  
function pen() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "inline";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };
  
function control() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "inline";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };

function sensing() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "inline";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };
  

function numbers() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "inline";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "none";
  };
  
function variables() {

  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("motion")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("motion")');
  else
     document.poppedLayer =   
        eval('document.layers["motion"]');
  document.poppedLayer.style.display = "none";
    
    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("looks")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("looks")');
  else
     document.poppedLayer = 
         eval('document.layers["looks"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sound")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sound")');
  else
     document.poppedLayer = 
         eval('document.layers["sound"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("pen")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("pen")');
  else
     document.poppedLayer = 
         eval('document.layers["pen"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("control")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("control")');
  else
     document.poppedLayer = 
         eval('document.layers["control"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("sensing")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("sensing")');
  else
     document.poppedLayer = 
         eval('document.layers["sensing"]');
  document.poppedLayer.style.display = "none";

    if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("numbers")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("numbers")');
  else
     document.poppedLayer = 
         eval('document.layers["numbers"]');
  document.poppedLayer.style.display = "none";
  
      if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById("variables")');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById("variables")');
  else
     document.poppedLayer = 
         eval('document.layers["variables"]');
  document.poppedLayer.style.display = "inline";
  };



function insertAtCursor(myField, myValue) {
//IE support
if (document.selection) {
myField.focus();
sel = document.selection.createRange();
sel.text = myValue;
}
//MOZILLA/NETSCAPE support
else if (myField.selectionStart || myField.selectionStart == '0') {
var startPos = myField.selectionStart;
var endPos = myField.selectionEnd;
myField.value = myField.value.substring(0, startPos)
+ myValue
+ myField.value.substring(endPos, myField.value.length);
} else {
myField.value += myValue;
}
}


function LoadMotion()
{
	document.images["move_steps"].src= "img/sb_no_nums/move_steps.png";
	document.images["turncw_degrees"].src= "img/sb_no_nums/turncw_degrees.png";
	document.images["turnccw_degrees"].src= "img/sb_no_nums/turnccw_degrees.png";
	document.images["pointindirection_"].src= "img/sb_no_nums/pointindirection_.png";
	document.images["pointtowards_"].src= "img/sb_no_nums/pointtowards_.png";
	document.images["gotox_y_"].src= "img/sb_no_nums/gotox_y_.png";
	document.images["goto"].src= "img/sb_no_nums/goto.png";
	document.images["glide_secsto_x_y_"].src= "img/sb_no_nums/glide_secsto_x_y_.png";
	document.images["changexby_"].src= "img/sb_no_nums/changexby_.png";
	document.images["setxto_"].src= "img/sb_no_nums/setxto_.png";
	document.images["changeyby_"].src= "img/sb_no_nums/changeyby_.png";
	document.images["setyto_"].src= "img/sb_no_nums/setyto_.png";
	document.images["ifonedgebounce"].src= "img/sb_no_nums/ifonedgebounce.png";
	document.images["xposition"].src= "img/sb_no_nums/xposition.png";
	document.images["yposition"].src= "img/sb_no_nums/yposition.png";
	document.images["direction"].src= "img/sb_no_nums/direction.png";
	return true;
}

function LoadLooks()
{
	document.images["switchtocostume_"].src= "img/sb_no_nums/switchtocostume_.png";
	document.images["nextcostume"].src= "img/sb_no_nums/nextcostume.png";
	document.images["say_for_secs"].src= "img/sb_no_nums/say_for_secs.png";
	document.images["say_"].src= "img/sb_no_nums/say_.png";
	document.images["think_for_secs"].src= "img/sb_no_nums/think_for_secs.png";
	document.images["think_"].src= "img/sb_no_nums/think_.png";
	document.images["change_effectby_"].src= "img/sb_no_nums/change_effectby_.png";
	document.images["set_effectto_"].src= "img/sb_no_nums/set_effectto_.png";
	document.images["cleargraphiceffects"].src= "img/sb_no_nums/cleargraphiceffects.png";
	document.images["changesizeby_"].src= "img/sb_no_nums/changesizeby_.png";
	document.images["setsizeto_"].src= "img/sb_no_nums/setsizeto_.png";
	document.images["size"].src= "img/sb_no_nums/size.png";
	document.images["show"].src= "img/sb_no_nums/show.png";
	document.images["hide"].src= "img/sb_no_nums/hide.png";
	document.images["gotofront"].src= "img/sb_no_nums/gotofront.png";
	document.images["goback_layers"].src= "img/sb_no_nums/goback_layers.png";
}

function LoadSound()
{
	document.images["playsound_"].src= "img/sb_no_nums/playsound_.png";
	document.images["playsound_andwait"].src= "img/sb_no_nums/playsound_andwait.png";
	document.images["stopallsounds"].src= "img/sb_no_nums/stopallsounds.png";
	document.images["playdrum_for_secs"].src= "img/sb_no_nums/playdrum_for_secs.png";
	document.images["playnote_for_secs"].src= "img/sb_no_nums/playnote_for_secs.png";
	document.images["setinstrumentto_"].src= "img/sb_no_nums/setinstrumentto_.png";
}

function LoadPen()
{
	document.images["clear"].src= "img/sb_no_nums/clear.png";
	document.images["pendown"].src= "img/sb_no_nums/pendown.png";
	document.images["penup"].src= "img/sb_no_nums/penup.png";
	document.images["changepencolorby_"].src= "img/sb_no_nums/changepencolorby_.png";
	document.images["setpencolorto_"].src= "img/sb_no_nums/setpencolorto_.png";
	document.images["changepenshadeby_"].src= "img/sb_no_nums/changepenshadeby_.png";
	document.images["setpenshadeto_"].src= "img/sb_no_nums/setpenshadeto_.png";
	document.images["changepensizeby_"].src= "img/sb_no_nums/changepensizeby_.png";
	document.images["setpensizeto_"].src= "img/sb_no_nums/setpensizeto_.png";
	document.images["stamp"].src= "img/sb_no_nums/stamp.png";
}

function LoadControl()
{
	document.images["whengreenflagclicked"].src= "img/sb_no_nums/whengreenflagclicked.png";
	document.images["when_keypressed"].src= "img/sb_no_nums/when_keypressed.png";
	document.images["when_clicked"].src= "img/sb_no_nums/when_clicked.png";
	document.images["wait_secs"].src= "img/sb_no_nums/wait_secs.png";
	document.images["forever"].src= "img/sb_no_nums/forever.png";
	document.images["repeat_"].src= "img/sb_no_nums/repeat_.png";
	document.images["broadcast_"].src= "img/sb_no_nums/broadcast_.png";
	document.images["broadcast_andwait"].src= "img/sb_no_nums/broadcast_andwait.png";
	document.images["whenireceive_"].src= "img/sb_no_nums/whenireceive_.png";
	document.images["foreverif_"].src= "img/sb_no_nums/foreverif_.png";
	document.images["if_"].src= "img/sb_no_nums/if_.png";
	document.images["endcontrol"].src= "img/sb_no_nums/endcontrol.png";
	document.images["else"].src= "img/sb_no_nums/else.png";
	document.images["waituntil"].src= "img/sb_no_nums/waituntil.png";
	document.images["repeatuntil"].src= "img/sb_no_nums/repeatuntil.png";
	document.images["stopscript"].src= "img/sb_no_nums/stopscript.png";
	document.images["stopall"].src= "img/sb_no_nums/stopall.png";
}

function LoadSensing()
{
	document.images["mousex"].src= "img/sb_no_nums/mousex.png";
	document.images["mousey"].src= "img/sb_no_nums/mousey.png";
	document.images["mousedownq"].src= "img/sb_no_nums/mousedownq.png";
	document.images["key_pressedq"].src= "img/sb_no_nums/key_pressedq.png";
	document.images["touching_q"].src= "img/sb_no_nums/touching_q.png";
	document.images["touchingcolor_q"].src= "img/sb_no_nums/touchingcolor_q.png";
	document.images["color_isover_q"].src= "img/sb_no_nums/color_isover_q.png";
	document.images["distanceto_"].src= "img/sb_no_nums/distanceto_.png";
	document.images["resettimer"].src= "img/sb_no_nums/resettimer.png";
	document.images["timer"].src= "img/sb_no_nums/timer.png";
	document.images["loudness"].src= "img/sb_no_nums/loudness.png";
	document.images["loudq"].src= "img/sb_no_nums/loudq.png";
}

function LoadNumbers()
{
	document.images["_plus_"].src= "img/sb_no_nums/_plus_.png";
	document.images["_minus_"].src= "img/sb_no_nums/_minus_.png";
	document.images["_times_"].src= "img/sb_no_nums/_times_.png";
	document.images["_dividedby_"].src= "img/sb_no_nums/_dividedby_.png";
	document.images["pickrandom_to_"].src= "img/sb_no_nums/pickrandom_to_.png";
	document.images["_lessthan_"].src= "img/sb_no_nums/_lessthan_.png";
	document.images["_equals_"].src= "img/sb_no_nums/_equals_.png";
	document.images["_greaterthan_"].src= "img/sb_no_nums/_greaterthan_.png";
	document.images["_and_"].src= "img/sb_no_nums/_and_.png";
	document.images["_or_"].src= "img/sb_no_nums/_or_.png";
	document.images["not_"].src= "img/sb_no_nums/not_.png";
	document.images["_mod_"].src= "img/sb_no_nums/_mod_.png";
	document.images["abs_"].src= "img/sb_no_nums/abs_.png";
	document.images["round_"].src= "img/sb_no_nums/round_.png";
}

function LoadVariables()
{
	document.images["change_by_"].src= "img/sb_no_nums/change_by_.png";
	document.images["set_to_"].src= "img/sb_no_nums/set_to_.png";
	document.images["_var"].src= "img/sb_no_nums/_var.png";
}

</script>
<?php

}

$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
if (strpos($user_agent, 'msie') !== false && strpos($user_agent, 'windows') !== false && strpos($user_agent, 'opera') === false)
	echo '<script type="text/javascript" src="style/imports/minmax.js"></script>';

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_head>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_head>


// START SUBST - <body>
if (isset($focus_element))
{
	$tpl_main = str_replace('<body onload="', '<body onload="document.getElementById(\''.$focus_element[0].'\').'.$focus_element[1].'.focus();', $tpl_main);
	$tpl_main = str_replace('<body>', '<body onload="document.getElementById(\''.$focus_element[0].'\').'.$focus_element[1].'.focus()">', $tpl_main);
}
// END SUBST - <body>


// START SUBST - <pun_page>
$tpl_main = str_replace('<pun_page>', htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php')), $tpl_main);
// END SUBST - <pun_page>


// START SUBST -<pun_mystuff>
if ($pun_user['is_guest']){
	//commentd for not dispaly "My stuff" link in header when user is not logged in.
    //$tpl_main = str_replace('<pun_mystuff>', '<li><a href="/login">my stuff</a></li>',$tpl_main);
}
else{
    $tpl_main = str_replace('<pun_mystuff>', "<li ><a href='/users/" . $pun_user['username'] . "'>my stuff</a></li>", $tpl_main);
}

// START SUBST - <pun_logonstatus>
if ($pun_user['is_guest']){
$tpl_main = str_replace('<pun_logonstatus>', '<a href="../login" onclick="return(showLogin())">Login</a> or <a href="../signup">Signup</a> for an account', $tpl_main);
}
else{
$tpl_main = str_replace('<pun_logonstatus>', 'Welcome, <a href="/users/'.$pun_user['username'].'">'.$pun_user['username'].'</a> | <a href="../logout">Logout</a>', $tpl_main);
}
// END SUBST - <pun_logonstatus>

// START SUBST - <pun_title>
$tpl_main = str_replace('<pun_title>', '<h1><span>'.pun_htmlspecialchars($pun_config['o_board_title']).'</span></h1>', $tpl_main);
// END SUBST - <pun_title>


// START SUBST - <pun_desc>
$tpl_main = str_replace('<pun_desc>', '<p><span>'.$pun_config['o_board_desc'].'</span></p>', $tpl_main);
// END SUBST - <pun_desc>


// START SUBST - <pun_navlinks>
$tpl_main = str_replace('<pun_navlinks>','<div id="brdmenu" class="inbox">'."\n\t\t\t". generate_navlinks()."\n\t\t".'</div>', $tpl_main);
// END SUBST - <pun_navlinks>

// START SUBST - <pun_current_page>
$tpl_main = str_replace('<pun_current_page>',$_SERVER['REQUEST_URI'], $tpl_main);
// END SUBST - <pun_current_page>

// START SUBST - <pun_status>
if ($pun_user['is_guest'])
	$tpl_temp = '<div id="brdwelcome" class="inbox">'."\n\t\t\t".'<p>'.$lang_common['Not logged in'].'</p>'."\n\t\t".'</div>';
else
{
	$tpl_temp = '<div id="brdwelcome" class="inbox">'."\n\t\t\t".'<ul class="conl">'."\n\t\t\t\t".'<li>'.$lang_common['Logged in as'].' <strong>'.pun_htmlspecialchars($pun_user['username']).'</strong></li>'."\n\t\t\t\t".'<li>'.$lang_common['Last visit'].': '.format_time($pun_user['last_visit']).'</li>';

	if ($pun_user['g_id'] < PUN_GUEST)
	{
		$result_header = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());

		if ($db->result($result_header))
			$tpl_temp .= "\n\t\t\t\t".'<li class="reportlink"><strong><a href="admin_reports.php">There are new reports</a></strong></li>';

		if ($pun_config['o_maintenance'] == '1')
			$tpl_temp .= "\n\t\t\t\t".'<li class="maintenancelink"><strong><a href="admin_options.php#maintenance">Maintenance mode is enabled!</a></strong></li>';
	}

	if (in_array(basename($_SERVER['PHP_SELF']), array('index.php', 'search.php')))
		$tpl_temp .= "\n\t\t\t".'</ul>'."\n\t\t\t".'<ul class="conr">'."\n\t\t\t\t".'<li><a href="search.php?action=show_new">'.$lang_common['Show new posts'].'</a></li>'."\n\t\t\t\t".'<li><a href="misc.php?action=markread">'.$lang_common['Mark all as read'].'</a></li>'."\n\t\t\t".'</ul>'."\n\t\t\t".'<div class="clearer"></div>'."\n\t\t".'</div>';
	else
		$tpl_temp .= "\n\t\t\t".'</ul>'."\n\t\t\t".'<div class="clearer"></div>'."\n\t\t".'</div>';
}

$tpl_main = str_replace('<pun_status>', $tpl_temp, $tpl_main);
// END SUBST - <pun_status>


// START SUBST - <pun_announcement>
if ($pun_config['o_announcement'] == '1')
{
	ob_start();

?>
<div id="announce" class="block">
	<h2><span><?php echo $lang_common['Announcement'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div><?php echo $pun_config['o_announcement_message'] ?></div>
		</div>
	</div>
</div>
<?php

	$tpl_temp = trim(ob_get_contents());
	$tpl_main = str_replace('<pun_announcement>', $tpl_temp, $tpl_main);
	ob_end_clean();
}
else
	$tpl_main = str_replace('<pun_announcement>', '', $tpl_main);
// END SUBST - <pun_announcement>


// START SUBST - <pun_main>
ob_start();


define('PUN_HEADER', 1);
