<?php
class UtilHelper extends Helper
	{
		function username($username, $role, $extra = null) {
    		$asterisk = "";
    		if($role=='admin') {
        		$asterisk = "<a  class='asterisk' href=" .INFO_URL. '/Admins' ." >*</a>";
				
    		}
    		
			if($extra){
					$asterisk .= " ";
					return "<a href=\"/users/{$username}\">{$username}</a>" .___("'s", true). $asterisk ;
				}
				else
				return "<a href=\"/users/{$username}\">{$username}</a>" . $asterisk;
		}
		
	}
 
?>
