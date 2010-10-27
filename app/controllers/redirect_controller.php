<?php
class RedirectController extends Controller {
var $components = array('Cookie','Session');
var $uses =array();

function about(){
	$browser_lang = $this->_get_browser_lang();
	$this->redirect('http://' . SUPPORT_URL . '/' . trim($browser_lang) . 'About_Scratch');
}


function support(){
    $browser_lang = $this->_get_browser_lang();
    $this->redirect('http://' . SUPPORT_URL . '/' . trim($browser_lang). 'Support');
}

function share(){
	$browser_lang = $this->_get_browser_lang();
	$this->redirect('http://' . SUPPORT_URL . '/' . trim($browser_lang) . 'Support/Get_Started');
}

function donate(){
	$browser_lang = $this->_get_browser_lang();
	$this->redirect('http://' . SUPPORT_URL . '/' . trim($browser_lang) . 'Donate');
}

function copyright(){
	$browser_lang = $this->_get_browser_lang();
	$this->redirect('http://' . SUPPORT_URL . '/' . trim($browser_lang). 'DMCA');
}

function terms(){
	$browser_lang = $this->_get_browser_lang();
	$this->redirect('http://' . SUPPORT_URL . '/' . trim($browser_lang). 'Terms_of_use');
}

function privacy(){
	$browser_lang = $this->_get_browser_lang();
	$this->redirect('http://' . SUPPORT_URL . '/' . trim($browser_lang) . 'Privacy_Policy');
}

function _get_browser_lang(){
	   $cookie_lang = $this->Cookie->read('lang');
	   $set_lang = get_client_language($cookie_lang);
	   return $browser_lang = !empty($set_lang) ? "$set_lang/" : '';
}

function scratch_faq(){
	App::import('model', 'UserWelcome');
	$this->UserWelcome = new UserWelcome();
	$logged_id  = $this->Session->read('User.id');
	$sql = "INSERT INTO `user_clicks` (`id`,`user_id`,`page`) VALUES"
                        ." (NULL, $logged_id, 'Scratch_FAQ')";
                $this->UserWelcome->query($sql);
	$this->redirect('http://' . SUPPORT_URL . '/' . 'Support/Scratch_FAQ');
}

function about_scratch(){
	App::import('model', 'UserWelcome');
	$this->UserWelcome = new UserWelcome();
	$logged_id  = $this->Session->read('User.id');
	$sql = "INSERT INTO `user_clicks` (`id`,`user_id`,`page`) VALUES"
                        ." (NULL, $logged_id, 'All About Scratch forum')";
                $this->UserWelcome->query($sql);
	$this->redirect("/forums/viewforum.php?id=6");
}

}
?>
