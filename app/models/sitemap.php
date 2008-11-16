<?php
/**
 * Model for the SitemapController
 *
 * @author	Anupom Syam
 */
class Sitemap extends AppModel
{
	var $useTable = false;
	
	/**
	* Returns number of user sitemap files
	* counts total number of visible users and then divides it by max links allowed per sitemap
	*
	* @return int	number of user sitemap files
	*/
	function countUserSitemaps( ) {
		App::import('Model', 'User');
		$this->User =& ClassRegistry::init('User');
		$total = $this->User->find('count', array(
								'conditions' => array('User.status' => 'normal'),
								'recursive' => -1)
							);
		return ceil($total / MAX_LINKS_PER_SITEMAP);
	}
	
	/**
	* Returns a list of users in a given page specified by $index
	*
	* @param int $index	specifies the index of sliding window, offset is multiple of $index
	* @return array		list of users
	*/
	function getUsers($index) {
		App::import('Model', 'User');
		$this->User =& ClassRegistry::init('User');
		return $this->User->find('all', array(
				'conditions' => array(),
				'limit' => MAX_LINKS_PER_SITEMAP, 'offset' => $index * MAX_LINKS_PER_SITEMAP,
				'fields' => array('User.username', 'User.created'),  'order' => 'created DESC',
				'conditions' => array('User.status' => 'normal'),
				'recursive' => -1
		));
	}
	
	/**
	* Returns number of project sitemap files
	* counts total number of visible projects and then divides it by max links allowed per sitemap
	*
	* @return int	number of project sitemap files
	*/
	function countProjectSitemaps( ) {
		App::import('Model', 'Project');
		$this->Project =& ClassRegistry::init('Project');
		$total = $this->Project->find('count', array(
								'conditions' => array('Project.proj_visibility' => 'visible'),
								'recursive' => -1)
							);
		return ceil($total / MAX_LINKS_PER_SITEMAP);
	}
	
	/**
	* Returns a list of projects in a given page specified by $index
	*
	* @param int $index	specifies the index of sliding window, offset is multiple of $index
	* @return array		list of projects
	*/
	function getProjects($index) {
		App::import('Model', 'Project');
		$this->Project =& ClassRegistry::init('Project');
		return $this->Project->find('all', array( 
				'limit' => MAX_LINKS_PER_SITEMAP, 'offset' => $index * MAX_LINKS_PER_SITEMAP,
				'fields' => array('Project.id', 'Project.user_id', 'Project.modified', 'User.username'), 
				'conditions' => array('Project.proj_visibility' => 'visible'),
				'order' => 'modified DESC', 'recursive' => 1
		));
	}
	
	/**
	* Returns number of tag sitemap files
	* counts total number of tags and then divides it by max links allowed per sitemap
	*
	* @return int	number of tag sitemap files
	*/
	function countTagSitemaps( ) {
		App::import('Model', 'Tag');
		$this->Tag =& ClassRegistry::init('Tag');
		$total = $this->Tag->find('count', array('recursive' => -1));
		return ceil($total / MAX_LINKS_PER_SITEMAP);
	}
	
	/**
	* Returns a list of tags in a given page specified by $index
	*
	* @param int $index	specifies the index of sliding window, offset is multiple of $index
	* @return array		list of tags
	*/
	function getTags($index) {
		App::import('Model', 'Tag');
		$this->Tag =& ClassRegistry::init('Tag');
		return $this->Tag->find('all', array( 
				'limit' => MAX_LINKS_PER_SITEMAP, 'offset' => $index * MAX_LINKS_PER_SITEMAP,
				'fields' => array('Tag.name', 'Tag.timestamp'), 
				'order' => 'Tag.timestamp DESC', 'recursive' => -1
		));
	}
	
	/**
	* Returns number of gallery sitemap files
	* counts total number of visible galleries and then divides it by max links allowed per sitemap
	*
	* @return int	number of gallery sitemap files
	*/
	function countGallerySitemaps( ) {
		App::import('Model', 'Gallery');
		$this->Gallery =& ClassRegistry::init('Gallery');
		$total = $this->Gallery->find('count', array(
						'conditions' => array('Gallery.visibility' => 'visible'),
						'recursive' => -1)
					);
		return ceil($total / MAX_LINKS_PER_SITEMAP);
	}
	
	/**
	* Returns a list of galleries in a given page specified by $index
	*
	* @param int $index	specifies the index of sliding window, offset is multiple of $index
	* @return array		list of galleries
	*/
	function getGalleries($index) {
		App::import('Model', 'Gallery');
		$this->Gallery =& ClassRegistry::init('Gallery');
		return $this->Gallery->find('all', array( 
				'limit' => MAX_LINKS_PER_SITEMAP, 'offset' => $index * MAX_LINKS_PER_SITEMAP,
				'fields' => array('Gallery.id', 'Gallery.modified'), 
				'conditions' => array('Gallery.visibility' => 'visible'),
				'order' => 'Gallery.modified DESC', 'recursive' => -1
		));
	}
	
	/**
	* Returns number of page sitemap files
	* max links allowed per sitemap is 5K, we assume that number of static pages will never cross that limit
	* so we are always returning 1
	*
	* @return int	number of page sitemap files
	*/
	function countPageSitemaps( ) {
		return 1;
	}
	
	/**
	* Returns a list of static pages
	*
	* @return array	list of pages
	*/
	function getPages() {
		//list any static page here
		$pages = array('download', 'research', 'credits', 'news',
					   'howto', 'about', 'share', 'login', 'signup',
					   'privacy', 'terms', 'contact/us', 'quotes', 'educators');
		
		//dynamically collect static pages from /views/pages directory
		$pages_dir = VIEWS.'pages'.DS;
		uses('Folder');
		$Folder =& new Folder($pages_dir);
		$files = $Folder->ls();
		$files = $files[1];
		$filenames = array();
		foreach($files as $index=>$file) {
			$temp = new File($pages_dir.$file);
			$name = substr($file, 0, strpos($file, '.'));
			$filenames[] = $name;
			$files[$index] = array(	'Page' => array(
					'name' => $name,
					'modified' => $temp->lastChange()
				));
		}
		
		//now handle pages array
		$pages = array_diff($pages, $filenames);
		$now = strtotime('now');
		foreach($pages as $page) {
			$files[] = array('Page' => array(
					'name' => $page,
					'modified' => $now
				));
		}
		
		return $files;
	}
}
?>