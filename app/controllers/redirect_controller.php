<?php
class RedirectController extends Controller {
var $components = array('Cookie');
var $uses =array();

function about(){
	$drupalcode = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $cookie_lang = $this->Cookie->read('lang');
	$this->redirect('http://' . SUPPORT_URL . '/' . ((isset($drupalcode[$cookie_lang])) ? "$drupalcode[$cookie_lang]/" : '') . 'About_Scratch');
}


function support(){
	$drupalcode = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $cookie_lang = $this->Cookie->read('lang');
	$this->redirect('http://' . SUPPORT_URL . '/' . ((isset($drupalcode[$cookie_lang])) ? "$drupalcode[$cookie_lang]/" : '') . 'Support');
}

function share(){
	$drupalcode = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $cookie_lang = $this->Cookie->read('lang');
	$this->redirect('http://' . SUPPORT_URL . '/' . ((isset($drupalcode[$cookie_lang])) ? "$drupalcode[$cookie_lang]/" : '') . 'Support/Get_Started');
}

function donate(){
	$drupalcode = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $cookie_lang = $this->Cookie->read('lang');
	$this->redirect('http://' . SUPPORT_URL . '/' . ((isset($drupalcode[$cookie_lang])) ? "$drupalcode[$cookie_lang]/" : '') . 'Donate');
}

function copyright(){
	$drupalcode = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $cookie_lang = $this->Cookie->read('lang');
	$this->redirect('http://' . SUPPORT_URL . '/' . ((isset($drupalcode[$cookie_lang])) ? "$drupalcode[$cookie_lang]/" : '') . 'DMCA');
}

function terms(){
	$drupalcode = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $cookie_lang = $this->Cookie->read('lang');
	$this->redirect('http://' . SUPPORT_URL . '/' . ((isset($drupalcode[$cookie_lang])) ? "$drupalcode[$cookie_lang]/" : '') . 'Terms_of_use');
}

function privacy(){
	$drupalcode = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $cookie_lang = $this->Cookie->read('lang');
	$this->redirect('http://' . SUPPORT_URL . '/' . ((isset($drupalcode[$cookie_lang])) ? "$drupalcode[$cookie_lang]/" : '') . 'Privacy_Policy');
}




}
?>
