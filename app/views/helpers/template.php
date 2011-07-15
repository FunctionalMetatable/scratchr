<?php
/**
 * Template Helper
 * Helps creating notification messages from notification template and otehr parameters
 * @author Anupom Syam http://syamantics.com
 */
class TemplateHelper extends Helper
{
	function create($notification, $username) {
		$message = '';
	    $search = array('{to_user_name}','{from_user_name}',
		    '{gallery_id}', '{gallery_name}',
		    '{project_id}', '{project_owner_name}', '{project_name}',
            '{comment_id}');
		if($notification['notif_type'] == 'notification') {
			if($notification['is_admin']) {
				if(!empty($notification['extra'])) {
					$message = $notification['extra'];
				}
				else {
					$message = $notification['template'];
				}
				$message = str_replace($search,
							array(
								$username,
								$notification['from_user_name'],
								$notification['gallery_id'],
								$notification['gallery_name'],
								$notification['project_id'],
								$notification['project_owner_name'],
								strip_tags($notification['project_name']),
                                $notification['comment_id']
							),
							$message
						);
			}
			else {
				$message = str_replace($search,
							array(
								$username,
								$notification['from_user_name'],
								$notification['gallery_id'],
								$notification['gallery_name'],
								$notification['project_id'],
								$notification['project_owner_name'],
								strip_tags($notification['project_name']),
                                $notification['comment_id']
							),
							$notification['template']
						);
				if(!empty($notification['extra'])) {
					$message = vsprintf($message, explode('|', $notification['extra']));
				}
			}
		}
		else {
			$message = '<a href="/users/'.$notification['from_user_name'].'">'
			. $notification['from_user_name'].'</a>'
			. ___(" has added you to her or his list of friends.", 'false');
		}
		
		return $message;
	}  
}         
?>
