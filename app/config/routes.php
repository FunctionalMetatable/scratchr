<?php
/* SVN FILE: $Id: routes.php 7296 2008-06-27 09:09:03Z gwoo $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 7296 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2008-06-27 02:09:03 -0700 (Fri, 27 Jun 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
	
//top level pages
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */
Router::connect('/', array('controller' => 'home', 'action' => 'index', 'home'));
Router::connect('/notifications', array('controller' => 'notifications', 'action' => 'index', 'home'));
Router::connect('/oldnotifications', array('controller' => 'oldnotifications', 'action' => 'index', 'home'));
Router::connect('/download', array('controller' => 'dusers', 'action' => 'add'));
Router::connect('/password_recovery', array('controller' => 'users', 'action' => 'password_recovery'));
Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
Router::connect('/confirm_signup', array('controller' => 'users', 'action' => 'confirm_signup'));
Router::connect('/signup', array('controller' => 'users', 'action' => 'signup'));
Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
Router::connect('/tags', array('controller' => 'tags', 'action' => 'index'));
Router::connect('/administration', array('controller' => 'administration', 'action' => 'index'));
Router::connect('/galleries', array('controller' => 'galleries', 'action' => 'index'));
Router::connect('/sitemap_(.*)_(.*).xml', array('controller' => 'sitemaps', 'action' => 'get'));
Router::connect('/sitemap_(.*)_(.*).xml.gz', array('controller' => 'sitemaps', 'action' => 'get'));
Router::connect('/sitemap.xml', array('controller' => 'sitemaps', 'action' => 'index'));
Router::connect('/sitemap.xml.gz', array('controller' => 'sitemaps', 'action' => 'index'));
Router::connect('/robots.txt', array('controller' => 'sitemaps', 'action' => 'robots'));

// Routes the language controller
Router::connect('/lang/*', array('controller' => 'p28n', 'action' => 'change'));

// User pages
Router::connect('/users/flag_list', array('controller' => 'users', 'action' => 'flag_list'));
Router::connect('/users/update', array('controller' => 'users', 'action' => 'update'));
Router::connect('/users/send_request', array('controller' => 'users', 'action' => 'send_request'));
Router::connect('/users/updatepic/*', array('controller' => 'users', 'action' => 'updatepic'));
Router::connect('/users/curator/*', array('controller' => 'users', 'action' => 'curator'));
Router::connect('/users/uncurator/*', array('controller' => 'users', 'action' => 'uncurator'));
Router::connect('/users/showgalleries/*', array('controller' => 'users', 'action' => 'showgalleries'));
Router::connect('/users/comment_list/*', array('controller' => 'users', 'action' => 'comment_list'));
Router::connect('/users/ignore_user/*', array('controller' => 'users', 'action' => 'ignore_user'));
Router::connect('/users/remove_ignore_user/*', array('controller' => 'users', 'action' => 'remove_ignore_user'));
Router::connect('/users/add_ignore_user/*', array('controller' => 'users', 'action' => 'add_ignore_user'));
Router::connect('/users/updatepass/*', array('controller' => 'users', 'action' => 'updatepass'));
Router::connect('/users/updateemail/*', array('controller' => 'users', 'action' => 'updateemail'));
Router::connect('/users/notify/*', array('controller' => 'users', 'action' => 'notify'));
Router::connect('/users/removefriend/*', array('controller' => 'users', 'action' => 'removefriend'));
Router::connect('/users/removegallery/*', array('controller' => 'users', 'action' => 'removegallery'));
Router::connect('/users/showfriends/*', array('controller' => 'users', 'action' => 'showfriends'));
Router::connect('/users/renderProjects/*', array('controller' => 'users', 'action' => 'renderProjects'));
Router::connect('/users/renderFriendProjects/*', array('controller' => 'users', 'action' => 'renderFriendProjects'));
Router::connect('/users/render_comment_list/*', array('controller' => 'users', 'action' => 'render_comment_list'));
Router::connect('/users/loginsu/*', array('controller' => 'users', 'action' => 'loginsu'));
Router::connect('/users/pwdreset/*', array('controller' => 'users', 'action' => 'pwdreset'));
Router::connect('/users/set_email/*', array('controller' => 'users', 'action' => 'set_email'));
Router::connect('/users/removefavorites', array('controller' => 'users', 'action' => 'removefavorites'));
Router::connect('/users/countries/*', array('controller' => 'users', 'action' => 'countries'));
Router::connect('/users/*', array('controller' => 'users', 'action' => 'view'));


// Project pages

Router::connect('/projects/gallerylist/*', array('controller' => 'projects', 'action' => 'gallerylist'));
Router::connect('/projects/feature/*', array('controller' => 'projects', 'action' => 'feature'));
Router::connect('/projects/defeature/*', array('controller' => 'projects', 'action' => 'defeature'));
Router::connect('/projects/censor', array('controller' => 'projects', 'action' => 'censor'));
Router::connect('/projects/uncensor', array('controller' => 'projects', 'action' => 'uncensor'));
Router::connect('/projects/lock/*', array('controller' => 'projects', 'action' => 'lock'));
Router::connect('/projects/markcomment/*', array('controller' => 'projects', 'action' => 'markcomment'));
Router::connect('/projects/comment_reply/*', array('controller' => 'projects', 'action' => 'comment_reply'));
Router::connect('/projects/delete_comment/*', array('controller' => 'projects', 'action' => 'delete_comment'));
Router::connect('/projects/deleteprojects/*', array('controller' => 'projects', 'action' => 'deleteprojects'));
Router::connect('/projects/display_replies/*', array('controller' => 'projects', 'action' => 'display_replies'));
Router::connect('/projects/show_comment_reply/*', array('controller' => 'projects', 'action' => 'show_comment_reply'));
Router::connect('/projects/set_status/*', array('controller' => 'projects', 'action' => 'set_status'));
Router::connect('/projects/set_attribute/*', array('controller' => 'projects', 'action' => 'set_attribute'));
Router::connect('/projects/expandDescription/*', array('controller' => 'projects', 'action' => 'expandDescription'));
Router::connect('/projects/deltag/*', array('controller' => 'projects', 'action' => 'deltag'));
Router::connect('/projects/markTag/*', array('controller' => 'projects', 'action' => 'markTag'));
Router::connect('/projects/upgradeTag/*', array('controller' => 'projects', 'action' => 'upgradeTag'));
Router::connect('/projects/moreprojects/*', array('controller' => 'projects', 'action' => 'moreprojects'));
Router::connect('/projects/renderComments/*', array('controller' => 'projects', 'action' => 'renderComments'));
Router::connect('/projects/:username/:id', array('controller' => 'projects', 'action' => 'view'),
													array(
													   'pass' => array('username','id'),
														array('id' => '([0-9]+)','username'=>'([A-Za-z0-9-_]+)')  
													)
				);
Router::connect('/projects/:username/:id/:action', array('controller' => 'projects', 'action' => $Action),
													array(
													   'pass' => array('username','id'),
													   array('id' => '([0-9]+)','username'=>'([A-Za-z0-9-_]+)')
													)
				);

//Then we connect url '/test' to our test controller. This is helpful in developement.
Router::connect('/tests', array('controller' => 'tests', 'action' => 'index'));

//All other page calls will be routed  through the pages_controller
Router::connect('/:pagename', array('controller' => 'pages', 'action' => 'display'),
									array(
									   'pass' => array('pagename'),
										array('pagename'=>'([A-Za-zs]+)')   
									)
				);

Router::connect('/pages/:pagename', array('controller' => 'pages', 'action' => 'display'),
										array(
										'pass' => array('pagename'),
										array('pagename'=>'([A-Za-zs]+)')
									)
				);
?>