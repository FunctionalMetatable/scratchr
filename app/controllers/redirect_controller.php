<?php
class RedirectController extends Controller {
var $components = array('Cookie');
var $uses =array();
function about(){
	$scratchr2drupal = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
	$about_scratch_url = 'http://' . ABOUT_SCRATCH_URL;
	$cookie_lang = $this->Cookie->read(lang);
	if($scratchr2drupal[$cookie_lang])
		$this->redirect("$about_scratch_url/$scratchr2drupal[$cookie_lang]/About_Scratch");
	else
		$this->redirect("$about_scratch_url/About_Scratch");

}

function support(){
	$scratchr2drupal = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
	$support_url ='http://'.SUPPORT_URL;
	$cookie_lang = $this->Cookie->read('lang');
	if($scratchr2drupal[$cookie_lang])
		$this->redirect("$support_url/$scratchr2drupal[$cookie_lang]/Support");
	else
		$this->redirect("$support_url/Support");
}

function share(){
	$scratchr2drupal = array( 'fr-fr' => 'fr', 'es-es' => 'es', 'ita'   => 'it', 'rus'	=> 'ru', 'dut'	=> 'nl', 'de-de' => 'de', 'heb'	=> 'he', 'jpn'	=> 'ja',);
        $support_url ='http://'.SUPPORT_URL;
        $cookie_lang = $this->Cookie->read('lang');
        if($scratchr2drupal[$cookie_lang])
                $this->redirect("$support_url/$scratchr2drupal[$cookie_lang]/Support/Get_Started");
        else
                $this->redirect("$support_url/Support/Get_Started");
}


}
?>
