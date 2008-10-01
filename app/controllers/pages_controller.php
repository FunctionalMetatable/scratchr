<?php
class PagesController extends AppController {
	var $name = 'Pages';
	var $uses = array('Announcement', 'Notification');
	var $helpers = array("head");
	
	/**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
	function display($view=null, $layout=null, $pageTitle=null) {
		
		if (!func_num_args()) {
			$this->redirect('/');
		}
		
		if (!$pageTitle) {
			$pageTitle = 'Scratch | Programming for everyone | Informational Page';
			if ($view == 'credits') { 
				$pageTitle = ___('Scratch | Credits', true); 
			}
			if($view == 'share') { 
				$pageTitle = ___('Scratch | Share', true);
			}
			if($view == 'scratchboard-purchase') {
				$pageTitle = ___('Scratch | Scratch Board purchase', true);
			}
		}
		
		$this->set('content_status', $this->getContentStatus());
		$this->set('active_head_element', $view);
		$this->pageTitle = $pageTitle;
		$this->render($view, $layout);
	}
}
?>
