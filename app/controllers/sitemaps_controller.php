<?php
/**
 * Methods for generating sitemap-index file and different sitemap files
 *
 * @author	Anupom Syam
 */
class SitemapsController extends AppController
{
	var $name = 'Sitemaps';
	var $components = array('RequestHandler');
    var $helpers = array('Time', 'Xml');
	var $is_gzipped = false;
	
	/**
	* Stuffs to do before calling any action of this controller
	*/
	function beforeFilter() {
		Configure::write ('debug', 0);
		if($this->params['url']['ext'] == 'gz') {
			$this->is_gzipped   = true;
			$this->viewPath .= '/xml';
			$this->layoutPath = 'xml';
		}
		if($this->params['url']['ext'] != 'txt') {
			$this->RequestHandler->respondAs('xml', array('index' => 1));
		}
		
		$this->autoRender = false;
	}
	
	/**
	* Generetaes the sitemap index file.
	* executes when a request arrives for /sitemap.xml or /sitemap.xml.gz
	* when .gz suffix is present in the request, it returns gzipped version of the sitemap index
	*/
    function index() {
		$output = Cache::read('sitemap_index');
		if(empty($output)) {
			$types = array( 'user', 'project', 'tag', 'gallery', 'page');
			$sitemaps = array();
			foreach($types as $type) {
				$method = 'count'.ucwords($type).'Sitemaps';
				$sitemaps[$type] = array();
				$sitemaps[$type]['count'] = $this->Sitemap->$method();
			}
			$this->set('sitemaps', $sitemaps);
			$this->set('ext', $this->is_gzipped ? '.gz' : '');
			$this->render('index');
			$output = ($this->is_gzipped) ? gzencode($this->output) : $this->output;
			$this->output = null;
			Cache::write('sitemap_index', $output, CACHE_DURATION);
		}
		echo $output;
	}
	
	/**
	* Generetaes sitemap file
	* executes for any request of the pattern /sitemap_{type}_{index}.xml{.gz}
	* string {type} 	can be user/gallery/project/tag etc.
	* int {index} 	index of the sitemap, when index = 0, it returns a sitemap containing the first 5000 urls of the specific {type}
	* string {.gz}	optional, present when gzipped version of the sitemap is requierd
	*/
	function get() {
		preg_match( '/sitemap_(?<type>\w+)_(?<index>\d+).\w+/', $this->params['url']['url'], $matches );
		$cache_file_name = 'sitemap_' . $matches['type'] .'_' . $matches['index'] . ($this->is_gzipped ? '_gz' : '');
		$output = Cache::read($cache_file_name);
		if(empty($output)) {
			$method = '__'.$matches['type'];
			if ( method_exists($this, $method) ) {
				$this->$method($matches['index']);
			}
			$output = ($this->is_gzipped) ? gzencode($this->output) : $this->output;
			$this->output = null;
			Cache::write($cache_file_name, $output, CACHE_DURATION);
		}
		echo $output;
	}
	
	/**
	* Generetaes sitemap file for {type} = user, for all users
	*
	* executes through the SitemapController::get() method
	* for any request of the pattern /sitemap_user_{index}.xml{.gz}
	*
	* @param integer $index	index of the sitemap requested
	*/
	private function __user( $index ) {
		$items = $this->Sitemap->getUsers($index);
		$config = array (
						'priority' => '0.75',
						'changefreq' => 'weekly',
						'url_format' => '/users/%s',
						'url_params' => array( array('User', 'username') ),
						'lastmod' => array('User', 'created')
					);
		$this->__renderSitemap($items, $config);
	}
	
	/**
	* Generetaes sitemap file for {type} = project, for all projects
	*
	* executes through the SitemapController::get() method
	* for any request of the pattern /sitemap_project_{index}.xml{.gz}
	*
	* @param integer $index	index of the sitemap requested
	*/
	private function __project($index) {
		$items = $this->Sitemap->getProjects($index);
		$config = array (
					'priority' => '0.85',
					'changefreq' => 'daily',
					'url_format' => '/projects/%s/%s',
					'url_params' => array( array('User', 'username'), array('Project', 'id') ),
					'lastmod' => array('Project', 'modified')
				);
		$this->__renderSitemap($items, $config);
	}
	
	/**
	* Generetaes sitemap file for {type} = gallery, for all galleries
	*
	* executes through the SitemapController::get() method
	* for any request of the pattern /sitemap_gallery_{index}.xml{.gz}
	*
	* @param integer $index	index of the sitemap requested
	*/
	private function __gallery($index) {
		$items = $this->Sitemap->getGalleries($index);
		$config = array (
					'priority' => '0.80',
					'changefreq' => 'daily',
					'url_format' => '/galleries/view/%s',
					'url_params' => array( array('Gallery', 'id')),
					'lastmod' => array('Gallery', 'modified')
				);
		$this->__renderSitemap($items, $config);
	}
	
	/**
	* Generetaes sitemap file for {type} = tag, for all tags
	*
	* executes through the SitemapController::get() method
	* for any request of the pattern /sitemap_tag_{index}.xml{.gz}
	*
	* @param integer $index	index of the sitemap requested
	*/
	private function __tag($index) {
		$items = $this->Sitemap->getTags($index);
		$config = array (
					'priority' => '0.65',
					'changefreq' => 'hourly',
					'url_format' => '/tags/view/%s',
					'url_params' => array( array('Tag', 'name')),
					'lastmod' => array('Tag', 'timestamp')
				);
		$this->__renderSitemap($items, $config);
	}
	
	/**
	* Generetaes sitemap file for {type} = page, for all static pages
	*
	* executes through the SitemapController::get() method
	* for any request of the pattern /sitemap_page_{index}.xml{.gz}
	* though {index} is ignored and set to 0 in this case, as we assume that there will never be more than 5k static pages
	*/
	private function __page() {
		$items = $this->Sitemap->getPages();
		$config = array (
					'priority' => '0.80',
					'changefreq' => 'weekly',
					'url_format' => '/%s',
					'url_params' => array( array('Page', 'name')),
					'lastmod' => array('Page', 'modified')
				);
		$this->__renderSitemap($items, $config);
	}
	
	/**
	* Renders the sitemap view file
	*
	* @param mixed $items		array containing all items
	* @param mixed $config	index of the sitemap requested
	*
	* float $config['priority']		priority of the URL, must be between 0.00 to 1.00
	* string $config['changefreq']	how frequent the content of the URL changes - hourly/daily/weekly/monthly
	* string $config['url_format']		format of the URL for a specific type
	* array $config['url_params']		contains arrays of Modelnames and fieldnames that are used along with url_format to create absolute URLs of items
	* string $config['lastmod']		datetime field, specifies when the content of this URL was last modified
	*/
	private function __renderSitemap($items, $config) {
		$this->set('items', $items);
		$this->set('url_format', Router::url($config['url_format'], true));
		$this->set('url_params', $config['url_params']);
			
		$this->set('priority', $config['priority']);
		$this->set('changefreq', $config['changefreq']);
		$this->set('lastmod', $config['lastmod']);
			
		$this->render('sitemap');
	}
	
	/**
	* Generetaes the robots.txt
	* executes when a request arrives for /robots.txt
	*/
	function robots() {
		echo "User-agent: *\nSitemap: ".Router::url('/', true)."sitemap.xml";
	}
}
?>