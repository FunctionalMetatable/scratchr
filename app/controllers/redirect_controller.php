<?php
class RedirectController extends Controller {
var $components = array('Cookie');
var $uses =array();

function about(){
	$lang_array =array();
	$about_scratch_url ='http://'.ABOUT_SCRATCH_URL;
	$cookie_lang = $this->Cookie->read(lang);
	$pos=strpos($cookie_lang,'-');
	if($pos===false){
		$lang = $cookie_lang;
	}
	else
	{
	 	$lang_array =explode('-',$cookie_lang);
	 	$lang = $lang_array['0'];
	
	}
	if($lang=='en')
	$this->redirect($about_scratch_url);
	else
	$this->redirect($about_scratch_url.'/'.$lang);
}//about fun


function support(){
	$lang_array =array();
	$support_url ='http://'.SUPPORT_URL;
	$cookie_lang = $this->Cookie->read(lang);
	$pos=strpos($cookie_lang,'-');
	if($pos===false){
		$lang = $cookie_lang;
	}
	else
	{
	 	$lang_array =explode('-',$cookie_lang);
	 	$lang = $lang_array['0'];
	
	}
	if($lang =='en')
	$this->redirect($support_url);
	else
	$this->redirect($support_url.'/'.$lang);
}//about support



}//class
?>